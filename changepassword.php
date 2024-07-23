<?php
require_once('../../config.php');
require_once($CFG->libdir . '/formslib.php');
require_once($CFG->libdir . '/authlib.php');

global $DB, $CFG, $USER, $OUTPUT, $PAGE;

require_login();

$context = context_system::instance();
$PAGE->set_context($context);
$PAGE->set_url(new moodle_url('/local/regform/changepassword.php'));
$PAGE->set_title(get_string('changepassword', 'local_regform'));
$PAGE->set_heading(get_string('changepassword', 'local_regform'));



if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $newpassword = required_param('newpassword', PARAM_RAW);
    $confirmpassword = required_param('confirmpassword', PARAM_RAW);

    $errors = [];
    //chech if password is more than 8 chars
    if (strlen($newpassword) < 8) {
        $errors[] = get_string('passwordminlength', 'local_regform');
    }
    //check if passwords match
    if ($newpassword !== $confirmpassword) {
        $errors[] = get_string('passwordmismatch', 'local_regform');
    }

    if (empty($errors)) {
        // Update the user's password
        $hashedpassword = hash_internal_user_password($newpassword);
        $DB->set_field('user', 'password', $hashedpassword, ['id' => $USER->id]);

        // Remove the password change requirement.
        unset_user_preference('password_change_required', $USER->id);

        // Logout the user after password change.
        require_logout();
        // Redirect to login page with success message
        redirect(new moodle_url('/local/regform/login.php', ['password_changed' => 1]));
    } 
        
    
}
// Define the form
echo $OUTPUT->header();
echo '<form action="" method="POST">';
echo '<label for="newpassword">' . get_string('newpassword', 'local_regform') . '</label>';
echo '<input type="password" name="newpassword" required>';
echo '<label for="confirmpassword">' . get_string('confirmpassword', 'local_regform') . '</label>';
echo '<input type="password" name="confirmpassword" required>';
echo '<button type="submit">' . get_string('savechanges', 'local_regform') . '</button>';
echo '</form>';
if (!empty($errors)) {
    foreach ($errors as $error) {
        echo html_writer::div($error, 'error');
    }
}
echo $OUTPUT->footer();
?>