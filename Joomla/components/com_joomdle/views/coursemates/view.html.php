<?php

// no direct access
defined('_JEXEC') or die('Restricted access');

jimport( 'joomla.application.component.view');

class JoomdleViewCoursemates extends JViewLegacy {
	function display($tpl = null) {
		global $mainframe;

		$course_id =  JRequest::getVar( 'course_id' );
        if (!$course_id)
            $course_id = $params->get( 'course_id' );


		$app        = JFactory::getApplication();
		$params = $app->getParams();
		$this->assignRef('params',              $params);

		$this->course_info = JoomdleHelperContent::getCourseInfo($course_id);
		$this->users = JoomdleHelperContent::call_method('get_course_students', (int) $course_id);

		$document = JFactory::getDocument();
		$document->setTitle($this->course_info['fullname'] . ': ' . JText::_('COM_JOOMDLE_STUDENTS'));

		$this->pageclass_sfx = htmlspecialchars($params->get('pageclass_sfx'));


		parent::display($tpl);
    }
}
?>
