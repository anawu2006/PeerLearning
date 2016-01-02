<?php
/**
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('JPATH_BASE') or die;

jimport('joomla.html.html');
jimport('joomla.form.formfield');
jimport('joomla.form.helper');
JFormHelper::loadFieldClass('list');

require_once( JPATH_ADMINISTRATOR.'/components/com_joomdle/helpers/profiletypes.php' );

/**
 * Form Field class for the Joomla Framework.
 *
 * @package		Qexan
 */
class JFormFieldProfiletype extends JFormFieldList
{
	/**
	 * The form field type.
	 *
	 * @var		string
	 * @since	1.6
	 */
	protected $type = 'Profiletype';

	/**
	 * Method to get the field options.
	 *
	 * @return	array	The field option objects.
	 * @since	1.6
	 */
	protected function getOptions()
	{
		$options = array();

		$pts = JoomdleHelperProfiletypes::getProfiletypes ('', 0, 1000, 'name', '', '');

		$option['value'] = 0;
		$option['text'] = JText::_('COM_JOOMDLE_NONE');;
		$options[] = $option;

		if (is_array ($pts))
		foreach ($pts as $pt)
		{
			$option['value'] = $pt->id;
			$option['text'] = $pt->name;
			$options[] = $option;
		}

		return $options;
	}
}
