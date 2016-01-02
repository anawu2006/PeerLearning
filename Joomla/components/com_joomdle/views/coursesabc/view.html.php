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
class JoomdleViewCoursesabc extends JViewLegacy {
	function display($tpl = null) {

	$app        = JFactory::getApplication();
    $params = $app->getParams();
	$this->assignRef('params',              $params);

	$chars =  JRequest::getVar( 'start_chars' );
	if (!$chars)
		$chars = $params->get( 'start_chars', 'ABC' );

	$user = JFactory::getUser();
	$username = $user->username;
	$this->cursos = JoomdleHelperContent::call_method ('courses_abc',  $chars , $username);

	$this->pageclass_sfx = htmlspecialchars($params->get('pageclass_sfx'));

	$this->_prepareDocument();

	parent::display($tpl);
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
            $this->params->def('page_heading', JText::_('COM_JOOMDLE_COURSES'));
        }
    }

}
?>
