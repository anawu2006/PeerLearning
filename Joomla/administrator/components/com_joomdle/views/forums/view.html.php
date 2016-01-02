<?php
/**
 * @author Antonio Durán Terrés
 * @package Joomdle
 * @license GNU/GPL
 *
 */

// no direct access
defined('_JEXEC') or die('Restricted access');
// Import Joomla! libraries
jimport( 'joomla.application.component.view');
require_once( JPATH_COMPONENT.'/helpers/forum.php' );
require_once( JPATH_COMPONENT.'/helpers/content.php' );

class JoomdleViewForums extends JViewLegacy {
    function display($tpl = null) {

		$this->courses = JoomdleHelperContent::getCourseList ();

        $this->addToolbar();
		$this->sidebar = JHtmlSidebar::render();

        parent::display($tpl);
    }

	protected function addToolbar()
    {
        JToolbarHelper::title(JText::_('COM_JOOMDLE_VIEW_FORUMS_TITLE'), 'forums');

		JToolBarHelper::custom( 'sync_to_kunena', 'restore', 'restore', 'COM_JOOMDLE_SYNC_TO_KUNENA', true, false );

        JHtmlSidebar::setAction('index.php?option=com_joomdle&view=forums');
    }

}
?>
