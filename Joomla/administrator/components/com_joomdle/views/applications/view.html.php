<?php
/**
 * Joomdle
 *
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
require_once( JPATH_COMPONENT.'/helpers/mappings.php' );
require_once( JPATH_COMPONENT.'/helpers/applications.php' );

class JoomdleViewApplications extends JViewLegacy {
    function display($tpl = null) {

		$id = JRequest::getVar( 'course_id' );

		$this->items   = $this->get('Items');
        $this->pagination   = $this->get('Pagination');
        $this->state        = $this->get('State');

		$course_info = JoomdleHelperContent::getCourseInfo( (int) $id);
		$this->course_name = $course_info['fullname'];


		$this->addToolbar();
		$this->sidebar = JHtmlSidebar::render();

		parent::display($tpl);
    }

	protected function addToolbar()
    {
        JToolbarHelper::title(JText::_('COM_JOOMDLE_VIEW_COURSE_APPLICATIONS_TITLE'), 'mapping');


		JToolBarHelper::back('Back' , 'index.php?option=com_joomdle&view=courseapplications');
		JToolBarHelper::custom( 'applications.approve', 'publish', 'publish', 'Approve applications', true, false );
		JToolBarHelper::custom( 'applications.reject', 'unpublish', 'unpublish', 'Reject applications', true, false );

        JHtmlSidebar::setAction('index.php?option=com_joomdle&view=courseapplications');

        JHtmlSidebar::addFilter(
            JText::_('COM_JOOMDLE_STATE'),
            'filter_state',
            JHtml::_('select.options',  JoomdleHelperApplications::getStateOptions(), 'value', 'text', $this->state->get('filter.state'))
        );

    }

}
?>
