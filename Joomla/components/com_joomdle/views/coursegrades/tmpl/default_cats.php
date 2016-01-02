<?php defined('_JEXEC') or die('Restricted access'); ?>

<?php
$itemid = JoomdleHelperContent::getMenuItem();
?>
<div class="joomdle-gradelist<?php echo $this->pageclass_sfx;?>">
    <h1>
            <?php echo $this->course_info['fullname'] ; ?>
    </h1>

<?php
$app        = JFactory::getApplication();
$params = $app->getParams();

$use_pdf_integration = $params->get('use_pdf_integration');

if ($use_pdf_integration) : ?>
<P align="right">
<a href="index.php?option=com_joomdle&view=coursegrades&course_id=<?php echo $this->course_info['remoteid']; ?>&format=pdf"><img src="<?php echo JURI::root(); ?>/media/media/images/mime-icon-16/pdf.png" alt="PDF"></a>
</P>
<?php endif; ?>

<table width="100%" cellpadding="4" cellspacing="0" border="0" align="center" class="simpletable<?php echo $this->params->get( 'pageclass_sfx' ); ?>">
<tr>
        <td width="30%" height="20" class="sectiontableheader<?php echo $this->params->get( 'pageclass_sfx' ); ?>">
                <?php echo JText::_('COM_JOOMDLE_CATEGORY'); ?>
        </td>
        <td width="30%" height="20" class="sectiontableheader<?php echo $this->params->get( 'pageclass_sfx' ); ?>" style="text-align:center;" nowrap="nowrap">
                <?php echo JText::_('COM_JOOMDLE_ASSESSMENT_TITLE'); ?>
        </td>
        <td width="5%" height="20" class="sectiontableheader<?php echo $this->params->get( 'pageclass_sfx' ); ?>" style="text-align:center;" nowrap="nowrap">
                <?php echo JText::_('COM_JOOMDLE_DUE_DATE'); ?>
        </td>
        <td width="5%" height="20" class="sectiontableheader<?php echo $this->params->get( 'pageclass_sfx' ); ?>" style="text-align:center;" nowrap="nowrap">
                <?php echo JText::_('COM_JOOMDLE_MAXIMUM_MARKS'); ?>
        </td>
        <td width="5%" height="20" class="sectiontableheader<?php echo $this->params->get( 'pageclass_sfx' ); ?>" style="text-align:center;" nowrap="nowrap">
                <?php echo JText::_('COM_JOOMDLE_MARK'); ?>
        </td>
        <td width="15%" height="20" class="sectiontableheader<?php echo $this->params->get( 'pageclass_sfx' ); ?>" style="text-align:center;" nowrap="nowrap">
                <?php echo JText::_('COM_JOOMDLE_FEEDBACK'); ?>
        </td>
</tr>

<?php
$i = 0;
$odd = 0;


$total = array_shift ($this->gcats['data']);
if (is_array($this->gcats))
foreach ($this->gcats['data'] as  $gcat) : 
	$n = count ($gcat['items']);
?>
                <?php
					$cat_shown =  false;
					foreach ($gcat['items'] as $item) :
					?>
					<tr>
						<?php
							if (!$cat_shown) :
							?>
							<td rowspan="<?php echo $n + 1; ?>" valign="top">
							<?php
								echo $gcat['fullname']; ?>
								<br>
								<?php printf ("%d", $gcat['grademax']); ?>% <?php echo JText::_('COM_JOOMDLE_OF_TOTAL');
								$cat_shown = true;
								?>
							</td>
							<?php endif; ?>
						<td width="30%">
							<?php echo $item['name']; ?>
						</td>
						<td width="5%" style="text-align:center;">
							<?php 
								if ($item['due'])
									echo JHTML::_('date', $item['due'] , JText::_('DATE_FORMAT_LC4'));
							?>      
						</td>
						<td width="5%"  style="text-align:center;">
							<?php printf ("%d", $item['grademax']); ?>%
						</td>
						<td width="5%">
							<?php if ($this->gcats['config']['showlettergrade']) :?>
								<?php echo $item['letter']; ?>
							<?php else : ?>
								<?php printf ("%d", $item['finalgrade']); ?>%
							<?php endif; ?>
						</td>
						<td width="15%">
							<?php echo $item['feedback']; ?>
						</td>
					</tr>
					<?php
				endforeach;

				// Category totals
				?>
				<tr>
                    <td>
							<?php echo JText::_('COM_JOOMDLE_CATEGORY_TOTAL'); ?>
                    </td>
					<td>
					</td>
					<td align="center">
					<?php printf ("%d", $gcat['grademax']); ?>%
					</td>
					<td>
					<?php if ($this->gcats['config']['showlettergrade']) :?>
						<?php echo $gcat['letter']; ?>
					<?php else : ?>
						<?php printf ("%d", $gcat['finalgrade']); ?>%
					<?php endif; ?>
					</td>
                </tr>

<?php endforeach; ?>

<?php
				// Course total
?>
				<tr>
                    <td>
							<?php echo JText::_('COM_JOOMDLE_SUBJECT_TOTAL'); ?>
                    </td>
					<td>
					</td>
					<td>
					</td>
					<td align="center">
					<?php printf ("%d", $total['grademax']); ?>%
					</td>
					<td align="center">
					<?php if ($this->gcats['config']['showlettergrade']) :?>
						<?php echo $total['letter']; ?>
					<?php else : ?>
						<?php printf ("%d", $total['finalgrade']); ?>%
					<?php endif; ?>
					</td>
                </tr>
</table>

<?php if ($this->params->get('show_back_links')) : ?>
    <div>
    <P align="center">
    <a href="javascript: history.go(-1)"><?php echo JText::_('COM_JOOMDLE_BACK'); ?></a>
    </P>
    </div>
<?php endif; ?>

</div>
