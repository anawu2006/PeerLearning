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

defined('_JEXEC') or die;
$headright = $this->countModules('head-calendar or head-links') || $this->getParam('addon_offcanvas_enable');
?>

<!-- MAIN NAVIGATION -->
<nav id="t3-mainnav" class="t3-mainnav">
	<div class="navbar navbar-default">

		<!-- Brand and toggle get grouped for better mobile display -->
		<div class="navbar-header">
		
			<?php if ($this->getParam('navigation_collapse_enable', 1) && $this->getParam('responsive', 1)) : ?>
				<?php $this->addScript(T3_URL.'/js/nav-collapse.js'); ?>
				<button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".t3-navbar-collapse">
					<i class="fa fa-bars"></i>
				</button>
			<?php endif ?>

		</div>

		<div class="t3-navbar navbar-collapse collapse <?php if ($headright) echo 'col-xs-2 col-lg-9' ?>">
			<jdoc:include type="<?php echo $this->getParam('navigation_type', 'megamenu') ?>" name="<?php echo $this->getParam('mm_type', 'mainmenu') ?>" />
		</div>

		<?php if ($this->getParam('addon_offcanvas_enable')) : ?>
			<?php $this->loadBlock ('off-canvas') ?>
		<?php endif ?>

		<?php if ($headright): ?>
		<div class="col-xs-8 col-md-6 col-lg-3 pull-right">
			<?php if ($this->countModules('head-links')) : ?>
				<!-- HEAD LINKS -->
				<div class="head-right-item head-links">
					<jdoc:include type="modules" name="<?php $this->_p('head-links') ?>" />
				</div>
				<!-- //HEAD LINKS -->
			<?php endif ?>
		</div>
		<?php endif ?>

		<?php if ($this->getParam('navigation_collapse_enable')) : ?>
			<div class="t3-navbar-collapse navbar-collapse collapse"></div>
		<?php endif ?>
	</div>
</nav>
<!-- //MAIN NAVIGATION -->