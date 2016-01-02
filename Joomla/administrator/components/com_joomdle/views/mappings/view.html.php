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
require_once( JPATH_COMPONENT.'/helpers/content.php' );
require_once( JPATH_COMPONENT.'/helpers/mappings.php' );

class JoomdleViewMappings extends JViewLegacy {

	protected $items;
    protected $pagination;
    protected $state;


    function display($tpl = null) {
        global $mainframe, $option;

        $this->items   = $this->get('Items');
        $this->pagination   = $this->get('Pagination');
        $this->state        = $this->get('State');

        $this->addToolbar();
        $this->sidebar = JHtmlSidebar::render();
        parent::display($tpl);
    }

    protected function addToolbar()
    {
        JToolbarHelper::title(JText::_('COM_JOOMDLE_VIEW_MAPPINGS_TITLE'), 'mapping');

		JToolbarHelper::addNew('mapping.add');
		JToolbarHelper::deleteList('', 'mappings.delete');

        JHtmlSidebar::setAction('index.php?option=com_joomdle&view=mappings');

        JHtmlSidebar::addFilter(
            JText::_('COM_JOOMDLE_JOOMLA_COMPONENT'),
            'filter_state',
            JHtml::_('select.options',  JoomdleHelperMappings::getStateOptions(), 'value', 'text', $this->state->get('filter.state'))
        );

    }

}
?>
