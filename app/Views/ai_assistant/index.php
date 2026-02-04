<?php
/**
 * AI Assistant Loader
 * Loads the AI chat widget for authenticated users
 * Include this in the main layout
 */

// Only load if user is logged in and AI is enabled
if (isset($login_user) && $login_user && is_ai_enabled()) {

    // Check access
    $ai_access = array(
        'allowed' => can_access_ai($login_user->id),
        'checkout_url' => null,
        'reason' => null,
        'message' => null
    );

    // If subscription is required but user doesn't have one
    if (!$ai_access['allowed'] && get_ai_setting('polar_enabled') === '1') {
        $ai_access['reason'] = 'subscription_required';
        $ai_access['message'] = app_lang('ai_subscription_required');
        $ai_access['checkout_url'] = get_ai_checkout_url($login_user->id);
    }

    // Load the widget view
    echo view('ai_assistant/chat_widget', array(
        'login_user' => $login_user,
        'ai_access' => $ai_access
    ));
}
