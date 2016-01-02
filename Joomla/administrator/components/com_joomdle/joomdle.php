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

//JHTML::_('stylesheet', 'joomdle.css', 'administrator/components/com_joomdle/assets/css/');
/*
 * Define constants for all pages
 */
define( 'COM_JOOMDLE_DIR', 'images'.'/'.'joomdle'.'/' );
define( 'COM_JOOMDLE_BASE', JPATH_ROOT.'/'.COM_JOOMDLE_DIR );
define( 'COM_JOOMDLE_BASEURL', JURI::root().str_replace( '/', '/', COM_JOOMDLE_DIR ));

// Require the base controller
require_once JPATH_COMPONENT.'/'.'controller.php';

// Require the base controller
require_once JPATH_COMPONENT.'/'.'helpers'.'/'.'system.php';

JoomdleHelperSystem::check_system ();

$controller = JControllerLegacy::getInstance('Joomdle');
$controller->execute(JFactory::getApplication()->input->get('task'));
$controller->redirect();

?>
