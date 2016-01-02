<?php
defined('_JEXEC') or die('Restricted access');

$i = 0;
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

       <table  class="table table-striped">
             <thead>
                    <tr>
						<th width="10">ID</th>
						<th width="10"><input type="checkbox" name="checkall-toggle" value="" onclick="Joomla.checkAll(this)" /></th>
						<th width="600"><?php echo JText::_('COM_JOOMDLE_COURSE'); ?></th>
						<th class="center"><?php echo JText::_('COM_JOOMDLE_MAILING_LIST_STUDENTS'); ?></th>
						<th class="center"><?php echo JText::_('COM_JOOMDLE_MAILING_LIST_TEACHERS'); ?></th>
						<th class="center"><?php echo JText::_('COM_JOOMDLE_MAILING_LIST_PARENTS'); ?></th>
                    </tr>              
             </thead>
             <tbody>
                    <?php
                    $k = 0;
					$row = JoomdleHelperMailinglist::get_general_lists ();
					$checked = JHTML::_('grid.id', $i, $row->id);
					$row->published = $row->published_students;
					$published_students      = JHTML::_('jgrid.published', $row->published, $i , 'mailinglist.students_');

					$row->published = $row->published_teachers;
					$published_teachers      = JHTML::_('jgrid.published', $row->published, $i , 'mailinglist.teachers_');

					$row->published = $row->published_parents;
					$published_parents      = JHTML::_('jgrid.published', $row->published, $i , 'mailinglist.parents_');

			   ?>
					<tr class="<?php echo "row$k";?>">
						<td><?php echo $row->id;?></td>
						<td><?php echo $checked; ?></td>
						<td><?php echo $row->fullname;?></td>
						<td class="center"><?php echo $published_students; ?> </td>
						<td class="center"><?php echo $published_teachers; ?> </td>
						<td align="center"><?php echo $published_parents; ?> </td>
				   </tr>
                    <?php
                    $k = 1 - $k;
                    $i++;
                    ?>
             </tbody>
       </table>
       <table  class="table table-striped">
             <thead>
                    <tr>
							<th width="10">ID</th>
							<th width="10"></th>
							<th width="600"><?php echo JText::_('COM_JOOMDLE_COURSE'); ?></th>
							<th class="center"><?php echo JText::_('COM_JOOMDLE_MAILING_LIST_STUDENTS'); ?></th>
							<th class="center"><?php echo JText::_('COM_JOOMDLE_MAILING_LIST_TEACHERS'); ?></th>
							<th class="center"><?php echo JText::_('COM_JOOMDLE_MAILING_LIST_PARENTS'); ?></th>
                    </tr>              
             </thead>
             <tbody>
                    <?php
                    $k = 0;
                    foreach ($this->courses as $row){
							$checked = JHTML::_('grid.id', $i, $row->id);
							$row->published = $row->published_students;
							$published_students      = JHTML::_('jgrid.published', $row->published, $i , 'mailinglist.students_');
							$row->published = $row->published_teachers;
							$published_teachers      = JHTML::_('jgrid.published', $row->published, $i , 'mailinglist.teachers_');
							$row->published = $row->published_parents;
							$published_parents      = JHTML::_('jgrid.published', $row->published, $i , 'mailinglist.parents_');
				   ?>
			   <tr class="<?php echo "row$k";?>">
				  <td><?php echo $row->id;?></td>
				  <td><?php echo $checked; ?></td>
				  <td><?php echo $row->fullname;?></td>
				  <td class="center"><?php echo $published_students; ?> </td>
				  <td class="center"><?php echo $published_teachers; ?> </td>
                  <td align="center"><?php echo $published_parents; ?> </td>
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
