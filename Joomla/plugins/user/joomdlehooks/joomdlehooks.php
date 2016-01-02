<?php
/**
 * @version             
 * @package             Joomdle
 * @copyright   Copyright (C) 2008 - 2015 Antonio Duran Terres
 * @license             GNU/GPL, see LICENSE.php
 */

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );
require_once(JPATH_SITE.'/components/com_joomdle/helpers/content.php');
require_once(JPATH_ADMINISTRATOR.'/components/com_joomdle/helpers/profiletypes.php');

class plgUserJoomdlehooks extends JPlugin
{
    protected $app;
    protected $db;

	function plgUserJoomdlehooks(& $subject, $config) {
                parent::__construct($subject, $config);
        }

	/* Destroys Moodle session */
	function onUserLogout($user, $options = array())
	{
		$mainframe = JFactory::getApplication('site');

		if (array_key_exists ('skip_joomdlehooks', $options))
			return true;

		if ($mainframe->isAdmin()) 
			return true;

		$comp_params = JComponentHelper::getParams( 'com_joomdle' );
		$redirectless_logout = $comp_params->get( 'redirectless_logout' );

		if (!$redirectless_logout)
		{
			// Delete "remember me" cookie if present
			$cookieName  = JUserHelper::getShortHashedUserAgent();
			$cookieValue = $this->app->input->cookie->get($cookieName);
			if ($cookieValue)
			{
				$cookieArray = explode('.', $cookieValue);

				// Filter series since we're going to use it in the query
				$filter = new JFilterInput;
				$series = $filter->clean($cookieArray[1], 'ALNUM');

				// Remove the record from the database
				$query = $this->db->getQuery(true);
				$query
					->delete('#__user_keys')
					->where($this->db->quoteName('series') . ' = ' . $this->db->quote($series));

				$this->db->setQuery($query)->execute();

				// Destroy the cookie
				$this->app->input->cookie->set(
					$cookieName,
					false,
					time() - 42000,
					$this->app->get('cookie_path', '/'),
					$this->app->get('cookie_domain')
				);
			}

			$moodle_url = $comp_params->get( 'MOODLE_URL' );
			$app = JFactory::getApplication();
			$app->redirect($moodle_url."/auth/joomdle/land_logout.php" ); 
			return;
		}

//		if (!array_key_exists ('MoodleSession', $_COOKIE))
//			return;

		$cookie_path = $comp_params->get( 'cookie_path' , "/");

		$old_session = session_id ();
		session_name ("MoodleSession");
		session_id("");
		@session_destroy();
	//	session_unregister("USER");
	//	session_unregister("SESSION");
		unset($_SESSION['USER']);
		unset($_SESSION['SESSION']);
		setcookie('MoodleSession', '',  time() - 3600, $cookie_path,'','',0);
		unset($_SESSION);

		return true;
	}

	function onUserLogin ($user, $options = array())
	{
		if ($this->params->get('login_event_to_hook', 'onUserLogin') != 'onUserLogin')
			return;

		$username = $user['username'];
		$this->do_login ($username, $options);
	}

	function onUserAfterLogin ($options = array())
	{
		if ($this->params->get('login_event_to_hook', 'onUserLogin') != 'onUserAfterLogin')
			return;

		$user = $options['user'];
		$username = $user->username;
		$this->do_login ($username, $options);
	}

	function do_login ($username, $options = array())
	{
		$mainframe = JFactory::getApplication('site');
			
		if (array_key_exists ('skip_joomdlehooks', $options))
			return;

		if ($mainframe->isAdmin()) 
			return;

        $moodle_user = JoomdleHelperContent::call_method ("user_id", $username);
        // Do nothing if user does not exist in Moodle
        if (!$moodle_user)
            return;


		$comp_params = JComponentHelper::getParams( 'com_joomdle' );

		$moodle_url = $comp_params->get( 'MOODLE_URL' );
		$redirectless_sso = $comp_params->get( 'redirectless_sso' );

		$session                = JFactory::getSession();
		$token = md5 ($session->getId());

		/* Don't log in Moodle if user is blocked */
		$user_id = JUserHelper::getUserId($username);
		$user_obj = JFactory::getUser($user_id);
		if  ($user_obj->block)
			return;

		$app =  JFactory::getApplication();


		if (JRequest::getVar ('return'))
		{
			$return = JRequest::getVar ('return');
            if (!strncmp ($return, 'B:', 2))
            {
                /* CB login module */
                $login_url = urlencode (base64_decode (substr ($return, 2)));
            }
            else
            {
                /* Normal login */
                $login_url = urlencode (base64_decode (JRequest::getVar ('return')));
            }
		}
		else if (array_key_exists ('url', $options))
			$login_url = urlencode ($options['url']);
		else
			$login_url = urlencode (JRequest::getUri ());

		$username = urlencode ($username);
		// Metodo nuevo con cURL
		if ($redirectless_sso)
			plgUserJoomdlehooks::log_into_moodle ($username, $token);
		else  // Metodo normal usando redirect
			$app->redirect($moodle_url."/auth/joomdle/land.php?username=$username&token=$token&use_wrapper=0&create_user=0&wantsurl=$login_url" ); 
	}

	/* Logs the user into Moodle using cURL to set the cookies */
	function log_into_moodle ($username, $token)
	{
		$mainframe = JFactory::getApplication('site');

		$comp_params = JComponentHelper::getParams( 'com_joomdle' );

		$moodle_url = $comp_params->get( 'MOODLE_URL' );
		$cookie_path = $comp_params->get( 'cookie_path' , "/");

		$username = str_replace (' ', '%20', $username);
		$login_url = $moodle_url; // '';
		$file = $moodle_url. "/auth/joomdle/land.php?username=$username&token=$token&use_wrapper=0&create_user=1&wantsurl=$login_url";

		$ch = curl_init();
		// set url
		curl_setopt($ch, CURLOPT_URL, $file);

		$config = JFactory::getConfig();
		$temppath = $config->get('tmp_path');
		$file = $temppath . "/" . JUserHelper::genRandomPassword() . ".txt";

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
				if (($line == '\n') || ($line[0] == '#'))
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

	/* Creates Moodle user */
	function onUserAfterSave ($user, $isnew, $success, $msg)
	{
		return plgUserJoomdlehooks::sync_user ($user, $isnew, $success, $msg);
	}

	// Made static so we can call it from other places, like CB user plugin
	public static function sync_user ($user, $isnew, $success, $msg)
	{
		$mainframe = JFactory::getApplication('site');

		$last_visit = $user['lastvisitDate'];

		if ($last_visit == 0)
			$isnew = 1;
		else $isnew = 0;

		$comp_params = JComponentHelper::getParams( 'com_joomdle' );

		/* Don't create user if not configured to do so */
		if (($isnew) && (!$comp_params->get( 'auto_create_users' )))
			return;


		$username = $user['username'];
		$moodle_user = JoomdleHelperContent::call_method ("user_id", $username);

		/* If user don't exist, and it is configured to not autocreate return  */
		if ((!$moodle_user) && (!$comp_params->get( 'auto_create_users' )))
				return;

		$is_child = false;
		if (is_string ($user['params'])) // check added because CB passes different info in params
			if (strstr ($user['params'], '_parent_id'))
				$is_child = true;

		
		/* Check Profile Types */
		$use_profiletypes = $comp_params->get( 'use_profiletypes' );
		if ((!$moodle_user) && ($use_profiletypes) && (!$is_child))
		{
			 /* Only create Moodle user if Profile Type in selected ones */

			if ($use_profiletypes == 'xipt')
			{
				$db = JFactory::getDBO();
				$query = "select id from #__community_fields where fieldcode = 'XIPT_PROFILETYPE'";
				$db->setQuery($query);
				$field_id = $db->loadResult();
				$field = 'field'.$field_id;

				/* If editing anyhting else in the profile, not intereseting */
				if (!array_key_exists ($field, $_POST))
					return;

				$profile_type = $_POST[$field];
			}
			else if ($use_profiletypes == 'jomsocial')
			{
				$profile_type =  JRequest::getVar( 'profileType' );
			}


			$profile_type_ids = JoomdleHelperProfiletypes::get_profiletypes_to_create ();
			$profile_ok = in_array ($profile_type, $profile_type_ids);
			if ((!$profile_ok) &&  (!$moodle_user) )
				return;

		}

		/* If we reach here, user HAS to be created */
		$reply = JoomdleHelperContent::call_method ("create_joomdle_user", $username);
	}

	function onUserAfterDelete ($user, $options = array())
	{
		$comp_params = JComponentHelper::getParams( 'com_joomdle' );

		/* Don't delete user if not configured to do so */
		if (!$comp_params->get( 'auto_delete_users' ))
			return;

		$otherlanguage = JFactory::getLanguage();
		$otherlanguage->load( 'com_joomdle', JPATH_SITE );

		plgUserJoomdlehooks::delete_user ($user);
	}

	// Made static so we can call it from other places, like CB user plugin
	public static function delete_user ($user)
	{
		$mainframe = JFactory::getApplication('site');

		$username = $user['username'];

		$reply = JoomdleHelperContent::call_method ("delete_user", $username);

		if ($reply)
			 $mainframe->enqueueMessage(JText::_('COM_JOOMDLE_USER_DELETED_FROM_MOODLE'));
	}
}

?>
