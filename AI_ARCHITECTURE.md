# AI Assistant Architecture - Production Ready

**AI Provider**: DeepSeek (`deepseek-chat` model, 128K context window)
**Payment Gateway**: Polar.sh (subscription-based access control)
**Framework**: CodeIgniter 4.6.3 (PHP 8.1+)
**Timeline**: 8 weeks (aligned with MVP.md)

## Business Model

```
┌─────────────────────────────────────────────────────────────┐
│                     User Journey                             │
├─────────────────────────────────────────────────────────────┤
│  1. User registers → Free CRM access                        │
│  2. User clicks "AI Assistant" → Paywall shown              │
│  3. User subscribes via Polar.sh → Webhook updates status   │
│  4. User gets active subscription → AI Assistant unlocked   │
│  5. Subscription expires → AI disabled, data preserved      │
└─────────────────────────────────────────────────────────────┘
```

## 1. Core Security Principles

### 1.1 Permission Inheritance Model
- AI assistants inherit **exact same permissions** as the logged-in user
- No permission escalation under any circumstances
- All data access filtered through existing `Permission_manager` library
- Row-level security enforced at data query level

### 1.2 Defense-in-Depth Security Layers
1. **Input Validation Layer**: AI requests validated against same rules as user inputs
2. **Permission Check Layer**: All operations verified against user's permissions
3. **Data Filtering Layer**: Query results filtered based on `own`, `specific`, `assigned_only` permissions
4. **Output Validation Layer**: AI responses scanned for permission violations
5. **Audit Logging Layer**: All interactions logged with full context

## 2. System Architecture

### 2.1 Component Overview

```
┌─────────────────────────────────────────────────────────────┐
│                    User Interface Layer                      │
│  ┌─────────────────────────────────────────────────────┐    │
│  │ Chat Widget (JavaScript + AJAX to Ai_assistant)     │    │
│  │ • Floating button in bottom-right                   │    │
│  │ • Sidebar panel for conversation                    │    │
│  │ • Module-aware (knows current page context)         │    │
│  └─────────────────────────────────────────────────────┘    │
└─────────────────────────────────────────────────────────────┘
                               │
┌─────────────────────────────────────────────────────────────┐
│         Ai_assistant Controller (extends Security_Controller)│
│  ┌──────────────────────────────────────────────────────┐   │
│  │ • Inherits auth from Security_Controller             │   │
│  │ • Access to $this->permission_manager                │   │
│  │ • Access to $this->login_user (with permissions)     │   │
│  │ • Methods: query(), history(), clear_session()       │   │
│  └──────────────────────────────────────────────────────┘   │
└─────────────────────────────────────────────────────────────┘
                               │
┌─────────────────────────────────────────────────────────────┐
│                    New Libraries (app/Libraries/)            │
│  ┌────────────────────┐  ┌────────────────────────────┐     │
│  │ Ai_context.php     │  │ Ai_deepseek.php            │     │
│  │ • build_context()  │  │ • chat() - API call        │     │
│  │ • Uses Permission_ │  │ • build_system_prompt()    │     │
│  │   manager methods  │  │ • Token counting           │     │
│  └────────────────────┘  └────────────────────────────┘     │
└─────────────────────────────────────────────────────────────┘
                               │
┌─────────────────────────────────────────────────────────────┐
│              Existing RISE Infrastructure                    │
│  ┌─────────────────┐  ┌─────────────────┐                   │
│  │ Permission_     │  │ Crud_model      │                   │
│  │ manager.php     │  │ (base model)    │                   │
│  │ • can_view_*()  │  │ • Hooks on CRUD │                   │
│  │ • can_manage_*()│  │ • Activity logs │                   │
│  └─────────────────┘  └─────────────────┘                   │
│  ┌─────────────────┐  ┌─────────────────┐                   │
│  │ app_hooks()     │  │ 93 Models       │                   │
│  │ • do_action()   │  │ (Tasks, Clients │                   │
│  │ • apply_filters │  │  Projects, etc) │                   │
│  └─────────────────┘  └─────────────────┘                   │
└─────────────────────────────────────────────────────────────┘
                               │
                               ▼
┌─────────────────────────────────────────────────────────────┐
│                    DeepSeek API                              │
│  Endpoint: https://api.deepseek.com/chat/completions        │
│  Model: deepseek-chat | Context: 128K tokens                │
└─────────────────────────────────────────────────────────────┘
```

### 2.2 Key Components Description

#### 2.2.1 Ai_assistant Controller
Location: `app/Controllers/Ai_assistant.php`

```php
class Ai_assistant extends Security_Controller {
    function __construct() {
        parent::__construct();
        // Inherits: $this->login_user, $this->permission_manager
    }

    function query() {
        // AJAX endpoint for chat queries
        // Returns JSON response
    }
}
```

#### 2.2.2 Ai_context Library
Location: `app/Libraries/Ai_context.php`

Responsibilities:
- Calls existing `Permission_manager` methods (`can_view_clients()`, `can_manage_tasks()`, etc.)
- Uses `show_own_*_user_id()` helpers from `Security_Controller`
- Builds structured context array for AI system prompt
- No custom permission logic - relies entirely on existing infrastructure

#### 2.2.3 Ai_deepseek Library
Location: `app/Libraries/Ai_deepseek.php`

Responsibilities:
- HTTP client for DeepSeek API
- System prompt construction with permission context
- Token counting (128K context window)
- Error handling with fallback messages
- Rate limiting enforcement

## 3. Action Execution Pattern

### 3.1 Secure Execution Flow

```php
class AiActionExecutor {
    public function executeAction($action, $parameters, $user) {
        // 1. Validate action is allowed for user
        if (!$this->isActionAllowed($action, $user)) {
            throw new PermissionException("Action not allowed");
        }

        // 2. Load action handler based on module
        $handler = $this->getActionHandler($action['module']);

        // 3. Validate parameters using existing validation rules
        $this->validateParameters($parameters, $action['validation_rules']);

        // 4. Apply permission filters to parameters
        $filteredParams = $this->applyPermissionFilters($parameters, $user);

        // 5. Execute action through handler
        $result = $handler->execute($filteredParams, $user);

        // 6. Trigger application hooks
        $this->triggerHooks($action, $filteredParams, $result);

        // 7. Log audit trail
        $this->logAudit($action, $filteredParams, $result, $user);

        return $result;
    }
}
```

### 3.2 Module-Specific Action Handlers

Each module (Proposals, Tasks, Estimates, etc.) gets its own action handler that understands:
- Data structure and validation rules
- Permission requirements
- Business logic flow
- Hook trigger points

## 4. Database Schema

### 4.1 Core Tables

```sql
-- AI Conversations (follows RISE conventions: rise_ prefix, deleted flag, utf8)
CREATE TABLE `rise_ai_conversations` (
  `id` INT PRIMARY KEY AUTO_INCREMENT,
  `user_id` INT NOT NULL,
  `session_id` VARCHAR(128) NOT NULL,
  `module` VARCHAR(50) DEFAULT NULL COMMENT 'task, project, client, etc.',
  `user_query` TEXT NOT NULL,
  `assistant_response` TEXT,
  `tokens_used` INT DEFAULT 0,
  `context_snapshot` TEXT COMMENT 'JSON: permission state at query time',
  `deleted` INT(1) NOT NULL DEFAULT 0,
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
  INDEX `idx_user_session` (`user_id`, `session_id`),
  INDEX `idx_created_at` (`created_at`),
  FOREIGN KEY (`user_id`) REFERENCES `rise_users`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

-- AI Settings (global configuration, not per-user)
CREATE TABLE `rise_ai_settings` (
  `id` INT PRIMARY KEY AUTO_INCREMENT,
  `setting_name` VARCHAR(100) NOT NULL,
  `setting_value` TEXT,
  `deleted` INT(1) NOT NULL DEFAULT 0,
  UNIQUE KEY `uk_setting_name` (`setting_name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

-- Polar.sh Subscription tracking
CREATE TABLE `rise_ai_subscriptions` (
  `id` INT PRIMARY KEY AUTO_INCREMENT,
  `user_id` INT NOT NULL,
  `polar_customer_id` VARCHAR(128) NOT NULL,
  `polar_subscription_id` VARCHAR(128) NOT NULL,
  `status` ENUM('active', 'canceled', 'past_due', 'trialing', 'inactive') DEFAULT 'inactive',
  `current_period_start` DATETIME,
  `current_period_end` DATETIME,
  `canceled_at` DATETIME DEFAULT NULL,
  `plan_name` VARCHAR(100) DEFAULT 'ai_assistant_monthly',
  `deleted` INT(1) NOT NULL DEFAULT 0,
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  UNIQUE KEY `uk_user_id` (`user_id`),
  INDEX `idx_polar_subscription` (`polar_subscription_id`),
  INDEX `idx_status` (`status`),
  FOREIGN KEY (`user_id`) REFERENCES `rise_users`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

-- Default settings for DeepSeek + Polar.sh
INSERT INTO `rise_ai_settings` (`setting_name`, `setting_value`) VALUES
('ai_enabled', '0'),
('ai_provider', 'deepseek'),
('ai_model', 'deepseek-chat'),
('ai_api_key', ''),
('ai_api_endpoint', 'https://api.deepseek.com/chat/completions'),
('ai_max_tokens', '4096'),
('ai_temperature', '0.7'),
('ai_rate_limit_per_hour', '60'),
('ai_allowed_user_types', 'staff'),
('polar_enabled', '0'),
('polar_access_token', ''),
('polar_webhook_secret', ''),
('polar_product_id', ''),
('polar_organization_id', '');
```

Note: Permission checks use existing `Permission_manager` - no separate AI permission layer needed. Subscription status is the first gate before permission checks.

### 4.2 Audit Strategy

For MVP, audit logging uses the existing `rise_ai_conversations` table with `context_snapshot` field storing permission state at query time. This provides:
- Who asked what (user_id, user_query)
- What context they had (context_snapshot JSON)
- What the AI responded (assistant_response)
- Token usage tracking (tokens_used)

Post-MVP, if detailed action auditing is needed, the existing `Activity_logs_model` can be extended to track AI-initiated actions with an `ai_generated` flag.

## 5. Integration with Existing Modules

### 5.1 Proposal Module Integration

```php
class ProposalAiHandler extends BaseAiHandler {
    public function getCapabilities($user) {
        return [
            'read_proposal' => $this->permissionManager->can_view_proposals(),
            'create_proposal' => $this->permissionManager->can_manage_proposals(),
            'update_proposal' => $this->permissionManager->can_manage_proposals($proposal_id),
            'generate_proposal_content' => $this->permissionManager->can_manage_proposals(),
            'send_proposal' => $this->permissionManager->can_manage_proposals($proposal_id)
        ];
    }

    public function generateProposalContent($params, $user) {
        // 1. Validate user can access proposal
        if (!$this->permissionManager->can_manage_proposals($params['proposal_id'])) {
            throw new PermissionException("Cannot access proposal");
        }

        // 2. Load proposal data
        $proposal = $this->Proposals_model->get_one($params['proposal_id']);

        // 3. Apply permission filters to related data
        $clientData = $this->applyClientPermissionFilters(
            $this->Clients_model->get_one($proposal->client_id)
        );

        // 4. Generate content using AI
        $aiContent = $this->aiService->generateProposalContent([
            'proposal' => $proposal,
            'client' => $clientData,
            'tone' => $params['tone'],
            'length' => $params['length']
        ]);

        // 5. Return content (doesn't auto-save)
        return [
            'content' => $aiContent,
            'requires_user_approval' => true
        ];
    }

    public function saveProposalContent($params, $user) {
        // Uses existing controller validation pattern
        $validationRules = [
            'proposal_id' => 'required|numeric',
            'content' => 'required'
        ];

        $this->validateParameters($params, $validationRules);

        // Check edit permission
        if (!$this->permissionManager->can_manage_proposals($params['proposal_id'])) {
            throw new PermissionException("Cannot edit proposal");
        }

        // Prepare data exactly as controller would
        $proposalData = [
            'content' => $params['content'],
            'last_updated_by' => $user->id
        ];

        // Use existing model method
        $result = $this->Proposals_model->ci_save($proposalData, $params['proposal_id']);

        // Trigger hooks
        $this->triggerHooks('proposal_updated', [
            'proposal_id' => $params['proposal_id'],
            'updated_by' => $user->id,
            'ai_generated' => true
        ]);

        return $result;
    }
}
```

### 5.2 Task Module Integration

```php
class TaskAiHandler extends BaseAiHandler {
    public function createTask($params, $user) {
        // Replicate controller validation
        $validationRules = [
            'title' => 'required',
            'project_id' => 'numeric',
            'assigned_to' => 'numeric'
        ];

        $this->validateParameters($params, $validationRules);

        // Check create permission based on context
        $context = $params['project_id'] ? 'project' : 'general';
        if (!$this->permissionManager->can_create_tasks($context)) {
            throw new PermissionException("Cannot create tasks in this context");
        }

        // Check assignment permission
        if ($params['assigned_to'] && !$this->canAssignTaskToUser($params['assigned_to'], $user)) {
            throw new PermissionException("Cannot assign task to this user");
        }

        // Prepare task data (mirroring controller logic)
        $taskData = [
            'title' => $params['title'],
            'description' => $params['description'] ?? '',
            'project_id' => $params['project_id'] ?? 0,
            'assigned_to' => $params['assigned_to'] ?? 0,
            'status_id' => $params['status_id'] ?? get_setting('default_task_status'),
            'created_by' => $user->id,
            'created_at' => get_current_utc_time()
        ];

        // Use existing model
        $taskId = $this->Tasks_model->ci_save($taskData);

        // Trigger hooks
        $this->triggerHooks('task_created', [
            'task_id' => $taskId,
            'created_by' => $user->id,
            'ai_generated' => true
        ]);

        return $taskId;
    }
}
```

## 6. Natural Language Processing Pipeline

### 6.1 Intent Recognition System

```
User Query → Text Normalization → Entity Extraction → Intent Classification → Action Mapping
```

### 6.2 Intent-Action Mapping Configuration

```php
// Configuration defining what actions map to what intents
$intentActions = [
    'create_proposal' => [
        'handler' => 'ProposalAiHandler',
        'method' => 'createProposal',
        'permission_required' => 'can_manage_proposals',
        'validation_rules' => [
            'client_id' => 'required|numeric',
            'title' => 'required'
        ]
    ],
    'generate_proposal_content' => [
        'handler' => 'ProposalAiHandler',
        'method' => 'generateProposalContent',
        'permission_required' => 'can_manage_proposals',
        'requires_ownership' => true
    ],
    'create_task' => [
        'handler' => 'TaskAiHandler',
        'method' => 'createTask',
        'permission_required' => 'can_create_tasks',
        'context_specific' => true
    ]
];
```

## 7. Voice Command Integration

### 7.1 Voice Processing Pipeline

```
Voice Input → Speech-to-Text → Intent Recognition → Action Execution → Text-to-Speech Response
```

### 7.2 Security Considerations for Voice
- Voice commands require explicit user confirmation for destructive actions
- Session-based voice authentication
- Rate limiting on voice commands
- Audio recording retention policy compliance

## 8. Permission Enforcement Implementation

### 8.1 Permission-Aware Data Querying

```php
class PermissionAwareQueryBuilder {
    public function buildQuery($model, $user, $permissionType) {
        $query = $model->newQuery();

        switch ($permissionType) {
            case 'own':
                $query->where('created_by', $user->id)
                      ->orWhere('owner_id', $user->id);
                break;

            case 'specific':
                $specificIds = $this->getUserSpecificIds($user, $model);
                if (!empty($specificIds)) {
                    $query->whereIn('group_id', $specificIds);
                } else {
                    $query->where('1', '0'); // No access
                }
                break;

            case 'assigned_only':
                $query->where('assigned_to', $user->id);
                break;

            case 'read_only':
                $query->where($this->buildReadOnlyConditions($user));
                break;
        }

        return $query;
    }
}
```

### 8.2 Dynamic Permission Checking

```php
class DynamicPermissionChecker {
    public function checkActionPermission($action, $entityId, $user) {
        // Get user's permission for the module
        $modulePermission = $this->permissionManager->get_permission($action['module']);

        // Check based on permission type
        switch ($modulePermission) {
            case 'all':
                return true;

            case 'own':
                return $this->isUserOwner($entityId, $user);

            case 'specific':
                return $this->isInUserSpecificGroups($entityId, $user);

            case 'assigned_only':
                return $this->isAssignedToUser($entityId, $user);

            default:
                return false;
        }
    }
}
```

## 9. Audit and Compliance

### 9.1 Comprehensive Audit Trail

```php
class AiAuditLogger {
    public function logAction($action, $params, $result, $user) {
        $auditData = [
            'user_id' => $user->id,
            'action' => $action,
            'parameters' => $this->sanitizeParameters($params),
            'result' => $result,
            'permission_context' => $this->getPermissionContext($user),
            'ip_address' => $this->request->getIPAddress(),
            'user_agent' => $this->request->getUserAgent(),
            'timestamp' => get_current_utc_time()
        ];

        // Store in database
        $this->AiAudit_model->save($auditData);

        // Send to SIEM system if configured
        $this->sendToSiem($auditData);
    }
}
```

### 9.2 GDPR/Compliance Features
- Right to explanation: AI decisions can be explained
- Data deletion: AI conversations can be deleted per user request
- Consent management: Opt-in for AI features
- Data export: AI interactions exportable

## 9.5 Polar.sh Integration Architecture

### 9.5.1 Subscription Flow

```
┌──────────────┐      ┌──────────────┐      ┌──────────────┐
│    User      │      │   RISE CRM   │      │   Polar.sh   │
└──────┬───────┘      └──────┬───────┘      └──────┬───────┘
       │                      │                      │
       │  Click "Subscribe"   │                      │
       │─────────────────────>│                      │
       │                      │                      │
       │                      │  Create checkout URL │
       │                      │─────────────────────>│
       │                      │                      │
       │  Redirect to Polar   │<─────────────────────│
       │<─────────────────────│                      │
       │                      │                      │
       │  Complete payment    │                      │
       │─────────────────────────────────────────────>
       │                      │                      │
       │                      │  Webhook: sub.active │
       │                      │<─────────────────────│
       │                      │                      │
       │                      │  Update DB status    │
       │                      │──────┐               │
       │                      │      │               │
       │                      │<─────┘               │
       │                      │                      │
       │  AI Assistant ready  │                      │
       │<─────────────────────│                      │
```

### 9.5.2 Polar Webhook Controller

Location: `app/Controllers/Polar_webhook.php`

```php
class Polar_webhook extends App_Controller {
    // No Security_Controller - public endpoint

    public function index() {
        $payload = file_get_contents('php://input');
        $signature = $this->request->getHeader('Webhook-Signature');

        // Verify signature using Standard Webhooks spec
        if (!$this->verify_signature($payload, $signature)) {
            return $this->response->setStatusCode(401);
        }

        $event = json_decode($payload, true);
        $this->handle_event($event);

        return $this->response->setStatusCode(200);
    }

    private function handle_event($event) {
        $type = $event['type'];
        $data = $event['data'];

        switch ($type) {
            case 'subscription.created':
            case 'subscription.active':
                $this->activate_subscription($data);
                break;
            case 'subscription.canceled':
                $this->cancel_subscription($data);
                break;
            case 'subscription.revoked':
                $this->revoke_subscription($data);
                break;
        }
    }
}
```

### 9.5.3 Polar Library

Location: `app/Libraries/Polar.php`

Key methods:
- `has_active_subscription($user_id)` - Check DB for active status
- `get_checkout_url($user_id, $email)` - Generate Polar checkout link
- `verify_webhook_signature($payload, $signature)` - Validate incoming webhooks
- `get_customer_state($customer_id)` - Query Polar API for real-time status

### 9.5.4 Subscription Check in AI Controller

```php
class Ai_assistant extends Security_Controller {

    public function query() {
        // GATE 1: Subscription check
        $polar = new Polar();
        if (!$polar->has_active_subscription($this->login_user->id)) {
            echo json_encode([
                'error' => 'subscription_required',
                'message' => 'AI Assistant requires an active subscription',
                'checkout_url' => $polar->get_checkout_url(
                    $this->login_user->id,
                    $this->login_user->email
                )
            ]);
            return;
        }

        // GATE 2: Permission check (existing logic)
        // ... rest of query handling
    }
}
```

### 9.5.5 Webhook Events Handled

| Event | Action |
|-------|--------|
| `subscription.created` | Create `rise_ai_subscriptions` record with `status='trialing'` |
| `subscription.active` | Set `status='active'`, update period dates |
| `subscription.canceled` | Set `status='canceled'`, store `canceled_at` |
| `subscription.revoked` | Set `status='inactive'` (immediate access removal) |
| `subscription.updated` | Update `current_period_start`, `current_period_end` |

## 10. Deployment and Scalability

### 10.1 Phased Deployment Strategy (8 Weeks Total)

**Phase 1: Foundation + Polar.sh (Weeks 1-2)**
- Polar.sh account setup, product creation, webhook configuration
- `rise_ai_subscriptions` table and `Polar_webhook` controller
- `Polar` library with subscription checking
- Basic paywall UI (subscribe button, status display)
- DeepSeek integration with basic rate limiting
- Conversation logging to `rise_ai_conversations`

**Phase 2: AI Queries + Subscription Gate (Weeks 3-4)**
- Natural language queries for Tasks module only
- Permission-aware data filtering via existing `Permission_manager`
- Subscription check before every AI query
- Chat widget UI (floating button, sidebar panel)
- Graceful paywall messaging for non-subscribers

**Phase 3: Multi-Module + Content Generation (Weeks 5-6)**
- Expand to Projects, Clients, Invoices modules
- Data summarization (project status, client activity)
- Email/message drafting assistance
- User confirmation required before any writes
- Hook integration (`app_hooks()->do_action()`)

**Phase 4: Polish + Analytics (Weeks 7-8)**
- Admin settings UI (DeepSeek + Polar.sh configuration)
- Subscription management portal (cancel, upgrade)
- Usage analytics dashboard (queries per user, revenue)
- Performance optimization (response caching)

### 10.2 Scalability Considerations
- File-based caching initially (CI4 cache helper)
- Redis caching for production (optional)
- DeepSeek API rate limit handling with exponential backoff
- Conversation history pruning (configurable retention)

## 11. Monitoring and Alerting

### 11.1 Key Metrics to Monitor
- AI response latency
- Permission violation rate
- User adoption metrics
- Error rates by action type
- Cost per AI request

### 11.2 Alerting Rules
- Permission violation spikes
- AI service downtime
- Unusual usage patterns
- Cost threshold breaches
- Audit log failures

## 12. Risk Mitigation

### 12.1 Security Risks and Mitigations

| Risk | Impact | Mitigation |
|------|--------|------------|
| Permission Bypass | Critical | Multiple validation layers, audit logging, regular penetration testing |
| Data Leakage | Critical | Query-level filtering, output validation, encryption at rest |
| AI Hallucination | High | Fact verification, user confirmation, context grounding |
| Denial of Service | Medium | Rate limiting, query optimization, resource quotas |
| Model Poisoning | Medium | Input sanitization, model isolation, regular retraining |

### 12.2 Operational Risks
- **Cost Overruns**: Implement usage quotas, cost monitoring
- **Performance Degradation**: Caching, async processing, load testing
- **User Adoption**: Training materials, intuitive UI, gradual rollout

## 13. Testing Strategy

### 13.1 Security Testing
- Penetration testing of AI endpoints
- Permission bypass attempts
- SQL injection testing on AI queries
- Cross-user data access testing

### 13.2 Integration Testing
- All module action handlers
- Permission scenarios for each user type
- Hook execution verification
- Audit log completeness

### 13.3 Performance Testing
- Concurrent user load testing
- Voice processing latency
- AI service response times
- Database query optimization

## 14. Configuration Management

### 14.1 Configuration via Database

AI settings stored in `rise_ai_settings` table (consistent with RISE pattern of storing settings in DB):

```php
// Reading settings (uses existing get_setting() helper pattern)
$ai_enabled = get_ai_setting('ai_enabled');
$api_key = get_ai_setting('ai_api_key');

// Helper function in app/Helpers/general_helper.php
function get_ai_setting($name) {
    $ci = get_instance();
    $setting = $ci->db->table('rise_ai_settings')
        ->where('setting_name', $name)
        ->where('deleted', 0)
        ->get()
        ->getRow();
    return $setting ? $setting->setting_value : null;
}
```

### 14.2 DeepSeek Configuration

| Setting | Default | Description |
|---------|---------|-------------|
| `ai_enabled` | `0` | Master switch for AI features |
| `ai_provider` | `deepseek` | AI provider identifier |
| `ai_model` | `deepseek-chat` | Model name |
| `ai_api_key` | (empty) | DeepSeek API key |
| `ai_api_endpoint` | `https://api.deepseek.com/chat/completions` | API endpoint |
| `ai_max_tokens` | `4096` | Max response tokens |
| `ai_temperature` | `0.7` | Response randomness (0-1) |
| `ai_rate_limit_per_hour` | `60` | Queries per user per hour |
| `ai_allowed_user_types` | `staff` | Comma-separated: `staff`, `client` |

### 14.3 Polar.sh Configuration

| Setting | Default | Description |
|---------|---------|-------------|
| `polar_enabled` | `0` | Enable subscription requirement |
| `polar_access_token` | (empty) | Polar.sh API access token |
| `polar_webhook_secret` | (empty) | Webhook signature verification secret |
| `polar_product_id` | (empty) | AI Assistant product ID in Polar |
| `polar_organization_id` | (empty) | Your Polar organization ID |

## 15. Conclusion

This architecture provides a production-ready, monetized AI assistant system that:

1. **Leverages existing infrastructure** - Uses `Permission_manager`, `Crud_model` hooks, and CI4 patterns
2. **Subscription-gated access** - Polar.sh handles payments, webhooks update local subscription status
3. **Two-gate security** - Subscription check first, then permission validation
4. **Minimal new code** - 3 libraries, 2 controllers, 1 model, 3 database tables
5. **DeepSeek + Polar.sh** - Cost-effective AI with modern subscription management
6. **8-week delivery** - Phased rollout starting with Polar.sh integration

The system is designed to be incrementally deployable, with subscription infrastructure in Phase 1 before AI features roll out in Phase 2+.

---

*Last Updated: 2026-02-04*
*Architecture Version: 1.2*
*AI Provider: DeepSeek (deepseek-chat)*
*Payment Gateway: Polar.sh*
*Framework: CodeIgniter 4.6.3*
*Status: Ready for Implementation*