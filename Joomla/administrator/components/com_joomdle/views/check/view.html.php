<?php
/**
 * Joomdle
 *
 * @author Antonio Durán Terrés
 * @license GNU/GPL
 *
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

// Import Joomla! libraries
jimport( 'joomla.application.component.view');
class JoomdleViewCheck extends JViewLegacy {
    function display($tpl = null) {

	$params = JComponentHelper::getParams( 'com_joomdle' );
        if ($params->get( 'MOODLE_URL' ) == "")
        {
			echo "Joomdle is not configured yet. Please fill Moodle URL setting in Configuration";
			return;
        }


		$this->system_info = JoomdleHelperContent::check_joomdle_system ();

		$this->addToolbar();
		$this->sidebar = JHtmlSidebar::render();
		parent::display($tpl);
    }

    protected function addToolbar()
    {
        JToolbarHelper::title(JText::_('COM_JOOMDLE_VIEW_SYSTEM_CHECK_TITLE'), 'check');

        JHtmlSidebar::setAction('index.php?option=com_joomdle&view=check');
    }


}
?>
