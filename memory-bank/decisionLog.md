# Decision Log

This file records architectural and implementation decisions using a list format.
2025-10-22 16:59:35 - Log of updates made.

*
      
## Decision

Corrected baseURL case to 'https://eriteach.com/crm/' (lowercase 'crm').

## Rationale

Server directories are case-sensitive; 'CRM' vs 'crm' mismatch likely causing issues. Error persists due to missing Connection.php.

## Implementation Details

Modified app/Config/App.php line 67 to use lowercase path.

[2025-10-22 18:05:26] - Fixed case sensitivity in URL path.

## Decision

Recommend verifying and uploading missing system files.

## Rationale

Persistent "class not found" error despite config updates indicates incomplete CodeIgniter core files on server.

## Implementation Details

User to check if system/Database/MySQLi/Connection.php exists on server; if not, upload entire local system/ directory to public_html/crm/system/. Also, confirm PHP version >=7.4 and MySQLi extension enabled via phpinfo().

[2025-10-22 18:05:26] - Documented fix for missing class error.

## Decision

Created sample robots.txt for proper crawling configuration.

## Rationale

To allow search engines to crawl public content while disallowing sensitive areas like admin and private paths, preventing indexing of duplicate or private content.

## Implementation Details

Sample provided in memory-bank/robots-sample.txt. Deploy to https://eriteach.com/robots.txt. Includes references to sitemaps for all subdomains.

[2025-10-22 18:54:18] - Documented robots.txt setup.