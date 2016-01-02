<?php defined('_JEXEC') or die('Restricted access'); ?>
<div id="joomdle">
    <h1>
                <?php echo $this->news_item[0]['subject']; ?>
    </h1>

<?php foreach ($this->news_item as $post) : ?>
    <div class="joomdle_news_item_item">
		 <div class="joomdle_item_title joomdle_news_item_item_subject">
		 <?php echo $post['subject']; ?>
        </div>
        <div class="joomdle_item_content joomdle_news_item_item_message">
        <?php
			echo $post['message'];
        ?>
        </div>
    </div>
<?php endforeach; ?>
</div>
