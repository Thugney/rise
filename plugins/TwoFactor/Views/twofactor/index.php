<!DOCTYPE html>
<html lang="en">
    <head>
        <?php echo view('includes/head'); ?>
        <link rel='stylesheet' type='text/css' href='<?php echo base_url(PLUGIN_URL_PATH . "TwoFactor/assets/css/twofactor_styles.css"); ?>' />
    </head>
    <body>
        <?php
        if (get_setting("show_background_image_in_signin_page") === "yes") {
            $background_url = get_file_from_setting("signin_page_background");
            ?>
            <style type="text/css">
                html, body {
                    background-image: url('<?php echo $background_url; ?>');
                    background-size:cover;
                }
            </style>
        <?php } ?>

        <?php if (get_setting("enable_footer")) { ?>
            <div class="scrollable-page">
            <?php } ?>

            <div class="form-signin">

                <div class="card mb15">
                    <div class="card-header text-center">
                        <?php if (get_setting("show_logo_in_signin_page") === "yes") { ?>
                            <img class="p20 mw100p" src="<?php echo get_logo_url(); ?>" />
                        <?php } else { ?>
                            <h2><?php echo app_lang('twofactor_twofactor_authentication'); ?></h2>
                        <?php } ?>
                    </div>
                    <div class="card-body p30 rounded-bottom">
                        <?php echo form_open("twofactor/authenticate", array("id" => "twofactor-authenticate-form", "class" => "general-form", "role" => "form")); ?>
                        <div class="form-group">
                            <label for="twofactor_code" class="mb10"><?php
                                $twofactor_code_message = app_lang('twofactor_code_message_email');
                                if ($user_settings->method === "sms") {
                                    $twofactor_code_message = app_lang('twofactor_code_message_sms');
                                } else if ($user_settings->method === "google_authenticator") {
                                    $twofactor_code_message = app_lang('twofactor_code_message_google_authenticator');
                                }

                                echo $twofactor_code_message;
                                ?></label>
                            <div class="">
                                <?php
                                echo form_input(array(
                                    "id" => "twofactor_code",
                                    "name" => "twofactor_code",
                                    "class" => "form-control p10 twofactor-auto-z-index",
                                    "data-rule-required" => true,
                                    "autocomplete" => "off",
                                    "placeholder" => app_lang("twofactor_code")
                                ));
                                ?>
                            </div>
                        </div>
                        <div class="form-group mb0">
                            <button class="w-100 btn btn-lg btn-primary btn-block" type="submit"><?php echo app_lang('twofactor_continue'); ?></button>
                        </div>
                        <?php echo form_close(); ?>

                        <div class="mt5"><?php echo app_lang("twofactor_not_you") . " " . anchor("signin/sign_out", app_lang("signin")); ?></div>
                    </div>
                </div>

                <script type="text/javascript">
                    "use strict";

                    $(document).ready(function () {
                        $("#twofactor_code").focus();

                        $("#twofactor-authenticate-form").appForm({
                            isModal: false,
                            onSubmit: function () {
                                appLoader.show();
                            },
                            onSuccess: function (result) {
                                appLoader.hide();
                                appAlert.success(result.message, {container: '.card-body', animate: false});
                                $("#twofactor-authenticate-form").remove();
                                window.location.href = "<?php echo get_uri("twofactor/check_cookie_and_redirect_to_dashboard"); ?>";
                            },
                            onError: function (result) {
                                appLoader.hide();
                                appAlert.error(result.message, {container: '.card-body', animate: false});
                                return false;
                            }
                        });
                    });
                </script>    

            </div>

            <?php if (get_setting("enable_footer")) { ?>
            </div>
        <?php } ?>

        <?php echo view("includes/footer"); ?>
    </body>
</html>