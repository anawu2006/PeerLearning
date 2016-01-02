<?php
/**
 * @copyright	Antonio Duran Terres
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;


jimport('joomla.application.component.modellist');

/**
 * Methods supporting a list of user records.
 *
 */
class JoomdleModelApplications extends JModelList
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

        $course_id = $app->input->get('course_id', null);
		$this->setState('filter.course_id', $course_id);


		// Load the parameters.
		$params		= JComponentHelper::getParams('com_joomdle');
		$this->setState('params', $params);

		// List state information.
		parent::populateState('u.name', 'asc');
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
		$id	.= ':'.$this->getState('filter.course_id');

		return parent::getStoreId($id);
	}

	protected function getListQuery()
	{
		// Create a new query object.
		$db		= $this->getDbo();
		$query	= $db->getQuery(true);

		// Select the required fields from the table.
		$query->select(
			$this->getState(
				'list.select',
				'a.*, u.username, u.email, u.name'
			)
		);

		$query->from('`#__joomdle_course_applications` AS a');

		$query->leftJoin ('`#__users` AS u on a.user_id = u.id');

		// Filter by course
        $course_id = $this->getState('filter.course_id');
        if (is_numeric($course_id)) {
                $query->where('a.course_id='.(int) $course_id);
        }

		// Filter by state
        $state = $this->getState('filter.state');
        if (is_numeric($state)) {
                $query->where('a.state='.(int) $state);
        }


		return $query;
	}

}
