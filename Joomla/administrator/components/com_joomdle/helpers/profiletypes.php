<?php
/**
 * @version		
 * @package		Joomdle
 * @subpackage	Content
 * @copyright	Copyright (C) Antonio Duran Terres
 * @license		GNU/GPL, see LICENSE.php
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

jimport('joomla.user.helper');
require_once(JPATH_ADMINISTRATOR.'/components/com_joomdle/helpers/content.php');

class JoomdleHelperProfiletypes
{

	static function getProfiletypes ($filter_type, $limitstart, $limit, $filter_order, $filter_order_Dir, $search)
	{
		$comp_params = JComponentHelper::getParams( 'com_joomdle' );
		$custom_profiles = $comp_params->get( 'use_profiletypes');

		$profiles = array ();
		JPluginHelper::importPlugin( 'joomdleprofiletypes' );
		$dispatcher = JDispatcher::getInstance();
		$result = $dispatcher->trigger('OnGetProfiletypes', array($filter_type, $limitstart, $limit, $filter_order, $filter_order_Dir, $search));
		$profiles = array_shift ($result);

		return $profiles;
	}

	/* Returns an array of profiles_id to bre created in moodle  */
	static function get_profiletypes_to_create ()
	{
		$db           = JFactory::getDBO();
		$query = "select profiletype_id from #__joomdle_profiletypes where create_on_moodle = 1";

		$db->setQuery($query);
		$profiles = $db->loadObjectList();

		$ids = array ();
		foreach ($profiles as $p)
			$ids[] = $p->profiletype_id;

		return $ids;
	}



	/* Checks if a types is to be created on moodle  */
	static function create_this_type ($id)
	{
		$db           = JFactory::getDBO();
		$query = "select create_on_moodle from #__joomdle_profiletypes where profiletype_id = ". $db->Quote($id);

		$db->setQuery($query);
		$create = $db->loadObject();

		if (!$create)
			return 0;

		return $create->create_on_moodle;
	}

	/* Sets a profile type to be created in moodle  */
	static function create_on_moodle ($ids)
	{
                $db           = JFactory::getDBO();

		foreach ($ids as $id)
		{
			$query = "select * from #__joomdle_profiletypes where profiletype_id = " . $db->Quote($id);

			$db->setQuery($query);
			$exists = $db->loadObject();

			if (!$exists)
			{
				//create
				$query = "insert into  #__joomdle_profiletypes (profiletype_id, create_on_moodle) VALUES ('$id', '1')";
				$db->setQuery($query);
				if (!$db->query()) {
					return JError::raiseWarning( 500, $db->getError() );
				}
			}
			else
			{
				//update
				$query = "update  #__joomdle_profiletypes set create_on_moodle=1 where profiletype_id =$id";
				$db->setQuery($query);
				if (!$db->query()) {
					return JError::raiseWarning( 500, $db->getError() );
				}
			}
		}

	}

	/* Sets a profile type NOT to be created in moodle  */
	static function dont_create_on_moodle ($ids)
	{
		$db           = JFactory::getDBO();

		foreach ($ids as $id)
		{
			$query = "select * from #__joomdle_profiletypes where profiletype_id = " . $db->Quote($id);

			$db->setQuery($query);
			$exists = $db->loadObject();

			if (!$exists)
			{
				// do nothing
				continue;
			}
			else
			{
				//update
				$query = "update  #__joomdle_profiletypes set create_on_moodle=0 where profiletype_id = " . $db->Quote($id);
				$db->setQuery($query);
				if (!$db->query()) {
					return JError::raiseWarning( 500, $db->getError() );
				}
			}
		}

	}

	static function getStateOptions()
    {
        // Build the filter options.
        $options    = array();

		$options[] = JHTML::_('select.option',  '1',  JText::_('COM_JOOMDLE_PROFILES_TO_CREATE'));
		$options[] = JHTML::_('select.option',  '2',  JText::_('COM_JOOMDLE_PROFILES_NOT_TO_CREATE'));

        return $options;
    }

	static function get_profiletype_data ($id)
    {
		$db           = JFactory::getDBO();

		JPluginHelper::importPlugin( 'joomdleprofiletypes' );
		$dispatcher = JDispatcher::getInstance();
		$result = $dispatcher->trigger('OnGetProfiletypeData', array($id));
		$profile = array_shift ($result);

        $query = "select * from #__joomdle_profiletypes where profiletype_id = " . $db->Quote($id);
        $db->setQuery($query);
        $joomdle_profile = $db->loadObject();

        if ($joomdle_profile)
        {
            $profile->create_on_moodle = $joomdle_profile->create_on_moodle;
            $profile->moodle_role = $joomdle_profile->moodle_role;
        }
        else
        {
            $profile->create_on_moodle = 0;
            $profile->moodle_role = 0;
        }

        return $profile;
    }

    static function get_user_profile_role ($username)
    {
		$db           = JFactory::getDBO();

		$user_id = JUserHelper::getUserId($username);
        $user = JFactory::getUser ($user_id);


		JPluginHelper::importPlugin( 'joomdleprofiletypes' );
		$dispatcher = JDispatcher::getInstance();
		$result = $dispatcher->trigger('OnGetUserProfileId', array($user_id));
		$profile = array_shift ($result);

        $query = "select moodle_role from #__joomdle_profiletypes where profiletype_id = " . $db->Quote($profile);
        $db->setQuery($query);
        $role = $db->loadResult();

        return $role;
    }

	static function save_profiletype ($data)
    {
        $db           = JFactory::getDBO();

        $id = $data->id;
        $query = "select * from #__joomdle_profiletypes where profiletype_id = " . $db->Quote($id);

        $db->setQuery($query);
        $exists = $db->loadObject();

        if (!$exists)
        {
            //create
            $query = "insert into  #__joomdle_profiletypes (profiletype_id, create_on_moodle, moodle_role) VALUES ('$id', '$data->create_on_moodle', '$data->moodle_role')";
            $db->setQuery($query);
            if (!$db->query()) {
                return JError::raiseWarning( 500, $db->getError() );
            }
        }
        else
        {
            //update
            $query = "update  #__joomdle_profiletypes set create_on_moodle='$data->create_on_moodle', moodle_role='$data->moodle_role' where profiletype_id =$id";
            $db->setQuery($query);
            if (!$db->query()) {
                return JError::raiseWarning( 500, $db->getError() );
            }
        }
    }

	static function add_user_to_profile ($user_id, $profile_id)
    {
		JPluginHelper::importPlugin( 'joomdleprofiletypes' );
		$dispatcher = JDispatcher::getInstance();
		$result = $dispatcher->trigger('OnAddUserToProfile', array($user_id, $profile_id));
		$profile = array_shift ($result);
    }


}
