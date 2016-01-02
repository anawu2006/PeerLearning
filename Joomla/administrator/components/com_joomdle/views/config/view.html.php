<?php
/**
 * Joomla! 1.5 component Joomdle
 *
 * @version $Id: view.html.php 2009-04-17 03:54:05 svn $
 * @author Antonio Durán Terrés
 * @package Joomla
 * @subpackage Joomdle
 * @license GNU/GPL
 *
 * Shows information about Moodle courses
 *
 * This component file was created using the Joomla Component Creator by Not Web Design
 * http://www.notwebdesign.com/joomla_component_creator/
 *
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

// Import Joomla! libraries
jimport( 'joomla.application.component.view');
class JoomdleViewConfig extends JViewLegacy {
    function display($tpl = null) {

		$form   = $this->get('Form');
        $data   = $this->get('Data');
        $user = JFactory::getUser();

        // Check for model errors.
        if ($errors = $this->get('Errors')) {
            JError::raiseError(500, implode('<br />', $errors));
            return false;
        }

        // Bind the form to the data.
        if ($form && $data) {
            $form->bind($data);
        }

		$this->form   = &$form;

        $this->addToolbar();
		$this->sidebar = JHtmlSidebar::render();
        parent::display($tpl);
    }

	protected function addToolbar()
    {
        JToolbarHelper::title(JText::_('COM_JOOMDLE_VIEW_MAILINGLIST_TITLE_CONFIGURATION'), 'config.png');
        JToolbarHelper::apply('config.apply');
        JToolbarHelper::save('config.save');
        JToolbarHelper::cancel('config.cancel');

		JHtmlSidebar::setAction('index.php?option=com_joomdle&view=courseapplications');
    }

}
?>
