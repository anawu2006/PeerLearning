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
class JoomdleViewSendcert extends JViewLegacy {
	function display($tpl = null) {
	global $mainframe;

	$cert_id     = JRequest::getString('cert_id', '', 'post');
	$this->simple =  JRequest::getVar( 'simple' );

	$app        = JFactory::getApplication();
    $params = $app->getParams();
    $this->assignRef('params',              $params);


	 $data = $this->getData();
        if ($data === false) {
            return false;
        }

	$this->set('data'  , $data);


	parent::display($tpl);
    }

    function &getData()
    {
        $user = JFactory::getUser();
        $data = new stdClass();

        $data->cert_id = JRequest::getVar('cert_id', '');

        // Load with previous data, if it exists
        $mailto     = JRequest::getString('mailto', '', 'post');
        $sender     = JRequest::getString('sender', '', 'post');
        $from       = JRequest::getString('from', '', 'post');
        $subject    = JRequest::getString('subject', '', 'post');

        if ($user->get('id') > 0) {
            $data->sender   = $user->get('name');
            $data->from     = $user->get('email');
        }
        else
        {
            $data->sender   = $sender;
            $data->from     = $from;
        }

        $data->subject  = $subject;
        $data->mailto   = $mailto;

        return $data;
    }

}
?>
