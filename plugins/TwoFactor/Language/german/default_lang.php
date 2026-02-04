<?php

/* NOTE: DO NOT CHANGE THIS FILE. IF YOU WANT TO UPDATE THE LANGUAGE THEN COPY THIS FILE TO custom_lang.php AND UPDATE THERE */

$lang["twofactor"] = "zweifaktor";
$lang["twofactor_settings"] = "Zwei-Faktor-Einstellungen";
$lang["twofactor_email_subject"] = "E-Mail-Betreff";
$lang["twofactor_email_message"] = "E-Mail-Nachricht";
$lang["twofactor_twofactor_authentication"] = "Zwei-Faktor-Authentifizierung";
$lang["twofactor_enable_twofactor_authentication"] = "Zwei-Faktor-Authentifizierung aktivieren";
$lang["twofactor_info_text"] = "Bevor Sie sich abmelden, öffnen Sie bitte einen neuen Browser und stellen Sie sicher, dass die Zwei-Faktor-Authentifizierung funktioniert.";
$lang["twofactor_code"] = "Code";
$lang["twofactor_code_expaired_message"] = "Der Zwei-Faktor-Code ist abgelaufen oder etwas ist schief gelaufen.";
$lang["twofactor_code_message"] = "Ein OTP wurde an Ihre E-Mail gesendet. Bitte nehmen Sie es, um fortzufahren.";
$lang["twofactor_code_success_message"] = "Erfolgreich eingeloggt. Weiterleitung zum Dashboard...";
$lang["twofactor_continue"] = "Weiter";
$lang["twofactor_not_you"] = "Du nicht?";
$lang["twofactor_restore_email_message_to_default"] = "E-Mail-Nachricht auf Standard zurücksetzen";
$lang["twofactor_email_message_restored"] = "Die E-Mail-Nachricht wurde auf die Standardeinstellungen zurückgesetzt!";

/* Version: 1.1 */

$lang["twofactor_method"] = "Methode";
$lang["twofactor_send_otp"] = "OTP senden";
$lang["twofactor_qr_code"] = "QR-Code";
$lang["twofactor_google_authenticator_help_message"] = "Scannen Sie diesen QR-Code mit Ihrer Google Authenticator-App und geben Sie unten den Bestätigungscode ein.";

$lang["twofactor_enable_sms"] = "SMS aktivieren";
$lang["twofactor_twilio_account_sid"] = "Konto-SID";
$lang["twofactor_twilio_auth_token"] = "Auth-Token";
$lang["twofactor_twilio_phone_number"] = "Twilio-Telefonnummer";
$lang["twofactor_send_test_sms"] = "Test-SMS senden";

$lang["twofactor_twilio_phone_no_help_message"] = "Telefonnummern müssen im Format %s vorliegen. Andernfalls erhält er/sie keine SMS.";
$lang["twofactor_twilio_user_phone_no_help_message"] = "Bitte verwenden Sie das Format %s (<b>+14155552671</b>) in der Telefonnummer des Benutzers/Kunden. Andernfalls funktioniert das SMS-OTP nicht. Klicken Sie auf %s, um mehr über Telefonnummern zu erfahren sollte formatiert werden.";

$lang["twofactor_send_test_sms_successfull_message"] = "Test-SMS wurde erfolgreich gesendet!";
$lang["twofactor_send_test_sms_error_message"] = "Fehler! Mit den Anmeldeinformationen kann keine Verbindung zum Twilio hergestellt werden.";

$lang["twofactor_here"] = "Hier";
$lang["twofactor_restore_template_to_default"] = "Vorlage auf Standard wiederherstellen";

$lang["twofactor_code_message_email"] = "Ein OTP wurde an Ihre E-Mail-Adresse gesendet. Bitte greifen Sie darauf zu, um fortzufahren.";
$lang["twofactor_code_message_sms"] = "Ein OTP wurde an Ihr Telefon gesendet. Bitte greifen Sie darauf zu, um fortzufahren.";
$lang["twofactor_code_message_google_authenticator"] = "Bitte geben Sie den 6-stelligen Bestätigungscode ein, der von Ihrer Authentifizierungs-App generiert wurde.";

$lang["twofactor_enable_email_authentication_for_all"] = "E-Mail-Authentifizierung für alle aktivieren";
$lang["twofactor_enable_email_authentication_for_all_help_message"] = "Wenn Sie die E-Mail-Authentifizierung für alle aktivieren, wird sie nur auf Benutzer angewendet, deren persönliches Profil keine Zwei-Faktor-Authentifizierung aktiviert hat.";

return $lang;
