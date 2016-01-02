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
class JoomdleViewTeacher extends JViewLegacy {
	function display($tpl = null) {
		global $mainframe;

		$app        = JFactory::getApplication();
		$params = $app->getParams();
		$this->assignRef('params',              $params);

		$username =  JRequest::getVar( 'username' );
		if (!$username)
			$username = $params->get( 'username');

		if (!$username)
		{
			echo JText::_('COM_JOOMDLE_NO_USER_SELECTED');
			return;
		}


		$this->courses = JoomdleHelperContent::call_method('teacher_courses', $username);
		$this->username = $username;
		$this->user_info = JoomdleHelperMappings::get_user_info_for_joomla ($this->username);

		$document = JFactory::getDocument();
        $document->setTitle($this->user_info['name']);

        $this->pageclass_sfx = htmlspecialchars($params->get('pageclass_sfx'));


        parent::display($tpl);
    }
}
?>
