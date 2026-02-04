<?php

/**
 * AI Assistant Helper Functions
 *
 * These functions provide easy access to AI settings and subscription checks
 */

if (!function_exists('get_ai_setting')) {
    /**
     * Get an AI setting value
     *
     * @param string $name Setting name
     * @param mixed $default Default value if not found
     * @return mixed Setting value
     */
    function get_ai_setting($name, $default = null) {
        static $settings = null;

        if ($settings === null) {
            $Ai_settings_model = model("App\Models\Ai_settings_model");
            $settings = $Ai_settings_model->get_all_settings();
        }

        return isset($settings[$name]) ? $settings[$name] : $default;
    }
}

if (!function_exists('is_ai_enabled')) {
    /**
     * Check if AI assistant is enabled
     *
     * @return bool
     */
    function is_ai_enabled() {
        return get_ai_setting('ai_enabled') === '1';
    }
}

if (!function_exists('is_polar_enabled')) {
    /**
     * Check if Polar.sh subscription requirement is enabled
     *
     * @return bool
     */
    function is_polar_enabled() {
        return get_ai_setting('polar_enabled') === '1';
    }
}

if (!function_exists('has_ai_subscription')) {
    /**
     * Check if a user has an active AI subscription
     *
     * @param int $user_id User ID
     * @return bool
     */
    function has_ai_subscription($user_id) {
        $Ai_subscriptions_model = model("App\Models\Ai_subscriptions_model");
        return $Ai_subscriptions_model->has_active_subscription($user_id);
    }
}

if (!function_exists('can_use_ai')) {
    /**
     * Check if a user can use the AI assistant
     * Takes into account: AI enabled, subscription requirements, and user permissions
     *
     * @param int $user_id User ID
     * @param bool $is_admin Whether user is admin
     * @return array ['allowed' => bool, 'reason' => string|null]
     */
    function can_use_ai($user_id, $is_admin = false) {
        // Check if AI is enabled globally
        if (!is_ai_enabled()) {
            return array('allowed' => false, 'reason' => 'ai_disabled');
        }

        // Admins always have access
        if ($is_admin) {
            return array('allowed' => true, 'reason' => null);
        }

        // Check subscription requirement for non-admins
        if (is_polar_enabled()) {
            if (!has_ai_subscription($user_id)) {
                return array('allowed' => false, 'reason' => 'subscription_required');
            }
        }

        return array('allowed' => true, 'reason' => null);
    }
}

if (!function_exists('get_ai_rate_limit')) {
    /**
     * Get the AI rate limit per hour
     *
     * @return int Queries per hour
     */
    function get_ai_rate_limit() {
        $limit = get_ai_setting('ai_rate_limit_per_hour', 60);
        return (int) $limit;
    }
}

if (!function_exists('check_ai_rate_limit')) {
    /**
     * Check if user has exceeded rate limit
     *
     * @param int $user_id User ID
     * @return bool True if within limit, false if exceeded
     */
    function check_ai_rate_limit($user_id) {
        $limit = get_ai_rate_limit();

        if ($limit <= 0) {
            return true; // No limit
        }

        $Ai_conversations_model = model("App\Models\Ai_conversations_model");
        $query_count = $Ai_conversations_model->get_query_count($user_id, 'hour');

        return $query_count < $limit;
    }
}
