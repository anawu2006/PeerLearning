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
 * Joomdle simplecertificate viewer helper
 *
 * @package    auth_joomdle
 * @copyright Carlos Fonseca <carlos.alexandre@outlook.com>, Chardelle Busch, Mark Nelson <mark@moodle.com.au>
 * @copyright  2009 Qontori Pte Ltd
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


require_once('../../config.php');
require_once('../../mod/simplecertificate/lib.php');
require_once('../../mod/simplecertificate/locallib.php');
//include '../../lib/pdflib.php';
include '../../auth/joomdle/auth.php';

$id = required_param('id', PARAM_INT);    // Course Module ID
$action = optional_param('action', '', PARAM_ALPHA);

if (! $cm = get_coursemodule_from_id('simplecertificate', $id)) {
    error('Course Module ID was incorrect');
}
if (! $course = $DB->get_record('course', array('id'=> $cm->course))) {
    error('course is misconfigured');
}
if (! $certificate = $DB->get_record('simplecertificate', array('id'=> $cm->instance))) {
    error('course module is incorrect');
}

$token         = optional_param('token',  '',  PARAM_TEXT);
$tab = optional_param('tab', simplecertificate::DEFAULT_VIEW, PARAM_INT);
$sort = optional_param('sort', '', PARAM_RAW);
$type = optional_param('type', '', PARAM_ALPHA);
$page = optional_param('page', 0, PARAM_INT);
$perpage = optional_param('perpage', get_config('simplecertificate', 'perpage'), PARAM_INT);
$issuelist = optional_param('issuelist', null, PARAM_ALPHA);
$username = optional_param('username',   '',   PARAM_TEXT);

$username = strtolower ($username);

$auth = new auth_plugin_joomdle();
$logged = $auth->call_method ("confirmJoomlaSession", $username, $token);

if (!$logged)
	return;

$USER = get_complete_user_data('username', $username);
complete_user_login($USER);

$context = context_module::instance($cm->id);
require_capability('mod/simplecertificate:view', $context);
$canmanage = has_capability('mod/simplecertificate:manage', $context);



$url = new moodle_url('/mod/simplecertificate/view.php',
                    array('id' => $cm->id, 'tab' => $tab, 'page' => $page, 'perpage' => $perpage));

if ($type) {
    $url->param('type', $type);
}

if ($sort) {
    $url->param('sort', $sort);
}

if ($action) {
    $url->param('action', $action);
}

if ($issuelist) {
    $url->param('issuelist', $issuelist);
}



// Create certificate object
$simplecertificate = new simplecertificate($certificate, $context);

// Mark completion as view
$completion = new completion_info($course);
$completion->set_module_viewed($cm);

// Initialize $PAGE, compute blocks
$PAGE->set_url($url);
$PAGE->set_context($context);
$PAGE->set_cm($cm);
$PAGE->set_title(format_string($certificate->name));
$PAGE->set_heading(format_string($course->fullname));

require_login($course->id, false, $cm);

switch ($tab) {
    case $simplecertificate::ISSUED_CERTIFCADES_VIEW:
        $simplecertificate->view_issued_certificates($url);
    break;

    case $simplecertificate::BULK_ISSUE_CERTIFCADES_VIEW:
        $simplecertificate->view_bulk_certificates($url, $selectedusers);
    break;

    default:
        $simplecertificate->view_default($url, $canmanage);
    break;
}
