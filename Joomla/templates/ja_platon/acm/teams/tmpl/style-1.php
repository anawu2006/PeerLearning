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
 
  $count = $helper->getRows('data.member-name');
?>

<div class="acm-teams">
	<div class="style-1 team-items">
		<?php
      for ($i=0; $i < $count; $i++) :
    ?>
		<div class="item col-sm-12">
      <div class="item-inner">
        
        <?php if($helper->get('data.member-image',$i)):?>
        <div class="member-image">
          <img src="<?php echo $helper->get('data.member-image', $i); ?>" alt="<?php echo $helper->get('member-name', $i); ?>" />
        </div>
        <?php endif; ?>
    
	      <h4>
          <span class="member-title"><?php echo $helper->get('data.member-name', $i); ?></span>
          <span class="member-position"><?php echo $helper->get('data.member-position', $i); ?></span>
        </h4>
        
        <div class="member-info">
          <p><?php echo $helper->get('data.member-desc', $i); ?></p>
        </div>
        
      </div>
		</div>
		<?php endfor; ?>
	</div>
</div>


<script>
(function($){
  $(document).ready(function() {
    $(".acm-teams .team-items").owlCarousel({
      items: 1,
      navigation : true,
      navigationText : ["<i class='fa fa-long-arrow-left'></i>", "<i class='fa fa-long-arrow-right'></i>"],
      pagination: false,
      slideBy: 1,
      itemsScaleUp : true,
      singleItem : true
    });
  });
})(jQuery);
</script>