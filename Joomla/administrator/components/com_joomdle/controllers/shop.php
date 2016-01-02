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


// Added to deal with Hikashop, which still uses DS
if(!defined('DS'))
    define('DS', DIRECTORY_SEPARATOR);


/**
 * Joomdle Controller
 *
 * @package Joomla
 * @subpackage Joomdle
 */
class JoomdleControllerShop extends JControllerAdmin {

	function publish()
	{
		JRequest::checkToken() or jexit( 'Invalid Token' ); 

		$cid = JRequest::getVar( 'cid', array(), 'post', 'array' );

		if (count( $cid ) < 1) {
				JError::raiseError(500, JText::_( 'Select an item to publish' ) );
		}
	//	JoomdleHelperShop::sell_courses ($cid);
		JoomdleHelperShop::publish_courses ($cid);

		$this->setRedirect( 'index.php?option=com_joomdle&view=shop' );

	}

	function unpublish()
	{
		JRequest::checkToken() or jexit( 'Invalid Token' );

		$cid = JRequest::getVar( 'cid', array(), 'post', 'array' );

		if (count( $cid ) < 1) {
				JError::raiseError(500, JText::_( 'Select an item to unpublish' ) );
		}
		JoomdleHelperShop::dont_sell_courses ($cid);

		$this->setRedirect( 'index.php?option=com_joomdle&view=shop' );

	}

	function reload ()
	{
		JRequest::checkToken() or jexit( 'Invalid Token' );

		$cid = JRequest::getVar( 'cid', array(), 'post', 'array' );
		JArrayHelper::toInteger($cid);

		if (count( $cid ) < 1) {
				JError::raiseError(500, JText::_( 'Select an item to unpublish' ) );
		}
		$real_cid = array();
		foreach ($cid as $id)
		{
			// Skip bundles 
			if ($id)
				$real_cid[] = $id;
		}
		JoomdleHelperShop::reload_courses ($real_cid);

		$this->setRedirect( 'index.php?option=com_joomdle&view=shop' );
	}

	function delete_courses_from_shop ()
	{
		JRequest::checkToken() or jexit( 'Invalid Token' );

		$cid = JRequest::getVar( 'cid', array(), 'post', 'array' );

		if (count( $cid ) < 1) {
				JError::raiseError(500, JText::_( 'Select an item to delete' ) );
		}
		JoomdleHelperShop::delete_courses ($cid);

		$this->setRedirect( 'index.php?option=com_joomdle&view=shop' );
	}

    function new_bundle ()
    {
        $this->setRedirect( 'index.php?option=com_joomdle&view=shop&task=edit_bundle' );
    }

    function save_bundle ()
    {
        $bundle['courses'] = JRequest::getVar('courses', array(), 'post', 'array');
        $bundle['name'] = JRequest::getVar('name');
        $bundle['description'] = JRequest::getVar('description');
        $bundle['cost'] = JRequest::getVar('cost');
        $bundle['currency'] = JRequest::getVar('currency');
        $bundle_id = JRequest::getVar('bundle_id');
        if ($bundle_id)
            $bundle['id'] = $bundle_id;

        JoomdleHelperShop::create_bundle ($bundle);

        $this->setRedirect( 'index.php?option=com_joomdle&view=shop' );
    }

    function cancel_edit_shop ()
    {
            $this->setRedirect( 'index.php?option=com_joomdle&view=shop' );
    }

}
?>
