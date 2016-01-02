<?php
/**
* @version		$Id: list.php 10707 2008-08-21 09:52:47Z eddieajau $
* @package		Joomla.Framework
* @subpackage	Parameter
* @copyright	Copyright (C) 2005 - 2008 Open Source Matters. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* Joomla! is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/

// Check to ensure this file is within the rest of the framework
defined('JPATH_BASE') or die();
require_once(JPATH_SITE.'/components/com_joomdle/helpers/content.php');

/**
 * Renders a list element
 *
 * @package 	Joomla.Framework
 * @subpackage		Parameter
 * @since		1.5
 */

class JFormFieldCourse extends JFormField
{
	/**
	* Element type
	*
	* @access	protected
	* @var		string
	*/
	//var	$_name = 'Course';
	public $type ='Course';

	//function fetchElement($name, $value, &$node, $control_name)
	protected function getInput()
	{
//		$class = ( $node->attributes('class') ? 'class="'.$node->attributes('class').'"' : 'class="inputbox"' );
		$attr = $this->element['class'] ? ' class="'.(string) $this->element['class'].'"' : '';

		$courses = JoomdleHelperContent::getCourseList (0);

		$options = array ();
		if (is_array ($courses))
		{
			foreach ($courses as $course)
			{
				$val = $course['remoteid'];
				$text = $course['fullname'];
				$options[] = JHTML::_('select.option', $val, JText::_($text));
			}
		}
		array_unshift($options, JHTML::_('select.option', '0', '- '.JText::_('COM_JOOMDLE_SELECT_COURSE').' -'));

		return JHTML::_('select.genericlist',  $options, $this->name, $attr, 'value', 'text', $this->value, $this->id);
	}
}
