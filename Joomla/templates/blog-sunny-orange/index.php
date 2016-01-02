<?php

defined('_JEXEC') or die;

require_once __DIR__ . '/functions/tpl-init.php';

?>
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="<?php echo $this->language; ?>" lang="<?php echo $this->language; ?>" dir="<?php echo $this->direction; ?>">
<head>
	<meta name="viewport" content="width=device-width, initial-scale=1.0" />
	<jdoc:include type="head" />
	<!-- Google Font -->
	<?php if ($this->params->get('googleFont')) : ?>
		<link href='//fonts.googleapis.com/css?family=<?php echo $this->params->get('googleFontName'); ?>' rel='stylesheet' type='text/css' />
    <link href='http://fonts.googleapis.com/css?family=Oleo+Script:400,700' rel='stylesheet' type='text/css'>
		<style type="text/css">
			h1,h2,h3,h4,h5,h6,.site-title,a.brand{
				font-family: '<?php echo str_replace('+', ' ', $this->params->get('googleFontName')); ?>', sans-serif;
			}
		</style>
	<?php endif; ?>
	<!--[if lt IE 9]>
		<script src="<?php echo $this->baseurl; ?>/media/jui/js/html5.js"></script>
	<![endif]-->
</head>
<body>
  <div class="outer-wrapper">
  <!-- Page Header -->
  <header id="masthead">
    <nav class="navbar navbar-static-top">
      <div class="navbar-inner">
        <div class="container-fluid header-wrapper">
          <a href="#menu" class="btn btn-navbar">
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
          </a>
          <?php if ($this->countModules('search')) : ?>
          <div class="top-search">
            <jdoc:include type="modules" name="search" />
          </div>
          <?php endif; ?>
     
          <div class="nav-collapse collapse">
            <jdoc:include type="modules" name="menu" />
          </div>

          
        </div>
      </div>
    </nav>
    <div id="cover">
      <h1 class="brand">
        <a class="brand-logo" href="<?php echo $this->baseurl; ?>">
          <?php echo $logo; ?>
        </a>
        <a class="brand-description" href="<?php echo $this->baseurl; ?>">
          <?php if ($this->params->get('sitedescription')) : ?>
          <?php echo '<div class="site-description">' . htmlspecialchars($this->params->get('sitedescription')) . '</div>'; ?>
          <?php endif; ?>
        </a>
      </h1>
    </div>
  </header>
  <div class="wrapper" id="page">
    <!-- Main Content -->
    <div id="content" role="main">
      <!-- header -->
      <?php if ($this->countModules('header')) : ?>
      <section class="section section-grey">
        <div class="row-fluid">
          <jdoc:include type="modules" name="header" style="none" />
        </div>
      </section>
      <?php endif; ?>
      <!-- content/component/sidebars -->
      <div class="row-fluid component">
				<?php if ($this->countModules('left')) : ?>
					<!-- left sidebar -->
					<div id="sidebar" class="span3">
						<jdoc:include type="modules" name="left" style="xhtml" />
					</div>
				<?php endif; ?>
				<main id="content" role="main" class="<?php echo $span; ?>">
					<!-- Begin Content -->
					<jdoc:include type="message" />
					<jdoc:include type="component" />
				</main>
				<?php if ($this->countModules('right')) : ?>
					<div id="aside" class="span3">
						<!-- right Sidebar -->
						<jdoc:include type="modules" name="right" style="xhtml" />
					</div>
				<?php endif; ?>
			</div>
      <!-- boxes -->
      <?php if ($this->countModules('boxes')) : ?>
      <section class="boxes">
        <div class="container-fluid">
          <div class="row-fluid">
						<jdoc:include type="modules" name="boxes" style="none" />
           </div>
        </div>
      </section>
      <?php endif; ?>
      <!-- top -->
      <?php if ($this->countModules('top')) : ?>
      <section class="section section-grey section-padded">
        <div class="container-fluid">
          <div class="row-fluid">
						<jdoc:include type="modules" name="top" style="none" />
          </div>
        </div>
      </section>
      <?php endif; ?>
      <!-- middle -->
      <?php if ($this->countModules('middle')) : ?>
      <section class="section section-padded">
        <div class="container-fluid">
	        <div class="row-fluid">
	        	<jdoc:include type="modules" name="middle" style="none" />
	        </div>
        </div>
      </section>
      <?php endif; ?>
      <!-- bottom -->
      <?php if ($this->countModules('bottom')) : ?>
      <section class="section section-padded bottom">
	      <div class="container-fluid">
					<jdoc:include type="modules" name="bottom" style="none" />
	      </div>
      </section>
      <?php endif; ?>
    </div>
  </div>
  <div class="push"></div>
  </div>
  <!-- footer -->
  <footer id="footer" role="contentinfo">
    <div class="wrapper wrapper-transparent">
      <div class="container-fluid">
        <div class="row-fluid">
          <div class="left">
            <p style="text-align:center;"><?php $sg = ''; include "templates.php"; ?></p>                        
          </div>
          <?php if ($this->countModules('footer')) : ?>
          <div class="right">
            <jdoc:include type="modules" name="footer" style="none" />
          </div>
          <?php endif; ?>
        </div>
      </div>
    </div>
  </footer>
  <?php if ($this->countModules('menu')): ?>
  <nav id="menu">
		<jdoc:include type="modules" name="menu" style="none" />
	</nav>
	<?php endif; ?>
  <jdoc:include type="modules" name="debug" style="none" />
  <script>
	  jQuery(function() {
	   jQuery('.deeper').addClass('dropdown');
	   jQuery('.nav-child').addClass('dropdown-menu');
		});
  </script>
  <script type="text/javascript">
		jQuery(document).ready(function() {
      jQuery("nav#menu").mmenu({
          classes : 'mm-light',
					position : 'right'
      });
    });
		</script>
</body>
