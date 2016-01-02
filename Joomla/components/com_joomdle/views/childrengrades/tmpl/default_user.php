<?php defined('_JEXEC') or die('Restricted access'); ?>

<div class="joomdle-gradelist<?php echo $this->pageclass_sfx;?>">
    <?php if ($this->params->get('show_page_heading', 1)) : ?>
    <h1>
    <?php echo $this->escape($this->params->get('page_heading')); ?>
    </h1>
    <?php endif; ?>

<table width="100%" cellpadding="4" cellspacing="0" border="0" align="center" class="contentpane<?php echo $this->params->get( 'pageclass_sfx' ); ?>">
<?php
/* Para cada curso, mostramos sus tareas */
foreach ($this->tasks as  $curso) :
$tareas = $curso['grades'];
if ((!is_array ($tareas)) || (!count ($tareas)))
    continue;
?>
<tr>
        <td width="90%" height="20" class="sectiontableheader<?php echo $this->params->get( 'pageclass_sfx' ); ?>">
			<h4>
				<?php echo JText::_('COM_JOOMDLE_COURSE_TASKS'); ?>:
				<?php echo $curso['fullname']; ?>
			</h4>
        </td>
        <td width="30" height="20" class="sectiontableheader<?php echo $this->params->get( 'pageclass_sfx' ); ?>"  nowrap="nowrap">
			<h4>
                <?php echo JText::_('COM_JOOMDLE_GRADE'); ?>
			</h4>
        </td>
<tr>
<?php

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
                <?php 
        echo $tarea['finalgrade'];
        ?>

        </td>
</tr>
<?php endif; ?>
<?php endforeach; ?>
<?php endforeach; ?>

</table>
</div>
