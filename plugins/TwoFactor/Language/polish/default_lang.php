<?php

/* NOTE: DO NOT CHANGE THIS FILE. IF YOU WANT TO UPDATE THE LANGUAGE THEN COPY THIS FILE TO custom_lang.php AND UPDATE THERE */

$lang["twofactor"] = "Dwuczynnikowy";
$lang["twofactor_settings"] = "Ustawienia dwuskładnikowe";
$lang["twofactor_email_subject"] = "Temat e-maila";
$lang["twofactor_email_message"] = "Wiadomość e-mail";
$lang["twofactor_twofactor_authentication"] = "Uwierzytelnianie dwuskładnikowe";
$lang["twofactor_enable_twofactor_authentication"] = "Włącz uwierzytelnianie dwuskładnikowe";
$lang["twofactor_info_text"] = "Zanim się wylogujesz, otwórz nową przeglądarkę i upewnij się, że uwierzytelnianie dwuskładnikowe działa.";
$lang["twofactor_code"] = "Kod";
$lang["twofactor_code_expaired_message"] = "Kod dwuskładnikowy wygasł lub coś poszło nie tak.";
$lang["twofactor_code_message"] = "OTP został wysłany na twój e-mail. Proszę pobrać, aby kontynuować.";
$lang["twofactor_code_success_message"] = "Zalogowano pomyślnie. Przekierowanie do pulpitu nawigacyjnego...";
$lang["twofactor_continue"] = "Kontynuuj";
$lang["twofactor_not_you"] = "Nie ty?";
$lang["twofactor_restore_email_message_to_default"] = "Przywróć domyślne wiadomości e-mail";
$lang["twofactor_email_message_restored"] = "Wiadomość e-mail została przywrócona do wartości domyślnych!";

/* Version: 1.1 */

$lang["twofactor_method"] = "Metoda";
$lang["twofactor_send_otp"] = "Wyślij hasło jednorazowe";
$lang["twofactor_qr_code"] = "Kod QR";
$lang["twofactor_google_authenticator_help_message"] = "Zeskanuj ten kod QR za pomocą aplikacji Google Authenticator i wprowadź poniżej kod weryfikacyjny.";

$lang["twofactor_enable_sms"] = "Włącz SMS-y";
$lang["twofactor_twilio_account_sid"] = "ID konta";
$lang["twofactor_twilio_auth_token"] = "Token uwierzytelnienia";
$lang["twofactor_twilio_phone_number"] = "Numer telefonu Twilio";
$lang["twofactor_send_test_sms"] = "Wyślij testową wiadomość SMS";

$lang["twofactor_twilio_phone_no_help_message"] = "Numery telefonów muszą być w formacie %s. W przeciwnym razie on/ona nie otrzyma SMS-ów.";
$lang["twofactor_twilio_user_phone_no_help_message"] = "Użyj formatu %s (<b>+14155552671</b>) w numerze telefonu użytkownika/klienta. W przeciwnym razie SMS OTP nie będzie działać. Kliknij %s, aby dowiedzieć się więcej o numerach telefonów należy sformatować.";

$lang["twofactor_send_test_sms_successfull_message"] = "Testowy SMS został wysłany pomyślnie!";
$lang["twofactor_send_test_sms_error_message"] = "Błąd! Nie można połączyć się z Twilio przy użyciu poświadczeń.";

$lang["twofactor_here"] = "Tutaj";
$lang["twofactor_restore_template_to_default"] = "Przywróć szablon do ustawień domyślnych";

$lang["twofactor_code_message_email"] = "Na Twój e-mail wysłano hasło jednorazowe. Proszę je pobrać, aby kontynuować.";
$lang["twofactor_code_message_sms"] = "Na Twój telefon zostało wysłane hasło jednorazowe. Pobierz je, aby kontynuować.";
$lang["twofactor_code_message_google_authenticator"] = "Wprowadź 6-cyfrowy kod weryfikacyjny wygenerowany przez aplikację uwierzytelniającą.";

$lang["twofactor_enable_email_authentication_for_all"] = "Włącz uwierzytelnianie e-mail dla wszystkich";
$lang["twofactor_enable_email_authentication_for_all_help_message"] = "Jeśli włączysz uwierzytelnianie poczty e-mail dla wszystkich, zostanie ono zastosowane tylko do użytkowników, którzy nie mają włączonego żadnego uwierzytelniania dwuskładnikowego w swoim profilu osobistym.";

return $lang;
