<?php defined('_JEXEC') or die('Restricted access'); ?>

<?php
$itemid = JoomdleHelperContent::getMenuItem();
?>
<div class="joomdle-gradelist<?php echo $this->pageclass_sfx;?>">
    <h1>
            <?php echo $this->course['fullname']; ?>
    </h1>
<?php
$app        = JFactory::getApplication();
$params = $app->getParams();

$use_pdf_integration = $params->get('use_pdf_integration');

if ($use_pdf_integration) : ?>
<P align="right">
<a href="index.php?option=com_joomdle&view=coursegrades&course_id=<?php echo $this->course['remoteid']; ?>&format=pdf"><img src="<?php echo JURI::root(); ?>/media/media/images/mime-icon-16/pdf.png" alt="PDF"></a>
</P>
<?php endif; ?>

<table width="100%" cellpadding="4" cellspacing="0" border="0" align="center" class="simpletable<?php echo $this->params->get( 'pageclass_sfx' ); ?>">
<tr>
        <td width="30%" height="20" class="simpletable<?php echo $this->params->get( 'pageclass_sfx' ); ?>">
               <b><?php echo JText::_('COM_JOOMDLE_CATEGORY'); ?></b>
        </td>
        <td width="30%" height="20" class="simpletable<?php echo $this->params->get( 'pageclass_sfx' ); ?>" style="text-align:center;" nowrap="nowrap">
                <b><?php echo JText::_('COM_JOOMDLE_ASSESSMENT_TITLE'); ?></b>
        </td>
        <td width="5%" height="20" class="simpletable<?php echo $this->params->get( 'pageclass_sfx' ); ?>" style="text-align:center;" nowrap="nowrap">
                <b><?php echo JText::_('COM_JOOMDLE_DUE_DATE'); ?></b>
        </td>
        <td width="5%" height="20" class="simpletable<?php echo $this->params->get( 'pageclass_sfx' ); ?>" style="text-align:center;" nowrap="nowrap">
                <b><?php echo JText::_('COM_JOOMDLE_MAXIMUM_MARKS'); ?></b>
        </td>
        <td width="10%" height="20" class="simpletable<?php echo $this->params->get( 'pageclass_sfx' ); ?>" style="text-align:center;" nowrap="nowrap">
                <b><?php echo JText::_('COM_JOOMDLE_MARK'); ?></b>
        </td>
        <td width="15%" height="20" class="simpletable<?php echo $this->params->get( 'pageclass_sfx' ); ?>" style="text-align:center;" nowrap="nowrap">
                <b><?php echo JText::_('COM_JOOMDLE_FEEDBACK'); ?></b>
        </td>
</tr>

<?php
$i = 0;
$odd = 0;

$this->gcats = $this->course['grades'];
$total = array_shift ($this->gcats['data']);
if (is_array($this->gcats['data']))
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
							<td rowspan="<?php echo $n + 1; ?>" valign="top" class="simpletable<?php echo $this->params->get( 'pageclass_sfx' ); ?>">
							<?php
								echo $gcat['fullname']; ?>
								<br>
								<?php printf ("%d", $gcat['grademax']); ?>% <?php echo JText::_('COM_JOOMDLE_OF_TOTAL');
								$cat_shown = true;
								?>
							</td>
							<?php endif; ?>
						<td width="30%" class="simpletable<?php echo $this->params->get( 'pageclass_sfx' ); ?>">
							<?php echo $item['name']; ?>
						</td>
						<td width="5%" style="text-align:center;" class="simpletable<?php echo $this->params->get( 'pageclass_sfx' ); ?>">
							<?php 
								if ($item['due'])
									echo JHTML::_('date', $item['due'] , JText::_('DATE_FORMAT_LC4'));
							?>      
						</td>
						<td width="5%"  style="text-align:center;" class="simpletable<?php echo $this->params->get( 'pageclass_sfx' ); ?>">
							<?php printf ("%d", $item['grademax']); ?>%
						</td>
						<td width="10%" align="center" class="simpletable<?php echo $this->params->get( 'pageclass_sfx' ); ?>">
							<?php 
								if ($item['finalgrade'])
								{
									if ($this->gcats['config']['showlettergrade'])
										echo $item['letter'];
									else
									{
										printf ("%d", $item['finalgrade']); 
										echo "%";
									}
								}
								else
									echo "-";
							?>

						</td>
						<td width="15%" class="simpletable<?php echo $this->params->get( 'pageclass_sfx' ); ?>">
							<?php echo $item['feedback']; ?>
						</td>
					</tr>
					<?php
				endforeach;

				// Category totals
				if ($n > 0) :
				?>
				<tr>
                    <td class="simpletable<?php echo $this->params->get( 'pageclass_sfx' ); ?>">
							<?php echo JText::_('COM_JOOMDLE_CATEGORY_TOTAL'); ?>
                    </td>
					<td class="simpletable<?php echo $this->params->get( 'pageclass_sfx' ); ?>">
					</td>
					<td align="center" class="simpletable<?php echo $this->params->get( 'pageclass_sfx' ); ?>">
					<?php 
						if ($gcat['grademax'])
						{
							printf ("%d", $gcat['grademax']); 
							echo "%";
						}
					?>
					</td>
					<td align="center" class="simpletable<?php echo $this->params->get( 'pageclass_sfx' ); ?>">
					<?php 
						if ($gcat['finalgrade'])
						{
								if ($this->gcats['config']['showlettergrade'])
									echo $item['letter'];
								else
								{
									printf ("%d", $item['finalgrade']); 
									echo "%";
								}
						}
					?>
					</td>
                </tr>

				<?php endif; ?>
<?php endforeach; ?>

<?php
				// Course total
?>
				<tr>
                    <td class="simpletable<?php echo $this->params->get( 'pageclass_sfx' ); ?>">
							<?php echo JText::_('COM_JOOMDLE_SUBJECT_TOTAL'); ?>
                    </td>
					<td class="simpletable<?php echo $this->params->get( 'pageclass_sfx' ); ?>">
					</td>
					<td class="simpletable<?php echo $this->params->get( 'pageclass_sfx' ); ?>">
					</td>
					<td align="center" class="simpletable<?php echo $this->params->get( 'pageclass_sfx' ); ?>">
					<?php printf ("%d", $total['grademax']); ?>%
					</td>
					<td align="center" class="simpletable<?php echo $this->params->get( 'pageclass_sfx' ); ?>">
					<?php
							if ($this->gcats['config']['showlettergrade'])
								echo $total['letter'];
							else
							{
								printf ("%d", $total['finalgrade']); 
								echo "%";
							}
					?>
					</td>
					<td class="simpletable<?php echo $this->params->get( 'pageclass_sfx' ); ?>">
					</td>
                </tr>
</table>

</div>
<P>
&nbsp;
</P>
