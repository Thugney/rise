<!DOCTYPE html>
<html lang="en">
    <head>
        <?php echo view('includes/head'); ?>
    </head>
    <body class="public-view signup-page">
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
        <div class="container" style="background-color: white; border-radius: 20px; padding: 20px; margin: 50px auto; box-shadow: 0 4px 8px rgba(0,0,0,0.1);">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <img src="assets/images/register.svg" alt="Welcome " style="width: 100%; height: auto;">
                </div>
                <div class="col-md-6">
                    <h3>Welcome to Eriteach CRM - Sign Up</h3>
                    <?php
                    $action_url = ($signup_type == "send_verify_email") ? "signup/send_verification_mail" : "signup/create_account";
                    echo form_open($action_url, array("id" => "signup-form", "class" => "general-form", "role" => "form"));
                    ?>

                    <?php if ($signup_type == "send_verify_email") { ?>
                        <div class="form-group">
                            <label for="email"><?php echo app_lang('input_your_email'); ?></label>
                            <?php
                            echo form_input(array(
                                "id" => "email",
                                "name" => "email",
                                "class" => "form-control",
                                "autofocus" => true,
                                "placeholder" => app_lang('email'),
                                "data-rule-email" => true,
                                "data-msg-email" => app_lang("enter_valid_email"),
                                "data-rule-required" => true,
                                "data-msg-required" => app_lang("field_required"),
                            ));
                            ?>
                        </div>
                    <?php } else { ?>
                        <div class="form-group">
                            <label for="first_name"><?php echo app_lang('first_name'); ?></label>
                            <?php
                            echo form_input(array(
                                "id" => "first_name",
                                "name" => "first_name",
                                "class" => "form-control",
                                "autofocus" => true,
                                "data-rule-required" => true,
                                "data-msg-required" => app_lang("field_required"),
                            ));
                            ?>
                        </div>

                        <input type="hidden" name="signup_key" value="<?php echo isset($signup_key) ? $signup_key : ''; ?>" />
                        <input type="hidden" name="role_id" value="<?php echo isset($role_id) ? $role_id : ''; ?>" />

                        <div class="form-group">
                            <label for="last_name"><?php echo app_lang('last_name'); ?></label>
                            <?php
                            echo form_input(array(
                                "id" => "last_name",
                                "name" => "last_name",
                                "class" => "form-control",
                                "data-rule-required" => true,
                                "data-msg-required" => app_lang("field_required"),
                            ));
                            ?>
                        </div>

                        <?php if ($signup_type === "new_client" || $signup_type === "verify_email") { ?>
                            <div class="form-group">
                                <label for="account_type"><?php echo app_lang('type'); ?></label>
                                <div>
                                    <?php
                                    echo form_radio(array(
                                        "id" => "type_organization",
                                        "name" => "account_type",
                                        "class" => "form-check-input account-type",
                                        "data-msg-required" => app_lang("field_required"),
                                    ), "organization", true);
                                    ?>
                                    <label for="type_organization"><?php echo app_lang('organization'); ?></label>
                                    <?php
                                    echo form_radio(array(
                                        "id" => "type_person",
                                        "name" => "account_type",
                                        "class" => "form-check-input account-type",
                                        "data-msg-required" => app_lang("field_required"),
                                    ), "person", false);
                                    ?>
                                    <label for="type_person"><?php echo app_lang('individual'); ?></label>
                                </div>
                            </div>

                            <div class="form-group company-name-section">
                                <label for="company_name"><?php echo app_lang('company_name'); ?></label>
                                <?php
                                echo form_input(array(
                                    "id" => "company_name",
                                    "name" => "company_name",
                                    "class" => "form-control",
                                ));
                                ?>
                            </div>
                        <?php } ?>

                        <?php if ($type === "staff") { ?>
                            <div class="form-group">
                                <label for="job_title"><?php echo app_lang('job_title'); ?></label>
                                <?php
                                echo form_input(array(
                                    "id" => "job_title",
                                    "name" => "job_title",
                                    "class" => "form-control"
                                ));
                                ?>
                            </div>
                        <?php } ?>
                        <?php if ($signup_type === "new_client") { ?>
                            <div class="form-group">
                                <label for="email"><?php echo app_lang('email'); ?></label>
                                <?php
                                echo form_input(array(
                                    "id" => "email",
                                    "name" => "email",
                                    "class" => "form-control",
                                    "autofocus" => true,
                                    "data-rule-email" => true,
                                    "data-msg-email" => app_lang("enter_valid_email"),
                                    "data-rule-required" => true,
                                    "data-msg-required" => app_lang("field_required"),
                                ));
                                ?>
                            </div>
                        <?php } else if ($signup_type === "verify_email" && isset($key)) { ?>
                            <input type="hidden" name="verify_email_key" value="<?php echo $key; ?>" />
                        <?php } ?>

                        <div class="form-group">
                            <label for="password"><?php echo app_lang('password'); ?></label>
                            <?php
                            echo form_password(array(
                                "id" => "password",
                                "name" => "password",
                                "class" => "form-control",
                                "data-rule-required" => true,
                                "data-msg-required" => app_lang("field_required"),
                                "data-rule-minlength" => 6,
                                "data-msg-minlength" => app_lang("enter_minimum_6_characters"),
                                "autocomplete" => "off",
                                "style" => "z-index:auto;"
                            ));
                            ?>
                        </div>
                        <div class="form-group">
                            <label for="retype_password"><?php echo app_lang('retype_password'); ?></label>
                            <?php
                            echo form_password(array(
                                "id" => "retype_password",
                                "name" => "retype_password",
                                "class" => "form-control",
                                "autocomplete" => "off",
                                "style" => "z-index:auto;",
                                "data-rule-equalTo" => "#password",
                                "data-msg-equalTo" => app_lang("enter_same_value")
                            ));
                            ?>
                        </div>

                        <?php if (get_setting("enable_gdpr") && get_setting("show_terms_and_conditions_in_client_signup_page") && get_setting("gdpr_terms_and_conditions_link")) { ?>
                            <div class="form-group">
                                <label for="i_accept_the_terms_and_conditions">
                                    <?php
                                    echo form_checkbox("i_accept_the_terms_and_conditions", "1", false, "id='i_accept_the_terms_and_conditions' class='form-check-input' data-rule-required='true' data-msg-required='" . app_lang("field_required") . "'");
                                    ?>
                                    <span><?php echo app_lang('i_accept_the_terms_and_conditions') . " " . anchor(get_setting("gdpr_terms_and_conditions_link"), app_lang("gdpr_terms_and_conditions") . ".", array("target" => "_blank")); ?> </span>
                                </label>
                            </div>
                        <?php } ?>

                        <div>
                            <?php echo view("signin/re_captcha"); ?>
                        </div>

                        <button class="btn btn-lg btn-primary w-100" type="submit"><?php echo $signup_type == "send_verify_email" ? app_lang("get_started") : app_lang('signup'); ?></button>
                    <?php echo form_close(); ?>
                    <?php app_hooks()->do_action('app_hook_signup_extension'); ?>

                    <p>Already have an account? <a href="<?php echo get_uri("signin"); ?>">Sign in</a></p>
                </div>
            </div>

            <script>
                $(document).ready(function () {
                    // Remove scrollbar init if not needed
                    $("#signup-form").appForm({
                        isModal: false,
                        onSubmit: function () {
                            appLoader.show();
                        },
                        onSuccess: function (result) {
                            appLoader.hide();
                            appAlert.success(result.message, {container: '.container', animate: false});
                            $("#signup-form").remove();
                        },
                        onError: function (result) {
                            appLoader.hide();
                            appAlert.error(result.message, {container: '.container', animate: false});
                            return false;
                        }
                    });

                    $('.account-type').click(function () {
                        var inputValue = $(this).attr("value");
                        if (inputValue === "person") {
                            $(".company-name-section").addClass("hide");
                        } else {
                            $(".company-name-section").removeClass("hide");
                        }
                    });
                });
            </script>
            <?php echo view("includes/footer"); ?>
        </body>
    </html>