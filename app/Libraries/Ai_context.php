<?php

namespace App\Libraries;

/**
 * AI Context Library
 *
 * Builds permission-aware context for AI queries.
 * Uses existing Permission_manager patterns and model methods
 * to fetch only data the user has access to.
 */
class Ai_context {

    private $login_user;
    private $permissions;
    private $controller;

    // Models
    private $Projects_model;
    private $Tasks_model;
    private $Clients_model;
    private $Invoices_model;
    private $Tickets_model;
    private $Leads_model;

    public function __construct($controller = null) {
        $this->controller = $controller;

        if ($controller && isset($controller->login_user)) {
            $this->login_user = $controller->login_user;
            $this->permissions = $this->login_user->permissions ?? array();
        }

        // Load models
        $this->Projects_model = model('App\Models\Projects_model');
        $this->Tasks_model = model('App\Models\Tasks_model');
        $this->Clients_model = model('App\Models\Clients_model');
        $this->Invoices_model = model('App\Models\Invoices_model');
        $this->Tickets_model = model('App\Models\Tickets_model');
        $this->Leads_model = model('App\Models\Leads_model');
    }

    /**
     * Set login user (if not passed via controller)
     */
    public function set_user($user) {
        $this->login_user = $user;
        $this->permissions = $user->permissions ?? array();
    }

    /**
     * Build full context for AI based on user query
     * Returns structured data the AI can use
     */
    public function build_context($query, $modules = array()) {
        if (!$this->login_user) {
            return array('error' => 'User not authenticated');
        }

        $context = array(
            'user' => $this->get_user_context(),
            'accessible_modules' => $this->get_accessible_modules(),
            'data' => array()
        );

        // Auto-detect relevant modules from query if not specified
        if (empty($modules)) {
            $modules = $this->detect_modules_from_query($query);
        }

        // Fetch context data for each relevant module
        foreach ($modules as $module) {
            $context['data'][$module] = $this->get_module_context($module, $query);
        }

        return $context;
    }

    /**
     * Get basic user context
     */
    public function get_user_context() {
        return array(
            'id' => $this->login_user->id,
            'name' => $this->login_user->first_name . ' ' . $this->login_user->last_name,
            'email' => $this->login_user->email ?? '',
            'user_type' => $this->login_user->user_type,
            'is_admin' => $this->login_user->is_admin ?? false,
            'client_id' => $this->login_user->client_id ?? null,
            'timezone' => get_setting('timezone'),
            'current_date' => date('Y-m-d'),
            'current_time' => date('H:i:s')
        );
    }

    /**
     * Get list of modules user can access
     */
    public function get_accessible_modules() {
        $modules = array();

        // Check each module's setting and user permission
        if (get_setting('module_project')) {
            if ($this->can_access_module('project')) {
                $modules[] = 'projects';
            }
        }

        if (get_setting('module_invoice')) {
            if ($this->can_access_module('invoice')) {
                $modules[] = 'invoices';
            }
        }

        if (get_setting('module_ticket')) {
            if ($this->can_access_module('ticket')) {
                $modules[] = 'tickets';
            }
        }

        if (get_setting('module_lead')) {
            if ($this->can_access_module('lead')) {
                $modules[] = 'leads';
            }
        }

        if (get_setting('module_estimate')) {
            if ($this->can_access_module('estimate')) {
                $modules[] = 'estimates';
            }
        }

        if (get_setting('module_expense')) {
            if ($this->can_access_module('expense')) {
                $modules[] = 'expenses';
            }
        }

        if (get_setting('module_contract')) {
            if ($this->can_access_module('contract')) {
                $modules[] = 'contracts';
            }
        }

        // Clients - check access
        if ($this->can_access_module('client')) {
            $modules[] = 'clients';
        }

        // Tasks are always available for staff
        if ($this->login_user->user_type === 'staff') {
            $modules[] = 'tasks';
        }

        return $modules;
    }

    /**
     * Check if user can access a module
     */
    private function can_access_module($module) {
        if ($this->login_user->is_admin) {
            return true;
        }

        $permission = get_array_value($this->permissions, $module);
        return !empty($permission);
    }

    /**
     * Detect relevant modules from query text
     */
    private function detect_modules_from_query($query) {
        $query_lower = strtolower($query);
        $modules = array();

        $module_keywords = array(
            'projects' => array('project', 'projects', 'milestone', 'deadline'),
            'tasks' => array('task', 'tasks', 'todo', 'assignment', 'assigned'),
            'clients' => array('client', 'clients', 'customer', 'company'),
            'invoices' => array('invoice', 'invoices', 'payment', 'bill', 'paid', 'unpaid', 'overdue'),
            'tickets' => array('ticket', 'tickets', 'support', 'issue', 'help'),
            'leads' => array('lead', 'leads', 'prospect', 'opportunity'),
            'estimates' => array('estimate', 'estimates', 'quote', 'quotation'),
            'expenses' => array('expense', 'expenses', 'cost', 'spending'),
            'contracts' => array('contract', 'contracts', 'agreement')
        );

        foreach ($module_keywords as $module => $keywords) {
            foreach ($keywords as $keyword) {
                if (strpos($query_lower, $keyword) !== false) {
                    $modules[] = $module;
                    break;
                }
            }
        }

        // Default to tasks if no specific module detected
        if (empty($modules)) {
            $modules = array('tasks');
        }

        // Filter to only accessible modules
        $accessible = $this->get_accessible_modules();
        return array_intersect($modules, $accessible);
    }

    /**
     * Get context data for a specific module
     */
    public function get_module_context($module, $query = '') {
        switch ($module) {
            case 'projects':
                return $this->get_projects_context($query);
            case 'tasks':
                return $this->get_tasks_context($query);
            case 'clients':
                return $this->get_clients_context($query);
            case 'invoices':
                return $this->get_invoices_context($query);
            case 'tickets':
                return $this->get_tickets_context($query);
            case 'leads':
                return $this->get_leads_context($query);
            default:
                return array('note' => "Module '{$module}' context not implemented");
        }
    }

    /**
     * Get projects context with permission filtering
     */
    public function get_projects_context($query = '') {
        $options = array();

        // Apply permission filters
        if (!$this->login_user->is_admin && $this->login_user->user_type === 'staff') {
            $can_manage_all = get_array_value($this->permissions, 'can_manage_all_projects') == '1';
            if (!$can_manage_all) {
                $options['user_id'] = $this->login_user->id;
            }
        } elseif ($this->login_user->user_type === 'client') {
            $options['client_id'] = $this->login_user->client_id;
        }

        // Get summary data
        $projects = $this->Projects_model->get_details($options)->getResult();

        $summary = array(
            'total_count' => count($projects),
            'by_status' => array(),
            'recent_projects' => array()
        );

        foreach ($projects as $project) {
            $status = $project->status_title ?? 'Unknown';
            if (!isset($summary['by_status'][$status])) {
                $summary['by_status'][$status] = 0;
            }
            $summary['by_status'][$status]++;
        }

        // Get 10 most recent projects
        $recent = array_slice($projects, 0, 10);
        foreach ($recent as $project) {
            $summary['recent_projects'][] = array(
                'id' => $project->id,
                'title' => $project->title,
                'status' => $project->status_title ?? 'Unknown',
                'client' => $project->company_name ?? 'N/A',
                'deadline' => $project->deadline,
                'progress' => $project->progress ?? 0
            );
        }

        return $summary;
    }

    /**
     * Get tasks context with permission filtering
     */
    public function get_tasks_context($query = '') {
        $options = array('deleted' => 0);

        // Apply permission filters
        if ($this->login_user->user_type === 'staff') {
            $show_assigned_only = get_array_value($this->permissions, 'show_assigned_tasks_only') == '1';
            if ($show_assigned_only && !$this->login_user->is_admin) {
                $options['assigned_to'] = $this->login_user->id;
            }
        } elseif ($this->login_user->user_type === 'client') {
            $options['client_id'] = $this->login_user->client_id;
        }

        $tasks = $this->Tasks_model->get_details($options)->getResult();

        $summary = array(
            'total_count' => count($tasks),
            'by_status' => array(),
            'overdue_count' => 0,
            'due_today_count' => 0,
            'recent_tasks' => array()
        );

        $today = date('Y-m-d');

        foreach ($tasks as $task) {
            // Count by status
            $status = $task->status_title ?? 'Unknown';
            if (!isset($summary['by_status'][$status])) {
                $summary['by_status'][$status] = 0;
            }
            $summary['by_status'][$status]++;

            // Check overdue
            if (!empty($task->deadline) && $task->deadline < $today && $task->status_id != 3) {
                $summary['overdue_count']++;
            }

            // Check due today
            if ($task->deadline === $today) {
                $summary['due_today_count']++;
            }
        }

        // Get 15 most recent/relevant tasks
        $recent = array_slice($tasks, 0, 15);
        foreach ($recent as $task) {
            $summary['recent_tasks'][] = array(
                'id' => $task->id,
                'title' => $task->title,
                'status' => $task->status_title ?? 'Unknown',
                'project' => $task->project_title ?? 'N/A',
                'assigned_to' => $task->assigned_to_name ?? 'Unassigned',
                'deadline' => $task->deadline,
                'priority' => $task->priority_title ?? 'Normal'
            );
        }

        return $summary;
    }

    /**
     * Get clients context with permission filtering
     */
    public function get_clients_context($query = '') {
        // Check permission level
        $client_permission = get_array_value($this->permissions, 'client');

        if (!$this->login_user->is_admin && empty($client_permission)) {
            return array('note' => 'No access to clients module');
        }

        $options = array('deleted' => 0, 'is_lead' => 0);

        // Apply permission filters
        if (!$this->login_user->is_admin) {
            if ($client_permission === 'own') {
                $options['created_by'] = $this->login_user->id;
            } elseif ($client_permission === 'specific') {
                $specific_groups = get_array_value($this->permissions, 'client_specific');
                if ($specific_groups) {
                    $options['group_ids'] = $specific_groups;
                }
            }
        }

        if ($this->login_user->user_type === 'client') {
            // Clients can only see their own company
            $options['id'] = $this->login_user->client_id;
        }

        $clients = $this->Clients_model->get_details($options)->getResult();

        $summary = array(
            'total_count' => count($clients),
            'recent_clients' => array()
        );

        // Get 10 most recent clients
        $recent = array_slice($clients, 0, 10);
        foreach ($recent as $client) {
            $summary['recent_clients'][] = array(
                'id' => $client->id,
                'company_name' => $client->company_name,
                'primary_contact' => ($client->first_name ?? '') . ' ' . ($client->last_name ?? ''),
                'email' => $client->email ?? '',
                'phone' => $client->phone ?? '',
                'city' => $client->city ?? ''
            );
        }

        return $summary;
    }

    /**
     * Get invoices context with permission filtering
     */
    public function get_invoices_context($query = '') {
        if (!get_setting('module_invoice')) {
            return array('note' => 'Invoice module is disabled');
        }

        $invoice_permission = get_array_value($this->permissions, 'invoice');

        if (!$this->login_user->is_admin && empty($invoice_permission) && $this->login_user->user_type !== 'client') {
            return array('note' => 'No access to invoices module');
        }

        $options = array('deleted' => 0);

        // Apply permission filters for staff
        if ($this->login_user->user_type === 'staff' && !$this->login_user->is_admin) {
            if ($invoice_permission === 'manage_own_client_invoices' ||
                $invoice_permission === 'manage_own_client_invoices_except_delete' ||
                $invoice_permission === 'view_own_client_invoices') {
                $options['client_owner_id'] = $this->login_user->id;
            } elseif ($invoice_permission === 'manage_only_own_created_invoices' ||
                      $invoice_permission === 'manage_only_own_created_invoices_except_delete') {
                $options['created_by'] = $this->login_user->id;
            }
        } elseif ($this->login_user->user_type === 'client') {
            $options['client_id'] = $this->login_user->client_id;
        }

        $invoices = $this->Invoices_model->get_details($options)->getResult();

        $summary = array(
            'total_count' => count($invoices),
            'by_status' => array(),
            'total_amount' => 0,
            'total_paid' => 0,
            'total_due' => 0,
            'overdue_count' => 0,
            'recent_invoices' => array()
        );

        $today = date('Y-m-d');

        foreach ($invoices as $invoice) {
            // Count by status
            $status = $invoice->status ?? 'Unknown';
            if (!isset($summary['by_status'][$status])) {
                $summary['by_status'][$status] = 0;
            }
            $summary['by_status'][$status]++;

            // Calculate totals
            $total = floatval($invoice->invoice_total ?? 0);
            $paid = floatval($invoice->payment_received ?? 0);
            $summary['total_amount'] += $total;
            $summary['total_paid'] += $paid;
            $summary['total_due'] += ($total - $paid);

            // Check overdue
            if ($status !== 'paid' && !empty($invoice->due_date) && $invoice->due_date < $today) {
                $summary['overdue_count']++;
            }
        }

        // Get 10 most recent invoices
        $recent = array_slice($invoices, 0, 10);
        foreach ($recent as $invoice) {
            $summary['recent_invoices'][] = array(
                'id' => $invoice->id,
                'invoice_number' => $invoice->invoice_id ?? 'N/A',
                'client' => $invoice->company_name ?? 'N/A',
                'status' => $invoice->status ?? 'Unknown',
                'total' => $invoice->invoice_total ?? 0,
                'paid' => $invoice->payment_received ?? 0,
                'due_date' => $invoice->due_date
            );
        }

        return $summary;
    }

    /**
     * Get tickets context with permission filtering
     */
    public function get_tickets_context($query = '') {
        if (!get_setting('module_ticket')) {
            return array('note' => 'Ticket module is disabled');
        }

        $ticket_permission = get_array_value($this->permissions, 'ticket');

        if (!$this->login_user->is_admin && empty($ticket_permission) && $this->login_user->user_type !== 'client') {
            return array('note' => 'No access to tickets module');
        }

        $options = array('deleted' => 0);

        // Apply permission filters
        if ($this->login_user->user_type === 'staff' && !$this->login_user->is_admin) {
            if ($ticket_permission === 'assigned_only') {
                $options['assigned_to'] = $this->login_user->id;
            } elseif ($ticket_permission === 'specific') {
                $specific_types = get_array_value($this->permissions, 'ticket_specific');
                if ($specific_types) {
                    $options['ticket_type_id'] = explode(',', $specific_types);
                }
            }
        } elseif ($this->login_user->user_type === 'client') {
            $options['client_id'] = $this->login_user->client_id;
        }

        $tickets = $this->Tickets_model->get_details($options)->getResult();

        $summary = array(
            'total_count' => count($tickets),
            'by_status' => array(),
            'open_count' => 0,
            'recent_tickets' => array()
        );

        foreach ($tickets as $ticket) {
            $status = $ticket->status ?? 'open';
            if (!isset($summary['by_status'][$status])) {
                $summary['by_status'][$status] = 0;
            }
            $summary['by_status'][$status]++;

            if ($status === 'open' || $status === 'new') {
                $summary['open_count']++;
            }
        }

        // Get 10 most recent tickets
        $recent = array_slice($tickets, 0, 10);
        foreach ($recent as $ticket) {
            $summary['recent_tickets'][] = array(
                'id' => $ticket->id,
                'title' => $ticket->title,
                'status' => $ticket->status ?? 'open',
                'type' => $ticket->ticket_type_title ?? 'General',
                'client' => $ticket->company_name ?? 'N/A',
                'assigned_to' => $ticket->assigned_to_name ?? 'Unassigned',
                'created_at' => $ticket->created_at
            );
        }

        return $summary;
    }

    /**
     * Get leads context with permission filtering
     */
    public function get_leads_context($query = '') {
        if (!get_setting('module_lead')) {
            return array('note' => 'Lead module is disabled');
        }

        $lead_permission = get_array_value($this->permissions, 'lead');

        if (!$this->login_user->is_admin && empty($lead_permission)) {
            return array('note' => 'No access to leads module');
        }

        $options = array('deleted' => 0, 'is_lead' => 1);

        // Apply permission filters
        if (!$this->login_user->is_admin) {
            if ($lead_permission === 'own') {
                $options['owner_id'] = $this->login_user->id;
            }
        }

        $leads = $this->Clients_model->get_details($options)->getResult();

        $summary = array(
            'total_count' => count($leads),
            'by_status' => array(),
            'recent_leads' => array()
        );

        foreach ($leads as $lead) {
            $status = $lead->lead_status_title ?? 'New';
            if (!isset($summary['by_status'][$status])) {
                $summary['by_status'][$status] = 0;
            }
            $summary['by_status'][$status]++;
        }

        // Get 10 most recent leads
        $recent = array_slice($leads, 0, 10);
        foreach ($recent as $lead) {
            $summary['recent_leads'][] = array(
                'id' => $lead->id,
                'company_name' => $lead->company_name,
                'contact' => ($lead->first_name ?? '') . ' ' . ($lead->last_name ?? ''),
                'email' => $lead->email ?? '',
                'status' => $lead->lead_status_title ?? 'New',
                'owner' => $lead->owner_name ?? 'Unassigned',
                'source' => $lead->lead_source_title ?? ''
            );
        }

        return $summary;
    }

    /**
     * Get specific record details (for focused queries)
     */
    public function get_record_details($module, $record_id) {
        // Verify access first
        switch ($module) {
            case 'project':
                return $this->get_project_details($record_id);
            case 'task':
                return $this->get_task_details($record_id);
            case 'client':
                return $this->get_client_details($record_id);
            case 'invoice':
                return $this->get_invoice_details($record_id);
            case 'ticket':
                return $this->get_ticket_details($record_id);
            default:
                return array('error' => 'Module not supported');
        }
    }

    /**
     * Get detailed project information
     */
    private function get_project_details($project_id) {
        $project = $this->Projects_model->get_details(array('id' => $project_id))->getRow();

        if (!$project) {
            return array('error' => 'Project not found');
        }

        // Check access
        if (!$this->can_access_project($project)) {
            return array('error' => 'Access denied');
        }

        return array(
            'id' => $project->id,
            'title' => $project->title,
            'description' => $project->description,
            'client' => $project->company_name ?? 'N/A',
            'status' => $project->status_title ?? 'Unknown',
            'start_date' => $project->start_date,
            'deadline' => $project->deadline,
            'progress' => $project->progress ?? 0,
            'price' => $project->price ?? 0,
            'labels' => $project->labels ?? ''
        );
    }

    /**
     * Check if user can access a project
     */
    private function can_access_project($project) {
        if ($this->login_user->is_admin) {
            return true;
        }

        if ($this->login_user->user_type === 'client') {
            return $project->client_id == $this->login_user->client_id;
        }

        $can_manage_all = get_array_value($this->permissions, 'can_manage_all_projects') == '1';
        if ($can_manage_all) {
            return true;
        }

        // Check if user is a project member
        $Project_members_model = model('App\Models\Project_members_model');
        return $Project_members_model->is_user_a_project_member($project->id, $this->login_user->id);
    }

    /**
     * Get detailed task information
     */
    private function get_task_details($task_id) {
        $task = $this->Tasks_model->get_details(array('id' => $task_id))->getRow();

        if (!$task) {
            return array('error' => 'Task not found');
        }

        return array(
            'id' => $task->id,
            'title' => $task->title,
            'description' => $task->description,
            'project' => $task->project_title ?? 'N/A',
            'status' => $task->status_title ?? 'Unknown',
            'priority' => $task->priority_title ?? 'Normal',
            'assigned_to' => $task->assigned_to_name ?? 'Unassigned',
            'start_date' => $task->start_date,
            'deadline' => $task->deadline,
            'points' => $task->points ?? 0
        );
    }

    /**
     * Get detailed client information
     */
    private function get_client_details($client_id) {
        $client = $this->Clients_model->get_details(array('id' => $client_id))->getRow();

        if (!$client) {
            return array('error' => 'Client not found');
        }

        return array(
            'id' => $client->id,
            'company_name' => $client->company_name,
            'contact' => ($client->first_name ?? '') . ' ' . ($client->last_name ?? ''),
            'email' => $client->email ?? '',
            'phone' => $client->phone ?? '',
            'address' => $client->address ?? '',
            'city' => $client->city ?? '',
            'state' => $client->state ?? '',
            'country' => $client->country ?? '',
            'website' => $client->website ?? ''
        );
    }

    /**
     * Get detailed invoice information
     */
    private function get_invoice_details($invoice_id) {
        $invoice = $this->Invoices_model->get_details(array('id' => $invoice_id))->getRow();

        if (!$invoice) {
            return array('error' => 'Invoice not found');
        }

        return array(
            'id' => $invoice->id,
            'invoice_number' => $invoice->invoice_id ?? 'N/A',
            'client' => $invoice->company_name ?? 'N/A',
            'status' => $invoice->status ?? 'Unknown',
            'total' => $invoice->invoice_total ?? 0,
            'paid' => $invoice->payment_received ?? 0,
            'due' => ($invoice->invoice_total ?? 0) - ($invoice->payment_received ?? 0),
            'bill_date' => $invoice->bill_date,
            'due_date' => $invoice->due_date,
            'note' => $invoice->note ?? ''
        );
    }

    /**
     * Get detailed ticket information
     */
    private function get_ticket_details($ticket_id) {
        $ticket = $this->Tickets_model->get_details(array('id' => $ticket_id))->getRow();

        if (!$ticket) {
            return array('error' => 'Ticket not found');
        }

        return array(
            'id' => $ticket->id,
            'title' => $ticket->title,
            'description' => strip_tags($ticket->description ?? ''),
            'status' => $ticket->status ?? 'open',
            'type' => $ticket->ticket_type_title ?? 'General',
            'client' => $ticket->company_name ?? 'N/A',
            'assigned_to' => $ticket->assigned_to_name ?? 'Unassigned',
            'created_at' => $ticket->created_at,
            'last_activity' => $ticket->last_activity_at ?? ''
        );
    }

    /**
     * Format context as a string for AI system prompt
     */
    public function format_context_for_ai($context) {
        $output = array();

        // User info
        $user = $context['user'] ?? array();
        $output[] = "USER INFORMATION:";
        $output[] = "- Name: " . ($user['name'] ?? 'Unknown');
        $output[] = "- Role: " . ($user['is_admin'] ? 'Administrator' : ucfirst($user['user_type'] ?? 'User'));
        $output[] = "- Current Date: " . ($user['current_date'] ?? date('Y-m-d'));
        $output[] = "";

        // Accessible modules
        $output[] = "ACCESSIBLE MODULES: " . implode(', ', $context['accessible_modules'] ?? array());
        $output[] = "";

        // Module data summaries
        $data = $context['data'] ?? array();
        foreach ($data as $module => $module_data) {
            if (isset($module_data['note'])) {
                continue; // Skip modules with access notes
            }

            $output[] = strtoupper($module) . " SUMMARY:";

            if (isset($module_data['total_count'])) {
                $output[] = "- Total: " . $module_data['total_count'];
            }

            if (isset($module_data['overdue_count']) && $module_data['overdue_count'] > 0) {
                $output[] = "- Overdue: " . $module_data['overdue_count'];
            }

            if (isset($module_data['open_count']) && $module_data['open_count'] > 0) {
                $output[] = "- Open: " . $module_data['open_count'];
            }

            if (isset($module_data['by_status']) && !empty($module_data['by_status'])) {
                $output[] = "- By Status: " . json_encode($module_data['by_status']);
            }

            if (isset($module_data['total_due']) && $module_data['total_due'] > 0) {
                $output[] = "- Total Due: " . number_format($module_data['total_due'], 2);
            }

            $output[] = "";
        }

        return implode("\n", $output);
    }
}
