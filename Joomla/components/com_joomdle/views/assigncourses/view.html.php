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
require_once( JPATH_ADMINISTRATOR.'/components/com_joomdle/helpers/parents.php' );

/**
 * HTML View class for the Joomdle component
 */
class JoomdleViewAssigncourses extends JViewLegacy {
	function display($tpl = null) {

		$app        = JFactory::getApplication();
		$params = $app->getParams();
		$this->assignRef('params',              $params);

		$this->my_courses = JoomdleHelperParents::getUnassignedCourses();

		$this->pageclass_sfx = htmlspecialchars($params->get('pageclass_sfx'));

		parent::display($tpl);
    }
}
?>
