<?php

/* NOTE: DO NOT CHANGE THIS FILE. IF YOU WANT TO UPDATE THE LANGUAGE THEN COPY THIS FILE TO custom_lang.php AND UPDATE THERE */

$lang["twofactor"] = "To-faktor";
$lang["twofactor_settings"] = "To-faktor innstillinger";
$lang["twofactor_email_subject"] = "Emne på e -post";
$lang["twofactor_email_message"] = "E -postmelding";
$lang["twofactor_twofactor_authentication"] = "To-faktor autentisering";
$lang["twofactor_enable_twofactor_authentication"] = "Aktiver tofaktorautentisering";
$lang["twofactor_info_text"] = "Før du logger ut, må du åpne en ny nettleser og kontrollere at tofaktorautentiseringen fungerer.";
$lang["twofactor_code"] = "Kode";
$lang["twofactor_code_expaired_message"] = "To-faktor koden er utløpt eller noe gikk galt.";
$lang["twofactor_code_message"] = "En OTP har blitt sendt til e -posten din. Ta det for å fortsette.";
$lang["twofactor_code_success_message"] = "Logget inn vellykket. Viderekobler til dashbordet ...";
$lang["twofactor_continue"] = "Fortsett";
$lang["twofactor_not_you"] = "Ikke du?";
$lang["twofactor_restore_email_message_to_default"] = "Gjenopprett e -post til standard";
$lang["twofactor_email_message_restored"] = "E -postmeldingen er gjenopprettet til standard!";

/* Version: 1.1 */

$lang["twofactor_method"] = "Metode";
$lang["twofactor_send_otp"] = "Send OTP";
$lang["twofactor_qr_code"] = "QR-kode";
$lang["twofactor_google_authenticator_help_message"] = "Skann denne QR-koden med Google Authenticator-appen og skriv inn bekreftelseskoden nedenfor.";

$lang["twofactor_enable_sms"] = "Aktiver SMS";
$lang["twofactor_twilio_account_sid"] = "Konto SID";
$lang["twofactor_twilio_auth_token"] = "Godkjenningstoken";
$lang["twofactor_twilio_phone_number"] = "Twilio-telefonnummer";
$lang["twofactor_send_test_sms"] = "Send test-SMS";

$lang["twofactor_twilio_phone_no_help_message"] = "Telefonnumre må være i formatet %s. Ellers vil han/hun ikke motta SMS.";
$lang["twofactor_twilio_user_phone_no_help_message"] = "Vennligst bruk %s-format (<b>+14155552671</b>) i bruker-/klienttelefonnummer. Ellers vil ikke SMS OTP fungere. Klikk %s for å lese mer hvordan telefonnumre skal formateres.";

$lang["twofactor_send_test_sms_successfull_message"] = "Test SMS har blitt sendt!";
$lang["twofactor_send_test_sms_error_message"] = "Feil! Kan ikke koble til Twilio ved å bruke legitimasjonen.";

$lang["twofactor_here"] = "Her";
$lang["twofactor_restore_template_to_default"] = "Gjenopprett mal til standard";

$lang["twofactor_code_message_email"] = "En OTP har blitt sendt til din e-post. Vennligst ta tak i den for å fortsette.";
$lang["twofactor_code_message_sms"] = "En OTP har blitt sendt til telefonen din. Vennligst ta tak i den for å fortsette.";
$lang["twofactor_code_message_google_authenticator"] = "Vennligst skriv inn den 6-sifrede bekreftelseskoden generert av autentiseringsappen.";

$lang["twofactor_enable_email_authentication_for_all"] = "Aktiver e-postautentisering for alle";
$lang["twofactor_enable_email_authentication_for_all_help_message"] = "Hvis du aktiverer e-postautentisering for alle, vil det bare bli brukt på brukere som ikke har noen tofaktorautentisering aktivert fra deres personlige profil.";

return $lang;
