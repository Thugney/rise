<!DOCTYPE html>
<html lang="en">
    <head>
        <?php echo view('includes/head'); ?>
    </head>
    <body class="public-view signin-page">
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
                    <img src="assets/images/welcome-illustration.svg" alt="Welcome " style="width: 100%; height: auto;">
                </div>
                <div class="col-md-6">
                    <h3>Welcome to Eriteach CRM</h3>
                    <?php
                    if (isset($form_type) && $form_type == "request_reset_password") {
                        echo view("signin/reset_password_form");
                    } else if (isset($form_type) && $form_type == "new_password") {
                        echo view('signin/new_password_form');
                    } else {
                        echo view("signin/signin_form");
                    }
                    ?>
                    <p>Don't have an account? <a href="<?php echo get_uri("signup"); ?>">Sign up</a></p>
                </div>
            </div>
        </div>

        <script>
            $(document).ready(function () {
                // Remove scrollbar init if not needed for new design
            });
        </script>

        <?php echo view("includes/footer"); ?>
    </body>
</html>