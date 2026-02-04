<?php

/* NOTE: DO NOT CHANGE THIS FILE. IF YOU WANT TO UPDATE THE LANGUAGE THEN COPY THIS FILE TO custom_lang.php AND UPDATE THERE */

$lang["twofactor"] = "Due fattori";
$lang["twofactor_settings"] = "Impostazioni a due fattori";
$lang["twofactor_email_subject"] = "Oggetto email";
$lang["twofactor_email_message"] = "Messaggio e-mail";
$lang["twofactor_twofactor_authentication"] = "Autenticazione a due fattori";
$lang["twofactor_enable_twofactor_authentication"] = "Abilita l'autenticazione a due fattori";
$lang["twofactor_info_text"] = "Prima di uscire, apri un nuovo browser e assicurati che l'autenticazione a due fattori funzioni.";
$lang["twofactor_code"] = "Codice";
$lang["twofactor_code_expaired_message"] = "Il codice a due fattori è scaduto o qualcosa è andato storto.";
$lang["twofactor_code_message"] = "Una OTP è stata inviata alla tua email. Prendila per continuare.";
$lang["twofactor_code_success_message"] = "Accesso effettuato con successo. Reindirizzamento alla dashboard...";
$lang["twofactor_continue"] = "Continua";
$lang["twofactor_not_you"] = "Non sei tu?";
$lang["twofactor_restore_email_message_to_default"] = "Ripristina il messaggio email ai valori predefiniti";
$lang["twofactor_email_message_restored"] = "Il messaggio email è stato ripristinato ai valori predefiniti!";

/* Version: 1.1 */

$lang["twofactor_method"] = "Metodo";
$lang["twofactor_send_otp"] = "Invia OTP";
$lang["twofactor_qr_code"] = "Codice QR";
$lang["twofactor_google_authenticator_help_message"] = "Scansiona questo codice QR con la tua app Google Authenticator e inserisci il codice di verifica qui sotto.";

$lang["twofactor_enable_sms"] = "Abilita SMS";
$lang["twofactor_twilio_account_sid"] = "SID account";
$lang["twofactor_twilio_auth_token"] = "Token di autenticazione";
$lang["twofactor_twilio_phone_number"] = "Numero di telefono Twilio";
$lang["twofactor_send_test_sms"] = "Invia SMS di prova";

$lang["twofactor_twilio_phone_no_help_message"] = "I numeri di telefono devono essere nel formato %s. Altrimenti non riceverà SMS.";
$lang["twofactor_twilio_user_phone_no_help_message"] = "Utilizza il formato %s (<b>+14155552671</b>) nel numero di telefono dell'utente/cliente. Altrimenti l'OTP SMS non funzionerà. Fai clic su %s per saperne di più su come numeri di telefono dovrebbe essere formattato.";

$lang["twofactor_send_test_sms_successfull_message"] = "L'SMS di prova è stato inviato con successo!";
$lang["twofactor_send_test_sms_error_message"] = "Errore! Impossibile connettersi a Twilio utilizzando le credenziali.";

$lang["twofactor_here"] = "Qui";
$lang["twofactor_restore_template_to_default"] = "Ripristina il modello predefinito";

$lang["twofactor_code_message_email"] = "Una OTP è stata inviata alla tua email. Prendila per continuare.";
$lang["twofactor_code_message_sms"] = "Una OTP è stata inviata al tuo telefono. Prendila per continuare.";
$lang["twofactor_code_message_google_authenticator"] = "Inserisci il codice di verifica a 6 cifre generato dalla tua app di autenticazione.";

$lang["twofactor_enable_email_authentication_for_all"] = "Abilita l'autenticazione email per tutti";
$lang["twofactor_enable_email_authentication_for_all_help_message"] = "Se abiliti l'autenticazione email per tutti, verrà applicata solo agli utenti che non hanno l'autenticazione a due fattori abilitata nel loro profilo personale.";

return $lang;
