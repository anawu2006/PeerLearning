<?php
/**
 * @package     Joomla.Site
 * @subpackage  mod_menu
 *
 * @copyright   Copyright (C) 2005 - 2014 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

// Include the syndicate functions only once
require_once __DIR__ . '/helper.php';
require_once __DIR__ . '/chrome.php';

$helper = new ModJAACMHelper ($params);


$class_sfx	= htmlspecialchars($params->get('class_sfx'));

$helper->addAssets();

$layout_path = $helper->getLayout();
$buffer = '';
if ($layout_path)
{
	ob_start();
	include $layout_path;
	$buffer = ob_get_contents();
	ob_end_clean();
}
if ($params->get('parse-jdoc', 0)) {
	$buffer = $helper->renderJDoc($module->id, $buffer);
}

echo $buffer;