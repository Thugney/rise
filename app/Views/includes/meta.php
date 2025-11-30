<!-- Google tag (gtag.js) with Consent Mode -->
<script>
// Define dataLayer and the gtag function
window.dataLayer = window.dataLayer || [];
function gtag(){dataLayer.push(arguments);}

// Set default consent to 'denied' for compliance with region-specific rules
gtag('consent', 'default', {
  'ad_storage': 'denied',
  'ad_user_data': 'denied',
  'ad_personalization': 'denied',
  'analytics_storage': 'denied',
  'region': ['EEA']
});
</script>

<script async src="https://www.googletagmanager.com/gtag/js?id=G-6GMRXK3RQ9"></script>
<script>
window.dataLayer = window.dataLayer || [];
function gtag(){dataLayer.push(arguments);}
gtag('js', new Date());
gtag('config', 'G-6GMRXK3RQ9', {
  'cookie_expires': 2 * 365 * 24 * 60 * 60, // Set to 2 years (default for _ga)
  'linker': {
    'domains': ['eriteach.com', 'learn.eriteach.com', 'status.eriteach.com', 'shop.eriteach.com']
  }
});
</script>

<!-- Functions to update consent based on user choice -->
<script>
function grantAnalyticsConsent() {
  gtag('consent', 'update', {
    'analytics_storage': 'granted'
  });
}

function grantAdConsent() {
  gtag('consent', 'update', {
    'ad_storage': 'granted',
    'ad_user_data': 'granted',
    'ad_personalization': 'granted'
  });
}
</script>

<link rel="manifest" href="<?php echo get_uri('pwa/manifest'); ?>">
<meta id="theme-color-meta-tag" name="theme-color" content="<?php echo get_setting("pwa_theme_color") ? get_setting("pwa_theme_color") : "#1c2026"; ?>">

<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta http-equiv="X-UA-Compatible" content="IE=Edge">

<!-- Primary Meta Tags -->
<title>
    <?php
    $router = service('router');
    $controller_name = strtolower(get_actual_controller_name($router));
    $title = get_setting('app_title');
    if (strpos(app_lang($controller_name), '.') === false) {
        $title = app_lang($controller_name) . " | Eriteach CRM | " . $title;
    }
    echo $title;
    ?>
</title>
<meta name="description" content="Eriteach CRM: Professional project management and CRM for marketing agencies in Norway. Manage clients, projects, invoices, and more efficiently.">
<meta name="keywords" content="CRM Norge, project management tool, marketing agency software, client management, invoice system, task tracker, SEO tools integration">
<link rel="canonical" href="<?php echo current_url(); ?>">

<!-- Open Graph / Facebook -->
<meta property="og:title" content="<?php echo $title; ?>">
<meta property="og:description" content="Eriteach CRM: Streamline your marketing projects with our powerful CRM solution.">
<meta property="og:url" content="<?php echo current_url(); ?>">
<meta property="og:type" content="website">
<meta property="og:image" content="https://eriteach.com/assets/images/logo.png">

<!-- Twitter -->
<meta name="twitter:card" content="summary_large_image">
<meta name="twitter:title" content="<?php echo $title; ?>">
<meta name="twitter:description" content="Eriteach CRM: Streamline your marketing projects with our powerful CRM solution.">
<meta name="twitter:image" content="https://eriteach.com/assets/images/logo.png">

<link rel="icon" href="<?php echo get_favicon_url(); ?>" />