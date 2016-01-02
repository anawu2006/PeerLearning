<?php defined('_JEXEC') or die('Restricted access'); ?>

<div class="joomdle-newslist<?php echo $this->pageclass_sfx;?>">
    <?php if ($this->params->get('show_page_heading', 1)) : ?>
    <h1>
    <?php echo $this->escape($this->params->get('page_heading')); ?>
    </h1>
    <?php endif; ?>

<?php

$itemid = JoomdleHelperContent::getMenuItem();

/* Para cada curso, mostramos todos las noticias */
if (is_array ($this->my_news))
foreach ($this->my_news as $id => $curso) :
    if ((is_array($curso['news'])) && (count($curso['news']))) :
    ?>
        <div class="joomdle_course_events">
        <h4>
                <?php echo $curso['fullname']; ?>
        </h4>
		<br>
    <?php
        foreach ($curso['news'] as  $news_item) : ?>
			<div class="joomdle_news_list_item">
				<div class="joomdle_item_title joomdle_news_list_item_date">
                    <?php echo JHTML::_('date', $news_item['timemodified'] , JText::_('DATE_FORMAT_LC2')); ?>
				</div>
				<div class="joomdle_item_content joomdle_news_list_item_name">
                    <?php
						$link = 'index.php?option=com_joomdle&view=newsitem&course_id='.$curso['remoteid'].'&id='.$news_item['discussion'].'&Itemid='.$itemid;
						echo "<a href=\"$link\">".$news_item['subject']."</a>";
                    ?>
				</div>
			</div>
        <?php endforeach; ?>
		</div>
    <?php endif; ?>
<?php endforeach; ?>
</div>
