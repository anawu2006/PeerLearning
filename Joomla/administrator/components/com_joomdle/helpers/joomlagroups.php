<?php
/**
 * @version     
 * @package     Joomdle
 * @subpackage  Content
 * @copyright   Copyright (C) Antonio Duran Terres
 * @license     GNU/GPL, see LICENSE.php
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

jimport('joomla.user.helper');

//require_once (JPATH_SITE . '/libraries/joomla/database/table/usergroup.php');
require_once (JPATH_ADMINISTRATOR . '/components/com_joomdle/tables/coursegroups.php');


class JoomdleHelperJoomlagroups
{
	static function add_group ($course_id, $group_name, $type)
	{
		// dont create if exists
		$group_id = JoomdleHelperJoomlagroups::get_course_group_id ($course_id, $type);
		if ($group_id)
		{
			$db           = JFactory::getDBO();
			$query = 'SELECT id' .
				' FROM #__usergroups' .
				" WHERE id = " . $db->Quote($group_id);
			$db->setQuery($query);
			$joomla_group_id = $db->loadResult();

			if ($joomla_group_id)
				return; // course group already exists, nothing to do

			if (!$joomla_group_id)
			{
				//Group was deleted from Joomla
				// Delete entry in Joomdle table so that it can be created again
				$query = 'DELETE ' .
					' FROM #__joomdle_course_groups' .
					" WHERE group_id = " . $db->Quote($group_id);
				$db->setQuery($query);
				$db->query();
			}
		}

		$data['parent_id'] = JoomdleHelperJoomlagroups::get_parent_id ($type);
		$data['title'] = $group_name;

		$db = JFactory::getDBO ();
		$row = new JTableUsergroup ($db);
		$row->save ($data);

		$group_id = $row->id;

		// Add to joomdle xref table
		$row = new JoomdleTableCourseGroups ($db);
		$group_data['course_id'] = $course_id;
		$group_data['group_id'] = $group_id;
		$group_data['type'] = $type;
		$row->save ($group_data);
	}

	static function get_parent_id ($type)
	{
		$comp_params = JComponentHelper::getParams( 'com_joomdle' );

		switch ($type)
		{
			case 'teachers':
				$id = $comp_params->get( 'joomlagroups_teachers' );
				break;
			case 'students':
				$id = $comp_params->get( 'joomlagroups_students' );
				break;
		}

		return $id;
	}

	static function add_course_groups ($course_id, $course_name)
	{
		$group_name = $course_name . ' (' . JText::_('COM_JOOMDLE_TEACHERS') . ')';
		JoomdleHelperJoomlagroups::add_group ($course_id, $group_name, 'teachers');
		$group_name = $course_name . ' (' . JText::_('COM_JOOMDLE_STUDENTS') . ')';
		JoomdleHelperJoomlagroups::add_group ($course_id, $group_name, 'students');
	}

	static function remove_course_groups ($course_id)
	{
		$group_id = JoomdleHelperJoomlagroups::get_course_group_id ($course_id, 'teachers');
		JoomdleHelperJoomlagroups::remove_group ($group_id);
		$group_id = JoomdleHelperJoomlagroups::get_course_group_id ($course_id, 'students');
		JoomdleHelperJoomlagroups::remove_group ($group_id);
	}

	static function remove_group ($group_id)
	{
		$db = JFactory::getDBO ();
		$row = new JTableUsergroup ($db);
		$row->delete ($group_id);

		// remove from joomdle table
        $query = 'DELETE ' .
            ' FROM #__joomdle_course_groups' .
            " WHERE group_id = " . $db->Quote($group_id);
		$db->setQuery($query);
		$db->query();
	}

	static function add_group_member ($course_id, $username, $type)
	{
		$db = JFactory::getDBO ();

		$user_id = JUserHelper::getUserId ($username);
		if (!$user_id)
			return;

		// Add to general group if needed
		$parent_id = JoomdleHelperJoomlagroups::get_parent_id ($type);
        $query = 'SELECT * ' .
            ' FROM #__user_usergroup_map' .
            " WHERE group_id = " . $db->Quote($parent_id) .
			" AND user_id = " . $db->Quote($user_id);
		$db->setQuery($query);
		$map = $db->loadAssocList();

		if (!count ($map))
		{
			$data = new JObject ();
			$data->user_id = $user_id;
			$data->group_id = $parent_id;
			$db->insertObject( '#__user_usergroup_map', $data);
		}

		// Add to course group
		$group_id = JoomdleHelperJoomlagroups::get_course_group_id ($course_id, $type);
		if (!$group_id)
			return;

        $query = 'SELECT * ' .
            ' FROM #__user_usergroup_map' .
            " WHERE group_id = " . $db->Quote($group_id) .
			" AND user_id = " . $db->Quote($user_id);
		$db->setQuery($query);
		$map = $db->loadAssocList();

		if (!count ($map))
		{
			$data = new JObject ();
			$data->user_id = $user_id;
			$data->group_id = $group_id;
			$db->insertObject( '#__user_usergroup_map', $data);
		}
	}

	static function remove_group_member ($course_id, $username, $type)
	{
		$user_id = JUserHelper::getUserId ($username);
		if (!$user_id)
			return;

		$group_id = JoomdleHelperJoomlagroups::get_course_group_id ($course_id, $type);
		if (!$group_id)
			return;

		$db = JFactory::getDBO ();
        $query = 'DELETE ' .
            ' FROM #__user_usergroup_map' .
            " WHERE group_id = " . $db->Quote($group_id) .
			" AND user_id = " . $db->Quote($user_id);
		$db->setQuery($query);
		$db->query();
	}

	static function get_course_group_id ($course_id, $type)
	{
		$db           = JFactory::getDBO();
        $query = 'SELECT group_id' .
            ' FROM #__joomdle_course_groups' .
            " WHERE course_id = " . $db->Quote($course_id);
		$query .= ' AND type=' . $db->Quote($type);
		$db->setQuery($query);
		$group_id = $db->loadResult();

        return $group_id;
	}

	static function sync_group_members ($course_id)
	{
		$group_id = JoomdleHelperJoomlagroups::get_course_group_id ($course_id, 'students');

		// Fetch students
		$students = JoomdleHelperContent::call_method ('get_course_students', (int) $course_id);
		foreach ($students as $student)
		{
			JoomdleHelperJoomlagroups::add_group_member ($course_id, $student['username'], 'students');
		}

		$group_id = JoomdleHelperJoomlagroups::get_course_group_id ($course_id, 'teachers');

		// Fetch teachers
		$teachers = JoomdleHelperContent::getCourseTeachers ($course_id);
		foreach ($teachers as $teacher)
		{
			JoomdleHelperJoomlagroups::add_group_member ($course_id, $teacher['username'], 'teachers');
		}

	}
}

?>
