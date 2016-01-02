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
 * Joomdle event handlers
 *
 * @package    auth_joomdle
 * @copyright  2009 Qontori Pte Ltd
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot . '/auth/joomdle/auth.php');


/**
 * Event handler for joomdle auth plugin.
 */
class auth_joomdle_handler {

    public static function user_created (\core\event\user_created $event)
	{
		global $CFG, $DB;

        $sync_to_joomla = get_config('auth/joomdle', 'sync_to_joomla');
		if (!$sync_to_joomla)
			return true;

		$user = $event->get_record_snapshot('user', $event->objectid);

        if ($user->auth != 'joomdle')
            return true;

        $auth_joomdle = new auth_plugin_joomdle ();

		/*
        $password_clear = '';
        if (array_key_exists ('password', $_POST))
            $password_clear =  $_POST['password']; //Self registration
        if ((array_key_exists ('newpassword', $_POST)) && (!$password_clear))
            $password_clear =  $_POST['newpassword']; //admin form
		*/

        /* Create user in Joomla */
        $userinfo['username'] = $user->username;
		/*
        $userinfo['password'] = $password_clear;
        $userinfo['password2'] = $password_clear;
		*/
        $userinfo['password'] = $user->password;
        $userinfo['password2'] = $user->password;

        $userinfo['name'] = $user->firstname. " " . $user->lastname;
        $userinfo['email'] = $user->email;
        $userinfo['firstname'] = $user->firstname;
        $userinfo['lastname'] = $user->lastname;
        $userinfo['city'] = $user->city;
        $userinfo['country'] = $user->country;
        $userinfo['lang'] = $user->lang;
        $userinfo['timezone'] = $user->timezone;
        $userinfo['phone1'] = $user->phone1;
        $userinfo['phone2'] = $user->phone2;
        $userinfo['address'] = $user->address;
        $userinfo['description'] = $user->description;
        $userinfo['institution'] = $user->institution;
        $userinfo['url'] = $user->url;
        $userinfo['icq'] = $user->icq;
        $userinfo['skype'] = $user->skype;
        $userinfo['aim'] = $user->aim;
        $userinfo['yahoo'] = $user->yahoo;
        $userinfo['msn'] = $user->msn;
        $userinfo['idnumber'] = $user->idnumber;
        $userinfo['department'] = $user->department;
        $userinfo['picture'] = $user->picture;
        $userinfo['lastnamephonetic'] = $user->lastnamephonetic;
        $userinfo['firstnamephonetic'] = $user->firstnamephonetic;
        $userinfo['middlename'] = $user->middlename;
        $userinfo['alternatename'] = $user->alternatename;

        $id = $user->id;
        $usercontext = context_user::instance($id);
        $context_id = $usercontext->id;

		if ($user->picture)
            $userinfo['pic_url'] = $CFG->wwwroot."/pluginfile.php/$context_id/user/icon/f1";

        $userinfo['block'] = 0;

        /* Custom fields */
        $query = "SELECT f.id, d.data 
                    FROM {$CFG->prefix}user_info_field as f, {$CFG->prefix}user_info_data d 
                    WHERE f.id=d.fieldid and userid = ?";

        $params = array ($id);
        $records =  $DB->get_records_sql($query, $params);

        $i = 0;
        $userinfo['custom_fields'] = array ();
        foreach ($records as $field)
        {
            $userinfo['custom_fields'][$i]['id'] = $field->id;
            $userinfo['custom_fields'][$i]['data'] = $field->data;
            $i++;
        }

        $auth_joomdle->call_method ("createUser", $userinfo);

        return true;

	}


    public static function user_updated (\core\event\user_updated $event)
	{
		global $CFG, $DB;

        $sync_to_joomla = get_config('auth/joomdle', 'sync_to_joomla');
		if (!$sync_to_joomla)
			return true;

		$user = $event->get_record_snapshot('user', $event->objectid);

		if ($user->auth != 'joomdle')
				return true;

		$auth_joomdle = new auth_plugin_joomdle ();

		/* Update user info in Joomla */
		$userinfo['username'] = $user->username;
		$userinfo['name'] = $user->firstname. " " . $user->lastname;
		$userinfo['email'] = $user->email;
		$userinfo['firstname'] =  $user->firstname;
		$userinfo['lastname'] = $user->lastname;
		$userinfo['city'] = $user->city;
		$userinfo['country'] = $user->country;
		$userinfo['lang'] = $user->lang;
		$userinfo['timezone'] = $user->timezone;
		$userinfo['phone1'] = $user->phone1;
		$userinfo['phone2'] = $user->phone2;
		$userinfo['address'] = $user->address;
		$userinfo['description'] = $user->description;
		$userinfo['institution'] = $user->institution;
		$userinfo['url'] = $user->url;
		$userinfo['icq'] = $user->icq;
		$userinfo['skype'] = $user->skype;
		$userinfo['aim'] = $user->aim;
		$userinfo['yahoo'] = $user->yahoo;
		$userinfo['msn'] = $user->msn;
		$userinfo['idnumber'] = $user->idnumber;
		$userinfo['department'] = $user->department;
		$userinfo['picture'] = $user->picture;
		$userinfo['lastnamephonetic'] = $user->lastnamephonetic;
		$userinfo['firstnamephonetic'] = $user->firstnamephonetic;
		$userinfo['middlename'] = $user->middlename;
		$userinfo['alternatename'] = $user->alternatename;

		$id = $user->id;
		$usercontext = context_user::instance($id);
		$context_id = $usercontext->id;

		if ($user->picture)
			$userinfo['pic_url'] = $CFG->wwwroot."/pluginfile.php/$context_id/user/icon/f1";

		$userinfo['block'] = 0;

		/* Custom fields */
		$query = "SELECT f.id, d.data 
					FROM {$CFG->prefix}user_info_field as f, {$CFG->prefix}user_info_data d 
					WHERE f.id=d.fieldid and userid = ?";

		$params = array ($id);
		$records =  $DB->get_records_sql($query, $params);

		$i = 0;
		$userinfo['custom_fields'] = array ();
		foreach ($records as $field)
		{
			$userinfo['custom_fields'][$i]['id'] = $field->id;
			$userinfo['custom_fields'][$i]['data'] = $field->data;
			$i++;
		}

		$auth_joomdle->call_method ("updateUser", $userinfo);

		return true;
    }

    public static function user_deleted (\core\event\user_deleted $event)
	{
        global $CFG, $DB;

        $sync_to_joomla = get_config('auth/joomdle', 'sync_to_joomla');
		if (!$sync_to_joomla)
			return true;

		$user = $event->get_record_snapshot('user', $event->objectid);

        if ($user->auth != 'joomdle')
            return true;

        $auth_joomdle = new auth_plugin_joomdle ();

        $auth_joomdle->call_method ("deleteUser", $user->username);
        return true;
	}

    public static function course_created (\core\event\course_created $event)
	{
        global $CFG, $DB;

		$course = $event->get_record_snapshot('course', $event->objectid);

        $activities = get_config('auth/joomdle', 'jomsocial_activities');
        $groups = get_config('auth/joomdle', 'jomsocial_groups');
        $autosell = get_config('auth/joomdle', 'auto_sell');
        $joomla_user_groups = get_config('auth/joomdle', 'joomla_user_groups');
        $use_kunena_forums = get_config('auth/joomdle', 'use_kunena_forums');

        $auth_joomdle = new auth_plugin_joomdle ();

        /* kludge for the call_method fn to work */
        if (!$course->summary)
            $course->summary = ' ';

        $conditions = array ('id' => $course->category);
        $cat = $DB->get_record('course_categories',$conditions);

        $context = context_course::instance($course->id);
        $course->summary = file_rewrite_pluginfile_urls ($course->summary, 'pluginfile.php', $context->id, 'course', 'summary', NULL);
        $course->summary = str_replace ('pluginfile.php', '/auth/joomdle/pluginfile_joomdle.php', $course->summary);
        if ($activities)
            $auth_joomdle->call_method ('addActivityCourse', (int) $course->id, $course->fullname,  $course->summary, (int) $course->category, $cat->name);
        if ($groups)
            $auth_joomdle->call_method ('addSocialGroup', $course->fullname,  get_string('auth_joomla_group_for_course', 'auth_joomdle') . ' ' .$course->fullname,  (int) $course->id);

        if ($autosell)
        {
            $cid = array ($course->id);
            $auth_joomdle->call_method ("sellCourse", array ((int) $course->id));
        }

        if ($joomla_user_groups)
        {
            $auth_joomdle->call_method ('addUserGroups', (int) $course->id, $course->fullname);
        }

        if ($use_kunena_forums)
        {
            // Create section
            $auth_joomdle->call_method ('addForum', (int) $course->id, (int) -2, $course->fullname);
            // Create news forum
        }

        return true;
    }

    public static function course_deleted (\core\event\course_deleted $event)
    {
		$course = $event->get_record_snapshot('course', $event->objectid);

        $groups_delete = get_config('auth/joomdle', 'jomsocial_groups_delete');
        $autosell = get_config('auth/joomdle', 'auto_sell');
        $use_kunena_forums = get_config('auth/joomdle', 'use_kunena_forums');
        $joomla_user_groups = get_config('auth/joomdle', 'joomla_user_groups');

        $auth_joomdle = new auth_plugin_joomdle ();

        if ($groups_delete)
            $auth_joomdle->call_method ('deleteSocialGroup', $course->id);

        if ($autosell)
        {
            $cid = array ($course->id);
            $auth_joomdle->call_method ("deleteCourseShop", array ((int) $course->id));
        }

        if ($joomla_user_groups)
            $auth_joomdle->call_method ("removeUserGroups", (int) $course->id);

        if ($use_kunena_forums)
            $auth_joomdle->call_method ("removeCourseForums", (int) $course->id);

        return true;
    }

    public static function course_updated (\core\event\course_updated $event)
    {
		$course = $event->get_record_snapshot('course', $event->objectid);

        $groups = get_config('auth/joomdle', 'jomsocial_groups');
        $autosell = get_config('auth/joomdle', 'auto_sell');

        $auth_joomdle = new auth_plugin_joomdle ();

        if ($groups)
            $auth_joomdle->call_method ('updateSocialGroup', $course->fullname,  get_string('auth_joomla_group_for_course', 'auth_joomdle') . ' ' .$course->fullname,  (int) $course->id);

        if ($autosell)
        {
            $cid = array ($course->id);
            $auth_joomdle->call_method ("updateCourseShop", array ((int) $course->id));
        }

        return true;

    }

    public static function role_assigned (\core\event\role_assigned $event)
    {
        global $CFG, $DB;

        $activities = get_config('auth/joomdle', 'jomsocial_activities');
        $groups = get_config('auth/joomdle', 'jomsocial_groups');
        $enrol_parents = get_config('auth/joomdle', 'enrol_parents');
        $parent_role_id = get_config('auth/joomdle', 'parent_role_id');
        $points = get_config('auth/joomdle', 'give_points');
        $auto_mailing_lists = get_config('auth/joomdle', 'auto_mailing_lists');
        $use_kunena_forums = get_config('auth/joomdle', 'use_kunena_forums');
        $joomla_user_groups = get_config('auth/joomdle', 'joomla_user_groups');

        $auth_joomdle = new auth_plugin_joomdle ();

        $context = context::instance_by_id($event->contextid, MUST_EXIST);

        /* If a course enrolment, publish */
        if ($context->contextlevel == CONTEXT_COURSE)
        {
            $courseid = $context->instanceid;
            $conditions = array ('id' => $courseid);
            $course = $DB->get_record('course', $conditions);
            $conditions = array ('id' => $course->category);
            $cat = $DB->get_record('course_categories',$conditions);
			$userid = $event->relateduserid;
            $conditions = array ('id' => $userid);
            $user = $DB->get_record('user', $conditions);

            // Jomsocial activity
            if ($activities)
            {
                $auth_joomdle->call_method ('addActivityCourseEnrolment', $user->username, (int) $courseid, $course->fullname, (int) $course->category, $cat->name);
            }

			$roleid = $event->objectid;
            // Join Jomsocial group
            if ($groups)
            {
                /* Join teachers as group admins, and students as regular members */
                if ($roleid == 3)
                    $auth_joomdle->call_method ('addSocialGroupMember', $user->username, 1, (int) $courseid);
                else
                    $auth_joomdle->call_method ('addSocialGroupMember', $user->username, -1, (int) $courseid);
            }

            // Enrol parents
            if (($enrol_parents) && ($parent_role_id))
            {
                if ($roleid == 5)
                {
                    /* Get mentors for the student */
                    $usercontext = context_user::instance($userid);
                    $usercontextid = $usercontext->id;

                    $query =
                        "SELECT userid
                        FROM
                        {$CFG->prefix}role_assignments
                        WHERE
                        roleid = ? and contextid = ?
                        ";

                    $params = array ($parent_role_id, $usercontextid);
                    $mentors =  $DB->get_records_sql($query, $params);
                    foreach ($mentors as $mentor)
                    {
                        /* Enrol as parent into course*/
                        $conditions = array ('id' => $mentor->userid);
                        $parent_user = $DB->get_record('user', $conditions);

                        $auth_joomdle->enrol_user ($parent_user->username, $courseid, $parent_role_id);
                    }
                }
            }

            if ($points)
                $auth_joomdle->call_method ('addPoints', 'joomdle.enrol', $user->username,   (int) $courseid, $course->fullname);

            if ($auto_mailing_lists)
            {
                $type = '';
                if ($roleid == 3)
                    $type = 'course_teachers';
                else  if ($roleid == 5)
                {
                    $type = 'course_students';

                    /* Get mentors for the student */
                    $usercontext = context_user::instance($userid);
                    $usercontextid = $usercontext->id;

                    $query =
                        "SELECT userid
                        FROM
                        {$CFG->prefix}role_assignments
                        WHERE
                        roleid = ? and contextid = ?
                        ";

                    $params = array ($parent_role_id, $usercontextid);
                    $mentors =  $DB->get_records_sql($query, $params);
                    foreach ($mentors as $mentor)
                    {
                        $conditions = array ('id' => $mentor->userid);
                        $parent_user = $DB->get_record('user', $conditions);

                        $auth_joomdle->call_method ('addMailingSub',  $parent_user->username,   (int) $courseid, 'course_parents');
                    }

                }

                if ($type)
                    $auth_joomdle->call_method ('addMailingSub',  $user->username,   (int) $courseid, $type);
            }

            if ($joomla_user_groups)
            {
                $type = '';
                if ($roleid == 3)
                    $type = 'teachers';
                else  if ($roleid == 5)
                    $type = 'students';

                if ($type)
                    $auth_joomdle->call_method ('addGroupMember',  (int) $courseid, $user->username, $type);
            }

            if ($use_kunena_forums)
            {
                if ($roleid == 3)
                    $auth_joomdle->call_method ('addForumsModerator',  (int) $courseid, $user->username);
            }
        }

        return true;
    }

    public static function role_unassigned (\core\event\role_unassigned $event)
    {
        global $DB, $CFG;

        $groups = get_config('auth/joomdle', 'jomsocial_groups');
        $auto_mailing_lists = get_config('auth/joomdle', 'auto_mailing_lists');
        $use_kunena_forums = get_config('auth/joomdle', 'use_kunena_forums');
        $joomla_user_groups = get_config('auth/joomdle', 'joomla_user_groups');
        $parent_role_id = get_config('auth/joomdle', 'parent_role_id');

        $auth_joomdle = new auth_plugin_joomdle ();

        $context = context::instance_by_id($event->contextid, MUST_EXIST);
        /* If a course unenrolment, remove from group */
        if ($context->contextlevel == CONTEXT_COURSE)
        {
            $courseid = $context->instanceid;
            $conditions = array ('id' => $courseid);
            $course = $DB->get_record('course', $conditions);
            $conditions = array ('id' => $course->category);
            $cat = $DB->get_record('course_categories', $conditions);
			$userid = $event->relateduserid;
            $conditions = array ('id' => $userid);
            $user = $DB->get_record('user', $conditions);

            if ($groups)
                $auth_joomdle->call_method ('removeSocialGroupMember', $user->username, $courseid);

			$roleid = $event->objectid;
            if ($auto_mailing_lists)
            {
                if ($roleid == 3)
                    $type = 'course_teachers';
                else  if ($roleid == 5)
                {
                    $type = 'course_students';

                    /* Get mentors for the student */
                    $usercontext = context_user::instance($userid);
                    $usercontextid = $usercontext->id;

                    $query =
                        "SELECT userid
                        FROM
                        {$CFG->prefix}role_assignments
                        WHERE
                        roleid = ? and contextid = ?
                        ";

                    $params = array ($parent_role_id, $usercontextid);
                    $mentors =  $DB->get_records_sql($query, $params);
                    foreach ($mentors as $mentor)
                    {
                        $conditions = array ('id' => $mentor->userid);
                        $parent_user = $DB->get_record('user', $conditions);

                        $auth_joomdle->call_method ('removeMailingSub',  $parent_user->username,   (int) $courseid, 'course_parents');
                    }

                }

                $auth_joomdle->call_method ('removeMailingSub',  $user->username,   (int) $courseid, $type);
            }

            if ($joomla_user_groups)
            {
                $type = '';
                if ($roleid == 3)
                    $type = 'teachers';
                else  if ($roleid == 5)
                    $type = 'students';

                if ($type)
                    $auth_joomdle->call_method ('removeGroupMember',  (int) $courseid, $user->username, $type);
            }

            if ($use_kunena_forums)
            {
                if ($roleid == 3)
                    $auth_joomdle->call_method ('removeForumsModerator',  (int) $courseid, $user->username);
            }
        }

        return true;
    }


    public static function attempt_submitted (\mod_quiz\event\attempt_submitted $event)
    {
        global $DB , $CFG;

        $activities = get_config('auth/joomdle', 'jomsocial_activities');
        $points = get_config('auth/joomdle', 'give_points');

        $auth_joomdle = new auth_plugin_joomdle ();

        $course  = $DB->get_record('course', array('id' => $event->courseid));
        $quiz    = $DB->get_record('quiz', array('id' => $event->other['quizid']));
        $user    = $DB->get_record('user', array('id' => $event->other['submitterid']));

        if ($activities)
            $auth_joomdle->call_method ('addActivityQuizAttempt', $user->username, (int) $event->courseid, $course->fullname,  $quiz->name);

        if ($points)
                $auth_joomdle->call_method ('addPoints', 'joomdle.quiz_attempt', $user->username,   (int) $event->courseid, $course->fullname);

        return true;
    }

    public static function course_module_created (\core\event\course_module_created $event)
    {
        $use_kunena_forums = get_config('auth/joomdle', 'use_kunena_forums');

        $auth_joomdle = new auth_plugin_joomdle ();

        if ($use_kunena_forums)
        {
            if ($event->other['modulename'] == 'forum')
            {
                $auth_joomdle->call_method ('addForum', (int) $event->courseid, $event->objectid, $event->other['name']);
            }
        }

        return true;
    }

    public static function course_module_deleted (\core\event\course_module_deleted $event)
    {
        $use_kunena_forums = get_config('auth/joomdle', 'use_kunena_forums');

        $auth_joomdle = new auth_plugin_joomdle ();

        if ($use_kunena_forums)
        {
            if ($event->other['modulename'] == 'forum')
            {
                $auth_joomdle->call_method ("removeForum", (int) $event->courseid, $event->objectid);
            }
        }

        return true;
    }

    public static function course_module_updated (\core\event\course_module_updated $event)
    {
        $use_kunena_forums = get_config('auth/joomdle', 'use_kunena_forums');

        $auth_joomdle = new auth_plugin_joomdle ();

        if ($use_kunena_forums)
        {
            if ($event->other['modulename'] == 'forum')
            {
                $auth_joomdle->call_method ("updateForum", (int) $event->courseid, $event->objectid, $event->other['name']);
            }
        }

        return true;
    }

    public static function course_completed (\core\event\course_completed $event)
    {
        global $DB , $CFG;

        $activities = get_config('auth/joomdle', 'jomsocial_activities');
        $points = get_config('auth/joomdle', 'give_points');

        $auth_joomdle = new auth_plugin_joomdle ();

        $course  = $DB->get_record('course', array('id' => $event->courseid));
        $user    = $DB->get_record('user', array('id' => $event->relateduserid));

        if ($activities)
            $auth_joomdle->call_method ('addActivityCourseCompleted', $user->username, (int) $event->courseid, $course->fullname);

        if ($points)
			$auth_joomdle->call_method ('addPoints', 'joomdle.course_completed', $user->username,   (int) $event->courseid, $course->fullname);

        return true;
    }

    public static function user_password_updated (\core\event\user_password_updated $event)
	{
        $sync_to_joomla = get_config('auth/joomdle', 'sync_to_joomla');
		if (!$sync_to_joomla)
			return true;

		$user = $event->get_record_snapshot('user', $event->contextinstanceid);

        if ($user->auth != 'joomdle')
            return true;

        $auth_joomdle = new auth_plugin_joomdle ();
		$auth_joomdle->call_method ('changePassword', $user->username, $user->password);
	}
}
