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
 * Paypal enrolments plugin settings and presets.
 *
 * @package    enrol
 * @subpackage paypal
 * @copyright  2010 Eugene Venter
 * @author     Eugene Venter - based on code by Petr Skoda and others
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

if ($ADMIN->fulltree) {


    //--- enrol instance defaults ----------------------------------------------------------------------------
    $settings->add(new admin_setting_heading('enrol_joomdle_defaults',
        get_string('enrolinstancedefaults', 'admin'), get_string('enrolinstancedefaults_desc', 'admin')));

    $options = array(ENROL_INSTANCE_ENABLED  => get_string('yes'),
                     ENROL_INSTANCE_DISABLED => get_string('no'));
    $settings->add(new admin_setting_configselect('enrol_joomdle/status',
        get_string('status', 'enrol_joomdle'), get_string('status_desc', 'enrol_joomdle'), ENROL_INSTANCE_DISABLED, $options));

    $settings->add(new admin_setting_configtext('enrol_joomdle/cost', get_string('cost', 'enrol_joomdle'), '', 0, PARAM_FLOAT, 4));

    $paypalcurrencies = array('USD' => 'US Dollars',
                              'EUR' => 'Euros',
                              'JPY' => 'Japanese Yen',
                              'GBP' => 'British Pounds',
                              'CAD' => 'Canadian Dollars',
                              'AUD' => 'Australian Dollars',
							  'CNY' => 'Renminbi'
                             );
    $settings->add(new admin_setting_configselect('enrol_joomdle/currency', get_string('currency', 'enrol_joomdle'), '', 'USD', $paypalcurrencies));

    $settings->add(new admin_setting_configtextarea('enrol_joomdle/enrol_message', get_string('enrol_message', 'enrol_joomdle'), '', 0, PARAM_RAW));

    $settings->add(new admin_setting_configtextarea('enrol_joomdle/guest_enrol_message', get_string('guest_enrol_message', 'enrol_joomdle'), '', 0, PARAM_RAW));

    if (!during_initial_install()) {
        $options = get_default_enrol_roles(context_system::instance());
        $student = get_archetype_roles('student');
        $student = reset($student);
        $settings->add(new admin_setting_configselect('enrol_joomdle/roleid',
            get_string('defaultrole', 'enrol_joomdle'), get_string('defaultrole_desc', 'enrol_joomdle'), $student->id, $options));
    }

    $settings->add(new admin_setting_configtext('enrol_joomdle/enrolperiod',
        get_string('enrolperiod', 'enrol_joomdle'), get_string('enrolperiod_desc', 'enrol_joomdle'), 0, PARAM_INT));
}
