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


/**
 * HTML View class for the Joomdle component
 */
class JoomdleViewBuycourse extends JViewLegacy {
	function display($tpl = null) {
	global $mainframe;

	$app                = JFactory::getApplication();
    $pathway =$app->getPathWay();

	$menus      = $app->getMenu();
	$menu  = $menus->getActive();

	$params = $app->getParams();
	$this->assignRef('params',              $params);

	$logged_user = JFactory::getUser();
	$user_id = $logged_user->id;

	 $id = $params->get( 'course_id' );
	 if (!$id)
		 $id =  JRequest::getVar( 'course_id' );
	 $id = (int) $id;

	$itemid = JoomdleHelperContent::getMenuItem();
	$link = base64_encode ("index.php?option=com_joomdle&view=detail&course_id=$id&Itemid=$itemid");
	/* If user is logged, dont redirect */
	if (!$user_id)
	{
		$app->redirect(JURI::base ()."index.php?option=com_users&view=login&return=$link");
	}



	 if (!$id)
    {
		echo JText::_('COM_JOOMDLE_NO_COURSE_SELECTED');
        return;
    }


	$this->course_info = JoomdleHelperContent::getCourseInfo($id);

	$this->moodle_url = $params->get( 'MOODLE_URL' );

	/* pathway */
	/*
	$cat_slug = $this->course_info['cat_id'].":".$this->course_info['cat_name'];
	if(is_object($menu) && $menu->query['view'] != 'detail') {
                        $pathway->addItem($this->course_info['cat_name'], 'index.php?view=coursecategory&cat_id='.$cat_slug);
                        $pathway->addItem($this->course_info['fullname'], '');
                }
*/
        parent::display($tpl);
    }
}
?>
