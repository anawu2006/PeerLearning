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

$catids = $params->get('catid');
$numberItem = $params->get('count');
if(isset($catids) && $catids['0'] != ''){
	$catid = $catids[0];	
	$jacategoriesModel = JCategories::getInstance('content');
	$jacategory = $jacategoriesModel->get($catid);
}

?>
<div class="articles-list normal">

	<div class="article-items clearfix">
		<?php $count=1; foreach ($list as $item) : ?>
		<?php 
			$extrafields = new JRegistry($item->attribs); 
		?>
		<div class="article-item" itemscope itemtype="http://schema.org/Article">
				<?php $images = json_decode($item->images); ?>
				<?php if($images->image_intro) : ?>
				<div class="col-md-6 hidden-xs hidden-sm">
					<a class="<?php echo $item->active; ?>" href="<?php echo $item->link; ?>"><img src="<?php echo $images->image_intro ; ?>" alt="<?php echo $item->title; ?>" /></a>
				</div>
				<?php endif; ?>
				<div class="col-md-6 article-content">
					<?php if ($item->displayCategoryTitle) : ?>
						<span class="mod-articles-category-category">
							<?php echo $item->displayCategoryTitle; ?>
						</span>
					<?php endif; ?>
					
					<?php if ($params->get('link_titles') == 1) : ?>
						<h3 class="article-title">
							<a class="<?php echo $item->active; ?>" href="<?php echo $item->link; ?>">
								<?php echo $item->title; ?>
							</a>
						</h3>
					<?php else : ?>
						<?php echo $item->title; ?>
					<?php endif; ?>

					<?php if ($params->get('show_introtext')) : ?>
						<p class="mod-articles-category-introtext">
							<?php echo $item->displayIntrotext; ?>
						</p>
					<?php endif; ?>

					<div class="article-footer">
					<?php if ($item->displayDate) : ?>
						<span class="mod-articles-category-date">
							<?php echo $item->displayDate; ?>
						</span>
					<?php endif; ?>

					<?php if ($params->get('show_readmore')) : ?>
						<p class="mod-articles-category-readmore">
							<a class="mod-articles-category-title <?php echo $item->active; ?>" href="<?php echo $item->link; ?>">
								<?php if ($item->params->get('access-view') == false) : ?>
									<?php echo JText::_('MOD_ARTICLES_CATEGORY_REGISTER_TO_READ_MORE'); ?>
								<?php elseif ($readmore = $item->alternative_readmore) : ?>
									<?php echo $readmore; ?>
									<?php echo JHtml::_('string.truncate', $item->title, $params->get('readmore_limit')); ?>
										<?php if ($params->get('show_readmore_title', 0) != 0) : ?>
											<?php echo JHtml::_('string.truncate', ($this->item->title), $params->get('readmore_limit')); ?>
										<?php endif; ?>
								<?php elseif ($params->get('show_readmore_title', 0) == 0) : ?>
									<?php echo JText::sprintf('MOD_ARTICLES_CATEGORY_READ_MORE_TITLE'); ?>
								<?php else : ?>
									<?php echo JText::_('MOD_ARTICLES_CATEGORY_READ_MORE'); ?>
									<?php echo JHtml::_('string.truncate', ($item->title), $params->get('readmore_limit')); ?>
								<?php endif; ?>
							</a>
						</p>
					<?php endif; ?>
					</div>
				</div>
		</div>
		<?php break; endforeach; ?>
	</div>
</div>