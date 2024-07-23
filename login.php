<?php
require_once('../../config.php');
require_once($CFG->libdir . '/authlib.php');
require_once($CFG->libdir . '/weblib.php');

global $DB, $CFG, $USER, $OUTPUT, $PAGE;

$context = context_system::instance();
$PAGE->set_context($context);
$PAGE->set_url(new moodle_url('/local/regform/login.php'));
$PAGE->set_title(get_string('pluginname', 'local_regform'));
$PAGE->set_heading(get_string('login', 'local_regform'));

$message = '';

// Check if redirected from changepassword
$password_changed = optional_param('password_changed', 0, PARAM_BOOL);
if ($password_changed) {
    $message = get_string('password_changed', 'local_regform');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = required_param('email', PARAM_EMAIL);
    $password = required_param('password', PARAM_RAW);

    // Authenticate user
    $user = authenticate_user_login($email, $password);

    if ($user) {
        // Valid user, log in.
        complete_user_login($user);
        // Check if the user needs to change their password.
        if (get_user_preferences('password_change_required', 0, $user->id)) {
            // Redirect to change password page.
            redirect(new moodle_url('/local/regform/changepassword.php'));
        } else {
            // Redirect to the dashboard.
            redirect(new moodle_url('/my'));
        }
    } else {
        // Invalid login error message
        $message = get_string('invalidlogin', 'local_regform');
    }
}

echo $OUTPUT->header();

if (!empty($message)) {
    echo $message;
}

echo '<form action="" method="POST">';
echo '<label for="email">' . get_string('email', 'local_regform') . '</label>';
echo '<input type="email" name="email" required>';
echo '<label for="password">' . get_string('password', 'local_regform') . '</label>';
echo '<input type="password" name="password" required>';
echo '<button type="submit">' . get_string('login', 'local_regform') . '</button>';
echo '</form>';

echo $OUTPUT->footer();
?>