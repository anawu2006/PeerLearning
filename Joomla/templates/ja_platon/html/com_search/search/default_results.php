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
$db = JFactory::getDbo();
$idArr = array();
foreach ($this->results as $item) {
	if ($item->slug) {
		$id = preg_replace("/[^0-9]/","",$item->slug);
		$idArr[] = $id;
	}
}
$imgArr = array();
if ($idArr != false) {
	$query = 'SELECT id, images FROM #__content WHERE id IN ('.implode(',', $idArr).')';
	$db->setQuery($query);
	$results = $db->loadObjectList();
	foreach ($results AS $res) {
		$imagesObj = json_decode($res->images);
		if (trim($imagesObj->image_intro) != '')
			$imgArr[$res->id] = $imagesObj->image_intro;
	}
}
?>

<div class="search-results<?php echo $this->pageclass_sfx; ?>">
	<?php foreach ($this->results as $result) : ?>
		<div class="result-item clearfix">
			<?php $id = preg_replace("/[^0-9]/","",$result->slug);if (isset($imgArr[$id])) : ?>
				<div class="img-intro">
					<img class="img-responsive" src="<?php echo $imgArr[$id]; ?>" />
				</div>
			<?php endif;?>
			<h3 class="result-title">
				<?php if ($result->href) : ?>
					<a href="<?php echo JRoute::_($result->href); ?>"
						<?php if ($result->browsernav == 1) : ?>
							target="_blank"
						<?php endif; ?>>
						<?php echo $this->escape($result->title); ?>
					</a>
				<?php else: ?>
					<?php echo $this->escape($result->title); ?>
				<?php endif; ?>
			</h3>
			<?php if ($result->section) : ?>
				<div class="result-category">
					<span class="small<?php echo $this->pageclass_sfx; ?>">
						(<?php echo $this->escape($result->section); ?>)
					</span>
				</div>
			<?php endif; ?>
			<p class="result-text">
				<?php echo $result->text; ?>
			</p>
			<?php if ($this->params->get('show_date') && !empty($result->created)) : ?>
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
