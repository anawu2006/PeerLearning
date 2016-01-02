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
require_once( JPATH_COMPONENT.'/helpers/content.php' );
require_once( JPATH_COMPONENT.'/helpers/mappings.php' );

class JoomdleViewMapping extends JViewLegacy {

    protected $form;

    protected $item;

    function display($tpl = null) {
	    global $mainframe, $option;

		$this->form         = $this->get('Form');
        $this->item         = $this->get('Item');

		$params = JComponentHelper::getParams( 'com_joomdle' );
		$additional_data_source = $params->get( 'additional_data_source' );

		if ($additional_data_source == 'no')
		{
			echo JText::_ ('COM_JOOMDLE_YOU_NEED_TO_SELECT_AN_ADDITIONAL_DATA_SOURCE');
			return;
		}

        // Check for errors.
        if (count($errors = $this->get('Errors'))) {
            JError::raiseError(500, implode("\n", $errors));
            return false;
        }

        parent::display($tpl);
		$this->addToolbar();

    }

	protected function addToolbar()
    {
        JFactory::getApplication()->input->set('hidemainmenu', true);

        $isNew = ($this->item->id == 0);

		JToolbarHelper::title(JText::_('COM_JOOMDLE_VIEW_MAPPINGS_TITLE'), 'mapping');
		JToolbarHelper::apply('mapping.apply');
		JToolbarHelper::save('mapping.save');

        if (empty($this->item->id))  {
            JToolbarHelper::cancel('mapping.cancel');
        } else {
            JToolbarHelper::cancel('mapping.cancel', 'JTOOLBAR_CLOSE');
        }
    }

}
?>
