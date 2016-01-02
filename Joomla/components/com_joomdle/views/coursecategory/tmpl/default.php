<?php defined('_JEXEC') or die('Restricted access'); ?>

<?php
$itemid = JoomdleHelperContent::getMenuItem();
$free_courses_button = $this->params->get( 'free_courses_button' );
$paid_courses_button = $this->params->get( 'paid_courses_button' );
$show_buttons = $this->params->get( 'coursecategory_show_buttons' );

?>
<div class="joomdle-coursecategory<?php echo $this->pageclass_sfx;?>">
    <h1>
        <?php echo $this->cat_name; ?>
    </h1>

    <?php if ($this->params->get ('coursecategory_show_category_info')) : ?>
        <?php if (is_array ($this->cursos) && (count ($this->cursos))) : ?>
        <div class="joomdle_list_description">
            <?php echo $this->cursos[0]['cat_description'];?>
        </div>
        <?php endif; ?>
    <?php endif; ?>

    <div class="joomdle_categories">
        <?php if ((is_array($this->categories)) && (count ($this->categories)) && (is_array($this->cursos)) && (count ($this->cursos))) : ?>
                <h4>
                <?php
                    echo JText::_('COM_JOOMDLE_COURSE_CATEGORIES_IN');
                    echo " ".$this->cat_name;
                ?> 
                </h4>
        <?php endif;?>

    <?php
    if (is_array ($this->categories))
    foreach ($this->categories as  $cat) : ?>
            <div class="joomdle_category_list_item">
                <div class="joomdle_item_title joomdle_category_list_item_title">
                    <?php $url = JRoute::_("index.php?option=com_joomdle&view=coursecategory&cat_id=".$cat['id'].':'.JFilterOutput::stringURLSafe($cat['name'])."&Itemid=$itemid"); ?>
                    <?php  echo "<a href=\"$url\">".$cat['name']."</a>"; ?>
                </div>
                <?php if  ($cat['description']) : ?>
                <div class="joomdle_item_content joomdle_course_list_item_description">
                    <?php echo JoomdleHelperSystem::fix_text_format($cat['description']); ?>
                </div>
                <?php endif; ?>
            </div>
    <?php endforeach; ?>
    </div>

    <div class="joomdle_courses">
    <?php if ((is_array($this->categories)) && (count ($this->categories)) && (is_array($this->cursos)) && (count ($this->cursos))) : ?>
    <h4>
                    <?php echo JText::_('COM_JOOMDLE_COURSES_IN');
            echo " ".$this->cat_name;
            ?> 
    </h4>
    <?php endif; ?>

    <?php
    if (is_array ($this->cursos))
    foreach ($this->cursos as  $curso) : ?>
        <div class="joomdle_course_list_item">
            <div class="joomdle_item_title joomdle_course_list_item_title">
                <?php $url = JRoute::_("index.php?option=com_joomdle&view=detail&cat_id=".$curso['cat_id'].":".JFilterOutput::stringURLSafe($curso['cat_name'])."&course_id=".$curso['remoteid'].':'.JFilterOutput::stringURLSafe($curso['fullname'])."&Itemid=$itemid"); ?>
                <?php  echo "<a href=\"$url\">".$curso['fullname']."</a><br>"; ?>
            </diV>
            <?php if ($curso['summary']) : ?>
            <div class="joomdle_item_content joomdle_course_list_item_description">
				<div class="joomdle_course_description">
				<?php
					if (count ($curso['summary_files']))
					{
						foreach ($curso['summary_files'] as $file) :
						?>
							<img hspace="5" vspace="5" align="left" src="<?php echo $file['url']; ?>">
						<?php
						endforeach;
					}
				?>

                <?php echo JoomdleHelperSystem::fix_text_format($curso['summary']); ?>
                </div>
				  <?php if ($show_buttons) : ?>
                <div class="joomdle_course_buttons">
                    <?php echo JoomdleHelperSystem::actionbutton ( $curso, $free_courses_button, $paid_courses_button) ?>
                </div>
                <?php endif; ?>
            </div>
            <?php endif; ?>
        </div>
    <?php endforeach; ?>
    </div>
</div>
