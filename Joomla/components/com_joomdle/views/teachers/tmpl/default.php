<?php defined('_JEXEC') or die('Restricted access'); ?>

<?php
$itemid = JoomdleHelperContent::getMenuItem();
?>
<div class="joomdle-userlist<?php echo $this->pageclass_sfx;?>">
    <h1>
        <?php echo $this->course_info['fullname'] . ': '; ?>
        <?php echo JText::_('COM_JOOMDLE_TEACHERS'); ?>
    </h1>


<?php
if (is_array ($this->teachers))
foreach ($this->teachers as  $teacher) : ?>
<?php
    $user_info = JoomdleHelperMappings::get_user_info_for_joomla ($teacher['username']);
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

            <a href="<?php echo JRoute::_($user_info['profile_url']."&Itemid=$itemid"); ?>"><img height='64' width='64' src="<?php echo $user_info['pic_url']; ?>"></a>
        </div>
        <div class="joomdle_user_list_item_name">
               <a href="<?php echo JRoute::_("index.php?option=com_joomdle&view=teacher&username=".$teacher['username']."&Itemid=$itemid"); ?>"><?php echo $teacher['firstname']." ".$teacher['lastname']; ?></a>
        </div>
    </div>
<?php endforeach; ?>

<?php if ($this->params->get('show_back_links')) : ?>
    <div>
    <P align="center">
    <a href="javascript: history.go(-1)"><?php echo JText::_('COM_JOOMDLE_BACK'); ?></a>
    </P>
    </div>
<?php endif; ?>

</div>
