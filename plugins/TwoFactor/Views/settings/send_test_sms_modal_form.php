<?php echo form_open(get_uri("twofactor_settings/send_test_sms"), array("id" => "test-sms-form", "class" => "general-form", "role" => "form")); ?>
<div class="modal-body clearfix">
    <div class="container-fluid">
        <div class="form-group">
            <div class="row">
                <label for="phone" class=" col-md-3"><?php echo app_lang('phone'); ?></label>
                <div class=" col-md-9">
                    <?php
                    echo form_input(array(
                        "id" => "phone",
                        "name" => "phone",
                        "class" => "form-control mb-1",
                        "placeholder" => app_lang('phone'),
                        "data-rule-required" => true,
                        "data-msg-required" => app_lang("field_required")
                    ));
                    ?>
                    <span><i data-feather="alert-triangle" class="icon-16 text-danger"></i> <?php echo sprintf(app_lang('twofactor_twilio_phone_no_help_message'), anchor("https://www.twilio.com/docs/glossary/what-e164", "E.164", array("target" => "_blank"))); ?></span>
                </div>
            </div>
        </div>

        <div class="form-group">
            <div class="row">
                <label for="message" class="col-md-3"><?php echo app_lang('message'); ?></label>
                <div class=" col-md-9">
                    <?php
                    echo form_textarea(array(
                        "id" => "message",
                        "name" => "message",
                        "class" => "form-control",
                        "placeholder" => app_lang('message'),
                        "data-rich-text-editor" => true,
                        "data-rule-required" => true,
                        "data-msg-required" => app_lang("field_required")
                    ));
                    ?>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal-footer">
    <button type="button" class="btn btn-default" data-bs-dismiss="modal"><span data-feather="x" class="icon-16"></span> <?php echo app_lang('close'); ?></button>
    <button type="submit" class="btn btn-primary"><span data-feather="send" class="icon-16"></span> <?php echo app_lang('twofactor_send_test_sms'); ?></button>
</div>
<?php echo form_close(); ?>

<script type="text/javascript">
    "use strict";

    $(document).ready(function () {
        $("#test-sms-form").appForm({
            onSuccess: function (result) {
                appAlert.success(result.message, {duration: 10000});
            }
        });

        $('[data-bs-toggle="tooltip"]').tooltip();
    });
</script>