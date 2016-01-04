<?php  
///////////////////////////////////////////////////////////////////////////
//                                                                       //
// NOTICE OF COPYRIGHT                                                   //
//                                                                       //
// Moodle - Modular Object-Oriented Dynamic Learning Environment         //
//          http://moodle.org                                            //
//                                                                       //
// Copyright (C) 2004  Martin Dougiamas  http://moodle.com               //
// Modifications (c) 2010 Antonio Duran Terres
//                                                                       //
// This program is free software; you can redistribute it and/or modify  //
// it under the terms of the GNU General Public License as published by  //
// the Free Software Foundation; either version 2 of the License, or     //
// (at your option) any later version.                                   //
//                                                                       //
// This program is distributed in the hope that it will be useful,       //
// but WITHOUT ANY WARRANTY; without even the implied warranty of        //
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the         //
// GNU General Public License for more details:                          //
//                                                                       //
//          http://www.gnu.org/copyleft/gpl.html                         //
//                                                                       //
///////////////////////////////////////////////////////////////////////////

require_once($CFG->dirroot.'/group/lib.php');

/**
* enrolment_plugin_joomdle
*
* Just shows a message with the indications for buying the course
*/

class enrolment_plugin_joomdle {

var $errormsg;

/**
* Prints the entry form/page for this enrolment
*
* This is only called from course/enrol.php
* Most plugins will probably override this to print payment
* forms etc, or even just a notice to say that manual enrolment
* is disabled
*
* @param    course  current course object
*/
function print_entry($course) {
    global $CFG, $USER, $SESSION, $THEME;

    $strloginto = get_string('loginto', '', $course->shortname);
    $strcourses = get_string('courses');

    $context = context_system::get_context();

    $navlinks = array();
    $navlinks[] = array('name' => $strcourses, 'link' => ".", 'type' => 'misc');
    $navlinks[] = array('name' => $strloginto, 'link' => null, 'type' => 'misc');
    $navigation = build_navigation($navlinks);

    // if we get here we are going to display the BUY COURSE message

    print_header($strloginto, $course->fullname, $navigation, "form.password");

    include("$CFG->dirroot/enrol/joomdle/enrol.html");

    print_footer();

}



/**
* The other half to print_entry, this checks the form data
*
* This function checks that the user has completed the task on the
* enrolment entry page and then enrolls them.
*
* @param    form    the form data submitted, as an object
* @param    course  the current course, as an object
*/
function check_entry($form, $course) {
    global $CFG, $USER, $SESSION, $THEME;

}


/**
* Prints a form for configuring the current enrolment plugin
*
* This function is called from admin/enrol.php, and outputs a
* full page with a form for defining the current enrolment plugin.
*
* @param    frm  an object containing all the data for this page
*/
function config_form($frm) {
    global $CFG;

   $paypalcurrencies = array(  'USD' => 'US Dollars',
                                'EUR' => 'Euros',
                                'JPY' => 'Japanese Yen',
                                'GBP' => 'British Pounds',
                                'CAD' => 'Canadian Dollars',
                                'AUD' => 'Australian Dollars',
								'CNY' => 'Renminbi'
                             );

    $vars = array('enrol_cost', 'enrol_currency', 'enrol_message');
    foreach ($vars as $var) {
        if (!isset($frm->$var)) {
            $frm->$var = '';
        }
    }

    include ("$CFG->dirroot/enrol/joomdle/config.html");
}


/**
* Processes and stored configuration data for the enrolment plugin
*
* @param    config  all the configuration data as entered by the admin
*/
function process_config($config) {

    $return = true;

    foreach ($config as $name => $value) {
        if (!set_config($name, $value)) {
            $return = false;
        }
    }

    return $return;
}



/**
* Returns the relevant icons for a course
*
* @param    course  the current course, as an object
*/
function get_access_icons($course) {
    global $CFG;

    global $strallowguests;
    global $strrequireskey;

    if (empty($strallowguests)) {
        $strallowguests = get_string('allowguests');
        $strrequireskey = get_string('requireskey');
    }

    $str = '';

    if (!empty($course->guest)) {
        $str .= '<a title="'.$strallowguests.'" href="'.$CFG->wwwroot.'/course/view.php?id='.$course->id.'">';
        $str .= '<img class="accessicon" alt="'.$strallowguests.'" src="'.$CFG->pixpath.'/i/guest.gif" /></a>&nbsp;&nbsp;';
    }
    if (!empty($course->password)) {
        $str .= '<a title="'.$strrequireskey.'" href="'.$CFG->wwwroot.'/course/view.php?id='.$course->id.'">';
        $str .= '<img class="accessicon" alt="'.$strrequireskey.'" src="'.$CFG->pixpath.'/i/key.gif" /></a>';
    }

    return $str;
}


} /// end of class

?>
