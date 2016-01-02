<?php defined('_JEXEC') or die('Restricted access'); ?>
<?php
require_once(JPATH_ADMINISTRATOR.'/components/com_joomdle/helpers/mappings.php');
$itemid = JoomdleHelperContent::getMenuItem();
?>
<div class="joomdle-userlist<?php echo $this->pageclass_sfx;?>">
    <h1>
        <?php echo $this->course_info['fullname'] . ': '; ?>
        <?php echo JText::_('COM_JOOMDLE_STUDENTS'); ?>
    </h1>

<?php
if (is_array ($this->users))
foreach ($this->users as $id => $student) :
    $user_info = JoomdleHelperMappings::get_user_info_for_joomla ($student['username']);
    if (!count ($user_info)) //not a Joomla user
        continue;
?>
    <div class="joomdle_user_list_item">
        <div class="joomdle_user_list_item_pic">
		<?php
			// Use thumbs if available
            if ((array_key_exists ('thumb_url', $user_info)) && ($user_info['thumb_url'] != ''))
                $user_info['pic_url'] = $user_info['thumb_url'];
		?>
		<?php   if ((array_key_exists ('profile_url', $user_info)) &&($user_info['profile_url'])) : ?>
            <a href="<?php echo JRoute::_($user_info['profile_url']."&Itemid=$itemid"); ?>"><img height='64' width='64' src="<?php echo $user_info['pic_url']; ?>"></a>
        <?php else :?>
            <img height='64' width='64' src="<?php echo $user_info['pic_url']; ?>">
        <?php endif; ?>
        </div>
        <div class="joomdle_user_list_item_name">
            <?php

		   if ((array_key_exists ('profile_url', $user_info)) &&($user_info['profile_url']))
            {
              //  $link = $user_info['profile_url']."&Itemid=$itemid";
                $link = $user_info['profile_url'];
                echo "<a href=\"$link\">".$student['firstname']. " " .$student['lastname']."</a>";
            }
            else echo $student['firstname']. " " .$student['lastname'];
            ?>
        </div>
    </div>
<?php endforeach; ?>
</div>
