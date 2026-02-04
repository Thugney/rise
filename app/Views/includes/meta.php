<?php
/**
 * Meta Tags & SEO
 * Enhanced with professional SEO implementation for Eriteach CRM
 */

// Get current page information
$router = service('router');
$controller_name = strtolower(get_actual_controller_name($router));
$app_title = get_setting('app_title');

// Prepare SEO data
$seo_data = [
    'page_name' => $controller_name,
    'title' => isset($page_title) ? $page_title : (strpos(app_lang($controller_name), '.') === false ? app_lang($controller_name) : ''),
    'description' => isset($page_description) ? $page_description : null,
    'keywords' => isset($page_keywords) ? $page_keywords : null,
    'image' => isset($page_image) ? $page_image : null,
    'url' => current_url(),
    'type' => isset($page_type) ? $page_type : 'website'
];

// Build full title
if ($seo_data['title']) {
    $full_title = $seo_data['title'] . " | " . $app_title;
} else {
    $full_title = $app_title;
}
?>

<!-- Basic Meta Tags -->
<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=Edge">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="author" content="Eriteach">

<!-- PWA & Theme -->
<link rel="manifest" href="<?php echo get_uri('pwa/manifest'); ?>">
<meta id="theme-color-meta-tag" name="theme-color" content="<?php echo get_setting("pwa_theme_color") ? get_setting("pwa_theme_color") : "#1c2026"; ?>">

<!-- Favicon -->
<link rel="icon" href="<?php echo get_favicon_url(); ?>" />
<link rel="apple-touch-icon" href="<?php echo get_favicon_url(); ?>" />

<!-- Page Title -->
<title><?php echo $full_title; ?></title>

<!-- SEO Meta Tags, Open Graph, Twitter Cards, Structured Data -->
<?php
// Load SEO helper if not already loaded
helper('seo');

// Output all SEO meta tags
echo seo_meta_tags($seo_data);
?>