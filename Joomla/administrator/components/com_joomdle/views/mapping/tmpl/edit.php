<?php
/**
 * @package     Joomdle
 *
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

// Include the component HTML helpers.
JHtml::addIncludePath(JPATH_COMPONENT.'/helpers/html');

// Load the tooltip behavior.
JHtml::_('behavior.tooltip');
JHtml::_('behavior.formvalidation');
JHtml::_('formbehavior.chosen', 'select');

// Get the form fieldsets.
$fieldsets = $this->form->getFieldsets();
?>

<script type="text/javascript">
	Joomla.submitbutton = function(task)
	{
		if (task == 'mapping.cancel' || document.formvalidator.isValid(document.id('mapping-form'))) {
			Joomla.submitform(task, document.getElementById('mapping-form'));
		}
	}
</script>

<form action="<?php echo JRoute::_('index.php?option=com_joomdle&view=mapping&layout=edit&id='.(int) $this->item->id); ?>" method="post" name="adminForm" id="mapping-form" class="form-validate form-horizontal" enctype="multipart/form-data">
	<fieldset>
		<ul class="nav nav-tabs">
		<li class="active"><a href="#details" data-toggle="tab"><?php echo JText::_('COM_JOOMDLE_DATA_MAPPING_CONFIGURATION');?></a></li>
		</ul>

			<div class="tab-content">
				<div class="tab-pane active" id="details">
					<?php foreach($this->form->getFieldset('mapping') as $field) :?>
						<div class="control-group">
							<div class="control-label">
								<?php echo $field->label; ?>
							</div>
							<div class="controls">
								<?php echo $field->input; ?>
							</div>
						</div>
					<?php endforeach; ?>
				</div>
		</div>
	</fieldset>
	<input type="hidden" name="task" value="" />
	<?php echo JHtml::_('form.token'); ?>
</form>
