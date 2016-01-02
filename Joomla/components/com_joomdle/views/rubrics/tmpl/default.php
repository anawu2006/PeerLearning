<?php defined('_JEXEC') or die('Restricted access'); ?>

<?php
$itemid = JoomdleHelperContent::getMenuItem();
?>
<div class="joomdle-gradelist<?php echo $this->pageclass_sfx;?>">
    <h1>
            <?php echo $this->rubrics['assign_name']; ?>
    </h1>

<table width="100%" cellpadding="4" cellspacing="0" border="1" align="center" class="simpletable<?php echo $this->params->get( 'pageclass_sfx' ); ?>">

<?php
$i = 0;
$odd = 0;
if (is_array($this->rubrics['definitions']))
foreach ($this->rubrics['definitions'] as  $rubric) : 
?>
<tr class="sectiontableentry<?php echo $odd + 1; ?>">
                <?php $odd++; $odd = $odd % 2; ?>
        <td width="25%" height="20" align="left" colspan="20" >
                <?php echo $rubric['definition']; ?>
        </td>
</tr>
                <?php
					foreach ($rubric['criteria'] as $item) :
					?>
					<tr>
						<td width="25%">
							<b>
							<?php echo $item['description']; ?>
							</b>
						</td>
						<?php 
						foreach ($item['levels'] as $level) : 
						?>
						<td width="10%">
							<?php 
								echo $level['definition'];
								echo "<br>";
								echo $level['score'] . " " .  JText::_('COM_JOOMDLE_POINTS');
							?>      
						</td>
						<?php endforeach; ?>
					</tr>
					<?php
					endforeach;
				?>
</tr>
<?php endforeach; ?>
</table>

<?php if ($this->params->get('show_back_links')) : ?>
    <div>
    <P align="center">
    <a href="javascript: history.go(-1)"><?php echo JText::_('COM_JOOMDLE_BACK'); ?></a>
    </P>
    </div>
<?php endif; ?>

</div>
