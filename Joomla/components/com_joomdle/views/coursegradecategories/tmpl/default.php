<?php defined('_JEXEC') or die('Restricted access'); ?>

<?php
$itemid = JoomdleHelperContent::getMenuItem();
?>
<div class="joomdle-gradelist<?php echo $this->pageclass_sfx;?>">
    <h1>
            <?php echo $this->course_info['fullname'] . ': '; ?>
            <?php echo JText::_('COM_JOOMDLE_GRADING_SYSTEM'); ?>
    </h1>

<table width="100%" cellpadding="4" cellspacing="0" border="0" align="center" class="contentpane<?php echo $this->params->get( 'pageclass_sfx' ); ?>">
<tr>
        <td width="90%" height="20" class="sectiontableheader<?php echo $this->params->get( 'pageclass_sfx' ); ?>">
                <?php echo JText::_('COM_JOOMDLE_TASKS'); ?>
        </td>
        <td width="30" height="20" class="sectiontableheader<?php echo $this->params->get( 'pageclass_sfx' ); ?>" style="text-align:center;" nowrap="nowrap">
                <?php echo JText::_('COM_JOOMDLE_VALUE'); ?>
        </td>
</tr>

<tr>
        <td width="60%" colspan="2">
        </td>
</tr>
<?php
$i = 0;
$odd = 0;
if (is_array($this->gcats))
foreach ($this->gcats as  $gcat) : ?>
<tr class="sectiontableentry<?php echo $odd + 1; ?>">
                <?php $odd++; $odd = $odd % 2; ?>
        <td height="20" align="left">
                <?php echo $gcat['fullname']; ?>
        </td>
        <td height="20" align="right">
                <?php printf ("%.2f", $gcat['grademax']); ?>%
        </td>
</tr>
<?php endforeach; ?>
<?php $i++;
$cat_id = $this->course_info['cat_id'];
$course_id = $this->course_info['remoteid'];
$cat_slug = JFilterOutput::stringURLSafe ($this->course_info['cat_name']);
$course_slug = JFilterOutput::stringURLSafe ($this->course_info['fullname']);
?>

<?php
?>
</table>

<?php if ($this->params->get('show_back_links')) : ?>
    <div>
    <P align="center">
    <a href="javascript: history.go(-1)"><?php echo JText::_('COM_JOOMDLE_BACK'); ?></a>
    </P>
    </div>
<?php endif; ?>

</div>
