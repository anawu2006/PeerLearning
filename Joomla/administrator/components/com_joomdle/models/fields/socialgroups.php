<?php
/**
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('JPATH_BASE') or die;

jimport('joomla.html.html');
jimport('joomla.form.formfield');
jimport('joomla.form.helper');
JFormHelper::loadFieldClass('list');

//require_once( JPATH_ADMINISTRATOR.'/components/com_joomdle/helpers/activities.php' );

/**
 * Form Field class for the Joomla Framework.
 *
 * @package		Joomdle
 */
class JFormFieldSocialgroups extends JFormFieldList
{
	/**
	 * The form field type.
	 *
	 * @var		string
	 * @since	1.6
	 */
	protected $type = 'Socialgroups';

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
		$app = $params->get( 'activities' );

		$option = array ('value' => 'no', 'text' => JText::_ ('COM_JOOMDLE_NONE'));
		$options[] = $option;

		// Add sources added via plugins
		JPluginHelper::importPlugin( 'joomdlesocialgroups' );
		$dispatcher = JDispatcher::getInstance();
		$more_sources = $dispatcher->trigger('onGetSocialGroupsExtension', array());
		if (is_array ($more_sources))
		foreach ($more_sources as  $source)
		{
			$keys =  array_keys ($source);
			$key = $keys[0];
			$source_name = array_shift ($source);
			$option['value'] = $key;
			$option['text'] = $source_name;

			$options[] = $option;
		}

		return $options;
	}
}
