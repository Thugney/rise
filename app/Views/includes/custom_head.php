<?php
/**
 * Custom Head - SEO Structured Data
 * Schema.org JSON-LD markup for Eriteach CRM
 */

// Load SEO helper
helper('seo');

// Get current page information
$router = service('router');
$controller_name = strtolower(get_actual_controller_name($router));

// Always include Organization schema
echo seo_schema_organization();

// Include WebApplication schema on main pages
if (in_array($controller_name, ['dashboard', 'projects', 'clients', 'tasks'])) {
    echo seo_schema_web_application();
}

// Include Services schema on dashboard
if ($controller_name === 'dashboard') {
    echo seo_schema_services();
}

// Include page-specific structured data if available
if (isset($structured_data)) {
    echo $structured_data;
}

// Add custom headers here if needed