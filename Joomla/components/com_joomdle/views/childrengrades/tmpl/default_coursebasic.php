<tr>
        <td width="90%" height="20" class="sectiontableheader<?php echo $this->params->get( 'pageclass_sfx' ); ?>">
		<b>
        <?php echo JText::_('COM_JOOMDLE_COURSE_TASKS'); ?>:
                <?php echo $this->course_name; ?>
		</b>
        </td>
        <td width="30" height="20" class="sectiontableheader<?php echo $this->params->get( 'pageclass_sfx' ); ?>" style="text-align:center;" nowrap="nowrap">
		<b>
                <?php echo JText::_('COM_JOOMDLE_GRADE'); ?>
		</b>
        </td>
<tr>
<?php

foreach ($this->tareas as  $tarea) :
if ($tarea['itemname']) :
?>
<tr>
        <td height="20">
                <?php  echo $tarea['itemname']; ?>
        </td>
        <td height="20" align="center">
                <?php  echo $tarea['finalgrade']; ?>

        </td>
</tr>
<?php endif; ?>
<?php endforeach; ?>

