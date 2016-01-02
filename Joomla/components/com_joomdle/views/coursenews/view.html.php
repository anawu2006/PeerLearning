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
class JoomdleViewCoursenews extends JViewLegacy {
	function display($tpl = null) {
		global $mainframe;

		$app        = JFactory::getApplication();
		$params = $app->getParams();
		$this->assignRef('params',              $params);

		$user = JFactory::getUser();
		$username = $user->username;

		$id = $params->get( 'course_id' );
			if (!$id)
					$id =  JRequest::getVar( 'course_id' );

		$id = (int) $id;

		if (!$id)
		{
			echo JText::_('COM_JOOMDLE_NO_COURSE_SELECTED');
			return;
		}


        $this->course_info = JoomdleHelperContent::getCourseInfo($id, $username);

		// user not enroled and no guest access
        if ((!$this->course_info['enroled']) && (!$this->course_info['guest']))
            return;

        $this->news = JoomdleHelperContent::getCourseNews($id);
		$this->jump_url =  JoomdleHelperContent::getJumpURL ();

		$document = JFactory::getDocument();
        $document->setTitle($this->course_info['fullname'] . ': ' . JText::_('COM_JOOMDLE_NEWS'));

        $this->pageclass_sfx = htmlspecialchars($params->get('pageclass_sfx'));


        parent::display($tpl);
    }
}
?>
