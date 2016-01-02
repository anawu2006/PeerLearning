<?php defined('_JEXEC') or die('Restricted access'); ?>

<?php
$show_topics_numbers = $this->params->get ('topics_show_numbers');
?>
<div class="joomdle-topiclist<?php echo $this->pageclass_sfx;?>">
	<h1>
                <?php echo $this->course_info['fullname'] . ': '; ?>
                <?php echo JText::_('COM_JOOMDLE_TOPICS'); ?>
    </h1>
<?php
if (is_array ($this->temas))
foreach ($this->temas as  $tema) : ?>
    <?php if (($tema['summary']) || ($tema['name'])) : ?>
    <div class="joomdle_course_topic">
        <?php if ($show_topics_numbers) : ?>
        <div class="joomdle_item_title joomdle_topic_number">
        <?php
                $title = '';
				if ($tema['name'])
					$title = $tema['name'];
				else
				{
					if ($tema['section'])
					{
						$title =  JText::_('COM_JOOMDLE_SECTION') . " ";
						$title .= $tema['section'] ;
					}
					else  $title =  JText::_('COM_JOOMDLE_INTRO');
				}
                echo $title;
        ?>
        </div>
        <?php endif; ?>
        <div class="joomdle_item_content joomdle_topic_name">
        <?php       echo $tema['summary'];       ?>
        </div>
    </div>
    <?php endif; ?>
<?php endforeach; ?>

<?php if ($this->params->get('show_back_links')) : ?>
    <div>
    <P align="center">
    <a href="javascript: history.go(-1)"><?php echo JText::_('COM_JOOMDLE_BACK'); ?></a>
    </P>
    </div>
<?php endif; ?>

</div>
