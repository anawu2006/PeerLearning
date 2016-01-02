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
class JoomdleViewCoursecategory extends JViewLegacy {
	function display($tpl = null) {
		global $mainframe;

		$app                = JFactory::getApplication();
		$pathway =$app->getPathWay();
		$menus      = $app->getMenu();
		$menu  = $menus->getActive();

		$params = $app->getParams();
		$this->assignRef('params',              $params);

		$id =  JRequest::getVar( 'cat_id' );
		if (!$id)
			$id = $params->get( 'cat_id' );
		
		$id = (int) $id;

		if (!$id)
		{
			echo JText::_('COM_JOOMDLE_NO_CATEGORY_SELECTED');
			return;
		}

		$this->cat_id = $id;

		$this->cat_name = JoomdleHelperContent::call_method ('get_cat_name', $id);

		$user = JFactory::getUser();
        $username = $user->username;
		$this->cursos = JoomdleHelperContent::getCourseCategory ($id, $username);

		$this->categories = JoomdleHelperContent::getCourseCategories ($id);

		if(is_object($menu) && $menu->query['view'] != 'coursecategory') {
							$pathway->addItem($this->cat_name, '');
					}

		$this->pageclass_sfx = htmlspecialchars($params->get('pageclass_sfx'));

		$document = JFactory::getDocument();
		$document->setTitle($this->cat_name);

        parent::display($tpl);
    }
}
?>
