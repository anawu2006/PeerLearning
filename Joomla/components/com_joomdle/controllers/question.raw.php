<?php
// no direct access
defined('_JEXEC') or die;

jimport('joomla.application.component.controller');

require_once(JPATH_ADMINISTRATOR.'/components/com_joomdle/helpers/content.php');

class JoomdleControllerQuestion extends JControllerLegacy
{
	public function get_correct_answer ()
	{
		$question_id = JRequest::getVar ('question_id', '');

		$answer_id = JoomdleHelperContent::call_method ('quiz_get_correct_answer', (int) $question_id);
		echo $answer_id;
		
		exit ();
	}
}

?>
