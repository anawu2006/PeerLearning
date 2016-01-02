<?php
/**
 * @package     Joomdle
 *
 * @copyright   Copyright (C) Antonio Duran Terres
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

// Load tooltips behavior
JHtml::_('behavior.formvalidation');
JHtml::_('behavior.tooltip');
JHtml::_('formbehavior.chosen', 'select');
?>
<script type="text/javascript">
	Joomla.submitbutton = function(task)
	{
		if (task == 'application.cancel' || document.formvalidator.isValid(document.id('application-form'))) {
			Joomla.submitform(task, document.getElementById('application-form'));
		}
	}
</script>

<form action="<?php echo JRoute::_('index.php?option=com_joomdle&view=config');?>" id="application-form" method="post" name="adminForm" class="form-validate">
  <?php if(!empty( $this->sidebar)): ?>
        <div id="j-sidebar-container" class="span2">
        <?php echo $this->sidebar; ?>
        </div>
        <div id="j-main-container" class="span10">
    <?php else : ?>
        <div id="j-main-container">
    <?php endif;?>

	<div class="row-fluid">
		<!-- Begin Content -->
		<div class="span10">
			<ul class="nav nav-tabs">
				<li class="active"><a href="#page-general" data-toggle="tab"><?php echo JText::_('COM_JOOMDLE_GENERAL_CONFIG');?></a></li>
				<li><a href="#page-links" data-toggle="tab"><?php echo JText::_('COM_JOOMDLE_LINKS_BEHAVIOUR');?></a></li>
				<li><a href="#page-views" data-toggle="tab"><?php echo JText::_('COM_JOOMDLE_CONFIG_VIEWS');?></a></li>
				<li><a href="#page-shop" data-toggle="tab"><?php echo JText::_('COM_JOOMDLE_SHOP');?></a></li>
				<li><a href="#page-userprofiles" data-toggle="tab"><?php echo JText::_('COM_JOOMDLE_USER_PROFILES');?></a></li>
				<li><a href="#page-integrations" data-toggle="tab"><?php echo JText::_('COM_JOOMDLE_INTEGRATIONS');?></a></li>
				<li><a href="#page-applications" data-toggle="tab"><?php echo JText::_('COM_JOOMDLE_APPLICATIONS');?></a></li>
			</ul>
			<div id="config-document" class="tab-content">
				<div id="page-general" class="tab-pane active">
					<div class="row-fluid">
						<div class="span6">
							<?php echo $this->loadTemplate('general'); ?>
						</div>
						<div class="span6">
						</div>
					</div>
				</div>
				<div id="page-links" class="tab-pane">
					<div class="row-fluid">
						<div class="span12">
							<?php echo $this->loadTemplate('links'); ?>
						</div>
					</div>
				</div>
				<div id="page-views" class="tab-pane">
					<div class="row-fluid">
						<div class="span6">
							<?php echo $this->loadTemplate('actionbuttons'); ?>
							<?php echo $this->loadTemplate('topics'); ?>
							<?php echo $this->loadTemplate('coursecategory'); ?>
							<?php echo $this->loadTemplate('course'); ?>
							<?php echo $this->loadTemplate('backlinks'); ?>
						</div>
						<div class="span6">
							<?php echo $this->loadTemplate('detailview'); ?>
							<?php echo $this->loadTemplate('coursesabc'); ?>
						</div>
					</div>
				</div>
				<div id="page-shop" class="tab-pane">
					<div class="row-fluid">
						<div class="span12">
							<?php echo $this->loadTemplate('shop'); ?>
						</div>
					</div>
				</div>
				<div id="page-userprofiles" class="tab-pane">
					<div class="row-fluid">
						<div class="span6">
							<?php echo $this->loadTemplate('datasource'); ?>
							<?php echo $this->loadTemplate('profiletypes'); ?>
							<?php echo $this->loadTemplate('usergroups'); ?>
						</div>
						<div class="span6">
							<?php echo $this->loadTemplate('activities'); ?>
							<?php echo $this->loadTemplate('points'); ?>
							<?php echo $this->loadTemplate('socialgroups'); ?>
						</div>
					</div>
				</div>
				<div id="page-integrations" class="tab-pane">
					<div class="row-fluid">
						<div class="span6">
							<?php echo $this->loadTemplate('mailinglist'); ?>
							<?php echo $this->loadTemplate('pdf'); ?>
							<?php echo $this->loadTemplate('kunena'); ?>
						</div>
						<div class="span6">
						</div>
					</div>
				</div>
				<div id="page-applications" class="tab-pane">
					<div class="row-fluid">
						<div class="span6">
							<?php echo $this->loadTemplate('applications'); ?>
						</div>
						<div class="span6">
						</div>
					</div>
				</div>
				<input type="hidden" name="task" value="" />
				<?php echo JHtml::_('form.token'); ?>
			</div>
			</div>
		<!-- End Content -->
	</div>
</form>
