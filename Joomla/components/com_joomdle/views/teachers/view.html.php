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
class JoomdleViewTeachers extends JViewLegacy {
	function display($tpl = null) {
		global $mainframe;

		$app                = JFactory::getApplication();
		$pathway = $app->getPathWay();

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
		$this->teachers = JoomdleHelperContent::getCourseTeachers($id);

		/* pathway */
		$cat_slug = $this->course_info['cat_id'].":".$this->course_info['cat_name'];
		$course_slug = $this->course_info['remoteid'].":".$this->course_info['fullname'];

		if(is_object($menu) && $menu->query['view'] != 'teachers') {
							$pathway->addItem($this->course_info['cat_name'], 'index.php?view=coursecategory&cat_id='.$cat_slug);
							$pathway->addItem($this->course_info['fullname'], 'index.php?view=detail&cat_id='.$cat_slug.'&course_id='.$course_slug);
							$pathway->addItem(JText::_('COM_JOOMDLE_COURSE_TEACHERS'), '');
					}

		$document = JFactory::getDocument();
        $document->setTitle($this->course_info['fullname'] . ': ' . JText::_('COM_JOOMDLE_TEACHERS'));

        $this->pageclass_sfx = htmlspecialchars($params->get('pageclass_sfx'));

		parent::display($tpl);
    }
}
?>
