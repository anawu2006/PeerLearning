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
require_once(JPATH_ADMINISTRATOR.'/'.'components'.'/'.'com_joomdle'.'/'.'helpers'.'/'.'content.php');
require_once(JPATH_ADMINISTRATOR.'/'.'components'.'/'.'com_joomdle'.'/'.'helpers'.'/'.'system.php');
require_once(JPATH_ADMINISTRATOR.'/'.'components'.'/'.'com_joomdle'.'/'.'helpers'.'/'.'acymailing.php');

class JoomdleHelperMailinglist
{

	static function getListCourses ()
	{
		$cursos = JoomdleHelperContent::getCourseList (0);

		$c = array ();
		$i = 0;
        if (!is_array ($cursos))
            return $c;

		foreach ($cursos as $curso)
		{
			$c[$i] = new JObject ();
			$c[$i]->id = $curso['remoteid'];
			$c[$i]->fullname = $curso['fullname'];
            $c[$i]->published_students = JoomdleHelperMailinglist::course_list_exists ($curso['remoteid'], 'course_students');
            $c[$i]->published_teachers = JoomdleHelperMailinglist::course_list_exists ($curso['remoteid'], 'course_teachers');
			$c[$i]->published_parents = JoomdleHelperMailinglist::course_list_exists ($curso['remoteid'], 'course_parents');
			$i++;
		}

		return $c;
	}

	static function get_course_list_id ($course_id, $type)
	{
		$db           = JFactory::getDBO();

        $query = 'SELECT list_id' .
                ' FROM #__joomdle_mailinglists' .
                ' WHERE course_id='.$db->Quote( $course_id ) .
				' AND type='.$db->Quote( $type );
        $db->setQuery( $query );
        $id = $db->loadResult();

        return $id;
	}

	static function course_list_exists ($course_id, $type)
	{
		$id = JoomdleHelperMailinglist::get_course_list_id ($course_id, $type);

		if ($id)
			return 1;
		else
			return 0;
	}

	static function get_general_list_id ($type)
	{
		$db           = JFactory::getDBO();

		$course_id = 0;
        $query = 'SELECT list_id' .
                ' FROM #__joomdle_mailinglists' .
                ' WHERE course_id='.$db->Quote( $course_id ) .
				' AND type='.$db->Quote( $type );
        $db->setQuery( $query );
        $id = $db->loadResult();

        return $id;
	}

	static function general_list_exists ($type)
	{
		$id = JoomdleHelperMailinglist::get_general_list_id ($type);

		if ($id)
			return 1;
		else
			return 0;
	}

	static function get_type_str ($type)
	{
		switch ($type)
		{
			case 'course_students':
				$str = JText::_ ('COM_JOOMDLE_STUDENTS');
				break;
			case 'course_teachers':
				$str = JText::_ ('COM_JOOMDLE_TEACHERS');
				break;
			case 'course_parents':
				$str = JText::_ ('COM_JOOMDLE_PARENTS');
				break;
		}

		$str = ' (' . $str . ')';

		return $str;
	}

	static function save_mailing_lists ($cid)
	{
		foreach ($cid as $id)
		{
			JoomdleHelperMailinglist::save_course_mailing_list ($id);
		}
	}

	static function save_course_mailing_list ($course_id, $type = 'course_students')
	{
		$db           = JFactory::getDBO();
		//XXX checkar q no existe ya

		$comp_params = JComponentHelper::getParams( 'com_joomdle' );
        $ml = $comp_params->get( 'mailing_list_integration' );

		$type_str = JoomdleHelperMailinglist::get_type_str ($type);
		// Add to mailing list component
		$course_info = JoomdleHelperContent::getCourseInfo ($course_id);

		switch ($ml)
		{
			case 'acymailing':
				$list_id = JoomdleHelperAcymailing::save_list ($course_info['fullname'] . $type_str, $course_info['summary']);
				break;
			default:
				// No component selected, do nothing
				return;
		}

		// Add to joomdle table
		$mlist = new JObject ();
		$mlist->course_id = $course_id;
		$mlist->list_id = $list_id;
		$mlist->type = $type;

		$status = $db->insertObject('#__joomdle_mailinglists',$mlist);


		// Add all course members to list
		 JoomdleHelperMailinglist::add_list_members ($course_id, $type);

	}

	static function save_general_mailing_list ($type = 'course_students')
	{
		$db           = JFactory::getDBO();
		//XXX checkar q no existe ya

		$comp_params = JComponentHelper::getParams( 'com_joomdle' );
        $ml = $comp_params->get( 'mailing_list_integration' );

		$type_str = JoomdleHelperMailinglist::get_type_str ($type);
		switch ($ml)
		{
			case 'acymailing':
				$list_id = JoomdleHelperAcymailing::save_list (JText::_('COM_JOOMDLE_GENERAL') . $type_str, JText::_('COM_JOOMDLE_LIST_FOR_ALL') . " " . $type_str);
				break;
			default:
				// No component selected, do nothing
				return;
		}

		// Add to joomdle table
		$mlist = new JObject ();
		$mlist->course_id = 0;
		$mlist->list_id = $list_id;
		$mlist->type = $type;

		$status = $db->insertObject('#__joomdle_mailinglists',$mlist);


		// Add all type members to list
		 JoomdleHelperMailinglist::add_general_list_members ($type);
	}

	static function add_sub ($list_id, $user_id)
	{
		$comp_params = JComponentHelper::getParams( 'com_joomdle' );
        $ml = $comp_params->get( 'mailing_list_integration' );

		switch ($ml)
		{
			case 'acymailing':
				JoomdleHelperAcymailing::add_sub ($list_id, $user_id);
				break;
			default:
				// No component selected, do nothing
				break;
		}
	}

	static function remove_sub ($list_id, $user_id)
	{
		$comp_params = JComponentHelper::getParams( 'com_joomdle' );
        $ml = $comp_params->get( 'mailing_list_integration' );

		switch ($ml)
		{
			case 'acymailing':
				JoomdleHelperAcymailing::remove_sub ($list_id, $user_id);
				break;
			default:
				// No component selected, do nothing
				break;
		}
	}

	static function add_list_member ($username, $course_id, $type)
	{
		$list_id = JoomdleHelperMailinglist::get_course_list_id ($course_id, $type);
		$user_id = JUserHelper::getUserId($username);

		if ($list_id)
			JoomdleHelperMailinglist::add_sub ($list_id, $user_id);
		// Add to general list if necessary
		$list_id = JoomdleHelperMailinglist::get_general_list_id ($type);
		if ($list_id)
			JoomdleHelperMailinglist::add_sub ($list_id, $user_id);
	}

	static function remove_list_member ($username, $course_id, $type)
	{
		$list_id = JoomdleHelperMailinglist::get_course_list_id ($course_id, $type);
		$user_id = JUserHelper::getUserId($username);


		// Remove from general list if necessary
		$remove = false;
		$glist_id = JoomdleHelperMailinglist::get_general_list_id ($type);
		if ($glist_id)
		{
			//Only remove if user has no more course enrolments of this type
			switch ($type)
			{
				case 'course_students':
					$my_courses = JoomdleHelperContent::getMyCourses ($username);
					if (count ($my_courses) == 0)
						$remove = true;
					break;
				case 'course_teachers':
					$my_courses = JoomdleHelperContent::call_method ('teacher_courses', $username);
					if (count ($my_courses) == 0)
						$remove = true;
					break;
			}

		}

		JoomdleHelperMailinglist::remove_sub ($list_id, $user_id);
		if ($remove)
			JoomdleHelperMailinglist::remove_sub ($glist_id, $user_id);
	}

	// Adds all course members
	static function add_list_members ($course_id, $type)
	{
		$list_id = JoomdleHelperMailinglist::get_course_list_id ($course_id, $type);

		switch ($type)
		{
			case 'course_students':
				$students = JoomdleHelperContent::call_method ('get_course_students', $course_id);
				foreach ($students as $student)
				{
					$user_id = JUserHelper::getUserId($student['username']);
					JoomdleHelperMailinglist::add_sub ($list_id, $user_id);
				}
				break;
			case 'course_teachers':
				$teachers = JoomdleHelperContent::call_method ('get_course_editing_teachers', $course_id);
				foreach ($teachers as $teacher)
				{
					$user_id = JUserHelper::getUserId($teacher['username']);
					JoomdleHelperMailinglist::add_sub ($list_id, $user_id);
				}
				break;
			case 'course_parents':
                $parents = JoomdleHelperContent::call_method ('get_course_parents', $course_id);
                foreach ($parents as $parent)
                {
                    $user_id = JUserHelper::getUserId($parent['username']);
                    JoomdleHelperMailinglist::add_sub ($list_id, $user_id);
                }
                break;
			default:
				break;
		}

	}

	static function add_general_list_members ($type)
	{
		$list_id = JoomdleHelperMailinglist::get_general_list_id ($type);

		switch ($type)
		{
			case 'course_students':
				$courses = JoomdleHelperContent::getCourseList ();
				foreach ($courses as $course)
				{
					$teachers = array ();
					$course_id = $course['remoteid'];
					$students = JoomdleHelperContent::call_method ('get_course_students', $course_id);
					foreach ($students as $student)
					{
						$user_id = JUserHelper::getUserId($student['username']);
						//JoomdleHelperAcymailing::add_sub ($list_id, $user_id);
						JoomdleHelperMailinglist::add_sub ($list_id, $user_id);
					}
				}
				break;
			case 'course_teachers':
				$courses = JoomdleHelperContent::getCourseList ();
				foreach ($courses as $course)
				{
					$teachers = array ();
					$course_id = $course['remoteid'];
					$teachers = JoomdleHelperContent::call_method ('get_course_editing_teachers', $course_id);
					foreach ($teachers as $teacher)
					{
						$user_id = JUserHelper::getUserId($teacher['username']);
						//JoomdleHelperAcymailing::add_sub ($list_id, $user_id);
						JoomdleHelperMailinglist::add_sub ($list_id, $user_id);
					}
				}
				break;
            case 'course_parents':
                $parents = JoomdleHelperContent::call_method ('get_all_parents');
                foreach ($parents as $parent)
                {
                    $user_id = JUserHelper::getUserId($parent['username']);
                    JoomdleHelperMailinglist::add_sub ($list_id, $user_id);
                }
                break;
			default:
				break;
		}
	}

	static function delete_mailing_lists ($cid, $type = 'course_students')
	{
		foreach ($cid as $id)
		{
			JoomdleHelperMailinglist::delete_course_mailing_list ($id, $type);
		}
	}

	static function delete_course_mailing_list ($course_id, $type)
	{
		$db           = JFactory::getDBO();

		$list_id = JoomdleHelperMailinglist::get_course_list_id ($course_id, $type);

        $query = 'DELETE ' .
                ' FROM #__joomdle_mailinglists' .
                ' WHERE course_id='.$db->Quote( $course_id ).
				' AND type='.$db->Quote( $type );
        $db->setQuery( $query );
        $db->Query();

		//Delete from mailing list component

		JoomdleHelperAcymailing::delete_list ($list_id);
	}

	static function save_lists_students ($cid)
	{
		foreach ($cid as $id)
		{
			if ($id)
				JoomdleHelperMailinglist::save_course_mailing_list ($id, 'course_students');
			else
				JoomdleHelperMailinglist::save_general_mailing_list ('course_students');
		}
	}

	static function save_lists_teachers ($cid)
	{
		foreach ($cid as $id)
		{
			if ($id)
				JoomdleHelperMailinglist::save_course_mailing_list ($id, 'course_teachers');
			else
				JoomdleHelperMailinglist::save_general_mailing_list ('course_teachers');
		}
	}

    static function save_lists_parents ($cid)
    {
        foreach ($cid as $id)
        {
            if ($id)
                JoomdleHelperMailinglist::save_course_mailing_list ($id, 'course_parents');
            else
                JoomdleHelperMailinglist::save_general_mailing_list ('course_parents');
        }
    }

	static function get_general_lists ()
	{

		$i = 0;
		$c = new JObject ();
		$c->id = 0;
		$c->fullname = JText::_('COM_JOOMDLE_GENERAL');
		$c->published_students = JoomdleHelperMailinglist::general_list_exists ('course_students');
		$c->published_teachers = JoomdleHelperMailinglist::general_list_exists ('course_teachers');
		$c->published_parents = JoomdleHelperMailinglist::general_list_exists ('course_parents');
		$i++;

		return $c;
	}

}
