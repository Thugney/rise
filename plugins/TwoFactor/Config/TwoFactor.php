<?php

/* Don't change or add any new config in this file */

namespace TwoFactor\Config;

use CodeIgniter\Config\BaseConfig;
use TwoFactor\Models\TwoFactor_settings_model;

class TwoFactor extends BaseConfig {

    public $app_settings_array = array();

    public function __construct() {
        $twofactor_settings_model = new TwoFactor_settings_model();

        $settings = $twofactor_settings_model->get_all_settings()->getResult();
        foreach ($settings as $setting) {
            $this->app_settings_array[$setting->setting_name] = $setting->setting_value;
        }
    }

}
