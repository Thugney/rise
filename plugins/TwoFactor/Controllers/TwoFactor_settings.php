<?php

namespace TwoFactor\Controllers;

use App\Controllers\Security_Controller;
use PragmaRX\Google2FA\Google2FA;
use chillerlan\QRCode\QRCode;

class TwoFactor_settings extends Security_Controller
{

    protected $TwoFactor_settings_model;
    protected $TwoFactor_user_settings_model;
    protected $TwoFactor_verification_model;

    function __construct()
    {
        parent::__construct();

        $this->TwoFactor_settings_model = new \TwoFactor\Models\TwoFactor_settings_model();
        $this->TwoFactor_user_settings_model = new \TwoFactor\Models\TwoFactor_user_settings_model();
        $this->TwoFactor_verification_model = new \TwoFactor\Models\TwoFactor_verification_model();

        require_once(PLUGINPATH . "TwoFactor/ThirdParty/antonioribeiro-google2fa/vendor/autoload.php");
        require_once(PLUGINPATH . "TwoFactor/ThirdParty/chillerlan-php-qrcode/vendor/autoload.php");
    }

    function index()
    {
        $this->access_only_admin_or_settings_admin();
        return $this->template->rander("TwoFactor\Views\settings\index");
    }

    function save()
    {
        $this->access_only_admin_or_settings_admin();
        $settings = array("enable_sms", "twilio_account_sid", "twilio_auth_token", "twilio_phone_number", "twofactor_sms_template", "enable_email_authentication_for_all_team_members", "enable_email_authentication_for_all_client_contacts");

        foreach ($settings as $setting) {
            $value = $this->request->getPost($setting);
            if (is_null($value)) {
                $value = "";
            }

            $this->TwoFactor_settings_model->save_setting($setting, $value);
        }
        echo json_encode(array("success" => true, 'message' => app_lang('settings_updated')));
    }

    //load send test sms modal
    function send_test_sms_modal_form()
    {
        $this->access_only_admin_or_settings_admin();
        return $this->template->view('TwoFactor\Views\settings\send_test_sms_modal_form');
    }

    //send a test sms 
    function send_test_sms()
    {
        $this->access_only_admin_or_settings_admin();
        $this->validate_submitted_data(array(
            "phone" => "required",
            "message" => "required"
        ));

        $phone = $this->request->getPost('phone');
        $message = $this->request->getPost('message');

        if (twofactor_send_sms($message, $phone)) {
            echo json_encode(array("success" => true, 'message' => app_lang('twofactor_send_test_sms_successfull_message')));
        } else {
            echo json_encode(array("success" => false, 'message' => app_lang('twofactor_send_test_sms_error_message')));
        }
    }

    //restore sms template to default
    function restore_template_to_default()
    {
        $this->access_only_admin_or_settings_admin();
        $this->TwoFactor_settings_model->save_setting("twofactor_sms_template", "");
        echo json_encode(array("success" => true, 'message' => app_lang('template_restored')));
    }

    function user_settings()
    {
        $view_data["model_info"] = $this->TwoFactor_user_settings_model->get_one_where(array("user_id" => $this->login_user->id, "deleted" => 0));
        $view_data["login_user"] = $this->Users_model->get_one($this->login_user->id);

        //prepare QR code
        $google2fa = new Google2FA();
        $secretKey = $view_data["model_info"]->google_secret_key ? $view_data["model_info"]->google_secret_key : $google2fa->generateSecretKey();
        $view_data["model_info"]->google_secret_key = $secretKey;

        $google2fa_name = get_setting('app_title') . " (" . $this->login_user->email . ")";
        $qrCodeUrl = $google2fa->getQRCodeUrl($google2fa_name, "", $secretKey);

        $qr = new QRCode;
        $view_data["google2fa_qr_code_image_data"] = $qr->render($qrCodeUrl);

        return $this->template->view("TwoFactor\Views\settings\user_settings", $view_data);
    }

    function save_user_settings()
    {
        $method = $this->request->getPost("twofactor_method");
        $enable_twofactor = $this->request->getPost("enable_twofactor");
        $twofactor_code = $this->request->getPost("twofactor_code");
        $google_secret_key = $this->request->getPost("google_secret_key");

        $data = array(
            "user_id" => $this->login_user->id,
            "enable_twofactor" => $enable_twofactor ? $enable_twofactor : "",
            "google_secret_key" => $google_secret_key,
            "method" => $method,
        );

        if ($method === "email" && is_valid_twofactor_email_code($this->login_user, $twofactor_code)) {
            $authorized = 1;
        } else if ($method === "sms" && is_valid_twofactor_sms_code($this->login_user, $twofactor_code)) {
            $authorized = 1;
        } else if ($method === "google_authenticator" && is_valid_google_authenticator_code($google_secret_key, $twofactor_code)) {
            $authorized = 1;
        } else {
            $authorized = 0;
        }

        if ($authorized) {
            //save the cookie to this browser 
            twofactor_set_cookie("twofactor_cookie_of_user_" . $this->login_user->id, encode_id($this->login_user->id, "twofactor_cookie"));

            if ($method === "email" || $method === "sms") {
                //the code is valid, delete the code
                $options = array("code" => $twofactor_code);
                $verification_info = $this->TwoFactor_verification_model->get_details($options)->getRow();
                if ($verification_info->id) {
                    $this->TwoFactor_verification_model->delete_permanently($verification_info->id);
                }
            }
        }

        $data["authorized"] = $authorized;

        $existing_info = $this->TwoFactor_user_settings_model->get_one_where(array("user_id" => $this->login_user->id, "deleted" => 0));
        $this->TwoFactor_user_settings_model->ci_save($data, $existing_info->id);

        if (!$enable_twofactor || $authorized) {
            echo json_encode(array("success" => true, 'message' => app_lang('settings_updated')));
        } else {
            echo json_encode(array("success" => false, 'message' => app_lang("twofactor_code_expaired_message")));
        }
    }

    function send_email_otp()
    {
        //check if there has any valid code for this user
        //if exists, don't send a new one
        $options = array("email" => $this->login_user->email);
        $verifications = $this->TwoFactor_verification_model->get_details($options)->getResult();
        foreach ($verifications as $verification) {
            if (is_valid_twofactor_email_code($this->login_user, $verification->code)) {
                echo json_encode(array("success" => true, "message" => app_lang('twofactor_code_message_email')));
                exit();
            }
        }

        $verification_data = twofactor_send_email_otp($this->login_user);
        if ($verification_data) {
            //save verification code after sending email
            $save_id = $this->TwoFactor_verification_model->ci_save($verification_data);
            if (!$save_id) {
                echo json_encode(array("success" => false, 'message' => app_lang('error_occurred')));
            }

            //show two-factor form
            echo json_encode(array("success" => true, "message" => app_lang('twofactor_code_message_email')));
        } else {
            echo json_encode(array("success" => false, 'message' => app_lang('error_occurred')));
        }
    }

    function send_sms_otp()
    {
        //check if there has any valid code for this user
        //if exists, don't send a new one
        $user_info = $this->Users_model->get_one($this->login_user->id);
        $options = array("phone" => $user_info->phone);
        $verifications = $this->TwoFactor_verification_model->get_details($options)->getResult();
        foreach ($verifications as $verification) {
            if (is_valid_twofactor_sms_code($this->login_user, $verification->code)) {
                echo json_encode(array("success" => true, "message" => app_lang('twofactor_code_message_sms')));
                exit();
            }
        }

        $verification_data = twofactor_send_sms_otp($this->login_user);
        if ($verification_data) {
            //save verification code after sending sms
            $save_id = $this->TwoFactor_verification_model->ci_save($verification_data);
            if (!$save_id) {
                echo json_encode(array("success" => false, 'message' => app_lang('error_occurred')));
            }

            //show two-factor form
            echo json_encode(array("success" => true, "message" => app_lang('twofactor_code_message_sms')));
        } else {
            echo json_encode(array("success" => false, 'message' => app_lang('error_occurred')));
        }
    }
}
