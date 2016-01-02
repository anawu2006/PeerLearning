<?php defined('_JEXEC') or die('Restricted access'); ?>

<div class="joomdle-newslist<?php echo $this->pageclass_sfx;?>">
    <h1>
        <?php echo $this->course_info['fullname'] . ': '; ?>
        <?php echo JText::_('COM_JOOMDLE_COURSE_NEWS'); ?>
    </h1>

<?php
//$lang = JoomdleHelperContent::get_lang ();
$itemid = JoomdleHelperContent::getMenuItem();
//$linkstarget = $this->params->get( 'linkstarget' );

if (is_array ($this->news)) {
foreach ($this->news as  $news_item) : ?>
    <div class="joomdle_news_list_item">
        <div class="joomdle_item_title joomdle_news_list_item_date">
        <?php echo JHTML::_('date', $news_item['timemodified'] , JText::_('DATE_FORMAT_LC2')); ?>
        </div>
        <div class="joomdle_item_content joomdle_news_list_item_name">
        <?php

			$link = 'index.php?option=com_joomdle&view=newsitem&course_id='.$this->course_info['remoteid'].'&id='.$news_item['discussion'].'&Itemid='.$itemid;
			echo "<a href=\"$link\">".$news_item['subject']."</a>";

 /*           $link = $this->jump_url."&mtype=news&id=".$news_item['discussion'];

            if ($lang)
                $link .= "&lang=$lang";

            if ($linkstarget == "new")
                 $target = " target='_blank'";
             else $target = "";
                echo "<a $target href=\"$link\">".$news_item['subject']."</a>";
*/
        ?>
        </div>
    </div>
<?php endforeach;
} ?>

</div>
