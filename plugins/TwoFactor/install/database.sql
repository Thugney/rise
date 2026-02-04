CREATE TABLE IF NOT EXISTS `twofactor_settings` (
  `setting_name` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `setting_value` mediumtext COLLATE utf8_unicode_ci NOT NULL,
  `type` varchar(20) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'app',
  `deleted` tinyint(1) NOT NULL DEFAULT '0',
  UNIQUE KEY `setting_name` (`setting_name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci; --#

CREATE TABLE IF NOT EXISTS `twofactor_verification` (
`id` int(11) NOT NULL AUTO_INCREMENT,
`created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
`code` varchar(10) COLLATE utf8_unicode_ci NOT NULL,
`params` text COLLATE utf8_unicode_ci NOT NULL,
`deleted` int(1) NOT NULL DEFAULT '0',
PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ; --#

INSERT INTO `twofactor_settings` (`setting_name`, `setting_value`, `deleted`) VALUES 
('twofactor_item_purchase_code', 'TwoFactor-ITEM-PURCHASE-CODE', 0),
('twofactor_default_sms_template', 'Your verification code for {APP_TITLE} is {CODE}. This code will expire in 2 hours. ', 0); --#

INSERT INTO `email_templates` (`template_name`, `email_subject`, `default_message`, `custom_message`, `template_type`, `language`, `deleted`) VALUES
('twofactor_twofactor_authentication', 'Confirm your login', '<div style="background-color: #eeeeef; padding: 50px 0; "><div style="max-width:640px; margin:0 auto; "> <div style="color: #fff; text-align: center; background-color:#33333e; padding: 30px; border-top-left-radius: 3px; border-top-right-radius: 3px; margin: 0;">  <h1>{APP_TITLE}</h1></div><div style="padding: 20px; background-color: rgb(255, 255, 255);">            <p style="color: rgb(85, 85, 85); font-size: 14px;"> Somebody (hopefully you) just attempted to log into the account for <b>{LOGIN_EMAIL}</b>.</p><p style=""><font color="#555555"><span style="font-size: 14px;">Please confirm your login by entering the code from below:</span></font></p><div style="background-color: #eeeeef; padding: 30px 0; text-align: center"><h2>{CODE}</h2></div><p style=""><br></p><p style="color: rgb(85, 85, 85); font-size: 14px;">This code will expire in <b>2&nbsp;hours</b>.</p><p style=""><font color="#555555"><span style="font-size: 14px;">If this was not you, please change your password to make sure your account is secure.</span></font></p>            <p style="color: rgb(85, 85, 85);"><br></p>            <p style="color: rgb(85, 85, 85); font-size: 14px;">{SIGNATURE}</p>        </div>    </div></div>', '', 'default', '', 0); --# 

CREATE TABLE IF NOT EXISTS `twofactor_user_settings` (
`id` int(11) NOT NULL AUTO_INCREMENT,
`user_id` int(11) NOT NULL DEFAULT 0,
`enable_twofactor` TINYINT(1) NOT NULL DEFAULT '0',
`method` enum('email','sms','google_authenticator') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'email',
`email` VARCHAR(255) COLLATE utf8_unicode_ci NULL,
`google_secret_key` TEXT COLLATE utf8_unicode_ci NULL,
`authorized` TINYINT(1) NOT NULL DEFAULT '0',
`deleted` tinyint(1) NOT NULL DEFAULT '0',
PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ; --#
