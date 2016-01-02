<?php defined('_JEXEC') or die('Restricted access'); ?>

<?php 
$itemid = JoomdleHelperContent::getMenuItem();

$linkstarget = $this->params->get( 'linkstarget' );
if ($linkstarget == "new")
    $target = " target='_blank'";
else $target = "";

?>
<div class="joomdle-course<?php echo $this->pageclass_sfx?>">

   <h1> <?php echo $this->course_info['fullname']; ?></h1>
<?php
$jump_url =  JoomdleHelperContent::getJumpURL ();
$user = JFactory::getUser();
$username = $user->username;
$session                = JFactory::getSession();
$token = md5 ($session->getId());
$course_id = $this->course_info['remoteid'];
$direct_link = 1;
$show_summary = $this->params->get( 'course_show_summary');
$show_topics_numbers = $this->params->get( 'course_show_numbers');

if ($this->course_info['guest'])
    $this->is_enroled = true;

//skip intro
//array_shift ($this->mods);
if (is_array ($this->mods)) {
foreach ($this->mods as  $tema) : ?>
	<div class="joomdle_course_section">
		<?php if ($show_topics_numbers) : ?>
		<div class="joomdle_item_title joomdle_section_list_item_title">
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
		<div class="joomdle_item_content joomdle_section_list_item_resources">
		<?php
				if ($show_summary)
					if ($tema['summary'])
						echo  $tema['summary'];
		?>
		<?php
			$resources = $tema['mods'];
			if (is_array($resources)) : ?>
			<?php
			foreach ($resources as $id => $resource) {
				$mtype = JoomdleHelperSystem::get_mtype ($resource['mod']);
				if (!$mtype) // skip unknow modules
					continue;

				$icon_url = JoomdleHelperSystem::get_icon_url ($resource['mod'], $resource['type']);
				if ($icon_url)
					echo '<img align="center" src="'. $icon_url.'">&nbsp;';

				if ($resource['mod'] == 'label')
                {
                    echo '</P>';
					echo $resource['content'];
                    echo '</P>';
                    continue;
                }

				if (($this->is_enroled) && ($resource['available']))
				{
					$direct_link = JoomdleHelperSystem::get_direct_link ($resource['mod'], $course_id, $resource['id'], $resource['type']);
                    if ($direct_link)
                    {
						// Open in new window if configured like that in moodle
						if ($resource['display'] == 6)
							$resource_target = 'target="_blank"';
						else
							$resource_target = '';

						if ($direct_link != 'none')
							echo "<a $resource_target  href=\"".$direct_link."\">".$resource['name']."</a><br>";
                    }
                    else
                        echo "<a $target href=\"".$jump_url."&mtype=$mtype&id=".$resource['id']."&course_id=$course_id&create_user=0&Itemid=$itemid&redirect=$direct_link\">".$resource['name']."</a><br>";

				}
				else
				{
					echo $resource['name'] .'<br>';
					if ((!$resource['available']) && ($resource['completion_info'] != '')) : ?>
						<div class="joomdle_completion_info">
							<?php echo $resource['completion_info']; ?>
						</div>
					<?php
					endif;
				}

				if ($resource['content'] != '') : ?>
				<div class="joomdle_section_list_item_resources_content">
							<?php echo $resource['content']; ?>
				</div>
				<?php
				endif;
			}
			?>
			<?php endif; ?>
		</div>
	</div>
<?php endforeach;
}
?>

<?php if ($this->params->get('show_back_links')) : ?>
	<div>
	<P align="center">
	<a href="javascript: history.go(-1)"><?php echo JText::_('COM_JOOMDLE_BACK'); ?></a>
	</P>
	</div>
<?php endif; ?>
</div>
