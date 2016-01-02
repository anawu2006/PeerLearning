<?php
defined('_JEXEC') or die('Restricted access');

?>
<form action="index.php?option=com_joomdle&view=customprofiletypes" id="adminForm" method="POST" name="adminForm">


      <?php if(!empty( $this->sidebar)): ?>
        <div id="j-sidebar-container" class="span2">
        <?php echo $this->sidebar; ?>
        </div>
        <div id="j-main-container" class="span10">
    <?php else : ?>
        <div id="j-main-container">
    <?php endif;?>


		<table class="table table-stripped">
             <thead>
                    <tr>
                           <th width="10">ID</th>
                           <th width="10"><input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo count($this->profiletypes); ?>)" /></th>
			   <th><?php echo JText::_('COM_JOOMDLE_PROFILE_TYPE'); ?></th>
			   <th><?php echo JText::_('COM_JOOMDLE_CREATE_ON_MOODLE'); ?></th>
                    </tr>              
             </thead>
		<tfoot>
                        <tr>
                                <td colspan="10">
                                        <?php echo $this->pagination->getListFooter(); ?>
                                </td>
                        </tr>
                </tfoot>
             <tbody>
                    <?php
                    $k = 0;
                    $i = 0;
					if (is_array ($this->profiletypes))
                    foreach ($this->profiletypes as $row){
                           $checked = JHTML::_('grid.id', $i, $row->id);
							$published      = JHTML::_('grid.published', $row, $i );


			   ?>
                           <tr class="<?php echo "row$k";?>">
                                  <td><?php echo $row->id;?></td>
                                  <td><?php  echo $checked; ?></td>
								   <td>
								   	<?php if ($row->profiletype_id != 0) : ?>
								   	<a href="index.php?option=com_joomdle&task=customprofiletype.edit&id=<?php echo $row->profiletype_id; ?>"><?php echo $row->name; ?></a>
									<?php else : 
										echo $row->name;
			   						endif;
									?>
								   </td>
								  <?php
								  $text = "Ticked"; $image = "tick.png";
								  ?>
				<td align="center"><?php echo $row->create_on_moodle ? JHTML::_('image', 'joomdle/' . $image , NULL, NULL, $text ) : 
										JHTML::_('image', 'joomdle/' . 'publish_r.png' , NULL, NULL, 'Not ticked' ); ?>
				</td>
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
