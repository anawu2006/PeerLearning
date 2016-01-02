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

class JoomdleModelConfig extends JModelAdmin
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
	public function getTable($type = 'mappings', $prefix = 'JoomdleTable', $config = array())
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
		$form = $this->loadForm('com_joomdle.config', 'config', array('control' => 'jform', 'load_data' => $loadData));
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
		$data = JFactory::getApplication()->getUserState('com_joomdle.edit.mapping.data', array());

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
		if ($item = parent::getItem($pk)) {

			// Set Joomla component for new mappings
			if (!$item->joomla_app)
				$item->joomla_app = 'joomla16';
		}

		return $item;
	}

    function save ($data)
	{
		$app    = JFactory::getApplication();
		if (file_exists (JPATH_SITE . '/components/com_config/model/cms.php'))
			require_once JPATH_SITE . '/components/com_config/model/cms.php';
		if (file_exists (JPATH_SITE . '/components/com_config/model/form.php'))
			require_once JPATH_SITE . '/components/com_config/model/form.php';
		if (file_exists (JPATH_ADMINISTRATOR . '/components/com_config/model/component.php'))
			require_once JPATH_ADMINISTRATOR . '/components/com_config/model/component.php';
		if (file_exists (JPATH_ADMINISTRATOR . '/components/com_config/models/component.php'))
			require_once JPATH_ADMINISTRATOR . '/components/com_config/models/component.php';

        $model = new ConfigModelComponent ();

		//Get joomdle extension id
        $db           = JFactory::getDBO();
        $query = 'SELECT extension_id '.
                ' FROM #__extensions'.
                " WHERE name = 'com_joomdle'";
        $db->setQuery($query);
        $extension_id = $db->loadResult();

        $option = 'com_joomdle';

		// Generate auth token if needed
		if ($data['joomla_auth_token'] == '')
		{
			$token = JUserHelper::genRandomPassword(32);
			$token = preg_replace('/[\x00-\x1F\x7F]/', '', $token);

			$data['joomla_auth_token'] = $token;
		}

        $data   = array(
                    'params'    => $data,
                    'id'        => $extension_id,
                    'option'    => $option
                    );
        $return = $model->save($data);

		return $return;
	}

    public function getData()
    {
        $params = JComponentHelper::getParams('com_joomdle');

        return $params;
    }


}
