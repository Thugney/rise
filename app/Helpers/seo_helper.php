<?php

/**
 * SEO Helper Functions
 *
 * Provides functions for generating SEO meta tags, Open Graph tags,
 * Twitter Cards, and Schema.org structured data for EriteachCRM
 */

if (!function_exists('seo_meta_tags')) {
    /**
     * Generate complete SEO meta tags for a page
     *
     * @param array $data SEO data (title, description, keywords, image, etc.)
     * @return string HTML meta tags
     */
    function seo_meta_tags($data = []) {
        $seo_config = config('Seo');

        // Extract data with defaults
        $page_name = $data['page_name'] ?? 'dashboard';
        $title = $data['title'] ?? null;
        $description = $data['description'] ?? null;
        $keywords = $data['keywords'] ?? null;
        $image = $data['image'] ?? $seo_config->crm_meta['default_image'];
        $url = $data['url'] ?? current_url();
        $type = $data['type'] ?? 'website';

        // Get page-specific meta if not provided
        if (!$title || !$description) {
            $page_meta = $seo_config->page_meta[$page_name] ?? [];
            $title = $title ?? ($page_meta['title'] ?? $seo_config->crm_meta['default_title']);
            $description = $description ?? ($page_meta['description'] ?? $seo_config->crm_meta['default_description']);
            $keywords = $keywords ?? ($page_meta['keywords'] ?? $seo_config->crm_meta['default_keywords']);
        }

        // Build full title
        $full_title = $title . $seo_config->crm_meta['title_separator'] . $seo_config->crm_meta['site_name'];

        // Get robots directive
        $robots = $seo_config->robots[$page_name] ?? $seo_config->robots['default'];

        $html = '';

        // Basic Meta Tags
        if ($seo_config->features['meta_tags']) {
            $html .= '<meta name="description" content="' . esc($description) . '">' . PHP_EOL;
            $html .= '<meta name="keywords" content="' . esc($keywords) . '">' . PHP_EOL;
            $html .= '<meta name="robots" content="' . esc($robots) . '">' . PHP_EOL;
            $html .= '<meta name="googlebot" content="' . esc($robots) . '">' . PHP_EOL;
        }

        // Canonical URL
        if ($seo_config->features['canonical_urls'] && $seo_config->canonical['enable']) {
            $canonical_url = seo_canonical_url($url);
            $html .= '<link rel="canonical" href="' . esc($canonical_url) . '">' . PHP_EOL;
        }

        // Open Graph Tags
        if ($seo_config->features['open_graph']) {
            $html .= seo_open_graph_tags([
                'title' => $full_title,
                'description' => $description,
                'url' => $url,
                'image' => $image,
                'type' => $type
            ]);
        }

        // Twitter Card Tags
        if ($seo_config->features['twitter_cards']) {
            $html .= seo_twitter_card_tags([
                'title' => $full_title,
                'description' => $description,
                'image' => $image
            ]);
        }

        // Language/Locale Tags
        if ($seo_config->features['hreflang']) {
            $html .= seo_hreflang_tags($url);
        }

        // DNS Prefetch for Performance
        if ($seo_config->performance['dns_prefetch']) {
            foreach ($seo_config->performance['dns_prefetch'] as $domain) {
                $html .= '<link rel="dns-prefetch" href="' . esc($domain) . '">' . PHP_EOL;
            }
        }

        return $html;
    }
}

if (!function_exists('seo_open_graph_tags')) {
    /**
     * Generate Open Graph meta tags
     *
     * @param array $data OG data
     * @return string HTML meta tags
     */
    function seo_open_graph_tags($data = []) {
        $seo_config = config('Seo');
        $og = $seo_config->open_graph;

        $title = $data['title'] ?? $seo_config->crm_meta['default_title'];
        $description = $data['description'] ?? $seo_config->crm_meta['default_description'];
        $url = $data['url'] ?? current_url();
        $image = $data['image'] ?? $seo_config->crm_meta['default_image'];
        $type = $data['type'] ?? $og['type'];

        $html = '';
        $html .= '<meta property="og:title" content="' . esc($title) . '">' . PHP_EOL;
        $html .= '<meta property="og:description" content="' . esc($description) . '">' . PHP_EOL;
        $html .= '<meta property="og:url" content="' . esc($url) . '">' . PHP_EOL;
        $html .= '<meta property="og:type" content="' . esc($type) . '">' . PHP_EOL;
        $html .= '<meta property="og:site_name" content="' . esc($og['site_name']) . '">' . PHP_EOL;
        $html .= '<meta property="og:locale" content="' . esc($og['locale']) . '">' . PHP_EOL;

        if ($image) {
            $html .= '<meta property="og:image" content="' . esc($image) . '">' . PHP_EOL;
            $html .= '<meta property="og:image:width" content="' . $og['image_width'] . '">' . PHP_EOL;
            $html .= '<meta property="og:image:height" content="' . $og['image_height'] . '">' . PHP_EOL;
            $html .= '<meta property="og:image:alt" content="' . esc($title) . '">' . PHP_EOL;
        }

        return $html;
    }
}

if (!function_exists('seo_twitter_card_tags')) {
    /**
     * Generate Twitter Card meta tags
     *
     * @param array $data Twitter card data
     * @return string HTML meta tags
     */
    function seo_twitter_card_tags($data = []) {
        $seo_config = config('Seo');
        $twitter = $seo_config->twitter_card;

        $title = $data['title'] ?? $seo_config->crm_meta['default_title'];
        $description = $data['description'] ?? $seo_config->crm_meta['default_description'];
        $image = $data['image'] ?? $seo_config->crm_meta['default_image'];

        $html = '';
        $html .= '<meta name="twitter:card" content="' . esc($twitter['card_type']) . '">' . PHP_EOL;
        $html .= '<meta name="twitter:site" content="' . esc($twitter['site']) . '">' . PHP_EOL;
        $html .= '<meta name="twitter:creator" content="' . esc($twitter['creator']) . '">' . PHP_EOL;
        $html .= '<meta name="twitter:title" content="' . esc($title) . '">' . PHP_EOL;
        $html .= '<meta name="twitter:description" content="' . esc($description) . '">' . PHP_EOL;

        if ($image) {
            $html .= '<meta name="twitter:image" content="' . esc($image) . '">' . PHP_EOL;
            $html .= '<meta name="twitter:image:alt" content="' . esc($title) . '">' . PHP_EOL;
        }

        return $html;
    }
}

if (!function_exists('seo_canonical_url')) {
    /**
     * Generate canonical URL
     *
     * @param string $url URL to canonicalize
     * @return string Canonical URL
     */
    function seo_canonical_url($url = null) {
        $seo_config = config('Seo');
        $url = $url ?? current_url();

        // Force HTTPS if configured
        if ($seo_config->canonical['force_https']) {
            $url = str_replace('http://', 'https://', $url);
        }

        // Force WWW if configured
        if ($seo_config->canonical['force_www']) {
            $url = preg_replace('/https?:\/\/(?!www\.)/', 'https://www.', $url);
        }

        // Remove query strings for canonical (optional)
        $url = strtok($url, '?');

        return $url;
    }
}

if (!function_exists('seo_hreflang_tags')) {
    /**
     * Generate hreflang tags for multi-language support
     *
     * @param string $url Current page URL
     * @return string HTML link tags
     */
    function seo_hreflang_tags($url = null) {
        $seo_config = config('Seo');
        $url = $url ?? current_url();

        $html = '';

        foreach ($seo_config->locale['alternate_locales'] as $lang => $locale) {
            // Generate URL for each language
            $lang_url = $url; // In future, modify URL based on language
            $html .= '<link rel="alternate" hreflang="' . esc($lang) . '" href="' . esc($lang_url) . '">' . PHP_EOL;
        }

        // Add x-default for default language
        $html .= '<link rel="alternate" hreflang="x-default" href="' . esc($url) . '">' . PHP_EOL;

        return $html;
    }
}

if (!function_exists('seo_schema_organization')) {
    /**
     * Generate Schema.org Organization structured data
     *
     * @return string JSON-LD markup
     */
    function seo_schema_organization() {
        $seo_config = config('Seo');
        $org = $seo_config->organization;

        $schema = [
            '@context' => 'https://schema.org',
            '@type' => 'Organization',
            'name' => $org['name'],
            'legalName' => $org['legal_name'],
            'url' => $org['url'],
            'logo' => $org['logo'],
            'description' => $org['description'],
            'email' => $org['email'],
            'telephone' => $org['phone'],
            'foundingDate' => $org['founding_date'],
            'address' => [
                '@type' => 'PostalAddress',
                'streetAddress' => $org['address']['street'],
                'addressLocality' => $org['address']['city'],
                'addressRegion' => $org['address']['region'],
                'postalCode' => $org['address']['postal_code'],
                'addressCountry' => $org['address']['country']
            ],
            'areaServed' => array_map(function($area) {
                return ['@type' => 'Place', 'name' => $area];
            }, $org['areas_served']),
            'sameAs' => array_values($org['social_media'])
        ];

        return '<script type="application/ld+json">' . PHP_EOL . json_encode($schema, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) . PHP_EOL . '</script>' . PHP_EOL;
    }
}

if (!function_exists('seo_schema_web_application')) {
    /**
     * Generate Schema.org WebApplication structured data
     *
     * @return string JSON-LD markup
     */
    function seo_schema_web_application() {
        $seo_config = config('Seo');

        $schema = [
            '@context' => 'https://schema.org',
            '@type' => 'WebApplication',
            'name' => $seo_config->crm_meta['site_name'],
            'url' => $seo_config->canonical['base_url'] . '/crm',
            'description' => $seo_config->crm_meta['default_description'],
            'applicationCategory' => 'BusinessApplication',
            'operatingSystem' => 'Web Browser',
            'offers' => [
                '@type' => 'Offer',
                'price' => '0',
                'priceCurrency' => 'NOK'
            ],
            'provider' => [
                '@type' => 'Organization',
                'name' => $seo_config->organization['name'],
                'url' => $seo_config->organization['url']
            ]
        ];

        return '<script type="application/ld+json">' . PHP_EOL . json_encode($schema, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) . PHP_EOL . '</script>' . PHP_EOL;
    }
}

if (!function_exists('seo_schema_breadcrumb')) {
    /**
     * Generate Schema.org BreadcrumbList structured data
     *
     * @param array $breadcrumbs Array of breadcrumb items
     * @return string JSON-LD markup
     */
    function seo_schema_breadcrumb($breadcrumbs = []) {
        if (empty($breadcrumbs)) {
            return '';
        }

        $items = [];
        $position = 1;

        foreach ($breadcrumbs as $crumb) {
            $items[] = [
                '@type' => 'ListItem',
                'position' => $position++,
                'name' => $crumb['name'],
                'item' => $crumb['url'] ?? null
            ];
        }

        $schema = [
            '@context' => 'https://schema.org',
            '@type' => 'BreadcrumbList',
            'itemListElement' => $items
        ];

        return '<script type="application/ld+json">' . PHP_EOL . json_encode($schema, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) . PHP_EOL . '</script>' . PHP_EOL;
    }
}

if (!function_exists('seo_schema_project')) {
    /**
     * Generate Schema.org Project structured data
     *
     * @param array $project Project data
     * @return string JSON-LD markup
     */
    function seo_schema_project($project = []) {
        if (empty($project)) {
            return '';
        }

        $schema = [
            '@context' => 'https://schema.org',
            '@type' => 'Project',
            'name' => $project['title'] ?? '',
            'description' => $project['description'] ?? '',
            'startDate' => $project['start_date'] ?? '',
            'endDate' => $project['end_date'] ?? null,
            'url' => $project['url'] ?? current_url()
        ];

        if (!empty($project['client'])) {
            $schema['sponsor'] = [
                '@type' => 'Organization',
                'name' => $project['client']
            ];
        }

        return '<script type="application/ld+json">' . PHP_EOL . json_encode($schema, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) . PHP_EOL . '</script>' . PHP_EOL;
    }
}

if (!function_exists('seo_schema_event')) {
    /**
     * Generate Schema.org Event structured data
     *
     * @param array $event Event data
     * @return string JSON-LD markup
     */
    function seo_schema_event($event = []) {
        if (empty($event)) {
            return '';
        }

        $seo_config = config('Seo');

        $schema = [
            '@context' => 'https://schema.org',
            '@type' => 'Event',
            'name' => $event['title'] ?? '',
            'description' => $event['description'] ?? '',
            'startDate' => $event['start_date'] ?? '',
            'endDate' => $event['end_date'] ?? null,
            'url' => $event['url'] ?? current_url(),
            'organizer' => [
                '@type' => 'Organization',
                'name' => $seo_config->organization['name'],
                'url' => $seo_config->organization['url']
            ]
        ];

        if (!empty($event['location'])) {
            $schema['location'] = [
                '@type' => 'Place',
                'name' => $event['location']
            ];
        }

        return '<script type="application/ld+json">' . PHP_EOL . json_encode($schema, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) . PHP_EOL . '</script>' . PHP_EOL;
    }
}

if (!function_exists('seo_schema_article')) {
    /**
     * Generate Schema.org Article structured data
     *
     * @param array $article Article data
     * @return string JSON-LD markup
     */
    function seo_schema_article($article = []) {
        if (empty($article)) {
            return '';
        }

        $seo_config = config('Seo');

        $schema = [
            '@context' => 'https://schema.org',
            '@type' => 'Article',
            'headline' => $article['title'] ?? '',
            'description' => $article['description'] ?? '',
            'author' => [
                '@type' => 'Person',
                'name' => $article['author'] ?? 'Eriteach'
            ],
            'publisher' => [
                '@type' => 'Organization',
                'name' => $seo_config->organization['name'],
                'logo' => [
                    '@type' => 'ImageObject',
                    'url' => $seo_config->organization['logo']
                ]
            ],
            'datePublished' => $article['published_date'] ?? date('c'),
            'dateModified' => $article['modified_date'] ?? date('c')
        ];

        if (!empty($article['image'])) {
            $schema['image'] = $article['image'];
        }

        return '<script type="application/ld+json">' . PHP_EOL . json_encode($schema, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) . PHP_EOL . '</script>' . PHP_EOL;
    }
}

if (!function_exists('seo_schema_services')) {
    /**
     * Generate Schema.org Service structured data for all Eriteach services
     *
     * @return string JSON-LD markup
     */
    function seo_schema_services() {
        $seo_config = config('Seo');
        $org = $seo_config->organization;

        $services = [];
        foreach ($seo_config->services as $service) {
            $services[] = [
                '@type' => 'Service',
                'name' => $service['name'],
                'description' => $service['description'],
                'url' => $service['url'],
                'provider' => [
                    '@type' => 'Organization',
                    'name' => $org['name']
                ],
                'areaServed' => $org['areas_served']
            ];
        }

        $schema = [
            '@context' => 'https://schema.org',
            '@type' => 'ItemList',
            'itemListElement' => $services
        ];

        return '<script type="application/ld+json">' . PHP_EOL . json_encode($schema, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) . PHP_EOL . '</script>' . PHP_EOL;
    }
}

if (!function_exists('seo_get_page_title')) {
    /**
     * Get formatted page title
     *
     * @param string $page_name Page identifier
     * @param string $custom_title Custom title (optional)
     * @return string Formatted title
     */
    function seo_get_page_title($page_name = '', $custom_title = null) {
        $seo_config = config('Seo');

        if ($custom_title) {
            $title = $custom_title;
        } else {
            $page_meta = $seo_config->page_meta[$page_name] ?? [];
            $title = $page_meta['title'] ?? $seo_config->crm_meta['default_title'];
        }

        return $title . $seo_config->crm_meta['title_separator'] . $seo_config->crm_meta['site_name'];
    }
}

if (!function_exists('seo_get_page_description')) {
    /**
     * Get page description
     *
     * @param string $page_name Page identifier
     * @param string $custom_description Custom description (optional)
     * @return string Description
     */
    function seo_get_page_description($page_name = '', $custom_description = null) {
        $seo_config = config('Seo');

        if ($custom_description) {
            return $custom_description;
        }

        $page_meta = $seo_config->page_meta[$page_name] ?? [];
        return $page_meta['description'] ?? $seo_config->crm_meta['default_description'];
    }
}
