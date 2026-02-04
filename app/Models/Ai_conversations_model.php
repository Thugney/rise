<?php

namespace App\Models;

class Ai_conversations_model extends Crud_model {

    protected $table = null;

    function __construct() {
        $this->table = 'ai_conversations';
        parent::__construct($this->table);
    }

    /**
     * Log a conversation entry
     *
     * @param int $user_id User ID
     * @param string $session_id Session ID
     * @param string $query User query
     * @param string $response AI response
     * @param int $tokens_used Tokens used
     * @param string $module Module context
     * @param array $context_snapshot Permission context snapshot
     * @return int|bool Conversation ID or false
     */
    function log_conversation($user_id, $session_id, $query, $response, $tokens_used = 0, $module = null, $context_snapshot = array()) {
        $data = array(
            'user_id' => $user_id,
            'session_id' => $session_id,
            'user_query' => $query,
            'assistant_response' => $response,
            'tokens_used' => $tokens_used,
            'module' => $module,
            'context_snapshot' => !empty($context_snapshot) ? json_encode($context_snapshot) : null,
            'created_at' => date('Y-m-d H:i:s')
        );

        return $this->ci_save($data);
    }

    /**
     * Get conversation history for a user session
     *
     * @param int $user_id User ID
     * @param string $session_id Session ID
     * @param int $limit Number of messages to retrieve
     * @return array Conversation history
     */
    function get_session_history($user_id, $session_id, $limit = 20) {
        $this->db_builder->where('user_id', $user_id);
        $this->db_builder->where('session_id', $session_id);
        $this->db_builder->where('deleted', 0);
        $this->db_builder->orderBy('created_at', 'DESC');
        $this->db_builder->limit($limit);

        $result = $this->db_builder->get();
        $rows = $result->getResult();

        // Reverse to get chronological order
        return array_reverse($rows);
    }

    /**
     * Get recent conversations for a user
     *
     * @param int $user_id User ID
     * @param int $limit Number of conversations
     * @return array Recent conversations
     */
    function get_user_conversations($user_id, $limit = 50) {
        $this->db_builder->where('user_id', $user_id);
        $this->db_builder->where('deleted', 0);
        $this->db_builder->orderBy('created_at', 'DESC');
        $this->db_builder->limit($limit);

        return $this->db_builder->get()->getResult();
    }

    /**
     * Clear session history
     *
     * @param int $user_id User ID
     * @param string $session_id Session ID
     * @return bool Success
     */
    function clear_session($user_id, $session_id) {
        return $this->db_builder->where(array(
            'user_id' => $user_id,
            'session_id' => $session_id
        ))->update(array('deleted' => 1));
    }

    /**
     * Get total tokens used by user in current period
     *
     * @param int $user_id User ID
     * @param string $period 'hour', 'day', 'month'
     * @return int Total tokens
     */
    function get_tokens_used($user_id, $period = 'hour') {
        $table = $this->db->prefixTable('ai_conversations');

        switch ($period) {
            case 'hour':
                $time_condition = "created_at >= DATE_SUB(NOW(), INTERVAL 1 HOUR)";
                break;
            case 'day':
                $time_condition = "created_at >= DATE_SUB(NOW(), INTERVAL 1 DAY)";
                break;
            case 'month':
                $time_condition = "created_at >= DATE_SUB(NOW(), INTERVAL 1 MONTH)";
                break;
            default:
                $time_condition = "1=1";
        }

        $sql = "SELECT COALESCE(SUM(tokens_used), 0) as total_tokens
                FROM $table
                WHERE user_id = ? AND deleted = 0 AND $time_condition";

        $result = $this->db->query($sql, array($user_id));
        $row = $result->getRow();

        return $row ? (int)$row->total_tokens : 0;
    }

    /**
     * Get query count for rate limiting
     *
     * @param int $user_id User ID
     * @param string $period 'hour', 'day'
     * @return int Query count
     */
    function get_query_count($user_id, $period = 'hour') {
        $table = $this->db->prefixTable('ai_conversations');

        switch ($period) {
            case 'hour':
                $time_condition = "created_at >= DATE_SUB(NOW(), INTERVAL 1 HOUR)";
                break;
            case 'day':
                $time_condition = "created_at >= DATE_SUB(NOW(), INTERVAL 1 DAY)";
                break;
            default:
                $time_condition = "created_at >= DATE_SUB(NOW(), INTERVAL 1 HOUR)";
        }

        $sql = "SELECT COUNT(*) as query_count
                FROM $table
                WHERE user_id = ? AND deleted = 0 AND $time_condition";

        $result = $this->db->query($sql, array($user_id));
        $row = $result->getRow();

        return $row ? (int)$row->query_count : 0;
    }

    // ========================================
    // Analytics Methods for Admin Dashboard
    // ========================================

    /**
     * Get queries count since a specific datetime
     *
     * @param string $since Datetime string
     * @return int Count
     */
    function get_queries_count_since($since) {
        $table = $this->db->prefixTable('ai_conversations');
        $sql = "SELECT COUNT(*) as count FROM $table WHERE deleted = 0 AND created_at >= ?";
        $result = $this->db->query($sql, array($since));
        $row = $result->getRow();
        return $row ? (int)$row->count : 0;
    }

    /**
     * Get count of active users in the last N days
     *
     * @param int $days Number of days
     * @return int Count
     */
    function get_active_users_count($days = 30) {
        $table = $this->db->prefixTable('ai_conversations');
        $sql = "SELECT COUNT(DISTINCT user_id) as count
                FROM $table
                WHERE deleted = 0 AND created_at >= DATE_SUB(NOW(), INTERVAL ? DAY)";
        $result = $this->db->query($sql, array($days));
        $row = $result->getRow();
        return $row ? (int)$row->count : 0;
    }

    /**
     * Get total tokens used across all users
     *
     * @return int Total tokens
     */
    function get_total_tokens() {
        $table = $this->db->prefixTable('ai_conversations');
        $sql = "SELECT COALESCE(SUM(tokens_used), 0) as total FROM $table WHERE deleted = 0";
        $result = $this->db->query($sql);
        $row = $result->getRow();
        return $row ? (int)$row->total : 0;
    }

    /**
     * Get top users by query count
     *
     * @param int $limit Number of users
     * @return array Top users
     */
    function get_top_users($limit = 10) {
        $conversations_table = $this->db->prefixTable('ai_conversations');
        $users_table = $this->db->prefixTable('users');

        $sql = "SELECT u.id, u.first_name, u.last_name, u.image, COUNT(c.id) as query_count
                FROM $conversations_table c
                JOIN $users_table u ON c.user_id = u.id
                WHERE c.deleted = 0
                GROUP BY u.id, u.first_name, u.last_name, u.image
                ORDER BY query_count DESC
                LIMIT ?";

        return $this->db->query($sql, array($limit))->getResult();
    }

    /**
     * Get recent conversations with user info
     *
     * @param int $limit Number of conversations
     * @return array Recent conversations
     */
    function get_recent_conversations($limit = 20) {
        $conversations_table = $this->db->prefixTable('ai_conversations');
        $users_table = $this->db->prefixTable('users');

        $sql = "SELECT c.*, CONCAT(u.first_name, ' ', u.last_name) as user_name
                FROM $conversations_table c
                LEFT JOIN $users_table u ON c.user_id = u.id
                WHERE c.deleted = 0
                ORDER BY c.created_at DESC
                LIMIT ?";

        return $this->db->query($sql, array($limit))->getResult();
    }

    /**
     * Get all conversations for export
     *
     * @return array All conversations
     */
    function get_all_for_export() {
        $conversations_table = $this->db->prefixTable('ai_conversations');
        $users_table = $this->db->prefixTable('users');

        $sql = "SELECT c.*, CONCAT(u.first_name, ' ', u.last_name) as user_name
                FROM $conversations_table c
                LEFT JOIN $users_table u ON c.user_id = u.id
                WHERE c.deleted = 0
                ORDER BY c.created_at DESC";

        return $this->db->query($sql)->getResult();
    }

    /**
     * Get daily usage statistics for chart
     *
     * @param int $days Number of days
     * @return array Chart data
     */
    function get_daily_usage_stats($days = 30) {
        $table = $this->db->prefixTable('ai_conversations');

        $sql = "SELECT DATE(created_at) as date,
                       COUNT(*) as queries,
                       COALESCE(SUM(tokens_used), 0) as tokens
                FROM $table
                WHERE deleted = 0 AND created_at >= DATE_SUB(NOW(), INTERVAL ? DAY)
                GROUP BY DATE(created_at)
                ORDER BY date ASC";

        $results = $this->db->query($sql, array($days))->getResult();

        $labels = array();
        $queries = array();
        $tokens = array();

        // Fill in missing days with zeros
        $start_date = new \DateTime("-{$days} days");
        $end_date = new \DateTime();
        $interval = new \DateInterval('P1D');
        $period = new \DatePeriod($start_date, $interval, $end_date);

        $data_map = array();
        foreach ($results as $row) {
            $data_map[$row->date] = array(
                'queries' => (int)$row->queries,
                'tokens' => (int)$row->tokens
            );
        }

        foreach ($period as $date) {
            $date_str = $date->format('Y-m-d');
            $labels[] = $date->format('M d');
            $queries[] = $data_map[$date_str]['queries'] ?? 0;
            $tokens[] = $data_map[$date_str]['tokens'] ?? 0;
        }

        return array(
            'labels' => $labels,
            'queries' => $queries,
            'tokens' => $tokens
        );
    }

    /**
     * Get monthly usage for a user (for subscription tracking)
     *
     * @param int $user_id User ID
     * @return int Query count this month
     */
    function get_monthly_queries($user_id) {
        $table = $this->db->prefixTable('ai_conversations');
        $sql = "SELECT COUNT(*) as count
                FROM $table
                WHERE user_id = ? AND deleted = 0
                AND MONTH(created_at) = MONTH(CURRENT_DATE())
                AND YEAR(created_at) = YEAR(CURRENT_DATE())";
        $result = $this->db->query($sql, array($user_id));
        $row = $result->getRow();
        return $row ? (int)$row->count : 0;
    }
}
