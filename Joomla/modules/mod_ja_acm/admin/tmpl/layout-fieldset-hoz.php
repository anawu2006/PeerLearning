<?php
$fields = $form->getFieldset($name);
$width = 100 / count ($fields);
$width = 'width="' . $width . '%"';
?>

<table>
	<tr>
	<?php foreach ($fields as $field) : ?>
		<th <?php echo $width ?>><?php echo $field->getLabel() ?></th>
	<?php endforeach ?>
	</tr>
	<tr class="jatools-row clearfix">
		<?php foreach ($fields as $field) : ?>
			<td>
				<?php echo $field->getInput() ?>
			</td>
		<?php endforeach ?>
	</tr>
</table>
