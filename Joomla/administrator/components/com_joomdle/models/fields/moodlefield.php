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
class JFormFieldMoodlefield extends JFormFieldList
{
	/**
	 * The form field type.
	 *
	 * @var		string
	 * @since	1.6
	 */
	protected $type = 'Moodlefield';

	/**
	 * Method to get the field options.
	 *
	 * @return	array	The field option objects.
	 * @since	1.6
	 */
	protected function getOptions()
	{
		$options = array();

		$fields = array ('firstname', 'lastname', 'email', 'icq', 'skype', 'yahoo', 'aim', 'msn', 'phone1', 'phone2', 'institution', 'department', 
							'address', 'city', 'country', 'lang', 'timezone', 'idnumber', 'description', 'lastnamephonetic', 'firstnamephonetic', 'middlename', 'alternatename');

		foreach ($fields as $field)
		{
			$option['value'] = $field;
			$option['text'] = $field;

			$options[] = $option;
		}

		$moodle_custom_fields = JoomdleHelperMappings::get_moodle_fields ();
		foreach ($moodle_custom_fields as $mf)
		{
			$option['value'] = "cf_".$mf['id'];
			$option['text'] = $mf['name'];

			$options[] = $option;
		}

		return $options;
	}
}
