<?php
/**
* @version		
* @package		Jomdle
* @copyright	Copyright (C) 2008 - 2010 Antonio Duran Terres
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

class JFormFieldCoursecategory extends JFormField
{
	/**
	* Element type
	*
	* @access	protected
	* @var		string
	*/
	 public $type = 'Coursecategory';

	protected function getInput()
	//function fetchElement($name, $value, &$node, $control_name)
	{
		$attr = $this->element['class'] ? ' class="'.(string) $this->element['class'].'"' : '';

		$options = $this->getCats (0);
		array_unshift($options, JHTML::_('select.option', '0', '- '.JText::_('COM_JOOMDLE_SELECT_CATEGORY').' -'));

		return JHTML::_('select.genericlist',  $options, $this->name, $attr, 'value', 'text', $this->value, $this->id);
	}


	function getCats ($cat_id, $options = array(), $level = 0)
	{
		$cats = JoomdleHelperContent::getCourseCategories ($cat_id);

		if (!is_array ($cats))
			return $options;

		foreach ($cats as $cat)
		{
			$val = $cat['id'];
			$text = $cat['name'];
			for ($i = 0; $i < $level; $i++)
				$text = "-".$text;
			$options[] = JHTML::_('select.option', $val, $text);
			$options = $this->getCats ($cat['id'], $options, $level + 1);
		}

		return $options;
	}
}
