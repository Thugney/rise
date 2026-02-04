<?php

defined('PLUGINPATH') or exit('No direct script access allowed');

/*
  Plugin Name: Two-Factor Authentication
  Description: An extra layer of security for your RISE profile.
  Version: 1.1.1
  Requires at least: 3.5
  Author: ClassicCompiler
  Author URL: https://codecanyon.net/user/classiccompiler
 */

use App\Controllers\Security_Controller;

//redirect to two-factor auth page if the cookie isn't found
app_hooks()->add_action('app_hook_before_app_access', function ($data) {
    helper('cookie');
    $login_user_id = get_array_value($data, "login_user_id");
    $redirect = get_array_value($data, "redirect");
    $has_valid_cookie = false;

    $cookie = get_cookie("twofactor_cookie_of_user_" . $login_user_id);
    if ($cookie) {
        $user_id = decode_id($cookie, "twofactor_cookie");
        if ($user_id == $login_user_id) {
            $has_valid_cookie = true;
        }
    }

    $TwoFactor_user_settings_model = new \TwoFactor\Models\TwoFactor_user_settings_model();
    $Users_model = model('App\Models\Users_model');

    $user_settings = $TwoFactor_user_settings_model->get_one_where(array("user_id" => $login_user_id, "deleted" => 0));
    $user_info = $Users_model->get_one($login_user_id);

    if (!$has_valid_cookie && $redirect) {
        //check global settings
        if (($user_info->user_type === "staff" && get_twofactor_setting("enable_email_authentication_for_all_team_members")) || ($user_info->user_type === "client" && get_twofactor_setting("enable_email_authentication_for_all_client_contacts"))) {
            app_redirect("twofactor");
        }

        //check user settings
        if ($user_settings->enable_twofactor && $user_settings->authorized) {
            app_redirect("twofactor");
        }
    }
});

//add ajax-tab to the user's profile
app_hooks()->add_filter('app_filter_staff_profile_ajax_tab', 'twofactor_profile_ajax_tab');
app_hooks()->add_filter('app_filter_client_profile_ajax_tab', 'twofactor_profile_ajax_tab');

if (!function_exists('twofactor_profile_ajax_tab')) {

    function twofactor_profile_ajax_tab($hook_tabs, $user_id = 0)
    {
        $instance = new Security_Controller();
        if ($instance->login_user->id === $user_id) {
            $hook_tabs[] = array(
                "title" => app_lang('twofactor_twofactor_authentication'),
                "url" => get_uri("twofactor_settings/user_settings"),
                "target" => "tab-twofactor_twofactor_authentication"
            );
        }

        return $hook_tabs;
    }
}

//install dependencies
register_installation_hook("TwoFactor", function ($item_purchase_code) {
    include PLUGINPATH . "TwoFactor/install/do_install.php";
});

//update plugin
use TwoFactor\Controllers\TwoFactor_Updates;

register_update_hook("TwoFactor", function () {
    $update = new TwoFactor_Updates();
    return $update->index();
});

//uninstallation: remove data from database
register_uninstallation_hook("TwoFactor", function () {
    $dbprefix = get_db_prefix();
    $db = db_connect('default');

    $sql_query = "DROP TABLE IF EXISTS `" . $dbprefix . "twofactor_settings`;";
    $db->query($sql_query);

    $sql_query = "DROP TABLE IF EXISTS `" . $dbprefix . "twofactor_verification`;";
    $db->query($sql_query);

    $sql_query = "DROP TABLE IF EXISTS `" . $dbprefix . "twofactor_user_settings`;";
    $db->query($sql_query);

    $sql_query = "DELETE FROM `" . $dbprefix . "email_templates` WHERE `" . $dbprefix . "email_templates`.`template_name`='twofactor_twofactor_authentication';";
    $db->query($sql_query);
});

app_hooks()->add_filter('app_filter_email_templates', function ($templates_array) {
    $templates_array["account"]["twofactor_twofactor_authentication"] = array("FIRST_NAME", "LAST_NAME", "LOGIN_EMAIL", "CODE", "APP_TITLE", "COMPANY_NAME", "LOGO_URL", "SIGNATURE");

    return $templates_array;
});

//add admin setting menu item
app_hooks()->add_filter('app_filter_admin_settings_menu', function ($settings_menu) {
    $settings_menu["plugins"][] = array("name" => "twofactor", "url" => "twofactor_settings");
    return $settings_menu;
});
