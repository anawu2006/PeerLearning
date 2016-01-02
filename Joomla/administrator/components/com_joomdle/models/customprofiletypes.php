<?php
/**
 * @copyright	Copyright (C) Antonio Duran Terres
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;


jimport('joomla.application.component.modellist');

/**
 * Methods supporting a list of user records.
 *
 */
class JoomdleModelCustomprofiletypes extends JModelList
{
	/**
	 * Constructor.
	 *
	 * @param	array	An optional associative array of configuration settings.
	 * @see		JController
	 * @since	1.6
	 */
	public function __construct($config = array())
	{
		if (empty($config['filter_fields'])) {
			$config['filter_fields'] = array(
				'joomla_app', 'joomla_app',
				'moodle_field', 'moodle_field',
			);
		}

		parent::__construct($config);
	}

	/**
	 * Method to auto-populate the model state.
	 *
	 * Note. Calling getState in this method will result in recursion.
	 *
	 * @return	void
	 * @since	1.6
	 */
	protected function populateState($ordering = null, $direction = null)
	{
		// Initialise variables.
		$app = JFactory::getApplication('administrator');

		// Adjust the context to support modal layouts.
		if ($layout = JRequest::getVar('layout', 'default')) {
			$this->context .= '.'.$layout;
		}

		// Load the filter state.
		$search = $this->getUserStateFromRequest($this->context.'.filter.search', 'filter_search');
		$this->setState('filter.search', $search);


		$state = $this->getUserStateFromRequest($this->context.'.filter.state', 'filter_state');
		$this->setState('filter.state', $state);

		// Load the parameters.
		$params		= JComponentHelper::getParams('com_joomdle');
		$this->setState('params', $params);

		// List state information.
		parent::populateState('name', 'asc');
	}

	/**
	 * Method to get a store id based on model configuration state.
	 *
	 * This is necessary because the model is used by the component and
	 * different modules that might need different sets of data or different
	 * ordering requirements.
	 *
	 * @param	string		$id	A prefix for the store id.
	 *
	 * @return	string		A store id.
	 * @since	1.6
	 */
	protected function getStoreId($id = '')
	{
		// Compile the store id.
		$id	.= ':'.$this->getState('filter.search');
		$id	.= ':'.$this->getState('filter.state');

		return parent::getStoreId($id);
	}

	/**
	 * Gets the list of users
	 *
	 * @return	mixed	An array of data items on success, false on failure.
	 * @since	1.6
	 */
	public function getItems()
	{
		$db           = JFactory::getDBO();

		$search = $this->getState ('filter.search');
		if ($search)
			$searchEscaped = $db->Quote( '%'.$db->getEscaped( $search, true ).'%', false );
		else $searchEscaped = "";

		$pagination = $this->getPagination ();
		$limitstart = $pagination->limitstart;
		$limit = $pagination->limit;


		$listOrder  = $this->state->get('list.ordering');
		$listDirn   = $this->state->get('list.direction');
		$filter_order = $listOrder;
		$filter_order_Dir = $listDirn;

		$filter_type = $this->getState ('filter.state');
		if ($filter_type == '*')
			$filter_type = 0;

		$items = JoomdleHelperProfiletypes::getProfiletypes ($filter_type, $limitstart, $limit,$filter_order, $filter_order_Dir, $searchEscaped);

		return $items;
	}

	protected function getListQuery()
	{
		// NOT USED
	}

}
