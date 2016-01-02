<?php
/**
 * @version             
 * @package             Joomdle
 * @copyright   Copyright (C) Antonio Duran Terres
 * @license             GNU/GPL, see LICENSE.php
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

DEFINE('JOOMDLE_APPLICATION_STATE_REQUESTED',1);
DEFINE('JOOMDLE_APPLICATION_STATE_APPROVED',2);
DEFINE('JOOMDLE_APPLICATION_STATE_REJECTED',3);

class JoomdleHelperApplications
{

	static function applicate_for_course ($username, $course_id, $motivation, $experience)
    {
		$user_id = JUserHelper::getUserId($username);
		$user = JFactory::getUser($user_id);

		$id = $user->get('id');

        $db           = JFactory::getDBO();

        $sql = "SELECT * from #__joomdle_course_applications" .
            " WHERE user_id = ". $db->Quote($id) . " and course_id = " . $db->Quote($course_id);


        $db->setQuery($sql);
        $pc = $db->loadObject();

        $a->user_id = $id;
        $a->course_id = $course_id;
        $a->motivation = $motivation;
        $a->experience = $experience;
		$date = JFactory::getDate();
		$a->application_date = $date->toSql();

        if ($pc)
        {
			// Nothing to do, already inserted
			return false;
        }
        else
        {
            $a->state = JOOMDLE_APPLICATION_STATE_REQUESTED;
            /* Insert row */
            $db->insertObject ('#__joomdle_course_applications', $a);

			return true;
        }
    }

	static function get_course_applications ($course_id, $state = '')
	{
        $db           = JFactory::getDBO();

		$sql = "select ca.*, u.name, u.email, u.username
				from #__joomdle_course_applications as ca, #__users as u 
				where u.id = ca.user_id
				and  course_id = " . $db->Quote($course_id);

		if ($state)
			$sql .= " and ca.state = ". $db->Quote($state);

        $db->setQuery($sql);
        $pc = $db->loadAssocList();

		return $pc;
	}

	static function get_user_applications ($user_id, $state = '')
	{
        $db           = JFactory::getDBO();

		$sql = "select ca.*
				from #__joomdle_course_applications as ca, #__users as u 
				where u.id = ca.user_id
				and  u.id = " . $db->Quote($user_id);

		if ($state)
			$sql .= " and ca.state = ". $db->Quote($state);

        $db->setQuery($sql);
        $pc = $db->loadAssocList();

		$applications = array ();
		$i = 0;
		if (is_array ($pc))
		{
			foreach ($pc as $a)
			{
				$course_id = $a['course_id'];
				$course_info = JoomdleHelperContent::getCourseInfo ((int) $course_id);
				$a['fullname'] = $course_info['fullname'];

				$applications[$i] = $a;
				$i++;
			}
		}

		return $applications;
	}

	static function approve_applications ($cid)
	{
		$db           = JFactory::getDBO();

        foreach ($cid as $id)
        {
			/* get application info */
			$query = "SELECT *
						FROM #__joomdle_course_applications  where id = " . $db->Quote($id);
			$db->setQuery($query);
			$app = $db->loadAssoc();

			$user_id = $app['user_id'];
			$user = JFactory::getUser($user_id);


			$username = $user->username;
			$course_id = $app['course_id'];

			$date = JFactory::getDate();
			$confirmation_date = $date->toSql();
			/* Mark as approved */
			$query = "update  #__joomdle_course_applications set state=".JOOMDLE_APPLICATION_STATE_APPROVED .", confirmation_date = '$confirmation_date' where id = " . $db->Quote($id);
			$db->setQuery($query);
			$db->query();

			/* Enrol user in course */
			JoomdleHelperContent::enrolUser($username, $course_id);
			/* Send message to user */
			JoomdleHelperApplications::send_confirmation_email($username, $user->email, $course_id, JOOMDLE_APPLICATION_STATE_APPROVED);
        }
	}

	static function reject_applications ($cid)
	{
		$db           = JFactory::getDBO();

        foreach ($cid as $id)
        {
			/* get application info */
			$query = "SELECT *
						FROM #__joomdle_course_applications  where id = " . $db->Quote($id);
			$db->setQuery($query);
			$app = $db->loadAssoc();

			$user_id = $app['user_id'];
			$user = JFactory::getUser($user_id);
			$username = $user->username;
			$course_id = $app['course_id'];

			$date = JFactory::getDate();
			$confirmation_date = $date->toSql();
			/* Mark as rejected */
			$query = "update  #__joomdle_course_applications set state=".JOOMDLE_APPLICATION_STATE_REJECTED .", confirmation_date = '$confirmation_date'  where id = " . $db->Quote($id);
			$db->setQuery($query);
			$db->query();

			/* Send message to user */
			JoomdleHelperApplications::send_confirmation_email($username, $user->email, $course_id, JOOMDLE_APPLICATION_STATE_REJECTED);
        }
	}

	static function send_confirmation_email ($username, $email, $course_id, $state)
    {

		$app = JFactory::getApplication();

        $comp_params = JComponentHelper::getParams( 'com_joomdle' );
        $linkstarget = $comp_params->get( 'linkstarget' );
        $moodle_url = $comp_params->get( 'MOODLE_URL' );

		$user_id = JUserHelper::getUserId($username);
		$user = JFactory::getUser($user_id);

        if ($linkstarget == 'wrapper')
        {
            /* XXX After and hour tryng and searching I could not find the GOOD way
               to do this, so I do this kludge and it seems to work ;) 
               */
            $url            = JURI::base();
            $pos =  strpos ($url, '/administrator/');
            if ($pos)
                $url = substr ($url, 0, $pos);
            $url            = $url.'/index.php?option=com_joomdle&view=wrapper&moodle_page_type=course&id='.$course_id;
        } else {
            $url = $moodle_url.'/course/view.php?id='.$course_id;
        }

        $course_info = JoomdleHelperContent::getCourseInfo ((int) $course_id);
        $name = $course_info['fullname'];


		/* Set language for email to the one chosen by user */
		$user_lang = $user->getParam('language','');
		$default_language = JComponentHelper::getParams('com_languages')->get('administrator');
		if ($user_lang)
		{
			$lang = new JLanguage ($user_lang);
			$lang->load ('com_joomdle', JPATH_ADMINISTRATOR, $user_lang, true);
		}
		else $lang = JLanguage::getInstance($default_language);

		// Set the e-mail parameters
		$from           = $app->getCfg('mailfrom');
		$fromname       = $app->getCfg('fromname');

		switch ($state)
		{
			case JOOMDLE_APPLICATION_STATE_APPROVED:
				$subject           = JText::sprintf('COM_JOOMDLE_APPLICATION_ACCEPTED_MESSAGE_SUBJECT', $name);
				$body           = JText::sprintf($lang->_('COM_JOOMDLE_APPLICATION_ACCEPTED_MESSAGE_BODY'), $user->name, $name);
				break;
			case JOOMDLE_APPLICATION_STATE_REJECTED:
				$subject           = JText::sprintf('COM_JOOMDLE_APPLICATION_REJECTED_MESSAGE_SUBJECT', $name);
				$body           = JText::sprintf($lang->_('COM_JOOMDLE_APPLICATION_REJECTED_MESSAGE_BODY'), $user->name, $name);
				break;

		}

		// Send the e-mail
		if (!JFactory::getMailer()->sendMail($from, $fromname, $email, $subject, $body))

		{
				$this->setError('ERROR_SENDING_CONFIRMATION_EMAIL');
				return false;
		}

		return true;
    }

	static function get_application_info ($app_id)
	{
		$db           = JFactory::getDBO();
		$query = "SELECT *
					FROM #__joomdle_course_applications  where id = " . $db->Quote($app_id);
		$db->setQuery($query);
		$app = $db->loadAssoc();

		return $app;
	}

    static function user_can_applicate ($user_id, $course_id,  &$message)
    {
        $db           = JFactory::getDBO();
        $query = "SELECT count(*)
                    FROM #__joomdle_course_applications  where user_id = " . $db->Quote($user_id);
        $query .= " and (state = 1 or state = 2)";
        $db->setQuery($query);
        $n = $db->loadResult();

        $comp_params = JComponentHelper::getParams( 'com_joomdle' );
        $applications_max = $comp_params->get( 'applications_max' );

        if ($n >= $applications_max)
            return false;
        else
        {
             $app                = JFactory::getApplication();
             $results = $app->triggerEvent('onUserCanApplicate', array($user_id, $course_id, &$message));
             if (in_array (false, $results))
                return false;
             else
                return true;
        }

    }

	static function getStateOptions()
    {
        // Build the filter options.
        $options    = array();

        $options[] = JHTML::_('select.option',  JOOMDLE_APPLICATION_STATE_APPROVED,  JText::_( 'COM_JOOMDLE_APPROVED_APPLICATIONS' ));
        $options[] = JHTML::_('select.option',  JOOMDLE_APPLICATION_STATE_REJECTED,  JText::_( 'COM_JOOMDLE_REJECTED_APPICATIONS' ));
        $options[] = JHTML::_('select.option',  JOOMDLE_APPLICATION_STATE_REQUESTED, JText::_( 'COM_JOOMDLE_NOT_PROCESSED' ) );

        return $options;
    }


}


?>
