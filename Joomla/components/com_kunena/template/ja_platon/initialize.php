<?php
/**
* Kunena Component
* @package Kunena.Template.JA_Platon
*
* @copyright (C) 2008 - 2015 Kunena Team. All rights reserved.
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
* @link http://www.kunena.org
**/
defined( '_JEXEC' ) or die();

$app = JFactory::getApplication();
$document = JFactory::getDocument();
$template = KunenaFactory::getTemplate();

// Template requires Mootools 1.2 framework
$template->loadMootools();

// We load mediaxboxadvanced library only if configuration setting allow it
if ( KunenaFactory::getConfig()->lightbox == 1 ) {
	$template->addStyleSheet ( 'css/mediaboxAdv.css');
	$template->addScript( 'js/mediaboxAdv.js' );
}

// New Kunena JS for default template
$template->addScript ( 'js/default.js' );

$skinner = $template->params->get('enableSkinner', 0);

if (is_file(JPATH_ROOT . "/templates/{$app->getTemplate()}/css/kunena.forum.css")) {
	// Load css from Joomla template
	CKunenaTools::addStyleSheet ( JUri::root(true). "/templates/{$app->getTemplate()}/css/kunena.forum.css" );
	if ($skinner && is_file(JPATH_ROOT. "/templates/{$app->getTemplate()}/css/kunena.skinner.css")){
		CKunenaTools::addStyleSheet ( JUri::root(true). "/templates/{$app->getTemplate()}/css/kunena.skinner.css" );
	} elseif (!$skinner && is_file(JPATH_ROOT. "/templates/{$app->getTemplate()}/css/kunena.default.css")) {
		CKunenaTools::addStyleSheet ( JUri::root(true). "/templates/{$app->getTemplate()}/css/kunena.default.css" );
	}
} else {
	$loadResponsiveCSS = $template->params->get('loadResponsiveCSS', 1);
	// Load css from default template
	$template->addStyleSheet ( 'css/kunena.forum.css' );
	if ($loadResponsiveCSS) $template->addStyleSheet ( 'css/kunena.responsive.css' );
	if ($skinner) {
		$template->addStyleSheet ( 'css/kunena.skinner.css' );
	} else {
		$template->addStyleSheet ( 'css/kunena.default.css' );
	}
}

$rtl = JFactory::getLanguage()->isRTL();

if ($rtl) {
	$template->addStyleSheet ( 'css/kunena.forum.rtl.css');
}

$cssurl = JUri::root(true) . '/components/com_kunena/template/ja_platon/css';
?>
<!--[if lte IE 7]>
<link rel="stylesheet" href="<?php echo $cssurl; ?>/kunena.forum.ie7.css" type="text/css" />
<![endif]-->
<?php
$mediaurl = JUri::root(true) . "/components/com_kunena/template/{$template->name}/media";

$profileIcons = $template->getFile("media/iconsets/profile/{$template->params->get('profileIconset', 'default')}/default.png", true);
$buttonIcons = $template->getFile("media/iconsets/buttons/{$template->params->get('buttonIconset', 'default')}/default.png", true);
$editorIcons = $template->getFile("media/iconsets/editor/{$template->params->get('editorIconset', 'default')}/default.png", true);