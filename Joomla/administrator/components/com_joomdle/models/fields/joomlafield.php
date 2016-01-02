<?php
/**
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('JPATH_BASE') or die;

jimport('joomla.html.html');
jimport('joomla.form.formfield');
jimport('joomla.form.helper');
JFormHelper::loadFieldClass('list');

require_once( JPATH_ADMINISTRATOR.'/components/com_joomdle/helpers/mappings.php' );

/**
 * Form Field class for the Joomla Framework.
 *
 * @package		Qexan
 */
class JFormFieldJoomlafield extends JFormFieldList
{
	/**
	 * The form field type.
	 *
	 * @var		string
	 * @since	1.6
	 */
	protected $type = 'Joomlafield';

	/**
	 * Method to get the field options.
	 *
	 * @return	array	The field option objects.
	 * @since	1.6
	 */
	protected function getOptions()
	{
		$options = array();

		$params = JComponentHelper::getParams( 'com_joomdle' );
		$app = $params->get( 'additional_data_source' );

		$joomla_fields = JoomdleHelperMappings::get_fields ($app);

		if (is_array ($joomla_fields))
		foreach ($joomla_fields as $field)
		{
			$option['value'] = $field->id;
			$option['text'] = JText::_ ($field->name);

			$options[] = $option;
		}

		return $options;
	}
}
