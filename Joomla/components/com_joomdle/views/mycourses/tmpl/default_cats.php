<?php defined('_JEXEC') or die('Restricted access'); ?>
<?php
$linkto = $this->params->get( 'mycourses_linkto' );
$default_itemid = $this->params->get( 'default_itemid' );
$joomdle_itemid = $this->params->get( 'joomdle_itemid' );
$courseview_itemid = $this->params->get( 'courseview_itemid' );
if ($linkto == 'moodle')
{
    if ($default_itemid)
        $itemid = $default_itemid;
    else
        $itemid = JoomdleHelperContent::getMenuItem();
}
else if ($linkto == 'detail')
{
		// Get the best menu item id we can get
        $itemid = JoomdleHelperContent::getMenuItem();

        if ($joomdle_itemid)
            $itemid = $joomdle_itemid;
}
else // Link to course view
{
		// Get the best menu item id we can get
        $itemid = JoomdleHelperContent::getMenuItem();

        if ($joomdle_itemid)
            $itemid = $joomdle_itemid;
        if ($courseview_itemid)
            $itemid = $courseview_itemid;
}

$linkstarget = $this->params->get( 'linkstarget' );
if ($linkstarget == "new")
	 $target = " target='_blank'";
else $target = "";

$show_unenrol_link = $this->params->get( 'show_unenrol_link' );
$show_images_and_summary = $this->params->get( 'show_images_and_summary' );

?>

<div class="joomdle-mycourses<?php echo $this->pageclass_sfx;?>">
    <?php if ($this->params->get('show_page_heading', 1)) : ?>
    <h1>
    <?php echo $this->escape($this->params->get('page_heading')); ?>
    </h1>
    <?php endif; ?>

    <?php if ($show_images_and_summary) : ?>
    <div class="joomdle_mycourses_no_list">
    <?php else : ?>
    <div class="joomdle_mycourses">
    <?php endif; ?>

<?php
$lang = JoomdleHelperContent::get_lang ();
$prev_cat = 0;
$i = 0;
if (is_array ($this->my_courses))
foreach ($this->my_courses as $id => $curso) :
	$i++;
	if ($curso['category'] != $prev_cat) :
		$prev_cat = $curso['category'];
		$cat_name = $curso['cat_name'];
if ($i > 1) :
?>
</ul>
<?php endif; ?>
<h4>
		<?php echo $cat_name; ?>
</h4>
		<ul>
<?php
	endif;
?>
			<li>
                <?php 
					if ($linkto == 'moodle')
                    {
                        $link = $this->jump_url."&mtype=course&id=".$curso['id']."&Itemid=$itemid";
                        if ($lang)
                            $link .= "&lang=$lang";
                        $course_link = "<a $target href=\"$link\">".$curso['fullname']."</a>";
                    }
					else if ($linkto == 'detail')
                    {
                        // Link to detail view
                        $redirect_url = JRoute::_("index.php?option=com_joomdle&view=detail&course_id=".$curso['id'].':'.JFilterOutput::stringURLSafe($curso['fullname'])."&Itemid=$itemid");
                        $course_link = "<a href=\"".$redirect_url."\">".$curso['fullname']."</a>";
                    }

                    else
                    {
                        // Link to course view
                        $redirect_url = JRoute::_("index.php?option=com_joomdle&view=course&course_id=".$curso['id'].':'.JFilterOutput::stringURLSafe($curso['fullname'])."&Itemid=$itemid");
                        $course_link = "<a href=\"".$redirect_url."\">".$curso['fullname']."</a>";
                    }

                    if ($show_images_and_summary)
                        if (count ($curso['summary_files']))
                        {
                            foreach ($curso['summary_files'] as $file) :
                                echo "<a $target href=\"$link\">";
                        ?>
                                <img style="float:none;" hspace="5" vspace="5" align="left" src="<?php echo $file['url']; ?>">
                        <?php
                               echo "</a";
                            endforeach;
                        }

                    echo $course_link;

                    if ($show_unenrol_link)
                    {
                        if ($curso['can_unenrol'])
                        {
                            $redirect_url = "index.php?option=com_joomdle&view=course&task=unenrol&course_id=".$curso['id'];
                            echo "<a href=\"".$redirect_url."\"> (".JText::_ ('COM_JOOMDLE_UNENROL').")</a>";
                        }
                    }

                    if ($show_images_and_summary)
                    {
                        echo "<div class='course_summary'>".$curso['summary']."</div>";
                        echo "<div class='clear_float'></div>";
                    }

				?>
			</li>


<?php endforeach; ?>
	</div>
</div>
