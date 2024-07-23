<?php
namespace local_regform\form;

defined('MOODLE_INTERNAL') || die();
require_once('../../config.php');
require_once($CFG->libdir.'/formslib.php');

class regform extends \moodleform {
    public function definition() {
        $mform = $this->_form;

        $mform->addElement('text', 'email', get_string('email', 'local_regform'));
        $mform->setType('email', PARAM_EMAIL);
        $mform->addRule('email', null, 'required', null, 'client');

        $mform->addElement('text', 'name', get_string('name', 'local_regform'));
        $mform->setType('name', PARAM_NOTAGS);
        $mform->addRule('name', null, 'required', null, 'client');

        $mform->addElement('text', 'surname', get_string('surname', 'local_regform'));
        $mform->setType('surname', PARAM_NOTAGS);
        $mform->addRule('surname', null, 'required', null, 'client');

        $countries = get_string_manager()->get_list_of_countries();
        $mform->addElement('select', 'country', get_string('country','local_regform'), $countries);
        $mform->setType('country', PARAM_ALPHA);
        $mform->addRule('country', null, 'required', null, 'client');

        $mform->addElement('text', 'mobile', get_string('mobile', 'local_regform'));
        $mform->setType('mobile', PARAM_NOTAGS);
        $mform->addRule('mobile', null, 'required', null, 'client');

        $this->add_action_buttons(true, get_string('submit', 'local_regform'));
    }

    public function validation($data, $files) {
        global $DB;
        $errors = parent::validation($data, $files);

        if (!validate_email($data['email'])) {
            $errors['email'] = get_string('invalidemail');
        }

        // Check if the email already exists in the user table.
        if ($DB->record_exists('user', ['email' => $data['email']])) {
            $errors['email'] = get_string('user_email_exists', 'local_regform');
        }

        return $errors;
    }
}
?>