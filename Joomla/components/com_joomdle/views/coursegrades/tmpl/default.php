<?php defined('_JEXEC') or die('Restricted access'); ?>

<?php
require_once(JPATH_SITE.'/components/com_joomdle/helpers/content.php');

$user = JFactory::getUser();
$id = $user->get('id');
$username = $user->get('username');
?>
<div class="joomdle-gradelist<?php echo $this->pageclass_sfx;?>">
    <h1>
        <?php echo $this->course_info['fullname'] . ': '; ?>
        <?php echo JText::_('COM_JOOMDLE_COURSE_GRADES'); ?>
    </h1>

<table width="100%" cellpadding="4" cellspacing="0" border="0" align="center" class="contentpane<?php echo $this->params->get( 'pageclass_sfx' ); ?>">
<tr>
        <td width="90%" height="20" class="sectiontableheader<?php echo $this->params->get( 'pageclass_sfx' ); ?>">
        <?php echo JText::_('COM_JOOMDLE_TASKS'); ?>
        </td>
        <td width="30" height="20" class="sectiontableheader<?php echo $this->params->get( 'pageclass_sfx' ); ?>" style="text-align:center;" nowrap="nowrap">
                <?php echo JText::_('COM_JOOMDLE_GRADE'); ?>
        </td>

<?php
$tareas = JoomdleHelperContent::call_method ("get_user_grades", $username, $this->course_id);

$odd = 0;
foreach ($tareas as  $tarea) :
if ($tarea['itemname']) :
?>
<tr class="sectiontableentry<?php echo $odd + 1; ?>">
                <?php $odd++; $odd = $odd % 2; ?>
        <td height="20">
                <?php  echo $tarea['itemname']; ?>
        </td>
        <td height="20">
                <?php echo $tarea['finalgrade']; ?>

        </td>
</tr>
<?php endif; ?>
<?php endforeach; ?>


</table>
</div>
