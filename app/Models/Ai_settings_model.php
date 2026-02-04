<?php

namespace App\Models;

class Ai_settings_model extends Crud_model {

    protected $table = null;
    private static $cache = array();
    private static $table_checked = false;

    function __construct() {
        $this->table = 'ai_settings';
        parent::__construct($this->table);

        // Ensure table exists on first use
        if (!self::$table_checked) {
            $this->ensure_table_exists();
            self::$table_checked = true;
        }
    }

    /**
     * Ensure the ai_settings table exists, create if not
     */
    private function ensure_table_exists() {
        $table_name = $this->db->prefixTable('ai_settings');

        // Check if table exists
        $query = $this->db->query("SHOW TABLES LIKE '$table_name'");
        if ($query->getNumRows() == 0) {
            // Create the table
            $sql = "CREATE TABLE IF NOT EXISTS `$table_name` (
                `id` INT PRIMARY KEY AUTO_INCREMENT,
                `setting_name` VARCHAR(100) NOT NULL,
                `setting_value` TEXT,
                `deleted` INT(1) NOT NULL DEFAULT 0,
                UNIQUE KEY `uk_setting_name` (`setting_name`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci";

            $this->db->query($sql);

            // Insert default settings
            $defaults = array(
                array('setting_name' => 'ai_enabled', 'setting_value' => '0'),
                array('setting_name' => 'ai_provider', 'setting_value' => 'deepseek'),
                array('setting_name' => 'ai_model', 'setting_value' => 'deepseek-chat'),
                array('setting_name' => 'ai_api_key', 'setting_value' => ''),
                array('setting_name' => 'ai_api_endpoint', 'setting_value' => 'https://api.deepseek.com/chat/completions'),
                array('setting_name' => 'ai_max_tokens', 'setting_value' => '4096'),
                array('setting_name' => 'ai_temperature', 'setting_value' => '0.7'),
                array('setting_name' => 'ai_rate_limit_per_minute', 'setting_value' => '10'),
                array('setting_name' => 'ai_rate_limit_per_hour', 'setting_value' => '60'),
                array('setting_name' => 'polar_enabled', 'setting_value' => '0'),
                array('setting_name' => 'polar_access_token', 'setting_value' => ''),
                array('setting_name' => 'polar_webhook_secret', 'setting_value' => ''),
                array('setting_name' => 'polar_product_id', 'setting_value' => ''),
                array('setting_name' => 'polar_organization_id', 'setting_value' => ''),
            );

            $builder = $this->db->table($table_name);
            foreach ($defaults as $default) {
                $builder->insert($default);
            }

            // Re-initialize the db_builder after table creation
            $this->db_builder = $this->db->table($table_name);
        }
    }

    /**
     * Get a single AI setting value by name
     *
     * @param string $name Setting name
     * @return string|null Setting value or null if not found
     */
    function get_setting($name) {
        // Check cache first
        if (isset(self::$cache[$name])) {
            return self::$cache[$name];
        }

        $result = $this->get_one_where(array(
            'setting_name' => $name,
            'deleted' => 0
        ));

        $value = $result && $result->setting_value !== '' ? $result->setting_value : null;

        // Cache the result
        self::$cache[$name] = $value;

        return $value;
    }

    /**
     * Save or update an AI setting
     *
     * @param string $name Setting name
     * @param string $value Setting value
     * @return bool Success
     */
    function save_setting($name, $value) {
        try {
            // Clear cache for this setting
            if (isset(self::$cache[$name])) {
                unset(self::$cache[$name]);
            }

            $existing = $this->get_one_where(array(
                'setting_name' => $name,
                'deleted' => 0
            ));

            if ($existing && $existing->id) {
                // Update existing
                $result = $this->ci_save(array('setting_value' => $value), $existing->id);
                if (!$result) {
                    log_message('error', "Ai_settings_model: Failed to update setting '$name' (id: {$existing->id})");
                }
                return $result;
            } else {
                // Insert new
                $result = $this->ci_save(array(
                    'setting_name' => $name,
                    'setting_value' => $value
                ));
                if (!$result) {
                    log_message('error', "Ai_settings_model: Failed to insert setting '$name'");
                }
                return $result;
            }
        } catch (\Exception $e) {
            log_message('error', "Ai_settings_model: Exception saving '$name': " . $e->getMessage());
            return false;
        }
    }

    /**
     * Get all AI settings as an associative array
     *
     * @return array Settings array
     */
    function get_all_settings() {
        $result = $this->get_all_where(array('deleted' => 0));
        $settings = array();

        foreach ($result->getResult() as $row) {
            $settings[$row->setting_name] = $row->setting_value;
            self::$cache[$row->setting_name] = $row->setting_value;
        }

        return $settings;
    }

    /**
     * Check if AI assistant is enabled
     *
     * @return bool
     */
    function is_ai_enabled() {
        return $this->get_setting('ai_enabled') === '1';
    }

    /**
     * Check if Polar.sh subscription requirement is enabled
     *
     * @return bool
     */
    function is_polar_enabled() {
        return $this->get_setting('polar_enabled') === '1';
    }

    /**
     * Clear the settings cache
     */
    function clear_cache() {
        self::$cache = array();
    }
}
