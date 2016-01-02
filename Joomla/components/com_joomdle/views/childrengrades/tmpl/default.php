<?php defined('_JEXEC') or die('Restricted access'); ?>

<div class="joomdle-gradelist<?php echo $this->pageclass_sfx;?>">
    <?php if ($this->params->get('show_page_heading', 1)) : ?>
    <h1>
    <?php echo $this->escape($this->params->get('page_heading')); ?>
    </h1>
    <?php endif; ?>

<table width="100%" cellpadding="4" cellspacing="0" border="0" align="center" class="contentpane<?php echo $this->params->get( 'pageclass_sfx' ); ?>">
<?php
foreach ($this->tasks as  $user) :

	?>
		<tr><td><h3><?php echo $user['name']; ?></h3></td></tr>
	<?php

	foreach ($user['grades'] as  $course) :
		$tareas = $course['grades'];

		if ((!is_array ($tareas)) || (!count ($tareas)))
			continue;

		$this->name = $user['name'];
		$this->tareas = $tareas;
		$this->course_name = $course['fullname'];
		/* Para cada curso, mostramos sus tareas */
        echo $this->loadTemplate('coursebasic');
?>

	<?php endforeach; ?>
<?php endforeach; ?>

</table>
</div>
