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
 * Joomdle certificate viewer helper
 *
 * @package    auth_joomdle
 * @copyright  Mark Nelson <markn@moodle.com>
 * @copyright  2009 Qontori Pte Ltd  (changes for Joomdle integration)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


require_once('../../config.php');
require_once('../../mod/certificate/lib.php');
include '../../lib/pdflib.php';
include '../../auth/joomdle/auth.php';

$id = required_param('id', PARAM_INT);    // Course Module ID
$action = optional_param('action', '', PARAM_ALPHA);

if (! $cm = get_coursemodule_from_id('certificate', $id)) {
    error('Course Module ID was incorrect');
}
if (! $course = $DB->get_record('course', array('id'=> $cm->course))) {
    error('course is misconfigured');
}
if (! $certificate = $DB->get_record('certificate', array('id'=> $cm->instance))) {
    error('course module is incorrect');
}

$token         = optional_param('token',  '',  PARAM_TEXT);
$username = optional_param('username',   '',   PARAM_TEXT);
$username = strtolower ($username);

$auth = new auth_plugin_joomdle();
$logged = $auth->call_method ("confirmJoomlaSession", $username, $token);

if (!$logged)
	return;

$USER = get_complete_user_data('username', $username);
complete_user_login($USER);

require_login($course->id, true, $cm);
$context = context_module::instance($cm->id);
require_capability('mod/certificate:view', $context);


// log update
add_to_log($course->id, 'certificate', 'view', "view.php?id=$cm->id", $certificate->id, $cm->id);
$completion=new completion_info($course);
$completion->set_module_viewed($cm);

// Initialize $PAGE, compute blocks
$PAGE->set_url('/mod/certificate/view.php', array('id' => $cm->id));
$PAGE->set_context($context);
$PAGE->set_cm($cm);

// Create new certrecord
//certificate_prepare_issue($course, $USER, $certificate);
//$certrecord = certificate_get_issue($course, $USER, $certificate, $cm);


// Get previous certrecord
$sql = "SELECT MAX(timecreated) AS latest " .
       "FROM {certificate_issues} " .
       "WHERE userid = '$USER->id' ".
       "AND certificateid = '$certificate->id'";
if ($record = $DB->get_record_sql($sql)) {
    $latest = $record->latest;
}
$certrecord = $DB->get_record('certificate_issues', array('certificateid'=>$certificate->id, 'userid'=>$USER->id, 'timecreated'=>$latest));
$type = $certificate->certificatetype;

// Load some strings
$strreviewcertificate = get_string('reviewcertificate', 'certificate');
$strgetcertificate = get_string('getcertificate', 'certificate');
$strgrade = get_string('grade', 'certificate');
$strcoursegrade = get_string('coursegrade', 'certificate');
$strcredithours = get_string('credithours', 'certificate');
$filename = clean_filename($certificate->name.'.pdf');

// Load the specific certificatetype
require ("$CFG->dirroot/mod/certificate/type/$certificate->certificatetype/certificate.php");

/*
if ($certificate->reissuecert) { // Reissue certificate every time
    if (empty($action)) {
        view_header($course, $certificate, $cm);
        if ($attempts = certificate_get_attempts($certificate->id, $USER->id)) {
            echo certificate_print_attempts($certificate->id, $USER->id);
        }
        if ($certificate->delivery == 0)    {
            echo '<p align="center">'.get_string('openwindow', 'certificate').'</p>';
        } elseif ($certificate->delivery == 1)    {
            echo '<p align="center">'.get_string('opendownload', 'certificate').'</p>';
        } elseif ($certificate->delivery == 2)    {
            echo '<p align="center">'.get_string('openemail', 'certificate').'</p>';
        }

        echo '<center>';
        $link = new moodle_url('/mod/certificate/view.php?id='.$cm->id.'&action=get');
                    $linkname = $strgetcertificate;
        $button = new single_button($link, $linkname);
        $button->add_action(new popup_action('click', $link, 'view'.$cm->id, array('height' => 600, 'width' => 800)));
        echo $OUTPUT->render($button);
        echo '</center>';
        add_to_log($course->id, 'certificate', 'received', "view.php?id=$cm->id", $certificate->id, $cm->id);
        echo $OUTPUT->footer($course);
        exit;
    }
    certificate_issue($course, $certificate, $certrecord, $cm); // update certrecord as issued
} else if ($certrecord->certdate > 0) { // Review certificate
*/
    if (empty($action)) {
        view_header($course, $certificate, $cm);
        $link = new moodle_url('/mod/certificate/view.php?id='.$cm->id.'&action=get');
        echo '<p align="center">'.get_string('viewed', 'certificate').'<br />'.userdate($certrecord->certdate).'</p>';
        echo '<center>';
        $linkname = $strreviewcertificate;
        $button = new single_button($link, $linkname);
        $button->add_action(new popup_action('click', $link, 'view'.$cm->id, array('height' => 600, 'width' => 800)));
        echo $OUTPUT->render($button);
        echo '</center>';
        echo $OUTPUT->footer($course);
        exit;
    }
/*
} else if ($certrecord->certdate == 0) { //Create certificate
    if (empty($action)) {
        view_header($course, $certificate, $cm);
        if ($certificate->delivery == 0)    {
            echo '<p align="center">'.get_string('openwindow', 'certificate').'</p>';
        } elseif ($certificate->delivery == 1)    {
            echo '<p align="center">'.get_string('opendownload', 'certificate').'</p>';
        } elseif ($certificate->delivery == 2)    {
            echo '<p align="center">'.get_string('openemail', 'certificate').'</p>';
        }

        $link = new moodle_url('/mod/certificate/view.php?id='.$cm->id.'&action=get');
        echo '<center>';
        $linkname = $strgetcertificate;
        $button = new single_button($link, $linkname);
        $button->add_action(new popup_action('click', $link, 'view'.$cm->id, array('height' => 600, 'width' => 800)));
        echo $OUTPUT->render($button);
        echo '</center>';
        add_to_log($course->id, 'certificate', 'received', "view.php?id=$cm->id", $certificate->id, $cm->id);
        echo $OUTPUT->footer($course);
        exit;
    }
   // certificate_issue($course, $certificate, $certrecord, $cm); // update certrecord as issued
}
*/
if ($action) {
    // Output to pdf
    if ($certificate->savecert == 1){
        //pdf contents are now in $file_contents as a string
       $file_contents = $pdf->Output('', 'S');
       $filename = clean_filename($certificate->name.'.pdf');
       certificate_save_pdf($file_contents, $certrecord->id, $filename, $context->id);
    }
    if ($certificate->delivery == 0){
        $pdf->Output($filename, 'I');// open in browser
    } elseif ($certificate->delivery == 1){
        $pdf->Output($filename, 'D'); // force download when create
    } elseif ($certificate->delivery == 2){
        certificate_email_students($USER, $course, $certificate, $certrecord, $context);
        $pdf->Output($filename, 'I');// open in browser
        $pdf->Output('', 'S');// send
    }
}
