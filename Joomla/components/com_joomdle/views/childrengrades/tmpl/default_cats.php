<?php defined('_JEXEC') or die('Restricted access'); ?>

<div class="joomdle-gradelist<?php echo $this->pageclass_sfx;?>">
    <?php if ($this->params->get('show_page_heading', 1)) : ?>
    <h1>
    <?php echo $this->escape($this->params->get('page_heading')); ?>
    </h1>
    <?php endif; ?>

<?php
$app        = JFactory::getApplication();
$params = $app->getParams();

$use_pdf_integration = $params->get('use_pdf_integration');

if ($use_pdf_integration) : ?>
<P align="right">
<a href="index.php?option=com_joomdle&view=childrengrades&format=pdf"><img src="<?php echo JURI::root(); ?>/media/media/images/mime-icon-16/pdf.png" alt="PDF"></a>
</P>
<?php endif; ?>

<?php
foreach ($this->tasks as  $user) :

    ?>
        <tr><td><h2><?php echo $user['name']; ?></h2></td></tr>
    <?php

	/* Para cada curso, mostramos sus tareas */
	foreach ($user['grades'] as  $this->course) :
			if (count ($this->course['grades']['data']) == 0)
				continue;
			echo $this->loadTemplate('course');
	endforeach; ?>

<?php endforeach; ?>

</div>
