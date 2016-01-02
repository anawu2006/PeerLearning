<?php
/**
 * Joomdle
 *
 * @author Antonio Durán Terrés
 * @package Joomla
 * @subpackage Joomdle
 * @license GNU/GPL
 *
 */

// no direct access
defined('_JEXEC') or die('Restricted access');
// Import Joomla! libraries
jimport( 'joomla.application.component.view');
require_once( JPATH_COMPONENT.'/helpers/shop.php' );
require_once( JPATH_COMPONENT.'/helpers/groups.php' );

class JoomdleViewShop extends JViewLegacy {
    function display($tpl = null) {

		$params = JComponentHelper::getParams( 'com_joomdle' );

        $this->sidebar = JHtmlSidebar::render();

		if ($params->get( 'shop_integration' ) == 'no')
		{
			JToolbarHelper::title(JText::_('COM_JOOMDLE_VIEW_SHOP_TITLE'), 'shop');
			$this->message = JText::_('COM_JOOMDLE_SHOP_INTEGRATION_NOT_ENABLED');
			$tpl = "disabled";
			parent::display($tpl);
			return;
		}

        $this->addToolbar();

		$this->bundles = JoomdleHelperShop::get_bundles ();
		$this->courses = JoomdleHelperShop::getShopCourses ();


        parent::display($tpl);
    }

    protected function addToolbar()
    {
        JToolbarHelper::title(JText::_('COM_JOOMDLE_VIEW_SHOP_TITLE'), 'shop');

		JToolBarHelper::addNew('bundle.add', 'COM_JOOMDLE_NEW_BUNDLE');
		JToolbarHelper::publish('shop.publish', 'JTOOLBAR_PUBLISH', true);
		JToolbarHelper::unpublish('shop.unpublish', 'JTOOLBAR_UNPUBLISH', true);

		JToolBarHelper::custom( 'shop.reload', 'restore', 'restore', 'COM_JOOMDLE_RELOAD_FROM_MOODLE', true, false );
		JToolBarHelper::trash('shop.delete_courses_from_shop');


        JHtmlSidebar::setAction('index.php?option=com_joomdle&view=shop');
    }

}
?>
