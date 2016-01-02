<?php
/**
* @version		
* @package		Joomdle
* @copyright	Copyright (C) 2008 - 2010 Antonio Duran Terres
* @license		GNU/GPL, see LICENSE.php
* Joomla! is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/

// no direct access
defined('_JEXEC') or die('Restricted access');
require_once(JPATH_SITE.'/components/com_joomdle/helpers/content.php');

// Include the whosonline functions only once
require_once (dirname(__FILE__).'/helper.php');

$linkto = $params->get( 'linkto' , 'moodle');

$comp_params = JComponentHelper::getParams( 'com_joomdle' );

$moodle_xmlrpc_server_url = $comp_params->get( 'MOODLE_URL' ).'/mnet/xmlrpc/server.php';
$moodle_auth_land_url = $comp_params->get( 'MOODLE_URL' ).'/auth/joomdle/land.php';
$linkstarget = $comp_params->get( 'linkstarget' );
$default_itemid = $comp_params->get( 'default_itemid' );
$joomdle_itemid = $comp_params->get( 'joomdle_itemid' );
$courseview_itemid = $comp_params->get( 'courseview_itemid' );
$moduleclass_sfx = htmlspecialchars($params->get('moduleclass_sfx'));
$show_unenrol_link = $params->get( 'show_unenrol_link' );

$user = JFactory::getUser();
$username = $user->get('username');

$session                = JFactory::getSession();
$token = md5 ($session->getId());

$cursos = JoomdleHelperContent::getMyCourses ();

if ($cursos)
    require(JModuleHelper::getLayoutPath('mod_joomdle_my_courses'));
else
{
    $nocourses_text = $params->get( 'nocourses_text' );
    echo $nocourses_text;
}
