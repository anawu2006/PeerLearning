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

defined('_JEXEC') or die;
require_once(dirname(dirname(__FILE__)).'../../../helper.php');
JHtml::addIncludePath(JPATH_COMPONENT.'/helpers');
JHtml::addIncludePath(T3_PATH.'/html/com_content');
JHtml::addIncludePath(dirname(dirname(__FILE__)));
JHtml::_('behavior.caption');

$params = JComponentHelper::getParams('com_content');
$mainframe = JFactory::getApplication();
$jinput = $mainframe->input;
$jaordering = $jinput->get('ordering');
$jaorder='a.created DESC';
if ($jaordering == 'newest')
	$jaorder = 'a.created DESC';
else if ($jaordering == 'oldest')
	$jaorder = 'a.created ASC';
else if ($jaordering == 'popular')
	$jaorder = 'a.hits DESC';
else if ($jaordering == 'alpha')
	$jaorder = 'a.title DESC';
else if ($jaordering == 'category')
	$jaorder = 'a.catid ASC';
$this->results = JATemplateHelper::getArticleContent('', array('c.id!=24'), array($jaorder));

$numresult = JATemplateHelper::getArticleContentNumber('', array('c.id!=24'));

$limit = $mainframe->getUserStateFromRequest('global.list.limit', 'limit', $mainframe->getCfg('list_limit'), 'int');

$limitstart = $jinput->get('limitstart', 0);

// In case limit has been changed, adjust it
$limitstart = ($limit != 0 ? (floor($limitstart / $limit) * $limit) : 0);

$this->pagination = new JPagination($numresult, $limitstart, $limit);
?>

<div class="searchintro<?php echo $this->params->get('pageclass_sfx'); ?>">
	<p><?php echo JText::plural('COM_SEARCH_SEARCH_KEYWORD_N_RESULTS', '<span class="badge badge-info">' . $numresult . '</span>'); ?></p>
</div>

<div class="form-limit">
	<p class="counter">
		<?php echo $this->pagination->getPagesCounter(); ?>
	</p>
	<label for="limit">
		<?php echo JText::_('JGLOBAL_DISPLAY_NUM'); ?>
	</label>
	<?php echo $this->pagination->getLimitBox(); ?>
</div>
<div class="search-results<?php echo $this->pageclass_sfx; ?>">
	<?php foreach ($this->results AS $rk => $result) : ?>
		<?php
		$link='';
		$param  = $result->params;
		$images = json_decode($result->images);
		$image_intro = $images->image_intro;
		?>
		<div class="result-item clearfix">
			<?php if ($image_intro != '') : ?>
				<div class="img-intro">
					<img src="<?php echo $image_intro; ?>" />
				</div>
			<?php endif; ?>
			<h3 class="result-title">
				<?php 
				  if ($param->get('access-view')) :
					$link = JRoute::_(ContentHelperRoute::getArticleRoute($result->slug, $result->catid));
				  else :
					$menu      = JFactory::getApplication()->getMenu();
					$active    = $menu->getActive();
					$itemId    = $active->id;
					$link1     = JRoute::_('index.php?option=com_users&view=login&Itemid=' . $itemId);
					$returnURL = JRoute::_(ContentHelperRoute::getArticleRoute($result->slug, $result->catid));
					$link      = new JURI($link1);
					$link->setVar('return', base64_encode($returnURL));
				  endif;
				?>
				<a href="<?php echo $link; ?>" >
					<?php echo $this->escape($result->title); ?>
				</a>
			</h3>
			<?php if ($params->get('show_intro')) : ?>
				<p class="result-text">
					<?php echo $result->text; ?>
				</p>
			<?php endif; ?>
			
			<?php if ($params->get('show_create_date')) : ?>
			<p class="result-created<?php echo $this->pageclass_sfx; ?>">
				<?php echo JText::sprintf('JGLOBAL_CREATED_DATE_ON', '<strong>' . $result->created . '</strong>'); ?>
			</p>
			<?php endif; ?>
		</div>
	<?php endforeach; ?>
</div>

<div class="pagination-wrap">
	<?php echo $this->pagination->getPagesLinks(); ?>
</div>
