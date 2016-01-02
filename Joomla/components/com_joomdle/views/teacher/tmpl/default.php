<?php defined('_JEXEC') or die('Restricted access'); ?>
<?php
$itemid = JoomdleHelperContent::getMenuItem();
?>
<div class="joomdle-teacher<?php echo $this->pageclass_sfx;?>">
    <h1>
        <?php echo $this->user_info['name']; ?>
    </h1>

    <div class="joomdle_user">
        <div class="joomdle_user_pic">
			<?php
            // Use thumbs if available
            if ((array_key_exists ('thumb_url', $this->user_info)) && ($this->user_info['thumb_url'] != ''))
                $this->user_info['pic_url'] = $this->user_info['thumb_url'];

			if (array_key_exists ('profile_url', $this->user_info)) :
            ?>
            <a href="<?php echo JRoute::_($this->user_info['profile_url']."&Itemid=$itemid"); ?>"><img height='64' width='64' src="<?php echo $this->user_info['pic_url']; ?>"></a>
			<?php else : ?>
            <img height='64' width='64' src="<?php echo $this->user_info['pic_url']; ?>">
			<?php endif; ?>
        </div>
        <div class="joomdle_user_details">
            <?php if ((array_key_exists ('city', $this->user_info)) && ($this->user_info['city'])) : ?>
            <div class="joomdle_user_city">
                <?php echo '<b>'.JText::_('COM_JOOMDLE_CITY'). ': </b>'; ?>
                <?php       echo $this->user_info['city']; ?>
            </div>
            <?php endif; ?>
            <?php if ((array_key_exists ('country', $this->user_info)) && ($this->user_info['country'])) : ?>
            <div class="joomdle_user_country">
                <?php echo '<b>'.JText::_('COM_JOOMDLE_COUNTRY'). ': </b>'; ?>
                <?php       echo $this->user_info['country']; ?>
            </div>
            <?php endif; ?>
            <?php if ((array_key_exists ('description', $this->user_info)) && ($this->user_info['description'])) : ?>
            <div class="joomdle_user_country">
                <?php echo '<b>'.JText::_('COM_JOOMDLE_ABOUTME'). ': </b>'; ?>
                <?php       echo $this->user_info['description']; ?>
            </div>
            <?php endif; ?>
            <?php
                if ($this->params->get('additional_data_source') == 'jomsocial') : ?>
                <div class="joomdle_user_actions">
                <?php
                    echo '<br>';
                    echo '<img src="'.JURI::root().'/components/com_community/templates/default/images/action/icon-email-go.png"> ';
                    // Send message button
                    $jspath = JPATH_ROOT.'/components/com_community';
                    include_once($jspath.'/libraries/core.php');
                    include_once($jspath.'/libraries/messaging.php');

                    $user_id = JUserHelper::getUserId($this->username);
                    $user = JFactory::getUser ($user_id);
                    $onclick = CMessaging::getPopup($user->id);
                    echo '<a href="javascript:void(0)" onclick="'. $onclick .'">'. JText::_('COM_JOOMDLE_WRITE_MESSAGE').'</a>';
                ?>
                </div>
                <?php endif; ?>
        </div>
    </div>

<table width="100%" cellpadding="4" cellspacing="0" border="0" align="center" class="contentpane<?php echo $this->params->get( 'pageclass_sfx' ); ?>">
<tr>
        <td width="90%" height="20" class="sectiontableheader<?php echo $this->params->get( 'pageclass_sfx' ); ?>">
        <?php echo JText::_('COM_JOOMDLE_TEACHED_COURSES'); ?>
        </td>
</tr>

<?php
$odd = 0;
if (is_array ($this->courses))
foreach ($this->courses as $id => $curso) :
$cat_id = $curso['cat_id'];
$course_id = $curso['remoteid'];
$cat_slug = JFilterOutput::stringURLSafe ($curso['cat_name']);
$course_slug = JFilterOutput::stringURLSafe ($curso['fullname']);
?>
<tr class="sectiontableentry<?php echo $odd + 1; ?>">
                <?php $odd++; $odd = $odd % 2; ?>
        <td align="left">
                <?php
            $link = JRoute::_("index.php?option=com_joomdle&view=detail&cat_id=$cat_id:$cat_slug&course_id=$course_id:$course_slug&Itemid=$itemid");

            echo "<a href=\"$link\">".$curso['fullname']."</a>";
        ?>

        </td>
</tr>
<?php endforeach; ?>
</table>
</div>
