<?php

namespace TwoFactor\Controllers;

use App\Controllers\Security_Controller;

class TwoFactor extends Security_Controller {

    protected $TwoFactor_verification_model;
    protected $TwoFactor_user_settings_model;

    function __construct() {
        parent::__construct(false);
        if (!isset($this->login_user->id)) {
            show_404();
        }

        $this->TwoFactor_verification_model = new \TwoFactor\Models\TwoFactor_verification_model();
        $this->TwoFactor_user_settings_model = new \TwoFactor\Models\TwoFactor_user_settings_model();
    }

    function index() {
        $user_settings = $this->get_user_settings();

        $this->check_cookie_and_redirect_to_dashboard();
        $view_data["user_settings"] = $user_settings;

        if ($user_settings->method === "google_authenticator") {
            //google authenticator
            return $this->template->view('TwoFactor\Views\twofactor\index', $view_data);
        }

        //email/sms
        //check if there has any valid code for this user
        //if exists, don't send a new one
        if ($user_settings->method === "email") {
            $options = array("email" => $this->login_user->email);
        } else {
            $user_info = $this->Users_model->get_one($this->login_user->id);
            $options = array("phone" => $user_info->phone);
        }

        $verification = $this->TwoFactor_verification_model->get_details($options)->getRow();

        if ($verification) {
            //verification code exists
            //check validity
            if ($user_settings->method === "email" && is_valid_twofactor_email_code($this->login_user, $verification->code)) {
                return $this->template->view('TwoFactor\Views\twofactor\index', $view_data);
            } else if (is_valid_twofactor_sms_code($this->login_user, $verification->code)) {
                return $this->template->view('TwoFactor\Views\twofactor\index', $view_data);
            }
        }

        if ($user_settings->method === "email") {
            $verification_data = twofactor_send_email_otp($this->login_user);
        } else {
            $verification_data = twofactor_send_sms_otp($this->login_user);
        }

        if (!$verification_data) {
            show_404();
        }

        //save verification code after sending email/sms
        $save_id = $this->TwoFactor_verification_model->ci_save($verification_data);
        if (!$save_id) {
            show_404();
        }

        //show two-factor form
        return $this->template->view('TwoFactor\Views\twofactor\index', $view_data);
    }

    private function get_user_settings() {
        //check user settings
        $user_settings = $this->TwoFactor_user_settings_model->get_one_where(array("user_id" => $this->login_user->id, "authorized" => 1, "deleted" => 0));
        if ($user_settings->id) {
            return $user_settings;
        }

        //check global settings
        $user_info = $this->Users_model->get_one($this->login_user->id);
        if (($user_info->user_type === "staff" && get_twofactor_setting("enable_email_authentication_for_all_team_members")) || ($user_info->user_type === "client" && get_twofactor_setting("enable_email_authentication_for_all_client_contacts"))) {
            $user_settings = new \stdClass();
            $user_settings->enable_twofactor = 1;
            $user_settings->method = "email";

            return $user_settings;
        }

        //user has no two-factor also not global
        return $user_settings;
    }

    function authenticate() {
        $this->validate_submitted_data(array(
            "twofactor_code" => "required|numeric",
        ));

        $user_settings = $this->get_user_settings();
        $twofactor_code = $this->request->getPost("twofactor_code");

        $authorized = 0;
        if ($user_settings->method === "email" && is_valid_twofactor_email_code($this->login_user, $twofactor_code)) {
            $authorized = 1;
        } else if ($user_settings->method === "sms" && is_valid_twofactor_sms_code($this->login_user, $twofactor_code)) {
            $authorized = 1;
        } else if ($user_settings->method === "google_authenticator" && is_valid_google_authenticator_code($user_settings->google_secret_key, $twofactor_code)) {
            $authorized = 1;
        }

        if ($authorized) {
            //save the cookie to this browser 
            twofactor_set_cookie("twofactor_cookie_of_user_" . $this->login_user->id, encode_id($this->login_user->id, "twofactor_cookie"));

            if ($user_settings->method === "email" || $user_settings->method === "sms") {
                //the code is valid, delete the code
                $options = array("code" => $twofactor_code);
                $verification_info = $this->TwoFactor_verification_model->get_details($options)->getRow();
                if ($verification_info->id) {
                    $this->TwoFactor_verification_model->delete_permanently($verification_info->id);
                }
            }

            echo json_encode(array("success" => true, "message" => app_lang("twofactor_code_success_message")));
        } else {
            echo json_encode(array("success" => false, 'message' => app_lang("twofactor_code_expaired_message")));
        }
    }

    function check_cookie_and_redirect_to_dashboard() {
        $redirect_to_dashboard = false;

        $user_settings = $this->get_user_settings();
        if (!$user_settings->enable_twofactor) {
            $redirect_to_dashboard = true;
        }


        helper('cookie');
        $cookie = get_cookie("twofactor_cookie_of_user_" . $this->login_user->id);
        if($cookie){
            $user_id = decode_id($cookie, "twofactor_cookie");
            if($user_id == $this->login_user->id){
                $redirect_to_dashboard = true;
            }
        }

        if($redirect_to_dashboard){
            try {
                app_hooks()->do_action('app_hook_after_signin');
            } catch (\Exception $ex) {
                log_message('error', '[ERROR] {exception}', ['exception' => $ex]);
            }

            app_redirect("dashboard/view");
        }
    }

}
