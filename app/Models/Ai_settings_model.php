<?php

namespace App\Models;

class Ai_settings_model extends Crud_model {

    protected $table = null;
    private static $cache = array();

    function __construct() {
        $this->table = 'ai_settings';
        parent::__construct($this->table);
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
            return $this->ci_save(array('setting_value' => $value), $existing->id);
        } else {
            // Insert new
            return $this->ci_save(array(
                'setting_name' => $name,
                'setting_value' => $value
            ));
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
