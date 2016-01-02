<?php
/**
 * Joomdle
 *
 * @author Antonio Durán Terrés
 * @license GNU/GPL
 *
 *
 */

// no direct access
defined('_JEXEC') or die('Restricted access');
// Import Joomla! libraries
jimport( 'joomla.application.component.view');

require_once(JPATH_ADMINISTRATOR.'/components/com_joomdle/helpers/mailinglist.php');


class JoomdleViewMailinglist extends JViewLegacy {
    function display($tpl = null) {

		$this->sidebar = JHtmlSidebar::render();
		$params = JComponentHelper::getParams( 'com_joomdle' );
		if ($params->get('mailing_list_integration') == 'no')
		{
			JToolbarHelper::title(JText::_('COM_JOOMDLE_VIEW_MAILINGLIST_TITLE'), 'mailinglist');
            $this->message = JText::_('COM_JOOMDLE_MAILING_LIST_INTEGRATION_NOT_ENABLED');
            $tpl = "disabled";
            parent::display($tpl);
			return;
		}

		$this->courses = JoomdleHelperMailinglist::getListCourses ();

		$this->addToolbar();
        parent::display($tpl);
    }

    protected function addToolbar()
    {
        JToolbarHelper::title(JText::_('COM_JOOMDLE_VIEW_MAILINGLIST_TITLE'), 'mailinglist');

		JToolbarHelper::publish('mailinglist.students_publish', 'COM_JOOMDLE_CREATE_STUDENT_LIST', true);
        JToolbarHelper::unpublish('mailinglist.students_unpublish', 'COM_JOOMDLE_DELETE_STUDENT_LIST', true);

		JToolbarHelper::publish('mailinglist.teachers_publish', 'COM_JOOMDLE_CREATE_TEACHER_LIST', true);
        JToolbarHelper::unpublish('mailinglist.teachers_unpublish', 'COM_JOOMDLE_DELETE_TEACHER_LIST', true);

		JToolbarHelper::publish('mailinglist.parents_publish', 'COM_JOOMDLE_CREATE_PARENT_LIST', true);
        JToolbarHelper::unpublish('mailinglist.parents_unpublish', 'COM_JOOMDLE_DELETE_PARENT_LIST', true);

        JHtmlSidebar::setAction('index.php?option=com_joomdle&view=mailinglist');
    }

}
?>
