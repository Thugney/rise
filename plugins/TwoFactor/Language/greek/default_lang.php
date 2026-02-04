<?php

/* NOTE: DO NOT CHANGE THIS FILE. IF YOU WANT TO UPDATE THE LANGUAGE THEN COPY THIS FILE TO custom_lang.php AND UPDATE THERE */

$lang["twofactor"] = "Δύο παράγοντες";
$lang["twofactor_settings"] = "Ρυθμίσεις δύο παραγόντων";
$lang["twofactor_email_subject"] = "Θέμα ηλεκτρονικού ταχυδρομείου";
$lang["twofactor_email_message"] = "Μήνυμα ηλεκτρονικού ταχυδρομείου";
$lang["twofactor_twofactor_authentication"] = "Έλεγχος ταυτότητας δύο παραγόντων";
$lang["twofactor_enable_twofactor_authentication"] = "Ενεργοποίηση ελέγχου ταυτότητας δύο παραγόντων";
$lang["twofactor_info_text"] = "Πριν αποσυνδεθείτε, ανοίξτε ένα νέο πρόγραμμα περιήγησης και βεβαιωθείτε ότι λειτουργεί ο έλεγχος ταυτότητας δύο παραγόντων.";
$lang["twofactor_code"] = "Κωδικός";
$lang["twofactor_code_expaired_message"] = "Ο κωδικός δύο παραγόντων έληξε ή κάτι πήγε στραβά.";
$lang["twofactor_code_message"] = "Έχει σταλεί ένα OTP στο email σας. Πιάστε το για να συνεχίσετε.";
$lang["twofactor_code_success_message"] = "Έχετε συνδεθεί επιτυχώς. Ανακατεύθυνση στον πίνακα ελέγχου ...";
$lang["twofactor_continue"] = "Συνέχεια";
$lang["twofactor_not_you"] = "Δεν είσαι εσύ;";
$lang["twofactor_restore_email_message_to_default"] = "Επαναφορά μηνύματος email στην προεπιλογή";
$lang["twofactor_email_message_restored"] = "Το μήνυμα ηλεκτρονικού ταχυδρομείου επανήλθε στην προεπιλογή!";

/* Version: 1.1 */

$lang["twofactor_method"] = "Μέθοδος";
$lang["twofactor_send_otp"] = "Αποστολή OTP";
$lang["twofactor_qr_code"] = "Κωδικός QR";
$lang["twofactor_google_authenticator_help_message"] = "Σαρώστε αυτόν τον κωδικό QR με την εφαρμογή Google Authenticator και εισαγάγετε τον κωδικό επαλήθευσης παρακάτω.";

$lang["twofactor_enable_sms"] = "Ενεργοποίηση SMS";
$lang["twofactor_twilio_account_sid"] = "SID λογαριασμού";
$lang["twofactor_twilio_auth_token"] = "Auth Token";
$lang["twofactor_twilio_phone_number"] = "Αριθμός τηλεφώνου Twilio";
$lang["twofactor_send_test_sms"] = "Αποστολή δοκιμαστικού SMS";

$lang["twofactor_twilio_phone_no_help_message"] = "Οι αριθμοί τηλεφώνου πρέπει να είναι σε μορφή %s. Διαφορετικά δεν θα λαμβάνει SMS.";
$lang["twofactor_twilio_user_phone_no_help_message"] = "Χρησιμοποιήστε τη μορφή %s (<b>+14155552671</b>) στον αριθμό τηλεφώνου χρήστη/πελάτη. Διαφορετικά το SMS OTP δεν θα λειτουργήσει. Κάντε κλικ στο %s για να διαβάσετε περισσότερα για το πώς οι αριθμοί τηλεφώνου πρέπει να μορφοποιηθεί.";

$lang["twofactor_send_test_sms_successfull_message"] = "Το SMS δοκιμής στάλθηκε με επιτυχία!";
$lang["twofactor_send_test_sms_error_message"] = "Σφάλμα! Δεν είναι δυνατή η σύνδεση με το Twilio χρησιμοποιώντας τα διαπιστευτήρια.";

$lang["twofactor_here"] = "Εδώ";
$lang["twofactor_restore_template_to_default"] = "Επαναφορά προτύπου στην προεπιλογή";

$lang["twofactor_code_message_email"] = "Έχει σταλεί ένα OTP στο email σας. Πιάστε το για να συνεχίσετε.";
$lang["twofactor_code_message_sms"] = "Έχει σταλεί ένα OTP στο τηλέφωνό σας. Πιάστε το για να συνεχίσετε.";
$lang["twofactor_code_message_google_authenticator"] = "Παρακαλώ εισάγετε τον 6ψήφιο κωδικό επαλήθευσης που δημιουργήθηκε από την εφαρμογή ελέγχου ταυτότητας.";

$lang["twofactor_enable_email_authentication_for_all"] = "Ενεργοποίηση ελέγχου ταυτότητας email για όλους";
$lang["twofactor_enable_email_authentication_for_all_help_message"] = "Εάν ενεργοποιήσετε τον έλεγχο ταυτότητας email για όλους, θα εφαρμοστεί μόνο σε χρήστες που δεν έχουν ενεργοποιημένο έλεγχο ταυτότητας δύο παραγόντων από το προσωπικό τους προφίλ.";

return $lang;
