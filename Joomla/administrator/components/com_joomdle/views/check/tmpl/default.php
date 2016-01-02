<?php
defined('_JEXEC') or die('Restricted access');

?>
  <?php if(!empty( $this->sidebar)): ?>
        <div id="j-sidebar-container" class="span2">
        <?php echo $this->sidebar; ?>
        </div>
        <div id="j-main-container" class="span10">
    <?php else : ?>
        <div id="j-main-container">
    <?php endif;?>

       <table  width="100%"  class="table table-striped">
             <thead>
                    <tr>
                           <th width="30%"><?php echo JText::_('COM_JOOMDLE_CHECK'); ?></th>
                           <th width="10%"><?php echo JText::_('COM_JOOMDLE_STATUS'); ?></th>
                           <th><?php echo JText::_('COM_JOOMDLE_ERROR'); ?></th>
                    </tr>
             </thead>
             <tbody>
                    <?php
                    $k = 0;
                    $i = 0;
                    foreach ($this->system_info as $row){
                           //$checked = JHTML::_('grid.id', $i, $row['id']);
                           //$j_account      = JHTML::_('grid.published', $row, $i );
                           ?>
                           <tr class="<?php echo "row$k";?>">
                                  <td><?php echo $row['description'];?></td>
                                  <td align="center"><?php echo ($row['value'] == 1)? JHTML::_('image', 'joomdle/' . 'tick.png' , NULL, NULL, 'Ok' )  : JHTML::_('image', 'joomdle/' . 'publish_r.png' , NULL, NULL, 'Error' ); ?></td>
                                  <td align="center"><?php echo $row['error']; ?> </td>
                           </tr>
                    <?php
                    $k = 1 - $k;
                    $i++;
                    }
                    ?>
             </tbody>
       </table>
	   <br>
<center><?php echo JText::_('COM_JOOMDLE_FOR_PROBLEM_RESOLUTION_SEE'); ?>: <a target='_blank' href="http://www.joomdle.com/wiki/System_health_check">http://www.joomdle.com/wiki/System_health_check</a></center>
</div>
