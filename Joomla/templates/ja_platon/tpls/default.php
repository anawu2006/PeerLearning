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

<!DOCTYPE html>
<html lang="<?php echo $this->language; ?>" dir="<?php echo $this->direction; ?>"
	  class='<jdoc:include type="pageclass" />'>

<head>
	<jdoc:include type="head" />
	<?php $this->loadBlock('head') ?>
    <?php $this->addCss('layouts/docs') ?>
</head>

<body <?php if ($this->countModules('slideshow')) echo 'class="has-slideshow"'; ?>>

<div class="t3-wrapper"> <!-- Need this wrapper for off-canvas menu. Remove if you don't use of-canvas -->
  <div class="container">
    <?php $this->loadBlock('topbar') ?>

    <?php $this->loadBlock('header') ?>

    <?php $this->loadBlock('mainnav') ?>

    <?php $this->loadBlock('slideshow') ?>

    <?php $this->loadBlock('spotlight-1') ?>

    <?php $this->loadBlock('spotlight-2') ?>

    <?php $this->loadBlock('sections') ?>

    <?php $this->loadBlock('mainbody') ?>

    <?php $this->loadBlock('spotlight-3') ?>

    <?php $this->loadBlock('navhelper') ?>

    <?php $this->loadBlock('spotlight-4') ?>

    <?php $this->loadBlock('footer') ?>
  </div>
</div>

</body>

</html>