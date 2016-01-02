<?php
/**
 * @version     1.0.0
 * @package     com_joomdle
 * @copyright   Copyright (C) 2012. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access
defined('_JEXEC') or die;

jimport('joomla.application.component.controllerform');
require_once( JPATH_ADMINISTRATOR.'/components/com_joomdle/helpers/applications.php' );


/**
 * Item controller class.
 */
class JoomdleControllerApplication extends JControllerForm
{
    protected $text_prefix = 'COM_JOOMDLE_APPLICATION';

	function approve ()
    {
		$app_id = JRequest::getVar( 'app_id', array(), 'post', 'int' );
        $course_id = JRequest::getVar( 'course_id', array(), 'post', 'int' );
        $cid = array ($app_id);
        JoomdleHelperApplications::approve_applications ($cid);

        $this->setRedirect( 'index.php?option=com_joomdle&view=applications&course_id='.$course_id );
    }

    function reject ()
    {
		$app_id = JRequest::getVar( 'app_id', array(), 'post', 'int' );
        $course_id = JRequest::getVar( 'course_id', array(), 'post', 'int' );
        $cid = array ($app_id);
        JoomdleHelperApplications::reject_applications ($cid);

        $this->setRedirect( 'index.php?option=com_joomdle&view=applications&course_id='.$course_id );
    }

}
