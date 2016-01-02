<?php
defined('_JEXEC') or die('Restricted access');

JToolBarHelper::title(JText::_('Joomdle'), 'generic.png');

JToolBarHelper::back('Back' , 'index.php?option=com_joomdle&view=courseapplications');
JToolBarHelper::custom( 'approve_applications', 'publish', 'publish', 'Approve applications', true, false );
JToolBarHelper::custom( 'reject_applications', 'unpublish', 'unpublish', 'Reject applications', true, false );

                           /*<th><?php echo JText::_('Username'); ?></th> */
?>
<form action="index.php?option=com_joomdle&view=courseapplications&task=manage_applications&course_id=<?php echo  JRequest::getVar( 'course_id' ); ?>"  id="adminForm" method="POST" name="adminForm">
<table>
	<tr>
	    <td nowrap="nowrap" width="100%">
		<?php echo $this->course_name; ?>
		</td>
	    <td nowrap="nowrap">
		<?php echo $this->lists['type'];?>
		</td>
	</tr>
</table>

       <table class="adminlist">
             <thead>
                    <tr>
                           <th width="10">ID</th>
                           <th width="10"><input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo count($this->users); ?>)" /></th>
                           <th><?php echo JText::_('COM_JOOMDLE_NAME'); ?></th>
                           <th><?php echo JText::_('COM_JOOMDLE_EMAIL'); ?></th>
                           <th><?php echo JText::_('COM_JOOMDLE_APPLICATION_DATE'); ?></th>
                           <th><?php echo JText::_('COM_JOOMDLE_CONFIRMATION_DATE'); ?></th>
                           <th><?php echo JText::_('COM_JOOMDLE_STATUS'); ?></th>
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
                    foreach ($this->users as $row){
                           $checked = JHTML::_('grid.id', $i, $row['id']);
			   //$j_account      = JHTML::_('grid.published', $row, $i );
	//					   $row['name'] = $row['firstname'] . " " . $row['lastname'];
						   $profile_url = JoomdleHelperMappings::get_profile_url ($row['username']);

						   $app_id = $row['id'];
			   ?>
                           <tr class="<?php echo "row$k";?>">
                                  <td><?php echo $row['id'];?></td>
                                  <td><?php echo $checked; ?></td>
                                  <td><a target="_blank" href="<?php echo $profile_url; ?>"><?php echo $row['name']; ?></a></td>
                                  <td><?php echo $row['email']; ?></td>
                                  <td><?php echo $row['application_date']; ?></td>
                                  <td><?php if ($row['confirmation_date'] != 0) echo $row['confirmation_date']; ?></td>
				  <td align="center"><?php 
				  					switch ($row['state'])
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
									echo "<a href='index.php?option=com_joomdle&view=courseapplications&task=view_application&app_id=$app_id'>".JText::_('COM_JOOMDLE_VIEW')."</a>";
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
        <input type="hidden" name="filter_order" value="<?php echo $this->lists['order']; ?>" />
        <input type="hidden" name="filter_order_Dir" value="<?php echo $this->lists['order_Dir']; ?>" />
       <?php echo JHTML::_( 'form.token' ); ?>
</form>
