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
?>

<div class="acm-features style-1 <?php echo $helper->get('features-style'); ?>">
	<?php $count = $helper->getRows('data.title'); ?>
	<?php $column = $helper->get('columns'); ?>
	<?php 
		for ($i=0; $i<$count; $i++) : 
		if ($i%$column==0) echo '<div class="row equal-height">'; 
	?>
	
		<div class="features-item col <?php echo $helper->get('data.features-style',$i); ?> <?php if($i==0) echo 'features-item-first'; ?> col-xs-12 col-sm-<?php echo 12/$column ?> col-lg-<?php echo 12/$column ?>">
			<?php if($helper->get('data.img-features', $i)) : ?>
				<div class="feature-image">
					<img src="<?php echo $helper->get('data.img-features', $i) ; ?>" alt="<?php echo $helper->get('data.title', $i) ?>" />
				</div>
			<?php endif ; ?>
			
			<div class="feature-content">
				<?php if($helper->get('data.title', $i)) : ?>
					<h3 class="feature-title">
						<?php if($helper->get('data.link', $i)) : ?><a href="<?php echo $helper->get('data.link', $i) ?>" title="<?php echo $helper->get('data.title', $i) ?>"><?php endif; ?>
						<?php echo $helper->get('data.title', $i) ?>
						<?php if($helper->get('data.link', $i)) : ?></a><?php endif; ?>
					</h3>
				<?php endif ; ?>
				
				<?php if($helper->get('data.description', $i)) : ?>
					<p class="feature-desc"><?php echo $helper->get('data.description', $i) ?></p>
				<?php endif ; ?>

				<?php if($helper->get('data.button-value', $i)) : ?>
					<a class="readmore" href="<?php echo $helper->get('data.link', $i); ?>"><?php echo $helper->get('data.button-value', $i); ?></a>
				<?php endif ; ?>
			</div>
		</div>
		<?php if ( ($i%$column==($column-1)) || $i==($count-1) )  echo '</div>'; ?>
	<?php endfor ?>
	<?php if($helper->get('features-link')) : ?>
	<div class="feature-footer">
		<a class="btn btn-border" href="<?php echo $helper->get('features-link') ?>">
			<?php echo $helper->get('features-button-value') ?>
		</a>
	</div>
	<?php endif; ?>
</div>