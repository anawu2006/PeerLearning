<?php
/**
 * @version     1.0.0
 * @package     com_joomdle
 * @copyright   Copyright (C) 2012. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access.
defined('_JEXEC') or die;

jimport('joomla.application.component.modeladmin');

class JoomdleModelCustomprofiletype extends JModelAdmin
{
	/**
	 * @var		string	The prefix to use with controller messages.
	 * @since	1.6
	 */
	protected $text_prefix = 'COM_JOOMDLE';


	/**
	 * Returns a reference to the a Table object, always creating it.
	 *
	 * @param	type	The table type to instantiate
	 * @param	string	A prefix for the table class name. Optional.
	 * @param	array	Configuration array for model. Optional.
	 * @return	JTable	A database object
	 * @since	1.6
	 */
	public function getTable($type = 'customprofiletypes', $prefix = 'JoomdleTable', $config = array())
	{
		return JTable::getInstance($type, $prefix, $config);
	}

	/**
	 * Method to get the record form.
	 *
	 * @param	array	$data		An optional array of data for the form to interogate.
	 * @param	boolean	$loadData	True if the form is to load its own data (default case), false if not.
	 * @return	JForm	A JForm object on success, false on failure
	 * @since	1.6
	 */
	public function getForm($data = array(), $loadData = true)
	{
		// Initialise variables.
		$app	= JFactory::getApplication();

		// Get the form.
		$form = $this->loadForm('com_joomdle.customprofiletype', 'customprofiletype', array('control' => 'jform', 'load_data' => $loadData));
		if (empty($form)) {
			return false;
		}

		return $form;
	}

	/**
	 * Method to get the data that should be injected in the form.
	 *
	 * @return	mixed	The data for the form.
	 * @since	1.6
	 */
	protected function loadFormData()
	{
		// Check the session for previously entered form data.
		$data = JFactory::getApplication()->getUserState('com_joomdle.edit.customprofiletype.data', array());

		if (empty($data)) {
			$data = $this->getItem();
		}

		return $data;
	}

	/**
	 * Method to get a single record.
	 *
	 * @param	integer	The id of the primary key.
	 *
	 * @return	mixed	Object on success, false on failure.
	 * @since	1.6
	 */
	public function getItem($pk = null)
	{
		/*
		// Get info by profiletype_id
		$pk = (!empty($pk)) ? $pk : (int) $this->getState($this->getName() . '.id');

		echo $pk;
		echo "XXXX";
		$item = JoomdleHelperProfiletypes::get_profiletype_data ($pk);
//		$item->profiletype = $item->name;
		print_r ($item);
		return $item;
*/
		if ($item = parent::getItem($pk)) {

			// Get profile name
			$profile_info = JoomdleHelperProfiletypes::get_profiletype_data ($item->profiletype_id);
			$item->name = $profile_info->name;

		}

		return $item;
	}

	function save_X ($data)
	{
		print_r ($data);
		exit ();
	}

}
