<?php defined('_JEXEC') or die('Restricted access'); ?>
<?php
$n = count ($this->tasks);
$i = 0;
foreach ($this->tasks as  $user) :

    ?>
        <h2><?php echo $user['name']; ?></h2>
    <?php

    /* Para cada curso, mostramos sus tareas */
    foreach ($user['grades'] as  $this->course) :
            if (count ($this->course['grades']['data']) == 0)
                continue;
            echo $this->loadTemplate('coursepdf');
    endforeach; ?>

	<?php 
		$i++;
		if ($i < $n)
			echo '<br pagebreak="true" />';
	?>
	
<?php endforeach; ?>
