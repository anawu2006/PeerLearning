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
require_once( JPATH_ADMINISTRATOR.'/components/com_joomdle/helpers/shop.php' );
require_once( JPATH_ADMINISTRATOR.'/components/com_joomdle/helpers/system.php' );


/**
 * HTML View class for the Joomdle component
 */
class JoomdleViewDetail extends JViewLegacy {
	function display($tpl = null) {
		global $mainframe;

		$app                = JFactory::getApplication();
		$pathway = $app->getPathWay();

		$menus      = $app->getMenu();

		$menu  = $menus->getActive();

		// Load the form validation behavior
        JHTML::_('behavior.formvalidation');

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


		$user = JFactory::getUser();
        $username = $user->username;
		
		$this->course_info = JoomdleHelperContent::getCourseInfo($id, $username);

		/* pathway */
		$cat_slug = $this->course_info['cat_id'].":".JFilterOutput::stringURLSafe($this->course_info['cat_name']);

		if(is_object($menu) && $menu->query['view'] != 'detail') {
							$pathway->addItem($this->course_info['cat_name'], 'index.php?view=coursecategory&cat_id='.$cat_slug);
							$pathway->addItem($this->course_info['fullname'], '');
					}

		$document = JFactory::getDocument();
		$document->setTitle($this->course_info['fullname']);

		$this->pageclass_sfx = htmlspecialchars($params->get('pageclass_sfx'));

        parent::display($tpl);
    }
}
?>
