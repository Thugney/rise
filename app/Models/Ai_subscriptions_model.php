<?php

namespace App\Models;

class Ai_subscriptions_model extends Crud_model {

    protected $table = null;

    function __construct() {
        $this->table = 'ai_subscriptions';
        parent::__construct($this->table);
    }

    /**
     * Get subscription by user ID
     *
     * @param int $user_id User ID
     * @return object|null Subscription object or null
     */
    function get_by_user_id($user_id) {
        $result = $this->get_one_where(array(
            'user_id' => $user_id,
            'deleted' => 0
        ));

        return ($result && $result->id) ? $result : null;
    }

    /**
     * Get subscription by Polar subscription ID
     *
     * @param string $polar_subscription_id Polar subscription ID
     * @return object|null Subscription object or null
     */
    function get_by_polar_subscription_id($polar_subscription_id) {
        $result = $this->get_one_where(array(
            'polar_subscription_id' => $polar_subscription_id,
            'deleted' => 0
        ));

        return ($result && $result->id) ? $result : null;
    }

    /**
     * Get subscription by Polar customer ID
     *
     * @param string $polar_customer_id Polar customer ID
     * @return object|null Subscription object or null
     */
    function get_by_polar_customer_id($polar_customer_id) {
        $result = $this->get_one_where(array(
            'polar_customer_id' => $polar_customer_id,
            'deleted' => 0
        ));

        return ($result && $result->id) ? $result : null;
    }

    /**
     * Check if user has an active subscription
     *
     * @param int $user_id User ID
     * @return bool True if subscription is active
     */
    function has_active_subscription($user_id) {
        $subscription = $this->get_by_user_id($user_id);

        if (!$subscription) {
            return false;
        }

        // Check status
        if ($subscription->status !== 'active' && $subscription->status !== 'trialing') {
            return false;
        }

        // Check if current period has not ended
        if ($subscription->current_period_end) {
            $end_time = strtotime($subscription->current_period_end);
            if ($end_time < time()) {
                return false;
            }
        }

        return true;
    }

    /**
     * Create or update subscription from Polar webhook data
     *
     * @param int $user_id User ID
     * @param array $polar_data Data from Polar webhook
     * @return int|bool Subscription ID or false
     */
    function upsert_from_polar($user_id, $polar_data) {
        $existing = $this->get_by_user_id($user_id);

        $data = array(
            'user_id' => $user_id,
            'polar_customer_id' => get_array_value($polar_data, 'customer_id', ''),
            'polar_subscription_id' => get_array_value($polar_data, 'subscription_id', ''),
            'status' => get_array_value($polar_data, 'status', 'inactive'),
            'current_period_start' => get_array_value($polar_data, 'current_period_start'),
            'current_period_end' => get_array_value($polar_data, 'current_period_end'),
            'canceled_at' => get_array_value($polar_data, 'canceled_at'),
            'plan_name' => get_array_value($polar_data, 'plan_name', 'ai_assistant_monthly'),
        );

        if ($existing && $existing->id) {
            return $this->ci_save($data, $existing->id);
        } else {
            return $this->ci_save($data);
        }
    }

    /**
     * Update subscription status
     *
     * @param int $subscription_id Subscription ID
     * @param string $status New status
     * @param array $extra_data Optional extra data to update
     * @return bool Success
     */
    function update_status($subscription_id, $status, $extra_data = array()) {
        $data = array_merge(array('status' => $status), $extra_data);
        return $this->ci_save($data, $subscription_id);
    }

    /**
     * Cancel subscription
     *
     * @param int $subscription_id Subscription ID
     * @return bool Success
     */
    function cancel_subscription($subscription_id) {
        return $this->ci_save(array(
            'status' => 'canceled',
            'canceled_at' => date('Y-m-d H:i:s')
        ), $subscription_id);
    }

    /**
     * Revoke subscription immediately
     *
     * @param int $subscription_id Subscription ID
     * @return bool Success
     */
    function revoke_subscription($subscription_id) {
        return $this->ci_save(array(
            'status' => 'inactive',
            'canceled_at' => date('Y-m-d H:i:s')
        ), $subscription_id);
    }

    /**
     * Get subscription details with user info
     *
     * @param int $subscription_id Subscription ID
     * @return object|null Subscription with user details
     */
    function get_details($subscription_id) {
        $subscriptions_table = $this->db->prefixTable('ai_subscriptions');
        $users_table = $this->db->prefixTable('users');

        $sql = "SELECT s.*, u.email, u.first_name, u.last_name
                FROM $subscriptions_table s
                LEFT JOIN $users_table u ON u.id = s.user_id
                WHERE s.id = ? AND s.deleted = 0";

        $result = $this->db->query($sql, array($subscription_id));
        return $result->getRow();
    }

    /**
     * Get all subscribers with their usage statistics
     *
     * @return array Subscribers with usage
     */
    function get_subscribers_with_usage() {
        $subscriptions_table = $this->db->prefixTable('ai_subscriptions');
        $users_table = $this->db->prefixTable('users');
        $conversations_table = $this->db->prefixTable('ai_conversations');

        $sql = "SELECT s.*, u.email, u.first_name, u.last_name, u.image,
                       (SELECT COUNT(*)
                        FROM $conversations_table c
                        WHERE c.user_id = s.user_id
                        AND c.deleted = 0
                        AND MONTH(c.created_at) = MONTH(CURRENT_DATE())
                        AND YEAR(c.created_at) = YEAR(CURRENT_DATE())) as monthly_queries
                FROM $subscriptions_table s
                LEFT JOIN $users_table u ON u.id = s.user_id
                WHERE s.deleted = 0
                ORDER BY s.status ASC, s.created_at DESC";

        return $this->db->query($sql)->getResult();
    }

    /**
     * Count subscriptions by status
     *
     * @param string $status Status to count
     * @return int Count
     */
    function count_by_status($status) {
        return $this->count_all_where(array(
            'status' => $status,
            'deleted' => 0
        ));
    }

    /**
     * Count all records matching where conditions
     *
     * @param array $where Where conditions
     * @return int Count
     */
    function count_all_where($where = array()) {
        $table = $this->db->prefixTable('ai_subscriptions');

        $conditions = array();
        $params = array();

        foreach ($where as $key => $value) {
            $conditions[] = "$key = ?";
            $params[] = $value;
        }

        $where_clause = count($conditions) > 0 ? 'WHERE ' . implode(' AND ', $conditions) : '';

        $sql = "SELECT COUNT(*) as count FROM $table $where_clause";
        $result = $this->db->query($sql, $params);
        $row = $result->getRow();

        return $row ? (int)$row->count : 0;
    }
}
