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

<?php
	if($helper->getRows('data.title') >= $helper->getRows('data.description')) {
		$count = $helper->getRows('data.title');
	} else {
		$count = $helper->getRows('data.description');
	}
?>

<div class="acm-slideshow acm-owl">
	<div id="acm-slideshow-<?php echo $module->id; ?>">
		<div class="owl-carousel owl-theme">
				<?php for ($i=0; $i<$count; $i++) : ?>
				<div class="item">
          <?php if($helper->get('data.image', $i)): ?>
            <img src="<?php echo $helper->get('data.image', $i); ?>" class="slider-img" alt="<?php echo $helper->get('data.title', $i) ?>">
          <?php endif; ?>
          <div class="slider-content"><div class="table-cell">
            <?php if($helper->get('data.title', $i)): ?>
              <h1 class="item-title">
              <?php if($helper->get('data.slideshow-link', $i)): ?>
                <a href="<?php echo $helper->get('data.slideshow-link', $i); ?>" title="<?php echo $helper->get('data.title', $i) ?>">
              <?php endif; ?>
                <?php echo $helper->get('data.title', $i) ?>
              <?php if($helper->get('data.slideshow-link', $i)): ?>
                </a>
              <?php endif; ?>
              </h1>
            <?php endif; ?>
            
  					<?php if($helper->get('data.description', $i)): ?>
  						<p class="item-desc"><?php echo $helper->get('data.description', $i) ?></p>
  					<?php endif; ?>
          </div></div>
				</div>
			 	<?php endfor ;?>
		</div>
	</div>
</div>

<script>
(function($){
  jQuery(document).ready(function($) {
    $("#acm-slideshow-<?php echo $module->id; ?> .owl-carousel").owlCarousel({
      items: 1,
      singleItem : true,
      itemsScaleUp : true,
      navigation : false,
      navigationText : ["<i class='fa fa-angle-left'></i>", "<i class='fa fa-angle-right'></i>"],
      pagination: true,
      merge: false,
      mergeFit: true,
      slideBy: 1,
      autoPlay: true
    });
  });
})(jQuery);
</script>