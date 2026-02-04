# AI Settings Save Issue - Debug Log

**STATUS: RESOLVED** (2026-02-04)

## Problem Description (Original)
- Cannot save API keys for DeepSeek and Polar.sh
- After clicking Save, data does not persist
- On page refresh, all settings are gone
- "Test Connection" shows "API key not configured" even after pasting the key

## Solution
The main settings form had CSRF token issues in production. A workaround page was created:
- **URL:** `/settings/manual_ai_save`
- This page bypasses the AJAX form and allows saving settings directly

## Environment
- Hosting: InMotion Hosting
- Application: CRM with AI Assistant Integration

---

## Attempt 1: Auto-create Database Tables (FAILED)
**Date:** 2026-02-04

**Hypothesis:** Database tables don't exist because migration SQL was never run.

**Changes Made:**
1. Added `ensure_table_exists()` method to `Ai_settings_model.php`
2. Added `ensure_table_exists()` method to `Ai_subscriptions_model.php`
3. Added `ensure_table_exists()` method to `Ai_conversations_model.php`
4. Added error handling to `Settings.php` `save_ai_settings()` method

**Files Modified:**
- `app/Models/Ai_settings_model.php`
- `app/Models/Ai_subscriptions_model.php`
- `app/Models/Ai_conversations_model.php`
- `app/Controllers/Settings.php`

**Result:** PARTIALLY SUCCESSFUL
- Table exists: YES
- Write test: SUCCESS
- Settings are saved to database but API key is still empty after form save

**Debug Output (2026-02-04):**
```json
{
    "db_connection": "OK",
    "db_prefix": "rise_",
    "table_exists": "YES",
    "settings_count": 15,
    "write_test": "SUCCESS",
    "ai_api_key": "**EMPTY**"
}
```

**Conclusion:** Database is working, the issue is with form submission (JavaScript/AJAX)

---

## Attempt 2: Adding Direct Save Test (IN PROGRESS)
**Date:** 2026-02-04

**Hypothesis:** The AJAX form submission isn't sending the data correctly, or there's an issue with how the form handles password fields.

**Changes Made:**
1. Added `test_direct_ai_save()` function to test save without JavaScript
2. Added direct test form to debug page
3. Added detailed logging to `save_ai_settings()` to see what POST data is received

**Files Modified:**
- `app/Controllers/Settings.php` - Added test_direct_ai_save() and logging

**How to Test:**
1. Go to `/settings/debug_ai_settings`
2. Scroll down to "Direct Save Test"
3. Click "Test Direct Save" button
4. Check the result

If direct save works but AJAX doesn't, the issue is JavaScript-related.

**Possible Reasons for AJAX Failure:**
1. Password field might not be submitted by browser
2. CSRF token might be invalid
3. JavaScript validation might be blocking submission
4. Form might be using GET instead of POST
5. appForm jQuery plugin might be filtering fields

---

## Attempt 3: Fixing CSRF and Admin Check Issues
**Date:** 2026-02-04

**Finding:** Browser Network tab shows **302 redirect** for save_ai_settings

**Root Cause Identified:**
1. In `app/Config/Security.php` line 85: `public bool $redirect = (ENVIRONMENT === 'production');`
   - In production, CSRF failures cause 302 redirects
2. The AI settings functions had duplicate `access_only_admin()` calls when the Settings controller already has `access_only_admin_or_settings_admin()` in constructor

**Changes Made:**
1. Removed duplicate `$this->access_only_admin()` calls from AI functions in Settings.php
2. Added explicit `<?php echo csrf_field(); ?>` to ai_settings.php form

**Files Modified:**
- `app/Controllers/Settings.php` - Removed duplicate admin checks
- `app/Views/settings/ai_settings.php` - Added explicit CSRF field

---

## Next Steps to Investigate

### 1. Check Browser Console
- Open browser Developer Tools (F12)
- Go to Network tab
- Click Save Settings
- Check if the AJAX request is sent and what response is returned

### 2. Check PHP Error Logs
- Location on InMotion: Usually in `~/logs/error.log` or check cPanel > Error Log
- Look for any PHP errors related to database or the Settings controller

### 3. Verify Database Tables Exist
Run this SQL in phpMyAdmin:
```sql
SHOW TABLES LIKE '%ai%';
```

If tables don't exist, run:
```sql
-- Check what table prefix is used
SHOW TABLES LIKE 'rise_%';

-- Then create tables manually (adjust prefix if needed)
CREATE TABLE IF NOT EXISTS `rise_ai_settings` (
  `id` INT PRIMARY KEY AUTO_INCREMENT,
  `setting_name` VARCHAR(100) NOT NULL,
  `setting_value` TEXT,
  `deleted` INT(1) NOT NULL DEFAULT 0,
  UNIQUE KEY `uk_setting_name` (`setting_name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

INSERT INTO `rise_ai_settings` (`setting_name`, `setting_value`) VALUES
('ai_enabled', '0'),
('ai_provider', 'deepseek'),
('ai_model', 'deepseek-chat'),
('ai_api_key', ''),
('ai_api_endpoint', 'https://api.deepseek.com/chat/completions'),
('ai_max_tokens', '4096'),
('ai_temperature', '0.7'),
('ai_rate_limit_per_minute', '10'),
('ai_rate_limit_per_hour', '60'),
('polar_enabled', '0'),
('polar_access_token', ''),
('polar_webhook_secret', ''),
('polar_product_id', ''),
('polar_organization_id', '')
ON DUPLICATE KEY UPDATE `setting_name` = VALUES(`setting_name`);
```

### 4. Test Database Write Permissions
```sql
-- Try inserting directly
INSERT INTO rise_ai_settings (setting_name, setting_value)
VALUES ('test_setting', 'test_value');

-- Check if it worked
SELECT * FROM rise_ai_settings WHERE setting_name = 'test_setting';

-- Clean up
DELETE FROM rise_ai_settings WHERE setting_name = 'test_setting';
```

---

## Debug Endpoint Added
A debug endpoint has been added to help diagnose the issue:
- **URL:** `{your_site}/settings/debug_ai_settings`
- **Access:** Admin only (must be logged in as admin)

### What the Debug Page Shows:
1. `db_connection` - Is the database connection working?
2. `db_prefix` - What table prefix is being used (e.g., `rise_`)
3. `table_exists` - Does the `ai_settings` table exist?
4. `table_creation` - If table didn't exist, did it create successfully?
5. `settings_count` - How many settings are in the database?
6. `settings` - Current values (sensitive values are masked)
7. `write_test` - Can we write to the database?

### How to Use:
1. Upload the modified `Settings.php` to your server
2. Go to: `https://your-domain.com/settings/debug_ai_settings`
3. Copy the output and share it for debugging

---

## Notes
- The RISE CRM uses table prefix (usually `rise_`)
- The Crud_model automatically adds the prefix in `use_table()` method
- CodeIgniter 4 framework is used
