<?php
/**
* @version		
* @package		Joomdle
* @copyright	Copyright (C) 2008 - 2010 Antonio Duran Terres
* @license		GNU/GPL, see LICENSE.php
*/

// Check to ensure this file is within the rest of the framework
defined('JPATH_BASE') or die();
require_once(JPATH_SITE.'/components/com_joomdle/helpers/content.php');

JFormHelper::loadFieldClass('list');

/**
 * Renders a list element
 *
 * @package 	Joomla.Framework
 * @subpackage		Parameter
 * @since		1.5
 */

class JFormFieldRole extends JFormFieldList
{
	/**
	* Element type
	*
	* @access	protected
	* @var		string
	*/
	var	$_name = 'Role';

	function getOptions()
	{
		$roles = JoomdleHelperContent::call_method ('get_roles');

		$options = array ();

		$option['value'] = 0;
		$option['text'] = JText::_('COM_JOOMDLE_SELECT_ROLE');
		$options[] = $option;

		if (is_array ($roles))
		{
			foreach ($roles as $role)
			{
				$option['value'] = $role['id'];
				$option['text'] = $role['name'];
				$options[] = $option;
			}
		}

		return $options;
	}
}
