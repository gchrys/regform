<?php
require_once('../../config.php');
require_once($CFG->libdir . '/moodlelib.php');
require_once($CFG->libdir . '/formslib.php');
require_once($CFG->dirroot . '/user/lib.php');

global $DB, $CFG, $USER;

$context = context_system::instance();
$PAGE->set_context($context);
$PAGE->set_url(new moodle_url('/local/regform/index.php'));
$PAGE->set_title(get_string('pluginname', 'local_regform'));
$PAGE->set_heading(get_string('pluginname', 'local_regform'));


$mform = new \local_regform\form\regform();
$message = '';

if ($mform->is_cancelled()) {
    // Form cancel redirects to home
    redirect(new moodle_url('/'));
} else if ($data = $mform->get_data()) {
    
    $temp_password = generate_temp_password(); // Generate temp password
    $user = new stdClass();
    $user->username = $data->email;
    $user->email = $data->email;
    $user->firstname = $data->name;
    $user->lastname = $data->surname;
    $user->phone1 = $data->mobile;
    $user->country = $data->country;
    $user->firstnamephonetic = $data->name; //required for email send
    $user->lastnamephonetic = $data->surname; //required for email send
    $user->middlename = ''; //required for email send
    $user->alternatename = ''; //required for email send
    $user->auth = 'manual';
    $user->confirmed = 1;
    $user->mnethostid = $CFG->mnet_localhost_id;
    $user->password = hash_internal_user_password($temp_password);
    //create user in db
    $user_id = user_create_user($user, false, false);
    $user->id=$user_id;

    // set user preference to indicate that the password needs to be changed.
    set_user_preference('password_change_required', 1, $user_id);

    // Send the email with the temp password.
    $email_subject = get_string('welcome_email_subject', 'local_regform');
    $email_body = get_string('welcome_email_body', 'local_regform', $temp_password);
    $email_success = email_to_user($user, get_admin(), $email_subject, $email_body);

    if ($email_success) {
        // Redirect to login.
        redirect(new moodle_url('/local/regform/login.php'));
    } else {
        // Email sending failure error
        $message= get_string('email_send_failed', 'local_regform');
    }
    
}

echo $OUTPUT->header();
if ($message) { echo $message; }
$mform->display();
echo $OUTPUT->footer();


function generate_temp_password($length = 8) {
    $characters = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
    $charactersLength = strlen($characters);
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, $charactersLength - 1)];
    }
    return $randomString;
}
?>