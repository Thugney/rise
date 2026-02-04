<?php

/* NOTE: DO NOT CHANGE THIS FILE. IF YOU WANT TO UPDATE THE LANGUAGE THEN COPY THIS FILE TO custom_lang.php AND UPDATE THERE */

$lang["twofactor"] = "Twee-factor";
$lang["twofactor_settings"] = "Twee-factor instellingen";
$lang["twofactor_email_subject"] = "E-mail onderwerp";
$lang["twofactor_email_message"] = "E-mailbericht";
$lang["twofactor_twofactor_authentication"] = "Twee-factor authenticatie";
$lang["twofactor_enable_twofactor_authentication"] = "Schakel twee-factor authenticatie in";
$lang["twofactor_info_text"] = "Voordat u uitlogt, opent u een nieuwe browser en controleert u of de twee-factor-authenticatie werkt.";
$lang["twofactor_code"] = "Code";
$lang["twofactor_code_expaired_message"] = "De tweefactorcode is verlopen of er is iets misgegaan.";
$lang["twofactor_code_message"] = "Er is een OTP naar je e-mailadres gestuurd. Pak die op om verder te gaan.";
$lang["twofactor_code_success_message"] = "Succesvol ingelogd. Omleiden naar het dashboard...";
$lang["twofactor_continue"] = "Doorgaan";
$lang["twofactor_not_you"] = "Niet jij?";
$lang["twofactor_restore_email_message_to_default"] = "Herstel e-mailbericht naar standaard";
$lang["twofactor_email_message_restored"] = "Het e-mailbericht is teruggezet naar de standaardwaarde!";

/* Version: 1.1 */

$lang["twofactor_method"] = "Methode";
$lang["twofactor_send_otp"] = "Verzend OTP";
$lang["twofactor_qr_code"] = "QR-code";
$lang["twofactor_google_authenticator_help_message"] = "Scan deze QR-code met uw Google Authenticator-app en voer de onderstaande verificatiecode in.";

$lang["twofactor_enable_sms"] = "Schakel SMS in";
$lang["twofactor_twilio_account_sid"] = "Account-SID";
$lang["twofactor_twilio_auth_token"] = "Authentificatietoken";
$lang["twofactor_twilio_phone_number"] = "Twilio-telefoonnummer";
$lang["twofactor_send_test_sms"] = "Verstuur test-SMS";

$lang["twofactor_twilio_phone_no_help_message"] = "Telefoonnummers moeten het formaat %s hebben. Anders ontvangt hij/zij geen SMS.";
$lang["twofactor_twilio_user_phone_no_help_message"] = "Gebruik het %s-formaat (<b>+14155552671</b>) in het telefoonnummer van de gebruiker/klant. Anders werkt de SMS OTP niet. Klik op %s om meer te lezen hoe telefoonnummers moet worden geformatteerd.";

$lang["twofactor_send_test_sms_successfull_message"] = "Test-SMS is succesvol verzonden!";
$lang["twofactor_send_test_sms_error_message"] = "Fout! Kan geen verbinding maken met de Twilio met behulp van de inloggegevens.";

$lang["twofactor_here"] = "Hier";
$lang["twofactor_restore_template_to_default"] = "Sjabloon terugzetten naar standaard";

$lang["twofactor_code_message_email"] = "Er is een OTP naar uw e-mailadres verzonden. Neem deze op om door te gaan.";
$lang["twofactor_code_message_sms"] = "Er is een OTP naar uw telefoon verzonden. Neem deze op om door te gaan.";
$lang["twofactor_code_message_google_authenticator"] = "Voer de 6-cijferige verificatiecode in die is gegenereerd door uw authenticator-app.";

$lang["twofactor_enable_email_authentication_for_all"] = "E-mailauthenticatie voor iedereen inschakelen";
$lang["twofactor_enable_email_authentication_for_all_help_message"] = "Als u e-mailauthenticatie voor iedereen inschakelt, wordt dit alleen toegepast op gebruikers die geen tweefactorauthenticatie hebben ingeschakeld via hun persoonlijke profiel.";

return $lang;
