<?php

/* NOTE: DO NOT CHANGE THIS FILE. IF YOU WANT TO UPDATE THE LANGUAGE THEN COPY THIS FILE TO custom_lang.php AND UPDATE THERE */

$lang["twofactor"] = "Deux facteurs";
$lang["twofactor_settings"] = "Paramètres à deux facteurs";
$lang["twofactor_email_subject"] = "Objet de l'e-mail";
$lang["twofactor_email_message"] = "E-mail";
$lang["twofactor_twofactor_authentication"] = "Authentification à deux facteurs";
$lang["twofactor_enable_twofactor_authentication"] = "Activer l'authentification à deux facteurs";
$lang["twofactor_info_text"] = "Avant de vous déconnecter, veuillez ouvrir un nouveau navigateur et vous assurer que l'authentification à deux facteurs fonctionne.";
$lang["twofactor_code"] = "Code";
$lang["twofactor_code_expaired_message"] = "Le code à deux facteurs a expiré ou quelque chose s'est mal passé.";
$lang["twofactor_code_message"] = "Un OTP a été envoyé à votre e-mail. Veuillez le saisir pour continuer.";
$lang["twofactor_code_success_message"] = "Connecté avec succès. Redirection vers le tableau de bord...";
$lang["twofactor_continue"] = "Continuer";
$lang["twofactor_not_you"] = "Pas vous ?";
$lang["twofactor_restore_email_message_to_default"] = "Restaurer l'e-mail par défaut";
$lang["twofactor_email_message_restored"] = "Le message électronique a été restauré par défaut !";

/* Version: 1.1 */

$lang["twofactor_method"] = "Méthode";
$lang["twofactor_send_otp"] = "Envoyer OTP";
$lang["twofactor_qr_code"] = "Code QR";
$lang["twofactor_google_authenticator_help_message"] = "Scannez ce code QR avec votre application Google Authenticator et saisissez le code de vérification ci-dessous.";

$lang["twofactor_enable_sms"] = "Activer les SMS";
$lang["twofactor_twilio_account_sid"] = "SID du compte";
$lang["twofactor_twilio_auth_token"] = "Jeton d'authentification";
$lang["twofactor_twilio_phone_number"] = "Numéro de téléphone Twilio";
$lang["twofactor_send_test_sms"] = "Envoyer un SMS de test";

$lang["twofactor_twilio_phone_no_help_message"] = "Les numéros de téléphone doivent être au format %s. Sinon, il ne recevra pas de SMS.";
$lang["twofactor_twilio_user_phone_no_help_message"] = "Veuillez utiliser le format %s (<b>+14155552671</b>) dans le numéro de téléphone de l'utilisateur/client. Sinon, l'OTP SMS ne fonctionnera pas. Cliquez sur %s pour en savoir plus sur les numéros de téléphone. doit être formaté.";

$lang["twofactor_send_test_sms_successfull_message"] = "Le SMS de test a été envoyé avec succès !";
$lang["twofactor_send_test_sms_error_message"] = "Erreur ! Impossible de se connecter à Twilio en utilisant les informations d'identification.";

$lang["twofactor_here"] = "Ici";
$lang["twofactor_restore_template_to_default"] = "Restaurer le modèle par défaut";

$lang["twofactor_code_message_email"] = "Un OTP a été envoyé à votre adresse e-mail. Veuillez le récupérer pour continuer.";
$lang["twofactor_code_message_sms"] = "Un OTP a été envoyé sur votre téléphone. Veuillez le récupérer pour continuer.";
$lang["twofactor_code_message_google_authenticator"] = "Veuillez saisir le code de vérification à 6 chiffres généré par votre application d'authentification.";

$lang["twofactor_enable_email_authentication_for_all"] = "Activer l'authentification par courrier électronique pour tous";
$lang["twofactor_enable_email_authentication_for_all_help_message"] = "Si vous activez l'authentification par courrier électronique pour tous, elle ne sera appliquée qu'aux utilisateurs pour lesquels aucune authentification à deux facteurs n'est activée dans leur profil personnel.";

return $lang;
