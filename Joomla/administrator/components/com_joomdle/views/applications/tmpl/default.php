<?php
defined('_JEXEC') or die('Restricted access');

?>
<form action="index.php?option=com_joomdle&view=applications&course_id=<?php echo  JRequest::getVar( 'course_id' ); ?>"  id="adminForm" method="POST" name="adminForm" width="100%">
  <?php if(!empty( $this->sidebar)): ?>
        <div id="j-sidebar-container" class="span2">
        <?php echo $this->sidebar; ?>
        </div>
        <div id="j-main-container" class="span10">
    <?php else : ?>
        <div id="j-main-container">
    <?php endif;?>

<table>
	<tr>
	    <td nowrap="nowrap" width="100%">
		<?php echo $this->course_name; ?>
		</td>
	</tr>
</table>

       <table class="table table-stripped">
             <thead>
                    <tr>
                           <th width="10">ID</th>
							<th width="10"><input type="checkbox" name="checkall-toggle" value="" onclick="Joomla.checkAll(this)" /></th>
                           <th><?php echo JText::_('COM_JOOMDLE_NAME'); ?></th>
                           <th><?php echo JText::_('COM_JOOMDLE_EMAIL'); ?></th>
                           <th><?php echo JText::_('COM_JOOMDLE_APPLICATION_DATE'); ?></th>
                           <th><?php echo JText::_('COM_JOOMDLE_CONFIRMATION_DATE'); ?></th>
                           <th class="center"><?php echo JText::_('COM_JOOMDLE_STATUS'); ?></th>
                           <th><?php echo JText::_('COM_JOOMDLE_DETAILS'); ?></th>
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
                    foreach ($this->items as $row){
                           $checked = JHTML::_('grid.id', $i, $row->id);
						   $profile_url = JoomdleHelperMappings::get_profile_url ($row->username);

						   $app_id = $row->id;
			   ?>
                           <tr class="<?php echo "row$k";?>">
                                  <td><?php echo $row->id;?></td>
                                  <td><?php echo $checked; ?></td>
                                  <td><a target="_blank" href="<?php echo $profile_url; ?>"><?php echo $row->name; ?></a></td>
                                  <td><?php echo $row->email; ?></td>
                                  <td><?php echo $row->application_date; ?></td>
                                  <td><?php if ($row->confirmation_date != 0) echo $row->confirmation_date; ?></td>
				  <td class="center"><?php 
				  					switch ($row->state)
									{
										case JOOMDLE_APPLICATION_STATE_APPROVED:
											echo JHTML::_('image', 'joomdle/' . 'tick.png' , NULL, NULL, 'COM_JOOMDLE_APPROVED' );
											break;
										case JOOMDLE_APPLICATION_STATE_REJECTED:
											 echo JHTML::_('image', 'joomdle/' . 'publish_r.png' , NULL, NULL, 'COM_JOOMDLE_REJECTED' );
											break;
										default:
											echo JText::_('COM_JOOMDLE_PENDING');
									}
								   ?>
								</td>
								<td>
								<?php
									echo "<a href='index.php?option=com_joomdle&view=application&app_id=$app_id'>".JText::_('COM_JOOMDLE_VIEW')."</a>";
								?>
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
       <input type="hidden" name="course_id" value="<?php echo JRequest::getVar( 'course_id' );?>"/>
       <input type="hidden" name="boxchecked" value="0"/>   
       <input type="hidden" name="hidemainmenu" value="0"/> 
       <input type="hidden" name="task" value="manage_applications"/> 
       <?php echo JHTML::_( 'form.token' ); ?>
</form>
