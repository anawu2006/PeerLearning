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
JHtml::_('formbehavior.chosen', 'select');
$mainframe = JFactory::getApplication();
$jinput = $mainframe->input;
?>

<div class="search<?php echo $this->pageclass_sfx; ?>">
	<div class="search-box-border">
		<?php if ($this->params->get('show_page_heading', 1)) : ?>
			<h1 class="page-title">
				<?php if ($this->escape($this->params->get('page_heading'))) : ?>
					<?php echo $this->escape($this->params->get('page_heading')); ?>
				<?php else : ?>
					<?php echo $this->escape($this->params->get('page_title')); ?>
				<?php endif; ?>
			</h1>
		<?php endif; ?>

		<?php echo $this->loadTemplate('form'); ?>
	</div>

	<!-- search intro -->
	<?php if (!empty($this->searchword)): ?>
		<div class="searchintro<?php echo $this->params->get('pageclass_sfx'); ?>">
			<p><?php echo JText::plural('COM_SEARCH_SEARCH_KEYWORD_N_RESULTS', '<span class="badge badge-info">' . $this->total . '</span>'); ?></p>
		</div>
	<?php endif; ?>

	<!-- form limit -->
	<?php if ($this->total > 0) : ?>
		<div class="form-limit">
			<label for="limit">
				<?php echo JText::_('JGLOBAL_DISPLAY_NUM'); ?>
			</label>
			<?php echo $this->pagination->getLimitBox(); ?>
			<p class="counter">
				<?php echo $this->pagination->getPagesCounter(); ?>
			</p>
		</div>
	<?php endif; ?>


	<?php 
	if ($this->error == null) :
		if (count($this->results) > 0 || $jinput->get('searchword', '', 'STRING') != '') :
			echo $this->loadTemplate('results');
		else:
			echo $this->loadTemplate('full');
		endif;
	else :
		echo $this->loadTemplate('error');
	endif; 
	?>
</div>
<script type="text/javascript">
jQuery(document).ready(function(){
	jQuery('input#hiddenlimit').val(jQuery('select#limit').val());
	jQuery('select#limit').bind('change', function(){
		jQuery('input#hiddenlimit').val(jQuery(this).val());
		jQuery('form#searchForm').submit();
	});
});
</script>