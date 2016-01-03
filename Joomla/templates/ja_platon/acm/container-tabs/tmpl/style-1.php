<?php
/**
 * ------------------------------------------------------------------------
 * JA Platon Template
 * ------------------------------------------------------------------------
 * Copyright (C) 2004-2011 J.O.O.M Solutions Co., Ltd. All Rights Reserved.
 * @license - Copyrighted Commercial Software
 * Author: J.O.O.M Solutions Co., Ltd
 * Websites:  http://www.joomlart.com -  http://www.joomlancers.com
 * This file may not be redistributed in whole or significant part.
 * ------------------------------------------------------------------------
 */
 
	$items_position = $helper->get('position');
	$mods = JModuleHelper::getModules($items_position);
?>
<div class="acm-container-tabs row" id="mod-<?php echo $module->id ?>">
	<div class="container-tabs-nav col-md-4">
		<!-- BEGIN: TAB NAV -->
		<ul class="nav nav-tabs" role="tablist">
			<?php
			$i = 0;
			foreach ($mods as $mod):
				?>
				<li class="<?php if ($i < 1) echo "active"; ?>">
					<a href="#mod-<?php echo $module->id ?> .mod-<?php echo $mod->id ?>" role="tab"
						 data-toggle="tab"><?php echo $mod->title ?></a>
				</li>
				<?php
				$i++;
			endforeach
			?>

		</ul>
		<!-- END: TAB NAV -->
	</div>

	<!-- BEGIN: TAB PANES -->
	<div class="tab-content col-md-8">
		<?php
		echo $helper->renderModules($items_position,
			array(
				'style'=>'ACMContainerItems',
				'active'=>0,
				'tag'=>'div',
				'class'=>'tab-pane fade'
			))
		?>
	</div>
	<!-- END: TAB PANES -->
</div>