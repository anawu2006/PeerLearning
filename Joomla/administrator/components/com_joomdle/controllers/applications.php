<?php
/**
 * @package     com_joomdle
 * @copyright   Copyright (C) 2012. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access.
defined('_JEXEC') or die;

jimport('joomla.application.component.controlleradmin');
require_once( JPATH_ADMINISTRATOR.'/components/com_joomdle/helpers/applications.php' );


/**
 * Items list controller class.
 */
class JoomdleControllerApplications extends JControllerAdmin
{

	public function approve ()
	{
		 JRequest::checkToken() or jexit( 'Invalid Token' );

        $course_id = JRequest::getVar( 'course_id', array(), 'post', 'int' );
        $cid = JRequest::getVar( 'cid', array(), 'post', 'array' );
        JArrayHelper::toInteger($cid);

        if (count( $cid ) < 1) {
                JError::raiseError(500, JText::_( 'Select an item to approve' ) );
        }
        JoomdleHelperApplications::approve_applications ($cid);

        $this->setRedirect( 'index.php?option=com_joomdle&view=applications&course_id='.$course_id );

	}

    function reject ()
    {
        JRequest::checkToken() or jexit( 'Invalid Token' );

        $course_id = JRequest::getVar( 'course_id', array(), 'post', 'int' );
        $cid = JRequest::getVar( 'cid', array(), 'post', 'array' );
        JArrayHelper::toInteger($cid);

        if (count( $cid ) < 1) {
                JError::raiseError(500, JText::_( 'Select an item to reject' ) );
        }
        JoomdleHelperApplications::reject_applications ($cid);

        $this->setRedirect( 'index.php?option=com_joomdle&view=applications&course_id='.$course_id );
    }

}
