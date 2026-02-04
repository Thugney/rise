<?php

namespace App\Controllers;

use App\Libraries\Ai_deepseek;
use App\Libraries\Ai_context;
use App\Libraries\Polar;

/**
 * AI Assistant Controller
 *
 * Handles AI chat interface and query processing.
 * Extends Security_Controller - requires authentication.
 * Implements two-gate security: Subscription check → Permission check
 */
class Ai_assistant extends Security_Controller {

    private $ai;
    private $polar;
    private $ai_context;

    function __construct() {
        parent::__construct();
        $this->ai = new Ai_deepseek();
        $this->polar = new Polar();
        $this->ai_context = new Ai_context($this);
    }

    /**
     * Check if user can access AI features
     * Two-gate security: 1) AI enabled 2) Subscription valid (if required)
     *
     * @return array ['allowed' => bool, 'reason' => string, 'checkout_url' => string|null]
     */
    private function check_ai_access() {
        // Gate 1: Is AI enabled globally?
        if (!is_ai_enabled()) {
            return array(
                'allowed' => false,
                'reason' => 'ai_disabled',
                'message' => app_lang('ai_assistant_disabled'),
                'checkout_url' => null
            );
        }

        // Admins always have full access - no subscription required
        if ($this->login_user->is_admin) {
            return array(
                'allowed' => true,
                'reason' => null,
                'message' => null,
                'checkout_url' => null
            );
        }

        // Gate 2: Is subscription required for non-admins?
        if (get_ai_setting('ai_require_subscription') === '1') {
            // Check if user has active subscription
            if (!has_ai_subscription($this->login_user->id)) {
                $checkout_url = get_ai_checkout_url($this->login_user->id);
                return array(
                    'allowed' => false,
                    'reason' => 'subscription_required',
                    'message' => app_lang('ai_subscription_required'),
                    'checkout_url' => $checkout_url
                );
            }
        }

        // All gates passed
        return array(
            'allowed' => true,
            'reason' => null,
            'message' => null,
            'checkout_url' => null
        );
    }

    /**
     * Main chat interface endpoint
     * GET /ai_assistant or /ai_assistant/index
     * Returns the chat widget HTML for AJAX loading
     */
    function index() {
        $access = $this->check_ai_access();

        $view_data = array(
            'login_user' => $this->login_user,
            'ai_access' => $access
        );

        return view('ai_assistant/chat_widget', $view_data);
    }

    /**
     * Process AI query
     * POST /ai_assistant/query
     *
     * @return JSON response with AI answer or error
     */
    function query() {
        // Validate request method
        if ($this->request->getMethod() !== 'post') {
            return $this->response
                ->setStatusCode(405)
                ->setJSON(array('success' => false, 'message' => 'Method not allowed'));
        }

        // Check AI access
        $access = $this->check_ai_access();
        if (!$access['allowed']) {
            return $this->response->setJSON(array(
                'success' => false,
                'reason' => $access['reason'],
                'message' => $access['message'],
                'checkout_url' => $access['checkout_url']
            ));
        }

        // Get query from request
        $query = trim($this->request->getPost('query'));
        $session_id = $this->request->getPost('session_id');

        if (empty($query)) {
            return $this->response->setJSON(array(
                'success' => false,
                'message' => app_lang('ai_query_empty')
            ));
        }

        // Rate limiting check
        $rate_limit = (int) get_ai_setting('ai_rate_limit_per_minute');
        if ($rate_limit > 0) {
            $Ai_conversations_model = model('App\Models\Ai_conversations_model');
            $recent_count = $Ai_conversations_model->get_query_count(
                $this->login_user->id,
                date('Y-m-d H:i:s', strtotime('-1 minute'))
            );

            if ($recent_count >= $rate_limit) {
                return $this->response->setJSON(array(
                    'success' => false,
                    'message' => app_lang('ai_rate_limit_exceeded')
                ));
            }
        }

        // Build context based on user permissions and query
        $context = $this->build_user_context($query);

        // Get session history for context continuity
        $history = array();
        if ($session_id) {
            $Ai_conversations_model = model('App\Models\Ai_conversations_model');
            $history = $Ai_conversations_model->get_session_history($this->login_user->id, $session_id, 10);
        }

        // Generate new session ID if not provided
        if (!$session_id) {
            $session_id = 'session_' . $this->login_user->id . '_' . time();
        }

        // Call AI
        $result = $this->ai->query($query, $context, $history);

        if (!$result['success']) {
            return $this->response->setJSON(array(
                'success' => false,
                'message' => $result['error'] ?? app_lang('ai_query_failed')
            ));
        }

        // Log conversation
        $Ai_conversations_model = model('App\Models\Ai_conversations_model');
        $Ai_conversations_model->log_conversation(
            $this->login_user->id,
            $session_id,
            $query,
            $result['response'],
            $result['tokens_used'] ?? 0
        );

        return $this->response->setJSON(array(
            'success' => true,
            'response' => $result['response'],
            'session_id' => $session_id,
            'tokens_used' => $result['tokens_used'] ?? 0
        ));
    }

    /**
     * Build context information based on user's permissions
     * Uses Ai_context library for permission-aware data fetching
     *
     * @param string $query The user's query (for module detection)
     * @return string Context string for AI
     */
    private function build_user_context($query = '') {
        // Build rich context using Ai_context library
        $context = $this->ai_context->build_context($query);

        // System prompt for AI behavior
        $system_prompt = "You are an AI assistant integrated into RISE CRM. You help users with their CRM tasks including project management, invoicing, client management, tickets, leads, and general business operations.

CAPABILITIES:
- Answer questions about projects, tasks, clients, invoices, tickets, and leads
- Provide data summaries and insights based on the context provided
- Help draft emails, messages, and responses
- Suggest next actions and prioritize work
- Explain CRM features and best practices

GUIDELINES:
- Be concise, helpful, and professional
- Use the data context provided to give specific, accurate answers
- When asked about data not in your context, explain you can only see summarized data
- For drafting tasks, create professional, clear content
- Always respect that you can only see data the user has permission to access
- Format responses clearly with bullet points or numbered lists when appropriate";

        // Format context for AI
        $formatted_context = $this->ai_context->format_context_for_ai($context);

        return $system_prompt . "\n\n" . $formatted_context;
    }

    /**
     * Get conversation history
     * GET /ai_assistant/history
     *
     * @return JSON with recent conversations
     */
    function history() {
        $access = $this->check_ai_access();
        if (!$access['allowed']) {
            return $this->response->setJSON(array(
                'success' => false,
                'reason' => $access['reason'],
                'message' => $access['message']
            ));
        }

        $session_id = $this->request->getGet('session_id');
        $limit = (int) $this->request->getGet('limit') ?: 20;

        $Ai_conversations_model = model('App\Models\Ai_conversations_model');
        $history = $Ai_conversations_model->get_session_history(
            $this->login_user->id,
            $session_id,
            $limit
        );

        return $this->response->setJSON(array(
            'success' => true,
            'history' => $history
        ));
    }

    /**
     * Clear conversation session
     * POST /ai_assistant/clear_session
     *
     * @return JSON success/failure
     */
    function clear_session() {
        if ($this->request->getMethod() !== 'post') {
            return $this->response
                ->setStatusCode(405)
                ->setJSON(array('success' => false, 'message' => 'Method not allowed'));
        }

        // No access check needed - user can always clear their own sessions

        $session_id = $this->request->getPost('session_id');

        if ($session_id) {
            $Ai_conversations_model = model('App\Models\Ai_conversations_model');
            $Ai_conversations_model->clear_session($this->login_user->id, $session_id);
        }

        return $this->response->setJSON(array(
            'success' => true,
            'message' => app_lang('ai_session_cleared')
        ));
    }

    /**
     * Get subscription status
     * GET /ai_assistant/subscription_status
     *
     * @return JSON with subscription details
     */
    function subscription_status() {
        $has_subscription = has_ai_subscription($this->login_user->id);
        $subscription_required = get_ai_setting('ai_require_subscription') === '1';

        $data = array(
            'success' => true,
            'ai_enabled' => is_ai_enabled(),
            'subscription_required' => $subscription_required,
            'has_subscription' => $has_subscription,
            'can_access' => is_ai_enabled() && (!$subscription_required || $has_subscription)
        );

        if (!$has_subscription && $subscription_required) {
            $data['checkout_url'] = get_ai_checkout_url($this->login_user->id);
        }

        // Get subscription details if exists
        if ($has_subscription) {
            $Ai_subscriptions_model = model('App\Models\Ai_subscriptions_model');
            $subscription = $Ai_subscriptions_model->get_by_user_id($this->login_user->id);
            if ($subscription) {
                $data['subscription'] = array(
                    'status' => $subscription->status,
                    'current_period_end' => $subscription->current_period_end
                );
            }
        }

        return $this->response->setJSON($data);
    }

    /**
     * Redirect to Polar checkout
     * GET /ai_assistant/subscribe
     */
    function subscribe() {
        $checkout_url = get_ai_checkout_url($this->login_user->id);

        if ($checkout_url) {
            return redirect()->to($checkout_url);
        }

        // Polar not configured, redirect back with error
        $session = \Config\Services::session();
        $session->setFlashdata('error_message', app_lang('ai_subscription_not_available'));
        return redirect()->back();
    }

    /**
     * Draft an email response
     * POST /ai_assistant/draft_email
     *
     * @return JSON with drafted email content
     */
    function draft_email() {
        if ($this->request->getMethod() !== 'post') {
            return $this->response
                ->setStatusCode(405)
                ->setJSON(array('success' => false, 'message' => 'Method not allowed'));
        }

        $access = $this->check_ai_access();
        if (!$access['allowed']) {
            return $this->response->setJSON(array(
                'success' => false,
                'reason' => $access['reason'],
                'message' => $access['message'],
                'checkout_url' => $access['checkout_url']
            ));
        }

        $context_type = $this->request->getPost('context_type'); // ticket, client, project, etc.
        $context_id = $this->request->getPost('context_id');
        $instructions = trim($this->request->getPost('instructions'));
        $tone = $this->request->getPost('tone') ?: 'professional';

        if (empty($instructions)) {
            return $this->response->setJSON(array(
                'success' => false,
                'message' => app_lang('ai_draft_instructions_required')
            ));
        }

        // Get context data for the specified record
        $record_context = '';
        if ($context_type && $context_id) {
            $record_data = $this->ai_context->get_record_details($context_type, $context_id);
            if (!isset($record_data['error'])) {
                $record_context = "\n\nRELATED RECORD:\n" . json_encode($record_data, JSON_PRETTY_PRINT);
            }
        }

        // Build drafting prompt
        $prompt = "You are drafting an email for a CRM user. Create a professional email based on these instructions:

INSTRUCTIONS: {$instructions}

TONE: {$tone}
{$record_context}

Please provide the email with:
- Subject line (prefixed with 'Subject: ')
- Email body

Keep the email concise and professional.";

        $context = $this->build_user_context();

        $result = $this->ai->query($prompt, $context, array());

        if (!$result['success']) {
            return $this->response->setJSON(array(
                'success' => false,
                'message' => $result['error'] ?? app_lang('ai_draft_failed')
            ));
        }

        // Trigger hook for AI draft action
        app_hooks()->do_action('app_hook_ai_email_drafted', array(
            'user_id' => $this->login_user->id,
            'context_type' => $context_type,
            'context_id' => $context_id,
            'instructions' => $instructions
        ));

        return $this->response->setJSON(array(
            'success' => true,
            'draft' => $result['response'],
            'tokens_used' => $result['tokens_used'] ?? 0
        ));
    }

    /**
     * Draft a ticket response
     * POST /ai_assistant/draft_ticket_response
     *
     * @return JSON with drafted response
     */
    function draft_ticket_response() {
        if ($this->request->getMethod() !== 'post') {
            return $this->response
                ->setStatusCode(405)
                ->setJSON(array('success' => false, 'message' => 'Method not allowed'));
        }

        $access = $this->check_ai_access();
        if (!$access['allowed']) {
            return $this->response->setJSON(array(
                'success' => false,
                'reason' => $access['reason'],
                'message' => $access['message'],
                'checkout_url' => $access['checkout_url']
            ));
        }

        $ticket_id = $this->request->getPost('ticket_id');
        $tone = $this->request->getPost('tone') ?: 'helpful';
        $additional_instructions = trim($this->request->getPost('instructions') ?? '');

        if (empty($ticket_id)) {
            return $this->response->setJSON(array(
                'success' => false,
                'message' => app_lang('ai_ticket_id_required')
            ));
        }

        // Get ticket details
        $ticket_data = $this->ai_context->get_record_details('ticket', $ticket_id);
        if (isset($ticket_data['error'])) {
            return $this->response->setJSON(array(
                'success' => false,
                'message' => $ticket_data['error']
            ));
        }

        // Build response prompt
        $prompt = "You are a support agent drafting a response to a support ticket.

TICKET DETAILS:
- Title: {$ticket_data['title']}
- Status: {$ticket_data['status']}
- Type: {$ticket_data['type']}
- Client: {$ticket_data['client']}
- Description: {$ticket_data['description']}

TONE: {$tone}
" . ($additional_instructions ? "\nADDITIONAL INSTRUCTIONS: {$additional_instructions}" : "") . "

Please draft a professional and helpful response to this ticket. The response should:
1. Acknowledge the issue
2. Provide helpful information or next steps
3. Maintain a {$tone} tone throughout";

        $context = $this->build_user_context();

        $result = $this->ai->query($prompt, $context, array());

        if (!$result['success']) {
            return $this->response->setJSON(array(
                'success' => false,
                'message' => $result['error'] ?? app_lang('ai_draft_failed')
            ));
        }

        // Trigger hook
        app_hooks()->do_action('app_hook_ai_ticket_response_drafted', array(
            'user_id' => $this->login_user->id,
            'ticket_id' => $ticket_id
        ));

        return $this->response->setJSON(array(
            'success' => true,
            'draft' => $result['response'],
            'tokens_used' => $result['tokens_used'] ?? 0
        ));
    }

    /**
     * Get a summary of specific module data
     * POST /ai_assistant/summarize
     *
     * @return JSON with AI-generated summary
     */
    function summarize() {
        if ($this->request->getMethod() !== 'post') {
            return $this->response
                ->setStatusCode(405)
                ->setJSON(array('success' => false, 'message' => 'Method not allowed'));
        }

        $access = $this->check_ai_access();
        if (!$access['allowed']) {
            return $this->response->setJSON(array(
                'success' => false,
                'reason' => $access['reason'],
                'message' => $access['message'],
                'checkout_url' => $access['checkout_url']
            ));
        }

        $module = $this->request->getPost('module');
        $focus = $this->request->getPost('focus'); // e.g., 'overdue', 'high_priority', 'this_week'

        if (empty($module)) {
            return $this->response->setJSON(array(
                'success' => false,
                'message' => app_lang('ai_module_required')
            ));
        }

        // Get module context
        $module_data = $this->ai_context->get_module_context($module);

        if (isset($module_data['note'])) {
            return $this->response->setJSON(array(
                'success' => false,
                'message' => $module_data['note']
            ));
        }

        // Build summary prompt
        $prompt = "Based on the following {$module} data, provide a concise executive summary.

DATA:
" . json_encode($module_data, JSON_PRETTY_PRINT) . "

" . ($focus ? "FOCUS AREA: {$focus}\n" : "") . "
Please provide:
1. Key metrics overview
2. Items requiring attention (if any)
3. 2-3 actionable recommendations

Keep the summary brief and actionable.";

        $context = $this->build_user_context();

        $result = $this->ai->query($prompt, $context, array());

        if (!$result['success']) {
            return $this->response->setJSON(array(
                'success' => false,
                'message' => $result['error'] ?? app_lang('ai_summary_failed')
            ));
        }

        // Trigger hook
        app_hooks()->do_action('app_hook_ai_summary_generated', array(
            'user_id' => $this->login_user->id,
            'module' => $module,
            'focus' => $focus
        ));

        return $this->response->setJSON(array(
            'success' => true,
            'summary' => $result['response'],
            'raw_data' => $module_data,
            'tokens_used' => $result['tokens_used'] ?? 0
        ));
    }

    /**
     * Suggest next actions for a record
     * POST /ai_assistant/suggest_actions
     *
     * @return JSON with suggested actions
     */
    function suggest_actions() {
        if ($this->request->getMethod() !== 'post') {
            return $this->response
                ->setStatusCode(405)
                ->setJSON(array('success' => false, 'message' => 'Method not allowed'));
        }

        $access = $this->check_ai_access();
        if (!$access['allowed']) {
            return $this->response->setJSON(array(
                'success' => false,
                'reason' => $access['reason'],
                'message' => $access['message'],
                'checkout_url' => $access['checkout_url']
            ));
        }

        $record_type = $this->request->getPost('record_type');
        $record_id = $this->request->getPost('record_id');

        if (empty($record_type) || empty($record_id)) {
            return $this->response->setJSON(array(
                'success' => false,
                'message' => app_lang('ai_record_info_required')
            ));
        }

        // Get record details
        $record_data = $this->ai_context->get_record_details($record_type, $record_id);
        if (isset($record_data['error'])) {
            return $this->response->setJSON(array(
                'success' => false,
                'message' => $record_data['error']
            ));
        }

        // Build suggestions prompt
        $prompt = "Based on the following {$record_type} record, suggest the most appropriate next actions.

RECORD DETAILS:
" . json_encode($record_data, JSON_PRETTY_PRINT) . "

Please provide 3-5 specific, actionable recommendations. For each suggestion:
1. State the action clearly
2. Explain why it's important
3. Note any urgency level (high/medium/low)

Format as a numbered list.";

        $context = $this->build_user_context();

        $result = $this->ai->query($prompt, $context, array());

        if (!$result['success']) {
            return $this->response->setJSON(array(
                'success' => false,
                'message' => $result['error'] ?? app_lang('ai_suggestions_failed')
            ));
        }

        return $this->response->setJSON(array(
            'success' => true,
            'suggestions' => $result['response'],
            'record' => $record_data,
            'tokens_used' => $result['tokens_used'] ?? 0
        ));
    }

    /**
     * Subscription success callback
     * GET /ai_assistant/subscription_success
     */
    function subscription_success() {
        $session = \Config\Services::session();
        $session->setFlashdata('success_message', app_lang('ai_subscription_activated'));

        // Trigger hook for subscription activation
        app_hooks()->do_action('app_hook_ai_subscription_activated', array(
            'user_id' => $this->login_user->id
        ));

        return redirect()->to(get_uri('dashboard'));
    }
}
