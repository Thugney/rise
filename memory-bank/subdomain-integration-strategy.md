# Subdomain Integration Strategy for Eriteach.com

## Objectives
Ensure cohesive SEO, user experience, and branding across main domain and subdomains (learn, status, shop, crm at /crm). Facilitate cross-domain tracking and internal linking to boost authority.

## Key Strategies

1. **Internal Linking**
   - Add navigation links in footers/menus to all subdomains (e.g., from main to learn.eriteach.com).
   - Use absolute URLs for cross-subdomain links.

2. **Cross-Domain Tracking**
   - Implemented in GA4 with linker in meta.php for seamless session tracking.

3. **Consistent Branding**
   - Uniform logos, colors, and messaging across subdomains.
   - Shared meta tags template, adapted per subdomain.

4. **SEO Best Practices**
   - Separate sitemaps and robots.txt per subdomain.
   - Hreflang tags for multilingual content (e.g., Norwegian/English/Tigrinya).
   - Canonical tags pointing to preferred versions.

5. **Technical Setup**
   - Use subdomains for specialized functions to build topical authority.
   - Monitor in Google Search Console as separate properties.

## Implementation Notes
- Updates made in CRM codebase; replicate in other subdomains.
- Test cross-subdomain navigation and tracking.
