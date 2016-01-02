<?php
/**
 * @author Antonio Durán Terrés
 * @package Joomdle
 * @license GNU/GPL
 *
 */

// no direct access
defined('_JEXEC') or die('Restricted access');
// Import Joomla! libraries
jimport( 'joomla.application.component.view');
require_once( JPATH_COMPONENT.'/helpers/content.php' );
require_once( JPATH_COMPONENT.'/helpers/users.php' );

class JoomdleViewUsers extends JViewLegacy {

	protected $items;
    protected $pagination;
    protected $state;


    function display($tpl = null) {
	    global $mainframe, $option;

        $this->users   = $this->get('Items');
        $this->pagination   = $this->get('Pagination');
        $this->state        = $this->get('State');

		$this->addToolbar();
        $this->sidebar = JHtmlSidebar::render();
		parent::display($tpl);
    }

	protected function addToolbar()
    {

        JToolbarHelper::title(JText::_('COM_JOOMDLE_VIEW_USERS_TITLE'), 'user');

		JToolBarHelper::custom( 'add_to_joomla', 'restore', 'restore', 'COM_JOOMDLE_ADD_USERS_TO_JOOMLA', true, false );
		JToolBarHelper::custom( 'add_to_moodle', 'restore', 'restore', 'COM_JOOMDLE_ADD_USERS_TO_MOODLE', true, false );
		JToolBarHelper::custom( 'migrate_to_joomdle', 'restore', 'restore', 'COM_JOOMDLE_MIGRATE_USERS_TO_JOOMDLE', true, false );
		JToolBarHelper::custom( 'sync_profile_to_moodle', 'restore', 'restore', 'COM_JOOMDLE_SYNC_MOODLE_PROFILES', true, false );
		JToolBarHelper::custom( 'sync_profile_to_joomla', 'restore', 'restore', 'COM_JOOMDLE_SYNC_JOOMLA_PROFILES', true, false );
		JToolBarHelper::custom( 'sync_parents_from_moodle', 'restore', 'restore', 'COM_JOOMDLE_SYNC_PARENTS_FROM_MOODLE', true, false );

        JHtmlSidebar::setAction('index.php?option=com_joomdle&view=users');

        JHtmlSidebar::addFilter(
            JText::_('COM_JOOMDLE_SELECT_STATE'),
            'filter_state',
            JHtml::_('select.options',  JoomdleHelperUsers::getStateOptions(), 'value', 'text', $this->state->get('filter.state'))
        );

    }

}
?>
