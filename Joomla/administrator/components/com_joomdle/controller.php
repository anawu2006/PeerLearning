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

jimport( 'joomla.application.component.controller' );
require_once( JPATH_COMPONENT.'/'.'helpers'.'/'.'helper.php' );
require_once( JPATH_COMPONENT.'/'.'helpers'.'/'.'content.php' );
require_once( JPATH_COMPONENT.'/'.'helpers'.'/'.'shop.php' );
require_once( JPATH_COMPONENT.'/'.'helpers'.'/'.'mappings.php' );
require_once( JPATH_COMPONENT.'/'.'helpers'.'/'.'parents.php' );
require_once( JPATH_COMPONENT.'/'.'helpers'.'/'.'profiletypes.php' );
require_once( JPATH_COMPONENT.'/'.'helpers'.'/'.'applications.php' );
require_once( JPATH_COMPONENT.'/'.'helpers'.'/'.'mailinglist.php' );
require_once (JPATH_ADMINISTRATOR . '/components/com_joomdle/helpers/forum.php');


/**
 * Joomdle Controller
 *
 * @package Joomla
 * @subpackage Joomdle
 */
class JoomdleController extends JControllerLegacy {
    /**
     * Constructor
     * @access private
     * @subpackage Joomdle
     */
    function __construct() {
        //Get View
        if(JRequest::getCmd('view') == '') {
            JRequest::setVar('view', 'default');
        }
        $this->item_type = 'Default';
        parent::__construct();
    }

	public function display($cachable = false, $urlparams = false)
    {
        // Load the submenu.
        JoomdleHelper::addSubmenu(JRequest::getCmd('view', 'default'));

		parent::display();

        return $this;

	}


    	/* Users actions */
	function add_to_moodle ()
	{
		JRequest::checkToken() or jexit( 'Invalid Token' );

                $cid = JRequest::getVar( 'cid', array(), 'post', 'array' );
                JArrayHelper::toInteger($cid);

                if (count( $cid ) < 1) {
                        JError::raiseError(500, JText::_( 'Select an item to unpublish' ) );
                }
		JoomdleHelperContent::add_moodle_users ($cid);

                $this->setRedirect( 'index.php?option=com_joomdle&view=users' );
	}

	function migrate_to_joomdle ()
	{
		JRequest::checkToken() or jexit( 'Invalid Token' );

                $cid = JRequest::getVar( 'cid', array(), 'post', 'array' );
                JArrayHelper::toInteger($cid);

                if (count( $cid ) < 1) {
                        JError::raiseError(500, JText::_( 'Select an item to unpublish' ) );
                }
		JoomdleHelperContent::migrate_users_to_joomdle ($cid);

                $this->setRedirect( 'index.php?option=com_joomdle&view=users' );
	}

	function sync_profile_to_moodle ()
	{
		JRequest::checkToken() or jexit( 'Invalid Token' );

                $cid = JRequest::getVar( 'cid', array(), 'post', 'array' );
                JArrayHelper::toInteger($cid);

                if (count( $cid ) < 1) {
                        JError::raiseError(500, JText::_( 'Select an item to sync' ) );
                }
		JoomdleHelperContent::sync_moodle_profiles ($cid);

                $this->setRedirect( 'index.php?option=com_joomdle&view=users' );
	}

	function sync_profile_to_joomla ()
	{
		JRequest::checkToken() or jexit( 'Invalid Token' );

                $cid = JRequest::getVar( 'cid', array(), 'post', 'array' );
                JArrayHelper::toInteger($cid);

                if (count( $cid ) < 1) {
                        JError::raiseError(500, JText::_( 'Select an item to sync' ) );
                }
		JoomdleHelperContent::sync_joomla_profiles ($cid);

                $this->setRedirect( 'index.php?option=com_joomdle&view=users' );
	}

	function add_to_joomla ()
	{
		JRequest::checkToken() or jexit( 'Invalid Token' );

                $cid = JRequest::getVar( 'cid', array(), 'post', 'array' );
                JArrayHelper::toInteger($cid);

                if (count( $cid ) < 1) {
                        JError::raiseError(500, JText::_( 'Select an item to unpublish' ) );
                }
		JoomdleHelperContent::create_joomla_users ($cid);

                $this->setRedirect( 'index.php?option=com_joomdle&view=users' );
	}

	function sync_parents_from_moodle ()
	{
		JRequest::checkToken() or jexit( 'Invalid Token' );

                $cid = JRequest::getVar( 'cid', array(), 'post', 'array' );
                JArrayHelper::toInteger($cid);

                if (count( $cid ) < 1) {
                        JError::raiseError(500, JText::_( 'Select an item to unpublish' ) );
                }
		JoomdleHelperParents::sync_parents_from_moodle ($cid);

                $this->setRedirect( 'index.php?option=com_joomdle&view=users' );
	}


	function create_profiletype_on_moodle ()
	{
		JRequest::checkToken() or jexit( 'Invalid Token' );

                $cid = JRequest::getVar( 'cid', array(), 'post', 'array' );
                JArrayHelper::toInteger($cid);

                if (count( $cid ) < 1) {
                        JError::raiseError(500, JText::_( 'Select an item to delete' ) );
                }
		JoomdleHelperProfiletypes::create_on_moodle ($cid);

                $this->setRedirect( 'index.php?option=com_joomdle&view=customprofiletypes' );
	}

	function dont_create_profiletype_on_moodle ()
	{
		JRequest::checkToken() or jexit( 'Invalid Token' );

                $cid = JRequest::getVar( 'cid', array(), 'post', 'array' );
                JArrayHelper::toInteger($cid);

                if (count( $cid ) < 1) {
                        JError::raiseError(500, JText::_( 'Select an item to delete' ) );
                }
		JoomdleHelperProfiletypes::dont_create_on_moodle ($cid);

                $this->setRedirect( 'index.php?option=com_joomdle&view=customprofiletypes' );
	}

	function approve_applications ()
	{
		JRequest::checkToken() or jexit( 'Invalid Token' );

		$course_id = JRequest::getVar( 'course_id', array(), 'post', 'int' );
		$cid = JRequest::getVar( 'cid', array(), 'post', 'array' );
		JArrayHelper::toInteger($cid);

		if (count( $cid ) < 1) {
				JError::raiseError(500, JText::_( 'Select an item to approve' ) );
		}
		JoomdleHelperApplications::approve_applications ($cid);

		$this->setRedirect( 'index.php?option=com_joomdle&view=courseapplications&task=manage_applications&course_id='.$course_id );
	}

	function reject_applications ()
	{
		JRequest::checkToken() or jexit( 'Invalid Token' );

		$course_id = JRequest::getVar( 'course_id', array(), 'post', 'int' );
		$cid = JRequest::getVar( 'cid', array(), 'post', 'array' );
		JArrayHelper::toInteger($cid);

		if (count( $cid ) < 1) {
				JError::raiseError(500, JText::_( 'Select an item to reject' ) );
		}
		JoomdleHelperApplications::reject_applications ($cid);

		$this->setRedirect( 'index.php?option=com_joomdle&view=courseapplications&task=manage_applications&course_id='.$course_id );
	}

	function approve_application ()
	{
		$app_id = JRequest::getVar( 'app_id', array(), 'post', 'int' );
		$course_id = JRequest::getVar( 'course_id', array(), 'post', 'int' );
		$cid = array ($app_id);
		JoomdleHelperApplications::approve_applications ($cid);

		$this->setRedirect( 'index.php?option=com_joomdle&view=courseapplications&task=manage_applications&course_id='.$course_id );
	}

	function reject_application ()
	{
		$app_id = JRequest::getVar( 'app_id', array(), 'post', 'int' );
		$course_id = JRequest::getVar( 'course_id', array(), 'post', 'int' );
		$cid = array ($app_id);
		JoomdleHelperApplications::reject_applications ($cid);

		$this->setRedirect( 'index.php?option=com_joomdle&view=courseapplications&task=manage_applications&course_id='.$course_id );
	}

	function save_profiletype ()
    {
        $id = JRequest::getVar( 'profiletype_id', array(), 'post', 'int' );
        $create_on_moodle = JRequest::getVar( 'create_on_moodle', '', 'post', 'string' );
        $moodle_role = JRequest::getVar( 'roles', array(), 'post', 'int' );

        $data->id = $id;
        if ($create_on_moodle == 'on')
            $data->create_on_moodle = 1;
        else
            $data->create_on_moodle = 0;
        $data->moodle_role = $moodle_role;

        JoomdleHelperProfiletypes::save_profiletype ($data);

    //  $this->setRedirect( 'index.php?option=com_joomdle&view=customprofiletypes&task=edit&profiletype_id='.$id );
        $this->setRedirect( 'index.php?option=com_joomdle&view=customprofiletypes' );
    }

  function back ()
  {
        $this->setRedirect( 'index.php?option=com_joomdle' );
  }

	function sync_to_kunena ()
	{
			JRequest::checkToken() or jexit( 'Invalid Token' );

			$cid = JRequest::getVar( 'cid', array(), 'post', 'array' );
			JArrayHelper::toInteger($cid);

			if (count( $cid ) < 1) {
					JError::raiseError(500, JText::_( 'Select an item to sync' ) );
			}
			JoomdleHelperForum::sync_forums ($cid);

			$this->setRedirect( 'index.php?option=com_joomdle&view=forums' );
	}

}
?>
