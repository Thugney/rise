<?php

/* NOTE: DO NOT CHANGE THIS FILE. IF YOU WANT TO UPDATE THE LANGUAGE THEN COPY THIS FILE TO custom_lang.php AND UPDATE THERE */

$lang["twofactor"] = "Dvoufaktorový";
$lang["twofactor_settings"] = "Dvoufaktorové nastavení";
$lang["twofactor_email_subject"] = "Předmět e -mailu";
$lang["twofactor_email_message"] = "E -mailová zpráva";
$lang["twofactor_twofactor_authentication"] = "Dvoufaktorová autentizace";
$lang["twofactor_enable_twofactor_authentication"] = "Povolit dvoufaktorové ověřování";
$lang["twofactor_info_text"] = "Než se odhlásíte, otevřete si nový prohlížeč a zkontrolujte, zda funguje dvoufaktorové ověřování.";
$lang["twofactor_code"] = "Kód";
$lang["twofactor_code_expaired_message"] = "Platnost dvoufaktorového kódu vypršela nebo se něco pokazilo.";
$lang["twofactor_code_message"] = "Na váš e -mail bylo odesláno jednorázové heslo. Chcete -li pokračovat, chyťte ho.";
$lang["twofactor_code_success_message"] = "Úspěšně přihlášen. Přesměrování na hlavní panel ...";
$lang["twofactor_continue"] = "Pokračovat";
$lang["twofactor_not_you"] = "Nejste vy?";
$lang["twofactor_restore_email_message_to_default"] = "Obnovit výchozí e -mailové zprávy";
$lang["twofactor_email_message_restored"] = "E -mailová zpráva byla obnovena na výchozí!";

/* Version: 1.1 */

$lang["twofactor_method"] = "Metoda";
$lang["twofactor_send_otp"] = "Odeslat jednorázové heslo";
$lang["twofactor_qr_code"] = "QR kód";
$lang["twofactor_google_authenticator_help_message"] = "Naskenujte tento QR kód pomocí aplikace Google Authenticator a zadejte ověřovací kód níže.";

$lang["twofactor_enable_sms"] = "Povolit SMS";
$lang["twofactor_twilio_account_sid"] = "SID účtu";
$lang["twofactor_twilio_auth_token"] = "Auth Token";
$lang["twofactor_twilio_phone_number"] = "Twilio telefonní číslo";
$lang["twofactor_send_test_sms"] = "Odeslat testovací SMS";

$lang["twofactor_twilio_phone_no_help_message"] = "Telefonní čísla musí být ve formátu %s. Jinak nebude dostávat SMS.";
$lang["twofactor_twilio_user_phone_no_help_message"] = "V telefonním čísle uživatele/klienta použijte prosím formát %s (<b>+14155552671</b>). V opačném případě nebude jednorázové heslo SMS fungovat. Kliknutím na %s zobrazíte další informace o telefonních číslech by měl být naformátován.";

$lang["twofactor_send_test_sms_successfull_message"] = "Testovací SMS byla úspěšně odeslána!";
$lang["twofactor_send_test_sms_error_message"] = "Chyba! Nelze se připojit k Twilio pomocí přihlašovacích údajů.";

$lang["twofactor_here"] = "Zde";
$lang["twofactor_restore_template_to_default"] = "Obnovit šablonu na výchozí";

$lang["twofactor_code_message_email"] = "Na váš e-mail bylo odesláno jednorázové heslo. Chcete-li pokračovat, stáhněte si ho.";
$lang["twofactor_code_message_sms"] = "Do vašeho telefonu bylo odesláno jednorázové heslo. Chcete-li pokračovat, uchopte jej.";
$lang["twofactor_code_message_google_authenticator"] = "Zadejte 6místný ověřovací kód vygenerovaný vaší autentizační aplikací.";

$lang["twofactor_enable_email_authentication_for_all"] = "Povolit ověřování e-mailů pro všechny";
$lang["twofactor_enable_email_authentication_for_all_help_message"] = "Pokud povolíte e-mailové ověřování pro všechny, bude aplikováno pouze na uživatele, kteří nemají ve svém osobním profilu povoleno žádné dvoufaktorové ověřování.";

return $lang;
