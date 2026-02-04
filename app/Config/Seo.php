<?php

namespace Config;

use CodeIgniter\Config\BaseConfig;

class Seo extends BaseConfig {

    /**
     * Default SEO Configuration for EriteachCRM
     *
     * Eriteach operates in Norway and worldwide, providing:
     * - Marketing & Social Media Management
     * - Advertising Services
     * - WordPress & WooCommerce Development
     * - Hosting & Website Management
     * - Tech Services for Tigrinya Community
     * - Courses in Technology, Photography, Videography, Editing
     */

    // ========================================
    // ORGANIZATION INFORMATION
    // ========================================

    public $organization = [
        'name' => 'Eriteach',
        'legal_name' => 'Eriteach AS',
        'url' => 'https://eriteach.com',
        'logo' => 'https://eriteach.com/assets/images/eriteach-logo.png',
        'description' => 'Professional marketing, web development, and technology services in Norway. Specializing in social media management, WordPress/WooCommerce development, hosting, and tech education.',
        'email' => 'contact@eriteach.com',
        'phone' => '+47 XXX XX XXX', // Update with actual phone
        'address' => [
            'street' => '', // Update with actual address
            'city' => '', // Update with city
            'region' => 'Norway',
            'postal_code' => '',
            'country' => 'NO'
        ],
        'social_media' => [
            'facebook' => 'https://facebook.com/eriteach',
            'twitter' => 'https://twitter.com/eriteach',
            'linkedin' => 'https://linkedin.com/company/eriteach',
            'instagram' => 'https://instagram.com/eriteach',
            'youtube' => 'https://youtube.com/@eriteach'
        ],
        'founding_date' => '2020', // Update with actual year
        'areas_served' => ['Norway', 'Europe', 'Worldwide']
    ];

    // ========================================
    // CRM SPECIFIC SEO
    // ========================================

    public $crm_meta = [
        'site_name' => 'Eriteach CRM',
        'tagline' => 'Client & Project Management System',
        'default_title' => 'Eriteach CRM - Professional Client & Project Management',
        'title_separator' => ' | ',
        'default_description' => 'Professional CRM and project management platform for Eriteach clients. Manage projects, track progress, collaborate with team members, and access services in marketing, web development, and technology.',
        'default_keywords' => 'CRM, project management, client portal, Eriteach, Norway, marketing services, web development, WordPress, WooCommerce, social media management',
        'default_image' => 'https://eriteach.com/crm/assets/images/eriteach-crm-og.png', // Create this image
        'twitter_site' => '@eriteach',
        'twitter_creator' => '@eriteach'
    ];

    // ========================================
    // PAGE-SPECIFIC META DATA
    // ========================================

    public $page_meta = [
        'dashboard' => [
            'title' => 'Dashboard',
            'description' => 'Your Eriteach CRM dashboard. View project status, upcoming events, tasks, and notifications. Access all your marketing and development projects in one place.',
            'keywords' => 'dashboard, project overview, CRM dashboard, client portal',
            'og_type' => 'website'
        ],
        'projects' => [
            'title' => 'Projects',
            'description' => 'Manage and track all your projects with Eriteach. From WordPress development to marketing campaigns, monitor progress, deadlines, and deliverables.',
            'keywords' => 'project management, web development projects, marketing campaigns, project tracking',
            'og_type' => 'website'
        ],
        'clients' => [
            'title' => 'Clients',
            'description' => 'Client management portal for Eriteach services. View client information, project history, and collaboration details.',
            'keywords' => 'client management, CRM, customer portal, client information',
            'og_type' => 'website'
        ],
        'events' => [
            'title' => 'Events',
            'description' => 'View and manage events, meetings, deadlines, and milestones for your Eriteach projects. Stay on top of your schedule.',
            'keywords' => 'events, calendar, meetings, deadlines, project milestones',
            'og_type' => 'website'
        ],
        'tasks' => [
            'title' => 'Tasks',
            'description' => 'Task management for your Eriteach projects. Track assignments, deadlines, and progress on development and marketing tasks.',
            'keywords' => 'task management, todo list, project tasks, assignments',
            'og_type' => 'website'
        ],
        'invoices' => [
            'title' => 'Invoices',
            'description' => 'View and manage invoices for Eriteach services including web development, marketing, hosting, and consulting.',
            'keywords' => 'invoices, billing, payments, web development invoices, marketing services',
            'og_type' => 'website'
        ],
        'leads' => [
            'title' => 'Leads',
            'description' => 'Lead management for Eriteach business development. Track potential clients and opportunities.',
            'keywords' => 'leads, sales pipeline, business development, CRM leads',
            'og_type' => 'website'
        ],
        'tickets' => [
            'title' => 'Support Tickets',
            'description' => 'Support ticket system for Eriteach clients. Get help with web hosting, WordPress issues, and technical support.',
            'keywords' => 'support tickets, technical support, help desk, customer service',
            'og_type' => 'website'
        ],
        'contracts' => [
            'title' => 'Contracts',
            'description' => 'View and manage service contracts with Eriteach for web development, hosting, and marketing services.',
            'keywords' => 'contracts, service agreements, web development contracts',
            'og_type' => 'website'
        ],
        'proposals' => [
            'title' => 'Proposals',
            'description' => 'Review project proposals from Eriteach for web development, marketing campaigns, and technology services.',
            'keywords' => 'proposals, project quotes, service proposals, web development estimates',
            'og_type' => 'website'
        ],
        'team_members' => [
            'title' => 'Team Members',
            'description' => 'Collaborate with Eriteach team members on your projects. View team directory and contact information.',
            'keywords' => 'team members, collaboration, project team, Eriteach staff',
            'og_type' => 'website'
        ],
        'files' => [
            'title' => 'Files',
            'description' => 'Access project files, documents, images, and resources for your Eriteach projects.',
            'keywords' => 'files, documents, project resources, file management',
            'og_type' => 'website'
        ],
        'settings' => [
            'title' => 'Settings',
            'description' => 'Manage your Eriteach CRM account settings, preferences, and notifications.',
            'keywords' => 'settings, account preferences, CRM settings',
            'og_type' => 'website'
        ]
    ];

    // ========================================
    // SERVICES OFFERED (for structured data)
    // ========================================

    public $services = [
        [
            'name' => 'Marketing Services',
            'description' => 'Professional marketing and social media management for businesses in Norway and worldwide.',
            'url' => 'https://eriteach.com/services/marketing'
        ],
        [
            'name' => 'Social Media Management',
            'description' => 'Strategic social media management and content creation to grow your online presence.',
            'url' => 'https://eriteach.com/services/social-media'
        ],
        [
            'name' => 'WordPress Development',
            'description' => 'Custom WordPress website development, themes, and plugin development.',
            'url' => 'https://eriteach.com/services/wordpress'
        ],
        [
            'name' => 'WooCommerce Development',
            'description' => 'E-commerce solutions with WooCommerce, including store setup and customization.',
            'url' => 'https://eriteach.com/services/woocommerce'
        ],
        [
            'name' => 'Web Hosting',
            'description' => 'Reliable web hosting services with 24/7 support and maintenance.',
            'url' => 'https://eriteach.com/services/hosting'
        ],
        [
            'name' => 'Website Management',
            'description' => 'Ongoing website maintenance, updates, and technical support.',
            'url' => 'https://eriteach.com/services/website-management'
        ],
        [
            'name' => 'Photography Services',
            'description' => 'Professional photography and videography services.',
            'url' => 'https://eriteach.com/photo'
        ],
        [
            'name' => 'Technology Courses',
            'description' => 'Training courses in technology, photography, videography, and editing.',
            'url' => 'https://eriteach.com/courses'
        ],
        [
            'name' => 'Tigrinya Tech Services',
            'description' => 'Technology services and support for the Tigrinya-speaking community.',
            'url' => 'https://eriteach.com/services/tigrinya'
        ]
    ];

    // ========================================
    // LANGUAGE & LOCALE
    // ========================================

    public $locale = [
        'default_language' => 'en',
        'available_languages' => ['en', 'no', 'ti'], // English, Norwegian, Tigrinya
        'default_country' => 'NO',
        'default_locale' => 'en_NO',
        'alternate_locales' => [
            'en' => 'en_US',
            'no' => 'nb_NO',
            'ti' => 'ti_ER'
        ]
    ];

    // ========================================
    // ROBOTS & INDEXING
    // ========================================

    public $robots = [
        'default' => 'index, follow, max-snippet:-1, max-image-preview:large, max-video-preview:-1',
        'dashboard' => 'noindex, nofollow', // Private area
        'projects' => 'noindex, nofollow', // Private client data
        'clients' => 'noindex, nofollow', // Private client data
        'events' => 'noindex, nofollow', // Private calendar
        'tasks' => 'noindex, nofollow', // Private tasks
        'invoices' => 'noindex, nofollow', // Private financial data
        'leads' => 'noindex, nofollow', // Private business data
        'tickets' => 'noindex, nofollow', // Private support data
        'contracts' => 'noindex, nofollow', // Private legal data
        'proposals' => 'noindex, nofollow', // Private business data
        'files' => 'noindex, nofollow', // Private files
        'settings' => 'noindex, nofollow', // Private settings
        'login' => 'noindex, nofollow', // Login page
        'signup' => 'index, follow', // Allow indexing signup page
        'about' => 'index, follow' // Public about page
    ];

    // ========================================
    // CANONICAL URL RULES
    // ========================================

    public $canonical = [
        'enable' => true,
        'force_https' => true,
        'force_www' => false,
        'base_url' => 'https://eriteach.com/crm'
    ];

    // ========================================
    // OPEN GRAPH DEFAULTS
    // ========================================

    public $open_graph = [
        'locale' => 'en_NO',
        'type' => 'website',
        'site_name' => 'Eriteach CRM',
        'image_width' => 1200,
        'image_height' => 630
    ];

    // ========================================
    // TWITTER CARD DEFAULTS
    // ========================================

    public $twitter_card = [
        'card_type' => 'summary_large_image',
        'site' => '@eriteach',
        'creator' => '@eriteach'
    ];

    // ========================================
    // SCHEMA.ORG TYPES
    // ========================================

    public $schema_types = [
        'organization' => 'Organization',
        'local_business' => 'LocalBusiness',
        'web_application' => 'WebApplication',
        'software_application' => 'SoftwareApplication',
        'project' => 'Project',
        'event' => 'Event',
        'person' => 'Person',
        'article' => 'Article'
    ];

    // ========================================
    // SEO FEATURES TOGGLE
    // ========================================

    public $features = [
        'meta_tags' => true,
        'open_graph' => true,
        'twitter_cards' => true,
        'schema_org' => true,
        'canonical_urls' => true,
        'breadcrumbs' => true,
        'sitemap' => true,
        'robots_txt' => true,
        'hreflang' => true
    ];

    // ========================================
    // PERFORMANCE & CACHING
    // ========================================

    public $performance = [
        'enable_meta_cache' => true,
        'cache_duration' => 3600, // 1 hour
        'preload_images' => true,
        'dns_prefetch' => [
            '//fonts.googleapis.com',
            '//fonts.gstatic.com',
            '//cdn.eriteach.com'
        ]
    ];

}
