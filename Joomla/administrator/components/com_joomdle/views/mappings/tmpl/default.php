<?php
defined('_JEXEC') or die('Restricted access');
?>
<form action="index.php?option=com_joomdle&view=mappings" method="POST"  id="adminForm" name="adminForm">

	  <?php if(!empty( $this->sidebar)): ?>
        <div id="j-sidebar-container" class="span2">
        <?php echo $this->sidebar; ?>
        </div>
        <div id="j-main-container" class="span10">
    <?php else : ?>
        <div id="j-main-container">
    <?php endif;?>

    <div id="filter-bar" class="btn-toolbar">
        <div class="filter-search btn-group pull-left">
            <input type="text" name="filter_search" id="filter_search" placeholder="<?php echo JText::_('COM_JOOMDLE_SEARCH_USERS'); ?>" value="<?php echo $this->escape($this->state->get('filter.search')); ?>" title="<?php echo JText::_('COM_JOOMDLE_SEARCH_USERS'); ?>" />
        </div>
        <div class="btn-group pull-left">
            <button type="submit" class="btn tip" title="<?php echo JText::_('JSEARCH_FILTER_SUBMIT'); ?>"><i class="icon-search"></i></button>
            <button type="button" class="btn tip" onclick="document.getElementById('filter_search').value='';this.form.submit();" title="<?php echo JText::_('JSEARCH_RESET'); ?>"><i class="icon-remove"></i></button>
        </div>
    </div>

	<div class="clearfix"> </div>
       <table class="table table-striped">
             <thead>
                    <tr>
						<th width="10">ID</th>
						<th width="10"><input type="checkbox" name="checkall-toggle" value="" onclick="Joomla.checkAll(this)" /></th>
						<th><?php echo JHTML::_('grid.sort',   JText::_('COM_JOOMDLE_JOOMLA_COMPONENT'), 'joomla_app', @$this->lists['order_Dir'], @$this->lists['order'] ); ?></th>
						<th><?php echo JText::_('COM_JOOMDLE_JOOMLA_FIELD'); ?></th>
						<th><?php echo JHTML::_('grid.sort',   JText::_('COM_JOOMDLE_MOODLE_FIELD'), 'moodle_field', @$this->lists['order_Dir'], @$this->lists['order'] ); ?></th>
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
                           $checked = JHTML::_('grid.id', $i, $row['id']);
				   ?>
                           <tr class="<?php echo "row$k";?>">
                                  <td><?php echo $row['id'];?></td>
                                  <td><?php  echo $checked; ?></td>
                                  <td><?php echo $row['joomla_app'];?></td>
                                  <td><a href='index.php?option=com_joomdle&task=mapping.edit&id=<?php echo $row['id'];?>'><?php echo JText::_ ($row['joomla_field_name']); ?></a></td>
                                  <td><a href='index.php?option=com_joomdle&task=mapping.edit&id=<?php echo $row['id'];?>'><?php echo $row['moodle_field_name']; ?></a></td>
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
	</div>
</form>
