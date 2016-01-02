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

<?php if (count ($this->bundles)) : ?>
       <table class="table table-striped" width="100%">
             <thead>
                    <tr>
                           <th width="10">ID</th>
						  <th width="10"><input type="checkbox" name="checkall-toggle" value="" onclick="Joomla.checkAll(this)" /></th>
                           <th width="600"><?php echo JText::_('COM_JOOMDLE_BUNDLE'); ?></th>
                           <th class="center"><?php echo JText::_('COM_JOOMDLE_SELL_ON_SHOP'); ?></th>
                    </tr>
             </thead>
             <tbody>
                    <?php
                    $k = 0;
                    $i = 0;
                    foreach ($this->bundles as $row){
                           $checked = JHTML::_('grid.id', $i, "bundle_".$row->id);
						   $published      = JHTML::_('jgrid.published', $row->published, $i , 'shop.');

               ?>
                           <tr class="<?php echo "row$k";?>">
                                  <td><?php echo $row->id;?></td>
                                  <td><?php echo $checked; ?></td>
                                  <td><a href="index.php?option=com_joomdle&view=bundle&task=bundle.edit&id=<?php echo $row->id; ?>"><?php echo $row->name;?></a></td>
                  <td class="center"><?php echo $published; ?> </td>
                           </tr>
                    <?php
                    $k = 1 - $k;
                    $i++;
                    }
                    ?>
             </tbody>
       </table>
<?php endif; ?>
       <table class="table table-striped" width="100%">
             <thead>
                    <tr>
                           <th width="10">ID</th>
						  <th width="10"><input type="checkbox" name="checkall-toggle" value="" onclick="Joomla.checkAll(this)" /></th>
                           <th width="600"><?php echo JText::_('COM_JOOMDLE_COURSE'); ?></th>
                           <th class="center"><?php echo JText::_('COM_JOOMDLE_SELL_ON_SHOP'); ?></th>
                    </tr>              
             </thead>
             <tbody>
                    <?php
                    $k = 0;
                    foreach ($this->courses as $row){
                           $checked = JHTML::_('grid.id', $i, $row->id);
			   $published      = JHTML::_('jgrid.published', $row->published, $i , 'shop.');

			   ?>
                           <tr class="<?php echo "row$k";?>">
                                  <td><?php echo $row->id;?></td>
                                  <td><?php echo $checked; ?></td>
                                  <td><?php echo $row->fullname;?></td>
				  <td class="center"><?php echo $published; ?> </td>
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
