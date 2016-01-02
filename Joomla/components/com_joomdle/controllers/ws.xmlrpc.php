<?php
// no direct access
defined('_JEXEC') or die;

jimport('joomla.application.component.controller');

require_once(JPATH_ADMINISTRATOR.'/components/com_joomdle/helpers/mappings.php');
require_once(JPATH_ADMINISTRATOR.'/components/com_joomdle/helpers/groups.php');
require_once(JPATH_ADMINISTRATOR.'/components/com_joomdle/helpers/users.php');
require_once(JPATH_ADMINISTRATOR.'/components/com_joomdle/helpers/shop.php');
require_once(JPATH_ADMINISTRATOR.'/components/com_joomdle/helpers/points.php');
require_once(JPATH_ADMINISTRATOR.'/components/com_joomdle/helpers/mailinglist.php');
require_once(JPATH_ADMINISTRATOR.'/components/com_joomdle/helpers/joomlagroups.php');
require_once(JPATH_ADMINISTRATOR.'/components/com_joomdle/helpers/forum.php');
require_once(JPATH_ADMINISTRATOR.'/components/com_joomdle/helpers/activities.php');


    function getUserInfo($method, $params)
    {
		$username = $params[0];
		if (array_key_exists (1, $params))
			$app = $params[1];
		else $app = '';
        $user_info = JoomdleHelperMappings::get_user_info ($params[0], $app);
        return $user_info;
    }

	function test ()
	{
		return "It works";
	}

    /* Web service used to log in from Moodle */
    function login ($method, $params)
    {
		$username = $params[0];
		$password = $params[1];

        $mainframe = JFactory::getApplication('site');

		$user_id = JUserHelper::getUserId($username);
        $user = JFactory::getUser($user_id);

		if (!$user)
			return false;

		if ($user->block)
			return false;

        $options = array ( 'skip_joomdlehooks' => '1', 'silent' => 1);
        $credentials = array ( 'username' => $username, 'password' => $password);
        if ($mainframe->login( $credentials, $options ))
            return true;
        return false;
	}

	function joomdle_getDefaultItemid ()
    {
        $comp_params = JComponentHelper::getParams( 'com_joomdle' );
        $default_itemid = $comp_params->get( 'default_itemid' );
        return $default_itemid;
    }


  function confirmJoomlaSession($method, $params)
    {
		$username = $params[0];
		$token = $params[1];

        $db = JFactory::getDBO();
        $query = 'SELECT session_id' .
                ' FROM #__session' .
                " WHERE username = ". $db->Quote($username). " and  md5(session_id) = ". $db->Quote($token);
        $db->setQuery( $query );
        $session = $db->loadResult();


		if ($session)
			return true;
		else
			return false;
    }

	function logout($method, $params)
    {
		$username = $params[0];
		$ua_string = $params[1];

        $mainframe = JFactory::getApplication('site');

        $id = JUserHelper::getUserId($username);

        $error = $mainframe->logout($id, array ( 'clientid' => 0, 'skip_joomdlehooks' => 1));

		// Return "remember me" cookie name so it  can be deleted
		$ua = new JApplicationWebClient ($ua_string);
		$uaString = $ua->userAgent;
        $browserVersion = $ua->browserVersion;
        $uaShort = str_replace($browserVersion, 'abcd', $uaString);

        $r = md5(JUri::base() . $uaShort);

        return $r;
    }

	function deleteUserKey ($method, $params)
	{
		$series = $params[0];

		// Delete the key
        $db = JFactory::getDBO();
		$query = $db->getQuery(true);
		$query
			->delete('#__user_keys')
			->where($db->quoteName('series') . ' = ' . $db->quote($series));

		$db->setQuery($query)->execute();
	}

	function createUser ($method, $params)
    {
		$user_info = $params[0];
        return JoomdleHelperUsers::create_joomla_user ($user_info);
    }

    function activateJoomlaUser ($method, $params)
    {
		$username = $params[0];

        $username = utf8_decode ($username);

        return JoomdleHelperUsers::activate_joomla_user ($username);
    }

    function updateUser ($method, $params)
    {
		$user_info = $params[0];
        return JoomdleHelperMappings::save_user_info ($user_info, false);
    }

    function changePassword ($method, $params)
    {
		$username = $params[0];
		$password = $params[1];

        $username = utf8_decode ($username);

		$user_id = JUserHelper::getUserId($username);
        $user = JFactory::getUser($user_id);

		/*
        $salt           = JUserHelper::genRandomPassword(32);
		$crypt          = JUserHelper::getCryptedPassword($password, $salt);
        $password_crypt       = $crypt.':'.$salt;

        $user->password = $password_crypt;
		*/
        $user->password = $password;
        @$user->save();

        return true;
    }

	function deleteUser ($method, $params)
    {
		$username = $params[0];
        $username = utf8_decode ($username);

        $user_id = JUserHelper::getUserId($username);
        $user = JFactory::getUser($user_id);
        $user->delete();
    }


	function addActivityCourse ($method, $params)
    {
		$id = $params[0];
		$name = $params[1];
		$desc = $params[2];
		$cat_id = $params[3];
		$cat_name = $params[4];

		return JoomdleHelperActivities::add_activity_course ($id, $name, $desc, $cat_id, $cat_name);
    }

	function addActivityCourseEnrolment ($method, $params)
    {
		$username = $params[0];
		$course_id = $params[1];
		$course_name = $params[2];
		$cat_id = $params[3];
		$cat_name = $params[4];

		return JoomdleHelperActivities::add_activity_course_enrolment ($username, $course_id, $course_name, $cat_id, $cat_name);
    }


/*
	function getJSGroupId ($method, $params)
    {
		$name = $params[0];
        return JoomdleHelperGroups::get_js_group_by_name ($name);
    }

    function getJSGroupImageLink ($method, $params)
    {
		$name = $params[0];
        return JoomdleHelperGroups::get_js_group_image_link ($name);
    }

    function addJSGroup ($method, $params)
    {
		$name = $params[0];
		$description = $params[1];
		$course_id = $params[2];
		$website = $params[3];

   //     $name = utf8_decode ($name);
   //     $description = utf8_decode ($description);

        return JoomdleHelperGroups::addJSGroup ($name, $description, $course_id, $website);
    }
*/

    function addSocialGroup ($method, $params)
    {
		$name = $params[0];
		$description = $params[1];
		$course_id = $params[2];

        return JoomdleHelperSocialgroups::add_group ($name, $description, $course_id);
    }

    function updateSocialGroup ($method, $params)
    {
		$name = $params[0];
		$description = $params[1];
		$course_id = $params[2];

        return JoomdleHelperSocialgroups::update_group ($name, $description, $course_id);
    }

    function deleteSocialGroup ($method, $params)
    {
		$course_id = $params[0];

        return JoomdleHelperSocialgroups::delete_group ($course_id);
    }

	function addSocialGroupMember ($method, $params)
    {
		$username = $params[0];
		$permissions = $params[1];
		$course_id = $params[2];

        $username = utf8_decode ($username);

        return JoomdleHelperSocialGroups::add_group_member ($username, $permissions, $course_id);
    }

    function removeSocialGroupMember ($method, $params)
    {
		$username = $params[0];
		$course_id = $params[1];

//        $username = utf8_decode ($username);

        return JoomdleHelperSocialGroups::remove_group_member ($username, $course_id);
    }

/*
    function updateJSGroup ($method, $params)
    {
		$name = $params[0];
		$description = $params[1];
		$course_id = $params[2];
		$website = $params[3];

 //       $name = utf8_decode ($name);
 //       $description = utf8_decode ($description);

        return JoomdleHelperGroups::updateJSGroup ($name, $description, $course_id, $website);
    }

    function removeJSGroup ($method, $params)
    {
		$name = $params[0];
        return JoomdleHelperGroups::removeJSGroup ($name);
    }


   function addJSGroupMember ($method, $params)
    {
		$group_name = $params[0];
		$username = $params[1];
		$permissions = $params[2];
		$course_id = $params[3];

        $username = utf8_decode ($username);
        $group_name = utf8_decode ($group_name);

        return JoomdleHelperGroups::addJSGroupMember ($group_name, $username, $permissions, $course_id);
    }

    function removeJSGroupMember ($method, $params)
    {
		$group_name = $params[0];
		$username = $params[1];

//        $username = utf8_decode ($username);

        return JoomdleHelperGroups::removeJSGroupMember ($group_name, $username);
    }
*/

    function addPoints ($method, $params)
    {
		$action = $params[0];
		$username = $params[1];
		$courseid = $params[2];
		$course_name = $params[3];

        $username = utf8_decode ($username);
        $course_name = utf8_decode ($course_name);

        return JoomdleHelperPoints::addPoints ($action, $username, $courseid, $course_name);
    }

	function addActivityQuizAttempt ($method, $params)
    {
		$username = $params[0];
		$course_id = $params[1];
		$course_name = $params[2];
		$quiz_name = $params[3];

		return JoomdleHelperActivities::add_activity_quiz_attempt ($username, $course_id, $course_name, $quiz_name);
    }


    function addMailingSub ($action, $params)
    {
		$username = $params[0];
		$course_id = $params[1];
		$type = $params[2];

        $username = utf8_decode ($username);

		return JoomdleHelperMailinglist::add_list_member ($username, $course_id, $type);
    }

    function removeMailingSub ($action, $params)
    {
		$username = $params[0];
		$course_id = $params[1];
		$type = $params[2];

        $username = utf8_decode ($username);

		return JoomdleHelperMailinglist::remove_list_member ($username, $course_id, $type);
    }

    function addUserGroups ($action, $params)
    {
		$course_id = $params[0];
		$course_name = $params[1];

    //    $course_name = utf8_decode ($course_name);

		return JoomdleHelperJoomlagroups::add_course_groups ($course_id, $course_name);
    }

    function removeUserGroups ($action, $params)
    {
		$course_id = $params[0];

		return JoomdleHelperJoomlagroups::remove_course_groups ($course_id);
    }

    function addGroupMember ($action, $params)
    {
		$course_id = $params[0];
		$username = $params[1];
		$type = $params[2];

        $username = utf8_decode ($username);

		return JoomdleHelperJoomlagroups::add_group_member ($course_id, $username, $type);
    }

    function removeGroupMember ($action, $params)
    {
		$course_id = $params[0];
		$username = $params[1];
		$type = $params[2];

        $username = utf8_decode ($username);

		return JoomdleHelperJoomlagroups::remove_group_member ($course_id, $username, $type);
    }

    function addForum ($action, $params)
    {
		$course_id = $params[0];
		$forum_id = $params[1];
		$forum_name = $params[2];

        $forum_name = utf8_decode ($forum_name);

		return JoomdleHelperForum::add_forum ($course_id, $forum_id, $forum_name);
    }

    function updateForum ($action, $params)
    {
		$course_id = $params[0];
		$forum_id = $params[1];
		$forum_name = $params[2];

        $forum_name = utf8_decode ($forum_name);

		return JoomdleHelperForum::update_forum ($course_id, $forum_id, $forum_name);
    }

    function removeForum ($action, $params)
    {
		$course_id = $params[0];
		$forum_id = $params[1];

		return JoomdleHelperForum::remove_forum ($course_id, $forum_id);
    }

    function addForumsModerator ($action, $params)
    {
		$course_id = $params[0];
		$username = $params[1];

        $username = utf8_decode ($username);

		return JoomdleHelperForum::add_forums_moderator ($course_id, $username);
    }

    function removeForumsModerator ($action, $params)
    {
		$course_id = $params[0];
		$username = $params[1];

        $username = utf8_decode ($username);

		return JoomdleHelperForum::remove_forums_moderator ($course_id, $username);
    }

    function removeCourseForums ($action, $params)
    {
		$course_id = $params[0];

		return JoomdleHelperForum::remove_course_forums ($course_id);
    }

    function getSellUrl ($action, $params)
    {
		$course_id = $params[0];

        return JoomdleHelperShop::get_sell_url ($course_id);
    }

	function addActivityCourseCompleted ($method, $params)
    {
		$username = $params[0];
		$course_id = $params[1];
		$course_name = $params[2];

		return JoomdleHelperActivities::add_activity_course_completed ($username, $course_id, $course_name);
    }

class JoomdleControllerWs extends JControllerLegacy
{
    function check_token ()
    {
        $token = JRequest::getVar ('token');
        $comp_params = JComponentHelper::getParams( 'com_joomdle' );

        $joomla_token = $comp_params->get( 'joomla_auth_token' );

        return  ($token == $joomla_token);
    }

	public function server ()
    {
		if (!$this->check_token ())
		{
			print_r (xmlrpc_encode ( "XML-RPC Error (1): Invalid token:" . JRequest::getVar ('token')));
			return;
		}

	$document = JFactory::getDocument();
	$document->setMimeEncoding('text/xml') ;

	$xmlrpc_server = xmlrpc_server_create();

	xmlrpc_server_register_method($xmlrpc_server, "joomdle.getUserInfo", "getUserInfo");
	xmlrpc_server_register_method($xmlrpc_server, "joomdle.test", "test");
	xmlrpc_server_register_method($xmlrpc_server, "joomdle.login", "login");
	xmlrpc_server_register_method($xmlrpc_server, "joomdle.confirmJoomlaSession", "confirmJoomlaSession");
	xmlrpc_server_register_method($xmlrpc_server, "joomdle.getDefaultItemid", "joomdle_getDefaultItemid"); //Name changed because of conflict
	xmlrpc_server_register_method($xmlrpc_server, "joomdle.logout", "logout");
	xmlrpc_server_register_method($xmlrpc_server, "joomdle.createUser", "createUser");
	xmlrpc_server_register_method($xmlrpc_server, "joomdle.activateUser", "activateJoomlaUser");
	xmlrpc_server_register_method($xmlrpc_server, "joomdle.updateUser", "updateUser");
	xmlrpc_server_register_method($xmlrpc_server, "joomdle.changePassword", "changePassword");
	xmlrpc_server_register_method($xmlrpc_server, "joomdle.deleteUser", "deleteUser");
	xmlrpc_server_register_method($xmlrpc_server, "joomdle.addActivityCourse", "addActivityCourse");
	xmlrpc_server_register_method($xmlrpc_server, "joomdle.addActivityCourseEnrolment", "addActivityCourseEnrolment");


	xmlrpc_server_register_method($xmlrpc_server, "joomdle.addSocialGroup", "addSocialGroup");
	xmlrpc_server_register_method($xmlrpc_server, "joomdle.updateSocialGroup", "updateSocialGroup");
	xmlrpc_server_register_method($xmlrpc_server, "joomdle.deleteSocialGroup", "deleteSocialGroup");
	xmlrpc_server_register_method($xmlrpc_server, "joomdle.addSocialGroupMember", "addSocialGroupMember");
	xmlrpc_server_register_method($xmlrpc_server, "joomdle.removeSocialGroupMember", "removeSocialGroupMember");

	xmlrpc_server_register_method($xmlrpc_server, "joomdle.addPoints", "addPoints");
	xmlrpc_server_register_method($xmlrpc_server, "joomdle.addActivityQuizAttempt", "addActivityQuizAttempt");
	xmlrpc_server_register_method($xmlrpc_server, "joomdle.addMailingSub", "addMailingSub");
	xmlrpc_server_register_method($xmlrpc_server, "joomdle.removeMailingSub", "removeMailingSub");

	xmlrpc_server_register_method($xmlrpc_server, "joomdle.addUserGroups", "addUserGroups");
	xmlrpc_server_register_method($xmlrpc_server, "joomdle.removeUserGroups", "removeUserGroups");
	xmlrpc_server_register_method($xmlrpc_server, "joomdle.addGroupMember", "addGroupMember");
	xmlrpc_server_register_method($xmlrpc_server, "joomdle.removeGroupMember", "removeGroupMember");

	xmlrpc_server_register_method($xmlrpc_server, "joomdle.addForum", "addForum");
	xmlrpc_server_register_method($xmlrpc_server, "joomdle.updateForum", "updateForum");
	xmlrpc_server_register_method($xmlrpc_server, "joomdle.removeForum", "removeForum");
	xmlrpc_server_register_method($xmlrpc_server, "joomdle.addForumsModerator", "addForumsModerator");
	xmlrpc_server_register_method($xmlrpc_server, "joomdle.removeForumsModerator", "removeForumsModerator");
	xmlrpc_server_register_method($xmlrpc_server, "joomdle.removeCourseForums", "removeCourseForums");
	xmlrpc_server_register_method($xmlrpc_server, "joomdle.getSellUrl", "getSellUrl");
	xmlrpc_server_register_method($xmlrpc_server, "joomdle.addActivityCourseCompleted", "addActivityCourseCompleted");

	xmlrpc_server_register_method($xmlrpc_server, "joomdle.deleteUserKey", "deleteUserKey");

	//$request_xml = $HTTP_RAW_POST_DATA;
	$request_xml = @file_get_contents('php://input');

	$response = xmlrpc_server_call_method($xmlrpc_server, $request_xml, '');
	print_r ( $response );
	//echo xmlrpc_encode ($response);

//	$xmlrpc_server->service($response);

	exit ();

    }


}

?>
