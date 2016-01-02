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

jimport( 'joomla.application.component.view');

/**
 * HTML View class for the Joomdle component
 */
class JoomdleViewCoursestats extends JViewLegacy {
	function display($tpl = null) {

		$app                = JFactory::getApplication();
		$pathway =$app->getPathWay();
		$menus      = $app->getMenu();
		$menu  = $menus->getActive();

		$params = $app->getParams();
		$this->assignRef('params',              $params);

		$id = $params->get( 'course_id' );
			if (!$id)
					$id =  JRequest::getVar( 'course_id' );

		$id = (int) $id;

		if (!$id)
		{
			echo JText::_('COM_JOOMDLE_NO_COURSE_SELECTED');
			return;
		}



		$this->course_info = JoomdleHelperContent::getCourseInfo($id);
		$this->student_no = JoomdleHelperContent::getCourseStudentsNo($id);
		$this->assignments = JoomdleHelperContent::getAssignmentSubmissions($id);
		$this->grades = JoomdleHelperContent::getAssignmentGrades($id);
		$this->daily_stats = JoomdleHelperContent::getCourseDailyStats($id);

		if(is_object($menu) && $menu->query['view'] != 'coursestats') {
							$pathway->addItem($this->course_info['fullname'], '');
					}

		$document = JFactory::getDocument();
        $document->setTitle($this->course_info['fullname'] . ': ' . JText::_('COM_JOOMDLE_STATS'));


        parent::display($tpl);
    }
}
?>
