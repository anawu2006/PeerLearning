<?php
// no direct access
defined('_JEXEC') or die('Restricted access');

jimport( 'joomla.application.component.view');
require_once(JPATH_ADMINISTRATOR.'/components/com_joomdle/helpers/content.php');


class JoomdleViewCoursegrades extends JViewLegacy {
	function display($tpl = null) {

        $app        = JFactory::getApplication();
        $params = $app->getParams();

        $this->assignRef('params',              $params);

        $this->course_id = $params->get( 'course_id' );
        if (!$this->course_id)
            $this->course_id =  JRequest::getVar( 'course_id' );
        $this->course_id = (int) $this->course_id;

        // Only for logged users
        $user = JFactory::getUser();
        $username = $user->username;
        if (!$username)
            return;

        if (!$this->course_id)
        {
            echo JText::_('COM_JOOMDLE_NO_COURSE_SELECTED');
            return;
        }

        $this->course_info = JoomdleHelperContent::getCourseInfo($this->course_id, $username);

        // user not enroled
        if (!$this->course_info['enroled'])
            return;


        $document = JFactory::getDocument();
        $document->setTitle($this->course_info['fullname'] . ': ' . JText::_('COM_JOOMDLE_GRADES'));

        $this->pageclass_sfx = htmlspecialchars($params->get('pageclass_sfx'));

    //  $this->gcats = JoomdleHelperContent::call_method ("get_course_grades_by_category", $this->course_id, $username);
        $this->gcats = JoomdleHelperContent::call_method ("get_grade_user_report", $this->course_id, $username);


        $tpl = "catspdf";


		$this->_prepareDocument();


		$htmlcontent = parent::loadTemplate ($tpl);

		require_once(JPATH_SITE. '/libraries/tcpdf/tcpdf.php');
        $pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

		$header = $this->course_info['fullname'];

		$header2 = JText::_('COM_JOOMDLEGRADES_TEACHER') . ': ' . $user->name;
		$header2 .= ' ' . JText::_('COM_JOOMDLEGRADES_DATE') . ': ' . date ('d-m-Y');

	//	$pdf->SetHeaderData('', 0, $header, $header2);
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

	//parent::display($tpl);
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
            $this->params->def('page_heading', JText::_('COM_JOOMDLE_MY_COURSES'));
        }
    }

}
?>
