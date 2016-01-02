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
class JoomdleViewJoomdle extends JViewLegacy {
	function display($tpl = null) {

		$app        = JFactory::getApplication();
		$params = $app->getParams();
		$this->assignRef('params',              $params);

		$enrollable_only = $params->get( 'enrollable_only' );
		$show_buttons = $params->get( 'show_buttons' );

		$sort_by = $params->get( 'sort_by', 'name' );

        switch ($sort_by)
        {
            case 'date':
                $order = 'created DESC';
                break;
            case 'sortorder':
                $order = 'sortorder ASC';
                break;
            default:
                $order = 'fullname ASC';
                break;
        }

		$user = JFactory::getUser();
		$username = $user->username;
		if (($show_buttons) && ($username))
		{
            $this->cursos = JoomdleHelperContent::getCourseList( (int) $enrollable_only,  $order, 0, $username);
		}
		else
            $this->cursos = JoomdleHelperContent::getCourseList( (int) $enrollable_only, $order);

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
