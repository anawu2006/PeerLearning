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
require_once( JPATH_COMPONENT.'/helpers/content.php' );
require_once( JPATH_COMPONENT.'/helpers/profiletypes.php' );

class JoomdleViewCustomprofiletype extends JViewLegacy {

    protected $form;

    protected $item;

    function display($tpl = null) {
        global $mainframe, $option;

        $this->form         = $this->get('Form');
        $this->item         = $this->get('Item');

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

        JToolbarHelper::title(JText::_('COM_JOOMDLE_VIEW_PROFILETYPES_TITLE'), 'customprofiletype');
        JToolbarHelper::apply('customprofiletype.apply');
        JToolbarHelper::save('customprofiletype.save');

        if (empty($this->item->id))  {
            JToolbarHelper::cancel('customprofiletype.cancel');
        } else {
            JToolbarHelper::cancel('customprofiletype.cancel', 'JTOOLBAR_CLOSE');
        }
    }
}

?>
