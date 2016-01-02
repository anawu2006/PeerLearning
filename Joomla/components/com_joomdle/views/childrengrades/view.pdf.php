<?php
/**
 *
 * @author Antonio Durán Terrés
 * @package Joomdle
 * @license GNU/GPL
 *
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

jimport( 'joomla.application.component.view');
require_once(JPATH_SITE.'/components/com_joomdle/helpers/content.php');


/**
 * HTML View class for the Joomdle component
 */
class JoomdleViewChildrengrades extends JViewLegacy {
	function display($tpl = null) {
		global $mainframe;

		$app        = JFactory::getApplication();
		$params = $app->getParams();
		$this->assignRef('params',              $params);

		$user = JFactory::getUser();
        $username = $user->username;
		$this->child = JFactory::getUser($username);

		$layout = $params->get('layout');

		$this->tasks = JoomdleHelperContent::call_method ("get_children_grade_user_report", $username);

		$tpl = "catspdf";

		$this->pageclass_sfx = htmlspecialchars($params->get('pageclass_sfx'));

        $this->_prepareDocument();


        $htmlcontent = parent::loadTemplate ($tpl);

        require_once(JPATH_SITE. '/libraries/tcpdf/tcpdf.php');
        $pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

        $header = JText::_('COM_JOOMDLE_GRADES');

        $pdf->SetHeaderData('', 0, $header);

        //set margins
        $pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
        $pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
        $pdf->SetFooterMargin(PDF_MARGIN_FOOTER);

        $pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));

        $pdf->setFontSubsetting(false);

        $pdf->SetFont('times', '', 8);
        // add a page
        $pdf->AddPage("L");

        // output the HTML content
        $pdf->writeHTML($htmlcontent, true, 0, true, 0);

        $pdf->Output("grades.pdf", 'D');
        exit ();

    }

	protected function _prepareDocument()
    {
        $app    = JFactory::getApplication();
        $menus  = $app->getMenu();
        $title  = null;

        // Because the application sets a default page title,
        // we need to get it from the menu item itself
        $menu = $menus->getActive();
        if ($menu)
        {
            $this->params->def('page_heading', $this->params->get('page_title', $menu->title));
        } else {
            $this->params->def('page_heading', JText::_('COM_JOOMDLE_MY_GRADES'));
        }
    }

}
?>
