<?php

/* NOTE: DO NOT CHANGE THIS FILE. IF YOU WANT TO UPDATE THE LANGUAGE THEN COPY THIS FILE TO custom_lang.php AND UPDATE THERE */

$lang["twofactor"] = "Dos factores";
$lang["twofactor_settings"] = "Configuración de dos factores";
$lang["twofactor_email_subject"] = "Asunto del correo electrónico";
$lang["twofactor_email_message"] = "Mensaje de correo electrónico";
$lang["twofactor_twofactor_authentication"] = "Autenticación de dos factores";
$lang["twofactor_enable_twofactor_authentication"] = "Habilitar la autenticación de dos factores";
$lang["twofactor_info_text"] = "Antes de cerrar la sesión, abra un nuevo navegador y asegúrese de que la autenticación de dos factores esté funcionando";
$lang["twofactor_code"] = "Código";
$lang["twofactor_code_expaired_message"] = "El código de dos factores ha expirado o algo salió mal.";
$lang["twofactor_code_message"] = "Se ha enviado una OTP a su correo electrónico. Tómela para continuar.";
$lang["twofactor_code_success_message"] = "Inicio de sesión exitoso. Redirigiendo al tablero ...";
$lang["twofactor_continue"] = "Continuar";
$lang["twofactor_not_you"] = "¿Tú no?";
$lang["twofactor_restore_email_message_to_default"] = "Restaurar el mensaje de correo electrónico a los valores predeterminados";
$lang["twofactor_email_message_restored"] = "¡El mensaje de correo electrónico ha sido restaurado a los valores predeterminados!";

/* Version: 1.1 */

$lang["twofactor_method"] = "Método";
$lang["twofactor_send_otp"] = "Enviar OTP";
$lang["twofactor_qr_code"] = "Código QR";
$lang["twofactor_google_authenticator_help_message"] = "Escanee este código QR con su aplicación Google Authenticator e ingrese el código de verificación a continuación.";

$lang["twofactor_enable_sms"] = "Habilitar SMS";
$lang["twofactor_twilio_account_sid"] = "SID de cuenta";
$lang["twofactor_twilio_auth_token"] = "Token de autenticación";
$lang["twofactor_twilio_phone_number"] = "Número de teléfono de Twilio";
$lang["twofactor_send_test_sms"] = "Enviar SMS de prueba";

$lang["twofactor_twilio_phone_no_help_message"] = "Los números de teléfono deben estar en formato %s. De lo contrario, no recibirá SMS.";
$lang["twofactor_twilio_user_phone_no_help_message"] = "Utilice el formato %s (<b>+14155552671</b>) en el número de teléfono del usuario/cliente. De lo contrario, la OTP de SMS no funcionará. Haga clic en %s para leer más sobre cómo números de teléfono debe ser formateado.";

$lang["twofactor_send_test_sms_successfull_message"] = "¡El SMS de prueba se envió correctamente!";
$lang["twofactor_send_test_sms_error_message"] = "¡Error! No se puede conectar con Twilio usando las credenciales.";

$lang["twofactor_here"] = "Aquí";
$lang["twofactor_restore_template_to_default"] = "Restaurar plantilla a los valores predeterminados";

$lang["twofactor_code_message_email"] = "Se ha enviado una OTP a su correo electrónico. Tómela para continuar.";
$lang["twofactor_code_message_sms"] = "Se ha enviado una OTP a su teléfono. Tómela para continuar.";
$lang["twofactor_code_message_google_authenticator"] = "Ingrese el código de verificación de 6 dígitos generado por su aplicación de autenticación.";

$lang["twofactor_enable_email_authentication_for_all"] = "Habilitar la autenticación de correo electrónico para todos";
$lang["twofactor_enable_email_authentication_for_all_help_message"] = "Si habilita la autenticación de correo electrónico para todos, solo se aplicará a los usuarios que no tengan habilitada la autenticación de dos factores en su perfil personal.";

return $lang;
