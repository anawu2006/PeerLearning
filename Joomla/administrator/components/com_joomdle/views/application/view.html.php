<?php
/**
 * Joomdle
 *
 * @author Antonio Durán Terrés
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

class JoomdleViewApplication extends JViewLegacy {
    function display($tpl = null) {
	    global $mainframe, $option;

		$mainframe = JFactory::getApplication();

		$id = JRequest::getVar( 'course_id' );
		$comp_params = JComponentHelper::getParams( 'com_joomdle' );


		$app_id = JRequest::getVar( 'app_id' );

		$this->app_info = JoomdleHelperApplications::get_application_info ($app_id);

		$app_user = JFactory::getUser($this->app_info['user_id']);
		$this->app_info['name'] = $app_user->name;
		$course_info = JoomdleHelperContent::getCourseInfo( (int) $this->app_info['course_id']);
		$this->app_info['course'] = $course_info['fullname'];

		$this->addToolbar ();
		parent::display($tpl);

	
    }

	protected function addToolbar()
    {
        JToolbarHelper::title(JText::_('COM_JOOMDLE_VIEW_COURSE_APPLICATIONS_TITLE'), 'mapping');

		JToolBarHelper::back('Back' , "index.php?option=com_joomdle&view=applications&course_id=".$this->app_info['course_id']);
		JToolBarHelper::custom( 'application.approve', 'publish', 'publish', 'Approve application', false, false );
		JToolBarHelper::custom( 'application.reject', 'unpublish', 'unpublish', 'Reject application', false, false );


        JHtmlSidebar::setAction('index.php?option=com_joomdle&view=applications');

    }

}
?>
