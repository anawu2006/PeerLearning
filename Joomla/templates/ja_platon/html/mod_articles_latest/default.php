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
?>
<ul class="latestnews<?php echo $moduleclass_sfx; ?>">
<?php foreach ($list as $item) : ?>
	<li itemscope itemtype="http://schema.org/Article">
    <?php $images = json_decode($item->images); ?>
    <?php if($images->image_intro): ?>
      <a class="item-image" href="<?php echo $item->link; ?>" itemprop="thumbnailUrl"><img src="<?php echo $images->image_intro ; ?>" alt="<?php echo $item->title; ?>" /></a>
    <?php endif; ?>
    <div class="item-content">
  		<a class="item-title" href="<?php echo $item->link; ?>" itemprop="url">
  			<span itemprop="name">
  				<?php echo $item->title; ?>
  			</span>
  		</a>
      <time class="<?php if($images->image_intro) echo 'has-image'; ?>" datetime="<?php echo JHtml::_('date', $item->modified, 'c'); ?>" itemprop="dateModified">
          <?php echo JHtml::_('date', $item->modified, JText::_('DATE_FORMAT_LC3')); ?>
      </time>
    </div>
	</li>
<?php endforeach; ?>
</ul>
