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
class JoomdleViewMycourses extends JViewLegacy {
	function display($tpl = null) {
	global $mainframe;

	$app        = JFactory::getApplication();
    $params = $app->getParams();
    $this->assignRef('params',              $params);


	$group_by_category = $params->get( 'group_by_category' );

	$user = JFactory::getUser();
	$username = $user->username;
	if ($group_by_category)
		$this->my_courses = JoomdleHelperContent::call_method ('my_courses', $username, 1);
	else
		$this->my_courses = JoomdleHelperContent::getMyCourses();

	$this->jump_url =  JoomdleHelperContent::getJumpURL ();

	$this->pageclass_sfx = htmlspecialchars($params->get('pageclass_sfx'));

	$this->_prepareDocument();


	if ($group_by_category)
		$tpl =  "cats";

	if ($this->my_courses)
		parent::display($tpl);
	else
		echo '<span class="joomdle_nocourses_message">'.$params->get('nocourses_text') . "</span>";
    }

	protected function _prepareDocument()
    {
        $app    = JFactory::getApplication();
        $menus  = $app->getMenu();
        $title  = null;

        // Because the application sets a default page title,
        // we need to get it from the menu item itself
        $menu = $menus->getActive();
        if ($menu)
        {
            $this->params->def('page_heading', $this->params->get('page_title', $menu->title));
        } else {
            $this->params->def('page_heading', JText::_('COM_JOOMDLE_MY_COURSES'));
        }
    }

}
?>
