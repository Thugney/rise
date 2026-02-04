<?php

namespace App\Controllers;

/**
 * Sitemap Controller
 * Generates XML sitemap for Eriteach CRM
 */
class Sitemap extends App_Controller {

    public function __construct() {
        parent::__construct();
    }

    /**
     * Generate XML sitemap
     * URL: /crm/sitemap.xml or /crm/sitemap
     */
    public function index() {
        helper('seo');
        $seo_config = config('Seo');

        // Set XML content type
        header('Content-Type: application/xml; charset=utf-8');

        $base_url = $seo_config->canonical['base_url'] . '/crm';

        // Start XML
        echo '<?xml version="1.0" encoding="UTF-8"?>' . PHP_EOL;
        echo '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9"' . PHP_EOL;
        echo '        xmlns:image="http://www.google.com/schemas/sitemap-image/1.1">' . PHP_EOL;

        // Public pages
        $public_pages = [
            [
                'loc' => $base_url,
                'changefreq' => 'daily',
                'priority' => '1.0',
                'lastmod' => date('Y-m-d')
            ],
            [
                'loc' => $base_url . '/signup',
                'changefreq' => 'monthly',
                'priority' => '0.8',
                'lastmod' => date('Y-m-d')
            ],
            [
                'loc' => $base_url . '/about',
                'changefreq' => 'monthly',
                'priority' => '0.7',
                'lastmod' => date('Y-m-d')
            ]
        ];

        // Output public pages
        foreach ($public_pages as $page) {
            echo '  <url>' . PHP_EOL;
            echo '    <loc>' . htmlspecialchars($page['loc']) . '</loc>' . PHP_EOL;
            echo '    <lastmod>' . $page['lastmod'] . '</lastmod>' . PHP_EOL;
            echo '    <changefreq>' . $page['changefreq'] . '</changefreq>' . PHP_EOL;
            echo '    <priority>' . $page['priority'] . '</priority>' . PHP_EOL;
            echo '  </url>' . PHP_EOL;
        }

        // Note: Private pages (dashboard, projects, clients, etc.) are NOT included
        // as they require authentication and should not be indexed

        // Close XML
        echo '</urlset>';

        exit;
    }

    /**
     * Generate HTML sitemap
     * URL: /crm/sitemap/html
     */
    public function html() {
        helper('seo');
        $seo_config = config('Seo');

        $base_url = $seo_config->canonical['base_url'] . '/crm';

        $data = [
            'page_title' => 'Sitemap',
            'base_url' => $base_url,
            'pages' => [
                'Public Pages' => [
                    ['name' => 'Home', 'url' => $base_url],
                    ['name' => 'Sign Up', 'url' => $base_url . '/signup'],
                    ['name' => 'About', 'url' => $base_url . '/about']
                ]
            ]
        ];

        return $this->template->rander('sitemap/html_view', $data);
    }

}
