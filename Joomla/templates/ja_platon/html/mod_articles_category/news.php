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

// no direct access
defined('_JEXEC') or die;

?>
<div class="section-inner <?php echo $params->get('moduleclass_sfx'); ?>">
	<div class="news">
	  <ul class="item-list-view">
	    <?php foreach ($list as $item) : ?>
	    <li>
	      <div class="item-inner <?php if (isset($item->images)) echo 'has-image'; ?>">
	        <!-- Item image -->
	        <?php  
	        $images = "";
	        if (isset($item->images)) {
	          $images = json_decode($item->images);
	        }
	        $imgexists = (isset($images->image_intro) and !empty($images->image_intro)) || (isset($images->image_fulltext) and !empty($images->image_fulltext));
	        
	        if ($imgexists) {			
	        $images->image_intro = $images->image_intro?$images->image_intro:$images->image_fulltext;
	        ?>
	
	        <a class="item-image" href="<?php echo $item->link; ?>">
	          <img src="<?php echo htmlspecialchars($images->image_intro); ?>" alt="<?php echo $item->title; ?>" />
	        </a>
	        <?php } ?>
	        <!-- // Item image -->
	      
	        <h3 class="item-title">
	        <?php if ($params->get('link_titles') == 1) : ?>
	        <a class="mod-articles-category-title <?php echo $item->active; ?>" href="<?php echo $item->link; ?>">
	        <?php echo $item->title; ?>
	            <?php if ($item->displayHits) :?>
	          <span class="mod-articles-category-hits">
	                (<?php echo $item->displayHits; ?>)  </span>
	            <?php endif; ?></a>
	            <?php else :?>
	            <?php echo $item->title; ?>
	              <?php if ($item->displayHits) :?>
	          <span class="mod-articles-category-hits">
	                (<?php echo $item->displayHits; ?>)  </span>
	            <?php endif; ?></a>
	                <?php endif; ?>
	          </h3>
	
	        <div class="item-meta">
	          <?php if ($item->displayDate) : ?>
	            <span class="mod-articles-category-date"><?php echo $item->displayDate; ?></span>
	          <?php endif; ?>

	          <?php if ($params->get('show_author')) :?>
	            <span class="mod-articles-category-writtenby">
	            <?php echo $item->displayAuthorName; ?>
	            </span>
	          <?php endif;?>
	          
	          <?php if ($item->displayCategoryTitle) :?>
	            <span class="mod-articles-category-category">
	              <?php echo $item->displayCategoryTitle; ?>
	            </span>
	          <?php endif; ?>
	          
	        </div>
	        
	        <?php if ($params->get('show_introtext')) :?>
	          <p class="mod-articles-category-introtext">
	          <?php echo $item->displayIntrotext; ?>
	          <?php //echo $item->introtext; ?>
	          </p>
	        <?php endif; ?>

	        <?php if ($params->get('show_readmore')) :?>
	          <p class="mod-articles-category-readmore">
	            <a class="mod-articles-category-title <?php echo $item->active; ?>" href="<?php echo $item->link; ?>">
	                <?php if ($item->params->get('access-view')== FALSE) :
	                echo JText::_('MOD_ARTICLES_CATEGORY_REGISTER_TO_READ_MORE');
	              elseif ($readmore = $item->alternative_readmore) :
	                echo $readmore;
	                echo JHtml::_('string.truncate', $item->title, $params->get('readmore_limit'));
	              elseif ($params->get('show_readmore_title', 0) == 0) :
	                echo JText::sprintf('MOD_ARTICLES_CATEGORY_READ_MORE_TITLE');
	              else :
	                echo JText::_('MOD_ARTICLES_CATEGORY_READ_MORE');
	                echo JHtml::_('string.truncate', $item->title, $params->get('readmore_limit'));
	              endif; ?>
	              </a>
	          </p>
	        <?php endif; ?>
	
	      </div>
	    </li>
	    <?php endforeach; ?>
	  </ul>
	</div>
</div>