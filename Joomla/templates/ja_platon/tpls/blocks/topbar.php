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

<?php if ($this->countModules('languageswitcherload or head-search or topbar')) : ?>
	<!-- TOPBAR -->
	<nav class="t3-topbar <?php $this->_c('topbar') ?>">
      <?php if ($this->countModules('languageswitcherload')) : ?>
        <!-- LANGUAGE SWITCHER -->
        <div class="topbar-left languageswitcherload col-xs-6 col-sm-4">
          <jdoc:include type="modules" name="<?php $this->_p('languageswitcherload') ?>" style="raw" />
        </div>
        <!-- //LANGUAGE SWITCHER -->
      <?php endif ?>
      <div class="topbar-right pull-right col-xs-6 col-sm-8">
         <?php if ($this->countModules('head-search')) : ?>
          <!-- HEAD SEARCH -->
          <div class="head-search">
            <jdoc:include type="modules" name="<?php $this->_p('head-search') ?>" />
          </div>
          <!-- //HEAD SEARCH -->
        <?php endif ?>

         <jdoc:include type="modules" name="<?php $this->_p('topbar') ?>" />
      </div>
	</nav>
	<!-- //TOPBAR -->
<?php endif ?>
