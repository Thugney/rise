# AI Personal Assistant Integration - MVP Plan

## Executive Summary

This document outlines a Minimum Viable Product (MVP) plan for integrating AI-powered personal assistants into the existing CRM and Project Management system (RISE). The core innovation is providing each user with a personalized AI assistant that operates strictly within their role-based permissions, accessing only the data and functionality that the user themselves can access.

**AI Provider**: DeepSeek (deepseek-chat model)
**Payment Gateway**: Polar.sh (subscription-based access)
**Framework**: CodeIgniter 4.6.3 (PHP 8.1+)
**Permission System**: Existing `Permission_manager` library at `app/Libraries/Permission_manager.php`

## Business Model

The AI Assistant is a **premium feature** behind a subscription paywall:
- Users register and use RISE CRM for free (base functionality)
- AI Assistant requires an active monthly subscription via Polar.sh
- Subscription status checked before every AI query
- Graceful degradation when subscription expires (feature disabled, not data lost)

## 1. Core Principles

### 1.1 Permission-First Architecture
- AI assistants inherit **exactly the same permissions** as the logged-in user
- No elevation of privileges - "If the user can't see it, the AI can't see it"
- Respects all existing permission types from `Permission_manager`:
  - `all` - Full access to module
  - `own` - Access records where `created_by`, `owner_id`, or `managers` includes user
  - `specific` - Access specific groups/types (via `client_specific`, `ticket_specific`)
  - `assigned_only` - Access only assigned records
  - `read_only` - View-only access
  - Module-specific variants: `manage_own_client_invoices`, `manage_own_created_estimates`, etc.

### 1.2 Personal Context Isolation
- Each user gets their own assistant instance
- Company users (staff) vs Client users get different assistant capabilities
- Assistants are aware of user type (`staff`, `client`, `lead`) and role permissions

### 1.3 Data Privacy & Security
- No cross-user data leakage
- All AI interactions logged for audit purposes
- No training on customer data without explicit consent

## 2. System Architecture

### 2.1 High-Level Components

```
┌─────────────────────────────────────────────────────────────────┐
│                    RISE Application (CI4)                        │
│  ┌─────────────────┐    ┌─────────────────┐                     │
│  │ Ai_assistant    │◄──►│ Security_       │                     │
│  │ Controller      │    │ Controller      │                     │
│  │ (extends        │    │ (auth + perms)  │                     │
│  │  Security_      │    └─────────────────┘                     │
│  │  Controller)    │              │                              │
│  └────────┬────────┘              ▼                              │
│           │              ┌─────────────────┐                     │
│           │              │ Permission_     │                     │
│           │              │ manager         │                     │
│           │              │ (app/Libraries) │                     │
│           │              └─────────────────┘                     │
│           ▼                                                      │
│  ┌─────────────────┐    ┌─────────────────┐                     │
│  │ Ai_context      │◄──►│ Crud_model      │                     │
│  │ Library         │    │ (base model     │                     │
│  │ (new)           │    │  with hooks)    │                     │
│  └────────┬────────┘    └─────────────────┘                     │
│           │                                                      │
└───────────┼──────────────────────────────────────────────────────┘
            │
            ▼
┌─────────────────────────────────────────────────────────────────┐
│                    DeepSeek API                                  │
│  Endpoint: https://api.deepseek.com/chat/completions            │
│  Model: deepseek-chat                                            │
└─────────────────────────────────────────────────────────────────┘
```

### 2.2 Component Descriptions

1. **Ai_assistant Controller** (`app/Controllers/Ai_assistant.php`)
   - Extends `Security_Controller` (inherits auth + permission infrastructure)
   - Entry point for all AI requests
   - Uses `$this->permission_manager` for access checks

2. **Ai_context Library** (`app/Libraries/Ai_context.php`)
   - Builds permission-aware context for AI prompts
   - Uses existing `Permission_manager` methods: `can_manage_*()`, `can_view_*()`
   - Generates query filters using `show_own_*_user_id()` helpers

3. **Ai_deepseek Library** (`app/Libraries/Ai_deepseek.php`)
   - HTTP client for DeepSeek API
   - Token counting and context window management (128K context)
   - Streaming response support

4. **Ai_conversations Model** (`app/Models/Ai_conversations_model.php`)
   - Extends `Crud_model` (inherits hook triggers)
   - Stores conversation history per user/session

5. **Hook Integration**
   - AI actions trigger existing hooks via `app_hooks()->do_action()`
   - Enables automation workflows through `Automations` library

## 3. User Roles & Assistant Capabilities

### 3.1 Staff Users (Team Members)

| Role Level | Example Permissions | Assistant Capabilities |
|------------|---------------------|------------------------|
| **Admin** | `all` permissions across all modules | Full system access, can analyze all data, generate reports, suggest optimizations |
| **Project Manager** | `can_manage_all_projects`, `can_create_tasks`, `can_edit_tasks` for assigned projects | Project analytics, task assignment suggestions, timeline predictions, resource allocation advice |
| **Sales Rep** | `client: "own"`, `lead: "own"`, `estimate: "manage_own_clients_estimates"` | Lead scoring, client communication suggestions, proposal generation, follow-up reminders |
| **Support Agent** | `ticket: "assigned_only"` or `ticket: "specific"` | Ticket categorization, response drafting, knowledge base article suggestions |

### 3.2 Client Users

| Client Type | Permissions | Assistant Capabilities |
|-------------|-------------|------------------------|
| **Primary Contact** | Full client access to projects, tickets, invoices | Project status updates, invoice explanations, ticket status, document requests |
| **Limited Contact** | Restricted client permissions | Limited to specific modules (e.g., only tickets) |

### 3.3 Lead Users
- Basic information access only
- Qualification assistance
- Next-step suggestions

## 4. MVP Feature Set - Phase 1

### 4.1 Core Assistant Features

1. **Natural Language Queries**
   - "Show me my overdue tasks"
   - "What's the status of Project X?"
   - "Which clients haven't paid this month?"

2. **Data Summarization**
   - Project status summaries
   - Client activity overviews
   - Team performance highlights

3. **Smart Suggestions**
   - Next actions for leads/clients
   - Task prioritization
   - Follow-up reminders

4. **Basic Automation**
   - Draft email responses
   - Generate meeting agendas
   - Create simple reports

### 4.2 Integration Points

| Module | Assistant Capabilities | Permission Considerations |
|--------|----------------------|---------------------------|
| **Projects** | Status updates, timeline analysis, resource allocation | Respects `can_manage_all_projects`, `show_assigned_tasks_only`, project membership |
| **Tasks** | Prioritization, deadline reminders, assignment suggestions | Follows `can_edit_tasks`, `can_update_only_assigned_tasks_status` |
| **Clients** | Activity summaries, communication history, opportunity identification | Adheres to `client: "all"`, `"own"`, `"specific"`, `"read_only"` |
| **Leads** | Qualification scoring, follow-up suggestions, conversion probability | Respects `lead: "all"` vs `"own"` permissions |
| **Tickets** | Categorization, response drafting, escalation suggestions | Follows `ticket: "all"`, `"assigned_only"`, `"specific"` |

## 5. Technical Implementation

### 5.1 Database Changes

```sql
-- AI Conversations table
CREATE TABLE `rise_ai_conversations` (
  `id` INT PRIMARY KEY AUTO_INCREMENT,
  `user_id` INT NOT NULL,
  `session_id` VARCHAR(128) NOT NULL,
  `module` VARCHAR(50) DEFAULT NULL COMMENT 'project, client, task, etc.',
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

-- Polar.sh Subscriptions table (tracks AI Assistant subscriptions)
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

-- AI Settings table
CREATE TABLE `rise_ai_settings` (
  `id` INT PRIMARY KEY AUTO_INCREMENT,
  `setting_name` VARCHAR(100) NOT NULL,
  `setting_value` TEXT,
  `deleted` INT(1) NOT NULL DEFAULT 0,
  UNIQUE KEY `uk_setting_name` (`setting_name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

-- Default settings (AI + Polar.sh)
INSERT INTO `rise_ai_settings` (`setting_name`, `setting_value`) VALUES
('ai_enabled', '0'),
('ai_provider', 'deepseek'),
('ai_model', 'deepseek-chat'),
('ai_api_key', ''),
('ai_max_tokens', '4096'),
('ai_temperature', '0.7'),
('ai_rate_limit_per_hour', '60'),
('polar_enabled', '0'),
('polar_access_token', ''),
('polar_webhook_secret', ''),
('polar_product_id', ''),
('polar_organization_id', '');
```

### 5.2 New Controller Structure

```
app/Controllers/Ai_assistant.php (extends Security_Controller)
├── __construct()        - init_permission_checker(), load libraries
├── index()              - Main chat interface view
├── query()              - AJAX: Process user query (checks subscription first)
├── history()            - AJAX: Get conversation history
├── clear_session()      - AJAX: Clear current session
├── settings()           - Admin: AI + Polar configuration page
├── subscribe()          - Redirect to Polar.sh checkout
└── subscription_status()- AJAX: Check current subscription status

app/Controllers/Polar_webhook.php (public endpoint, no auth)
├── index()              - Receives Polar.sh webhook events
├── verify_signature()   - Validates webhook signature
└── handle_event()       - Routes to event handlers:
    ├── subscription.created  → Create rise_ai_subscriptions record
    ├── subscription.active   → Set status = 'active'
    ├── subscription.canceled → Set status = 'canceled', store canceled_at
    ├── subscription.revoked  → Set status = 'inactive'
    └── subscription.updated  → Update period dates
```

### 5.3 Ai_context Library

Location: `app/Libraries/Ai_context.php`

Responsibilities:
- Build permission-aware context using existing `Permission_manager` methods
- Use `can_view_*()`, `can_manage_*()` for capability checks
- Use `show_own_*_user_id()` helpers for query filtering
- Return structured context array for AI system prompt

### 5.4 Ai_deepseek Library

Location: `app/Libraries/Ai_deepseek.php`

Configuration via `rise_ai_settings` table:
- `ai_api_key`: DeepSeek API key
- `ai_model`: Model name (default: `deepseek-chat`)
- `ai_max_tokens`: Max response tokens (default: 4096)
- `ai_temperature`: Response randomness (default: 0.7)

DeepSeek API endpoint: `https://api.deepseek.com/chat/completions`
Context window: 128K tokens (sufficient for most CRM queries)

### 5.5 Polar Library

Location: `app/Libraries/Polar.php`

Responsibilities:
- Check subscription status for user
- Generate checkout URLs for new subscriptions
- Verify webhook signatures
- Query Customer State API for real-time status

Key Methods:
```
has_active_subscription($user_id)  → bool
get_checkout_url($user_id, $email) → string (Polar checkout URL)
verify_webhook_signature($payload, $signature) → bool
get_customer_state($polar_customer_id) → array
```

Configuration via `rise_ai_settings` table:
- `polar_access_token`: Polar.sh API access token
- `polar_webhook_secret`: Webhook signature verification secret
- `polar_product_id`: AI Assistant product ID in Polar
- `polar_organization_id`: Your Polar organization ID

## 6. Security Implementation

### 6.1 Access Validation Pipeline (Subscription + Permission)

1. **Subscription Check (First Gate)**
   - Query `rise_ai_subscriptions` for user
   - Verify `status = 'active'` AND `current_period_end > NOW()`
   - If no active subscription → return `{"error": "subscription_required", "checkout_url": "..."}`

2. **Permission Validation (Second Gate)**
   - Check if user has any access to requested module
   - Validate query parameters against user permissions via `Permission_manager`

3. **Data Filtering**
   - Apply row-level security based on `own`, `specific`, `assigned_only`
   - Filter results using existing `Permission_manager` logic

4. **Post-Response Validation**
   - Scan AI response for unauthorized data references
   - Redact any potentially leaked information

### 6.2 Audit Logging

- All AI interactions logged with timestamp, user, and context
- Permission context stored with each query
- Regular security reviews of AI interaction logs

### 6.3 Rate Limiting & Abuse Prevention

- Query limits per user per hour
- Monitoring for permission probing attempts
- Alerting on unusual query patterns

## 7. Phased Rollout Plan

### Phase 1: Foundation + Polar.sh (Weeks 1-2)
- Polar.sh account setup, product creation ("AI Assistant Monthly")
- Configure webhook endpoint in Polar dashboard
- Implement `rise_ai_subscriptions` table, `Polar_webhook` controller
- `Polar` library with subscription checking
- Basic paywall UI (subscribe button, subscription status)
- DeepSeek integration with basic rate limiting

### Phase 2: AI Queries + Subscription Gate (Weeks 3-4)
- Natural language queries for Tasks module only
- Permission-aware data filtering via existing `Permission_manager`
- Subscription check before every AI query
- Chat widget UI (floating button, sidebar panel)
- Graceful paywall messaging for non-subscribers

### Phase 3: Multi-Module + Content Generation (Weeks 5-6)
- Expand to Projects, Clients, Invoices modules
- Data summarization (project status, client activity)
- Email/message drafting assistance
- User confirmation required before any writes
- Hook integration (`app_hooks()->do_action()`)

### Phase 4: Polish + Analytics (Weeks 7-8)
- Admin settings UI (DeepSeek API + Polar.sh configuration)
- Subscription management (view status, cancel link)
- Usage analytics dashboard (queries per user, token usage)
- Performance optimization (response caching)

## 8. UI/UX Integration

### 8.1 Assistant Interface
- Floating chat button in bottom-right corner
- Module-specific suggestions in context menus
- Keyboard shortcuts for common queries
- Voice input support (future phase)

### 8.2 Contextual Awareness
- Assistant knows which module/user is currently active
- Offers relevant suggestions based on current screen
- Can reference currently viewed record in conversations

## 9. Testing Strategy

### 9.1 Permission Testing
- Test each permission type with each user role
- Verify no data leakage between users
- Validate permission escalation prevention

### 9.2 Integration Testing
- Test with existing modules
- Verify no disruption to existing functionality
- Performance testing with large datasets

### 9.3 User Acceptance Testing
- Pilot with small group of users from each role
- Collect feedback on assistant usefulness
- Iterate based on real-world usage

## 10. Success Metrics

### 10.1 Quantitative Metrics
- User adoption rate (% of active users using assistant)
- Query success rate (% of queries successfully answered)
- Time saved (estimated minutes saved per user per day)
- Permission compliance (0% permission violations)

### 10.2 Qualitative Metrics
- User satisfaction scores
- Assistant helpfulness ratings
- Feature request frequency
- Reduction in support tickets for basic queries

## 11. Risks & Mitigations

| Risk | Impact | Mitigation |
|------|--------|------------|
| **Data Leakage** | High | Strict permission filtering, response validation, audit logging |
| **AI Hallucination** | Medium | Context grounding, fact verification, user confirmation for critical actions |
| **Performance Impact** | Medium | Query caching, rate limiting, async processing for complex queries |
| **User Adoption** | Low | Intuitive UI, contextual suggestions, training materials |
| **Cost Management** | Medium | Token usage tracking, query optimization, tiered pricing model |

## 12. Future Considerations

### 12.1 Advanced Features (Post-MVP)
- Voice input via Web Speech API
- Predictive analytics (lead scoring, project timeline predictions)
- Automated workflow suggestions via `Automations` library
- Calendar and email integration

### 12.2 Scalability
- Redis caching for frequent queries
- Queue system for long-running AI operations
- DeepSeek API rate limit handling with exponential backoff

## 13. Conclusion

This MVP plan provides a roadmap for integrating a **subscription-based AI personal assistant** into the RISE CRM and Project Management system. The 8-week phased approach starts with Polar.sh payment infrastructure in Phase 1, ensuring revenue generation is established before AI features roll out.

Key innovations:
- **Subscription-gated access** via Polar.sh - users pay monthly to unlock AI features
- **Permission-first architecture** via existing `Permission_manager` - AI sees only what the user sees
- **Two-gate security** - subscription check first, then permission validation

---

*Last Updated: 2026-02-04*
*Document Version: 1.2*
*AI Provider: DeepSeek (deepseek-chat)*
*Payment Gateway: Polar.sh*
*Framework: CodeIgniter 4.6.3*