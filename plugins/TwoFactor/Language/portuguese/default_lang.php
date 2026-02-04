<?php

/* NOTE: DO NOT CHANGE THIS FILE. IF YOU WANT TO UPDATE THE LANGUAGE THEN COPY THIS FILE TO custom_lang.php AND UPDATE THERE */

$lang["twofactor"] = "Dois fatores";
$lang["twofactor_settings"] = "Configurações de dois fatores";
$lang["twofactor_email_subject"] = "Assunto do email";
$lang["twofactor_email_message"] = "Mensagem de e-mail";
$lang["twofactor_twofactor_authentication"] = "Autenticação de dois fatores";
$lang["twofactor_enable_twofactor_authentication"] = "Habilitar autenticação de dois fatores";
$lang["twofactor_info_text"] = "Antes de sair, abra um novo navegador e certifique-se de que a autenticação de dois fatores está funcionando.";
$lang["twofactor_code"] = "Código";
$lang["twofactor_code_expair_message"] = "O código de dois fatores expirou ou algo deu errado.";
$lang["twofactor_code_message"] = "Um OTP foi enviado para o seu e-mail. Por favor, pegue-o para continuar.";
$lang["twofactor_code_success_message"] = "Conectado com sucesso. Redirecionando para o painel ...";
$lang["twofactor_continue"] = "Continuar";
$lang["twofactor_not_you"] = "Não é você?";
$lang["twofactor_restore_email_message_to_default"] = "Restaurar mensagem de e-mail para o padrão";
$lang["twofactor_email_message_restored"] = "A mensagem de e-mail foi restaurada ao padrão!";

/* Version: 1.1 */

$lang["twofactor_method"] = "Método";
$lang["twofactor_send_otp"] = "Enviar OTP";
$lang["twofactor_qr_code"] = "Código QR";
$lang["twofactor_google_authenticator_help_message"] = "Leia este código QR com seu aplicativo Google Authenticator e insira o código de verificação abaixo.";

$lang["twofactor_enable_sms"] = "Ativar SMS";
$lang["twofactor_twilio_account_sid"] = "SID da conta";
$lang["twofactor_twilio_auth_token"] = "Token de autenticação";
$lang["twofactor_twilio_phone_number"] = "Número de telefone do Twilio";
$lang["twofactor_send_test_sms"] = "Enviar SMS de teste";

$lang["twofactor_twilio_phone_no_help_message"] = "Os números de telefone devem estar no formato %s. Caso contrário ele/ela não receberá SMS.";
$lang["twofactor_twilio_user_phone_no_help_message"] = "Por favor, use o formato %s (<b>+14155552671</b>) no número de telefone do usuário/cliente. Caso contrário, o SMS OTP não funcionará. Clique em %s para ler mais como números de telefone deve ser formatado.";

$lang["twofactor_send_test_sms_successfull_message"] = "O SMS de teste foi enviado com sucesso!";
$lang["twofactor_send_test_sms_error_message"] = "Erro! Não é possível conectar-se ao Twilio usando as credenciais.";

$lang["twofactor_here"] = "Aqui";
$lang["twofactor_restore_template_to_default"] = "Restaurar modelo para padrão";

$lang["twofactor_code_message_email"] = "Uma OTP foi enviada para seu e-mail. Por favor, pegue-a para continuar.";
$lang["twofactor_code_message_sms"] = "Uma OTP foi enviada para o seu telefone. Por favor, pegue-a para continuar.";
$lang["twofactor_code_message_google_authenticator"] = "Por favor, insira o código de verificação de 6 dígitos gerado pelo seu aplicativo autenticador.";

$lang["twofactor_enable_email_authentication_for_all"] = "Habilitar autenticação de e-mail para todos";
$lang["twofactor_enable_email_authentication_for_all_help_message"] = "Se você habilitar a autenticação de e-mail para todos, ela só será aplicada a usuários que não tenham nenhuma autenticação de dois fatores habilitada em seu perfil pessoal.";

return $lang;
