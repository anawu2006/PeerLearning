<?php
/**
 * @version		
 * @package		Joomdle
 * @copyright	Copyright (C) Antonio Duran Terres
 * @license		GNU/GPL, see LICENSE.php
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

jimport('joomla.user.helper');

define ('CJ_EMPTY_VALUE', -775577);

require_once(JPATH_ADMINISTRATOR.'/'.'components'.'/'.'com_joomdle'.'/'.'helpers'.'/'.'profiletypes.php');


class JoomdleHelperContent
{
	static function _get_xmlrpc_url () {
		$params = JComponentHelper::getParams( 'com_joomdle' );
		$moodle_xmlrpc_server_url = $params->get( 'MOODLE_URL' ).'/webservice/xmlrpc/server.php?wstoken='.$params->get( 'auth_token');

		// Get current language and pass it in Moodle URL
        $lang = JFactory::getLanguage();
        $tag = $lang->getTag();
        $lang = explode('-',$tag);
        $lang = $lang[0];
        $moodle_xmlrpc_server_url.="&lang=".$lang;

		return $moodle_xmlrpc_server_url;
	}

	static function _get_cm () {
		$params = JComponentHelper::getParams( 'com_joomdle' );
		$connection_method = $params->get( 'connection_method' );
		return $connection_method;
	}

	static function get_request ($method, $params = CJ_EMPTY_VALUE, $params2 = CJ_EMPTY_VALUE, $params3 = CJ_EMPTY_VALUE, $params4 = CJ_EMPTY_VALUE, $params5 = CJ_EMPTY_VALUE)
	{
		if ($params == CJ_EMPTY_VALUE)
			$request = xmlrpc_encode_request("joomdle_".$method, array (), array ('encoding' => 'utf8'));
		else if ($params2 == CJ_EMPTY_VALUE)
			$request = xmlrpc_encode_request("joomdle_".$method, array ($params) , array ('encoding' => 'utf8'));
		else if ($params3 == CJ_EMPTY_VALUE)
			$request = xmlrpc_encode_request("joomdle_".$method, array ($params, $params2), array ('encoding' => 'utf8'));
		else if ($params4 == CJ_EMPTY_VALUE)
			$request = xmlrpc_encode_request("joomdle_".$method, array ($params, $params2, $params3), array ('encoding' => 'utf8'));
		else if ($params5 == CJ_EMPTY_VALUE)
			$request = xmlrpc_encode_request("joomdle_".$method, array ($params, $params2, $params3, $params4), array ('encoding' => 'utf8'));
		else
			$request = xmlrpc_encode_request("joomdle_".$method, array ($params, $params2, $params3, $params4, $params5), array ('encoding' => 'utf8'));

		return $request;
	}

	static function call_method_curl ($method, $params = CJ_EMPTY_VALUE, $params2 = CJ_EMPTY_VALUE, $params3 = CJ_EMPTY_VALUE, $params4 = CJ_EMPTY_VALUE, $params5 = CJ_EMPTY_VALUE)
	{
		$comp_params = JComponentHelper::getParams( 'com_joomdle' );
		$moodle_xmlrpc_server_url = JoomdleHelperContent::_get_xmlrpc_url ();

		//$request = xmlrpc_encode_request("auth/joomdle/auth.php/$method", array ($params, $params2, $params3, $params4, $params5)); //, array ('encoding' => 'utf8')); 
		$request =  JoomdleHelperContent::get_request ($method, $params, $params2, $params3, $params4, $params5);

		$headers = array();
		array_push($headers,"Content-Type: text/xml");
		array_push($headers,"Content-Length: ".strlen($request));
		array_push($headers,"\r\n");

		$ch = curl_init();
		curl_setopt( $ch, CURLOPT_URL, $moodle_xmlrpc_server_url); # URL to post to
		curl_setopt( $ch, CURLOPT_RETURNTRANSFER, 1 ); # return into a variable
		curl_setopt( $ch, CURLOPT_HTTPHEADER, $headers ); # custom headers, see above
		curl_setopt( $ch, CURLOPT_POSTFIELDS, $request );
		curl_setopt( $ch, CURLOPT_CUSTOMREQUEST, 'POST' ); # This POST is special, and uses its specified Content-type
		$file = curl_exec( $ch ); # run!
		curl_close($ch); 

		$response = xmlrpc_decode($file, 'utf8');

		if (is_array ($response))
			if (xmlrpc_is_fault ($response))
			{
				echo "XML-RPC Error (".$response['faultCode']."): ".$response['faultString'];
				die; // XXX Something softer?
			}

		return $response;
	}

	static function call_method_fgc ($method, $params = CJ_EMPTY_VALUE, $params2 = CJ_EMPTY_VALUE, $params3 = CJ_EMPTY_VALUE, $params4 = CJ_EMPTY_VALUE, $params5 = CJ_EMPTY_VALUE)
	{
		$comp_params = JComponentHelper::getParams( 'com_joomdle' );
		$moodle_xmlrpc_server_url = JoomdleHelperContent::_get_xmlrpc_url ();

		$request =  JoomdleHelperContent::get_request ($method, $params, $params2, $params3, $params4, $params5);

		$context = stream_context_create(array('http' => array(
					'method' => "POST",
					'header' => "Content-Type: text/xml",
					'content' => $request
						)));
		$file = file_get_contents($moodle_xmlrpc_server_url , false, $context);

		$response = xmlrpc_decode($file, 'utf8');

		if (is_array ($response))
			if (xmlrpc_is_fault ($response))
			{
				echo "XML-RPC Error (".$response['faultCode']."): ".$response['faultString'];
				die; // XXX Something softer?
			}

		return $response;
	}


	static function call_method ($method, $params = CJ_EMPTY_VALUE, $params2 = CJ_EMPTY_VALUE, $params3 = CJ_EMPTY_VALUE, $params4 = CJ_EMPTY_VALUE, $params5 = CJ_EMPTY_VALUE)
	{


		$cm = JoomdleHelperContent::_get_cm ();

		if ($cm == 'fgc')
			$response = JoomdleHelperContent::call_method_fgc ($method, $params, $params2, $params3, $params4,  $params5);
		else //if ($cm == 'curl')
			$response = JoomdleHelperContent::call_method_curl ($method, $params, $params2, $params3, $params4,  $params5);

		return $response;
	}

	static function call_method_debug ($method, $params = CJ_EMPTY_VALUE, $params2 = CJ_EMPTY_VALUE, $params3 = CJ_EMPTY_VALUE, $params4 = CJ_EMPTY_VALUE)
	{
		$cm = JoomdleHelperContent::_get_cm ();

		if ($cm == 'fgc')
			$response = JoomdleHelperContent::call_method_debug_fgc ($method, $params, $params2, $params3, $params4);
		else
			$response = JoomdleHelperContent::call_method_debug_curl ($method, $params, $params2, $params3, $params4);

		return $response;
	}

	static function call_method_debug_fgc ($method, $params = CJ_EMPTY_VALUE, $params2 = CJ_EMPTY_VALUE)
	{
		$moodle_xmlrpc_server_url = JoomdleHelperContent::_get_xmlrpc_url ();

	//	$request = xmlrpc_encode_request("auth/joomdle/auth.php/$method", array ($params, $params2));

		$request =  JoomdleHelperContent::get_request ($method, $params, $params2);

		$context = stream_context_create(array('http' => array(
					'method' => "POST",
					'header' => "Content-Type: text/xml ",
					'content' => $request
						)));
		$file = @file_get_contents($moodle_xmlrpc_server_url , false, $context);
		$response = xmlrpc_decode($file);

		return $response;
	}

	static function call_method_debug_curl ($method, $params = CJ_EMPTY_VALUE, $params2 = CJ_EMPTY_VALUE)
	{
		$moodle_xmlrpc_server_url = JoomdleHelperContent::_get_xmlrpc_url ();

		//$request = xmlrpc_encode_request("auth/joomdle/auth.php/$method", array ($params, $params2));
		$request =  JoomdleHelperContent::get_request ($method, $params, $params2);
		$headers = array();
		array_push($headers,"Content-Type: text/xml");
		array_push($headers,"Content-Length: ".strlen($request));
		array_push($headers,"\r\n");

		$ch = curl_init();
		curl_setopt( $ch, CURLOPT_URL, $moodle_xmlrpc_server_url); # URL to post to
		curl_setopt( $ch, CURLOPT_RETURNTRANSFER, 1 ); # return into a variable
		curl_setopt( $ch, CURLOPT_HTTPHEADER, $headers ); # custom headers, see above
		curl_setopt( $ch, CURLOPT_POSTFIELDS, $request );
		curl_setopt( $ch, CURLOPT_CUSTOMREQUEST, 'POST' ); # This POST is special, and uses its specified Content-type
		$response = curl_exec( $ch ); # run!
		curl_close($ch); 

		$response = xmlrpc_decode ($response);

/*		if (is_array ($response))
			if (xmlrpc_is_fault ($response))
			{
				echo "XML-RPC Error (".$response['faultCode']."): ".$response['faultString'];
				die; // XXX Something softer?
			}
*/
		return $response;
	}

	static function get_file ($file)
    {
        $cm = JoomdleHelperContent::_get_cm ();

		if ($cm == 'fgc')
				$response = file_get_contents ($file, FALSE, NULL);
		else
				$response = JoomdleHelperContent::get_file_curl ($file);

		return $response;
	}

	static function get_file_curl ($file)
	{
			$ch = curl_init();
			// set url
			curl_setopt($ch, CURLOPT_URL, $file);

			//return the transfer as a string
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

			// $output contains the output string
			$output = curl_exec($ch);

			// close curl resource to free up system resources
			curl_close($ch);

			return $output;
	}


	static function getCourseEvents($id)
	{
		return JoomdleHelperContent::call_method ('get_upcoming_events', $id);
	}

	static function getCourseInfo ($id, $username = '')
	{
		return JoomdleHelperContent::call_method ('get_course_info', (int) $id , $username);
	}

	static function getCourseCategories ($id = 0)
	{
		return JoomdleHelperContent::call_method ('get_course_categories', $id);
	}

	static function getCourseCategory ($id, $username)
	{
		return JoomdleHelperContent::call_method ('courses_by_category', $id, 0, $username);
	}

	static function getCourseNews ($id)
	{
		return JoomdleHelperContent::call_method ('get_news_items', $id);
	}

	static function getCourseStudentsNo ($id)
	{
		return JoomdleHelperContent::call_method ('get_course_students_no', $id);
	}

	static function getAssignmentSubmissions ($id)
	{
		return JoomdleHelperContent::call_method ('get_assignment_submissions', $id);
	}

	static function getAssignmentGrades ($id)
	{
		return JoomdleHelperContent::call_method ('get_assignment_grades', $id);
	}

	static function getCourseDailyStats ($id)
	{
		return JoomdleHelperContent::call_method ('get_course_daily_stats', $id);
	}

	static function getCourseList ($enrollable_only = 0, $orderby = 'fullname ASC', $guest = 0, $username = '')
	{
		return JoomdleHelperContent::call_method ('list_courses', (int) $enrollable_only, $orderby, (int) $guest, $username);
	}

	static function getStudentsNo ()
	{
		return JoomdleHelperContent::call_method ('get_student_no');
	}

	static function getCoursesNo ()
	{
		return JoomdleHelperContent::call_method ('get_course_no');
	}

	static function getEnrollableCoursesNo ()
	{
		return JoomdleHelperContent::call_method ('get_enrollable_course_no');
	}

	static function getAssignmentsNo ()
	{
		return JoomdleHelperContent::call_method ('get_total_assignment_submissions');
	}

	static function getLastWeekStats ()
	{
		return JoomdleHelperContent::call_method ('get_site_last_week_stats');
	}

	static function getCourseTeachers ($id)
	{
		return JoomdleHelperContent::call_method ('get_course_editing_teachers', $id);
	}

	static function getCourseContents ($id)
	{
		return JoomdleHelperContent::call_method ('get_course_contents', $id);
	}

	static function enrolUser ($username, $id)
	{
		$params = JComponentHelper::getParams( 'com_joomdle' );
        if ($params->get( 'use_profiletypes' ))
        {
            $moodle_role = JoomdleHelperProfiletypes::get_user_profile_role ($username);
            if (!$moodle_role)
                $moodle_role = 5;
            $return = JoomdleHelperContent::call_method ('enrol_user', $username, (int) $id, (int) $moodle_role);
        }
        else
            $return = JoomdleHelperContent::call_method ('enrol_user', $username, (int) $id, 5); //5  = student


        return $return;

	}

	static function getMyCourses ($username = "")
	{
		if ($username)
		{
			$user_id = JUserHelper::getUserId($username);
            $user = JFactory::getUser($user_id);
		}
		else 
			$user = JFactory::getUser();

		if (!$user)
			return array ();

		$id = $user->get('id');
		$username = $user->get('username');

		$cursos = JoomdleHelperContent::call_method ('my_courses', $username, 0);

		return $cursos;
	}

	static function is_enroled ($username, $course_id)
	{
		if ($username)
		{
			$user_id = JUserHelper::getUserId($username);
            $user = JFactory::getUser($user_id);
		}
		else 
			$user = JFactory::getUser();

		if (!$user)
			return 0;

		$username = $user->get('username');

		$cursos = JoomdleHelperContent::call_method ('my_courses', $username, 0);

		foreach ($cursos as $curso)
		{
			if ($curso['id'] == $course_id)
				return 1;
		}
		return 0;
	}

	static function getMyEvents ()
	{
		$user = JFactory::getUser();
		if (!$user)
			return array ();

		$id = $user->get('id');
		$username = $user->get('username');

		$events = JoomdleHelperContent::call_method ('get_my_events', $username);

		return $events;
	}

	static function getMyNews ()
	{
		$user = JFactory::getUser();
		if (!$user)
			return array ();

		$id = $user->get('id');
		$username = $user->get('username');

		$news = JoomdleHelperContent::call_method ('get_my_news', $username);

		return $news;

		/*
		print_r ($news);
		exit ();

		$cursos = JoomdleHelperContent::call_method ('my_courses', $username, 0);

		$i = 0;
		$course_news = array ();
		foreach ($cursos as $id => $curso) {
			$id = $curso['id'];
			$course_news[$i]['news'] = JoomdleHelperContent::getCourseNews ($id);
			$course_news[$i]['info'] = JoomdleHelperContent::getCourseInfo ($id);
			$i++;
		}

		return ($course_news);
		*/

	}

	static function getCourseGradeCategories ($id)
	{
		return JoomdleHelperContent::call_method ('get_course_grade_categories', $id);
	}

	/* Note: Moodle only users have negative ID */
	static function getMoodleUsers ($limitstart = 0, $limit = 20, $order, $order_dir, $search = "")
	{
		$users = JoomdleHelperContent::call_method ('get_moodle_users', $limitstart, $limit, $order, $order_dir, $search);
		$i = 0;
		if (!is_array ($users))
			return array();

		foreach ($users as $user)
		{
			$users[$i]['id'] = -$users[$i]['id']; // Set negative. If is a Joomla user, next lines change its ID again
			$users[$i]['m_account'] = 1;
			$id = JUserHelper::getUserId($users[$i]['username']);
			if ($id)
			{
				$user_obj = JFactory::getUser($id);
				if (!$user['admin'])
				{
					// If not moodle admin, check if joomla admin
					if (JoomdleHelperContent::is_admin ($user_obj->id))
						$users[$i]['admin'] = 1;
					else $users[$i]['admin'] = 0;
				}

				$users[$i]['j_account'] = 1;
				$users[$i]['id'] = $id;
			}
			else
				$users[$i]['j_account'] = 0;


			$i++;
		}

		return $users;
	}

	static function getMoodleUsersNumber ($search = "")
	{
		return  JoomdleHelperContent::call_method ('get_moodle_users_number', $search);
	}

	static function getJoomlaUsers ($limitstart, $limit, $order, $order_dir, $search = "")
	{
		$db           = JFactory::getDBO();

		$limit_c = "";
		if ($limit)
			$limit_c = " LIMIT $limitstart, $limit";

		if ($order != "")
			$order_c = " ORDER BY $order $order_dir";
		else $order_c = "";

		if ($search)
			$query = 'SELECT *' .
                                ' FROM #__users' .
                                ' WHERE (username LIKE '.$search.' OR email LIKE '.$search.' OR name LIKE '.$search.')'.
				$order_c.
				$limit_c;
		else
			$query = 'SELECT *' .
                                ' FROM #__users'.
				$order_c.
				$limit_c;
		$db->setQuery($query);
		$jusers = $db->loadObjectList();

		$musers = JoomdleHelperContent::call_method ('get_moodle_users', $limitstart, $limit, $order, $order_dir, $search);
		$mu_by_usernames = array ();
		foreach ($musers as $user)
		{
			$mu_by_usernames[$user['username']] = $user;
		}

		$rdo = array();
		$i = 0;
		foreach ($jusers as $user)
		{
			$item = array ();
			$item = get_object_vars ($user);
			$item['j_account'] = 1;
			if (!array_key_exists ($user->username, $mu_by_usernames))
			{
				// User not in Moodle
				$item['m_account'] = 0;
				$item['auth'] = 'N/A';
			}
			else
			{
				// User in Moodle
				$item['m_account'] = 1;
				$item['auth'] = $mu_by_usernames[$user->username]['auth']; 
			}

			if ((!$item['m_account']) || (!$mu_by_usernames[$user->username]['admin']))
			{
				if (JoomdleHelperContent::is_admin ($user->id))
					$item['admin'] = 1;
				else $item['admin'] = 0;
			}
			else $item['admin'] = 1;


			$rdo[] = $item;
		}

		return ($rdo);
	}

	static function getJoomlaUsersNumber ($search = "")
	{
		$db           = JFactory::getDBO();
		if ($search)
			$query = 'SELECT count(id) as n' .
                                ' FROM #__users' .
                                ' WHERE (username LIKE '.$search.' OR email LIKE '.$search.' OR name LIKE '.$search.')';
		else
			$query = 'SELECT count(id) as n' .
                                ' FROM #__users';
		$db->setQuery($query);
		$number = $db->loadAssoc();

		return ($number['n']);
	}

static function multisort($array, $order_dir, $sort_by, $key1, $key2=NULL, $key3=NULL, $key4=NULL, $key5=NULL, $key6=NULL, $key7=NULL, $key8=NULL){
    // sort by ?

if (!count ($array))
	return $array;

    foreach ($array as $pos =>  $val)
        $tmp_array[$pos] = $val[$sort_by];

if ($order_dir == 'desc')
    arsort($tmp_array);
else
    asort($tmp_array);
   
    // display however you want
    foreach ($tmp_array as $pos =>  $val){
        $return_array[$pos][$sort_by] = $array[$pos][$sort_by];
        $return_array[$pos][$key1] = $array[$pos][$key1];
        if (isset($key2)){
            $return_array[$pos][$key2] = $array[$pos][$key2];
            }
        if (isset($key3)){
            $return_array[$pos][$key3] = $array[$pos][$key3];
            }
        if (isset($key4)){
            $return_array[$pos][$key4] = $array[$pos][$key4];
            }
        if (isset($key5)){
            $return_array[$pos][$key5] = $array[$pos][$key5];
            }
        if (isset($key6)){
            $return_array[$pos][$key6] = $array[$pos][$key6];
            }
        if (isset($key7)){
            $return_array[$pos][$key7] = $array[$pos][$key7];
            }
        if (isset($key8)){
            $return_array[$pos][$key8] = $array[$pos][$key8];
            }
        }
    return $return_array;
    }

	static function is_admin($userid)
	{
		jimport( 'joomla.user.helper' );
		$user = JFactory::getUser($userid);
		$groups = JUserHelper::getUserGroups($user->id);
 
		$admin_groups = array(); //put all the groups that you consider to be admins
      	$admin_groups[] = "Super Users";
      	$admin_groups[] = "Administrator";
 
		foreach($admin_groups as $temp)
		{
			if(!empty($groups[$temp]))
			{
				return true;
				break;
			}
		}
 
		return false;
	}

	/* Note: Moodle only users have negative ID */
	static function getAllUsers ($limitstart, $limit, $order, $order_dir, $search)
	{
		$lang = JFactory::getLanguage();
		$db           = JFactory::getDBO();

		$searchEscaped = $db->Quote( '%'.$db->escape( $search, true ).'%', false );

		if ($search)
			$query = 'SELECT *' .
                                ' FROM #__users' .
                                ' WHERE (username LIKE '.$searchEscaped.' OR email LIKE '.$searchEscaped.' OR name LIKE '.$searchEscaped.')';
		else
			$query = 'SELECT *' .
                                ' FROM #__users';
		$db->setQuery($query);
		$jusers = $db->loadObjectList();
		$ju_by_usernames = array ();
		foreach ($jusers as $user)
		{
			$ju_by_usernames[$user->username] = $user;
		}

		$musers = JoomdleHelperContent::call_method ('get_moodle_users', 0, 100000, $order, $order_dir, $search);
		$mu_by_usernames = array ();
		foreach ($musers as $user)
		{
			$mu_by_usernames[$user['username']] = $user;
		}
		
		$rdo = array();
		foreach ($jusers as $user)
		{
			$item = get_object_vars ($user);
			$item['name_lower'] = strtolower($item['name']);;
			$item['username_lower'] = strtolower($item['username']);;
			$item['email_lower'] = strtolower($item['email']);;

			$item['j_account'] = 1;


			if (!array_key_exists ($user->username, $mu_by_usernames))
			{
				$item['m_account'] = 0;

				if (JoomdleHelperContent::is_admin ($user->id))
					$item['admin'] = 1;
				else $item['admin'] = 0;

				$item['auth'] = 'N/A';
			}
			else
			{
				// User in Joomla and Moodle
				$item['m_account'] = 1;

				if (!$mu_by_usernames[$user->username]['admin'])
				{
					if (JoomdleHelperContent::is_admin ($user->id))
						$item['admin'] = 1;
					else $item['admin'] = 0;
				}
				else $item['admin'] = 1;

				$item['auth'] = $mu_by_usernames[$user->username]['auth']; 
			}

			$rdo[] = $item;
		}
		
		// Get Moodle only users: those without a Joomla account
		$rdo2 =  array ();
		foreach ($musers as $user)
		{
			$item = array ();
			$item = $user;
			$item['m_account'] = 1;
			if (!array_key_exists ($user['username'], $ju_by_usernames))
			{
				// User not found in Joomla -> not a Joomdle user
				$item['j_account'] = 0;
				$item['m_account'] = 1;

				$item['id'] = - $user['id'];

				$item['name_lower'] = strtolower($item['name']);;
				$item['username_lower'] = strtolower($item['username']);;
				$item['email_lower'] = strtolower($item['email']);;

				$rdo2[] = $item;
			}
		}

		/* Kludge for uppercases */
		if ($order == 'name')
			$order = 'name_lower';
		if ($order == 'username')
			$order = 'username_lower';
		if ($order == 'email')
			$order = 'email_lower';
		
		$merged = array_merge ($rdo, $rdo2);
		$all = JoomdleHelperContent::multisort ($merged, $order_dir, $order, 'id', 'name', 'username', 'email', 'm_account', 'j_account', 'auth', 'admin');

		if ($limit)
			return array_slice ($all, $limitstart, $limit);
		else
			return $all;
	}

	static function getAllUsersNumber ($search)
	{
		$n = count (JoomdleHelperContent::getAllUsers (0, 100000, 'username', 'asc', $search));
		return $n;
	}

	static function getJoomdleUsers ($limitstart, $limit, $order, $order_dir, $search)
	{
		$lang = JFactory::getLanguage();
		$db           = JFactory::getDBO();

		if ($order != "")
			$order_c = " ORDER BY $order $order_dir";
		else $order_c = "";

		if ($search)
			$query = 'SELECT *' .
                                ' FROM #__users' .
                                ' WHERE (username LIKE '.$search.' OR email LIKE '.$search.' OR name LIKE '.$search.')'.
				$order_c;
		else
			$query = 'SELECT *' .
                                ' FROM #__users'.
				$order_c;
		$db->setQuery($query);
		$jusers = $db->loadObjectList();

		$musers = JoomdleHelperContent::call_method ('get_moodle_users', $limitstart, $limit, $order, $order_dir, $search);
		$mu_by_usernames = array ();
		foreach ($musers as $user)
		{
			$mu_by_usernames[$user['username']] = $user;
		}

		$rdo = array();
		$i = 0;
		foreach ($jusers as $user)
		{
			$item = array ();
			$item = get_object_vars ($user);
			$item['j_account'] = 1;
			if (!array_key_exists ($user->username, $mu_by_usernames))
				continue; // User not in Moodle -> not a joomdle user

			// User does not have joomdle auth method -> not a joomdle user
			if ($mu_by_usernames[$user->username]['auth'] != 'joomdle')
				continue;

			$item['m_account'] = 1;

			if (JoomdleHelperContent::is_admin ($user->id))
				$item['admin'] = 1;
			else $item['admin'] = 0;

			$item['auth'] = $mu_by_usernames[$user->username]['auth']; 

			$rdo[] = $item;
		}

		if ($limit)
			return array_slice ($rdo, $limitstart, $limit);
		else
			return $rdo;
	}

	/* Note: Moodle only users have negative ID */
	static function getNotJoomdleUsers ($limitstart, $limit, $order, $order_dir, $search)
	{
		$lang = JFactory::getLanguage();
		$db           = JFactory::getDBO();
		$searchEscaped = $db->Quote( '%'.$db->escape( $search, true ).'%', false );

		if ($search)
			$query = 'SELECT *' .
                                ' FROM #__users' .
                                ' WHERE (username LIKE '.$searchEscaped.' OR email LIKE '.$searchEscaped.' OR name LIKE '.$searchEscaped.')';
		else
			$query = 'SELECT *' .
                                ' FROM #__users';
		$db->setQuery($query);
		$jusers = $db->loadObjectList();
		$ju_by_usernames = array ();
		foreach ($jusers as $user)
		{
			$ju_by_usernames[$user->username] = $user;
		}

		$musers = JoomdleHelperContent::call_method ('get_moodle_users', $limitstart, $limit, $order, $order_dir, $search);
		$mu_by_usernames = array ();
		foreach ($musers as $user)
		{
			$mu_by_usernames[$user['username']] = $user;
		}

		$rdo = array ();
		foreach ($jusers as $user)
		{
			$item = array ();
			$item = get_object_vars ($user);
			$item['j_account'] = 1;
			if (!array_key_exists ($user->username, $mu_by_usernames))
			{
				// User not found in Moodle -> not a Joomdle user
				$item['m_account'] = 0;

				if (JoomdleHelperContent::is_admin ($user->id))
					$item['admin'] = 1;
				else $item['admin'] = 0;

				$item['auth'] = 'N/A';
			}
			else
			{
				// User in Joomla and Moodle
				$item['m_account'] = 1;

				if (!$mu_by_usernames[$user->username]['admin'])
				{
					if (JoomdleHelperContent::is_admin ($user->id))
						$item['admin'] = 1;
					else $item['admin'] = 0;
				}
				else $item['admin'] = 1;

				$item['auth'] = $mu_by_usernames[$user->username]['auth']; 
			}

			if (($item['m_account'] == 1) && ($item['auth'] == 'joomdle'))
				continue;

			$rdo[] = $item;
		}

		// Get Moodle only users: those without a Joomla account
		$rdo2 =  array ();
		foreach ($musers as $user)
		{
			$item = array ();
			$item = $user;
			$item['m_account'] = 1;
			if (!array_key_exists ($user['username'], $ju_by_usernames))
			{
				// User not found in Joomla -> not a Joomdle user
				$item['j_account'] = 0;
				$item['m_account'] = 1;

				$item['id'] = - $user['id'];

				$rdo2[] = $item;
			}
		}

		$merged = array_merge ($rdo, $rdo2);
		$all = JoomdleHelperContent::multisort ($merged, $order_dir, $order, 'id', 'name', 'username', 'email', 'm_account', 'j_account', 'auth', 'admin');
		if ($limit)
			return array_slice ($all, $limitstart, $limit);
		else
			return $all;
	}

	static function add_moodle_users ($user_ids)
	{
		foreach ($user_ids as $id)
		{
			/* If user not already in Joomla, warn user and continue to next item */
			if ($id < 0)
			{
				JError::raiseWarning(500, JText::_( 'COM_JOOMDLE_USER_ID_DOES_NOT_EXIT_IN_JOOMLA' ) . ": " . $id);
				continue;
			}
			$user = JFactory::getUser($id);
			/* If user already in Moodle, warn user and continue to next item */
			if (JoomdleHelperContent::call_method ('user_exists', $user->username))
			{
				JError::raiseWarning(500, JText::_( 'COM_JOOMDLE_USER_ALREADY_EXISTS_IN_MOODLE' ). ": ".$user->username );
				continue;
			}
			JoomdleHelperContent::call_method ('create_joomdle_user', $user->username);
		}
	}

	static function migrate_users_to_joomdle ($user_ids)
	{
		foreach ($user_ids as $id)
		{
			/* If user not already in Joomla, warn user and continue to next item */
			if ($id < 0)
			{
				JError::raiseWarning(500, JText::_( 'COM_JOOMDLE_USER_ID_DOES_NOT_EXIT_IN_JOOMLA' ) . ": " . $id);
				continue;
			}
			$user = JFactory::getUser($id);
			/* If user not already in Moodle, warn user and continue to next item */
			if (!JoomdleHelperContent::call_method ('user_exists', $user->username))
			{
				JError::raiseWarning(500, JText::_( 'COM_JOOMDLE_USER_ID_DOES_NOT_EXIT_IN_MOODLE' ). ": ".$user->username );
				continue;
			}
			JoomdleHelperContent::call_method ('migrate_to_joomdle', $user->username);
		}
	}

	static function sync_moodle_profiles ($user_ids)
	{
		foreach ($user_ids as $id)
		{
			/* If user not already in Joomla, warn user and continue to next item */
			if ($id < 0)
			{
				JError::raiseWarning(500, JText::_( 'COM_JOOMDLE_USER_ID_DOES_NOT_EXIT_IN_JOOMLA' ) . ": " . $id);
				continue;
			}
			$user = JFactory::getUser($id);
			/* If user not already in Moodle, warn user and continue to next item */
			if (!JoomdleHelperContent::call_method ('user_exists', $user->username))
			{
				JError::raiseWarning(500, JText::_( 'COM_JOOMDLE_USER_ID_DOES_NOT_EXIT_IN_MOODLE' ). ": ".$user->username );
				continue;
			}
			JoomdleHelperContent::call_method ('create_joomdle_user', $user->username);
		}
	}

	static function sync_joomla_profiles ($user_ids)
	{
		foreach ($user_ids as $id)
		{
			/* If user not already in Joomla, warn user and continue to next item */
			if ($id < 0)
			{
				JError::raiseWarning(500, JText::_( 'COM_JOOMDLE_USER_ID_DOES_NOT_EXIT_IN_JOOMLA' ) . ": " . $id);
				continue;
			}
			$user = JFactory::getUser($id);
			/* If user not already in Moodle, warn user and continue to next item */
			if (!JoomdleHelperContent::call_method ('user_exists', $user->username))
			{
				JError::raiseWarning(500, JText::_( 'CJ_USER_ID_DOES_NOT_EXIT_IN_MOODLE' ). ": ".$user->username );
				continue;
			}
			JoomdleHelperMappings::sync_user_to_joomla ( $user->username);
		}
	}

	static function create_joomla_users ($user_ids)
	{
		foreach ($user_ids as $id)
		{
			/* If user already in Joomla, warn user and continue to next item */
			if ($id >= 0)
			{
				JError::raiseWarning(500, JText::_( 'COM_JOOMDLE_USER_ALREADY_EXISTS_IN_JOOMLA' ). ": ".$id );
				continue;
			}
			/* Here we already know ID is from Moodle, as it is not from Joomla */
			$moodle_user = JoomdleHelperContent::call_method ('user_details_by_id', -$id); //We remove the minus
			if (!$moodle_user)
			{
				JError::raiseWarning(500, JText::_( 'COM_JOOMDLE_USER_ID_DOES_NOT_EXIT_IN_MOODLE' ). ": ".$id );
				continue;
			}
			$username = $moodle_user['username'];
			JoomdleHelperContent::create_joomla_user ($username);
		}
	}

	static function create_joomla_user ($username)
	{
		$mainframe = JFactory::getApplication( 'site' );


		$db           = JFactory::getDBO();
		// Get required system objects
		$user           = clone(JFactory::getUser());
		$config         = JFactory::getConfig();
		$authorize      = JFactory::getACL();
		$document   = JFactory::getDocument();

		$usersConfig    = JComponentHelper::getParams( 'com_users' );
		$useractivation = $usersConfig->get( 'useractivation' );

		$newUsertype = 'Registered';

		$moodle_user['username'] = $username;
		$user_details =JoomdleHelperContent::call_method ('user_details', $username);


		$moodle_user['name'] = $user_details['firstname'] .' '.$user_details['lastname'];
		$moodle_user['email'] = $user_details['email'];

		$password = JUserHelper::genRandomPassword();
		$password = preg_replace('/[\x00-\x1F\x7F]/', '', $password); //Disallow control chars in the email

	//	$password = ''; //XXX FOR NOT CHANGING PASSWORD FOR USERS

		$moodle_user['password'] = $password;
		$moodle_user['password2'] = $password;

		$moodle_user['activation'] = '';
		$moodle_user['sendEmail'] = 0;

		// Bind the post array to the user object
		if (!$user->bind( $moodle_user, 'usertype' )) {
				JError::raiseError( 500, $user->getError());
		}

		// Set some initial user values
		$user->set('id', 0);
		$user->set('usertype', $newUsertype);

		$system = 2; // ID of Registered
		$user->groups = array ();
		$user->groups[] = $system;


		$date = JFactory::getDate();
		$user->set('registerDate', $date->toSql());

		jimport('joomla.user.helper');

		$user->set('lastvisitDate', $db->getNullDate());

		// If there was an error with registration, set the message and display form
		if ( !$user->save() )
		{
				JError::raiseWarning('', JText::_( $user->getError()));
				return false;
		}

		// Send registration  mail
	   //XXX Email is already sent by joomla core
     //   JoomdleHelperContent::send_registration_email ($user, $password);
    }

    static function send_registration_email ($user, $password)
    {
        $config = JFactory::getConfig();

        $mailer =& JFactory::getMailer();
        // Set the e-mail parameters
        $sender = array(
                $config->get( 'mailfrom' ),
                $config->get( 'fromname' ) );

        $mailer->setSender($sender);

        $subject           = JText::_ ('COM_JOOMDLE_MIGRATION_EMAIL_SUBJECT');
        $body           = sprintf (JText::_ ('COM_JOOMDLE_MIGRATION_EMAIL_BODY'), $user->name, JURI::root (), $user->username, $password);

        $mailer->addRecipient($user->email);
        $mailer->setSubject ($subject);
        $mailer->setBody ($body);

        // Send the e-mail
        $send =& $mailer->Send();
        if (!$send)
        {
                $this->setError('ERROR_SENDING_CONFIRMATION_EMAIL');
                return false;
        }
    }

	static function getJumpURL ()
	{
		$params = JComponentHelper::getParams( 'com_joomdle' );
		$moodle_auth_land_url = $params->get( 'MOODLE_URL' ).'/auth/joomdle/land.php';

		 $linkstarget = $params->get( 'linkstarget' );
		 if ($linkstarget == 'wrapper')
			 $use_wrapper = 1;
		 else $use_wrapper = 0;

		$user = JFactory::getUser();
		$id = $user->get('id');
		$username = $user->get('username');


		$db           = JFactory::getDBO();
		$query = 'SELECT session_id' .
			' FROM #__session' .
			' WHERE userid =';
		$query .= "'$id'";
		$db->setQuery($query);
		$sessions = $db->loadObjectList();

		if ($db->getErrorNum()) {
			JError::raiseWarning( 500, $db->stderr() );
		}

		if (count($sessions))
		foreach ($sessions as $session)
			$token = md5 ($session->session_id);

                $jump_url = $moodle_auth_land_url."?username=$username&token=$token&use_wrapper=$use_wrapper";

		return $jump_url;
	}

	static function getMenuItem ()
	{
		$app = JFactory::getApplication();
		$menu = $app->getMenu();
		$menuItem = $menu->getActive();

		if (!$menuItem)
			return;

		$itemid = $menuItem->id;

		return $itemid;
	}

	static function get_language_str ($lang)
	{
		require_once (dirname(__FILE__).'/'.'languages.php');
		$l = explode ("_", $lang);
		$index = $l[0];

		return $LANGUAGES["$index"];
	}

	static function user_id_exists ($id)
	{
		$db           = JFactory::getDBO();

		$id = $db->Quote ($id);
		$query = "SELECT id from #__users where id=$id";
		$db->setQuery($query);
		$users = $db->loadObjectList();

		if ($db->getErrorNum()) {
			JError::raiseWarning( 500, $db->stderr() );
		}

		return (count ($users) != 0);
	}

	static function system_ok ()
	{
		$php_exts = get_loaded_extensions ();
		$xmlrpc_enabled = in_array ('xmlrpc', $php_exts);

		if (!$xmlrpc_enabled)
			return false;

		$comp_params = JComponentHelper::getParams( 'com_joomdle' );
		$connection = $comp_params->get( 'connection_method' );

		if ($connection == 'fgc')
			$connection_method_enabled = ini_get ('allow_url_fopen');
		else if ($connection == 'curl')
			$connection_method_enabled = function_exists('curl_version') == "Enabled";

		if (!$connection_method_enabled)
			return false;

		/* Test Moodle Web services in joomdle plugin */
		$response = JoomdleHelperContent::call_method_debug ('system_check');
		if ($response == '')
			return false;
		else if ((is_array ($response)) && (xmlrpc_is_fault ($response)))
			return false;
		else {
			if ($response ['joomdle_auth'] != 1)
				return false;
			else if ($response['joomdle_configured'] == 0)
				return false;
			else if ($response['test_data'] != 'It works')
				return false;
		}

		return true;
	}

	static function check_joomdle_system ()
	{
	    $params = JComponentHelper::getParams( 'com_joomdle' );

		$joomla_config = new JConfig();

		/* PHP XMLRPC extension enabled */
		$php_exts = get_loaded_extensions ();
		$xmlrpc_enabled = in_array ('xmlrpc', $php_exts);
		$system[2]['description'] = JText::_ ('COM_JOOMDLE_XMLRPC_PHP_EXTENSION');
		$system[2]['value'] = $xmlrpc_enabled;
		if ($system[2]['value'] == '0')
			$system[2]['error'] =  JText::_ ('COM_JOOMDLE_XMLRPC_PHP_EXTENSION_ERROR');
		else $system[2]['error'] = '';


		/* Error reporting */
		/*
        $display_errors = ini_get('display_errors');
        $error_reporting = ini_get('error_reporting');
        $system[6]['description'] = JText::_ ('COM_JOOMDLE_ERROR_REPORTING');

        if (($display_errors) && ($error_reporting & E_DEPRECATED))
        {
            $system[6]['error'] =  JText::_ ('COM_JOOMDLE_DEPRECATED_ERRORS_ON');
            $system[6]['value'] = 0;
        }
        else
        {
            $system[6]['error'] = '';
            $system[6]['value'] = 1;
        }
*/

		/*
		$system[0]['description'] = JText::_ ('COM_JOOMDLE_JOOMLA WEB SERVICES');
		$system[0]['value'] = $joomla_config->xmlrpc_server;
		if ($joomla_config->xmlrpc_server == '0')
			$system[0]['error'] =  JText::_ ('COM_JOOMDLE_JOOMLA WEB SERVICES ERROR');
		else $system[0]['error'] = '';
*/
		/* Mandatory Joomdle plugins enabled */

		$system[5]['description'] = JText::_ ('COM_JOOMDLE_JOOMDLEHOOKS_PLUGIN');
		$system[5]['value'] = JPluginHelper::isEnabled ('user', 'joomdlehooks');
		if (JPluginHelper::isEnabled ('user', 'joomdlehooks') != '1')
			$system[5]['error'] =  JText::_ ('COM_JOOMDLE_JOOMDLEHOOKS_PLUGIN_ERROR');
		else $system[5]['error'] = '';

		$comp_params = JComponentHelper::getParams( 'com_joomdle' );
		$connection = $comp_params->get( 'connection_method' );

		if ($connection == 'fgc')
		{
			/* file_get_contents call.  Test to see if allow_url_fopen PHP option is enabled */
			$system[1]['description'] = JText::_ ('COM_JOOMDLE_ALLOW_URL_FOPEN');
			$system[1]['value'] = ini_get ('allow_url_fopen');
			if ($system[1]['value'] != '1')
				$system[1]['error'] =  JText::_ ('COM_JOOMDLE_ALLOW_URL_FOPEN_ERROR');
			else $system[1]['error'] = '';
		}
		else if ($connection == 'curl')
		{
			$system[1]['description'] = JText::_ ('COM_JOOMDLE_CURL_ENABLED');
			$system[1]['value'] = function_exists('curl_version') == "Enabled";
			if (!$system[1]['value'])
				$system[1]['error'] =  JText::_ ('COM_JOOMDLE_CURL_ENABLED_ERROR');
			else $system[1]['error'] = '';
		}

		if (($system[1]['error'] != '') || ($system[2]['error'] != ''))
		{
			/* If no working connection, no need to continue */
			return $system;
		}

		// Check bare HTTP connection
        $moodle_url = $params->get( 'MOODLE_URL');
		$moodle_file_url = $moodle_url . '/auth/joomdle/connection_test.php';
		$joomla_file_url = $moodle_url . '/auth/joomdle/connection_test_joomla.php';

		// Joomla to Moodle
	//	$result = file_get_contents ($moodle_file_url);
		$result = JoomdleHelperContent::get_file ($moodle_file_url);
		$system[6]['description'] = JText::_ ('COM_JOOMDLE_JOOMDLE_JOOMLA_TO_MOODLE_CONNECTION');
		if (strncmp ($result, 'OK', 2) != 0)
		{
			$system[6]['value'] = 0;
			$system[6]['error'] =  JText::_ ('COM_JOOMDLE_JOOMLA_CANNOT_CONNECT_TO_MOODLE');
		}
		else
		{
			$system[6]['value'] = 1;
			$system[6]['error'] =  '';
		}

		// Moodle to Joomla
	//	$result = file_get_contents ($joomla_file_url);
		$result = JoomdleHelperContent::get_file ($joomla_file_url);
		$system[7]['description'] = JText::_ ('COM_JOOMDLE_JOOMDLE_MOODLE_TO_JOOMLA_CONNECTION');
		if (strncmp ($result, 'OK', 2) != 0)
		{
			$system[7]['value'] = 0;
			$system[7]['error'] =  JText::_ ('COM_JOOMDLE_MOODLE_CANNOT_CONNECT_TO_JOOMLA');
		}
		else
		{
			$system[7]['value'] = 1;
			$system[7]['error'] =  '';
		}

		/* Test Moodle Web services in joomdle plugin */
		$system[3]['description'] = JText::_ ('COM_JOOMDLE_JOOMDLE_WEB_SERVICES');
		$response = JoomdleHelperContent::call_method_debug ('system_check');
		if ($response == '')
		{
			$system[3]['value'] = 0;
			$system[3]['error'] =  JText::_ ('COM_JOOMDLE_EMPTY_RESPONSE_FROM_MOODLE');
		}
		else if ((is_array ($response)) && (xmlrpc_is_fault ($response)))
		{
			$code = $response['faultCode']; //."): ".$response['faultString'];
			switch ($code)
			{
				case '702':
					$system[3]['value'] = 0;
					$system[3]['error'] =  JText::_ ('COM_JOOMDLE_JOOMDLE_WEB_SERVICES_ERROR_702');
					break;
				case '704':
					$system[3]['value'] = 0;
					$system[3]['error'] =  JText::_ ('COM_JOOMDLE_JOOMDLE_WEB_SERVICES_ERROR_704');
					break;
				case '7021':
					$system[3]['value'] = 0;
					$system[3]['error'] =  JText::_ ('COM_JOOMDLE_JOOMDLE_WEB_SERVICES_ERROR 7021');
					break;
				case '7015':
					$system[3]['value'] = 0;
					$system[3]['error'] =  JText::_ ('COM_JOOMDLE_JOOMDLE_WEB_SERVICES_ERROR 7015');
					break;
				case '0':
					$system[3]['value'] = 0;
					if (strstr ($response['faultString'], 'joomdle_auth'))
						$system[3]['error'] =  JText::_ ('COM_JOOMDLE_JOOMDLE_AUTH_NOT_ENABLED');
					else if (strstr ($response['faultString'], 'mnet_auth'))
						$system[3]['error'] =  JText::_ ('COM_JOOMDLE_MNET_AUTH_NOT_ENABLED');
					else if (strstr ($response['faultString'], 'Access control'))
						$system[3]['error'] =  JText::_ ('COM_JOOMDLE_ACCESS_CONTROL_ERROR');
					break;
				case '620':
					$system[3]['value'] = 0;
					$system[3]['error'] =  JText::_ ('COM_JOOMDLE_JOOMDLE_WEB_SERVICES_ERROR_620');
					break;
				default:
					$system[3]['value'] = 0;
					$system[3]['error'] =  JText::_ ('COM_JOOMDLE_JOOMDLE_WEB_SERVICES_UNEXPECTED_ERROR'). ": ".$code .": ".$response['faultString'];

			}
		}
		else {
			if ($response ['joomdle_auth'] != 1)
			{
				$system[3]['value'] = 0;
				$system[3]['error'] =  JText::_ ('COM_JOOMDLE_JOOMDLE_AUTH_NOT_ENABLED');
			}
			else if ($response['joomdle_configured'] == 0)
			{
				$system[3]['value'] = 0;
				$system[3]['error'] =  JText::_ ('COM_JOOMDLE_JOOMLA_URL_NOT_CONFIGURED_IN_MOODLE_PLUGIN');
			}
			else if ($response['test_data'] != 'It works')
			{
				if (strncmp ($response['test_data'], 'XML-RPC Error (1): Access Denied', 32) == 0)
                {
                    $system[3]['value'] = 0;
					$system[3]['error'] =  JText::_ ('COM_JOOMDLE_IP_NOT_ALLOWED') .  ": " .substr ( $response['test_data'], 32 );
                }
				if (strncmp ($response['test_data'], 'XML-RPC Error (1): Invalid token', 32) == 0)
                {
                    $system[3]['value'] = 0;
					$system[3]['error'] =  JText::_ ('COM_JOOMDLE_INVALID_TOKEN') .  ": " .substr ( $response['test_data'], 33 );
                }
				else {
					$system[3]['value'] = 0;
					$system[3]['error'] =  JText::_ ('COM_JOOMDLE_JOOMLA_URL_MISCONFIGURED_IN_MOODLE_PLUGIN');
				}
			}
			else {
				$system[3]['value'] = 1;
				$system[3]['error'] = '';
			}
		}


		return $system;

	}


	static function get_course_url ($course_id)
	{
		$mainframe = JFactory::getApplication( 'site' );

		$user = JFactory::getUser ();
		$username = $user->username;

		if (!$user->id)
			$username = 'guest';

		$session                = JFactory::getSession();
        $token = md5 ($session->getId());

        $itemid = JRequest::getVar( 'Itemid' );
        $params = $mainframe->getParams();

		$comp_params = JComponentHelper::getParams( 'com_joomdle' );
        if ($comp_params->get( 'goto_course_button' ) == 'moodle')
		{
			if ($comp_params->get( 'linkstarget' ) == 'wrapper')
				$open_in_wrapper = 1;
			else
				$open_in_wrapper = 0;
			$moodle_auth_land_url = $comp_params->get( 'MOODLE_URL' ).'/auth/joomdle/land.php';

			$url = $moodle_auth_land_url."?username=$username&token=$token&mtype=course&id=$course_id&use_wrapper=$open_in_wrapper&Itemid=$itemid";
		}
		else // link to course Joomdle view
		{
			$itemid = $params->get( 'courseview_itemid');
			$url = JRoute::_("index.php?option=com_joomdle&view=course&course_id=$course_id&Itemid=$itemid");
		}

		return $url;
	}

	static function can_enrol ($course_id)
	{
		$enrol_methods = JoomdleHelperContent::call_method ('course_enrol_methods', $course_id);

		$self_ok = false;
        foreach ($enrol_methods as $method)
        {
            if ($method['enrol'] == 'self')
            {
                $self_ok = true;
                break;
            }
        }

		return $self_ok;
	}

    static function get_enrol_instance_id ($course_id, $enrol_method)
    {
        $enrol_methods = JoomdleHelperContent::call_method ('course_enrol_methods', $course_id);

        foreach ($enrol_methods as $method)
        {
            if ($method['enrol'] == $enrol_method)
            {
                break;
            }
        }

        return $method['id'];
    }


	static function in_enrol_date ($course_id)
	{
		$enrol_methods = JoomdleHelperContent::call_method ('course_enrol_methods', $course_id);

		$now = time();
		$in = true;
        foreach ($enrol_methods as $method)
        {
			if (($method['enrolstartdate']) && ($method['enrolenddate']))
			{
				$in = false;
				if (($method['enrolstartdate'] <= $now) && ($method['enrolenddate'] >= $now))
				{
					$in = true;
					break;
				}
			}
        }

		return $in;
	}

	static function get_lang ()
    {
        $client_lang = '';
        $lang_known = false;
        $lang = JRequest::getVar('lang');

        if ($lang)
        {
            //lang set via GET/POST
            $client_lang = $lang;
            $lang_known = true;

        }
 /*       else //no joomfish in 1.7
        {
            $jfcookie = JRequest::getVar('jfcookie', null ,"COOKIE");
            if (isset($jfcookie["lang"]) && $jfcookie["lang"] != "") {
                $client_lang = $jfcookie["lang"];
                $lang_known = true;
            }
        }
*/

        if ($lang_known)
            return $client_lang;
        else return false;
    }

}
