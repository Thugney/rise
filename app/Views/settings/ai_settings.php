<div id="page-content" class="page-wrapper clearfix">
    <div class="row">
        <div class="col-sm-3 col-lg-2">
            <?php
            $tab_view['active_tab'] = "ai_settings";
            echo view("settings/tabs", $tab_view);
            ?>
        </div>

        <div class="col-sm-9 col-lg-10">
            <?php echo form_open(get_uri("settings/save_ai_settings"), array("id" => "ai-settings-form", "class" => "general-form dashed-row", "role" => "form")); ?>
            <?php echo csrf_field(); ?>

            <!-- AI Assistant Configuration -->
            <div class="card mb15">
                <div class="card-header">
                    <h4>AI Assistant Configuration</h4>
                </div>
                <div class="card-body">

                    <!-- Enable AI -->
                    <div class="form-group">
                        <div class="row">
                            <label class="col-md-3">Enable AI Assistant</label>
                            <div class="col-md-9">
                                <?php
                                echo form_checkbox(
                                    "ai_enabled",
                                    "1",
                                    get_array_value($ai_settings, 'ai_enabled') === '1',
                                    "id='ai_enabled' class='form-check-input'"
                                );
                                ?>
                                <span class="ml10 text-muted">Allow users with active subscription to use AI Assistant</span>
                            </div>
                        </div>
                    </div>

                    <!-- AI Provider -->
                    <div class="form-group">
                        <div class="row">
                            <label for="ai_provider" class="col-md-3">AI Provider</label>
                            <div class="col-md-9">
                                <?php
                                echo form_dropdown(
                                    "ai_provider",
                                    array("deepseek" => "DeepSeek"),
                                    get_array_value($ai_settings, 'ai_provider', 'deepseek'),
                                    "class='select2 mini' id='ai_provider'"
                                );
                                ?>
                            </div>
                        </div>
                    </div>

                    <!-- API Key -->
                    <div class="form-group">
                        <div class="row">
                            <label for="ai_api_key" class="col-md-3">API Key</label>
                            <div class="col-md-9">
                                <?php
                                $api_key = get_array_value($ai_settings, 'ai_api_key');
                                echo form_password(array(
                                    "id" => "ai_api_key",
                                    "name" => "ai_api_key",
                                    "value" => $api_key ? "******" : "",
                                    "class" => "form-control",
                                    "placeholder" => "Enter your DeepSeek API key",
                                    "autocomplete" => "new-password"
                                ));
                                ?>
                                <small class="text-muted">Get your API key from <a href="https://platform.deepseek.com/" target="_blank">DeepSeek Platform</a></small>
                            </div>
                        </div>
                    </div>

                    <!-- Model -->
                    <div class="form-group">
                        <div class="row">
                            <label for="ai_model" class="col-md-3">Model</label>
                            <div class="col-md-9">
                                <?php
                                echo form_dropdown(
                                    "ai_model",
                                    array(
                                        "deepseek-chat" => "DeepSeek Chat (Recommended)",
                                        "deepseek-coder" => "DeepSeek Coder"
                                    ),
                                    get_array_value($ai_settings, 'ai_model', 'deepseek-chat'),
                                    "class='select2 mini' id='ai_model'"
                                );
                                ?>
                            </div>
                        </div>
                    </div>

                    <!-- API Endpoint -->
                    <div class="form-group">
                        <div class="row">
                            <label for="ai_api_endpoint" class="col-md-3">API Endpoint</label>
                            <div class="col-md-9">
                                <?php
                                echo form_input(array(
                                    "id" => "ai_api_endpoint",
                                    "name" => "ai_api_endpoint",
                                    "value" => get_array_value($ai_settings, 'ai_api_endpoint', 'https://api.deepseek.com/chat/completions'),
                                    "class" => "form-control",
                                    "placeholder" => "https://api.deepseek.com/chat/completions"
                                ));
                                ?>
                            </div>
                        </div>
                    </div>

                    <!-- Max Tokens -->
                    <div class="form-group">
                        <div class="row">
                            <label for="ai_max_tokens" class="col-md-3">Max Response Tokens</label>
                            <div class="col-md-9">
                                <?php
                                echo form_input(array(
                                    "id" => "ai_max_tokens",
                                    "name" => "ai_max_tokens",
                                    "value" => get_array_value($ai_settings, 'ai_max_tokens', '4096'),
                                    "class" => "form-control",
                                    "type" => "number",
                                    "min" => "100",
                                    "max" => "32000"
                                ));
                                ?>
                                <small class="text-muted">Maximum tokens in AI response (recommended: 4096)</small>
                            </div>
                        </div>
                    </div>

                    <!-- Temperature -->
                    <div class="form-group">
                        <div class="row">
                            <label for="ai_temperature" class="col-md-3">Temperature</label>
                            <div class="col-md-9">
                                <?php
                                echo form_input(array(
                                    "id" => "ai_temperature",
                                    "name" => "ai_temperature",
                                    "value" => get_array_value($ai_settings, 'ai_temperature', '0.7'),
                                    "class" => "form-control",
                                    "type" => "number",
                                    "min" => "0",
                                    "max" => "2",
                                    "step" => "0.1"
                                ));
                                ?>
                                <small class="text-muted">Controls randomness (0 = deterministic, 1 = creative)</small>
                            </div>
                        </div>
                    </div>

                    <!-- Rate Limit -->
                    <div class="form-group">
                        <div class="row">
                            <label for="ai_rate_limit_per_hour" class="col-md-3">Rate Limit (per hour)</label>
                            <div class="col-md-9">
                                <?php
                                echo form_input(array(
                                    "id" => "ai_rate_limit_per_hour",
                                    "name" => "ai_rate_limit_per_hour",
                                    "value" => get_array_value($ai_settings, 'ai_rate_limit_per_hour', '60'),
                                    "class" => "form-control",
                                    "type" => "number",
                                    "min" => "1",
                                    "max" => "1000"
                                ));
                                ?>
                                <small class="text-muted">Maximum queries per user per hour</small>
                            </div>
                        </div>
                    </div>

                    <!-- Test Connection Button -->
                    <div class="form-group">
                        <div class="row">
                            <label class="col-md-3"></label>
                            <div class="col-md-9">
                                <button type="button" id="test-ai-connection" class="btn btn-secondary">
                                    <span data-feather="zap" class="icon-16"></span> Test AI Connection
                                </button>
                                <span id="ai-connection-result" class="ml10"></span>
                            </div>
                        </div>
                    </div>

                </div>
            </div>

            <!-- Polar.sh Subscription Settings -->
            <div class="card mb15">
                <div class="card-header">
                    <h4>Polar.sh Subscription Settings</h4>
                </div>
                <div class="card-body">

                    <!-- Enable Polar -->
                    <div class="form-group">
                        <div class="row">
                            <label class="col-md-3">Require Subscription</label>
                            <div class="col-md-9">
                                <?php
                                echo form_checkbox(
                                    "polar_enabled",
                                    "1",
                                    get_array_value($ai_settings, 'polar_enabled') === '1',
                                    "id='polar_enabled' class='form-check-input'"
                                );
                                ?>
                                <span class="ml10 text-muted">Users must have active Polar.sh subscription to access AI</span>
                            </div>
                        </div>
                    </div>

                    <!-- Polar Access Token -->
                    <div class="form-group">
                        <div class="row">
                            <label for="polar_access_token" class="col-md-3">API Access Token</label>
                            <div class="col-md-9">
                                <?php
                                $polar_token = get_array_value($ai_settings, 'polar_access_token');
                                echo form_password(array(
                                    "id" => "polar_access_token",
                                    "name" => "polar_access_token",
                                    "value" => $polar_token ? "******" : "",
                                    "class" => "form-control",
                                    "placeholder" => "Enter your Polar.sh access token",
                                    "autocomplete" => "new-password"
                                ));
                                ?>
                                <small class="text-muted">Get from <a href="https://polar.sh/settings" target="_blank">Polar.sh Settings</a> > Access Tokens</small>
                            </div>
                        </div>
                    </div>

                    <!-- Webhook Secret -->
                    <div class="form-group">
                        <div class="row">
                            <label for="polar_webhook_secret" class="col-md-3">Webhook Secret</label>
                            <div class="col-md-9">
                                <?php
                                $webhook_secret = get_array_value($ai_settings, 'polar_webhook_secret');
                                echo form_password(array(
                                    "id" => "polar_webhook_secret",
                                    "name" => "polar_webhook_secret",
                                    "value" => $webhook_secret ? "******" : "",
                                    "class" => "form-control",
                                    "placeholder" => "Enter webhook secret",
                                    "autocomplete" => "new-password"
                                ));
                                ?>
                            </div>
                        </div>
                    </div>

                    <!-- Product ID -->
                    <div class="form-group">
                        <div class="row">
                            <label for="polar_product_id" class="col-md-3">Product ID</label>
                            <div class="col-md-9">
                                <?php
                                echo form_input(array(
                                    "id" => "polar_product_id",
                                    "name" => "polar_product_id",
                                    "value" => get_array_value($ai_settings, 'polar_product_id', ''),
                                    "class" => "form-control",
                                    "placeholder" => "Your AI Assistant product ID from Polar"
                                ));
                                ?>
                            </div>
                        </div>
                    </div>

                    <!-- Organization ID -->
                    <div class="form-group">
                        <div class="row">
                            <label for="polar_organization_id" class="col-md-3">Organization ID</label>
                            <div class="col-md-9">
                                <?php
                                echo form_input(array(
                                    "id" => "polar_organization_id",
                                    "name" => "polar_organization_id",
                                    "value" => get_array_value($ai_settings, 'polar_organization_id', ''),
                                    "class" => "form-control",
                                    "placeholder" => "Your Polar organization ID"
                                ));
                                ?>
                            </div>
                        </div>
                    </div>

                    <!-- Webhook URL Info -->
                    <div class="form-group">
                        <div class="row">
                            <label class="col-md-3">Webhook URL</label>
                            <div class="col-md-9">
                                <div class="input-group">
                                    <input type="text" class="form-control" id="webhook-url" value="<?php echo get_uri('polar_webhook'); ?>" readonly>
                                    <button type="button" class="btn btn-default" onclick="copyWebhookUrl()">
                                        <span data-feather="copy" class="icon-16"></span>
                                    </button>
                                </div>
                                <small class="text-muted">Add this URL to your Polar.sh webhook settings</small>
                            </div>
                        </div>
                    </div>

                    <!-- Test Polar Button -->
                    <div class="form-group">
                        <div class="row">
                            <label class="col-md-3"></label>
                            <div class="col-md-9">
                                <button type="button" id="test-polar-connection" class="btn btn-secondary">
                                    <span data-feather="link" class="icon-16"></span> Test Polar Configuration
                                </button>
                                <span id="polar-connection-result" class="ml10"></span>
                            </div>
                        </div>
                    </div>

                </div>
            </div>

            <!-- Subscription Statistics -->
            <div class="card mb15">
                <div class="card-header">
                    <h4>Subscription Statistics</h4>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="widget-icon-box bg-white border">
                                <div class="widget-icon bg-primary">
                                    <span data-feather="users" class="icon"></span>
                                </div>
                                <div class="widget-details">
                                    <h1><?php echo $total_subscriptions ?? 0; ?></h1>
                                    <span class="bg-transparent-white">Total Subscriptions</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="widget-icon-box bg-white border">
                                <div class="widget-icon bg-success">
                                    <span data-feather="check-circle" class="icon"></span>
                                </div>
                                <div class="widget-details">
                                    <h1><?php echo $active_subscriptions ?? 0; ?></h1>
                                    <span class="bg-transparent-white">Active Subscriptions</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card-footer rounded">
                <button type="submit" class="btn btn-primary">
                    <span data-feather="check-circle" class="icon-16"></span> Save Settings
                </button>
            </div>

            <?php echo form_close(); ?>
        </div>
    </div>
</div>

<script type="text/javascript">
$(document).ready(function() {
    $("#ai-settings-form").appForm({
        isModal: false,
        onSuccess: function(result) {
            appAlert.success(result.message, {duration: 5000});
        }
    });

    $("#ai-settings-form .select2").select2();

    // Test AI Connection
    $("#test-ai-connection").click(function() {
        var btn = $(this);
        var resultSpan = $("#ai-connection-result");

        btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm"></span> Testing...');
        resultSpan.html('');

        $.ajax({
            url: "<?php echo get_uri('settings/test_ai_connection'); ?>",
            type: "POST",
            dataType: "json",
            success: function(response) {
                btn.prop('disabled', false).html('<span data-feather="zap" class="icon-16"></span> Test AI Connection');
                feather.replace();

                if (response.success) {
                    resultSpan.html('<span class="text-success"><span data-feather="check" class="icon-16"></span> ' + response.message + '</span>');
                } else {
                    resultSpan.html('<span class="text-danger"><span data-feather="x" class="icon-16"></span> ' + response.message + '</span>');
                }
                feather.replace();
            },
            error: function() {
                btn.prop('disabled', false).html('<span data-feather="zap" class="icon-16"></span> Test AI Connection');
                resultSpan.html('<span class="text-danger">Connection failed</span>');
                feather.replace();
            }
        });
    });

    // Test Polar Connection
    $("#test-polar-connection").click(function() {
        var btn = $(this);
        var resultSpan = $("#polar-connection-result");

        btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm"></span> Testing...');
        resultSpan.html('');

        $.ajax({
            url: "<?php echo get_uri('settings/test_polar_connection'); ?>",
            type: "POST",
            dataType: "json",
            success: function(response) {
                btn.prop('disabled', false).html('<span data-feather="link" class="icon-16"></span> Test Polar Configuration');
                feather.replace();

                if (response.success) {
                    resultSpan.html('<span class="text-success"><span data-feather="check" class="icon-16"></span> ' + response.message + '</span>');
                } else {
                    resultSpan.html('<span class="text-danger"><span data-feather="x" class="icon-16"></span> ' + response.message + '</span>');
                }
                feather.replace();
            },
            error: function() {
                btn.prop('disabled', false).html('<span data-feather="link" class="icon-16"></span> Test Polar Configuration');
                resultSpan.html('<span class="text-danger">Connection failed</span>');
                feather.replace();
            }
        });
    });
});

function copyWebhookUrl() {
    var urlInput = document.getElementById('webhook-url');
    urlInput.select();
    document.execCommand('copy');
    appAlert.success('Webhook URL copied to clipboard', {duration: 2000});
}
</script>
