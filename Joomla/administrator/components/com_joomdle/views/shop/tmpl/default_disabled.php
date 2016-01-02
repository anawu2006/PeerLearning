<?php
defined('_JEXEC') or die('Restricted access');

$i = 0;
?>

<form action="index.php" method="POST" id="adminForm" name="adminForm" class="table table-stripped">
  <?php if(!empty( $this->sidebar)): ?>
        <div id="j-sidebar-container" class="span2">
        <?php echo $this->sidebar; ?>
        </div>
        <div id="j-main-container" class="span10">
    <?php else : ?>
        <div id="j-main-container">
    <?php endif;?>

	<?php echo $this->message; ?>
</div>
