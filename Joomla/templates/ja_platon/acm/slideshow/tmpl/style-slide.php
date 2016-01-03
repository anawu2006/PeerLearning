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
	$count = $helper->getRows('data.image');
?>

<div id="acm-slideshow-<?php echo $module->id; ?>" class="acm-slideshow acm-slick">
	<?php for ($i=0; $i<$count; $i++) : ?>
	<div class="item">
    <?php if($helper->get('data.image', $i)): ?>
      <img src="<?php echo $helper->get('data.image', $i); ?>" class="slider-img" alt="<?php echo $helper->get('data.title', $i) ?>">
    <?php endif; ?>
    <div class="slider-content">
      <?php if($helper->get('data.title', $i)): ?>
        <h1 class="item-title"><?php echo $helper->get('data.title', $i) ?></h1>
      <?php endif; ?>
    </div>
	</div>
 	<?php endfor ;?>
</div>

<script>
(function($){
  $(document).ready(function() {
    $("html[dir='ltr'] #acm-slideshow-<?php echo $module->id; ?>.acm-slick").slick({
      centerMode: true,
      centerPadding: '0',
      slidesToShow: 1,
      adaptiveHeight: true,
      responsive: [
        {
          breakpoint: 1200,
          settings: {
            slidesToShow: 1
          }
        },
        {
          breakpoint: 992,
          settings: {
            slidesToShow: 1
          }
        },
        {
          breakpoint: 768,
          settings: {
            slidesToShow: 1
          }
        },
        {
          breakpoint: 480,
          settings: {
            slidesToShow: 1
          }
        }
      ]
    });
  });
})(jQuery);

(function($){
  $(document).ready(function() {
    $("html[dir='rtl'] #acm-slideshow-<?php echo $module->id; ?>.acm-slick").slick({
      centerMode: true,
      centerPadding: '0',
      slidesToShow: 1,
      adaptiveHeight: true,
	  rtl: true,
      responsive: [
        {
          breakpoint: 1200,
          settings: {
            slidesToShow: 1
          }
        },
        {
          breakpoint: 992,
          settings: {
            slidesToShow: 1
          }
        },
        {
          breakpoint: 768,
          settings: {
            slidesToShow: 1
          }
        },
        {
          breakpoint: 480,
          settings: {
            slidesToShow: 1
          }
        }
      ]
    });
  });
})(jQuery);
</script>