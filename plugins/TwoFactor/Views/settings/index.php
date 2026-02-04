<div id="page-content" class="page-wrapper clearfix">
    <div class="row">
        <div class="col-sm-3 col-lg-2">
            <?php
            $tab_view['active_tab'] = "twofactor";
            echo view("settings/tabs", $tab_view);
            ?>
        </div>

        <div class="col-sm-9 col-lg-10">
            <?php echo form_open(get_uri("twofactor_settings/save"), array("id" => "twofactor-settings-form", "class" => "general-form dashed-row", "role" => "form")); ?>
            <div class="card">
                <div class=" card-header">
                    <h4><?php echo app_lang("twofactor_settings"); ?></h4>
                </div>
                <div class="card-body">
                    <div class="form-group">
                        <div class="row">
                            <label class="col-md-2 col-xs-8 col-sm-4"><?php echo app_lang('twofactor_enable_email_authentication_for_all'); ?> <span class="help" data-bs-toggle="tooltip" title="<?php echo app_lang('twofactor_enable_email_authentication_for_all_help_message'); ?>"><i data-feather='help-circle' class="icon-16"></i></span></label>
                            <div class="col-md-10 col-xs-4 col-sm-8">
                                <div>
                                    <?php
                                    echo form_checkbox("enable_email_authentication_for_all_team_members", "1", get_twofactor_setting("enable_email_authentication_for_all_team_members") ? true : false, "id='enable_email_authentication_for_all_team_members' class='form-check-input ml15'");
                                    ?>
                                    <label for="enable_email_authentication_for_all_team_members"><?php echo app_lang('team_members'); ?></label>
                                </div>
                                <div>
                                    <?php
                                    echo form_checkbox("enable_email_authentication_for_all_client_contacts", "1", get_twofactor_setting("enable_email_authentication_for_all_client_contacts") ? true : false, "id='enable_email_authentication_for_all_client_contacts' class='form-check-input ml15'");
                                    ?>
                                    <label for="enable_email_authentication_for_all_client_contacts"><?php echo app_lang('client_contacts'); ?></label>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="row">
                            <label for="enable_sms" class="col-md-2 col-xs-8 col-sm-4"><?php echo app_lang('twofactor_enable_sms'); ?></label>
                            <div class="col-md-10 col-xs-4 col-sm-8">
                                <?php
                                echo form_checkbox("enable_sms", "1", get_twofactor_setting("enable_sms") ? true : false, "id='enable_sms' class='form-check-input ml15'");
                                ?>
                            </div>
                        </div>
                    </div>

                    <div id="twilio-details-area" class="<?php echo get_twofactor_setting("enable_sms") ? "" : "hide" ?>">
                        <div class="form-group">
                            <div class="row">
                                <label for="" class=" col-md-12">
                                    <?php echo app_lang("get_your_app_credentials_from_here") . " " . anchor("https://www.twilio.com", "Twilio", array("target" => "_blank")); ?>
                                </label>
                            </div>
                        </div>

                        <div class="form-group">
                            <div class="row">
                                <label for="twilio_account_sid" class=" col-md-2"><?php echo app_lang('twofactor_twilio_account_sid'); ?></label>
                                <div class=" col-md-10">
                                    <?php
                                    echo form_input(array(
                                        "id" => "twilio_account_sid",
                                        "name" => "twilio_account_sid",
                                        "value" => get_twofactor_setting("twilio_account_sid"),
                                        "class" => "form-control",
                                        "placeholder" => app_lang('twofactor_twilio_account_sid'),
                                        "data-rule-required" => true,
                                        "data-msg-required" => app_lang("field_required")
                                    ));
                                    ?>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <div class="row">
                                <label for="twilio_auth_token" class=" col-md-2"><?php echo app_lang('twofactor_twilio_auth_token'); ?></label>
                                <div class=" col-md-10">
                                    <?php
                                    echo form_input(array(
                                        "id" => "twilio_auth_token",
                                        "name" => "twilio_auth_token",
                                        "value" => get_twofactor_setting("twilio_auth_token"),
                                        "class" => "form-control",
                                        "placeholder" => app_lang('twofactor_twilio_auth_token'),
                                        "data-rule-required" => true,
                                        "data-msg-required" => app_lang("field_required")
                                    ));
                                    ?>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <div class="row">
                                <label for="twilio_phone_number" class=" col-md-2"><?php echo app_lang('twofactor_twilio_phone_number'); ?></label>
                                <div class=" col-md-10">
                                    <?php
                                    echo form_input(array(
                                        "id" => "twilio_phone_number",
                                        "name" => "twilio_phone_number",
                                        "value" => get_twofactor_setting("twilio_phone_number"),
                                        "class" => "form-control",
                                        "placeholder" => app_lang('twofactor_twilio_phone_number'),
                                        "data-rule-required" => true,
                                        "data-msg-required" => app_lang("field_required")
                                    ));
                                    ?>
                                    <div class="mt10">
                                        <i data-feather="alert-triangle" class="icon-16 text-danger"></i>
                                        <span><?php echo sprintf(app_lang("twofactor_twilio_user_phone_no_help_message"), anchor("https://www.twilio.com/docs/glossary/what-e164", "E.164", array("target" => "_blank")), anchor("https://support.twilio.com/hc/en-us/articles/223183008-Formatting-International-Phone-Numbers", strtolower(app_lang('twofactor_here')), array("target" => "_blank"))); ?></span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <div class="row">
                                <label for="twofactor_sms_template" class=" col-md-2"><?php echo "SMS " . strtolower(app_lang('template')); ?></label>
                                <div class=" col-md-10">
                                    <?php
                                    echo form_textarea(array(
                                        "id" => "twofactor_sms_template",
                                        "name" => "twofactor_sms_template",
                                        "value" => get_twofactor_setting("twofactor_sms_template") ? get_twofactor_setting("twofactor_sms_template") : get_twofactor_setting("twofactor_default_sms_template"),
                                        "class" => "form-control",
                                        "placeholder" => app_lang('template'),
                                        "data-rule-required" => true,
                                        "data-msg-required" => app_lang("field_required")
                                    ));
                                    ?>
                                    <div class="mt10"><strong><?php echo app_lang("avilable_variables"); ?>: </strong>
                                        <?php
                                        $variables = array("FIRST_NAME", "LAST_NAME", "LOGIN_EMAIL", "CODE", "APP_TITLE", "COMPANY_NAME", "SITE_URL");
                                        foreach ($variables as $variable) {
                                            echo "{" . $variable . "}, ";
                                        }
                                        ?>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
                <div class="card-footer">
                    <button type="submit" class="btn btn-primary"><span data-feather="check-circle" class="icon-16"></span> <?php echo app_lang('save'); ?></button>

                    <?php
                    if (get_twofactor_setting("enable_sms") && get_twofactor_setting("twilio_account_sid") && get_twofactor_setting("twilio_auth_token") && get_twofactor_setting("twilio_phone_number")) {
                        echo modal_anchor(get_uri("twofactor_settings/send_test_sms_modal_form"), "<i data-feather='message-square' class='icon-16'></i> " . app_lang('twofactor_send_test_sms'), array("id" => "send-test-sms-btn", "class" => "btn btn-info text-white ml5", "title" => app_lang('twofactor_send_test_sms')));
                        ?>

                        <button id="restore_to_default" data-bs-toggle="popover" data-placement="top" type="button" class="btn btn-danger ml5"><span data-feather="refresh-cw" class="icon-16"></span> <?php echo app_lang('twofactor_restore_template_to_default'); ?></button>

                    <?php } ?>

                </div>
                <?php echo form_close(); ?>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
    "use strict";

    $(document).ready(function () {
        $("#twofactor-settings-form").appForm({
            isModal: false,
            onSuccess: function (result) {
                if (result.success) {
                    if ($("#enable_sms").is(":checked")) {
                        location.reload();
                    } else {
                        appAlert.success(result.message, {duration: 10000});
                    }
                }
            }
        });

        //show/hide twilio SMS integration details area
        $("#enable_sms").on("click", function () {
            $("#send-test-sms-btn").addClass("hide");
            $("#restore_to_default").addClass("hide");
            if ($(this).is(":checked")) {
                $("#twilio-details-area").removeClass("hide");
            } else {
                $("#twilio-details-area").addClass("hide");
            }
        });


        $('#restore_to_default').on("click", function () {
            $(this).appConfirmation({
                title: "<?php echo app_lang('are_you_sure'); ?>",
                btnConfirmLabel: "<?php echo app_lang('yes'); ?>",
                btnCancelLabel: "<?php echo app_lang('no'); ?>",
                onConfirm: function () {
                    $.ajax({
                        url: "<?php echo get_uri('twofactor_settings/restore_template_to_default') ?>",
                        dataType: 'json',
                        success: function (result) {
                            if (result.success) {
                                appAlert.success(result.message, {duration: 10000});
                                location.reload();
                            } else {
                                appAlert.error(result.message);
                            }
                        }
                    });

                }
            });

            return false;
        });

        $('[data-bs-toggle="tooltip"]').tooltip();
    });
</script>