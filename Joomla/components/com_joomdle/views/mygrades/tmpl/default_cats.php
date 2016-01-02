<?php defined('_JEXEC') or die('Restricted access'); ?>

<div class="joomdle-gradelist<?php echo $this->pageclass_sfx;?>">
    <?php if ($this->params->get('show_page_heading', 1)) : ?>
    <h1>
    <?php echo $this->escape($this->params->get('page_heading')); ?>
    </h1>
    <?php endif; ?>

<?php
/* Para cada curso, mostramos sus tareas */
foreach ($this->tasks as  $this->course) :
		if (count ($this->course['grades']['data']) == 0)
			continue;
		echo $this->loadTemplate('course');
endforeach; ?>

</div>
