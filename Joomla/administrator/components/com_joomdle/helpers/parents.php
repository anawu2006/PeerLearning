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
require_once(JPATH_ADMINISTRATOR.'/'.'components'.'/'.'com_joomdle'.'/'.'helpers'.'/'.'content.php');

class JoomdleHelperParents
{

	static function getUnassignedCourses ()
	{
		$user = JFactory::getUser();
                $id = $user->get('id');
                $username = $user->get('username');

		$db           = JFactory::getDBO();

		$sql = "SELECT * from #__joomdle_purchased_courses" .
			" WHERE user_id = '$id' and num > 0";

		$db->setQuery($sql);
		$courses = $db->loadObjectList();

		$i = 0;
		if (!$courses)
			return array();

		foreach ($courses as $course)
		{
			$course_info = JoomdleHelperContent::getCourseInfo ((int) $course->course_id);

			$c[$i]['id'] = $course->course_id;
			$c[$i]['num'] = $course->num;
			$c[$i]['name'] = $course_info['fullname'];

			$i++;
		}

		return $c;
	}

	static function getChildren ()
	{
		$user = JFactory::getUser();
                $id = $user->get('id');
                $username = $user->get('username');

		$db           = JFactory::getDBO();

		$sql = "SELECT * from #__users" .
			" WHERE params LIKE '%parent_id\":\"$id\"%'";

		$db->setQuery($sql);
		$users = $db->loadObjectList();

		if (!$users)
			return array ();

		$j = 0;
		foreach ($users as $child)
		{
			$c[$j]['id'] = $child->id;
			$c[$j]['name'] = $child->name;
			$courses = JoomdleHelperContent::getMyCourses ($child->username);
			$i = 0;
			$user_courses = array ();
			if ((is_array ($courses)) && (count ($courses)))
			{
				foreach ($courses as $course)
				{
					$user_courses[$i] = $course['id'];
					$i++;
				}
			}
			$c[$j]['courses'] = $user_courses;
			$j++;
		}

		return $c;
	}

	static function childrenSelect ($row_id = 1)
	{
		 $children = JoomdleHelperParents::getChildren ();
		 foreach ($children as $child)
		 {
			 $options[] = JHTML::_('select.option', $child['id'], $child['name']);

		 }
		 echo JHTML::_('select.genericlist', $options, 'children'.'['.$row_id.']', 'multiple=multiple', 'value', 'text'); //, $value, $control_name.$name );
	}


	static function childrenCheckbox ($user_id, $course_id, $disabled)
	{
		echo '<input type="checkbox" name="children['.$course_id.'][]" value="'.$user_id.'"';
		if ($disabled)
			echo " disabled";
		echo '>';
	}


	static function childrenCheckboxes ($course_id)
	{
		$params = JComponentHelper::getParams( 'com_joomdle' );
        $assign_courses_include_parent = $params->get( 'assign_courses_include_parent' );

		// Add current user if configured
		if ($assign_courses_include_parent)
		{
			$user = JFactory::getUser ();
			$courses = JoomdleHelperContent::getMyCourses ($user->username);
			$disabled = false;
			if ((is_array ($courses)) && (count ($courses)))
			{
				foreach ($courses as $course)
				{
					if ($course['id'] == $course_id)
					{
						$disabled = true;
						break;
					}
				}
			}
			JoomdleHelperParents::childrenCheckbox ($user->id, $course_id, $disabled);
			echo $user->name;
			if ($disabled)
				echo  " ".JText::_( 'COM_JOOMDLE_ALREADY_ENROLED' );
			echo "<br>";
		}

		$children = JoomdleHelperParents::getChildren ();
		foreach ($children as $child)
		{
			if (in_array ($course_id, $child['courses']))
				$disabled = true;
			else
				$disabled = false;

			JoomdleHelperParents::childrenCheckbox ($child['id'], $course_id, $disabled);
			echo $child['name'];
			if ($disabled)
				echo  " ".JText::_( 'COM_JOOMDLE_ALREADY_ENROLED' );
			echo "<br>";
		}
	}


	static function assingment_available ($course_id, $assingment)
	{
		$user = JFactory::getUser();
                $id = $user->get('id');
                $username = $user->get('username');

		$num = count ($assingment);

		$db           = JFactory::getDBO();

		$sql = "SELECT * from #__joomdle_purchased_courses" .
			" WHERE user_id = " . $db->Quote($id) ." and num >= " . $db->Quote($num) .
			" AND course_id = " . $db->Quote($course_id);

		$db->setQuery($sql);
		$courses = $db->loadObjectList();

		if (!$courses)
			return false;

		return true;
	}

	static function check_assign_availability ($assingments)
	{
		foreach ($assingments as $course_id => $a)
		{
			$available = JoomdleHelperParents::assingment_available($course_id, $a);
			if (!$available)
				return false;
		}
		return true;
	}

	static function assign_courses ($assingments)
	{
		foreach ($assingments as $course_id => $a)
		{
			JoomdleHelperParents::assign_course($course_id, $a);
		}
	}

	static function assign_course ($course_id, $assingment)
	{
		foreach ($assingment as $user_id)
		{
			$user = JFactory::getUser($user_id);
			$username = $user->get('username');
			JoomdleHelperContent::enrolUser($username, $course_id);
			 /* Send confirmation email */
			JoomdleHelperShop::send_confirmation_email ($user->email, $course_id);
		}
		JoomdleHelperParents::update_purchase ($course_id, $assingment);
	}

	static function update_purchase ($course_id, $assingment)
	{
		$user = JFactory::getUser();
                $id = $user->get('id');

		$db           = JFactory::getDBO();

		$sql = "SELECT * from #__joomdle_purchased_courses" .
			" WHERE user_id = ". $db->Quote($id) . " and course_id = " . $db->Quote($course_id);

		$db->setQuery($sql);
		$pc = $db->loadObject();

		$a = new JObject ();
		$a->user_id = $id;
		$a->course_id = $course_id;

		if ($pc)
		{
			$a->id = $pc->id;
			$a->num = $pc->num - count ($assingment);
			/* Update row */
			$db->updateObject ('#__joomdle_purchased_courses', $a, 'id');
		}
	}

	static function purchase_course ($username, $course_id, $num)
	{
		$user_id = JUserHelper::getUserId($username);
        $user = JFactory::getUser($user_id);

		$id = $user->get('id');

		$db           = JFactory::getDBO();

		$sql = "SELECT * from #__joomdle_purchased_courses" .
			" WHERE user_id = ". $db->Quote($id) . " and course_id = " . $db->Quote($course_id);


		$db->setQuery($sql);
		$pc = $db->loadObject();

		$a = new JObject ();
		$a->user_id = $id;
		$a->course_id = $course_id;

		if ($pc)
		{
			$a->id = $pc->id;
			$a->num = $num + $pc->num;
			/* Update row */
			$db->updateObject ('#__joomdle_purchased_courses', $a, 'id');
		}
		else
		{
			$a->num = $num;
			/* Insert row */
			$db->insertObject ('#__joomdle_purchased_courses', $a);
		}
	}

	static function sync_parents_from_moodle ($users_ids)
	{
		foreach ($users_ids as $id)
		{
			$user = JFactory::getUser($id);
			$parents = JoomdleHelperContent::call_method ('get_parents', $user->username);
			foreach ($parents as $parent)
			{
				$user_id = JUserHelper::getUserId($parent['username']);
                $parent = JFactory::getUser($user_id);
				$user->setParam('u'.$parent->id.'_parent_id', $parent->id);
				$user->save();
			}
		}
	}

}
