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
require_once (JPATH_ADMINISTRATOR . '/components/com_joomdle/helpers/mailinglist.php');


/**
 * Item controller class.
 */
class JoomdleControllerMailinglist extends JControllerForm
{
    protected $text_prefix = 'COM_JOOMDLE_MAILINGLIST';

	function students_publish ()
    {
        JRequest::checkToken() or jexit( 'Invalid Token' );

        $cid = JRequest::getVar( 'cid', array(), 'post', 'array' );
        JArrayHelper::toInteger($cid);

        if (count( $cid ) < 1) {
                JError::raiseError(500, JText::_( 'Select an item to create' ) );
        }
        JoomdleHelperMailinglist::save_lists_students ($cid);

        $this->setRedirect( 'index.php?option=com_joomdle&view=mailinglist' );
    }

    function students_unpublish ()
    {
        JRequest::checkToken() or jexit( 'Invalid Token' );

        $cid = JRequest::getVar( 'cid', array(), 'post', 'array' );
        JArrayHelper::toInteger($cid);

        if (count( $cid ) < 1) {
                JError::raiseError(500, JText::_( 'Select an item to create' ) );
        }
        JoomdleHelperMailinglist::delete_mailing_lists ($cid, 'course_students');

        $this->setRedirect( 'index.php?option=com_joomdle&view=mailinglist' );
    }

    function teachers_publish ()
    {
        JRequest::checkToken() or jexit( 'Invalid Token' );

        $cid = JRequest::getVar( 'cid', array(), 'post', 'array' );
        JArrayHelper::toInteger($cid);

        if (count( $cid ) < 1) {
                JError::raiseError(500, JText::_( 'Select an item to create' ) );
        }
        JoomdleHelperMailinglist::save_lists_teachers ($cid);

        $this->setRedirect( 'index.php?option=com_joomdle&view=mailinglist' );
    }

	function teachers_unpublish ()
    {
        JRequest::checkToken() or jexit( 'Invalid Token' );

        $cid = JRequest::getVar( 'cid', array(), 'post', 'array' );
        JArrayHelper::toInteger($cid);

        if (count( $cid ) < 1) {
                JError::raiseError(500, JText::_( 'Select an item to create' ) );
        }
        JoomdleHelperMailinglist::delete_mailing_lists ($cid, 'course_teachers');

        $this->setRedirect( 'index.php?option=com_joomdle&view=mailinglist' );
    }

	function parents_publish ()
    {
        JRequest::checkToken() or jexit( 'Invalid Token' );

        $cid = JRequest::getVar( 'cid', array(), 'post', 'array' );
        JArrayHelper::toInteger($cid);

        if (count( $cid ) < 1) {
                JError::raiseError(500, JText::_( 'Select an item to create' ) );
        }
        JoomdleHelperMailinglist::save_lists_parents ($cid);

        $this->setRedirect( 'index.php?option=com_joomdle&view=mailinglist' );
    }

	function parents_unpublish ()
    {
        JRequest::checkToken() or jexit( 'Invalid Token' );

        $cid = JRequest::getVar( 'cid', array(), 'post', 'array' );
        JArrayHelper::toInteger($cid);

        if (count( $cid ) < 1) {
                JError::raiseError(500, JText::_( 'Select an item to create' ) );
        }
        JoomdleHelperMailinglist::delete_mailing_lists ($cid, 'course_parents');

        $this->setRedirect( 'index.php?option=com_joomdle&view=mailinglist' );
    }



}
