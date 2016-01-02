<?php
defined('_JEXEC') or die('Restricted access');
?>

<form action="index.php" method="POST" id="adminForm" name="adminForm">

      <?php if(!empty( $this->sidebar)): ?>
        <div id="j-sidebar-container" class="span2">
        <?php echo $this->sidebar; ?>
        </div>
        <div id="j-main-container" class="span10">
    <?php else : ?>
        <div id="j-main-container">
    <?php endif;?>

       <table class="table table-striped">
             <thead>
                    <tr>
                           <th width="10">ID</th>
                           <th width="10"><input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo count($this->courses); ?>)" /></th>
                           <th><?php echo JText::_('COM_JOOMDLE_COURSE'); ?></th>
                    </tr>              
             </thead>
             <tbody>
                    <?php
                    $k = 0;
                    $i = 0;
                    foreach ($this->courses as $row){
                           $checked = JHTML::_('grid.id', $i, $row->id);

			   ?>
                           <tr class="<?php echo "row$k";?>">
                                  <td><?php echo $row->id;?></td>
                                  <td><?php echo $checked; ?></td>
                                  <td><a href='index.php?option=com_joomdle&view=applications&course_id=<?php echo $row->id; ?>'><?php echo $row->fullname;?></a></td>
                           </tr>
                    <?php
                    $k = 1 - $k;
                    $i++;
                    }
                    ?>
             </tbody>
       </table>
      
       <input type="hidden" name="option" value="<?php echo JRequest::getVar( 'option' );?>"/>
       <input type="hidden" name="task" value=""/>
       <input type="hidden" name="boxchecked" value="0"/>   
       <input type="hidden" name="hidemainmenu" value="0"/> 
       <?php echo JHTML::_( 'form.token' ); ?>
</form>
