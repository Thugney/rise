<?php

namespace App\Libraries;

/**
 * Polar.sh Integration Library
 * Handles subscription management via Polar.sh payment gateway
 */
class Polar {

    private $access_token;
    private $webhook_secret;
    private $product_id;
    private $organization_id;
    private $api_base = 'https://api.polar.sh/v1';
    private $Ai_settings_model;
    private $Ai_subscriptions_model;
    private $Users_model;

    public function __construct() {
        $this->Ai_settings_model = model("App\Models\Ai_settings_model");
        $this->Ai_subscriptions_model = model("App\Models\Ai_subscriptions_model");
        $this->Users_model = model("App\Models\Users_model");

        $this->access_token = $this->Ai_settings_model->get_setting('polar_access_token');
        $this->webhook_secret = $this->Ai_settings_model->get_setting('polar_webhook_secret');
        $this->product_id = $this->Ai_settings_model->get_setting('polar_product_id');
        $this->organization_id = $this->Ai_settings_model->get_setting('polar_organization_id');
    }

    /**
     * Check if Polar integration is configured
     */
    public function is_configured() {
        return !empty($this->access_token) && !empty($this->product_id);
    }

    /**
     * Check if user has an active subscription
     */
    public function has_active_subscription($user_id) {
        if (!$this->Ai_settings_model->is_polar_enabled()) {
            return true;
        }
        return $this->Ai_subscriptions_model->has_active_subscription($user_id);
    }

    /**
     * Get user's subscription status
     */
    public function get_subscription_status($user_id) {
        $subscription = $this->Ai_subscriptions_model->get_by_user_id($user_id);

        if (!$subscription) {
            return array(
                'has_subscription' => false,
                'status' => 'none',
                'message' => 'No subscription found'
            );
        }

        return array(
            'has_subscription' => true,
            'is_active' => $this->has_active_subscription($user_id),
            'status' => $subscription->status,
            'plan_name' => $subscription->plan_name,
            'current_period_end' => $subscription->current_period_end,
            'canceled_at' => $subscription->canceled_at
        );
    }

    /**
     * Generate Polar checkout URL for subscription
     */
    public function get_checkout_url($user_id, $email, $success_url = null, $cancel_url = null) {
        if (!$this->is_configured()) {
            return null;
        }

        if (!$success_url) {
            $success_url = get_uri('ai_assistant/subscription_success');
        }
        if (!$cancel_url) {
            $cancel_url = get_uri('ai_assistant');
        }

        $payload = array(
            'product_id' => $this->product_id,
            'success_url' => $success_url,
            'customer_email' => $email,
            'metadata' => array(
                'user_id' => (string)$user_id
            )
        );

        $response = $this->api_request('POST', '/checkouts/custom', $payload);

        if ($response && isset($response['url'])) {
            return $response['url'];
        }

        return null;
    }

    /**
     * Verify webhook signature using Standard Webhooks spec
     */
    public function verify_webhook_signature($payload, $signature) {
        if (empty($this->webhook_secret) || empty($signature)) {
            return false;
        }

        $parts = explode(',', $signature);

        // Handle Standard Webhooks format
        $timestamp = null;
        $provided_signature = null;

        foreach ($parts as $part) {
            $part = trim($part);
            if (strpos($part, 't=') === 0) {
                $timestamp = substr($part, 2);
            } elseif (strpos($part, 'v1=') === 0) {
                $provided_signature = substr($part, 3);
            }
        }

        if (!$provided_signature) {
            if (count($parts) >= 3) {
                $timestamp = $parts[1];
                $provided_signature = $parts[2];
            } else {
                return false;
            }
        }

        if (!$timestamp) {
            $timestamp = time();
        }

        $signed_payload = $timestamp . '.' . $payload;
        $expected_signature = base64_encode(
            hash_hmac('sha256', $signed_payload, base64_decode($this->webhook_secret), true)
        );

        return hash_equals($expected_signature, $provided_signature);
    }

    /**
     * Process webhook event data and update local subscription
     */
    public function process_webhook_event($event) {
        $event_type = get_array_value($event, 'type');
        $data = get_array_value($event, 'data', array());

        if (empty($event_type) || empty($data)) {
            log_message('error', 'Polar webhook: Invalid event structure');
            return false;
        }

        $metadata = get_array_value($data, 'metadata', array());
        $user_id = get_array_value($metadata, 'user_id');

        if (!$user_id) {
            $customer_id = get_array_value($data, 'customer_id');
            if ($customer_id) {
                $existing = $this->Ai_subscriptions_model->get_by_polar_customer_id($customer_id);
                if ($existing) {
                    $user_id = $existing->user_id;
                }
            }
        }

        if (!$user_id) {
            log_message('error', 'Polar webhook: Could not determine user_id');
            return false;
        }

        $polar_data = array(
            'customer_id' => get_array_value($data, 'customer_id', ''),
            'subscription_id' => get_array_value($data, 'id', ''),
            'status' => $this->map_polar_status(get_array_value($data, 'status')),
            'current_period_start' => $this->format_polar_date(get_array_value($data, 'current_period_start')),
            'current_period_end' => $this->format_polar_date(get_array_value($data, 'current_period_end')),
            'canceled_at' => $this->format_polar_date(get_array_value($data, 'canceled_at')),
            'plan_name' => get_array_value($data, 'product_name', 'ai_assistant_monthly'),
        );

        switch ($event_type) {
            case 'subscription.created':
            case 'subscription.active':
            case 'subscription.updated':
                return $this->Ai_subscriptions_model->upsert_from_polar($user_id, $polar_data);

            case 'subscription.canceled':
                $polar_data['status'] = 'canceled';
                $polar_data['canceled_at'] = date('Y-m-d H:i:s');
                return $this->Ai_subscriptions_model->upsert_from_polar($user_id, $polar_data);

            case 'subscription.revoked':
                $polar_data['status'] = 'inactive';
                return $this->Ai_subscriptions_model->upsert_from_polar($user_id, $polar_data);

            default:
                log_message('info', "Polar webhook: Unhandled event type: {$event_type}");
                return true;
        }
    }

    private function map_polar_status($polar_status) {
        $status_map = array(
            'active' => 'active',
            'trialing' => 'trialing',
            'past_due' => 'past_due',
            'canceled' => 'canceled',
            'incomplete' => 'inactive',
            'incomplete_expired' => 'inactive',
            'unpaid' => 'past_due',
        );
        return get_array_value($status_map, $polar_status, 'inactive');
    }

    private function format_polar_date($date) {
        if (empty($date)) {
            return null;
        }
        $timestamp = strtotime($date);
        return $timestamp !== false ? date('Y-m-d H:i:s', $timestamp) : null;
    }

    private function api_request($method, $endpoint, $data = null) {
        $url = $this->api_base . $endpoint;
        $ch = curl_init();

        $headers = array(
            'Authorization: Bearer ' . $this->access_token,
            'Content-Type: application/json',
            'Accept: application/json',
        );

        curl_setopt_array($ch, array(
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER => $headers,
            CURLOPT_TIMEOUT => 30,
        ));

        if ($method === 'POST') {
            curl_setopt($ch, CURLOPT_POST, true);
            if ($data) {
                curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
            }
        }

        $response = curl_exec($ch);
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curl_error = curl_error($ch);
        curl_close($ch);

        if ($curl_error) {
            log_message('error', "Polar API curl error: {$curl_error}");
            return null;
        }

        if ($http_code >= 400) {
            log_message('error', "Polar API error ({$http_code}): {$response}");
            return null;
        }

        return json_decode($response, true);
    }
}
