<?if( $sg == 'banner' ):?>
	<?php if (JRequest::getVar('view') == 'frontpage'):?>
	<!-- SIDE BEGIN --><!-- SIDE END -->
	<?php endif?>
<?else:?>
 	<?php echo $app->getCfg('sitename'); ?>, Powered by <a href="http://joomla.org/" class="sgfooter" target="_blank">Joomla!</a>
	<?php $menu = &JSite::getMenu();
	$sgfrontpage = /*SGFRONTPAGE BEGIN*/1/*SGFRONTPAGE END*/;
	if ($menu->getActive() == $menu->getDefault() || $sgfrontpage):?>
		<!-- FOOTER BEGIN --><a href="https://www.siteground.es/hosting-web.htm">Hosting Web por SiteGround</a><!-- FOOTER END -->
	<?php endif ?>
<?endif;?>