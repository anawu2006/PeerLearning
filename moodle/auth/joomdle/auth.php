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
 * Joomdle main class file
 *
 * Contains all Joomdle web service functions
 *
 * @package    auth_joomdle
 * @copyright  2009 Qontori Pte Ltd
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

if (!defined('MOODLE_INTERNAL')) {
    die('Direct access to this script is forbidden.');    ///  It must be included from a Moodle page
}

require_once($CFG->libdir.'/authlib.php');
require_once($CFG->dirroot.'/auth/manual/auth.php');
require_once($CFG->dirroot.'/calendar/lib.php');
require_once($CFG->dirroot.'/mod/forum/lib.php');
require_once($CFG->dirroot.'/lib/datalib.php');
require_once($CFG->dirroot.'/lib/gdlib.php');
require_once($CFG->dirroot.'/lib/grade/grade_grade.php');
require_once($CFG->dirroot.'/lib/grade/grade_item.php');
require_once($CFG->dirroot.'/lib/gradelib.php');
require_once $CFG->dirroot.'/grade/lib.php';
require_once("$CFG->dirroot/enrol/locallib.php");
require_once($CFG->dirroot.'/course/lib.php');
require_once($CFG->dirroot.'/group/lib.php');
require_once($CFG->dirroot.'/lib/grouplib.php');
require_once($CFG->dirroot.'/cohort/lib.php');
require_once $CFG->dirroot.'/grade/report/user/lib.php';
require_once $CFG->dirroot.'/lib/coursecatlib.php';




/**
 * Joomdle authentication plugin.
 */
class auth_plugin_joomdle extends auth_plugin_manual {

    /**
     * Constructor.
     */
    function auth_plugin_joomdle() {
        $this->authtype = 'joomdle';
		$this->config = get_config('auth/joomdle');
    }

	function can_signup() {
        return true;
    }

	function user_signup($user, $notify=true) 
	{
        global $CFG, $DB;
        require_once($CFG->dirroot.'/user/profile/lib.php');


		$password_clear = $user->password;
        $user->password = hash_internal_user_password($user->password);

        if (! ($user->id = $DB->insert_record('user', $user)) ) {
            print_error('auth_emailnoinsert','auth');
        }

        /// Save any custom profile field information
        profile_save_data($user);

		$conditions = array ('id' => $user->id);
        $user = $DB->get_record('user', $conditions);

		/* Create user in Joomla */
		$userinfo['username'] = $user->username;
		$userinfo['password'] = $password_clear;
		$userinfo['password2'] = $password_clear;
		$userinfo['name'] = $user->firstname. " " . $user->lastname;
		$userinfo['firstname'] = $user->firstname;
		$userinfo['lastname'] = $user->lastname;
		$userinfo['email'] = $user->email;
		$userinfo['block'] = 1;

		\core\event\user_created::create_from_userid($user->id)->trigger();

        if (! send_confirmation_email($user)) {
            print_error('auth_emailnoemail','auth');
        }

        if ($notify) {
            $emailconfirm = get_string('emailconfirm');
            $PAGE->set_url('/auth/joomdle/auth.php');
            $PAGE->navbar->add($emailconfirm);
            $PAGE->set_title($emailconfirm);
            $PAGE->set_heading($emailconfirm);
            echo $OUTPUT->header();
            notice(get_string('emailconfirmsent', '', $user->email), "{$CFG->wwwroot}/index.php");
        } else {
            return true;
        }
    }

 function can_confirm() {
        return true;
    }

   function user_confirm($username, $confirmsecret = null)  
   {
	   global $DB;

        $user = get_complete_user_data('username', $username);

        if (!empty($user)) {
            if ($user->confirmed) {
                return AUTH_CONFIRM_ALREADY;

            } else if ($user->auth != 'joomdle') {
                return AUTH_CONFIRM_ERROR;

            } else if ($user->secret == stripslashes($confirmsecret)) {   // They have provided the secret key to get in
				$conditions = array ('id' => $user->id);
                if (!$DB->set_field("user", "confirmed", 1, $conditions)) {
                    return AUTH_CONFIRM_FAIL;
                }
                if (!$DB->set_field("user", "firstaccess", time(), $conditions)) {
                    return AUTH_CONFIRM_FAIL;
                }

				/* Enable de user in Joomla */
				$this->call_method ("activateUser", $username);

                return AUTH_CONFIRM_OK;
            }
        } else {
            return AUTH_CONFIRM_ERROR;
        }
    }


    /**
     * Returns true if the username and password work and false if they are
     * wrong or don't exist.
     *
     * @param string $username The username (with system magic quotes)
     * @param string $password The password (with system magic quotes)
     *
     * @return bool Authentication success or failure.
     */
    function user_login($username, $password) {

	    if (!$password)
		    return false;

		$user = get_complete_user_data ('username', $username);

		if (!$user)
			return false;

		$logged = $this->call_method ("login", $username, $password);

		return $logged;
    }

	/**
     * Returns true if this authentication plugin is 'internal'.
     *
     * @return bool
     */
    function is_internal() {
        return true;
    }


	function can_change_password() {
		return true;
	}

	function user_update_password ($user, $password)
	{
		$return =  $this->call_method ("changePassword", $user->username, $password);

		$user = get_complete_user_data('id', $user->id);
		return update_internal_user_password($user, $password);

//		return true;
	//	return $return;
	}

    /**
     * Prints a form for configuring this authentication plugin.
     *
     * This function is called from admin/auth.php, and outputs a full page with
     * a form for configuring this plugin.
     *
     * @param array $page An object containing all the data for this page.
     */
    function config_form($config, $err, $user_fields) {
        include "config.html";
    }

    function process_config($config) 
	{
		global $DB;

		if (!isset($config->joomla_url)) {
			$config->joomla_url = '';
								}
		set_config('joomla_url', $config->joomla_url, 'auth/joomdle'); 

		if (!isset($config->connection_method)) {
			$config->connection_method = 'fgc';
		}
		set_config('connection_method', $config->connection_method, 'auth/joomdle'); 

		if (!isset($config->joomla_auth_token)) {
			$config->joomla_auth_token = '';
		}
		set_config('joomla_auth_token', $config->joomla_auth_token, 'auth/joomdle'); 

		if (!isset($config->sync_to_joomla)) {
			$config->sync_to_joomla = 0;
		}
		set_config('sync_to_joomla', $config->sync_to_joomla, 'auth/joomdle'); 

		if (!isset($config->joomla_lang)) {
            $config->joomla_lang = '';
        }
        set_config('joomla_lang', $config->joomla_lang, 'auth/joomdle');

		if (!isset($config->joomla_sef)) {
            $config->joomla_sef = '';
        }
        set_config('joomla_sef', $config->joomla_sef, 'auth/joomdle');

		if (!isset($config->redirectless_sso)) {
            $config->redirectless_sso = '';
        }
        set_config('redirectless_sso', $config->redirectless_sso, 'auth/joomdle');

		if (!isset($config->logout_redirect_to_joomla)) {
            $config->logout_redirect_to_joomla = '';
        }
        set_config('logout_redirect_to_joomla', $config->logout_redirect_to_joomla, 'auth/joomdle');

		if (!isset($config->jomsocial_activities)) {
			$config->jomsocial_activities = 0;
		}
		set_config('jomsocial_activities', $config->jomsocial_activities, 'auth/joomdle'); 

		if (!isset($config->jomsocial_groups)) {
			$config->jomsocial_groups = 0;
		}
		set_config('jomsocial_groups', $config->jomsocial_groups, 'auth/joomdle'); 

		if (!isset($config->jomsocial_groups_delete)) {
			$config->jomsocial_groups_delete = 0;
		}
		set_config('jomsocial_groups_delete', $config->jomsocial_groups_delete, 'auth/joomdle'); 

		if (!isset($config->auto_sell)) {
			$config->auto_sell = 0;
		}
		set_config('auto_sell', $config->auto_sell, 'auth/joomdle'); 

		if (!isset($config->enrol_parents)) {
			$config->enrol_parents = 0;
		}
		set_config('enrol_parents', $config->enrol_parents, 'auth/joomdle'); 

		if (!isset($config->parent_role_id)) {
			$config->parent_role_id = '';
		}
		set_config('parent_role_id', $config->parent_role_id, 'auth/joomdle'); 

		if (!isset($config->give_points)) {
			$config->give_points = 0;
		}
		set_config('give_points', $config->give_points, 'auth/joomdle'); 

		if (!isset($config->auto_mailing_lists)) {
			$config->auto_mailing_lists = 0;
		}
		set_config('auto_mailing_lists', $config->auto_mailing_lists, 'auth/joomdle'); 

		if (!isset($config->joomla_user_groups)) {
			$config->joomla_user_groups = 0;
		}
		set_config('joomla_user_groups', $config->joomla_user_groups, 'auth/joomdle'); 

		if (!isset($config->use_kunena_forums)) {
			$config->use_kunena_forums = 0;
		}
		set_config('use_kunena_forums', $config->use_kunena_forums, 'auth/joomdle'); 

		return true;
    }

	function _get_xmlrpc_url () {
        $joomla_lang = get_config('auth/joomdle', 'joomla_lang');
        $joomla_sef = get_config('auth/joomdle', 'joomla_sef');
        $joomla_auth_token = get_config('auth/joomdle', 'joomla_auth_token');

		if ($joomla_lang == '')
			$joomla_xmlrpc_server_url = get_config ('auth/joomdle', 'joomla_url').'/index.php?option=com_joomdle&task=ws.server&format=xmlrpc';
		else
			if ($joomla_sef)
				$joomla_xmlrpc_server_url = get_config ('auth/joomdle', 'joomla_url').'/index.php/'.$joomla_lang.'/?option=com_joomdle&task=ws.server&format=xmlrpc';
			else
				$joomla_xmlrpc_server_url = get_config ('auth/joomdle', 'joomla_url').'/index.php?lang='.$joomla_lang.'&option=com_joomdle&task=ws.server&format=xmlrpc';

		// Add auth token
		$joomla_xmlrpc_server_url .= "&token=" . $joomla_auth_token;

		return $joomla_xmlrpc_server_url;
	}


    function call_method ($method, $params = '', $params2 = '', $params3 = '' , $params4 = '', $params5 = '')
    {
		$connection_method = get_config('auth/joomdle', 'connection_method');

		if ($connection_method == 'fgc')
			$response = $this->call_method_fgc ($method, $params, $params2, $params3, $params4, $params5);
		else
			$response = $this->call_method_curl ($method, $params, $params2, $params3, $params4, $params5);

		return $response;
    }

    function call_method_fgc ($method, $params = '', $params2 = '', $params3 = '' , $params4 = '', $params5 = '')
    {
		$joomla_xmlrpc_url = $this->_get_xmlrpc_url ();

		$options = array ();

		if ($params == '')
			$request = xmlrpc_encode_request("joomdle.".$method, array (), $options);
		else if ($params2 == '')
			$request = xmlrpc_encode_request("joomdle.".$method, array ($params), $options);
		else if ($params3 == '')
			$request = xmlrpc_encode_request("joomdle.".$method, array ($params, $params2), $options);
		else if ($params4 == '')
			$request = xmlrpc_encode_request("joomdle.".$method, array ($params, $params2, $params3), $options);
		else if ($params5 == '')
			$request = xmlrpc_encode_request("joomdle.".$method, array ($params, $params2, $params3, $params4), $options);
		else
			$request = xmlrpc_encode_request("joomdle.".$method, array ($params, $params2, $params3, $params4, $params5), $options);

		$context = stream_context_create(array('http' => array(
			'method' => "POST",
			'header' => "Content-Type: text/xml ",
			'content' => $request
		)));
		$response = file_get_contents($joomla_xmlrpc_url, false, $context);

		$data = xmlrpc_decode($response);

		if (is_array ($data))
			if (xmlrpc_is_fault ($data))
			{
				return  "XML-RPC Error (".$data['faultCode']."): ".$data['faultString'];
			}

		return $data;
    }

    function call_method_curl ($method, $params = '', $params2 = '', $params3 = '' , $params4 = '', $params5 = '')
    {
		global $CFG;

		$joomla_xmlrpc_url = $this->_get_xmlrpc_url ();


		$options = array ();

		if ($params == '')
			$request = xmlrpc_encode_request("joomdle.".$method, array (), $options);
		else if ($params2 == '')
			$request = xmlrpc_encode_request("joomdle.".$method, array ($params), $options);
		else if ($params3 == '')
			$request = xmlrpc_encode_request("joomdle.".$method, array ($params, $params2), $options);
		else if ($params4 == '')
			$request = xmlrpc_encode_request("joomdle.".$method, array ($params, $params2, $params3), $options);
		else if ($params5 == '')
			$request = xmlrpc_encode_request("joomdle.".$method, array ($params, $params2, $params3, $params4), $options);
		else
			$request = xmlrpc_encode_request("joomdle.".$method, array ($params, $params2, $params3, $params4, $params5), $options);

		$headers = array();
		array_push($headers,"Content-Type: text/xml");
		array_push($headers,"Content-Length: ".strlen($request));
		array_push($headers,"\r\n");

		$ch = curl_init();
		curl_setopt( $ch, CURLOPT_URL, $joomla_xmlrpc_url); # URL to post to
		curl_setopt( $ch, CURLOPT_RETURNTRANSFER, 1 ); # return into a variable
		curl_setopt( $ch, CURLOPT_HTTPHEADER, $headers ); # custom headers, see above
		curl_setopt( $ch, CURLOPT_POSTFIELDS, $request );
		curl_setopt( $ch, CURLOPT_CUSTOMREQUEST, 'POST' ); # This POST is special, and uses its specified Content-type

		 // use proxy if one is configured
		if (!empty($CFG->proxyhost)) {
			if (empty($CFG->proxyport)) {
				curl_setopt($ch, CURLOPT_PROXY, $CFG->proxyhost);
			} else {

				curl_setopt($ch, CURLOPT_PROXY, $CFG->proxyhost.':'.$CFG->proxyport);
			}
			curl_setopt($ch, CURLOPT_HTTPPROXYTUNNEL, false);
		}

		$response = curl_exec( $ch ); # run!
		curl_close($ch);

		$data = xmlrpc_decode($response);

		if (is_array ($response))
			if (xmlrpc_is_fault ($response))
			{
				return  "XML-RPC Error (".$response['faultCode']."): ".$response['faultString'];
			}

		return $data;

	}

	function get_file ($file)
	{
		$connection_method = get_config('auth/joomdle', 'connection_method');

		if ($connection_method == 'fgc')
			$response = file_get_contents ($file, FALSE, NULL);
		else
			$response = $this->get_file_curl ($file);

		return $response;
	}

	function get_file_curl ($file)
	{
        global $CFG;

		$ch = curl_init();
		// set url
		curl_setopt($ch, CURLOPT_URL, $file);

		//return the transfer as a string
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

        // use proxy if one is configured
        if (!empty($CFG->proxyhost)) {
            if (empty($CFG->proxyport)) {
                curl_setopt($ch, CURLOPT_PROXY, $CFG->proxyhost);
            } else {
                curl_setopt($ch, CURLOPT_PROXY, $CFG->proxyhost.':'.$CFG->proxyport);
            }
            curl_setopt($ch, CURLOPT_HTTPPROXYTUNNEL, false);
        }

		// $output contains the output string
		$output = curl_exec($ch);

		// close curl resource to free up system resources
		curl_close($ch);      

		return $output;
	}

    function test () {
	    return "Moodle web services are working!";
    }

    function system_check ()
    {
		$system['joomdle_auth'] = is_enabled_auth('joomdle');
	//	$system['mnet_auth'] = is_enabled_auth('mnet');
		$system['mnet_auth'] = 1; // Left this way so we can have the same system check code for 19 and 20

		/* Web services and XMLRPC enabled */
	//	$system['enablewebservices'] =  get_config (NULL, 'enablewebservices');

		$joomla_url = get_config ('auth/joomdle', 'joomla_url');
		if ($joomla_url == '')
		{
			$system['joomdle_configured'] = 0;
		}
		else
		{
			$system['joomdle_configured'] = 1;
			$data = $this->call_method ("test");
			if (is_array ($data))
			{
				//j1.5 devuelve un error xmlrpc con esta info
				$system['test_data'] = $data['faultString'];
			}
			else
				$system['test_data'] = $data;

		}
		return $system;
    }

	function get_paypal_config ()
	{
		global $CFG;

		$paypal_config = array ();
		$paypal_config['paypalurl'] = empty($CFG->usepaypalsandbox) ? 'https://www.paypal.com/cgi-bin/webscr' : 'https://www.sandbox.paypal.com/cgi-bin/webscr';

		$plugin = enrol_get_plugin('paypal');
		$paypal_config['paypalbusiness'] = $plugin->get_config('paypalbusiness');

		return $paypal_config;
	}

    function my_courses ($username, $order_by_cat = 0) 
	{
        global $CFG;
	
		$username = utf8_decode ($username);
		$username = strtolower ($username);

		$user = get_complete_user_data ('username', $username);

		if (!$user)
			return array ();

		if ($order_by_cat)
			$c = enrol_get_users_courses ($user->id, true, array ('summary'), 'category, sortorder ASC');
		else
			$c = enrol_get_users_courses ($user->id, true, array ('summary'));

		$courses = array ();
		$i = 0;
		foreach ($c as $course)
		{
			$record = array ();
			$record['id'] = $course->id;
			$record['fullname'] = $course->fullname;
			$record['category'] = $course->category;
			$record['cat_name'] = $this->get_cat_name ($course->category);
			$record['summary'] = $course->summary;

            // Check if user can self-unenrol
            $context = context_course::instance($course->id);
            if ((has_capability('enrol/manual:unenrolself', $context, $user->id)) || (has_capability('enrol/self:unenrolself', $context, $user->id)))
                $record['can_unenrol'] = 1;
            else
                $record['can_unenrol'] = 0;

			$record['summary_files'] = array ();
            $course_obj = new course_in_list(get_course($course->id));
            foreach ($course_obj->get_course_overviewfiles() as $file) 
            {
                $isimage = $file->is_valid_image();
                $url = file_encode_url("$CFG->wwwroot/auth/joomdle/pluginfile_joomdle.php",
                    '/'. $file->get_contextid(). '/'. $file->get_component(). '/'.
                    $file->get_filearea(). $file->get_filepath(). $file->get_filename(), !$isimage);

				$url_item = array ();
				$url_item['url'] = $url;
                $record['summary_files'][] =  $url_item;
            }

			$courses[$i] = $record;
			$i++;
		}
		return $courses;
    }

	// Returns all courses in which the user has a role assigned
    function my_all_courses ($username)
    {
        global $CFG, $DB;

        $username = utf8_decode ($username);
        $username = strtolower ($username);
        $user = get_complete_user_data ('username', $username);

        $query = " SELECT distinct c.id as remoteid, c.fullname, ca.name as cat_name, ca.id as cat_id, ra.roleid
                    FROM {$CFG->prefix}course as c, {$CFG->prefix}role_assignments AS ra, {$CFG->prefix}user AS u, {$CFG->prefix}context AS ct,  {$CFG->prefix}course_categories ca
                    WHERE c.id = ct.instanceid  AND ra.userid = u.id AND ct.id = ra.contextid AND ca.id = c.category and u.username= ?";

        // Get user student courses
        $user = get_complete_user_data ('username', $username);
        $c = enrol_get_users_courses ($user->id, true); // get only non supended enrolments
        $my_courses = array (); 
        foreach ($c as $course)
        {   
            $my_courses[] = $course->id;
        }   


        $params = array ($username);
        $records =  $DB->get_records_sql($query, $params);
        $data = array ();
        $i = 0;
        foreach ($records as $p)
        {
            if ($p->roleid == 5) // If student, check that enrolment is not suspended
            {
                if (!in_array ($p->remoteid, $my_courses))
                    continue;
            }

            $e['id'] = $p->remoteid;
            $e['fullname'] = $p->fullname;
            $e['fullname'] = format_string($e['fullname']);
            $e['category'] = $p->cat_id;

            $data[$i] = $e;
            $i++;
        }
        return $data;
    }


    function my_teachers ($username) 
	{
        global $CFG;
	
		$username = utf8_decode ($username);
		$username = strtolower ($username);

		$user = get_complete_user_data ('username', $username);
		$c = enrol_get_users_courses ($user->id);

		$courses = array ();
		$i = 0;
		foreach ($c as $course)
		{
			$record = array ();
			$record['id'] = $course->id;
			$record['fullname'] = $course->fullname;

			$context = context_course::instance($course->id);
			/* 3 indica profesores editores (table mdl_role) */
			$profs = get_role_users(3 , $context);
			$data = array ();
			foreach ($profs as $p)
			{
				$e['firstname'] = $p->firstname;
				$e['lastname'] = $p->lastname;
				$e['username'] = $p->username;

				$data[] = $e;
			}
			$record['teachers'] = $data;

			$courses[$i] = $record;
			$i++;
		}
		return $courses;
    }

    function my_classmates ($username) 
	{
        global $CFG;
	
		$username = utf8_decode ($username);
		$username = strtolower ($username);

		$user = get_complete_user_data ('username', $username);
		$c = enrol_get_users_courses ($user->id);

		$courses = array ();
		$i = 0;
		$data = array ();
		foreach ($c as $course)
		{
			$mates = $this->get_course_students ($course->id);

			foreach ($mates as $p)
			{
				$e['firstname'] = $p['firstname'];
				$e['lastname'] = $p['lastname'];
				$e['username'] = $p['username'];

				$data[$e['username']] = $e;
			}
		}
		return $data;
    }

    /**
      * Returns course list
      * 
      * @param int $available If true, return only self enrollable courses
      */
    function list_courses ($available = 0, $sortby = 'created', $guest = 0, $username = '')
	{
		global $CFG, $DB;

		$where = '';

		$query =
			"SELECT
			co.id          AS remoteid,
			ca.id          AS cat_id,
			co.sortorder,
			co.fullname,
			co.shortname,
			co.idnumber,
			co.summary,
			co.startdate,
			co.timecreated as created,
			co.timemodified as modified,
			ca.name        AS cat_name,
			ca.description AS cat_description
			FROM
			{$CFG->prefix}course_categories ca
			JOIN
			{$CFG->prefix}course co ON
			ca.id = co.category
			WHERE
			co.visible = '1' 
			$where
			ORDER BY
			$sortby 
			";
		$records = $DB->get_records_sql($query);


		if ($username)
		{
			$user = get_complete_user_data ('username', $username);
			$c = enrol_get_users_courses ($user->id, true);

			$my_courses = array ();
			foreach ($c as $course)
			{
				$my_courses[] = $course->id;
			}
		}

		$i = 0;
		$now = time();
		$options['noclean'] = true;
		$cursos = array ();
		foreach ($records as $curso)
		{
			$enrol_methods = enrol_get_instances($curso->remoteid, true);

			$c = get_object_vars ($curso);

			$c['self_enrolment'] = 0;
			$c['guest'] = 0;
			$in = true;
			foreach ($enrol_methods as $instance)
			{
				if (($instance->enrol == 'paypal') || ($instance->enrol == 'joomdle'))
				{
					$enrol = $instance->enrol;
					$query = "SELECT cost, currency
								FROM {$CFG->prefix}enrol
								where courseid = ? and enrol = ?";
					$params = array ($curso->remoteid, $enrol);
					$record =  $DB->get_record_sql($query, $params);
					$c['cost'] = (float) $record->cost;
					$c['currency'] = $record->currency;
				}

				// Self-enrolment
				if ($instance->enrol == 'self')
					$c['self_enrolment'] = 1;
				else
					$c['self_enrolment'] = 0;


				// Guest access
				if ($instance->enrol == 'guest')
					$c['guest'] = 1;

				if (($instance->enrolstartdate) && ($instance->enrolenddate))
				{
					$in = false;
					if (($instance->enrolstartdate <= $now) && ($instance->enrolenddate >= $now))
						$in = true;
				}
				else if ($instance->enrolstartdate)
				{
					$in = false;
					if (($instance->enrolstartdate <= $now))
						$in = true;
				}
				else if ($instance->enrolenddate)
				{
					$in = false;
					if ($instance->enrolenddate >= $now)
						$in = true;
				}
			}

			// Check if only guest courses are wanted
			if (($guest) && (!$course_info['guest']))
				continue;

			// Skip not self-enrolable courses if param says so
			if (($available) && (!$c['self_enrolment']))
				continue;


			$c['in_enrol_date'] = $in;

			$c['enroled'] = 0;
			if ($username)
			{
				if (in_array ($curso->remoteid, $my_courses))
					$c['enroled'] = 1;
			}


			$c['fullname'] = format_string($c['fullname']);
			$c['cat_name'] = format_string($c['cat_name']);

			$context = context_course::instance($curso->remoteid);
			$c['summary'] = file_rewrite_pluginfile_urls ($c['summary'], 'pluginfile.php', $context->id, 'course', 'summary', NULL);
			$c['summary'] = str_replace ('pluginfile.php', '/auth/joomdle/pluginfile_joomdle.php', $c['summary']);
			$c['summary'] = format_text($c['summary'], FORMAT_MOODLE, $options);

			$context = context_coursecat::instance($curso->cat_id);
			$c['cat_description'] = file_rewrite_pluginfile_urls ($c['cat_description'], 'pluginfile.php', $context->id, 'coursecat', 'description', NULL);
			$c['cat_description'] = str_replace ('pluginfile.php', '/auth/joomdle/pluginfile_joomdle.php', $c['cat_description']);
			$c['cat_description'] = format_text($c['cat_description'], FORMAT_MOODLE, $options);

			$c['summary_files'] = array ();
            $course = new course_in_list(get_course($curso->remoteid));
            foreach ($course->get_course_overviewfiles() as $file) 
            {
                $isimage = $file->is_valid_image();
                $url = file_encode_url("$CFG->wwwroot/auth/joomdle/pluginfile_joomdle.php",
                    '/'. $file->get_contextid(). '/'. $file->get_component(). '/'.
                    $file->get_filearea(). $file->get_filepath(). $file->get_filename(), !$isimage);

				$url_item = array ();
				$url_item['url'] = $url;
                $c['summary_files'][] =  $url_item;
            }

			$cursos[$i] = $c;

			$i++;
		}

		return ($cursos);
    }

    /**
      * Returns course list, including non visible courses
      */
	function get_all_courses ($sortby = 'created')
    {
        global $CFG, $DB;

        $query =
            "SELECT
            co.id          AS remoteid,
            ca.id          AS cat_id,
            co.sortorder,
            co.fullname,
            co.shortname,
            co.idnumber,
            co.summary,
            co.startdate,
            co.timecreated as created,
            co.timemodified as modified,
            ca.name        AS cat_name,
            ca.description AS cat_description
            FROM
            {$CFG->prefix}course_categories ca
            JOIN
            {$CFG->prefix}course co ON
            ca.id = co.category
            ORDER BY
            $sortby 
            ";
        $records = $DB->get_records_sql($query);


        $i = 0;
        $now = time();
        $options['noclean'] = true;
        $cursos = array ();
        foreach ($records as $curso)
        {
            $enrol_methods = enrol_get_instances($curso->remoteid, true);

            $c = get_object_vars ($curso);

            $c['self_enrolment'] = 0;
            $c['guest'] = 0;
            $in = true;
            foreach ($enrol_methods as $instance)
            {
                if (($instance->enrol == 'paypal') || ($instance->enrol == 'joomdle'))
                {
                    $enrol = $instance->enrol;
                    $query = "SELECT cost, currency
                                FROM {$CFG->prefix}enrol
                                where courseid = ? and enrol = ?";
                    $params = array ($curso->remoteid, $enrol);
                    $record =  $DB->get_record_sql($query, $params);
                    $c['cost'] = (float) $record->cost;
                    $c['currency'] = $record->currency;
                }

                // Self-enrolment
                if ($instance->enrol == 'self')
                    $c['self_enrolment'] = 1;
                else
                    $c['self_enrolment'] = 0;


                // Guest access
                if ($instance->enrol == 'guest')
                    $c['guest'] = 1;

                if (($instance->enrolstartdate) && ($instance->enrolenddate))
                {
                    $in = false;
                    if (($instance->enrolstartdate <= $now) && ($instance->enrolenddate >= $now))
                        $in = true;
                }
                else if ($instance->enrolstartdate)
                {
                    $in = false;
                    if (($instance->enrolstartdate <= $now))
                        $in = true;
                }
                else if ($instance->enrolenddate)
                {
                    $in = false;
                    if ($instance->enrolenddate >= $now)
                        $in = true;
                }
            }

            $c['in_enrol_date'] = $in;

            $c['fullname'] = format_string($c['fullname']);
            $c['cat_name'] = format_string($c['cat_name']);

            $context = context_course::instance($curso->remoteid);
            $c['summary'] = file_rewrite_pluginfile_urls ($c['summary'], 'pluginfile.php', $context->id, 'course', 'summary', NULL);
            $c['summary'] = str_replace ('pluginfile.php', '/auth/joomdle/pluginfile_joomdle.php', $c['summary']);
            $c['summary'] = format_text($c['summary'], FORMAT_MOODLE, $options);

            $context = context_coursecat::instance($curso->cat_id);
            $c['cat_description'] = file_rewrite_pluginfile_urls ($c['cat_description'], 'pluginfile.php', $context->id, 'coursecat', 'description', NULL);
            $c['cat_description'] = str_replace ('pluginfile.php', '/auth/joomdle/pluginfile_joomdle.php', $c['cat_description']);
            $c['cat_description'] = format_text($c['cat_description'], FORMAT_MOODLE, $options);

            $c['summary_files'] = array ();
            $course = new course_in_list(get_course($curso->remoteid));
            foreach ($course->get_course_overviewfiles() as $file)
            {
                $isimage = $file->is_valid_image();
                $url = file_encode_url("$CFG->wwwroot/auth/joomdle/pluginfile_joomdle.php",
                    '/'. $file->get_contextid(). '/'. $file->get_component(). '/'.
                    $file->get_filearea(). $file->get_filepath(). $file->get_filename(), !$isimage);

                $url_item = array ();
                $url_item['url'] = $url;
                $c['summary_files'][] =  $url_item;
            }

            $cursos[$i] = $c;

            $i++;
        }

        return ($cursos);
    }

    /**
      * Returns course list based on start chars
      * 
      * @param start_chars: return courses that starts with these chars
      */
    function courses_abc ($start_chars, $username)
	{
		global $CFG, $DB;

		$where = '(';
        $array = str_split ($start_chars);
        foreach ($array as $c)
        {
            $conds[] =  " (fullname like '$c%')";
        }
        $where .= implode (' OR ', $conds);
        $where .= ')';

		$query =
			"SELECT
			co.id          AS remoteid,
			ca.id          AS cat_id,
			co.sortorder,
			co.fullname,
			co.shortname,
			co.idnumber,
			co.summary,
			co.startdate,
			co.timecreated as created,
			co.timemodified as modified,
			ca.name        AS cat_name,
			ca.description AS cat_description
			FROM
			{$CFG->prefix}course_categories ca
			JOIN
			{$CFG->prefix}course co ON
			ca.id = co.category
			WHERE
			co.visible = '1'  AND 
			$where
			ORDER BY
			fullname
			";
		$records = $DB->get_records_sql($query);

		if ($username)
		{
			$user = get_complete_user_data ('username', $username);
			$c = enrol_get_users_courses ($user->id, true);

			$my_courses = array ();
			foreach ($c as $course)
			{
				$my_courses[] = $course->id;
			}
		}

		$i = 0;
		$now = time();
		$options['noclean'] = true;
		$cursos = array ();
		foreach ($records as $curso)
		{
			$c = get_object_vars ($curso);

			$c['self_enrolment'] = 0;
			$c['guest'] = 0;
			$in = true;
			$enrol_methods = enrol_get_instances($curso->remoteid, true);
			foreach ($enrol_methods as $instance)
			{
				if (($instance->enrol == 'paypal') || ($instance->enrol == 'joomdle'))
				{
					$enrol = $instance->enrol;
					$query = "SELECT cost, currency
								FROM {$CFG->prefix}enrol
								where courseid = ? and enrol = ?";
					$params = array ($curso->remoteid, $enrol);
					$record =  $DB->get_record_sql($query, $params);
					$c['cost'] = (float) $record->cost;
					$c['currency'] = $record->currency;
				}

				// Self-enrolment
				if ($instance->enrol == 'self')
					$c['self_enrolment'] = 1;

				// Guest access
				if ($instance->enrol == 'guest')
					$c['guest'] = 1;

				if (($instance->enrolstartdate) && ($instance->enrolenddate))
				{
					$in = false;
					if (($instance->enrolstartdate <= $now) && ($instance->enrolenddate >= $now))
						$in = true;
				}
				else if ($instance->enrolstartdate)
				{
					$in = false;
					if (($instance->enrolstartdate <= $now))
						$in = true;
				}
				else if ($instance->enrolenddate)
				{
					$in = false;
					if ($instance->enrolenddate >= $now)
						$in = true;
				}
			}
			$c['in_enrol_date'] = $in;

			$c['enroled'] = 0;
			if ($username)
			{
				if (in_array ($curso->remoteid, $my_courses))
					$c['enroled'] = 1;
			}


			$c['fullname'] = format_string($c['fullname']);
			$c['cat_name'] = format_string($c['cat_name']);

			$context = context_course::instance($curso->remoteid);
			$c['summary'] = file_rewrite_pluginfile_urls ($c['summary'], 'pluginfile.php', $context->id, 'course', 'summary', NULL);
			$c['summary'] = str_replace ('pluginfile.php', '/auth/joomdle/pluginfile_joomdle.php', $c['summary']);
			$c['summary'] = format_text($c['summary'], FORMAT_MOODLE, $options);

			$c['summary_files'] = array ();
            $course = new course_in_list(get_course($curso->remoteid));
            foreach ($course->get_course_overviewfiles() as $file) 
            {
                $isimage = $file->is_valid_image();
                $url = file_encode_url("$CFG->wwwroot/auth/joomdle/pluginfile_joomdle.php",
                    '/'. $file->get_contextid(). '/'. $file->get_component(). '/'.
                    $file->get_filearea(). $file->get_filepath(). $file->get_filename(), !$isimage);

				$url_item = array ();
				$url_item['url'] = $url;
                $c['summary_files'][] =  $url_item;
            }

			$cursos[] = $c;
		}

		return ($cursos);
    }
    /**
      * Returns course category list
      * 
      * @param int $cat Parent category
      */
    function get_course_categories ($cat = 0) 
	{
		global $CFG, $DB;

		$cat = addslashes ($cat);
		$query =
			"SELECT id, name, description
			FROM
			{$CFG->prefix}course_categories
			WHERE
			visible = '1' AND
			parent = ?
			ORDER BY
			sortorder ASC
			";

		$params = array ($cat);
		$records =  $DB->get_records_sql($query, $params);

		$options['noclean'] = true;
		$cats = array ();
		foreach ($records as $cat)
		{
			$c = get_object_vars ($cat);
			$c['name'] = format_string($c['name']);

			$context = context_coursecat::instance($cat->id);
			$c['description'] = file_rewrite_pluginfile_urls ($c['description'], 'pluginfile.php', $context->id, 'coursecat', 'description', NULL);
			$c['description'] = str_replace ('pluginfile.php', '/auth/joomdle/pluginfile_joomdle.php', $c['description']);
			$c['description'] = format_text($c['description'], FORMAT_MOODLE, $options);
			$cats[] = $c;
		}


		return ($cats);
    }


    /**
      * Returns courses from a specific category
      * 
      * @param string $category Category ID
      * @param int $available If true, return only enrollable courses
      */
    function courses_by_category ($category, $available = 0, $username = '')
	{
		global $CFG, $DB;

		$where = '';
		if ($available)
			$where = " AND co.enrollable = '1'";

		$query =
			"SELECT
			co.id          AS remoteid,
			ca.id          AS cat_id,
			ca.name        AS cat_name,
			ca.description AS cat_description,
			co.sortorder,
			co.fullname,
			co.summary
			FROM
			{$CFG->prefix}course_categories ca
			JOIN
			{$CFG->prefix}course co ON
			ca.id = co.category
			WHERE
			co.visible = '1' AND
			ca.id = ?
			$where
			ORDER BY
			sortorder ASC
			";

		$params = array ($category);
		$records = $DB->get_records_sql($query,$params);

		if ($username)
		{
			$user = get_complete_user_data ('username', $username);
			$c = enrol_get_users_courses ($user->id, true);

			$my_courses = array ();
			foreach ($c as $course)
			{
				$my_courses[] = $course->id;
			}
		}

		$i = 0;
		$now = time();
		$options['noclean'] = true;
		$cursos = array ();
		foreach ($records as $curso)
		{
			$c = get_object_vars ($curso);

			// Course cost
			$enrol_methods = enrol_get_instances($curso->remoteid, true);
			$c['self_enrolment'] = 0;
			$c['guest'] = 0;
			$in = true;
			foreach ($enrol_methods as $instance)
			{
				if (($instance->enrol == 'paypal') || ($instance->enrol == 'joomdle'))
				{
					$enrol = $instance->enrol;
					$query = "SELECT cost, currency
								FROM {$CFG->prefix}enrol
								where courseid = ? and enrol = ?";
					$params = array ($curso->remoteid, $enrol);
					$record =  $DB->get_record_sql($query, $params);
					$c['cost'] = (float) $record->cost;
					$c['currency'] = $record->currency;
				}

				// Self-enrolment
				if ($instance->enrol == 'self')
					$c['self_enrolment'] = 1;

				// Guest access
				if ($instance->enrol == 'guest')
					$c['guest'] = 1;

				if (($instance->enrolstartdate) && ($instance->enrolenddate))
				{
					$in = false;
					if (($instance->enrolstartdate <= $now) && ($instance->enrolenddate >= $now))
						$in = true;
				}
				else if ($instance->enrolstartdate)
				{
					$in = false;
					if (($instance->enrolstartdate <= $now))
						$in = true;
				}
				else if ($instance->enrolenddate)
				{
					$in = false;
					if ($instance->enrolenddate >= $now)
						$in = true;
				}
			}
			$c['in_enrol_date'] = $in;

			$c['enroled'] = 0;
			if ($username)
			{
				if (in_array ($curso->remoteid, $my_courses))
					$c['enroled'] = 1;
			}

			$c['fullname'] = format_string($c['fullname']);
			$c['cat_name'] = format_string($c['cat_name']);
			$context = context_coursecat::instance($c['cat_id']);
			$c['cat_description'] = file_rewrite_pluginfile_urls ($c['cat_description'], 'pluginfile.php', $context->id, 'coursecat', 'description', NULL);
			$c['cat_description'] = str_replace ('pluginfile.php', '/auth/joomdle/pluginfile_joomdle.php', $c['cat_description']);
			$c['cat_description'] = format_text($c['cat_description'], FORMAT_MOODLE, $options);

			$context = context_course::instance($curso->remoteid);
			$c['summary'] = file_rewrite_pluginfile_urls ($c['summary'], 'pluginfile.php', $context->id, 'course', 'summary', NULL);
			$c['summary'] = str_replace ('pluginfile.php', '/auth/joomdle/pluginfile_joomdle.php', $c['summary']);
			$c['summary'] = format_text($c['summary'], FORMAT_MOODLE, $options);

			$c['summary_files'] = array ();
            $course = new course_in_list(get_course($curso->remoteid));
            foreach ($course->get_course_overviewfiles() as $file) 
            {
                $isimage = $file->is_valid_image();
                $url = file_encode_url("$CFG->wwwroot/auth/joomdle/pluginfile_joomdle.php",
                    '/'. $file->get_contextid(). '/'. $file->get_component(). '/'.
                    $file->get_filearea(). $file->get_filepath(). $file->get_filename(), !$isimage);

				$url_item = array ();
				$url_item['url'] = $url;
                $c['summary_files'][] =  $url_item;
			}

			$cursos[] = $c;
		}


		return ($cursos);
    }


    /**
      * Returns detailed info aboout a course
      * 
      * @param int $id Course identifier
      */
    function get_course_info ($id, $username = '') 
	{
		global $CFG, $DB;

		$username = utf8_decode ($username);
		$username = strtolower ($username);

		$query =
			"SELECT
			co.id          AS remoteid,
			ca.id          AS cat_id,
			ca.name        AS cat_name,
			ca.description AS cat_description,
			co.sortorder,
			co.fullname,
			co.shortname,
			co.idnumber,
			co.summary,
			co.startdate,
			co.lang
			FROM
			{$CFG->prefix}course_categories ca
			JOIN
			{$CFG->prefix}course co ON
			ca.id = co.category
			WHERE
			co.id = ?
			ORDER BY
			sortorder ASC";

		$params = array ($id);
		$record =  $DB->get_record_sql($query, $params);

		$options['noclean'] = true;

		$course_info =  get_object_vars ($record);
		$course_info['fullname'] = format_string($course_info['fullname']);
		$course_info['cat_name'] = format_string($course_info['cat_name']);
		$context = context_coursecat::instance($course_info['cat_id']);
		$course_info['cat_description'] = file_rewrite_pluginfile_urls ($course_info['cat_description'], 'pluginfile.php', $context->id, 'coursecat', 'description', NULL);
		$course_info['cat_description'] = str_replace ('pluginfile.php', '/auth/joomdle/pluginfile_joomdle.php', $course_info['cat_description']);
		$course_info['cat_description'] = format_text($course_info['cat_description'], FORMAT_MOODLE, $options);

		$context = context_course::instance($record->remoteid);
		$course_info['summary'] = file_rewrite_pluginfile_urls ($course_info['summary'], 'pluginfile.php', $context->id, 'course', 'summary', NULL);
		$course_info['summary'] = str_replace ('pluginfile.php', '/auth/joomdle/pluginfile_joomdle.php', $course_info['summary']);
		$course_info['summary'] = format_text($course_info['summary'], FORMAT_MOODLE, $options);
		/* Get course cost if any */
		$instances = enrol_get_instances($id, true);

		$params = array ($id);
        $query =
            "SELECT count(*)
            FROM
            {$CFG->prefix}course_sections
            WHERE
            course = ? and section != 0 and visible=1
            ";

        $course_info['numsections'] = $DB->count_records_sql($query, $params);


		$course_info['self_enrolment'] = 0;
		$course_info['guest'] = 0;
		$in = true;
		$now = time ();
		foreach ($instances as $instance)
		{
			if (($instance->enrol == 'paypal') || ($instance->enrol == 'joomdle'))
			{
				$enrol = $instance->enrol;
				$query = "SELECT cost, currency
							FROM {$CFG->prefix}enrol
							where courseid = ? and enrol = ?";
				$params = array ($id, $enrol);
				$record =  $DB->get_record_sql($query, $params);
				$course_info['cost'] = (float) $record->cost;
				$course_info['currency'] = $record->currency;
			}

			/* Get enrolment dates. We get the last one, as good/bad as any other XXX */
			if ($instance->enrolstartdate)
				$course_info['enrolstartdate'] = $instance->enrolstartdate;
			if ($instance->enrolenddate)
				$course_info['enrolenddate'] = $instance->enrolenddate;

			if ($instance->enrolperiod)
                $course_info['enrolperiod'] = $instance->enrolperiod;

			// Self-enrolment
			if ($instance->enrol == 'self')
				$course_info['self_enrolment'] = 1;

			// Guest access
			if ($instance->enrol == 'guest')
				$course_info['guest'] = 1;

			if (($instance->enrolstartdate) && ($instance->enrolenddate))
			{
				$in = false;
				if (($instance->enrolstartdate <= $now) && ($instance->enrolenddate >= $now))
					$in = true;
			}
			else if ($instance->enrolstartdate)
			{
				$in = false;
				if (($instance->enrolstartdate <= $now))
					$in = true;
			}
			else if ($instance->enrolenddate)
			{
				$in = false;
				if ($instance->enrolenddate >= $now)
					$in = true;
			}
		}

		$course_info['in_enrol_date'] = $in;

		$course_info['enroled'] = 0;
		if ($username)
		{
			$user = get_complete_user_data ('username', $username);
			$courses = enrol_get_users_courses ($user->id, true);

			$my_courses = array ();
			foreach ($courses as $course)
			{
				$my_courses[] = $course->id;
			}
			if (in_array ($id, $my_courses))
				$course_info['enroled'] = 1;
		}

		$course_info['summary_files'] = array ();
		$course = new course_in_list(get_course($id));
		foreach ($course->get_course_overviewfiles() as $file) 
		{
			$isimage = $file->is_valid_image();
			$url = file_encode_url("$CFG->wwwroot/auth/joomdle/pluginfile_joomdle.php",
				'/'. $file->get_contextid(). '/'. $file->get_component(). '/'.
				$file->get_filearea(). $file->get_filepath(). $file->get_filename(), !$isimage);

			$url_item = array ();
			$url_item['url'] = $url;
			$course_info['summary_files'][] =  $url_item;

		}

		return $course_info;
    }

    /**
      * Returns course topics
      * 
      * @param int $id Course identifier
      */
    function get_course_contents ($id) {
		global $CFG, $DB;

		$query =
			"SELECT
			cs.id,
			cs.section,
			cs.name,
			cs.summary
			FROM
			{$CFG->prefix}course_sections cs
			WHERE
			cs.course = ?
			and cs.visible = 1
			ORDER by cs.section;
			";

		$params = array ($id);
		$records =  $DB->get_records_sql($query, $params);

		$context = context_course::instance($id);

		$options['noclean'] = true;
		$data = array ();
		foreach ($records as $r)
		{
			$e['section'] = $r->section;
			$e['section'] = format_string($e['section']);
			$e['name'] = $r->name;
			$e['summary'] = $r->summary;
			$e['summary'] = file_rewrite_pluginfile_urls ($r->summary, 'pluginfile.php', $context->id, 'course', 'section', $r->id);
			$e['summary'] = str_replace ('pluginfile.php', '/auth/joomdle/pluginfile_joomdle.php', $e['summary']);
			$e['summary'] = format_text($e['summary'], FORMAT_MOODLE, $options);

			$data[] = $e;
		}

		return $data;
    }

    /**
      * Returns editing teachers
      * 
      * @param int $id Course identifier
      */
    function get_course_editing_teachers ($id) 
	{
		global $CFG;

		$id = addslashes ($id);
		$context = context_course::instance($id);
		/* 3 indica profesores editores (table mdl_role) */
		$profs = get_role_users(3 , $context);

		$data = array ();
		$i = 0;
		foreach ($profs as $p)
		{
			$e['firstname'] = $p->firstname;
			$e['lastname'] = $p->lastname;
			$e['username'] = $p->username;

			$data[$i] = $e;
			$i++;
		}

		return $data;
    }


	function teachers_abc ($start_chars)
	{
		global $CFG, $DB;

		$where = '(';
        $array = str_split ($start_chars);
        foreach ($array as $c)
        {
            $conds[] =  " (u.lastname like '$c%')";
        }
        $where .= implode (' OR ', $conds);
        $where .= ')';

		$query = "SELECT distinct (u.id), u.username, u.firstname, u.lastname
				 FROM {$CFG->prefix}course as c, {$CFG->prefix}role_assignments AS ra, {$CFG->prefix}user AS u, {$CFG->prefix}context AS ct
				 WHERE c.id = ct.instanceid AND ra.roleid =3 AND ra.userid = u.id AND ct.id = ra.contextid 
				 AND c.visible=1 and u.suspended=0 AND $where";

		$query .= " ORDER BY lastname, firstname";

		$records =  $DB->get_records_sql($query);
		$data = array ();
		foreach ($records as $p)
		{
			$e['firstname'] = $p->firstname;
			$e['lastname'] = $p->lastname;
			$e['username'] = $p->username;

			$data[] = $e;
		}

		return $data;
	}

	function teacher_courses ($username)
	{
		global $CFG, $DB;

		$username = utf8_decode ($username);
		$username = strtolower ($username);

		$query = " SELECT distinct c.id as remoteid, c.fullname, ca.name as cat_name, ca.id as cat_id
					FROM {$CFG->prefix}course as c, {$CFG->prefix}role_assignments AS ra, {$CFG->prefix}user AS u, {$CFG->prefix}context AS ct,  {$CFG->prefix}course_categories ca
					WHERE c.id = ct.instanceid AND ra.roleid =3 AND ra.userid = u.id AND ct.id = ra.contextid AND ca.id = c.category and u.username= ?";

		$params = array ($username);
		$records =  $DB->get_records_sql($query, $params);
		$data = array ();
		$i = 0;
		foreach ($records as $p)
		{
			$e['remoteid'] = $p->remoteid;
			$e['fullname'] = $p->fullname;
			$e['fullname'] = format_string($e['fullname']);
			$e['cat_id'] = $p->cat_id;
			$e['cat_name'] = $p->cat_name;
			$e['cat_name'] = format_string($e['cat_name']);

			$data[$i] = $e;
			$i++;
		}
		return $data;
	}


    /**
      * Returns number of visible courses
      * 
      */
    function get_course_no () 
	{
		global $CFG, $DB;

		$query =
			"SELECT count(*)
			FROM
			{$CFG->prefix}course_categories ca
			JOIN
			{$CFG->prefix}course co ON
			ca.id = co.category
			WHERE
			co.visible = '1'
			";

		return $DB->count_records_sql($query);
    }

    /**
      * Returns number of visible and enrollable courses
	  XXX NOT WORKING IN 2.0 now
      * 
      */
    function get_enrollable_course_no () 
	{
		global $CFG, $DB;

		$query =
			"SELECT count(*)
			FROM
			{$CFG->prefix}course_categories ca
			JOIN
			{$CFG->prefix}course co ON
			ca.id = co.category
			WHERE
			co.visible = '1' 
			";

		return $DB->count_records_sql($query);
    }

    /**
      * Returns student number
      * 
      */
    function get_student_no () 
	{
		global $CFG, $DB;

		$query =
			"select count(distinct (userid)) from  {$CFG->prefix}role_assignments where roleid=5;
			";

		return $DB->count_records_sql($query);

    }

    /**
      * Returns number of submitted assignments in each task of the course
      *
      * @param int $id Course identifier
      * 
      */
    function get_assignment_submissions ($id) 
	{
        global $CFG, $DB;

        /* Obtenemos todas las tareas del curso */
        $query =
                "select id,name from  {$CFG->prefix}assignment where course= ?;
                ";
        /* Para cada una, obtenemos el numero de trabajos entregados */


		$params = array ($id);
        $tareas = $DB->get_records_sql($query, $params);

        $i = 0;
		$rdo = array ();
        foreach ($tareas as $tarea)
        {
                $ass_id = $tarea->id;
                $query =
                        "select count(*) from  {$CFG->prefix}assignment_submissions where assignment= ?;
                        ";
				$params = array ($ass_id);
                $n = $DB->count_records_sql($query, $params);
                $rdo[$i]['id'] = $tarea->id;
                $rdo[$i]['tarea'] = $tarea->name;
                $rdo[$i]['entregados'] = $n;
                $i++;
        }

        return $rdo;
    }

    /**
      * Returns total number of submitted assignments
      * 
      */
    function get_total_assignment_submissions () 
	{
        global $CFG, $DB;

        $query =
			"select count(*) from  {$CFG->prefix}assignment_submissions;
                ";

		$n = $DB->count_records_sql($query);

        return $n;
    }

    function get_assignment_grades ($id) 
	{
        global $CFG, $DB;

		$SQL = "SELECT g.itemid, gi.itemname as iname,SUM(g.finalgrade) AS sum
		      FROM {$CFG->prefix}grade_items gi
			   JOIN {$CFG->prefix}grade_grades g      ON g.itemid = gi.id
		     WHERE gi.courseid = ?
			   AND g.finalgrade IS NOT NULL
			  GROUP BY g.itemid";
	    $sum_array = array();
		$params = array ($id);
	    if ($sums = $DB->get_records_sql($SQL, $params))
		{
			foreach ($sums as $itemid => $csum) {
				$sql = " select count(*) from {$CFG->prefix}grade_grades where itemid= ?;";
				$params = array ($itemid);
				$n = $DB->count_records_sql($sql, $params);
				$nota['tarea'] = $csum->iname;
				$nota['media'] = $csum->sum / $n;

				$sum_array[] = $nota;
			}
	    }

		return $sum_array;
	}

    /**
      * Returns average grade for a task
      * 
      * @param int $id Course identifier
      */
    function get_average_grade ($itemid) 
	{
        global $CFG, $DB;

		$id = addslashes ($itemid);
		$avg = 0;
		$SQL = "SELECT g.itemid, gi.itemname as iname,SUM(g.finalgrade) AS sum
				  FROM {$CFG->prefix}grade_items gi
				   JOIN {$CFG->prefix}grade_grades g      ON g.itemid = gi.id
				 WHERE gi.id = ?
				   AND g.finalgrade IS NOT NULL
			  GROUP BY g.itemid";
		$sum_array = array();
		$params = array ($itemid);
	    if ($sums = $DB->get_record_sql($SQL, $params)) {
		    $sql = " select count(*) from {$CFG->prefix}grade_grades where itemid=?;";
		    $n = $DB->count_records_sql($sql, $params);
		    $avg = $sums->sum / $n;
	    }

		return $avg;
	}

    /**
      * Returns stats about student grades
      * FIXME doc return type
      * 
//XXX creo que no la usamos
      * @param int $id Course identifier
      */
    function get_assignments_grades ($id)
	{
        global $CFG, $DB;

        /* Obtenemos todas las tareas del curso */
        $query =
                "select id,name from  {$CFG->prefix}assignment where course=?;
                ";
        /* Para cada una, obtenemos la nota media */
		$params = array ($id);
        $tareas = $DB->get_records_sql($query, $params);

        $i = 0;
        foreach ($tareas as $tarea)
        {
                $ass_id = $tarea->id;
                $query =
                        "select itemid,avg(finalgrade) as media
						from  {$CFG->prefix}grade_grades 
						where itemid= ? and
						finalgrade is not NULL
						GROUP BY itemid;
                        ";
				$params = array ($ass_id);
                $n = $DB->get_records_sql($query, $params);
                $rdo[$i]['tarea'] = $tarea->name;
		foreach ($n as $nn)
			$rdo[$i]['media'] = $nn;
                $i++;
        }

        return $rdo;
    }

    /**
      * Returns grades of a student for each task in a course
      * 
      * @param string $user Username
      * @param int $cid Course identifier
      */
    function get_user_grades ($username,$cid) 
	{
        global $CFG, $DB;

		$username = utf8_decode ($username);
		$username = strtolower ($username);
        $user = get_complete_user_data ('username', $username);
        $uid = $user->id;


		$SQL = "SELECT g.itemid, g.finalgrade,gi.courseid,gi.itemname,gi.id, g.timemodified
                      FROM {$CFG->prefix}grade_items gi
                           JOIN {$CFG->prefix}grade_grades g      ON g.itemid = gi.id
                           JOIN {$CFG->prefix}user u              ON u.id = g.userid
                           JOIN {$CFG->prefix}role_assignments ra ON ra.userid = u.id
                     WHERE g.finalgrade IS NOT NULL
			   AND u.id =  ?
			   AND gi.courseid = ?
                  GROUP BY g.itemid, g.finalgrade, gi.courseid, gi.itemname,gi.id, g.timemodified";

	    $sum_array = array();
		$params = array ($uid, $cid);
	    if ($sums = $DB->get_records_sql($SQL, $params)) 
		{
			$i = 0;
			$rdo = array ();
			foreach ($sums as $sum)
			{
				 if (! $grade_grade = grade_grade::fetch(array('itemid'=>$sum->id,'userid'=>$uid))) {
					$grade_grade = new grade_grade();
					$grade_grade->userid = $this->user->id;
					$grade_grade->itemid = $grade_object->id;
					}

					$grade_item = $grade_grade->load_grade_item();

					$sums2[$i] = $sum;
					$scale = $grade_item->load_scale();
					$formatted_grade = grade_format_gradevalue($sums2[$i]->finalgrade, $grade_item, true, GRADE_DISPLAY_TYPE_REAL);

					$sums2[$i]->finalgrade = $formatted_grade;
					
					$rdo[$i]['itemname'] = $sum->itemname;
					$rdo[$i]['finalgrade'] = $formatted_grade;

					$i++;
			}
			return $rdo;
		}

		return array();
    }

	function get_course_grades_by_category ($id, $username)
    {
        global $CFG, $DB;

        $username = utf8_decode ($username);
        $username = strtolower ($username);
        $conditions = array ('username' => $username);
        $user = $DB->get_record('user',$conditions);

        if (!$user)
            return array();

		// Get course total
        $query =
            "select id 
            from  {$CFG->prefix}grade_items 
            where courseid = '$id' 
            AND itemtype='course'
                ";
        $cat_item = $DB->get_record_sql($query);

        $query = "SELECT g.finalgrade,g.rawgrademax
          FROM {$CFG->prefix}grade_grades g
         WHERE g.itemid = ?
           AND g.userid =  ?";
        $params = array ($cat_item->id, $user->id);

        $grade = $DB->get_record_sql($query, $params);

        $total['fullname'] = '';
        $total['finalgrade'] = (float) $grade->finalgrade;
        $total['grademax'] = (float) $grade->rawgrademax;
        $total['items'] = array();


        $data = array ();
        $data[] = $total;

        $query =
            "select {$CFG->prefix}grade_categories.fullname, {$CFG->prefix}grade_items.id, {$CFG->prefix}grade_items.grademax, {$CFG->prefix}grade_categories.id
            from {$CFG->prefix}grade_categories, {$CFG->prefix}grade_items 
            where {$CFG->prefix}grade_categories.id = {$CFG->prefix}grade_items.iteminstance 
            and {$CFG->prefix}grade_items.courseid='$id' and itemtype='category';
                ";

        $cats = $DB->get_records_sql($query);

        foreach ($cats as $r)
        {
            $e['fullname'] = $r->fullname;
            $e['grademax'] = (float) $r->grademax;

            $cat_id = $r->id;

			// Get category grade total
            $query =
                "select id 
                from  {$CFG->prefix}grade_items 
                where iteminstance = '$cat_id' 
                    ";
            $cat_item = $DB->get_record_sql($query);

            $query = "SELECT g.finalgrade
              FROM {$CFG->prefix}grade_grades g
             WHERE g.itemid = ?
               AND g.userid =  ?";
            $params = array ($cat_item->id, $user->id);

            $grade = $DB->get_record_sql($query, $params);
            $e['finalgrade'] = (float) $grade->finalgrade;


            // Get items

            $query =
                "select * 
                from  {$CFG->prefix}grade_items 
                where categoryId = '$cat_id' 
                    ";

            $items = $DB->get_records_sql($query);
            $category_items = array ();

			foreach ($items as $item)
            {
                $category_item['name'] = $item->itemname;
                $category_item['grademax'] = $item->grademax;

                switch ($item->itemmodule)
                {
                    case 'quiz':
                        $conditions = array ('id' => $item->iteminstance);
                        $quiz = $DB->get_record('quiz',$conditions);
                        $category_item['due'] = $quiz->timeclose;
                        break;
                    case 'assignment':
                        $conditions = array ('id' => $item->iteminstance);
                        $assignment = $DB->get_record('assignment',$conditions);
                        $category_item['due'] = $assignment->timedue;
                        break;
					default:
                        $category_item['due'] = 0;
						break;
                }

                $query = "SELECT g.finalgrade, g.feedback
                  FROM {$CFG->prefix}grade_grades g
                 WHERE g.itemid = ?
                   AND g.userid =  ?";
                $params = array ($item->id, $user->id);

                $grade = $DB->get_record_sql($query, $params);

                if ($grade)
                {
                    $category_item['finalgrade'] = (float) $grade->finalgrade;
                    $category_item['feedback'] = $grade->feedback;
                }
                else
                {
                    $category_item['finalgrade'] = (float) 0;
                    $category_item['feedback'] = '';;
                }

                $category_items[] = $category_item;
            }

            $e['items'] = $category_items;

            $data[] = $e;
        }

		// don't return total if there is nothing else
		if (count ($data) == 1)
			return array ();

        return $data;
    }


    function get_my_grades ($username)
    {
        global $CFG, $DB;

        $username = utf8_decode ($username);
        $username = strtolower ($username);

        $user = get_complete_user_data ('username', $username);

  
        if (!$user)
            return array();

        $courses = enrol_get_users_courses ($user->id, true);

        $news = array ();
        foreach ($courses as $c)
        {
            $course_news['remoteid'] = $c->id;
            $course_news['fullname'] = $c->fullname;
            $course_news['fullname'] = format_string ($course_news['fullname']);
            $course_news['grades'] = $this->get_user_grades ($username, $c->id);

            $news[] = $course_news;
        }

        return $news;
    }


    function get_my_grades_by_category ($username)
    {
        global $CFG, $DB;

        $username = utf8_decode ($username);
        $username = strtolower ($username);

        $user = get_complete_user_data ('username', $username);

        if (!$user)
            return array();

        $courses = enrol_get_users_courses ($user->id, true);

        $news = array ();
        foreach ($courses as $c)
        {
            $course_news['remoteid'] = $c->id;
            $course_news['fullname'] = $c->fullname;
            $course_news['fullname'] = format_string ($course_news['fullname']);
            $course_news['grades'] = $this->get_course_grades_by_category ($c->id, $username);

            $news[] = $course_news;
        }

        return $news;
    }

    function get_my_grade_user_report ($username)
    {
        global $CFG, $DB;

        $username = utf8_decode ($username);
        $username = strtolower ($username);

        $user = get_complete_user_data ('username', $username);

        if (!$user)
            return array();

        $courses = enrol_get_users_courses ($user->id, true);

        $news = array ();
        foreach ($courses as $c)
        {
            $course_news['remoteid'] = $c->id;
            $course_news['fullname'] = $c->fullname;
            $course_news['fullname'] = format_string ($course_news['fullname']);
            $course_news['grades'] = $this->get_grade_user_report ($c->id, $username);


            $news[] = $course_news;
        }

        return $news;
    }

    function get_grade_user_report ($id, $username)
    {
        global $CFG, $DB;

        $username = utf8_decode ($username);
        $username = strtolower ($username);
        $conditions = array ('username' => $username);
        $user = $DB->get_record('user',$conditions);

        if (!$user)
            return array();

        $gpr = new grade_plugin_return(array('type'=>'report', 'plugin'=>'user', 'courseid'=>$id, 'userid'=>$user->id));
		$context = context_course::instance($id);
        $report = new grade_report_user($id, $gpr, $context, $user->id);

        // Get course total
        $query =
            "select id 
            from  {$CFG->prefix}grade_items 
            where courseid = '$id' 
            AND itemtype='course'
                ";
        $cat_item = $DB->get_record_sql($query);

        $query = "SELECT g.finalgrade,g.rawgrademax
          FROM {$CFG->prefix}grade_grades g
         WHERE g.itemid = ?
           AND g.userid =  ?";
        $params = array ($cat_item->id, $user->id);

        $grade = $DB->get_record_sql($query, $params);

        $total['fullname'] = '';
		if ($grade)
		{
			$total['finalgrade'] = (float) $grade->finalgrade;
			$total['grademax'] = (float) $grade->rawgrademax;
		}
		else
		{
			$total['finalgrade'] = (float) 0;
			$total['grademax'] = (float) 0;
		}
        $total['items'] = array();
        $total['letter'] = '';

		if (! $grade_grade = grade_grade::fetch(array('itemid'=>$cat_item->id,'userid'=>$user->id))) {
			$grade_grade = new grade_grade();
			$grade_grade->userid = $user->id;
			$grade_grade->itemid = $cat_item->id;
		}
		$grade_grade->load_grade_item();

		$total['letter'] = grade_format_gradevalue($total['finalgrade'], $grade_grade->grade_item, true, GRADE_DISPLAY_TYPE_LETTER);

        $data = array ();
        $data[] = $total;

        $query =
            "select {$CFG->prefix}grade_categories.fullname, {$CFG->prefix}grade_items.id, {$CFG->prefix}grade_items.grademax, {$CFG->prefix}grade_categories.id
            from {$CFG->prefix}grade_categories, {$CFG->prefix}grade_items 
            where {$CFG->prefix}grade_categories.id = {$CFG->prefix}grade_items.iteminstance 
            and {$CFG->prefix}grade_items.courseid='$id' and itemtype='category';
                ";
        $cats = $DB->get_records_sql($query);

		if (count ($cats) == 0)
        {
            // No cats, get items  in "main"
            $query =
                "select {$CFG->prefix}grade_categories.fullname, {$CFG->prefix}grade_items.id, {$CFG->prefix}grade_items.grademax, {$CFG->prefix}grade_categories.id
                from {$CFG->prefix}grade_categories, {$CFG->prefix}grade_items 
                where {$CFG->prefix}grade_categories.id = {$CFG->prefix}grade_items.iteminstance 
                and {$CFG->prefix}grade_items.courseid='$id' and itemtype='course';
                    ";
            $cats = $DB->get_records_sql($query);
        }


        foreach ($cats as $r)
        {
			if ($r->fullname != '?')
				$e['fullname'] = $r->fullname;
			else $e['fullname'] = '';
            $e['grademax'] = (float) $r->grademax;

            $cat_id = $r->id;

            // Get category grade total
            $query =
                "select id 
                from  {$CFG->prefix}grade_items 
                where iteminstance = '$cat_id' 
				AND courseid = '$id'
                    ";
            $cat_item = $DB->get_record_sql($query);

            $query = "SELECT g.finalgrade
              FROM {$CFG->prefix}grade_grades g
             WHERE g.itemid = ?
               AND g.userid =  ?";
            $params = array ($cat_item->id, $user->id);

            $grade = $DB->get_record_sql($query, $params);
            if ($grade)
                $e['finalgrade'] = (float) $grade->finalgrade;
            else $e['finalgrade'] = (float) 0;

			if (! $grade_grade = grade_grade::fetch(array('itemid'=>$cat_item->id,'userid'=>$user->id))) {
				$grade_grade = new grade_grade();
				$grade_grade->userid = $user->id;
				$grade_grade->itemid = $cat_item->id;
			}
			$grade_grade->load_grade_item();

			$e['letter'] = grade_format_gradevalue($total['finalgrade'], $grade_grade->grade_item, true, GRADE_DISPLAY_TYPE_LETTER);

            // Get items

            $query =
                "select * 
                from  {$CFG->prefix}grade_items 
                where categoryId = '$cat_id' 
                    ";

            $items = $DB->get_records_sql($query);
            $category_items = array ();

            if (count ($items) == 0)
                continue;

            foreach ($items as $item)
            {
                $category_item['name'] = $item->itemname;
                $category_item['grademax'] = $item->grademax;

                switch ($item->itemmodule)
                {
                    case 'quiz':
                        $conditions = array ('id' => $item->iteminstance);
                        $quiz = $DB->get_record('quiz',$conditions);
                        $category_item['due'] = $quiz->timeclose;
                        break;
                    case 'assignment':
                        $conditions = array ('id' => $item->iteminstance);
                        $assignment = $DB->get_record('assignment',$conditions);
                        $category_item['due'] = $assignment->timedue;
                        break;
                    default:
                        $category_item['due'] = 0;
                        break;
                }

                $query = "SELECT g.finalgrade, g.feedback
                  FROM {$CFG->prefix}grade_grades g
                 WHERE g.itemid = ?
                   AND g.userid =  ?";
                $params = array ($item->id, $user->id);

                $grade = $DB->get_record_sql($query, $params);

                if (! $grade_grade = grade_grade::fetch(array('itemid'=>$item->id,'userid'=>$user->id))) {
                    $grade_grade = new grade_grade();
                    $grade_grade->userid = $user->id;
                    $grade_grade->itemid = $item->id;
                }

                $grade_grade->load_grade_item();

                if ($grade)
                {
                    $category_item['finalgrade'] = (float) $grade->finalgrade;
                    $category_item['feedback'] = $grade->feedback;
                    if ($report->showlettergrade)
                        $category_item['letter'] = grade_format_gradevalue($grade->finalgrade, $grade_grade->grade_item, true, GRADE_DISPLAY_TYPE_LETTER);
                    else
                        $category_item['letter'] = '';
                }
                else
				{
                    $category_item['finalgrade'] = (float) 0;
                    $category_item['feedback'] = '';
                    $category_item['letter'] = '';
                }

                $category_items[] = $category_item;
            }

            $e['items'] = $category_items;

            $data[] = $e;
        }

		
        // don't return total if there is nothing else
        if (count ($data) == 1)
			$rdo['data'] = array();
		else
			$rdo['data'] = $data;

		$rdo['config']['showlettergrade'] = (int) $report->showlettergrade;

		return $rdo;
	}


    function teacher_get_course_grades ($id, $search)
    {
		if ($search)
			$students = $this->get_course_students  ($id, $search);
		else
			$students = $this->get_course_students  ($id);

        $course_grades = array ();
        foreach ($students as $student)
        {
            $grades = $this->get_course_grades_by_category ($id, $student['username']);
            $student['grades'] = $grades;

			$user_groups = groups_get_user_groups ($id, $student['id']);
            if (!count ($user_groups[0]))
                $student['group'] = '';
            else
            {
                $group_id = $user_groups[0][0];
                $student['group'] = groups_get_group_name ($group_id);
            }


            $course_grades[] = $student;
        }

        return $course_grades;
    }

    function teacher_get_group_grades ($course_id, $group_id, $search)
    {
        $students = $this->get_group_members  ($group_id, $search);

        $course_grades = array ();
        foreach ($students as $student)
        {
            $grades = $this->get_course_grades_by_category ($course_id, $student['username']);
            $student['grades'] = $grades;

			$student['group'] = groups_get_group_name ($group_id);

            $course_grades[] = $student;
        }

        return $course_grades;
    }

    function get_user_grade ($user_id, $item_id)
    {
        global $CFG, $DB;

        $SQL = "SELECT rawgrade 
                      FROM {$CFG->prefix}grade_grades" .
                " WHERE itemid = ? and userid = ?";
        $params = array ($item_id, $user_id);
        $grade = $DB->get_records_sql($SQL, $params);

        return $grade;
    }


    // Returns grades for each course task, for all students in course
    function get_course_grades ($id, $search)
    {
        global $CFG, $DB;

        if ($search)
            $students = $this->get_course_students  ($id, $search);
        else
            $students = $this->get_course_students  ($id);

        // Get grade items
        $SQL = "SELECT id, itemname, grademax
                      FROM {$CFG->prefix}grade_items gi" .
                " WHERE courseid = ? and categoryid is not NULL";
        $params = array ($id);
        $items = $DB->get_records_sql($SQL, $params);

        $return_grades = array ();

        foreach ($students as $student)
        {
            $conditions = array("username" => $student['username']);
            $user = $DB->get_record("user", $conditions);

            $course_grades = array ();
            foreach ($items as $item)
            {
                $grades = $this->get_user_grade ($user->id, $item->id);
                $grade = array_shift ($grades);
                $value = $grade->rawgrade;
                $cg = array ();
                $cg['rawgrade'] = $grade->rawgrade;
                $cg['grademax'] = $item->grademax;
                $course_grades[] = $cg;
            }

            // Get course total
            $query =
                "select id 
                from  {$CFG->prefix}grade_items 
                where courseid = '$id' 
                AND itemtype='course'
                    ";
            $cat_item = $DB->get_record_sql($query);

            $query = "SELECT g.finalgrade,g.rawgrademax
              FROM {$CFG->prefix}grade_grades g
             WHERE g.itemid = ?
               AND g.userid =  ?";
            $params = array ($cat_item->id, $user->id);

            $grade = $DB->get_record_sql($query, $params);

            $cg['rawgrade'] = (float) $grade->finalgrade;
            $cg['grademax'] = (float) $grade->rawgrademax;

            $course_grades[] = $cg;

            $student['grades'] = $course_grades;
            $student['email'] = $user->email;
            $student['firstname'] = $user->firstname;
            $student['lastname'] = $user->lastname;
            $return_grades[] = $student;
        }

        return $return_grades;
    }

    function get_course_grades_items ($id)
    {
        global $CFG, $DB;

        // Get grade items
        $SQL = "SELECT id, itemname, grademax
                      FROM {$CFG->prefix}grade_items gi" .
                " WHERE courseid = ? and categoryid is not NULL";
        $params = array ($id);
        $items = $DB->get_records_sql($SQL, $params);

        // Item names
        $grade_items = array ();
        foreach ($items as $item)
        {
            $gi = array ();
            $gi['itemname'] = $item->itemname;
            $grade_items[] = $gi;
        }

        return $grade_items;
    }



	function get_children_grades ($username)
	{
		$children = $this->get_mentees ($username);

		$cg = array ();
		foreach ($children as $child)
		{
			$data = array ();
			$data['username'] = $child['username'];
			$data['name'] = $child['name'];
			
			$grades = $this->get_my_grades ($child['username']);
			$data['grades'] = $grades;

			$cg[] = $data;
		}   
		
		return $cg;
	}

	function get_children_grade_user_report ($username)
	{
		$children = $this->get_mentees ($username);

		$cg = array ();
		foreach ($children as $child)
		{
			$data = array ();
			$data['username'] = $child['username'];
			$data['name'] = $child['name'];
			
			$grades = $this->get_my_grade_user_report ($child['username']);
			$data['grades'] = $grades;

			$cg[] = $data;
		}   
		
		return $cg;
	}

    /**
      * Returns latest grades 
      * 
      * @param string $user Username
      * @param int $cid Course identifier
      */
    function get_last_user_grades ($username, $limit) 
	{
        global $CFG, $DB;

		$username = utf8_decode ($username);
		$username = strtolower ($username);
        $user = get_complete_user_data ('username', $username);
        $uid = $user->id;

		if (!$limit)
			$limit = 1000;


		 $SQL = "SELECT distinct(g.itemid), g.finalgrade,gi.courseid,gi.itemname,gi.id, g.timemodified as tm
                      FROM {$CFG->prefix}grade_items gi
                           JOIN {$CFG->prefix}grade_grades g      ON g.itemid = gi.id
                           JOIN {$CFG->prefix}user u              ON u.id = g.userid
                           JOIN {$CFG->prefix}role_assignments ra ON ra.userid = u.id
                     WHERE g.finalgrade IS NOT NULL 
		     	and gi.itemname IS NOT NULL
			   AND u.id = ?
                  	ORDER BY tm
			LIMIT $limit";


	    $sum_array = array();
		$params = array ($uid);
	    if ($sums = $DB->get_records_sql($SQL, $params)) {

		    $i = 0;
		    foreach ($sums as $sum)
		    {
			 if (! $grade_grade = grade_grade::fetch(array('itemid'=>$sum->id,'userid'=>$uid))) {
				$grade_grade = new grade_grade();
				$grade_grade->userid = $this->user->id;
				$grade_grade->itemid = $grade_object->id;
			    }

			    $grade_item = $grade_grade->load_grade_item();

			    $scale = $grade_item->load_scale();
			    $formatted_grade = grade_format_gradevalue($sum->finalgrade, $grade_item, true, GRADE_DISPLAY_TYPE_REAL);

				$t['itemname']= $sum->itemname;
				$t['finalgrade']= $formatted_grade;
				$t['average']= $this->get_average_grade ($grade_grade->itemid);
				$tareas[] = $t;
			    $i++;
		    }

			return $tareas;
	    }

		return array();
    }
    /**
      * Retursn number of enrolled students in a course
      * 
      * @param int $id Course identifier
      */
    function get_course_students_no($id) 
	{
		global $CFG;

		$context = context_course::instance($id);
		/* 5 indica estudiantes (table mdl_role) */
		$alumnos = get_role_users(5 , $context);

		return count($alumnos);
    }

    function get_course_students($id, $search = '') 
	{
		global $CFG;

		$context = context_course::instance($id);
		/* 5 indica estudiantes (table mdl_role) */
		$alumnos = get_role_users(5 , $context);

		$students = array ();

		foreach ($alumnos as $alumno)
		{
			if ($search)
            {
                if ( (stripos ($alumno->username, $search) === false)
                        && ( stripos ($alumno->firstname, $search) === false)
                        && (stripos ($alumno->lastname, $search) === false)
                        && (stripos ($alumno->idnumber, $search) === false)
                    )
                    continue;
            }


			$a['username'] = $alumno->username;
			$a['firstname'] = $alumno->firstname;
			$a['lastname'] = $alumno->lastname;
			$a['id'] = $alumno->id;

			$students[] = $a;
		}

		return ($students);
    }


    /**
      * Returns upcoming events for a course
      * 
      * @param int $id Course identifier
      */
	function get_upcoming_events ($id, $username = '')
    {
        global $CFG, $DB;
 
        $id = addslashes ($id);
        $courseshown = $id;
        $coursestoload    = array($courseshown => $id);
        $groupeventsfrom = array($courseshown => 1);

		$filtercourse = $DB->get_records_list('course', 'id', $coursestoload);
 
        $true = true;
        if ($CFG->version >= 2011070100)
        {
            list($courses, $group, $user) = calendar_set_filters($filtercourse, $true);
            $courses = array ($id => $id);
 
            if ($username != '')
            {
                // Show only events for groups where user is a member
                $groups = groups_get_all_groups($id);
                $gs = array ();
                foreach ($groups as $group)
                {
                    $found = false;
                    // Check is user is a member of the group
                    $members = $this->get_group_members ($group->id);
                    foreach ($members as $member)
                    {
                        if ($member['username'] == $username)
                        {
                            $found = true;
                            break;
                        }
                    }
                    if ($found)
                        $gs[$group->id] = $group->id;
                }
            }
 
            $events = calendar_get_upcoming($courses, $gs, false,
                 CALENDAR_DEFAULT_UPCOMING_LOOKAHEAD,
                 CALENDAR_DEFAULT_UPCOMING_MAXEVENTS);
        }
        else
        {
            calendar_set_filters($courses, $group, $user, $filtercourse, $groupeventsfrom, true);
            $events = calendar_get_upcoming($courses, $group, $user,
                 CALENDAR_UPCOMING_DAYS,
                 CALENDAR_UPCOMING_MAXEVENTS);
        }
 
        $data = array ();
        foreach ($events as $r)
        {
			$e = array ();
            $e['name'] = $r->name;
            $e['timestart'] = $r->timestart;
            $e['courseid'] = $r->courseid;
 
            $data[$i] = $e;
            $i++;
        }
 
        return $data;
    }
    /**
      * Returns last news for a course
      * 
      * @param int $id Course identifier
      */
    function get_news_items ($id) 
	{
		global $CFG, $DB;

		$conditions = array ('id' => $id);
        $COURSE = $DB->get_record('course', $conditions);

		if (!$forum = forum_get_course_forum($COURSE->id, 'news')) {
			return array ();
		}

		$modinfo = get_fast_modinfo($COURSE);
		if (empty($modinfo->instances['forum'][$forum->id])) {
			return array ();
		}
		$cm = $modinfo->instances['forum'][$forum->id];


        /// Get all the recent discussions we're allowed to see

		if (! $discussions = forum_get_discussions($cm, 'p.modified DESC', false, -1, $COURSE->newsitems) ) 
		{
			return array ();
		}

		$data = array ();
		foreach ($discussions as $r)
		{
			$e['discussion'] = $r->discussion;
			$e['subject'] = $r->subject;
			$e['timemodified'] = $r->timemodified;

			$data[] = $e;
		}

		return $data;
	}

    function get_my_news ($username)
    {
        global $CFG, $DB;

        $username = utf8_decode ($username);
        $username = strtolower ($username);

        $user = get_complete_user_data ('username', $username);

        if (!$user)
            return array();

        $courses = enrol_get_users_courses ($user->id, true);

        $news = array ();
        foreach ($courses as $c)
        {
            $course_news['remoteid'] = $c->id;
            $course_news['fullname'] = $c->fullname;
            $course_news['news'] = $this->get_news_items ($c->id);

            $news[] = $course_news;
        }

        return $news;
    }



    /**
      * Returns daily stats for a course
      * 
      * @param int $id Course identifier
      */
    function get_course_daily_stats ($id) 
	{
        global $CFG, $DB;
		$id = addslashes ($id);

        $query =
		"select * from {$CFG->prefix}stats_daily 
		where courseid='$id'
		and roleid='5'
		and stattype='activity';
                ";

		$stats = $DB->get_records_sql($query);

		$data = array ();
		if ($stats)
		{
			foreach ($stats as $r)
			{
				$e['stat1'] = $r->stat1;
				$e['stat2'] = $r->stat2;
				$e['timeend'] = $r->timeend;

				$data[$i] = $e;
				$i++;
			}
		}
		else
		{
			$e['stat1'] = 0;
			$e['stat2'] = 0;
			$e['timeend'] = 0;

			$data[] = $e;
		}

		return $data;
    }

    /**
      * Return access daily stats
      * 
      */
    function get_site_last_week_stats () 
	{
        global $CFG, $DB;

        $query =
		"select * from {$CFG->prefix}stats_weekly
		where stattype='logins' 
		order by timeend DESC LIMIT 1;
                ";

		$stats = $DB->get_records_sql($query);

		$data = array ();
		if ($stats)
		{
			foreach ($stats as $r)
			{
				$e['stat1'] = $r->stat1;
				$e['stat2'] = $r->stat2;

				$data[$i] = $e;
				$i++;
			}
		}
		else
		{
			$e['stat1'] = 0;
			$e['stat2'] = 0;

			$data[] = $e;
		}

		return $data;
    }

    /**
      * Returns grading system for a course
      * 
      * @param int $id Course identifier
      */
    function get_course_grade_categories ($id) 
	{
        global $CFG, $DB;
        $query =
			"select {$CFG->prefix}grade_categories.fullname, {$CFG->prefix}grade_items.grademin, {$CFG->prefix}grade_items.grademax, {$CFG->prefix}grade_categories.id
			from {$CFG->prefix}grade_categories, {$CFG->prefix}grade_items 
			where {$CFG->prefix}grade_categories.id = {$CFG->prefix}grade_items.iteminstance 
			and {$CFG->prefix}grade_items.courseid='$id' and itemtype='category';
                ";
		
		$cats = $DB->get_records_sql($query);

		$data = array ();
		foreach ($cats as $r)
		{
			$e['fullname'] = $r->fullname;
			$e['grademax'] = $r->grademax;
			$e['id'] = $r->id;

			$data[] = $e;
		}

		return $data;
    }

	function get_course_grade_categories_and_items ($id)
    {
        global $CFG, $DB;
        $query =
            "select {$CFG->prefix}grade_categories.fullname, {$CFG->prefix}grade_items.id, {$CFG->prefix}grade_items.grademin, {$CFG->prefix}grade_items.grademax, {$CFG->prefix}grade_categories.id
            from {$CFG->prefix}grade_categories, {$CFG->prefix}grade_items 
            where {$CFG->prefix}grade_categories.id = {$CFG->prefix}grade_items.iteminstance 
            and {$CFG->prefix}grade_items.courseid='$id' and itemtype='category';
                ";

        $cats = $DB->get_records_sql($query);

        $data = array ();
        foreach ($cats as $r)
        {
            $e['fullname'] = $r->fullname;
            $e['grademax'] = $r->grademax;

            $cat_id = $r->id;
            $query =
                "select * 
                from  {$CFG->prefix}grade_items 
                where categoryId = '$cat_id' 
                    ";

            $items = $DB->get_records_sql($query);
            $category_items = array ();
            foreach ($items as $item)
            {
                $category_item['name'] = $item->itemname;

				$category_item['id'] = $item->id;
				$rubrics = $this->get_rubrics ($item->id);
				if (count ($rubrics['definitions']))
					$category_item['has_rubrics'] = true;
				else
					$category_item['has_rubrics'] = false;


                switch ($item->itemmodule)
                {
					case '':
						if ($item->itemtype == 'manual')
							$category_item['due'] = $item->locktime;
						break;
                    case 'quiz':
                        $conditions = array ('id' => $item->iteminstance);
                        $quiz = $DB->get_record('quiz',$conditions);
                        $category_item['due'] = $quiz->timeclose;
                        break;
                    case 'assignment':
                        $conditions = array ('id' => $item->iteminstance);
                        $assignment = $DB->get_record('assignment',$conditions);
                        $category_item['due'] = $assignment->timedue;
                        break;
                    case 'assign':
                        $conditions = array ('id' => $item->iteminstance);
                        $assignment = $DB->get_record('assign',$conditions);
                        $category_item['due'] = $assignment->duedate;
                        break;
                    case 'scorm':
                        $conditions = array ('id' => $item->iteminstance);
                        $assignment = $DB->get_record('scorm',$conditions);
                        $category_item['due'] = $assignment->timeclose;
                        break;
                    case 'forum':
                        $conditions = array ('id' => $item->iteminstance);
                        $assignment = $DB->get_record('forum',$conditions);
                        $category_item['due'] = $assignment->assesstimefinish;
                        break;
                    case 'lesson':
                        $conditions = array ('id' => $item->iteminstance);
                        $assignment = $DB->get_record('lesson',$conditions);
                        $category_item['due'] = $assignment->deadline;
                        break;
                    case 'turnitintool':
                        $conditions = array ('id' => $item->iteminstance);
                        $assignment = $DB->get_record('turnitintool',$conditions);
                        $category_item['due'] = $assignment->defaultdtdue;
                        break;
                    case 'bigbluebuttonbn':
                        $conditions = array ('id' => $item->iteminstance);
                        $assignment = $DB->get_record('bigbluebuttonbn',$conditions);
                        $category_item['due'] = $assignment->timedue;
                        break;
					default:
						$category_item['due'] = 0;
						break;
                }

                $category_items[] = $category_item;
            }

            $e['items'] = $category_items;

            $data[] = $e;
        }

        return $data;
    }

    function get_rubrics ($grade_item_id)
	{
        global $CFG, $DB;

        $conditions = array ('id' => $grade_item_id);
        $grade_item = $DB->get_record('grade_items',$conditions);

        $assign_data['assign_name'] = $grade_item->itemname;
        $assign_data['definitions'] = array();

        $conditions = array ('name' => $grade_item->itemmodule);
        $module = $DB->get_record('modules',$conditions);
        if (!$module)
            return $assign_data;

        $conditions = array ('course' => $grade_item->courseid, 'module' => $module->id, 'instance' => $grade_item->iteminstance);
        $cm = $DB->get_record('course_modules',$conditions);

        if (!$cm)
            return $assign_data;

		$context = context_module::instance($cm->id);

        $conditions = array ('contextid' => $context->id);
        $area = $DB->get_record('grading_areas',$conditions);

        if (!$area)
            return $assign_data;

        $conditions = array ('areaid' => $area->id);
        $definitions = $DB->get_records('grading_definitions',$conditions);

        $data = array ();
        foreach ($definitions as $definition)
        {
            $d['definition'] = $definition->name;
            $conditions = array ('definitionid' => $definition->id);
            $criteria = $DB->get_records('gradingform_rubric_criteria',$conditions);

            $d['criteria'] = array (); 
            foreach ($criteria as $c)
            {
                $data_criteria['description'] = $c->description;

                $conditions = array ('criterionid' => $c->id);
                $levels = $DB->get_records('gradingform_rubric_levels',$conditions);
                
                $data_levels = array ();
                foreach ($levels as $level)
                {
                    $dl['definition'] = $level->definition;
                    $dl['score'] = $level->score;

                    $data_levels[] = $dl;
                }
                $data_criteria['levels'] = $data_levels;


                $d['criteria'][] = $data_criteria;
            }

            $data[]  = $d;
        }

        $assign_data['definitions'] = $data;

        return $assign_data;
	}



function user_exists ($username)
{
	global $CFG, $DB;
	$username = utf8_decode ($username);
	$username = strtolower ($username);
	$conditions = array ('username' => $username);
	$user = $DB->get_record('user',$conditions);
	if ($user)
		return 1;
	return 0;
}

/*
function get_userinfo($username) {
	global $CFG, $DB;

	$username = utf8_decode ($username);
	$username = strtolower ($username);

	// Get user info from Joomla
	$juser_info = $this->call_method ("getUserInfo", $username, '');

	return $juser_info;
}
*/


function create_joomdle_user_additional ($username, $app)
{
	global $CFG, $DB;

	$conditions = array ('username' => $username);
	$user = $DB->get_record('user',$conditions);
	if (!$user)
		return 0;

	return $this->create_joomdle_user ($username, $app);
}


// Copy of create_user_record() in moodle/lib/moodlelib.php that removes password sync
// This used only when creating new accounts from Joomla
function create_joomdle_user_record($username, $password, $auth = 'joomdle') {
    global $CFG, $DB;
    require_once($CFG->dirroot.'/user/profile/lib.php');
    require_once($CFG->dirroot.'/user/lib.php');

    // Just in case check text case.
    $username = trim(core_text::strtolower($username));

    $authplugin = get_auth_plugin($auth);
    $customfields = $authplugin->get_custom_user_profile_fields();
    $newuser = new stdClass();
    if ($newinfo = $authplugin->get_userinfo($username)) {
        $newinfo = truncate_userinfo($newinfo);
        foreach ($newinfo as $key => $value) {
            if (in_array($key, $authplugin->userfields) || (in_array($key, $customfields))) {
                $newuser->$key = $value;
            }
        }
    }

    if (!empty($newuser->email)) {
        if (email_is_not_allowed($newuser->email)) {
            unset($newuser->email);
        }
    }

    if (!isset($newuser->city)) {
        $newuser->city = '';
    }

    $newuser->auth = $auth;
    $newuser->username = $username;

    // Fix for MDL-8480
    // user CFG lang for user if $newuser->lang is empty
    // or $user->lang is not an installed language.
    if (empty($newuser->lang) || !get_string_manager()->translation_exists($newuser->lang)) {
        $newuser->lang = $CFG->lang;
    }
    $newuser->confirmed = 1;
    $newuser->lastip = getremoteaddr();
    $newuser->timecreated = time();
    $newuser->timemodified = $newuser->timecreated;
    $newuser->mnethostid = $CFG->mnet_localhost_id;

    $newuser->id = user_create_user($newuser, false, false);

    // Save user profile data.
    profile_save_data($newuser);

	/*
    $user = get_complete_user_data('id', $newuser->id);
    if (!empty($CFG->{'auth_'.$newuser->auth.'_forcechangepassword'})) {
        set_user_preference('auth_forcepasswordchange', 1, $user);
    }
    // Set the password.
    update_internal_user_password($user, $password);
	*/

    // Trigger event.
    \core\event\user_created::create_from_userid($newuser->id)->trigger();

    return $newuser;
}


/**
* Creates a new Joomdle user
* XXX Also used to update user profile if the user already exists
* 
* @param string $username Joomla username
*/
function create_joomdle_user ($username, $app = '')
{
	global $CFG, $DB;

	$username = utf8_decode ($username);
	$username = strtolower ($username);
	/* Creamos el nuevo usuario de Moodle si no está creado */
	$conditions = array ('username' => $username);
	$user = $DB->get_record('user',$conditions);
	if (!$user)
		$user = $this->create_joomdle_user_record($username, "", "joomdle");

	// Get user info from Joomla
	$juser_info = $this->call_method ("getUserInfo", $username, $app);

	if (array_key_exists ('email', $juser_info))
		$email = $juser_info['email'];
	else  $email = '';
	if (array_key_exists ('firstname', $juser_info))
		$firstname = $juser_info['firstname'];
	else  $firstname = '';
	if (array_key_exists ('lastname', $juser_info))
		$lastname = $juser_info['lastname'];
	else  $lastname = '';
	if (array_key_exists ('city', $juser_info))
		$city = $juser_info['city'];
	else  $city = '';
	if (array_key_exists ('country', $juser_info))
		$country = $juser_info['country'];
	else  $country = '';
	if (array_key_exists ('lang', $juser_info))
		$lang = $juser_info['lang'];
	else  $lang = '';
	if (array_key_exists ('timezone', $juser_info))
		$timezone = $juser_info['timezone'];
	else  $timezone = '';
	if (array_key_exists ('phone1', $juser_info))
		$phone1 = $juser_info['phone1'];
	else  $phone1 = '';
	if (array_key_exists ('phone2', $juser_info))
		$phone2 = $juser_info['phone2'];
	else  $phone2 = '';
	if (array_key_exists ('address', $juser_info))
		$address = $juser_info['address'];
	else  $address = '';
	if (array_key_exists ('description', $juser_info))
		$description = $juser_info['description'];
	else  $description = '';
	if (array_key_exists ('institution', $juser_info))
		$institution = $juser_info['institution'];
	else  $institution = '';
	if (array_key_exists ('url', $juser_info))
		$url = $juser_info['url'];
	else  $url = '';

	if (array_key_exists ('icq', $juser_info))
		$icq = $juser_info['icq'];
	else  $icq = '';
	if (array_key_exists ('skype', $juser_info))
		$skype = $juser_info['skype'];
	else  $skype = '';
	if (array_key_exists ('aim', $juser_info))
		$aim = $juser_info['aim'];
	else  $aim = '';
	if (array_key_exists ('yahoo', $juser_info))
		$yahoo = $juser_info['yahoo'];
	else  $yahoo = '';
	if (array_key_exists ('msn', $juser_info))
		$msn = $juser_info['msn'];
	else  $msn = '';
	if (array_key_exists ('idnumber', $juser_info))
		$idnumber = $juser_info['idnumber'];
	else  $idnumber = '';
	if (array_key_exists ('department', $juser_info))
		$department = $juser_info['department'];
	else  $department = '';
	if (array_key_exists ('lastnamephonetic', $juser_info))
		$lastnamephonetic = $juser_info['lastnamephonetic'];
	else  $lastnamephonetic = '';
	if (array_key_exists ('firstnamephonetic', $juser_info))
		$firstnamephonetic = $juser_info['firstnamephonetic'];
	else  $firstnamephonetic = '';
	if (array_key_exists ('middlename', $juser_info))
		$middlename = $juser_info['middlename'];
	else  $middlename = '';
	if (array_key_exists ('alternatename', $juser_info))
		$alternatename = $juser_info['alternatename'];
	else  $alternatename = '';

	//XXX Maybe this can be optimized for a single DB call...$bool = update_record('user', addslashes_recursive($localuser)); en ment/aut.php
	if (!xmlrpc_is_fault($juser_info)) {

		/* Actualizamos la informacion del usuario recien creado con los datos de Joomla */
		$conditions = array ('id' => $user->id);
		if ($firstname)
			$DB->set_field('user', 'firstname', $firstname, $conditions);
		if ($lastname)
			$DB->set_field('user', 'lastname', $lastname, $conditions);
		if ($email)
			$DB->set_field('user', 'email', $email, $conditions);

		/* Set first access as now */
		$DB->set_field('user', 'firstaccess', time (), $conditions);
		/* Optional data in Joomla, only fill if has a value */
		if ($city)
			$DB->set_field('user', 'city', ($city), $conditions);
		if ($country)
			$DB->set_field('user', 'country', substr ($country, 0, 2), $conditions);
		//	$DB->set_field('user', 'country', $country, $conditions);
		if ($lang)
			$DB->set_field('user', 'lang', $lang, $conditions);
		if ($timezone)
			$DB->set_field('user', 'timezone', $timezone, $conditions);
		if ($phone1)
			$DB->set_field('user', 'phone1', ($phone1), $conditions);
		if ($phone2)
			$DB->set_field('user', 'phone2', ($phone2), $conditions);
		if ($address)
			$DB->set_field('user', 'address', ($address), $conditions);
		if ($description)
			$DB->set_field('user', 'description', ($description), $conditions);
		if ($institution)
			$DB->set_field('user', 'institution', ($institution), $conditions);
		if ($url)
			$DB->set_field('user', 'url', $url, $conditions);
		if ($icq)
			$DB->set_field('user', 'icq', $icq, $conditions);
		if ($skype)
			$DB->set_field('user', 'skype', $skype, $conditions);
		if ($aim)
			$DB->set_field('user', 'aim', $aim, $conditions);
		if ($yahoo)
			$DB->set_field('user', 'yahoo', $yahoo, $conditions);
		if ($msn)
			$DB->set_field('user', 'msn', $msn, $conditions);
		if ($idnumber)
			$DB->set_field('user', 'idnumber', $idnumber, $conditions);
		if ($department)
			$DB->set_field('user', 'department', $department, $conditions);
		if ($lastnamephonetic)
			$DB->set_field('user', 'lastnamephonetic', $lastnamephonetic, $conditions);
		if ($firstnamephonetic)
			$DB->set_field('user', 'firstnamephonetic', $firstnamephonetic, $conditions);
		if ($middlename)
			$DB->set_field('user', 'middlename', $middlename, $conditions);
		if ($alternatename)
			$DB->set_field('user', 'alternatename', $alternatename, $conditions);
	}

	/* Get user pic */
	if ((array_key_exists ('pic_url', $juser_info)) && ($juser_info['pic_url']))
	{
		if ($juser_info['pic_url'] != 'none')
		{
			$joomla_url = get_config ('auth/joomdle', 'joomla_url');
			if (strncmp ($juser_info['pic_url'], 'http', 4) != 0)
				$pic_url = $joomla_url.'/'.$juser_info['pic_url'];  // Only add joomla_url if it is not a full URL already
			else $pic_url = $juser_info['pic_url'];

			$pic = $this->get_file ($pic_url);
			if ($pic)
			{
				//$pic = file_get_contents ($pic_url, false, NULL);
				$pic = $this->get_file_curl ($pic_url);
				$tmp_file = $CFG->dataroot.'/temp/'.'tmp_pic';
				file_put_contents ($tmp_file, $pic);

				$user = get_complete_user_data('username', $username); // We need this to get user id
				$context = context_user::instance($user->id);
				process_new_icon($context, 'user', 'icon', 0, $tmp_file);

				$conditions = array ('id' => $user->id);
				$DB->set_field('user', 'picture', 1, $conditions);
			}
		}
	}


	/* Custom fields */
    if ($fields = $DB->get_records('user_info_field')) {
		foreach ($fields as $field)
		{
			if ((array_key_exists ('cf_'.$field->id, $juser_info)) && ($juser_info['cf_'.$field->id]))
			{
				$data = new stdClass();
				$data->fieldid = $field->id;
				$data->data = $juser_info['cf_'.$field->id];
				$data->userid = $user->id;
				/* update custom field */
				 if ($dataid = $DB->get_field('user_info_data', 'id', array('userid'=>$user->id, 'fieldid'=>$data->fieldid)))
				 {
						$data->id = $dataid;
						$DB->update_record('user_info_data', $data);
				} else {
						$DB->insert_record('user_info_data', $data);
				}

			}
		}
	}

	return 1;
}

function create_moodle_only_user ($user_data)
{
    global $CFG, $DB;

    $username = $user_data['username'];
    $username = utf8_decode ($username);
    $username = strtolower ($username);
    /* Creamos el nuevo usuario de Moodle si no está creado */
    $conditions = array ('username' => $username);
    $user = $DB->get_record('user',$conditions);
    if (!$user)
        $user = create_user_record($username, "", "manual");

    if (array_key_exists ('password', $user_data))
        $password = utf8_decode ($user_data['password']);
    else  $password = '';
    if (array_key_exists ('email', $user_data))
        $email = utf8_decode ($user_data['email']);
    else  $email = '';
    if (array_key_exists ('firstname', $user_data))
        $firstname = utf8_decode ($user_data['firstname']);
    else  $firstname = '';
    if (array_key_exists ('lastname', $user_data))
        $lastname = utf8_decode ($user_data['lastname']);
    else  $lastname = '';
    if (array_key_exists ('city', $user_data))
        $city = utf8_decode ($user_data['city']);
    else  $city = '';
	if (array_key_exists ('country', $user_data))
		$country = utf8_decode ($user_data['country']);
	else  $country = '';

    $conditions = array ('id' => $user->id);
    if ($firstname)
        $DB->set_field('user', 'firstname', $firstname, $conditions);
    if ($lastname)
        $DB->set_field('user', 'lastname', $lastname, $conditions);
    if ($email)
        $DB->set_field('user', 'email', $email, $conditions);
    if ($city)
        $DB->set_field('user', 'city', ($city), $conditions);
	if ($country)
		$DB->set_field('user', 'country', $country, $conditions);

    update_internal_user_password($user, $password);

}

function search_courses ($text, $phrase, $ordering, $limit)
{
	global $CFG, $DB;

	$text = utf8_decode ($text);
	$wheres = array();
	switch ($phrase) 
	{
			case 'exact':
					$text           = '\'%'.$text.'%\'';
					$wheres2        = array();
					$wheres2[]      = 'co.fullname LIKE '.$text;
					$wheres2[]      = 'co.shortname LIKE '.$text;
					$wheres2[]      = 'co.summary LIKE '.$text;
					$where          = '(' . implode( ') OR (', $wheres2 ) . ')';
					break;

			case 'all':
			case 'any':
			default:
					$words = explode( ' ', $text );
					$wheres = array();
					foreach ($words as $word) 
					{
	//					$word           = '\'%'.$DB->search_escape_string( $word).'%\'';
						$word           = '\'%'.$word.'%\'';
						$wheres2        = array();
						$wheres2[]      = 'co.fullname LIKE '.$word;
						$wheres2[]      = 'co.shortname LIKE '.$word;
						$wheres2[]      = 'co.summary LIKE '.$word;
						$wheres[]       = implode( ' OR ', $wheres2 );
					}
					$where = '(' . implode( ($phrase == 'all' ? ') AND (' : ') OR ('), $wheres ) . ')';
					break;
	}

	switch ( $ordering ) 
	{
			case 'alpha':
					$order = 'co.fullname ASC';
					break;

			case 'category':
					$order = 'ca.name ASC, co.fullname ASC';
					break;

			case 'newest':
					$order = 'co.startdate DESC';
					break;
			case 'oldest':
					$order = 'co.startdate ASC';
					break;
			case 'popular':
			default:
					$order = 'co.fullname DESC';
	}

	$query =
	    "SELECT
		co.id          AS remoteid,
		ca.id          AS cat_id,
		ca.name        AS cat_name,
		ca.description AS cat_description,
		co.sortorder,
		co.fullname,
		co.shortname,
		co.idnumber,
		co.summary,
		co.startdate
	    FROM
		{$CFG->prefix}course_categories ca
	    JOIN
		{$CFG->prefix}course co ON
		ca.id = co.category
	    WHERE
		co.visible = '1' AND
		$where
	    ORDER BY
		$order
	    LIMIT $limit
		";

	$results =  $DB->get_records_sql($query);
	$options['noclean'] = true;
	$data = array ();
	foreach ($results as $r)
	{
		$c = get_object_vars ($r);
		$c['fullname'] = format_string($c['fullname']);
		$c['summary'] = format_text($c['summary'], FORMAT_MOODLE, $options);
		$c['cat_name'] = format_string($c['cat_name']);
		$context = context_coursecat::instance($c['cat_id']);
		$c['cat_description'] = file_rewrite_pluginfile_urls ($c['cat_description'], 'pluginfile.php', $context->id, 'coursecat', 'description', NULL);
		$c['cat_description'] = str_replace ('pluginfile.php', '/auth/joomdle/pluginfile_joomdle.php', $c['cat_description']);
		$c['cat_description'] = format_text($c['cat_description'], FORMAT_MOODLE, $options);
		$data[] = $c;
	}

	return $data;
}

function search_categories ($text, $phrase, $ordering, $limit)
{
	global $CFG, $DB;

	$wheres = array();
	switch ($phrase) 
	{
			case 'exact':
					$text           = '\'%'.$text.'%\'';
					$wheres2        = array();
					$wheres2[]      = 'ca.name LIKE '.$text;
					$wheres2[]      = 'ca.description LIKE '.$text;
					$where          = '(' . implode( ') OR (', $wheres2 ) . ')';
					break;

			case 'all':
			case 'any':
			default:
					$words = explode( ' ', $text );
					$wheres = array();
					foreach ($words as $word) {
						$word           = '\'%'.$word.'%\'';
						$wheres2        = array();
						$wheres2[]      = 'ca.name LIKE '.$word;
						$wheres2[]      = 'ca.description LIKE '.$word;
						$wheres[]       = implode( ' OR ', $wheres2 );
					}
					$where = '(' . implode( ($phrase == 'all' ? ') AND (' : ') OR ('), $wheres ) . ')';
					break;
	}

	switch ( $ordering ) 
	{
                case 'alpha':
                case 'category':
                        $order = 'ca.name ASC';
                        break;
                case 'newest':
                case 'oldest':
                case 'popular':
                default:
                        $order = 'ca.name DESC';
	}

	$query =
	    "SELECT
		ca.id          AS cat_id,
		ca.name        AS cat_name,
		ca.description AS cat_description
	    FROM
		{$CFG->prefix}course_categories ca
	    WHERE
		$where
	    ORDER BY
		$order
	    LIMIT $limit
		";

	$results =  $DB->get_records_sql($query);

	$options['noclean'] = true;
	$data = array ();
	foreach ($results as $r)
	{
		$c = get_object_vars ($r);
		$c['cat_name'] = format_string($c['cat_name']);
		$c['cat_description'] = format_text($c['cat_description'], FORMAT_MOODLE, $options);
		$data[] = $c;
	}

	return $data;

}

function search_topics ($text, $phrase, $ordering, $limit = 50)
{
	global $CFG, $DB;

      $wheres = array();
        switch ($phrase) {
                case 'exact':
						$text           = '\'%'.$text.'%\'';
                        $where      = 'cs.summary LIKE '.$text;
                        break;

                case 'all':
                case 'any':
                default:
						$words = explode( ' ', $text );
                        $wheres = array();
                        foreach ($words as $word) 
						{
							$word           = '\'%'.$word.'%\'';
							$wheres[]      = 'cs.summary LIKE '.$word;
                        }
                        $where = '(' . implode( ($phrase == 'all' ? ') AND (' : ') OR ('), $wheres ) . ')';
                        break;
        }
	$where .= " and cs.visible = 1";

	switch ( $ordering ) {
                case 'alpha':
                        $order = 'cs.summary ASC';
                        break;
                case 'category':
                        $order = 'co.id ASC';
                        break;
                case 'newest':
                        $order = 'co.id ASC, cs.section DESC';
                        break;
                case 'oldest':
                        $order = 'co.id ASC, cs.section ASC';
                        break;
                case 'popular':
                default:
                        $order = 'cs.summary DESC';
        }

	/* REMEMBER: For get_records_sql First field in query must be UNIQUE!!!!! */
	$query =
	    "SELECT cs.id,
		co.id          AS remoteid,
		co.fullname,
		cs.course,
		cs.section,
		cs.summary,
		ca.id as cat_id,
		ca.name as cat_name
	    FROM
		{$CFG->prefix}course_sections cs 
	JOIN {$CFG->prefix}course co  ON
		co.id = cs.course 
	LEFT JOIN {$CFG->prefix}course_categories ca  ON
		ca.id = co.category
	    WHERE
		$where
	    ORDER BY
		$order
	    LIMIT $limit
		";
	
	$results =  $DB->get_records_sql($query);

	$options['noclean'] = true;
	$data = array ();
	foreach ($results as $r)
	{
		$c = get_object_vars ($r);
		$c['fullname'] = format_string($c['fullname']);
		$c['summary'] = format_text($c['summary'], FORMAT_MOODLE, $options);
		$c['cat_name'] = format_string($c['cat_name']);
		$data[] = $c;
	}

	return $data;

}

// $courses: course idnumbers separated by commas
// $groups: group names separated by commas
function multiple_enrol_and_addtogroup ($username, $courses, $groups, $roleid = 5)
{
        global $CFG, $DB;

        $username = utf8_decode ($username);
        $username = strtolower ($username);

        $conditions = array ('username' => $username);
        $user = $DB->get_record('user',$conditions);

        $c = explode (',', $courses);
        $g = explode (',', $groups);
        foreach ($c as $idnumber)
        {
         //   $conditions = array ('idnumber' => $idnumber);
         //   $course = $DB->get_record ('course', $conditions);

            $query = "SELECT idnumber, id
                        FROM {$CFG->prefix}course
                        WHERE idnumber LIKE '%$idnumber%'";

            $records = $DB->get_records_sql($query);

            $group_name = array_shift ($g);

            foreach ($records as $r)
            {
                $codes = explode (',', $r->idnumber);

                foreach ($codes as $code)
                {
                    $code = trim ($code);
                    if ($code == $idnumber)
                    {
                        $conditions = array ('id' => $r->id);
                        $course = $DB->get_record ('course', $conditions);


                        if (!$course)
                            continue;

						$this->enrol_user_change_role ($username, $course->id, $roleid);

                        // Group

						// If user already in a group, do nothing
						$user_groups = groups_get_user_groups ($course->id, $user->id);
                        if (count ($user_groups[0]) > 0)  // Already in a group
                            continue;

						// add first char from course code
						$char = substr ($code, 0, 1);
						$modified_group_name = $char.$group_name;
                        $conditions = array ('name' => $modified_group_name, 'courseid' => $course->id);
                        $group = $DB->get_record ('groups', $conditions);

                        if (!$group)
                        {
                            // Create group if it does not exist

                            $data->courseid = $course->id;
							$data->name = $modified_group_name;

                            groups_create_group ($data);
                        }

                        $conditions = array ('name' => $modified_group_name, 'courseid' => $course->id);
                        $group = $DB->get_record ('groups', $conditions);

                        groups_add_member ($group->id, $user->id);
                    }
                }
            }
        }
}

function multiple_enrol_to_course_and_group ($username, $courses, $roleid = 5)
{
        global $CFG, $DB;

        $username = utf8_decode ($username);
        $username = strtolower ($username);

        $conditions = array ('username' => $username);
        $user = $DB->get_record('user',$conditions);

        foreach ($courses as $course)
        {
			$conditions = array ('id' => $course['id']);
			$course_db = $DB->get_record ('course', $conditions);

			if (!$course_db)
				continue;

			$this->enrol_user ($username, $course_db->id, $roleid);

			// Group
			groups_add_member ($course['group_id'], $user->id);
        }
}



function enrol_user_change_role ($username, $course_id, $roleid = 5)
{
	global $CFG, $DB, $PAGE;

	$username = utf8_decode ($username);
	$username = strtolower ($username);
	/* Create the user before if it is not created yet */
	$conditions = array ('username' => $username);
	$user = $DB->get_record('user',$conditions);
	if (!$user)
		$this->create_joomdle_user ($username);

	$user = $DB->get_record('user',$conditions);
	$conditions = array ('id' => $course_id);
	$course = $DB->get_record('course', $conditions);

	if (!$course)
		return 0;

	// First, check if user is already enroled but suspended, so we just need to enable it

	$conditions = array ('courseid' => $course_id, 'enrol' => 'manual');
	$enrol = $DB->get_record('enrol', $conditions);

	if (!$enrol)
		return 0;

	$conditions = array ('username' => $username);
	$user = $DB->get_record('user', $conditions);

	if (!$user)
		return 0;

	$conditions = array ('enrolid' => $enrol->id, 'userid' => $user->id);
	$ue = $DB->get_record('user_enrolments', $conditions);

    // Update role info
    if ($ue)
    {
        $conditions = array ('contextid' => $context->id, 'userid' => $user->id);
        $ra = $DB->get_record('role_assignments', $conditions);

		if (!$ra)
			return 1;
		//  XXX Comprobar si ya esta en profe

        $ra->roleid = $roleid;
        $DB->update_record('role_assignments', $ra);
        return 1;
    }



	if ($CFG->version >= 2011061700)
		$manager = new course_enrolment_manager($PAGE, $course);
	else
		$manager = new course_enrolment_manager($course);

	$instances = $manager->get_enrolment_instances();
	$plugins = $manager->get_enrolment_plugins();
	$enrolid = 1; //manual

	$today = time();
	$today = make_timestamp(date('Y', $today), date('m', $today), date('d', $today), 0, 0, 0);
	$timestart = $today;
	$timeend = 0;


	$found = false;
	foreach ($instances as $instance)
	{
		if ($instance->enrol == 'manual')
		{
			$found = true;
			break;
		}
	}

	if (!$found)
		return 0;

	$plugin = $plugins['manual'];

	if ( $instance->enrolperiod)
		$timeend   = $timestart + $instance->enrolperiod;

	$plugin->enrol_user($instance, $user->id, $roleid, $timestart, $timeend);

	return 1;
}


    function get_course_groups ($id)
    {
        $groups = groups_get_all_groups($id);

        $rdo = array ();

        foreach ($groups as $group)
        {
            $g['id'] = $group->id;
            $g['name'] = $group->name;
            $g['description'] = $group->description;

            $rdo[] = $g;
        }

        return $rdo;
    }

    function get_group_members ($group_id, $search = '')
    {
        $users = groups_get_members ($group_id);

        $rdo = array ();
        foreach ($users as $u)
        {
			if ($search)
            {
                if ( (stripos ($u->username, $search) === false)
                        && ( stripos ($u->firstname, $search) === false)
                        && (stripos ($u->lastname, $search) === false)
                        && (stripos ($u->idnumber, $search) === false)
                    )
                    continue;
            }

            $member['id'] = $u->id;
            $member['firstname'] = $u->firstname;
            $member['lastname'] = $u->lastname;
            $member['username'] = $u->username;

            $rdo[] = $member;
        }

        return $rdo;
    }

    function get_courses_and_groups ()
    {
        $courses = $this->list_courses ();

        $c = array ();
        foreach ($courses as $course)
        {
            $course_data['remoteid'] = $course['remoteid'];
            $course_data['fullname'] = $course['fullname'];

            $course_data['groups'] = $this->get_course_groups ($course['remoteid']);

            $c[] = $course_data;
        }

        return $c;
    }


/*
// $courses: course shortnames separated by commas
function enrol_user_multiple_by_shortname ($username, $courses, $roleid = 5)
{
        global $CFG, $DB;

        $c = explode (',', $courses);
        foreach ($c as $shortname)
        {
            $conditions = array ('shortname' => $shortname);
            $course = $DB->get_record ('course', $conditions);

            $this->enrol_user ($username, $course->id, $roleid);
        }

        return 0;
}
*/

function multiple_enrol ($username, $courses, $roleid = 5)
{
        global $CFG, $DB;

		$username = utf8_decode ($username);
		$username = strtolower ($username);

        $conditions = array ('username' => $username);
        $user = $DB->get_record('user',$conditions);

		 if (!$user)
            $this->create_joomdle_user ($username);
        $user = $DB->get_record('user',$conditions);

		if (!$user)
			return;

        foreach ($courses as $c)
        {
            $conditions = array ('id' => $c['id']);
            $course = $DB->get_record ('course', $conditions);

			if (!$course)
				continue;

            $this->enrol_user ($username, $course->id, $roleid);
        }

        return 0;
}

function enrol_user ($username, $course_id, $roleid = 5, $timestart = 0, $timeend = 0)
{
    global $CFG, $DB, $PAGE;

    $username = utf8_decode ($username);
    $username = strtolower ($username);
    /* Create the user before if it is not created yet */
    $conditions = array ('username' => $username);
    $user = $DB->get_record('user',$conditions);
    if (!$user)
        $this->create_joomdle_user ($username);

    $user = $DB->get_record('user',$conditions);
    $conditions = array ('id' => $course_id);
    $course = $DB->get_record('course', $conditions);

    if (!$course)
        return 0;

    // Get enrol start and end dates of manual enrolment plugin
    if ($CFG->version >= 2011061700)
        $manager = new course_enrolment_manager($PAGE, $course);
    else
        $manager = new course_enrolment_manager($course);

    $instances = $manager->get_enrolment_instances();
    $plugins = $manager->get_enrolment_plugins();
    $enrolid = 1; //manual

	if (!$timestart)
    {
        // Set NOW as enrol start if not one defined
        $today = time();
        $today = make_timestamp(date('Y', $today), date('m', $today), date('d', $today), date ('H', $today), date ('i', $today), date ('s', $today));
        $timestart = $today; 
    }
  //  $timeend = 0;

    $found = false;
    foreach ($instances as $instance)
    {
        if ($instance->enrol == 'manual')
        {
            $found = true;
            break;
        }
    }

    if (!$found)
        return 0;

    $plugin = $plugins['manual'];

    if ( $instance->enrolperiod != 0)
 		$timeend   = $timestart + $instance->enrolperiod;

    // First, check if user is already enroled but suspended, so we just need to enable it

    $conditions = array ('courseid' => $course_id, 'enrol' => 'manual');
    $enrol = $DB->get_record('enrol', $conditions);

    if (!$enrol)
        return 0;

    $conditions = array ('username' => $username);
    $user = $DB->get_record('user', $conditions);

    if (!$user)
        return 0;

    $conditions = array ('enrolid' => $enrol->id, 'userid' => $user->id);
    $ue = $DB->get_record('user_enrolments', $conditions);

    if ($ue)
    {
        // User already enroled
        // Can be suspended, or maybe enrol time passed
        // Just activate enrolment and set new dates
        $ue->status = 0; //active
        $ue->timestart = $timestart;
        $ue->timeend = $timeend;
        $ue->timemodified = $timestart;
        $DB->update_record('user_enrolments', $ue);
        return 1;
    }

    $plugin->enrol_user($instance, $user->id, $roleid, $timestart, $timeend);

    return 1;
}


// Assigns role to user, to appear in "other users" course section
function add_user_role ($username, $course_id, $role_id)
{
    global $DB;

    $username = utf8_decode ($username);
    $username = strtolower ($username);

    $params = array ('username' => $username);
    $user = $DB->get_record('user',$params);
    if (!$user)
        return;

    $params = array ('id' => $course_id);
    $course = $DB->get_record('course', $params);

	$context = context_course::instance($course->id);

    if (!$context)
        return;

    if (!role_assign($role_id, $user->id, $context->id)) {
        return;
    }
}

function remove_user_role ($username, $course_id, $role_id)
{
    global $DB;

    $username = utf8_decode ($username);
    $username = strtolower ($username);

    $params = array ('username' => $username);
    $user = $DB->get_record('user',$params);
    if (!$user)
        return;

    $params = array ('id' => $course_id);
    $course = $DB->get_record('course', $params);

	$context = context_course::instance($course->id);

    if (!$context)
        return;

    if (!role_unassign($role_id, $user->id, $context->id)) {
        return;
    }
}

function add_system_role ($username, $role_id)
{
	global $DB;

	$username = utf8_decode ($username);
	$username = strtolower ($username);

	$params = array ('username' => $username);
	$user = $DB->get_record('user',$params);
	if (!$user)
		return;

	$context = context_system::instance();

	if (!$context)
		return;

	role_assign($role_id, $user->id, $context->id);

	return true;
}


function update_course_enrolments_dates ($course_id,  $timestart, $timeend)
{   
    global $CFG, $DB;

    $context = context_course::instance($course_id);
    /* 5 indica estudiantes (table mdl_role) */
    $students = get_role_users(5 , $context);

    $conditions = array ('courseid' => $course_id, 'enrol' => 'manual');
    $enrol = $DB->get_record('enrol', $conditions);

    if (!$enrol)
        return 0;

    foreach ($students as $user)
    {
        $conditions = array ('enrolid' => $enrol->id, 'userid' => $user->id);
        $ue = $DB->get_record('user_enrolments', $conditions);

        if ($ue)
        {
            // User already enroled
            // Can be suspended, or maybe enrol time passed
            // Just activate enrolment and set new dates
            $ue->status = 0; //active
            $ue->timestart = $timestart;
            $ue->timeend = $timeend;
            $ue->timemodified = $timestart;
            $DB->update_record('user_enrolments', $ue);
            return 1;
        }
    }   
}


function get_cat_name ($cat_id)
{
	global $CFG, $DB;

	$cat_id = addslashes ($cat_id);

	$query = "SELECT name
		FROM  {$CFG->prefix}course_categories
		WHERE id = '$cat_id';";

	$rdo = $DB->get_records_sql($query);
	$row = (reset ($rdo));
	return format_string ($row->name);
}

function get_my_courses_grades ($username)
{
	$i = 0;
	$rdo = array ();
	$username = utf8_decode ($username);
	$username = strtolower ($username);
	$user = get_complete_user_data ('username', $username);
	$cursos = enrol_get_users_courses ($user->id);
	foreach ($cursos as $curso)
	{
		$tareas = $this->get_user_grades ($username, $curso->id);
		$sum = 0;
		$n = count ($tareas);
		$rdo[$i]['id'] = $curso->id;
		$rdo[$i]['fullname'] = $curso->fullname;
		$rdo[$i]['cat_id'] = $curso->category;
		$rdo[$i]['cat_name'] = $this->get_cat_name ($curso->category);
		if ($n)
		{
			foreach ($tareas as  $tarea)
				$sum += $tarea['finalgrade'];
			$rdo[$i]['avg'] = $sum/$n;
		}
		else $rdo[$i]['avg'] = 0;
		$i++; 
	}
	return $rdo;
}

function get_moodle_users ($limitstart, $limit, $order, $order_dir, $search )
{
	global $CFG, $DB;

	/* Don't show admins and guests */
	$admins = get_admins();
	foreach ($admins as $admin)
	{
		$a[] = $admin->id;
	}
	$a[] = 1; //Guest user
	$userlist = "'".implode("','", $a)."'";

	if ($limit)
		$limit_c = " LIMIT $limitstart, $limit";

	// kludge for ordering by name
	if ($order == 'name')
		$order = 'firstname, lastname';

	if ($order != "")
		$order_c = "  ORDER BY $order $order_dir";
	else $order_c = "";

	if ($search)
	{
		/*
		$users = $DB->get_records_sql("SELECT id, username, email,  firstname, lastname ,auth
						FROM {$CFG->prefix}user
						WHERE deleted = 0
						AND((username like '%$search%') OR (email like '%$search%') OR (firstname like '%$search%') OR (lastname like '%$search%'))
						$order_c
						$limit_c");
*/

		$params = array();
		$likeu = $DB->sql_like('username', '?', false);
		$params[] = "%$search%";
		$likee = $DB->sql_like('email', '?', false);
		$params[] = "%$search%";
		$likef = $DB->sql_like('firstname', '?', false);
		$params[] = "%$search%";
		$likel = $DB->sql_like('lastname', '?', false);
		$params[] = "%$search%";

		$users = $DB->get_records_sql("SELECT id, username, email,  firstname, lastname ,auth
						FROM {$CFG->prefix}user
						WHERE deleted = 0
                       AND(({$likeu}) OR ({$likee}) OR ({$likef}) OR ({$likel})) 
						$order_c
						$limit_c", $params);
	}
	else
		$users = $DB->get_records_sql("SELECT id, username, email,  firstname, lastname ,auth
				FROM {$CFG->prefix}user
				WHERE deleted = 0
				$order_c
				$limit_c");

	$i = 0;
	$u = array ();
	foreach ($users as $user)
	{
		$u[$i] = get_object_vars ($user);
		if (in_array ($user->id, $a))
			$u[$i]['admin'] = '1';
		else $u[$i]['admin'] = '0';

		$u[$i]['name'] = $user->firstname . ' ' . $user->lastname;

		$i++;
	}
	return $u;
}

function get_moodle_users_number ($search = "")
{
	global $CFG, $DB;

	$search = addslashes ($search);
	/* Don't show admins and guets */
	$admins = get_admins();
	foreach ($admins as $admin)
	{
		$a[] = $admin->id;
	}
	$a[] = 1; //Guest user
	$userlist = "'".implode("','", $a)."'";

	if ($search)
	{
		$params = array();
		$likeu = $DB->sql_like('username', '?', false);
		$params[] = "%$search%";
		$likee = $DB->sql_like('email', '?', false);
		$params[] = "%$search%";
		$likef = $DB->sql_like('firstname', '?', false);
		$params[] = "%$search%";
		$likel = $DB->sql_like('lastname', '?', false);
		$params[] = "%$search%";

		$users = $DB->count_records_sql("SELECT count(id) as n
                        FROM {$CFG->prefix}user
                        WHERE deleted = 0
                        AND id not in ($userlist)
                       AND(({$likeu}) OR ({$likee}) OR ({$likef}) OR ({$likel}))", $params);

	}
	else
	{
		$users = $DB->count_records_sql("SELECT count(id) as n
				FROM {$CFG->prefix}user
				WHERE deleted = 0
				AND id not in ($userlist)");
	}

	return $users;
}

function check_moodle_users ($users)
{
	global $CFG, $DB;

	$admins = get_admins();
	foreach ($admins as $admin)
	{
		$a[] = $admin->id;
	}
	$a[] = 1; //Guest user
	$i = 0;
	foreach ($users as $user)
	{

		$username = utf8_decode ($user['username']);
		$username = strtolower ($username);
        $conditions = array ('username' => $username);

		$user = $DB->get_record('user', $conditions);
		if ($user)
		{
			$users[$i]['m_account'] = 1;
			$users[$i]['auth'] = $user->auth;
			if (in_array ($user->id, $a))
				$users[$i]['admin'] = 1;
			else
				$users[$i]['admin'] = 0;
		}
		else
		{
			$users[$i]['m_account'] = 0;
			$users[$i]['admin'] = 0;
			$users[$i]['auth'] = '';
		}
		$i++;
	}

	return $users;
}

function get_moodle_only_users ($users, $search)
{
	global $CFG, $DB;

	/* Don't show admins and guets */
	$admins = get_admins();
	foreach ($admins as $admin)
	{
		$a[] = $admin->id;
	}
	$a[] = 1; //Guest user
	$adminlist = "'".implode("','", $a)."'";

	$usernames = array ();
	foreach ($users as $user)
	{
		$username = utf8_decode ($user['username']);
		$username = strtolower ($username);
        $usernames[] = $username;
	}

	$userlist = "'".implode("','", $usernames)."'";
	$users = array();
	if ($search)
	{
		$params = array();
		$likeu = $DB->sql_like('username', '?', false);
		$params[] = "%$search%";
		$likee = $DB->sql_like('email', '?', false);
		$params[] = "%$search%";
		$likef = $DB->sql_like('firstname', '?', false);
		$params[] = "%$search%";
		$likel = $DB->sql_like('lastname', '?', false);
		$params[] = "%$search%";

		/*
		$users = $DB->count_records_sql("SELECT count(id) as n
                        FROM {$CFG->prefix}user
                        WHERE deleted = 0
                        AND id not in ($userlist)
                       AND(({$likeu}) OR ({$likee}) OR ({$likef}) OR ({$likel}))", $params);
*/

		$users = $DB->get_records_sql("SELECT id, username, email,  firstname, lastname, auth
						FROM {$CFG->prefix}user
						WHERE deleted = 0 
						AND auth != 'webservice'
						AND (username not in ($userlist))
					   AND(({$likeu}) OR ({$likee}) OR ({$likef}) OR ({$likel}))", $params);
	}
	else
		$users = $DB->get_records_sql("SELECT id, username, email, firstname, lastname, auth
				FROM {$CFG->prefix}user
				WHERE deleted = 0
				AND auth != 'webservice'
				AND (username not in ($userlist))");


	$n = count ($users);
	$i = 0;
	$u = array ();
	foreach ($users as $user)
	{
		$u[$i] = get_object_vars ($user);
		if (in_array ($user->id, $a))
			$u[$i]['admin'] = '1';
		else $u[$i]['admin'] = '0';

		$u[$i]['name'] = $user->firstname . ' ' . $user->lastname;

		$i++;
	}
	return $u;

}

function delete_user ($username)
{
	global $DB;

	$username = utf8_decode ($username);
	$username = strtolower ($username);
	$conditions = array("username" => $username);
	$user = $DB->get_record("user", $conditions);

	if ($user)
	{
		delete_user ($user);
		return 1;
	}
	return 0;
}

function user_id ($username)
{
	global $DB;

	$username = utf8_decode ($username);
	$username = strtolower ($username);
	$conditions = array("username" => $username);
	$user = $DB->get_record("user", $conditions);

	if (!$user)
		return 0;

	return $user->id;
}

function user_details ($username)
{
	global $DB, $CFG;

	$username = utf8_decode ($username);
	$username = strtolower ($username);
	$conditions = array("username" => $username);
	$user = $DB->get_record("user", $conditions);

	$u['username'] = $user->username;
	$u['firstname'] = $user->firstname;
	$u['lastname'] = $user->lastname;
	$u['email'] = $user->email;
	$u['id'] = $user->id;
	$u['name'] = $user->firstname. " " . $user->lastname;
	$u['city'] = $user->city;
	$u['country'] = $user->country;
	$u['lang'] = $user->lang;
	$u['timezone'] = $user->timezone;
	$u['phone1'] = $user->phone1;
	$u['phone2'] = $user->phone2;
	$u['address'] = $user->address;
	$u['description'] = $user->description;
	$u['institution'] = $user->institution;
	$u['url'] = $user->url;
	$u['icq'] = $user->icq;
	$u['skype'] = $user->skype;
	$u['aim'] = $user->aim;
	$u['yahoo'] = $user->yahoo;
	$u['msn'] = $user->msn;
	$u['idnumber'] = $user->idnumber;
	$u['department'] = $user->department;
	$u['picture'] = $user->picture;
	$u['lastnamephonetic'] = $user->lastnamephonetic;
	$u['firstnamephonetic'] = $user->firstnamephonetic;
	$u['middlename'] = $user->middlename;
	$u['alternatename'] = $user->alternatename;

	$id = $user->id;
	$usercontext = context_user::instance($id);
	$context_id = $usercontext->id;

	if ($user->picture)
		$u['pic_url'] = $CFG->wwwroot."/pluginfile.php/$context_id/user/icon/f1";

	/* Custom fields */
	$query = "SELECT f.id, d.data 
				FROM {$CFG->prefix}user_info_field as f, {$CFG->prefix}user_info_data d 
				WHERE f.id=d.fieldid and userid = ?";

	$params = array ($id);
	$records =  $DB->get_records_sql($query, $params);

	$i = 0;
	$u['custom_fields'] = array ();
	foreach ($records as $field)
	{
		$u['custom_fields'][$i]['id'] = $field->id;
		$u['custom_fields'][$i]['data'] = $field->data;
		$i++;
	}

	return $u;
}

function user_custom_fields ()
{
	global $DB, $CFG;

	$query = "SELECT id, name
				FROM {$CFG->prefix}user_info_field";

	$records =  $DB->get_records_sql($query);
	$i = 0;
	$custom_fields = array ();
	foreach ($records as $field)
	{
		$custom_fields[$i]['id'] = $field->id;
		$custom_fields[$i]['name'] = $field->name;
		$i++;
	}

	return $custom_fields;
}

function user_details_by_id ($id)
{
	global $DB;

	$conditions = array("id" => $id);
	$user = $DB->get_record("user", $conditions);

	$u['username'] = $user->username;

	return $u;
}

function update_session ($username)
{
        global $DB, $CFG;

        $conditions = array("username" => $username);
        $user = $DB->get_record("user", $conditions);

        $params = array ($user->id);
        $sql = "SELECT sid FROM {$CFG->prefix}sessions " .
                " WHERE userid = ? " .
                " ORDER BY timemodified DESC LIMIT 1";

        $session = $DB->get_records_sql ($sql, $params);

        if (!$session)
            return false;

        $session_obj = array_shift ($session);

        $conditions = array ('sid' => $session_obj->sid);
        $session = $DB->get_record ('sessions', $conditions);

        if (!$session)
            return;

        $session->timemodified = time ();
        $DB->update_record ('sessions', $session);

        return true;
}

function migrate_to_joomdle ($username)
{
	global $DB;
	$username = utf8_decode ($username);
	$username = strtolower ($username);
	$conditions = array ('username' =>  $username); 
	$DB->set_field('user', 'auth', 'joomdle', $conditions);

	return true;
}

	// Get user events
	function my_events ($username, $cursosid) 
	{
        global $CFG, $DB;

		$username = utf8_decode ($username);
		$username = strtolower ($username);
		$whereclause = '';
		if ($username == 'admin')
		{
			$whereclause .= ' (groupid = 0 AND courseid = 1) ';
		}
		else 
		{
			$user = get_complete_user_data ('username', $username);

			if (!$user)
				return array ();

			$g = array ();
			$i = 0;
			$w = array ();
			foreach ($cursosid as $course)
			{
				$course_id = $course['id'];
				$cursos_ids[] = $course_id;
				$groups = groups_get_user_groups ($course_id, $user->id);

				if (!count($groups[0]))
					continue;

				foreach ($groups[0] as $group)
					$w[] = " or (courseid = $course_id and groupid = $group)";
			}

			$whereclause = ' (userid = '.$user->id.' AND courseid = 0 AND groupid = 0)';
			$whereclause .= ' OR  (groupid = 0 AND courseid IN ('.implode(',', $cursos_ids).')) ';

			foreach ($w as $cond)
				$whereclause .= $cond;
		}
		$whereclause .= ' AND visible = 1';
		$events = $DB->get_records_select('event', $whereclause);

		$data = array ();
		foreach ($events as $event)
		{
			$e['name'] = $event->name;
			$e['timestart'] = $event->timestart;
			$e['courseid'] = $event->courseid;

			$data[] = $e;
		}

		return $data;
    }

	function get_my_events ($username)
    {
        global $CFG, $DB;
 
        $username = utf8_decode ($username);
        $username = strtolower ($username);
 
        $user = get_complete_user_data ('username', $username);
 
 
        if (!$user)
            return array();
 
        $courses = enrol_get_users_courses ($user->id, true);
 
        $news = array ();
        foreach ($courses as $c)
        {
            $course_news['remoteid'] = $c->id;
            $course_news['fullname'] = $c->fullname;
            $course_news['events'] = $this->get_upcoming_events ($c->id, $username);
 
            $news[] = $course_news;
        }
 
        return $news;
    }

    function get_events ($username, $start_date, $end_date, $type, $course_id)
    {
        global $USER, $DB;

        $username = utf8_decode ($username);
        $username = strtolower ($username);
        $user = get_complete_user_data ('username', $username);

		if ($username != 'guest')
		{
			if ($course_id)
			{
				if (! $course = $DB->get_record("course", array("id" => $course_id)))
					return array ();
				$coursestoload = array ($course_id => $course);
			}
			else
				$coursestoload = enrol_get_users_courses ($user->id, true);
		}
		else
		{
			if (! $course = $DB->get_record("course", array("id" => 1)))
				return array ();
			$coursestoload = array (1 => $course);
		}

        $ws_user = $USER; // save $USER var to reset it after use. It holds web service user. I don't think it is needed, but just in case...
        $USER = $user;
        $ignorefilters = false;
        list($courses, $group, $user_id_not_used) = calendar_set_filters($coursestoload, $ignorefilters); 
        $USER = $ws_user; // reset global var


        $events = calendar_get_events($start_date, $end_date, $user->id, $group, $courses);

        $es = array ();
        foreach ($events as $event)
        {
			// We filter user and site events here
			if (($type == 'site') &&  ($event->eventtype != 'site'))
                    continue;
            else if ($type == 'user')
            {
				//  We only show events with userid set as the user
                if ($event->userid != $user->id)
                    continue;
            }

            $e = array ();
            $e['id'] = $event->id;
            $e['name'] = $event->name;
            $e['description'] = $event->description;
            $e['timestart'] = $event->timestart;
            $e['timeduration'] = $event->timeduration;

            $es[] = $e;
        }   

        return $es;
    }

    function get_event ($id)
    {
        global $DB;

        $conditions = array ('id' => $id);
        $event = $DB->get_record ('event', $conditions);

        $e = array (); 
        $e['id'] = $event->id;
        $e['name'] = $event->name;
        $e['description'] = $event->description;
        $e['timestart'] = $event->timestart;
        $e['timeduration'] = $event->timeduration;
        $e['type'] = $event->eventtype;

        return $e;
    }

	function add_parent_role ($child, $parent)
	{
		$child = utf8_decode ($child);
		$child = strtolower ($child);
		$parent = utf8_decode ($parent);
		$parent = strtolower ($parent);

		$parent_user = get_complete_user_data ('username', $parent);
		if (!$parent_user)
            return false;

		$child_user = get_complete_user_data ('username', $child);
        if (!$child_user)
            return false;

		$parent_role_id = get_config('auth/joomdle', 'parent_role_id');
		if (!$parent_role_id)
            return false;


		$context = context_user::instance($child_user->id);
		
		role_assign($parent_role_id, $parent_user->id, $context->id ); //, $timestart, 0, $hidden);

		return true;
	}

	function get_mentees ($username)
	{
		global $CFG, $DB;

		$username = utf8_decode ($username);
		$username = strtolower ($username);
		$user = get_complete_user_data ('username', $username);

		if (!$user)
			return array ();

		$usercontexts = $DB->get_records_sql("SELECT c.instanceid, c.instanceid, u.firstname, u.lastname
                                         FROM {$CFG->prefix}role_assignments ra,
                                              {$CFG->prefix}context c,
                                              {$CFG->prefix}user u
                                         WHERE ra.userid = $user->id
                                         AND   ra.contextid = c.id
                                         AND   c.instanceid = u.id
                                         AND   c.contextlevel = ".CONTEXT_USER);
		if (!$usercontexts)
			return array ();

		$i = 0;
		$users = array ();
		foreach ($usercontexts as $usercontext) {
			$users[$i]['id'] = $usercontext->instanceid;
			$child_user = get_complete_user_data ('id', $usercontext->instanceid);
			$users[$i]['username'] = $child_user->username;
			$users[$i]['name'] = $child_user->firstname. " " . $child_user->lastname;
			$i++;
		}

		return $users;
	}

    function get_roles ()
    {
        global $CFG, $DB ,$PAGE;

        $roles = $DB->get_records_sql("SELECT id, name, shortname
                                         FROM {$CFG->prefix}role");

        $rolenames = role_fix_names($roles, NULL, ROLENAME_BOTH, true);

        $data = array ();
        foreach ($roles as $role)
        {
            // Only return roles assignables in course context
            $contextlevels = get_role_contextlevels($role->id);
            if (!in_array (CONTEXT_COURSE, $contextlevels))
                continue;

            $r['id'] = $role->id;
            $r['name'] = $rolenames[$role->id];

            $data[] = $r;
        }

        return $data;
    }

    function get_system_roles ()
    {
        global $CFG, $DB ,$PAGE;

        $roles = $DB->get_records_sql("SELECT id, name, shortname
                                         FROM {$CFG->prefix}role");

        $rolenames = role_fix_names($roles, NULL, ROLENAME_BOTH, true);

        $data = array ();
        foreach ($roles as $role)
        {
            // Only return roles assignables in course context
            $contextlevels = get_role_contextlevels($role->id);
            if (!in_array (CONTEXT_SYSTEM, $contextlevels))
                continue;

            $r['id'] = $role->id;
            $r['name'] = $rolenames[$role->id];

            $data[] = $r;
        }

        return $data;
    }

	function get_parents ($username)
	{
		global $CFG, $DB;
		$parent_role_id = get_config('auth/joomdle', 'parent_role_id');

		$username = utf8_decode ($username);
		$username = strtolower ($username);
		$user = get_complete_user_data ('username', $username);
		/* Get mentors for the student */
		$usercontext = context_user::instance($user->id);
		$usercontextid = $usercontext->id;

		$query =
		    "SELECT r.userid,u.username
		    FROM
			{$CFG->prefix}role_assignments r, {$CFG->prefix}user u
		    WHERE
			r.roleid = '$parent_role_id' and r.contextid = '$usercontextid'
			and r.userid  = u.id
			";

		$mentors =  $DB->get_records_sql($query);

		$data = array ();
		foreach ($mentors as $mentor)
		{
		//	$r['userid'] = $mentor->userid;
			$r['username'] = $mentor->username;

			$data[] = $r;
		}

		return $data;
	}

    function get_all_parents ()
	{
        global $DB, $CFG;

        $parent_role_id = get_config('auth/joomdle', 'parent_role_id');

        $params = array ('roleid' => $parent_role_id);
        $query = "SELECT distinct (userid) FROM {$CFG->prefix}role_assignments" .
                " WHERE roleid = ?";
        $parents =  $DB->get_records_sql($query, $params);

        $p = array ();
        foreach ($parents as $parent)
        {
            $conditions = array ('id' => $parent->userid);
            $parent_user = $DB->get_record('user',$conditions);

            $parent_data['username'] = $parent_user->username;
            $parent_data['firstname'] = $parent_user->firstname;
            $parent_data['lastname'] = $parent_user->lastname;
            $p[] = $parent_data;
        }

        return $p;
	}

    function get_course_parents ($id)
	{
        global $DB, $CFG;

        $p = array ();
        $students = $this->get_course_students ($id);
        foreach ($students as $student)
        {
			$context = context_user::instance($student['id']);

            $conditions = array ('contextid' => $context->id);
            $parents = $DB->get_records('role_assignments',$conditions);

            foreach ($parents as $parent)
            {
                $conditions = array ('id' => $parent->userid);
                $parent_user = $DB->get_record('user',$conditions);

                // Check it is not already included
                $added = 0;
                $parents_copy = $p;
                foreach ($parents_copy as $pc)
                {
                    if ($pc['username'] == $parent_user->username)
                    {
                        $added = true;
                        break;
                    }
                }

                if ($added)
                    continue;

                $parent_data['username'] = $parent_user->username;
                $parent_data['firstname'] = $parent_user->firstname;
                $parent_data['lastname'] = $parent_user->lastname;
                $p[] = $parent_data;
            }
        }
        return $p;
	}




	function course_enrol_methods ($course_id)
	{
		$instances = enrol_get_instances($course_id, true);

		$i = 0;
		foreach ($instances as $method)
		{
			$m[$i]['id'] = $method->id;
			$m[$i]['enrol'] = $method->enrol;
			$m[$i]['enrolstartdate'] = $method->enrolstartdate;
			$m[$i]['enrolenddate'] = $method->enrolenddate;
			$i++;
		}

		return $m;
	}


	function quiz_get_question ($id)
	{
		global $CFG, $DB;

		$query = "SELECT id,questiontext, qtype
				FROM {$CFG->prefix}question
				WHERE id = ?";
		$params = array ($id);
		$record =  $DB->get_record_sql($query, $params);

		$r = get_object_vars ($record);

		return $r;
	}

function question_rewrite_question_urls($text, $file, $contextid, $component,
        $filearea,  $ids, $itemid, $options=null) {
    global $CFG;

    $options = (array)$options;
    if (!isset($options['forcehttps'])) {
        $options['forcehttps'] = false;
    }

    if (!$CFG->slasharguments) {
        $file = $file . '?file=';
    }

    $baseurl = "$CFG->wwwroot/$file/$contextid/$component/$filearea/";

    if (!empty($ids)) {
        $baseurl .= (implode('/', $ids) . '/');
    }

    if ($itemid !== null) {
        $baseurl .= "$itemid/";
    }

    if ($options['forcehttps']) {
        $baseurl = str_replace('http://', 'https://', $baseurl);
    }

    return str_replace('@@PLUGINFILE@@/', $baseurl, $text);
	}

    public function make_html_inline($html) {
        $html = preg_replace('~\s*<p>\s*~', '', $html);
        $html = preg_replace('~\s*</p>\s*~', '<br />', $html);
        $html = preg_replace('~<br />$~', '', $html);
        return $html;
    }



    function quiz_get_answers ($id)
    {
        global $CFG, $DB;

        $query = "SELECT id, answer, fraction
                FROM {$CFG->prefix}question_answers
                WHERE question = ?";
        $params = array ($id);
        $records =  $DB->get_records_sql($query, $params);

		$options['noclean'] = true;
		$answers = array ();
        foreach ($records as $record)
        {
            $r = get_object_vars ($record);

			$r['fraction'] = (float) $r['fraction'];
            $r['answer'] = $this->question_rewrite_question_urls($r['answer'], 'pluginfile.php', 1, 'question', 'answer', array(), $r['id']);
			$r['answer'] = format_text($r['answer'], FORMAT_HTML, $options);
            $r['answer'] = str_replace ('pluginfile.php', '/auth/joomdle/pluginfile_joomdle.php', $r['answer']);
			$r['answer'] = $this->make_html_inline ($r['answer']);


            $answers[] = $r;
        }

        return $answers;
    }

    function quiz_get_random_question ($cat_id, $used_ids)
    {
        global $CFG, $DB;

		$query = "SELECT id,questiontext, qtype
                FROM {$CFG->prefix}question
                WHERE category = ? AND qtype = 'multichoice'";
        if ($used_ids)
            $query .= " AND id not in ($used_ids)";
        $query .= " ORDER BY RAND() LIMIT 1";

        $params = array ($cat_id);
        $record =  $DB->get_record_sql($query, $params);

		if (!$record)
			return NULL;

        $r = get_object_vars ($record);
        $r['questiontext'] = $this->question_rewrite_question_urls($r['questiontext'], 'pluginfile.php', 1, 'question', 'questiontext', array(), $r['id']);
        $r['questiontext'] = str_replace ('pluginfile.php', '/auth/joomdle/pluginfile_joomdle.php', $r['questiontext']);

        $r['answers'] = $this->quiz_get_answers ($r['id']);

        return $r;
    }

    function quiz_get_question_categories ()
    {
        global $CFG, $DB;
    
        $cat = addslashes ($cat);
        $query =
            "SELECT id, name
            FROM 
            {$CFG->prefix}question_categories
            ORDER BY
            sortorder ASC
            ";
        
        $params = array ($cat);
        $records =  $DB->get_records_sql($query, $params);
    
        $cats = array ();
        foreach ($records as $cat)
        {
            $c = get_object_vars ($cat);
            $cats[] = $c;
        }
    
        
        return ($cats);
    }



	function quiz_get_correct_answer ($id)
	{
		global $CFG, $DB;

		$query = "SELECT id
				FROM {$CFG->prefix}question_answers
				WHERE question = ? and fraction = 1";
		$params = array ($id);
		$record =  $DB->get_record_sql($query, $params);

		return $record->id;
	}

	function multiple_suspend_enrolment ($username, $courses)
	{
			global $CFG, $DB;

			$username = utf8_decode ($username);
			$username = strtolower ($username);

			$conditions = array ('username' => $username);
			$user = $DB->get_record('user',$conditions);

			if (!$user)
				return;

			foreach ($courses as $c)
			{
				$conditions = array ('id' => $c['id']);
				$course = $DB->get_record ('course', $conditions);

				if (!$course)
					continue;

				$this->suspend_enrolment ($username, $course->id);
			}

			return 0;
	}

	function suspend_enrolment ($username, $course_id)
    {
        global $CFG, $DB;

		$username = utf8_decode ($username);
		$username = strtolower ($username);

        $conditions = array ('courseid' => $course_id, 'enrol' => 'manual');
        $enrol = $DB->get_record('enrol', $conditions);

		if (!$enrol)
			return;

        $conditions = array ('username' => $username);
        $user = $DB->get_record('user', $conditions);

		if (!$user)
			return;

        $conditions = array ('enrolid' => $enrol->id, 'userid' => $user->id);
        $ue = $DB->get_record('user_enrolments', $conditions);

		if (!$ue)
			return;

        $ue->status = 1; //suspended
        $DB->update_record('user_enrolments', $ue);

        return;
    }


	function multiple_unenrol_user ($username, $courses)
	{
			global $CFG, $DB;

			$username = utf8_decode ($username);
			$username = strtolower ($username);

			$conditions = array ('username' => $username);
			$user = $DB->get_record('user',$conditions);

			if (!$user)
				return;

			foreach ($courses as $c)
			{
				$conditions = array ('id' => $c['id']);
				$course = $DB->get_record ('course', $conditions);

				if (!$course)
					continue;

				$this->unenrol_user ($username, $course->id);
			}

			return 0;
	}

	// Unenrol user totally
	function unenrol_user ($username, $course_id)
	{   
        global $CFG, $DB;

        $username = utf8_decode ($username);
        $username = strtolower ($username);

        $conditions = array ('courseid' => $course_id, 'enrol' => 'manual');
        $enrol = $DB->get_record('enrol', $conditions);
    
        if (!$enrol)
            return;

        $conditions = array ('username' => $username);
        $user = $DB->get_record('user', $conditions);
        
        if (!$user)
            return;

        $conditions = array ('enrolid' => $enrol->id, 'userid' => $user->id);
        $ue = $DB->get_record('user_enrolments', $conditions);

        if (!$ue)
            return;

        $instance = $DB->get_record('enrol', array('id'=>$ue->enrolid), '*', MUST_EXIST);

        $plugin = enrol_get_plugin($instance->enrol);

        $plugin->unenrol_user($instance, $ue->userid);
	}

	function multiple_remove_from_group ($username, $courses)
	{
			global $CFG, $DB;

			$username = utf8_decode ($username);
			$username = strtolower ($username);

			$conditions = array ('username' => $username);
			$user = $DB->get_record('user',$conditions);

			if (!$user)
				return;

			foreach ($courses as $c)
			{
				$conditions = array ('id' => $c['id']);
				$course = $DB->get_record ('course', $conditions);

				if (!$course)
					continue;

				// Group
				groups_remove_member ($c['group_id'], $user->id);
			}

			return 0;
	}

    function get_course_completion ($id, $username) {
		global $CFG, $DB;

		$username = utf8_decode ($username);
		$username = strtolower ($username);

		$query =
			"SELECT
			cs.id,
			cs.section,
			cs.summary
			FROM
			{$CFG->prefix}course_sections cs
			WHERE
			cs.course = ?
			and cs.visible = 1
			";

		$params = array ($id);
		$records =  $DB->get_records_sql($query, $params);

		$context = context_course::instance($id);

		$user = get_complete_user_data ('username', $username);

		$data = array ();
		foreach ($records as $r)
		{
			$e['section'] = $r->section;
			$e['summary'] = $r->summary;

			// Check all modules in section
			$query =
				"SELECT
				cm.id
				FROM
				{$CFG->prefix}course_modules cm
				WHERE
				cm.section = ?
				and cm.visible = 1
				and completion != 0
				";

			$params = array ($r->id);
			$records_cm =  $DB->get_records_sql($query, $params);
			
			$n_cm = count ($records_cm);
			$n = 0;
			foreach ($records_cm as $module)
			{
				$query = "SELECT count(*) 
					FROM  {$CFG->prefix}course_modules_completion 
					WHERE coursemoduleid = ? and userid = ?";

				$params = array ($module->id, $user->id);
				$n +=  $DB->count_records_sql($query, $params);
			}
			if ($n == 0)
				$complete = 0;
			else if ($n < $n_cm)
				$complete = 1;
			else $complete = 2;
			$e['complete'] = $complete;

			$data[$i] = $e;
			$i++;
		}

		return $data;
    }

   function get_course_resources ($id, $username = '') {
        global $CFG, $DB;

		$username = utf8_decode ($username);
		$username = strtolower ($username);

        if ($username)
                $user = get_complete_user_data ('username', $username);

        $modinfo = get_fast_modinfo($id);
        $sections = $modinfo->get_section_info_all();

		$mods = get_fast_modinfo($id)->get_cms();
		$modnames = get_module_types_names();
		$modnamesplural = get_module_types_names(true);
		$modnamesused = get_fast_modinfo($id)->get_used_module_names();

        foreach ($sections as $section)
        {
            $sectionmods = explode(",", $section->sequence);
            foreach ($sectionmods as $modnumber) {
                if (empty($mods[$modnumber])) {
                    continue;
                }
                $mod = $mods[$modnumber];

                if ( $mod->modname == 'resource')
                {
                    if ($username)
                    {
                        $cm = get_coursemodule_from_id(false, $mod->id);
						if (\core_availability\info_module::is_user_visible($cm, $user->id))
                            continue;
                    }

                    $e[$section->section]['section'] = $section->section;
                    $e[$section->section]['summary'] = $section->summary;
                    $resource['id'] = $mod->id;
                    $resource['name'] = $mod->name;
                    $resource['type'] = substr ($mod->icon, 2);

                    $e[$section->section]['resources'][] = $resource;
                }
            }
        }
		return $e;
	}

    function get_course_quizes ($id, $username = '') {
		global $CFG, $DB;

		$username = utf8_decode ($username);
		$username = strtolower ($username);

		$query =
			"SELECT
			cs.id,
			cs.section,
			cs.summary
			FROM
			{$CFG->prefix}course_sections cs
			WHERE
			cs.course = ?
			and cs.visible = 1
			";

		$params = array ($id);
		$records =  $DB->get_records_sql($query, $params);

		$context = context_course::instance($id);

		if ($username)
				$user = get_complete_user_data ('username', $username);

		$i = 0;
		$data = array ();
		foreach ($records as $r)
		{
			$e['section'] = $r->section;
			$e['summary'] = $r->summary;

			$query =
				"SELECT cm.id, q.name,  q.id as quiz_id
				FROM
				{$CFG->prefix}course_modules cm, {$CFG->prefix}quiz q, {$CFG->prefix}modules m
				WHERE
				cm.instance = q.id and cm.module = m.id and m.name = 'quiz'
				and cm.course = ?
				and cm.section = ?
				and cm.visible = 1
				";

			$params = array ($id, $r->id);
			$records_cm =  $DB->get_records_sql($query, $params);


			$resources = array ();
			foreach ($records_cm as $r_cm)
			{
				$resource['grade'] = (float) 0;
				$resource['passed'] = false;
				if ($username)
				{
					$cm = get_coursemodule_from_id(false, $r_cm->id);
					if (\core_availability\info_module::is_user_visible($cm, $user->id))
						continue;

					$grades = grade_get_grades ($id, 'mod', 'quiz', $r_cm->quiz_id, $user->id);
					$grade = array_shift ($grades->items[0]->grades);

					$resource['grade'] = (float) $grade->grade;
					if ($grade->grade == $grades->items[0]->grademax)
						$resource['passed'] = true;
					else
						$resource['passed'] = false;
				}


				$resource['id'] = $r_cm->id;
				$resource['name'] = $r_cm->name;

				$resources[] = $resource;

			}

			$e['quizes'] = $resources;
			$data[$i] = $e;
			$i++;
		}

		return $data;
	}

   function get_course_mods ($id, $username = '') {
        global $CFG, $DB;

		$username = utf8_decode ($username);
		$username = strtolower ($username);

        if ($username)
                $user = get_complete_user_data ('username', $username);

		$modinfo = get_fast_modinfo($id);
		$sections = $modinfo->get_section_info_all();


		$forum = forum_get_course_forum($id, 'news');
        $news_forum_id = $forum->id;

		$mods = get_fast_modinfo($id)->get_cms();
		$modnames = get_module_types_names();
		$modnamesplural = get_module_types_names(true);
		$modnamesused = get_fast_modinfo($id)->get_used_module_names();

		$rawmods = get_course_mods($id);

		$context = context_course::instance($id);

		// Kludge to use username param to get non visible sections and modules
		$username_orig = $username;
		if ($username_orig == 'joomdle_get_not_visible')
			$username = '';

		$e = array ();
        foreach ($sections as $section)
        {
			if ($username_orig != 'joomdle_get_not_visible')
				if (!$section->visible)
					continue;

            $sectionmods = explode(",", $section->sequence);
            foreach ($sectionmods as $modnumber) {
                if (empty($mods[$modnumber])) {
                    continue;
                }
                $mod = $mods[$modnumber];

				if ($username_orig != 'joomdle_get_not_visible')
					if (!$mod->visible)
						continue;

                $resource['completion_info'] = '';
                if ($username)
                {
                    $cm = get_coursemodule_from_id(false, $mod->id);
					if (!\core_availability\info_module::is_user_visible($cm, $user->id))
                    {
						if ( empty($mod->availableinfo)) // Mod not visible, and no completion info to show
                            continue;

                        $resource['available'] = 0;
                        $ci = new condition_info($mod);
                        $resource['completion_info'] = $ci->get_full_information ();
                    }
                    else
                        $resource['available'] = 1;
                }
                else
                        $resource['available'] = 1;

				$e[$section->section]['section'] = $section->section;
				$e[$section->section]['name'] = $section->name;
				$e[$section->section]['summary'] = file_rewrite_pluginfile_urls ($section->summary, 'pluginfile.php', $context->id, 'course', 'section', $section->id);
				$e[$section->section]['summary'] = str_replace ('pluginfile.php', '/auth/joomdle/pluginfile_joomdle.php', $e[$section->section]['summary']);
				$resource['id'] = $mod->id;
				$resource['name'] = $mod->name;
				$resource['mod'] = $mod->modname;

                // Get content
                $resource['content'] = '';
                $modname = $mod->modname;
                $functionname = $modname."_get_coursemodule_info";
                if (file_exists("$CFG->dirroot/mod/$modname/lib.php")) {
                    include_once("$CFG->dirroot/mod/$modname/lib.php");
                    if ($hasfunction = function_exists($functionname)) {
                        if ($info = $functionname($rawmods[$modnumber])) {
                            $resource['content'] = $info->content;
                        }
                    }
                }

				// Format for mod->icon is: f/type-24
				$type = substr ($mod->icon, 2);
                $parts = explode ('-', $type );
                $type = $parts[0];
                $resource['type'] = $type;


                //In forum, type is unused, so we use it for forum type: news/general
                if ($mod->modname == 'forum')
                {
                    $cm = get_coursemodule_from_id('forum', $mod->id);
                    if ($cm->instance == $news_forum_id)
                        $resource['type'] = 'news';
                }

/*
				if ($mod->modname == 'resource')
                {
                    // Get display options for resource
                    $params = array ($mod->instance);
                    $query = "SELECT display from  {$CFG->prefix}resource where id = ?";
                    $record = $DB->get_record_sql ($query, $params);
                    
                    $resource['display'] = $record->display;
                }
                else $resource['display'] = 0;
*/
				$resource['display'] = $this->get_display ($mod->modname, $mod->instance);

				$e[$section->section]['mods'][] = $resource;
            }
        }
		return $e;
	}

	function get_display ($modname, $instance)
	{
		global $CFG, $DB;

		switch ( $modname )
		{
			case 'resource':
				// Get display options for resource
				$params = array ($instance);
				$query = "SELECT display from  {$CFG->prefix}resource where id = ?";
				$record = $DB->get_record_sql ($query, $params);

				$display = $record->display;
				break;
			case 'url':
				// Get display options for url
				$params = array ($instance);
				$query = "SELECT display from  {$CFG->prefix}url where id = ?";
				$record = $DB->get_record_sql ($query, $params);

				$display = $record->display;
				break;
			default:
				$display = 0;
				break;
		}

		return $display;
	}


    function my_certificates ($username, $type = 'normal')
    {
		switch ($type)
		{
			case "normal":
				return $this->my_certificates_normal ($username);
				break;
			case "simple":
				return $this->my_certificates_simple ($username);
				break;
		}
	}

    function my_certificates_normal ($username)
    {
        global $CFG, $DB;

        $username = utf8_decode ($username);
        $username = strtolower ($username);

        $user = get_complete_user_data ('username', $username);
        $user_id = $user->id;


        $cursos = enrol_get_users_courses ($user->id, true);

        if (!count ($cursos))
            return array ();

        $c_ids = array ();
        foreach ($cursos as $curso)
        {
            $c_ids[] = $curso->id;
        }
        $ids_str = implode (',', $c_ids);

		$certs = $DB->get_records_sql("SELECT  c.name, c.id, ci.timecreated as certdate
				FROM {$CFG->prefix}certificate c
				LEFT JOIN {$CFG->prefix}certificate_issues ci ON c.id = ci.certificateid
				WHERE ci.userid = $user_id
				AND c.course  in ($ids_str)
				ORDER BY ci.timecreated DESC");

        $c = array ();
        foreach ($certs as $cert)
        {
            $coursemodule = get_coursemodule_from_instance ("certificate", $cert->id);
            $certificate['id'] =  $coursemodule->id;
            $certificate['name']  = $cert->name;
            $certificate['date']  = $cert->certdate;

            $c[] = $certificate;
        }

        return $c;
    }

    function my_certificates_simple ($username)
    {
        global $CFG, $DB;

        $username = utf8_decode ($username);
        $username = strtolower ($username);

        $user = get_complete_user_data ('username', $username);
        $user_id = $user->id;


        $cursos = enrol_get_users_courses ($user->id, true);

        if (!count ($cursos))
            return array ();

        $c_ids = array ();
        foreach ($cursos as $curso)
        {
            $c_ids[] = $curso->id;
        }
        $ids_str = implode (',', $c_ids);

		$certs = $DB->get_records_sql("SELECT  c.name, c.id, ci.timecreated as certdate
				FROM {$CFG->prefix}simplecertificate c
				LEFT JOIN {$CFG->prefix}simplecertificate_issues ci ON c.id = ci.certificateid
				WHERE ci.userid = $user_id
				AND c.course  in ($ids_str)
				ORDER BY ci.timecreated DESC");

        $c = array ();
        foreach ($certs as $cert)
        {
            $coursemodule = get_coursemodule_from_instance ("simplecertificate", $cert->id);
            $certificate['id'] =  $coursemodule->id;
            $certificate['name']  = $cert->name;
            $certificate['date']  = $cert->certdate;

            $c[] = $certificate;
        }

        return $c;
    }

	function get_page ($id)
	{   
		global $DB;

		if (!$cm = get_coursemodule_from_id('page', $id)) {
				return '';
		}
		$page = $DB->get_record('page', array('id'=>$cm->instance), '*', MUST_EXIST);

		$options['noclean'] = true;
		$mypage['name'] = $page->name;
		$context = context_module::instance($cm->id);
        $mypage['content'] = file_rewrite_pluginfile_urls ($page->content, 'pluginfile.php', $context->id, 'mod_page', 'content', $page->revision);
		$mypage['content'] = format_text($mypage['content'], FORMAT_MOODLE, $options);

		return $mypage;
	}

	function get_label ($id)
	{
        global $DB;
    
        if (!$cm = get_coursemodule_from_id('label', $id)) {
                return '';         
        }
        $label = $DB->get_record('label', array('id'=>$cm->instance), '*', MUST_EXIST);
            
		$options['noclean'] = true;
        $mylabel['name'] = $label->name;
		$context = context_module::instance($cm->id);
        $mylabel['content'] = $label->intro;
		$mylabel['content'] = file_rewrite_pluginfile_urls ($mylabel['content'], 'pluginfile.php', $context->id, 'mod_label', 'intro', NULL);
		$mylabel['content'] = format_text($mylabel['content'], FORMAT_MOODLE, $options);
		$mylabel['content'] = str_replace ('pluginfile.php', '/auth/joomdle/pluginfile_joomdle.php', $mylabel['content']);

        return $mylabel;
    }

    function get_news_item ($id)
    {
        global $CFG, $DB;

        $posts = forum_get_all_discussion_posts ($id, 'created');

        $item_posts = array ();
        foreach ($posts as $post)
        {
            $p['subject'] = $post->subject;
            $p['message'] = $post->message;

            $item_posts[] = $p;
        }   

        return $item_posts;
    }

    function get_questionnaire_question_result_radio ($qid)
    {
        global $CFG, $DB;

        require_once($CFG->dirroot.'/mod/questionnaire/questionnaire.class.php');

        $sql = "SELECT c.id as cid, q.id as qid, q.precise AS precise, q.name, c.content
            FROM {questionnaire_question} q ".
            "LEFT JOIN {questionnaire_quest_choice} c ON question_id = q.id ".
            'WHERE q.id = '.$qid.' ORDER BY cid ASC';
        if (!($records2 = $DB->get_records_sql($sql))) {
            $records2 = array();
        }

        $options = array ();
        foreach ($records2 as $record2)
        {
            $option = array ();
            $option['content'] = $record2->content;

            $cid = $record2->cid;

            $sql = "SELECT count(*) as n
                FROM {questionnaire_resp_single} rm ".
                'WHERE question_id = '.$qid.' AND choice_id ='.$cid;
            if (!($record3 = $DB->get_record_sql($sql))) {
                $record3 = NULL;
            }
            $option['n'] = $record3->n;

            $options[] = $option;
        }
        return $options;
    }

    function get_questionnaire_question_result_essay ($qid)
    {
        global $CFG, $DB;

        require_once($CFG->dirroot.'/mod/questionnaire/questionnaire.class.php');

        $sql = "SELECT response
            FROM {questionnaire_response_text} r ".
            'WHERE question_id = '.$qid.' ORDER BY id ASC';
        if (!($records2 = $DB->get_records_sql($sql))) {
            $records2 = array();
        }

        $options = array ();
        foreach ($records2 as $record2)
        {
            $option = array ();
            $option['content'] = $record2->response;
            $option['n'] = 1;

            $options[] = $option;
        }
        return $options;
    }

    function get_questionnaire_results ($id)
    {
        global $CFG, $DB;

        require_once($CFG->dirroot.'/mod/questionnaire/questionnaire.class.php');

        $select = 'survey_id = '.$id.' AND deleted = \'n\' AND type_id != 99';
        $fields = 'id, name, type_id, position, content';
        if (!($records = $DB->get_records_select('questionnaire_question', $select, null, 'position', $fields))) {
            $records = array();
        }

        $questions = array ();
        foreach ($records as $record)
        {
            $question = array ();
            $question['name'] = $record->name;
            $question['content'] = $record->content;
            $question['type'] = $record->type_id;

            $qid = $record->id;

            switch ($record->type_id)
            {
				case QUESSECTIONTEXT:
                    $question['options'] = array ();
                    break;

                case QUESRADIO:
                    $question['options'] = $this->get_questionnaire_question_result_radio ($qid);
                    break;

                case QUESTEXT:
                case QUESESSAY:
                    $question['options'] = $this->get_questionnaire_question_result_essay ($qid);
                    break;
            }

            $questions[] = $question;
        }

        return $questions;
    }

    function get_course_questionnaire_results ($id)
    {
        global $CFG, $DB;

        if (! $questionnaire = $DB->get_record("questionnaire", array("course" => $id))) {
            return array ();
        }

        return $this->get_questionnaire_results ($questionnaire->id);
    }

   function add_cohort_member ($username, $cohort_id)
    {
        global $CFG, $DB;

        $username = utf8_decode ($username);
        $username = strtolower ($username);
        $conditions = array ('username' => $username);
        $user = $DB->get_record('user',$conditions);

        if (!$user)
            return 0;

		$conditions = array ('userid' => $user->id, 'cohortid' => $cohort_id);;
        $member = $DB->get_record('cohort_members',$conditions);

        if ($member)
            return 0;


        cohort_add_member ($cohort_id, $user->id);

        return 1;
    }

    function remove_cohort_member ($username, $cohort_id)
    {
        global $CFG, $DB;

        $username = utf8_decode ($username);
        $username = strtolower ($username);
        $conditions = array ('username' => $username);
        $user = $DB->get_record('user',$conditions);

        if (!$user)
            return 0;

        $conditions = array ('userid' => $user->id, 'cohortid' => $cohort_id);;
        $member = $DB->get_record('cohort_members',$conditions);

        if (!$member)
            return 0;

        cohort_remove_member ($cohort_id, $user->id);

        return 1;
    }

	function multiple_add_cohort_member ($username, $cohorts)
	{
		global $CFG, $DB;

		$username = utf8_decode ($username);
		$username = strtolower ($username);

		$conditions = array ('username' => $username);
		$user = $DB->get_record('user',$conditions);

		if (!$user)
			return;

		foreach ($cohorts as $cohort)
		{
			$this->add_cohort_member ($username, $cohort['id']);
		}

		return 0;
	}


	function multiple_remove_cohort_member ($username, $cohorts)
	{
		global $CFG, $DB;

		$username = utf8_decode ($username);
		$username = strtolower ($username);

		$conditions = array ('username' => $username);
		$user = $DB->get_record('user',$conditions);

		if (!$user)
			return;

		foreach ($cohorts as $cohort)
		{
			$this->remove_cohort_member ($username, $cohort['id']);
		}

		return 0;
	}


    function get_cohorts ()
    {
        global $CFG, $DB;

        $query = "SELECT id, name
          FROM {$CFG->prefix}cohort";

        $cohorts = $DB->get_records_sql($query);

        $rdo = array ();
        foreach ($cohorts as $cohort)
        {
            $c['id'] = $cohort->id;
            $c['name'] = $cohort->name;

            $rdo[] = $c;
        }

        return $rdo;
    }

    function get_themes ()
	{
		$availablethemes = core_component::get_plugin_list('theme');

		$themes = array ();
		foreach ($availablethemes as $name => $path)
		{
			$theme = array ();
			$theme['name'] = $name;
			$themes[] = $theme;
		}

		return $themes;
	}


    function create_course ($course_ext,$skip_fix_course_sortorder=0){
        global $CFG, $DB;

        // set defaults
        $course = new object();
        $course->student  = get_string('defaultcoursestudent');
        $course->students = get_string('defaultcoursestudents');
        $course->teacher  = get_string('defaultcourseteacher');
        $course->teachers = get_string('defaultcourseteachers');
        $course->format = 'topics';

        // override with required ext data
        $course->fullname  = utf8_decode ($course_ext['fullname']);
        $course->shortname  = utf8_decode ( $course_ext['shortname']);
        $course->summary  = utf8_decode ( $course_ext['summary'] );
        $course->lang     = utf8_decode ( $course_ext['course_lang'] );
        $course->startdate     =   $course_ext['startdate'] ;
        $course->idnumber  = $course_ext['idnumber'];


        if ($course_ext['category'])
            $course->category = $course_ext['category'];
        else $course->category = 1; // the misc 'catch-all' category

        $course->timecreated = time();
        $course->visible     = 1;
        $course->enrollable     = 0;


        // store it and log
        if ($newcourseid = $DB->insert_record("course", $course)) {  // Set up new course
            $section = new object();
            $section->course = $newcourseid;   // Create a default section.
            $section->section = 0;
            $section->id = $DB->insert_record("course_sections", $section);
              add_to_log($newcourseid, "course", "new", "view.php?id=$newcourseid", "auth/joomdle auto-creation");
        } else {
            error_log("Could not create new course from Joomdle");
            notify("Serious Error! Could not create the new course!");
            return false;
        }

		// Add manual enrol method
		$enrol = enrol_get_plugin('manual');
        $courserec = $DB->get_record('course', array('id' => $newcourseid));
        $newitemid = $enrol->add_instance($courserec);

        return $newcourseid;
    }

    function update_course ($course_ext)
    {
        global $CFG, $DB;

        $id  = $course_ext['id'];
        $fullname  = utf8_decode ($course_ext['fullname']);
        $shortname  = utf8_decode ($course_ext['shortname']);
        $summary  = utf8_decode ($course_ext['summary']);
        $idnumber  = $course_ext['idnumber'];
        $category  = $course_ext['category'];
        $lang  = $course_ext['course_lang'];
        $startdate     =   $course_ext['startdate'] ;


        $course = new object();
        $course->id     =   $course_ext['id'] ;
        $course->fullname  = utf8_decode ($course_ext['fullname']);
        $course->shortname  = utf8_decode ( $course_ext['shortname']);
        $course->summary  = utf8_decode ( $course_ext['summary'] );
        $course->lang     = utf8_decode ( $course_ext['course_lang'] );
        $course->startdate     =   $course_ext['startdate'] ;
        $course->category     =   $course_ext['category'] ;
        if ($course_ext['idnumber']) // Don't touch idnumber if not set, in case it is used by an external application
			$course->idnumber  = $course_ext['idnumber'];

        $DB->update_record('course', $course);

        return;

        $query =
            "UPDATE
            {$CFG->prefix}course r
            SET
            fullname = '$fullname',  shortname = '$shortname', summary = '$summary', idnumber='$idnumber', category='$category', lang='$lang', startdate='$startdate'
            WHERE id = '$id'";

        $DB->execute_sql($query, false);

    }

    function logoutpage_hook() {
        global $redirect, $USER;

        if ($USER->auth != 'joomdle')
            return;

        $ua = core_useragent::get_user_agent_string ();
        $r = $this->call_method ("logout", $USER->username, $ua);

        // Delete user key from table in Joomla if we had a remember me cookie
        if ((array_key_exists ($r, $_COOKIE))  && ($_COOKIE[$r]))
        {
            $cookieValue = $_COOKIE[$r];
            $cookieArray = explode('.', $cookieValue);

            $this->call_method ("deleteUserKey", $cookieArray[1]);
        }

        setcookie($r, false,  time() - 42000, '/');

        $logout_redirect_to_joomla = get_config('auth/joomdle', 'logout_redirect_to_joomla');

        if ($logout_redirect_to_joomla)
            $redirect = get_config ('auth/joomdle', 'joomla_url').'/components/com_joomdle/views/wrapper/getout.php';
    }


	function get_scorm_item_track_data ($id, $username, $item)
	{
        global $CFG, $DB;
        $user = get_complete_user_data ('username', $username);

        $query =
            "SELECT
            value
            FROM
            {$CFG->prefix}scorm_scoes_track
            WHERE
            userid = ?
            and scormid = ?
            and element = ?
            ";

        $params = array ($user->id, $id, $item);
        $record =  $DB->get_record_sql($query, $params);

        $data = $record->value;

        return $data;
	}

	function get_scorm_track_data ($id, $username)
	{
		$data = array ();
		$data['start_time'] = $this->get_scorm_item_track_data ($id, $username, 'x.start.time');
		$data['total_time'] = $this->get_scorm_item_track_data ($id, $username, 'cmi.core.total_time');
		$data['lesson_status'] = $this->get_scorm_item_track_data ($id, $username, 'cmi.core.lesson_status');
		$data['score'] = $this->get_scorm_item_track_data ($id, $username, 'cmi.core.score.raw');

		return $data;
	}

	function get_scorm_data ($course_id, $username)
	{
		$sections = $this->get_course_mods ($course_id, $username);

		foreach ($sections as $section)
        {
            foreach ($section['mods'] as $mod)
            {
                if ($mod['mod'] == 'scorm')
                {
                    // Scorm object found, we return its info, as we assume only one scorm object per course
                    $cm = get_coursemodule_from_id('scorm', $mod['id']);
                    $scorm_track = $this->get_scorm_track_data ($cm->instance, $username);

                    return ($scorm_track);
                }
                else
                    continue;
            }
        }
	}

	function list_courses_scorm ($available = 0, $sortby = 'created', $guest = 0, $username = '')
	{
		$courses = $this->list_courses ($available, $sortby, $guest, $username);

		$scorm_courses = array ();
		foreach ($courses as $c)
		{
			$c['scorm_data'] = $this->get_scorm_data ($c['remoteid'], 'pepe');

			if (!$c['scorm_data'])
				continue; // Skip non scorm courses

			$scorm_courses[] = $c;
		}

		return $scorm_courses;

	}

    function my_badges ($username, $n = 10)
    {
        global $CFG;
		require_once($CFG->libdir . "/badgeslib.php");

        $username = utf8_decode ($username);
        $username = strtolower ($username);

        $user = get_complete_user_data ('username', $username);

        if (!$user)
            return array ();

        $badges = badges_get_user_badges($user->id, NULL, 0, $n);

        $bs = array ();
        foreach ($badges as $badge)
        {
            $b = array ();
            $b['name'] = $badge->name;
            $b['hash'] = $badge->uniquehash;

            $context = ($badge->type == BADGE_TYPE_SITE) ? context_system::instance() : context_course::instance($badge->courseid);
            $image_url = moodle_url::make_pluginfile_url($context->id, 'badges', 'badgeimage', $badge->id, '/', 'f1', false);
            $b['image_url'] = (string) $image_url;

            $bs[] = $b;
        }

        return $bs;
    }

    function get_certificates_credits ($courses)
    {
        global $CFG, $DB;

		if (count ($courses) == 0)
			return array ();

		$ids = array ();
        foreach ($courses as $course)
        {
            $ids[] = $course['id'];
        }

		list($insql, $params) = $DB->get_in_or_equal($ids);


        // Get course certificate
        $query =
            "SELECT
            id, course, printhours
            FROM
            {$CFG->prefix}certificate
            WHERE
            course  $insql
            ";

        $records =  $DB->get_records_sql($query, $params);

        $cs = array ();
        foreach ($records as $record)
        {
            $c = array ();
            $c['course_id'] = $record->course;
            $c['credits'] = $record->printhours;
            $c['id'] = $record->id;

            $cs[] = $c;
        }

        return $cs;
    }

    function set_section_visible ($course_id, $section, $active)
    {
        global $CFG, $DB;

		set_section_visible($course_id, $section, $active);
    }

    function create_events ($events)
    {
        global $CFG, $DB, $USER;

        foreach ($events as $event) {

            $user = get_complete_user_data('username', $event['username']);
            $event['userid'] = $user->id;  

            // Let us set some defaults.
            $event['modulename'] = '';
            $event['instance'] = 0;
            $event['subscriptionid'] = null;
            $event['uuid']= '';
            $event['format'] = 1;
            $event['repeat'] = 0;
            $event['timeduration'] = $event['timeend'] - $event['timestart'];

            $eventobj = new calendar_event($event);

            // Let's create the event.
            $var = $eventobj->create($event);
        }

        return count ($events);
    }

    function get_courses_not_editing_teachers ($courses)
    {
        global $CFG, $DB;

		if (count ($courses) == 0)
			return array ();

        $ids = array ();
        foreach ($courses as $course)
        {
            $ids[] = $course['id'];
        }

		if (count ($ids) == 0)
			return array ();

        list($insql, $params) = $DB->get_in_or_equal($ids);


        $query = "SELECT distinct (u.id), u.username, u.firstname, u.lastname, c.id as course_id
                 FROM {$CFG->prefix}course as c, {$CFG->prefix}role_assignments AS ra, {$CFG->prefix}user AS u, {$CFG->prefix}context AS ct
                 WHERE c.id = ct.instanceid AND ra.roleid =4 AND ra.userid = u.id AND ct.id = ra.contextid 
                 AND c.visible=1 and u.suspended=0 AND c.id $insql";

        $query .= " ORDER BY lastname, firstname";


        $records =  $DB->get_recordset_sql($query, $params);

        $cs = array ();
        foreach ($records as $record)
        {
            $c = array ();
            $c['course_id'] = $record->course_id;
            $c['firstname'] = $record->firstname;
            $c['lastname'] = $record->lastname;
            $c['username'] = $record->username;

            $cs[] = $c;
        }

        return $cs;
    }


	/* Logs the user in both Joomla and Moodle once auth is passed */
	function user_authenticated_hook (&$user, $username, $password)
	{
		global $redirect, $USER, $SESSION;


		if ($user->auth != 'joomdle')
			return;

		/* Login from password change, don't log in to Joomla */
		if ( (array_key_exists ('password', $_POST))  && (array_key_exists ('newpassword1', $_POST)) && (array_key_exists ('newpassword2', $_POST)) )
			return;

		complete_user_login ($user);

		$redirectless_sso = get_config('auth/joomdle', 'redirectless_sso');

		if ($redirectless_sso)
		{
			// redirect-less login
			$this->log_into_joomla ($username, $password);
			return;
		}

		// Normal login
		$login_data = base64_encode ($username.':'.$password);

		$redirect_url = get_config ('auth/joomdle', 'joomla_url').'/index.php?option=com_joomdle&view=joomdle&task=login&data='.$login_data;
		if (property_exists ($SESSION, 'wantsurl'))
				$redirect_url .= '&wantsurl='. urlencode ($SESSION->wantsurl);

		redirect($redirect_url);
	}

	/* Logs the user into Joomla using cURL to set the cookies */
    function log_into_joomla ($username, $password)
    {
		global $CFG;

        $cookie_path = "/";

        $username = str_replace (' ', '%20', $username);
		$login_data = base64_encode ($username.':'.$password);
		$url = get_config ('auth/joomdle', 'joomla_url').'/index.php?option=com_joomdle&view=joomdle&task=login&data='.$login_data;

        $ch = curl_init();
        // set url
        curl_setopt($ch, CURLOPT_URL, $url);

		$file =  $CFG->tempdir . "/" . random_string(20);

        //return the transfer as a string
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_COOKIEJAR, $file);
        curl_setopt($ch, CURLOPT_HEADER, 1);

        // Accept certificate
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

        $output = curl_exec($ch);
        curl_close($ch);


        if (!file_exists($file))
            die('The temporary file isn\'t there for CURL!');

        $f = fopen ($file, 'ro');

        if (!$f)
            die('The temporary file for CURL could not be opened!');

        while (!feof ($f))
        {
                $line = fgets ($f);
               // if (($line == '\n') || ($line[0] == '#'))
                if (($line == '\n') || ( strncmp ($line, '# ', 2) == 0))
                        continue;
                $parts = explode ("\t", $line);
                if (array_key_exists (5, $parts))
                {
                        $name = $parts[5];
                        $value = trim ($parts[6]);
                        setcookie ($name, $value, 0, $cookie_path);
                }
        }
        unlink ($file);
    }



	function update_joomla_sessions ()
	{
		global $CFG, $DB;
		 $cutoff = time() - 300;

		$query =
		    "SELECT username
		    FROM
			{$CFG->prefix}user
		    WHERE
			auth = 'joomdle' and
			lastaccess > '$cutoff'
			";

		$query = "SELECT username FROM {$CFG->prefix}user WHERE auth = 'joomdle' and lastaccess > '$cutoff';"; ///XXX PREFIX
		$records = $DB->get_records_sql($query);
		$usernames = array();
		foreach ($records as $record)
			$usernames[] = $record->username;

		$updates = $this->call_method ("updateSessions", $usernames);
	}

	function cron() {
		$this->update_joomla_sessions();
	}


} //class

?>
