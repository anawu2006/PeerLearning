<?php defined('_JEXEC') or die('Restricted access'); ?>

<?php
$itemid = JoomdleHelperContent::getMenuItem();
?>

<div class="joomdle-coursecategories<?php echo $this->pageclass_sfx;?>">
    <?php if ($this->params->get('show_page_heading', 1)) : ?>
    <h1>
    <?php echo $this->escape($this->params->get('page_heading')); ?>
    </h1>
    <?php endif; ?>

    <?php
    if (is_array($this->categories))
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
