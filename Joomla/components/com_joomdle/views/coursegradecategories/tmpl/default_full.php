<?php defined('_JEXEC') or die('Restricted access'); ?>

<?php
$itemid = JoomdleHelperContent::getMenuItem();
?>
<div class="joomdle-gradelist<?php echo $this->pageclass_sfx;?>">
    <h1>
            <?php echo $this->course_info['fullname'] . ': '; ?>
            <?php echo JText::_('COM_JOOMDLE_GRADING_SYSTEM'); ?>
    </h1>

<table width="100%" cellpadding="4" cellspacing="0" border="1" align="center" class="simpletable<?php echo $this->params->get( 'pageclass_sfx' ); ?>">
<tr>
        <td width="35%" height="20" class="sectiontableheader<?php echo $this->params->get( 'pageclass_sfx' ); ?>">
            <b><?php echo JText::_('COM_JOOMDLE_ASSESSMENT_CATEGORY'); ?></b>
        </td>
        <td width="40%" height="20" class="sectiontableheader<?php echo $this->params->get( 'pageclass_sfx' ); ?>" style="text-align:center;" nowrap="nowrap">
                <b><?php echo JText::_('COM_JOOMDLE_TASK'); ?></b>
        </td>
        <td width="10%" height="20" class="sectiontableheader<?php echo $this->params->get( 'pageclass_sfx' ); ?>" style="text-align:center;" nowrap="nowrap">
                <b><?php echo JText::_('COM_JOOMDLE_DUE_DATE'); ?></b>
        </td>
        <td width="15%" height="20" class="sectiontableheader<?php echo $this->params->get( 'pageclass_sfx' ); ?>" style="text-align:center;" nowrap="nowrap">
                <b><?php echo JText::_('COM_JOOMDLE_VALUE'); ?></b>
        </td>
</tr>

<?php
$i = 0;
$odd = 0;
if (is_array($this->gcats))
foreach ($this->gcats as  $gcat) : 
	$n = count ($gcat['items']);
?>
<tr class="sectiontableentry<?php echo $odd + 1; ?>">
                <?php $odd++; $odd = $odd % 2; ?>
        <td width="35%" height="20" align="left" colspan="3" >
                <?php echo $gcat['fullname']; ?>
        </td>
        <td width="15%" height="20" align="center" rowspan="<?php echo $n + 1; ?>" style="text-align:center;">
                <?php printf ("%d", $gcat['grademax']); ?>%
        </td>
</tr>
                <?php
					foreach ($gcat['items'] as $item) :
					?>
					<tr>
						<td>
						</td>
						<td width="40%">
							<?php 
								$url = "index.php?option=com_joomdle&view=rubrics&id=".$item['id'];
								if ($item['has_rubrics'])
									echo "<a href='$url'>".$item['name']."</a>";
								else
									echo $item['name']; 
							?>
						</td>
						<td width="10%" style="text-align:center;">
							<?php 
								if ($item['due'])
									echo JHTML::_('date', $item['due'] , JText::_('DATE_FORMAT_LC4'));
							?>      
						</td>
					</tr>
					<?php
					endforeach;
				?>
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
