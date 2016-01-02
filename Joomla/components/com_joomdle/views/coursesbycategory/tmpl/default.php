<?php defined('_JEXEC') or die('Restricted access'); ?>

<div class="joomdle-courselist<?php echo $this->pageclass_sfx;?>">
    <?php if ($this->params->get('show_page_heading', 1)) : ?>
    <h1>
    <?php echo $this->escape($this->params->get('page_heading')); ?>
    </h1>
    <?php endif; ?>

<?php
$itemid = JoomdleHelperContent::getMenuItem();
$user = JFactory::getUser();
$username = $user->username;
if (is_array($this->categories))
foreach ($this->categories as  $cat) :
    $cursos = JoomdleHelperContent::getCourseCategory ($cat['id'], '');
    $cat_id = $cat['id'];
	$categories = JoomdleHelperContent::getCourseCategories ($cat_id);

    if (((!is_array ($cursos)) || (!count($cursos))) && ((!is_array ($categories)) || (!count ($categories))))
        continue;
    ?>
    <h4>
        <?php echo $cat['name']; ?>
    </h4>

    <?php
    foreach ($cursos as  $curso) : ?>
        <div class="joomdle_course_list_item">
            <div class="joomdle_item_title joomdle_course_list_item_title">
                <?php $url = JRoute::_("index.php?option=com_joomdle&&view=detail&cat_id=$cat_id:".JFilterOutput::stringURLSafe($cat['name'])."&course_id=".$curso['remoteid'].":".JFilterOutput::stringURLSafe($curso['fullname'])."&Itemid=$itemid"); ?>
                <?php  echo "<a href=\"$url\">".$curso['fullname']."</a>"; ?>
            </div>
            <?php if ($curso['summary']) : ?>
            <div class="joomdle_item_content joomdle_course_list_item_description">
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
            <?php endif; ?>
        </div>
    <?php endforeach; //courses ?>
    <?php
        $parent_name = $cat['name'];
        $odd= 0;
        if (is_array($categories)) :
        foreach ($categories as  $cat) :

            $cursos = JoomdleHelperContent::getCourseCategory ($cat['id'], $username);
            $cat_id = $cat['id'];

            if ((!is_array ($cursos)) || (!count ($cursos)))
                continue;
            ?>
            <h4>
                <?php echo $parent_name.'>'.$cat['name']; ?>
            </h4>
            <?php
            foreach ($cursos as  $curso) : ?>
            <div class="joomdle_course_list_item">
                <div class="joomdle_item_title joomdle_course_list_item_title">
                        <?php $url = JRoute::_("index.php?option=com_joomdle&&view=detail&cat_id=$cat_id:".JFilterOutput::stringURLSafe($cat['name'])."&course_id=".$curso['remoteid'].":".JFilterOutput::stringURLSafe($curso['fullname'])."&Itemid=$itemid"); ?>
                        <?php  echo "<a href=\"$url\">".$curso['fullname']."</a>"; ?>
                </div>
                <?php if ($curso['summary']) : ?>
                <div class="joomdle_item_content joomdle_course_list_item_description">
                        <?php echo JoomdleHelperSystem::fix_text_format($curso['summary']); ?>
                </div>
                <?php endif; ?>
            </div>
            <?php endforeach; //cursos ?>
        <?php endforeach; //cats ?>
    <?php endif; ?>
<?php endforeach; //cats ?>

</div>
