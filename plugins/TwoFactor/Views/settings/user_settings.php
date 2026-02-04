<div class="tab-content">
    <?php
    $user_id = $login_user->id;
    echo form_open(get_uri("twofactor_settings/save_user_settings/"), array("id" => "twofactor-setting-form", "class" => "general-form dashed-row white", "role" => "form"));
    ?>
    <div class="card">
        <div class=" card-header">
            <h4> <?php echo app_lang('twofactor_twofactor_authentication'); ?></h4>
        </div>
        <div class="card-body">

            <div class="form-group">
                <div class="row">
                    <label for="enable_twofactor" class=" col-md-2"><?php echo app_lang('twofactor_enable_twofactor_authentication'); ?></label>
                    <div class=" col-md-10">
                        <?php
                        echo form_checkbox("enable_twofactor", "1", $model_info->enable_twofactor ? true : false, "id='enable_twofactor' class='form-check-input mt-2'");
                        ?>
                    </div>
                </div>
            </div>

            <div id="twofactor-details-area" class="<?php echo $model_info->enable_twofactor ? "" : "hide" ?>">

                <div class="form-group">
                    <div class="row">
                        <label for="twofactor_method" class=" col-md-2"><?php echo app_lang('twofactor_method'); ?></label>
                        <div class=" col-md-10">
                            <?php
                            $twofactor_methods = array(
                                "email" => app_lang('email')
                            );

                            if ($login_user->phone && get_twofactor_setting("enable_sms") && get_twofactor_setting("twilio_account_sid") && get_twofactor_setting("twilio_auth_token") && get_twofactor_setting("twilio_phone_number")) {
                                $twofactor_methods["sms"] = "SMS";
                            }

                            $twofactor_methods["google_authenticator"] = "Google Authenticator";

                            echo form_dropdown(
                                    "twofactor_method", $twofactor_methods, $model_info->method, "class='select2 mini' id='twofactor-method'"
                            );
                            ?>
                        </div>
                    </div>
                </div>

                <div id="twofactor-email-details-area" class="form-group <?php echo ($model_info->method === "email" || !$model_info->method) ? "" : "hide"; ?>">
                    <div class="row">
                        <label for="twofactor_email" class=" col-md-2"><?php echo app_lang('email'); ?></label>
                        <div class=" col-md-10">
                            <div><?php echo $login_user->email; ?></div>
                            <button type="button" id="twofactor-send-email-otp-button" class="btn btn-default mt15 spinning-btn"><span data-feather="send" class="icon-16"></span> <?php echo app_lang('twofactor_send_otp'); ?></button>
                        </div>
                    </div>
                </div>

                <div id="twofactor-google-authenticator-details-area" class="form-group <?php echo $model_info->method === "google_authenticator" ? "" : "hide"; ?>">
                    <input type="hidden" name="google_secret_key" value="<?php echo $model_info->google_secret_key; ?>" />
                    <div class="row">
                        <label for="twofactor_google_authenticator_qr_code" class=" col-md-2"><?php echo app_lang('twofactor_qr_code'); ?></label>
                        <div class=" col-md-10">
                            <img class="b-a" height="300" width="300" alt="twofactor-google-authenticator" src="<?php echo $google2fa_qr_code_image_data; ?>" />
                            <div class="mt10"><i data-feather="alert-circle" class="icon-16"></i> <?php echo app_lang("twofactor_google_authenticator_help_message"); ?></div>
                        </div>
                    </div>
                </div>

                <div id="twofactor-sms-details-area" class="form-group <?php echo $model_info->method === "sms" ? "" : "hide"; ?>">
                    <div class="row">
                        <label for="twofactor_sms" class=" col-md-2">SMS</label>
                        <div class=" col-md-10">
                            <div><?php echo $login_user->phone; ?></div>
                            <button type="button" id="twofactor-send-sms-otp-button" class="btn btn-default mt15 spinning-btn"><span data-feather="send" class="icon-16"></span> <?php echo app_lang('twofactor_send_otp'); ?></button>
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <div class="row">
                        <label for="twofactor_code" class=" col-md-2">OTP</label>
                        <div class=" col-md-10">
                            <?php
                            echo form_input(array(
                                "id" => "twofactor_code",
                                "name" => "twofactor_code",
                                "value" => "",
                                "class" => "form-control",
                                "placeholder" => "OTP",
                                "autocomplete" => "off",
                                "data-rule-required" => true,
                                "data-msg-required" => app_lang("field_required"),
                            ));
                            ?>
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <div class="row">
                        <label for="status" class=" col-md-2"><?php echo app_lang('status'); ?></label>
                        <div class=" col-md-10">
                            <?php if ($model_info->authorized) { ?>
                                <span class="ml5 badge bg-success"><?php echo app_lang("authorized"); ?></span>
                            <?php } else { ?>
                                <span class="ml5 badge" style="background:#F9A52D;"><?php echo app_lang("unauthorized"); ?></span>
                            <?php } ?>
                        </div>
                    </div>
                </div>

            </div>

        </div>
        <div class="card-footer rounded-0">
            <button type="submit" class="btn btn-primary"><span data-feather="check-circle" class="icon-16"></span> <?php echo app_lang('save'); ?></button>
        </div>
    </div>
    <?php echo form_close(); ?>
</div>

<script type="text/javascript">
    "use strict";

    $(document).ready(function () {
        var $twoFactorEmailDetailsArea = $("#twofactor-email-details-area"),
                $enableTwofactorAuthentication = $("#enable_twofactor"),
                $twofactorDetailsArea = $("#twofactor-details-area"),
                $twofactorSendEmailOtpButton = $("#twofactor-send-email-otp-button"),
                $twofactorSendSmsOtpButton = $("#twofactor-send-sms-otp-button"),
                $twoFactorGoogleAuthenticatorDetailsArea = $("#twofactor-google-authenticator-details-area"),
                $twoFactorSmsDetailsArea = $("#twofactor-sms-details-area");

        $("#twofactor-setting-form").appForm({
            isModal: false,
            onSuccess: function (result) {
                appAlert.success(result.message, {duration: 10000});

                if ($enableTwofactorAuthentication.is(":checked")) {
                    location.reload();
                }
            }
        });

        $enableTwofactorAuthentication.click(function () {
            if ($(this).is(":checked")) {
                $twofactorDetailsArea.removeClass("hide");
            } else {
                $twofactorDetailsArea.addClass("hide");
            }
        });

        $("#twofactor-method").select2().on("change", function () {
            var value = $(this).val();
            if (value === "email") {
                $twoFactorEmailDetailsArea.removeClass("hide");
                $twoFactorGoogleAuthenticatorDetailsArea.addClass("hide");
                $twoFactorSmsDetailsArea.addClass("hide");
            } else if (value === "google_authenticator") {
                $twoFactorGoogleAuthenticatorDetailsArea.removeClass("hide");
                $twoFactorEmailDetailsArea.addClass("hide");
                $twoFactorSmsDetailsArea.addClass("hide");
            } else if (value === "sms") {
                $twoFactorSmsDetailsArea.removeClass("hide");
                $twoFactorEmailDetailsArea.addClass("hide");
                $twoFactorGoogleAuthenticatorDetailsArea.addClass("hide");
            }
        });

        $twofactorSendEmailOtpButton.on("click", function () {
            $twofactorSendEmailOtpButton.addClass("spinning");

            $.ajax({
                url: "<?php echo get_uri("twofactor_settings/send_email_otp"); ?>",
                dataType: "json",
                success: function (result) {

                    if (result.success) {
                        $twofactorSendEmailOtpButton.removeClass("spinning");
                        appAlert.success(result.message, {duration: 10000});
                    } else {
                        appAlert.error(result.message);
                    }
                }
            });
        });

        $twofactorSendSmsOtpButton.on("click", function () {
            $twofactorSendSmsOtpButton.addClass("spinning");

            $.ajax({
                url: "<?php echo get_uri("twofactor_settings/send_sms_otp"); ?>",
                dataType: "json",
                success: function (result) {

                    if (result.success) {
                        $twofactorSendSmsOtpButton.removeClass("spinning");
                        appAlert.success(result.message, {duration: 10000});
                    } else {
                        appAlert.error(result.message);
                    }
                }
            });
        });
    });
</script>    