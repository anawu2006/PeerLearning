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
require_once( JPATH_COMPONENT.'/helpers/profiletypes.php' );

class JoomdleViewCustomprofiletypes extends JViewLegacy {

    protected $items;
    protected $pagination;
    protected $state;

    function display($tpl = null) {

		$mainframe = JFactory::getApplication();
		$params = JComponentHelper::getParams( 'com_joomdle' );

        $this->sidebar = JHtmlSidebar::render();

		if (!$params->get( 'use_profiletypes' ))
		{
			JToolbarHelper::title(JText::_('COM_JOOMDLE_VIEW_PROFILETYPES_TITLE'), 'customprofiletypes');
            $this->message = JText::_('COM_JOOMDLE_PROFILE_TYPES_INTEGRATION_NOT_ENABLED');
            $tpl = "disabled";
            parent::display($tpl);
			return;
		}

		/* List of profiletypes */
		$this->profiletypes   = $this->get('Items');
		$this->pagination   = $this->get('Pagination');
		$this->state        = $this->get('State');

		$this->addToolbar();
		parent::display($tpl);
    }

    protected function addToolbar()
    {

        JToolbarHelper::title(JText::_('COM_JOOMDLE_VIEW_PROFILETYPES_TITLE'), 'customprofiletypes');

		JToolBarHelper::custom( 'create_profiletype_on_moodle', 'publish', 'publish', 'COM_JOOMDLE_CREATE_ON_MOODLE', true, false );
		JToolBarHelper::custom( 'dont_create_profiletype_on_moodle', 'unpublish', 'unpublish', 'COM_JOOMDLE_NOT_CREATE_ON_MOODLE', true, false );

        JHtmlSidebar::setAction('index.php?option=com_joomdle&view=customprofiletypes');

        JHtmlSidebar::addFilter(
            JText::_('COM_JOOMDLE_SELECT_STATE'),
            'filter_state',
            JHtml::_('select.options',  JoomdleHelperProfiletypes::getStateOptions(), 'value', 'text', $this->state->get('filter.state'))
        );

    }

}
?>
