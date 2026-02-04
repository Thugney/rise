<?php

/* NOTE: DO NOT CHANGE THIS FILE. IF YOU WANT TO UPDATE THE LANGUAGE THEN COPY THIS FILE TO custom_lang.php AND UPDATE THERE */

$lang["twofactor"] = "Двухфакторный";
$lang["twofactor_settings"] = "Двухфакторные настройки";
$lang["twofactor_email_subject"] = "Тема письма";
$lang["twofactor_email_message"] = "Электронное сообщение";
$lang["twofactor_twofactor_authentication"] = "Двухфакторная аутентификация";
$lang["twofactor_enable_twofactor_authentication"] = "Включить двухфакторную аутентификацию";
$lang["twofactor_info_text"] = "Перед выходом откройте новый браузер и убедитесь, что двухфакторная аутентификация работает.";
$lang["twofactor_code"] = "Код";
$lang["twofactor_code_expaired_message"] = "Срок действия двухфакторного кода истек или что-то пошло не так.";
$lang["twofactor_code_message"] = "На вашу электронную почту был отправлен одноразовый пароль. Используйте его, чтобы продолжить.";
$lang["twofactor_code_success_message"] = "Выполнен вход в систему. Перенаправление на панель управления ...";
$lang["twofactor_continue"] = "Продолжить";
$lang["twofactor_not_you"] = "Не ты?";
$lang["twofactor_restore_email_message_to_default"] = "Восстановить сообщение электронной почты по умолчанию";
$lang["twofactor_email_message_restored"] = "Сообщение электронной почты восстановлено по умолчанию!";

/* Version: 1.1 */

$lang["twofactor_method"] = "Метод";
$lang["twofactor_send_otp"] = "Отправить OTP";
$lang["twofactor_qr_code"] = "QR-код";
$lang["twofactor_google_authenticator_help_message"] = "Отсканируйте этот QR-код с помощью приложения Google Authenticator и введите код подтверждения ниже.";

$lang["twofactor_enable_sms"] = "Включить SMS";
$lang["twofactor_twilio_account_sid"] = "SID аккаунта";
$lang["twofactor_twilio_auth_token"] = "Токен аутентификации";
$lang["twofactor_twilio_phone_number"] = "Номер телефона Twilio";
$lang["twofactor_send_test_sms"] = "Отправить тестовое SMS";

$lang["twofactor_twilio_phone_no_help_message"] = "Номера телефонов должны быть в формате %s. В противном случае он/она не получит SMS.";
$lang["twofactor_twilio_user_phone_no_help_message"] = "Пожалуйста, используйте формат %s (<b>+14155552671</b>) в номере телефона пользователя/клиента. В противном случае SMS OTP не будет работать. Нажмите %s, чтобы узнать больше о телефонных номерах. следует отформатировать.";

$lang["twofactor_send_test_sms_successfull_message"] = "Тестовое SMS отправлено успешно!";
$lang["twofactor_send_test_sms_error_message"] = "Ошибка! Невозможно подключиться к Twilio, используя учетные данные.";

$lang["twofactor_here"] = "Здесь";
$lang["twofactor_restore_template_to_default"] = "Восстановить шаблон по умолчанию";

$lang["twofactor_code_message_email"] = "На ваш адрес электронной почты был отправлен OTP. Пожалуйста, возьмите его, чтобы продолжить.";
$lang["twofactor_code_message_sms"] = "На ваш телефон был отправлен OTP. Пожалуйста, возьмите его, чтобы продолжить.";
$lang["twofactor_code_message_google_authenticator"] = "Введите 6-значный код подтверждения, сгенерированный вашим приложением для аутентификации.";

$lang["twofactor_enable_email_authentication_for_all"] = "Включить аутентификацию по электронной почте для всех";
$lang["twofactor_enable_email_authentication_for_all_help_message"] = "Если вы включите аутентификацию по электронной почте для всех, она будет применяться только к пользователям, у которых в личном профиле не включена двухфакторная аутентификация.";

return $lang;
