<?php
/**
 * @package     Joomdle
 *
 * @copyright   Copyright (C) 2012 Antonio Duran Terres
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */
defined('_JEXEC') or die;
?>
<fieldset class="form-horizontal">
	<legend><?php echo JText::_('COM_JOOMDLE_MAILING_LIST'); ?></legend>
	<?php
	foreach ($this->form->getFieldset('mailinglist') as $field):
	?>
		<div class="control-group">
			<div class="control-label"><?php echo $field->label; ?></div>
			<div class="controls"><?php echo $field->input; ?></div>
		</div>
	<?php
	endforeach;
	?>
</fieldset>
