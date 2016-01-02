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
require_once( JPATH_COMPONENT.'/helpers/applications.php' );

class JoomdleViewCourseapplications extends JViewLegacy {
    function display($tpl = null) {

		$cursos = JoomdleHelperContent::getCourseList ();
		$i = 0;
		$c = array ();
		foreach ($cursos as $curso)
		{
				$c[$i] = new JObject ();
				$c[$i]->id = $curso['remoteid'];
				$c[$i]->fullname = $curso['fullname'];
				$i++;
		}

		$this->courses = $c;

        $this->addToolbar();
        $this->sidebar = JHtmlSidebar::render();

        parent::display($tpl);
    }

    protected function addToolbar()
    {
        JToolbarHelper::title(JText::_('COM_JOOMDLE_VIEW_COURSE_APPLICATIONS_TITLE'), 'courseapplications');

        JHtmlSidebar::setAction('index.php?option=com_joomdle&view=courseapplications');
    }


}
?>
