<?php
$fields = $form->getFieldset($name);
?>

<div class="jatools-row clearfix">
<?php foreach ($fields as $field) : ?>
	<?php $layouts = $field->element['layouts'] ? ' data-layouts="' . $field->element['layouts'] . '"' : ''; ?>
	<div class="control-group"<?php echo $layouts ?>>
		<div class="control-label"><?php echo $field->getLabel() ?></div>
		<div class="controls"><?php echo $field->getInput() ?></div>
	</div>
<?php endforeach ?>
</div>
