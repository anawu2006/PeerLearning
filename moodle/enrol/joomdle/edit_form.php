<?php

// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Adds new instance of enrol_paypal to specified course
 * or edits current instance.
 *
 * @package    enrol
 * @subpackage paypal
 * @copyright  2010 Petr Skoda  {@link http://skodak.org}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir.'/formslib.php');

class enrol_joomdle_edit_form extends moodleform {

    function definition() {
        $mform = $this->_form;

        list($instance, $plugin, $context) = $this->_customdata;

        $mform->addElement('header', 'header', get_string('pluginname', 'enrol_joomdle'));

        $mform->addElement('text', 'name', get_string('custominstancename', 'enrol'));

        $options = array(ENROL_INSTANCE_ENABLED  => get_string('yes'),
                         ENROL_INSTANCE_DISABLED => get_string('no'));
        $mform->addElement('select', 'status', get_string('status', 'enrol_joomdle'), $options);
        $mform->setDefault('status', $plugin->get_config('status'));

        $mform->addElement('text', 'cost', get_string('cost', 'enrol_joomdle'), array('size'=>4));
        $mform->setDefault('cost', $plugin->get_config('cost'));

        $paypalcurrencies = array('USD' => 'US Dollars',
                                  'EUR' => 'Euros',
                                  'JPY' => 'Japanese Yen',
                                  'GBP' => 'British Pounds',
                                  'CAD' => 'Canadian Dollars',
                                  'AUD' => 'Australian Dollars'
                                 );
        $mform->addElement('select', 'currency', get_string('currency', 'enrol_joomdle'), $paypalcurrencies);
        $mform->setDefault('currency', $plugin->get_config('currency'));

        if ($instance->id) {
            $roles = get_default_enrol_roles($context, $instance->roleid);
        } else {
            $roles = get_default_enrol_roles($context, $plugin->get_config('roleid'));
        }
        $mform->addElement('select', 'roleid', get_string('assignrole', 'enrol_joomdle'), $roles);
        $mform->setDefault('roleid', $plugin->get_config('roleid'));


        $mform->addElement('duration', 'enrolperiod', get_string('enrolperiod', 'enrol_joomdle'), array('optional' => true, 'defaultunit' => 86400));
        $mform->setDefault('enrolperiod', $plugin->get_config('enrolperiod'));


        $mform->addElement('date_selector', 'enrolstartdate', get_string('enrolstartdate', 'enrol_joomdle'), array('optional' => true));
        $mform->setDefault('enrolstartdate', 0);


        $mform->addElement('date_selector', 'enrolenddate', get_string('enrolenddate', 'enrol_joomdle'), array('optional' => true));
        $mform->setDefault('enrolenddate', 0);

        $mform->addElement('textarea', 'customtext1', get_string('enrol_message', 'enrol_joomdle'), array('optional' => true, 'cols'=>'60', 'rows'=>'8'));
        $mform->setDefault('customtext1', $plugin->get_config('enrol_message'));

        $mform->addElement('editor', 'customtext2', get_string('guest_enrol_message', 'enrol_joomdle'), array('optional' => true, 'cols'=>'60', 'rows'=>'8', 'maxfiles'   => EDITOR_UNLIMITED_FILES));
		if ($this->_customdata[0]->customtext2)
			$a['text'] = $this->_customdata[0]->customtext2;
		else
			$a['text'] = $plugin->get_config('guest_enrol_message');
        $mform->setDefault('customtext2', $a);
		$mform->setType('customtext2', PARAM_RAW);

        $mform->addElement('hidden', 'id');
        $mform->addElement('hidden', 'courseid');

        $this->add_action_buttons(true, ($instance->id ? null : get_string('addinstance', 'enrol')));

        $this->set_data($instance);
    }

    function validation($data, $files) {
        global $DB, $CFG;
        $errors = parent::validation($data, $files);

        list($instance, $plugin, $context) = $this->_customdata;

        if ($data['status'] == ENROL_INSTANCE_ENABLED) {
            if (!empty($data['enrolenddate']) and $data['enrolenddate'] < $data['enrolstartdate']) {
                $errors['enrolenddate'] = get_string('enrolenddaterror', 'enrol_joomdle');
            }

            if (!is_numeric($data['cost'])) {
                $errors['cost'] = get_string('costerror', 'enrol_joomdle');

            }
        }

        return $errors;
    }
}
