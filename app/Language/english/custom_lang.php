<?php //copy from default_lang.php file and update

$lang["example"] = "Example";

// AI Assistant Language Keys
$lang["ai_assistant"] = "Assistant";
$lang["ai_settings"] = "AI Settings";
$lang["ai_configuration"] = "AI Configuration";
$lang["ai_enable_assistant"] = "Enable Assistant";
$lang["ai_enable_assistant_help"] = "Enable or disable the assistant feature for all users";
$lang["ai_api_key"] = "API Key";
$lang["ai_api_key_help"] = "Your DeepSeek API key";
$lang["ai_model"] = "AI Model";
$lang["ai_model_help"] = "Select the AI model to use";
$lang["ai_max_tokens"] = "Max Tokens";
$lang["ai_max_tokens_help"] = "Maximum number of tokens in AI responses";
$lang["ai_temperature"] = "Temperature";
$lang["ai_temperature_help"] = "Controls randomness in responses (0.0 = focused, 1.0 = creative)";
$lang["ai_rate_limit"] = "Rate Limit (per minute)";
$lang["ai_rate_limit_help"] = "Maximum AI queries per user per minute (0 = unlimited)";
$lang["ai_require_subscription"] = "Require Subscription";
$lang["ai_require_subscription_help"] = "Users must have an active Polar subscription to use AI";
$lang["ai_test_connection"] = "Test Connection";
$lang["ai_connection_success"] = "AI connection successful!";
$lang["ai_connection_failed"] = "AI connection failed. Please check your API key.";

// Polar.sh Settings
$lang["polar_settings"] = "Polar.sh Settings";
$lang["polar_enable"] = "Enable Polar Integration";
$lang["polar_enable_help"] = "Enable subscription management via Polar.sh";
$lang["polar_access_token"] = "Access Token";
$lang["polar_access_token_help"] = "Your Polar.sh API access token";
$lang["polar_webhook_secret"] = "Webhook Secret";
$lang["polar_webhook_secret_help"] = "Secret key for verifying webhook signatures";
$lang["polar_product_id"] = "Product ID";
$lang["polar_product_id_help"] = "The Polar product ID for AI subscription";
$lang["polar_organization_id"] = "Organization ID";
$lang["polar_organization_id_help"] = "Your Polar organization ID";
$lang["polar_webhook_url"] = "Webhook URL";
$lang["polar_webhook_url_help"] = "Configure this URL in your Polar.sh webhook settings";
$lang["polar_test_connection"] = "Test Polar Connection";
$lang["polar_connection_success"] = "Polar connection successful!";
$lang["polar_connection_failed"] = "Polar connection failed. Please check your credentials.";
$lang["polar_not_configured"] = "Polar.sh is not configured";

// Subscription Statistics
$lang["subscription_statistics"] = "Subscription Statistics";
$lang["total_subscribers"] = "Total Subscribers";
$lang["active_subscriptions"] = "Active Subscriptions";
$lang["ai_queries_today"] = "AI Queries Today";
$lang["no_statistics_available"] = "No statistics available";

// AI Chat Widget
$lang["ai_welcome_message"] = "Hello! I'm your assistant. I can help you with CRM tasks, project management questions, and general business guidance. How can I assist you today?";
$lang["ai_input_placeholder"] = "Type your message...";
$lang["ai_input_hint"] = "Press Enter to send, Shift+Enter for new line";
$lang["ai_new_chat"] = "New Chat";
$lang["ai_query_empty"] = "Please enter a message";
$lang["ai_query_failed"] = "Failed to process your query. Please try again.";
$lang["ai_error_occurred"] = "An error occurred. Please try again.";
$lang["ai_connection_error"] = "Connection error. Please check your internet connection.";
$lang["ai_rate_limit_exceeded"] = "Rate limit exceeded. Please wait a moment before sending another message.";
$lang["ai_session_cleared"] = "Chat session cleared";

// Subscription Messages
$lang["ai_subscription_required"] = "An active subscription is required to use the assistant.";
$lang["ai_subscription_required_title"] = "Subscription Required";
$lang["ai_subscription_required_message"] = "Unlock the power of AI assistance with a subscription. Get instant help with your CRM tasks, reports, and more.";
$lang["ai_subscribe_now"] = "Subscribe Now";
$lang["ai_subscription_not_available"] = "Subscription service is not available at this time.";
$lang["ai_assistant_disabled"] = "Assistant is currently disabled.";
$lang["ai_subscription_activated"] = "Your assistant subscription has been activated successfully!";

// Settings Menu (duplicate removed - using earlier definition)

// Phase 3: Drafting and Summarization
$lang["ai_draft_instructions_required"] = "Please provide instructions for the draft.";
$lang["ai_draft_failed"] = "Failed to generate draft. Please try again.";
$lang["ai_ticket_id_required"] = "Ticket ID is required.";
$lang["ai_module_required"] = "Please specify a module.";
$lang["ai_summary_failed"] = "Failed to generate summary. Please try again.";
$lang["ai_record_info_required"] = "Record type and ID are required.";
$lang["ai_suggestions_failed"] = "Failed to generate suggestions. Please try again.";

// AI Drafting UI
$lang["ai_draft_email"] = "Draft Email";
$lang["ai_draft_response"] = "Draft Response";
$lang["ai_generate_summary"] = "Generate Summary";
$lang["ai_suggest_actions"] = "Suggest Actions";
$lang["ai_drafting"] = "Drafting...";
$lang["ai_copy_draft"] = "Copy Draft";
$lang["ai_use_draft"] = "Use This Draft";
$lang["ai_regenerate"] = "Regenerate";

// Tone options
$lang["ai_tone_professional"] = "Professional";
$lang["ai_tone_friendly"] = "Friendly";
$lang["ai_tone_formal"] = "Formal";
$lang["ai_tone_helpful"] = "Helpful";
$lang["ai_tone_concise"] = "Concise";

// Phase 4: Analytics Dashboard
$lang["ai_analytics"] = "AI Analytics";
$lang["ai_usage_overview"] = "Usage Overview";
$lang["total_ai_queries"] = "Total AI Queries";
$lang["queries_today"] = "Queries Today";
$lang["active_ai_users"] = "Active AI Users";
$lang["total_tokens_used"] = "Total Tokens Used";
$lang["ai_usage_chart"] = "AI Usage (Last 30 Days)";
$lang["top_ai_users"] = "Top AI Users";
$lang["recent_ai_conversations"] = "Recent Conversations";
$lang["active_subscribers"] = "Active Subscribers";
$lang["canceled_subscriptions"] = "Canceled";
$lang["conversion_rate"] = "Conversion Rate";
$lang["queries_this_month"] = "Queries This Month";
$lang["period_end"] = "Period End";
$lang["plan"] = "Plan";
$lang["export_logs"] = "Export Logs";
$lang["no_data_available"] = "No data available";
$lang["no_conversations_yet"] = "No conversations yet";
$lang["no_subscribers_yet"] = "No subscribers yet";
$lang["queries"] = "Queries";
$lang["tokens"] = "Tokens";

return $lang;