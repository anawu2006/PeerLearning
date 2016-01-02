<?php // no direct access
defined('_JEXEC') or die('Restricted access');

if ($linkto == 'moodle')
{
    if ($default_itemid)
        $itemid = $default_itemid;
    else
        $itemid = JoomdleHelperContent::getMenuItem();
}
else if ($linkto == 'detail')
{
        $itemid = JoomdleHelperContent::getMenuItem();

        if ($joomdle_itemid)
            $itemid = $joomdle_itemid;
}
else
{
        $itemid = JoomdleHelperContent::getMenuItem();

        if ($joomdle_itemid)
            $itemid = $joomdle_itemid;
        if ($courseview_itemid)
            $itemid = $courseview_itemid;
}

if ($linkstarget == 'wrapper')
	$open_in_wrapper = 1;
else
	$open_in_wrapper = 0;

if ($linkstarget == "new")
	$target = " target='_blank'";
else $target = "";

$moodle_auth_land_url = $comp_params->get( 'MOODLE_URL' ).'/auth/joomdle/land.php';

$lang = JoomdleHelperContent::get_lang ();
$prev_cat = 0;

?>
    <ul class="joomdlecourses<?php echo $moduleclass_sfx; ?>">
<?php
		$group_by_category = $params->get( 'group_by_category' );


        if (is_array($cursos)) {
		foreach ($cursos as $id => $curso) {
			$id = $curso['id'];

		if ($group_by_category)
		{
			// Group by category
			if ($curso['category'] != $prev_cat) :
				$prev_cat = $curso['category'];
				$cat_name = $curso['cat_name']; 
			?>
			</ul>
			<h4>
					<?php echo $cat_name; ?>
			</h4>
			<ul>
			<?php
			endif;
		}

         if ($linkto == 'moodle')
            {
                $id = $curso['id'];
                $redirect_url = $moodle_auth_land_url."?username=$username&token=$token&mtype=course&id=$id&use_wrapper=$open_in_wrapper&Itemid=$itemid";
                if ($lang)
                    $redirect_url .= "&lang=$lang";
                echo "<li><a $target href=\"".$redirect_url."\">".$curso['fullname']."</a></li>";
            }
            else if ($linkto == 'detail')
            {
                // Link to detail view
                $redirect_url = JRoute::_("index.php?option=com_joomdle&view=detail&course_id=".$curso['id'].':'.JFilterOutput::stringURLSafe($curso['fullname'])."&Itemid=$itemid");
                echo "<li><a href=\"".$redirect_url."\">".$curso['fullname']."</a></li>";
			}
            else
            {
                // Link to course view
                $redirect_url = JRoute::_("index.php?option=com_joomdle&view=course&course_id=".$curso['id'].':'.JFilterOutput::stringURLSafe($curso['fullname'])."&Itemid=$itemid");
                echo "<li><a href=\"".$redirect_url."\">".$curso['fullname']."</a></li>";
            }

			if ($show_unenrol_link)
			{
				if ($curso['can_unenrol'])
				{
					$redirect_url = "index.php?option=com_joomdle&view=course&task=unenrol&course_id=".$curso['id'];
					echo "<a href=\"".$redirect_url."\"> (".JText::_ ('COM_JOOMDLE_UNENROL').")</a>";
				}
			}

		}
	}

?>
    </ul>
