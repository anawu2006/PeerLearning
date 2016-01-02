<?php defined('_JEXEC') or die('Restricted access'); ?>

<table width="100%" cellpadding="4" cellspacing="0" border="0" align="center" class="contentpane<?php echo $this->params->get( 'pageclass_sfx' ); ?>">
<tr>
        <td width="90%" height="20" class="sectiontableheader<?php echo $this->params->get( 'pageclass_sfx' ); ?>" colspan="3">
                <?php echo $this->course_info['fullname']; ?>
        </td>
</tr>

<tr>
        <td width="60%" colspan="2">
        </td>
</tr>
<tr class="sectiontableentry">
	<td align="left">
                <b><?php echo JText::_('COM_JOOMDLE_SUMMARY'); ?>:&nbsp;</b><?php echo JoomdleHelperSystem::fix_text_format($this->course_info['summary']); ?>
        </td>
</tr>
<tr class="sectiontableentry">
	<td align="left">
                <b><?php echo JText::_('COM_JOOMDLE_STUDENT_NO'); ?>:&nbsp;</b><?php echo $this->student_no; ?>
        </td>
</tr>



<tr>
        <td colspan=3 align="center" width="90%" height="20" class="sectiontableheader<?php echo $this->params->get( 'pageclass_sfx' ); ?>">
                <?php echo JText::_('COM_JOOMDLE_STUDENTS_DAILY_STATS'); ?>
        </td>
</tr>
<tr>
        <td width="90%" height="20" class="sectiontableheader<?php echo $this->params->get( 'pageclass_sfx' ); ?>">
                <?php echo JText::_('COM_JOOMDLE_DATE'); ?>
        </td>
        <td width="30" height="20" class="sectiontableheader<?php echo $this->params->get( 'pageclass_sfx' ); ?>" style="text-align:center;" nowrap="nowrap">
                <?php echo JText::_('COM_JOOMDLE_VIEWS'); ?>
        </td>
        <td width="30" height="20" class="sectiontableheader<?php echo $this->params->get( 'pageclass_sfx' ); ?>" style="text-align:center;" nowrap="nowrap">
                <?php echo JText::_('COM_JOOMDLE_WRITES'); ?>
        </td>
</tr>

<?php
$odd = 0;
if (is_array($this->daily_stats))
foreach ($this->daily_stats as  $stat) : ?>
<tr class="sectiontableentry<?php echo $odd + 1; ?>">
                <?php $odd++; $odd = $odd % 2; ?>
        <td align="left">
		<?php echo date('d-m-Y', $stat['timeend']); ?>
        </td>
        <td height="20" align="center">
                <?php echo $stat['stat1']; ?>
        </td>
        <td height="20" align="right">
                <?php echo $stat['stat2']; ?>
        </td>
</tr>
<?php endforeach; ?>

<tr>
        <td width="90%" height="20" class="sectiontableheader<?php echo $this->params->get( 'pageclass_sfx' ); ?>">
                <?php echo JText::_('COM_JOOMDLE_TOPICS'); ?>
        </td>
        <td width="30" height="20" class="sectiontableheader<?php echo $this->params->get( 'pageclass_sfx' ); ?>" style="text-align:center;" nowrap="nowrap">
                <?php echo JText::_('COM_JOOMDLE_SUBMISSIONS'); ?>
        </td>
        <td width="30" height="20" class="sectiontableheader<?php echo $this->params->get( 'pageclass_sfx' ); ?>" style="text-align:center;" nowrap="nowrap">
                <?php echo JText::_('COM_JOOMDLE_AVERAGE_GRADE'); ?>
        </td>
</tr>

<tr>
        <td width="60%" colspan="2">
        </td>
</tr>
<?php
if (is_array($this->assignments))
foreach ($this->assignments as  $assignment) : ?>
<tr class="sectiontableentry<?php echo $odd + 1; ?>">
                <?php $odd++; $odd = $odd % 2; ?>
        <td align="left">
                <?php echo $assignment['tarea']; ?>
        </td>
        <td height="20" align="center">
                <?php echo $assignment['entregados']; ?>
        </td>
        <td height="20" align="right">
                <?php //if  (array_key_exists($assignment['tarea'],$this->grades)) printf ("%.2f", $this->grades[$assignment['tarea']]); ?>
				<?php
					foreach ($this->grades as $grade)
					{
						if ($grade['tarea'] == $assignment['tarea'])
						{
							printf ("%.2f", $grade['media']);
							break;
						}
					}
				?>
        </td>
</tr>
<?php endforeach; ?>

</table>

