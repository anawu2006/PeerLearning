<?php defined('_JEXEC') or die('Restricted access'); ?>

<?php
$itemid = JoomdleHelperContent::getMenuItem();
?>

<div class="joomdle-courserequestlist<?php echo $this->pageclass_sfx;?>">
	<?php if ($this->params->get('show_page_heading', 1)) : ?>
    <h1>
    <?php echo $this->escape($this->params->get('page_heading')); ?>
    </h1>
    <?php endif; ?>


<table width="100%" cellpadding="4" cellspacing="0" border="0" align="center" class="contentpane<?php echo $this->params->get( 'pageclass_sfx' ); ?>">
<tr>
        <th align="left" width="400" height="20" class="sectiontableheader<?php echo $this->params->get( 'pageclass_sfx' ); ?>">
                <?php echo JText::_('COM_JOOMDLE_COURSE'); ?>
        </th>
        <th align="left" width="200" height="20" class="sectiontableheader<?php echo $this->params->get( 'pageclass_sfx' ); ?>">
                <?php echo JText::_('COM_JOOMDLE_APPLICATION_DATE'); ?>
        </th>
        <th align="center" width="20" height="20" class="sectiontableheader<?php echo $this->params->get( 'pageclass_sfx' ); ?>">
                <?php echo JText::_('COM_JOOMDLE_STATE'); ?>
        </th>
</tr>

<?php
$odd= 0;
if (is_array ($this->cursos))
{
foreach ($this->cursos as  $curso) : ?>
<?php
?>
<tr class="sectiontableentry<?php echo $odd + 1; ?>">
                <?php $odd++; $odd = $odd % 2; ?>
        <td height="20">
			<?php echo  $curso['fullname']; ?>
        </td>
        <td height="20">
			<?php echo  $curso['application_date']; ?>
        </td>
        <td align="center"height="20">
<?php
			  switch ($curso['state'])
            {
                case JOOMDLE_APPLICATION_STATE_APPROVED:
					echo JHTML::_('image', 'joomdle/' . 'tick.png' , NULL, NULL, 'COM_JOOMDLE_APPROVED' );
                    break;
                case JOOMDLE_APPLICATION_STATE_REJECTED:
					 echo JHTML::_('image', 'joomdle/' . 'publish_r.png' , NULL, NULL, 'COM_JOOMDLE_REJECTED' );
                    break;
                default:
					echo JText::_('COM_JOOMDLE_PENDING');
                    break;
            }
?>
        </td>
</tr>
<?php endforeach; 
} ?>

</table>
</div>
