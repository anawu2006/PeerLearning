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
?>

<!-- FOOTER -->
<footer id="t3-footer" class="t3-footer">

  	<?php if ($this->checkSpotlight('footnav', 'footer-7, footer-8, footer-9, footer-10')) : ?>
  		<!-- FOOT NAVIGATION -->
  			<?php $this->spotlight('footnav', 'footer-7, footer-8, footer-9, footer-10') ?>
  		<!-- //FOOT NAVIGATION -->
  	<?php endif ?>

  	<section class="t3-copyright">
  		<div class="row">
  			<div class="<?php echo $this->getParam('t3-rmvlogo', 1) ? 'col-sm-10 col-md-8' : 'col-md-12' ?> copyright <?php $this->_c('footer') ?>">
  				<jdoc:include type="modules" name="<?php $this->_p('footer') ?>" />
  			</div>
  			<?php if ($this->getParam('t3-rmvlogo', 1)): ?>
  				<div class="col-sm-2 col-md-4 poweredby text-hide">
  					<a class="t3-logo t3-logo-small t3-logo-light" href="http://t3-framework.org" title="<?php echo JText::_('T3_POWER_BY_TEXT') ?>"
  					   target="_blank" <?php echo method_exists('T3', 'isHome') && T3::isHome() ? '' : 'rel="nofollow"' ?>><?php echo JText::_('T3_POWER_BY_HTML') ?></a>
  				</div>
  			<?php endif; ?>
  		</div>
  	</section>
</footer>
<!-- //FOOTER -->