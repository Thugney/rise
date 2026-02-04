<?php

/**
 * get the defined config value by a key
 * @param string $key
 * @return config value
 */
if (!function_exists('get_twofactor_setting')) {

    function get_twofactor_setting($key = "") {
        $config = new TwoFactor\Config\TwoFactor();

        $setting_value = get_array_value($config->app_settings_array, $key);
        if ($setting_value !== NULL) {
            return $setting_value;
        } else {
            return "";
        }
    }

}

if (!function_exists('twofactor_set_cookie')) {

    function twofactor_set_cookie($name = "", $value = "") {
        if (!$name) {
            return false;
        }

        $secure = false;
        if (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') {
            $secure = true;
        }

        setcookie($name, $value, 0, '/', '', $secure);
    }

}

if (!function_exists('twofactor_send_email_otp')) {

    function twofactor_send_email_otp($login_user) {
        $code = rand(1000, 9999) . rand(10, 99);
        $verification_data = array(
            "code" => $code, //make 8 digit random code
            "params" => serialize(array(
                "email" => $login_user->email,
                "expire_time" => time() + (2 * 60 * 60) //make the code with 2hrs validity
            ))
        );

        $Email_templates_model = model("App\Models\Email_templates_model");
        $email_template = $Email_templates_model->get_final_template("twofactor_twofactor_authentication", true);
        
        $user_language = $login_user->language;

        //send code
        $parser_data["CODE"] = $code;
        $parser_data["FIRST_NAME"] = $login_user->first_name;
        $parser_data["LAST_NAME"] = $login_user->last_name;
        $parser_data["LOGIN_EMAIL"] = $login_user->email;
        $parser_data["APP_TITLE"] = get_setting("app_title");

        $Company_model = model('App\Models\Company_model');
        $company_info = $Company_model->get_one_where(array("is_default" => true, "deleted" => 0));
        $parser_data["COMPANY_NAME"] = $company_info->name;

        $parser_data["LOGO_URL"] = get_logo_url();

        $parser_data['SIGNATURE'] = get_array_value($email_template, "signature_$user_language") ? get_array_value($email_template, "signature_$user_language") : get_array_value($email_template, "signature_default");

        //send email
        $parser = \Config\Services::parser();
        $message = get_array_value($email_template, "message_$user_language") ? get_array_value($email_template, "message_$user_language") : get_array_value($email_template, "message_default");
        $subject = get_array_value($email_template, "subject_$user_language") ? get_array_value($email_template, "subject_$user_language") : get_array_value($email_template, "subject_default");
        
        $message = $parser->setData($parser_data)->renderString($message);
        $subject = $parser->setData($parser_data)->renderString($subject);
        $message = htmlspecialchars_decode($message);
        $subject = htmlspecialchars_decode($subject);
        
        if (send_app_mail($login_user->email, $subject, $message)) {
            return $verification_data;
        }

        return false;
    }

}

if (!function_exists('is_valid_twofactor_email_code')) {

    function is_valid_twofactor_email_code($login_user, $verification_code = "") {
        if (!$verification_code) {
            return false;
        }

        $options = array("code" => $verification_code);
        $TwoFactor_verification_model = new \TwoFactor\Models\TwoFactor_verification_model();
        $verification_info = $TwoFactor_verification_model->get_details($options)->getRow();

        if ($verification_info && $verification_info->id) {
            $invitation_info = unserialize($verification_info->params);

            $email = get_array_value($invitation_info, "email");
            $expire_time = get_array_value($invitation_info, "expire_time");

            if ($email && filter_var($email, FILTER_VALIDATE_EMAIL) && $email === $login_user->email && $expire_time && $expire_time > time()) {
                return true;
            }
        }
    }

}

if (!function_exists('is_valid_google_authenticator_code')) {

    function is_valid_google_authenticator_code($secret_key = "", $verification_code = "") {
        if (!($secret_key && $verification_code)) {
            return false;
        }

        require_once(PLUGINPATH . "TwoFactor/ThirdParty/antonioribeiro-google2fa/vendor/autoload.php");

        $google2fa = new PragmaRX\Google2FA\Google2FA();

        if ($google2fa->verifyKey($secret_key, $verification_code)) {
            return true;
        }
    }

}

if (!function_exists('is_valid_twofactor_sms_code')) {

    function is_valid_twofactor_sms_code($login_user, $verification_code = "") {
        if (!$verification_code) {
            return false;
        }

        $options = array("code" => $verification_code);
        $TwoFactor_verification_model = new \TwoFactor\Models\TwoFactor_verification_model();
        $verification_info = $TwoFactor_verification_model->get_details($options)->getRow();

        if ($verification_info && $verification_info->id) {
            $invitation_info = unserialize($verification_info->params);

            $phone = get_array_value($invitation_info, "phone");
            $expire_time = get_array_value($invitation_info, "expire_time");

            $Users_model = model('App\Models\Users_model');
            $user_info = $Users_model->get_one($login_user->id);

            if ($phone && $phone === $user_info->phone && $expire_time && $expire_time > time()) {
                return true;
            }
        }
    }

}

if (!function_exists('twofactor_send_sms_otp')) {

    function twofactor_send_sms_otp($login_user) {
        $Users_model = model('App\Models\Users_model');
        $user_info = $Users_model->get_one($login_user->id);

        if (!$user_info->phone) {
            return false;
        }

        $code = rand(1000, 9999) . rand(10, 99);
        $verification_data = array(
            "code" => $code, //make 8 digit random code
            "params" => serialize(array(
                "phone" => $user_info->phone,
                "expire_time" => time() + (2 * 60 * 60) //make the code with 2hrs validity
            ))
        );

        $sms_template = get_twofactor_setting("twofactor_sms_template");
        if (!$sms_template) {
            $sms_template = get_twofactor_setting("twofactor_default_sms_template");
        }

        //send code
        $parser_data["CODE"] = $code;
        $parser_data["FIRST_NAME"] = $login_user->first_name;
        $parser_data["LAST_NAME"] = $login_user->last_name;
        $parser_data["LOGIN_EMAIL"] = $login_user->email;
        $parser_data["APP_TITLE"] = get_setting("app_title");

        $Company_model = model('App\Models\Company_model');
        $company_info = $Company_model->get_one_where(array("is_default" => true, "deleted" => 0));
        $parser_data["COMPANY_NAME"] = $company_info->name;

        //send sms
        $parser = \Config\Services::parser();
        $message = $parser->setData($parser_data)->renderString($sms_template);

        if (twofactor_send_sms($message, $user_info->phone)) {
            return $verification_data;
        }

        return false;
    }

}

/**
 * send SMS
 * 
 * @param string $message
 * @param string $phone
 */
if (!function_exists('twofactor_send_sms')) {

    function twofactor_send_sms($message, $phone) {

        require_once(PLUGINPATH . "TwoFactor/ThirdParty/twilio/vendor/autoload.php");

        // Account SID, Auth Token and your twilio phone number
        $twilio_account_sid = get_twofactor_setting("twilio_account_sid");
        $twilio_auth_token = get_twofactor_setting("twilio_auth_token");
        $twilio_phone_number = get_twofactor_setting("twilio_phone_number");

        $client = new \Twilio\Rest\Client($twilio_account_sid, $twilio_auth_token);

        //Use the client to send text messages
        return $client->messages->create(
                        //the number you'd like to send the message to
                        $phone,
                        array(
                            // A Twilio phone number you purchased at twilio.com/console
                            'from' => $twilio_phone_number,
                            // the body of the text message you'd like to send
                            'body' => $message
                        )
        );
    }

}
