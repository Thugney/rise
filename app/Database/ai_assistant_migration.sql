-- =============================================================
-- AI Assistant Database Migration
-- Version: 1.0
-- Run this SQL manually in your database management tool
-- =============================================================

-- 1. AI Conversations table
CREATE TABLE IF NOT EXISTS `rise_ai_conversations` (
  `id` INT PRIMARY KEY AUTO_INCREMENT,
  `user_id` INT NOT NULL,
  `session_id` VARCHAR(128) NOT NULL,
  `module` VARCHAR(50) DEFAULT NULL COMMENT 'task, project, client, etc.',
  `user_query` TEXT NOT NULL,
  `assistant_response` TEXT,
  `tokens_used` INT DEFAULT 0,
  `context_snapshot` TEXT COMMENT 'JSON: permission state at query time',
  `deleted` INT(1) NOT NULL DEFAULT 0,
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
  INDEX `idx_user_session` (`user_id`, `session_id`),
  INDEX `idx_created_at` (`created_at`),
  FOREIGN KEY (`user_id`) REFERENCES `rise_users`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

-- 2. Polar.sh Subscriptions table
CREATE TABLE IF NOT EXISTS `rise_ai_subscriptions` (
  `id` INT PRIMARY KEY AUTO_INCREMENT,
  `user_id` INT NOT NULL,
  `polar_customer_id` VARCHAR(128) NOT NULL DEFAULT '',
  `polar_subscription_id` VARCHAR(128) NOT NULL DEFAULT '',
  `status` ENUM('active', 'canceled', 'past_due', 'trialing', 'inactive') DEFAULT 'inactive',
  `current_period_start` DATETIME DEFAULT NULL,
  `current_period_end` DATETIME DEFAULT NULL,
  `canceled_at` DATETIME DEFAULT NULL,
  `plan_name` VARCHAR(100) DEFAULT 'ai_assistant_monthly',
  `deleted` INT(1) NOT NULL DEFAULT 0,
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  UNIQUE KEY `uk_user_id` (`user_id`),
  INDEX `idx_polar_subscription` (`polar_subscription_id`),
  INDEX `idx_status` (`status`),
  FOREIGN KEY (`user_id`) REFERENCES `rise_users`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

-- 3. AI Settings table
CREATE TABLE IF NOT EXISTS `rise_ai_settings` (
  `id` INT PRIMARY KEY AUTO_INCREMENT,
  `setting_name` VARCHAR(100) NOT NULL,
  `setting_value` TEXT,
  `deleted` INT(1) NOT NULL DEFAULT 0,
  UNIQUE KEY `uk_setting_name` (`setting_name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

-- 4. Default settings (AI + Polar.sh)
INSERT INTO `rise_ai_settings` (`setting_name`, `setting_value`) VALUES
('ai_enabled', '0'),
('ai_provider', 'deepseek'),
('ai_model', 'deepseek-chat'),
('ai_api_key', ''),
('ai_api_endpoint', 'https://api.deepseek.com/chat/completions'),
('ai_max_tokens', '4096'),
('ai_temperature', '0.7'),
('ai_rate_limit_per_hour', '60'),
('polar_enabled', '0'),
('polar_access_token', ''),
('polar_webhook_secret', ''),
('polar_product_id', ''),
('polar_organization_id', '')
ON DUPLICATE KEY UPDATE `setting_name` = VALUES(`setting_name`);

-- =============================================================
-- End of Migration
-- =============================================================
