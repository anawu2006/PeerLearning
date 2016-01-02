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
class JoomdleViewNewsitem extends JViewLegacy {
	function display($tpl = null) {
	global $mainframe;

	$app                = JFactory::getApplication();
	$pathway =$app->getPathWay();
	$menus      = $app->getMenu();
	$menu  = $menus->getActive();

	$params = $app->getParams();
	$this->assignRef('params',              $params);


	$course_id =  JRequest::getVar( 'course_id' );
	if (!$course_id)
    {
        echo JText::_('COM_JOOMDLE_NO_COURSE_SELECTED');
        return;
    }

	$id =  JRequest::getVar( 'id' );
	$id = (int) $id;

	if (!$id)
    {
        echo JText::_('COM_JOOMDLE_NO_NEWS_ITEM_SELECTED');
        return;
    }

	$user = JFactory::getUser();
    $username = $user->username;
    $this->course_info = JoomdleHelperContent::getCourseInfo($course_id, $username);

    // user not enroled and no guest access
    if ((!$this->course_info['enroled']) && (!$this->course_info['guest']))
        return;

	$this->news_item = JoomdleHelperContent::call_method ( 'get_news_item', (int) $id);

	/* pathway */
        $cat_slug = $this->course_info['cat_id'].":".$this->course_info['cat_name'];
        $course_slug = $this->course_info['remoteid'].":".$this->course_info['fullname'];

        if(is_object($menu) && $menu->query['view'] != 'newsitem') {
                        $pathway->addItem($this->course_info['cat_name'], 'index.php?view=coursecategory&cat_id='.$cat_slug);
                        $pathway->addItem($this->course_info['fullname'], 'index.php?view=detail&cat_id='.$cat_slug.'&course_id='.$course_slug);
                        $pathway->addItem(JText::_('COM_JOOMDLE_COURSE_CONTENTS'), 'index.php?view=course&cat_id='.$cat_slug.'&course_id='.$course_slug);
                        $pathway->addItem($this->news_item[0]['subject'], '');
                }


		$document = JFactory::getDocument();
        $document->setTitle($this->course_info['fullname'] . ': ' . $this->news_item[0]['subject']);

        parent::display($tpl);
    }
}
?>
