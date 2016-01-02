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
class JoomdleModelUsers extends JModelList
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
				'name', 'name',
				'username', 'username',
				'email', 'email',
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


		$state = $this->getUserStateFromRequest($this->context.'.filter.state', 'filter_state', '');

		if ($state == '')
		{
			// XXX Check for sites with lots of users: this may hang this page until timeout if we show all users
            // XXX so we use a safe filter instead
            $total_moodle = JoomdleHelperContent::getMoodleUsersNumber ($search);
			$db     = $this->getDbo();
			if ($search != '')
				$searchEscaped = $db->Quote( '%'.$db->escape( $search, true ).'%', false );
			else $searchEscaped = "";
            $total_joomla = JoomdleHelperContent::getJoomlaUsersNumber ($searchEscaped);

            $max_users = 1000;
            if (($total_joomla > $max_users) || ($total_moodle > $max_users))
				$state = 'joomla';

		}
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
		$db		= $this->getDbo();
		$search = $this->getState ('filter.search');
		if ($search != '')
			$searchEscaped = $db->Quote( '%'.$db->escape( $search, true ).'%', false );
		else $searchEscaped = "";

		$pagination = $this->getPagination ();
		$limitstart = $pagination->limitstart;

		
		$limit = $pagination->limit;

		$listOrder  = $this->state->get('list.ordering');
		$listDirn   = $this->state->get('list.direction');
		$filter_order = $listOrder;
		$filter_order_Dir = $listDirn;

		$filter_type = $this->getState ('filter.state');
		switch ($filter_type)
		{

			case 'moodle':
				$users = JoomdleHelperContent::getMoodleUsers ($limitstart, $limit,$filter_order, $filter_order_Dir, $search);
				break;
			case 'joomla':
				$users = JoomdleHelperContent::getJoomlaUsers ($limitstart, $limit, $filter_order, $filter_order_Dir, $searchEscaped);
				break;
			case 'joomdle':
				$users = JoomdleHelperContent::getJoomdleUsers ($limitstart, $limit,  $filter_order, $filter_order_Dir, $searchEscaped);
				break;
			case 'not_joomdle':
				$users = JoomdleHelperContent::getNotJoomdleUsers ($limitstart, $limit, $filter_order, $filter_order_Dir, $search);
				break;
			default:
				$users = JoomdleHelperContent::getAllUsers ($limitstart, $limit, $filter_order, $filter_order_Dir, $search); 
				break;
		}

		return $users;
	}

	protected function getListQuery()
	{
		//XXX Note: this does nothing useful for us now, as we cannot pull data via a simple DB query,  but it seems needed

		// Create a new query object.
		$db		= $this->getDbo();
		$query	= $db->getQuery(true);

		// Select the required fields from the table.
		$query->select(
			$this->getState(
				'list.select',
				'a.*'
			)
		);
		$query->from('`#__users` AS a');

		return $query;
	}

	function getTotal ()
	{
		$db		= $this->getDbo();
		$search = $this->getState ('filter.search');
		$filter_type = $this->state->get ('filter.state');
		switch ($filter_type)
		{
			case 'moodle':
				$total = JoomdleHelperContent::getMoodleUsersNumber ($search);
				break;
			case 'joomla':
				$searchEscaped = $db->Quote( '%'.$db->escape( $search, true ).'%', false );
				$total = JoomdleHelperContent::getJoomlaUsersNumber ($searchEscaped);
				break;
			case 'joomdle':
				$searchEscaped = $db->Quote( '%'.$db->escape( $search, true ).'%', false );
				$total = count (JoomdleHelperContent::getJoomdleUsers (0, 0, '', '', $searchEscaped));
				break;
			case 'not_joomdle':
				$total = count (JoomdleHelperContent::getNotJoomdleUsers (0, 0, 'username', 'asc', $search));
				break;
			default:
				$total = JoomdleHelperContent::getAllUsersNumber ($search);
				break;
		}
		return $total;
	}
}
