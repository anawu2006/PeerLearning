<?php
/**
 * ------------------------------------------------------------------------
 * JA Platon Template
 * ------------------------------------------------------------------------
 * Copyright (C) 2004-2011 J.O.O.M Solutions Co., Ltd. All Rights Reserved.
 * @license - Copyrighted Commercial Software
 * Author: J.O.O.M Solutions Co., Ltd
 * Websites:  http://www.joomlart.com -  http://www.joomlancers.com
 * This file may not be redistributed in whole or significant part.
 * ------------------------------------------------------------------------
 */

defined('JPATH_BASE') or die;

$item = $displayData['item'];
$params = $displayData['params'];
$title = $this->escape($item->parent_title);
?>
			<dd class="parent-category-name hasTooltip" title="<?php echo JText::sprintf('COM_CONTENT_PARENT', ''); ?>">
				<?php if ($params->get('link_parent_category') && !empty($item->parent_slug)) : ?>
					<?php echo JHtml::_('link', JRoute::_(ContentHelperRoute::getCategoryRoute($item->parent_slug)), '<span itemprop="genre">'.$title.'</span>'); ?>
				<?php else : ?>
					<span itemprop="genre"><?php echo $title ?></span>
				<?php endif; ?>
			</dd>