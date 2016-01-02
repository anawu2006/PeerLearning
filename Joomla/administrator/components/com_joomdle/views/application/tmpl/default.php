<?php
defined('_JEXEC') or die('Restricted access');

JToolBarHelper::title(JText::_('Joomdle'), 'generic.png');

$course_id = $this->app_info['course_id'];

?>
<form action="index.php?option=com_joomdle&view=courseapplications" method="POST"  id="adminForm" name="adminForm">
  <?php if(!empty( $this->sidebar)): ?>
        <div id="j-sidebar-container" class="span2">
        <?php echo $this->sidebar; ?>
        </div>
        <div id="j-main-container" class="span10">
    <?php else : ?>
        <div id="j-main-container">
    <?php endif;?>

<?php
echo "<b>".JText::_('User').": </b>".$this->app_info['name'] . "<br>";
echo "<b>".JText::_('COM_JOOMDLE_COURSE').": </b>".$this->app_info['course'] . "<br>";
echo "<b>".JText::_('COM_JOOMDLE_APPLICATION_DATE').": </b>".$this->app_info['application_date'] . "<br>";
echo "<b>".JText::_('COM_JOOMDLE_CONFIRMATION_DATE').": </b>".$this->app_info['confirmation_date'] . "<br>";
echo "<b>".JText::_('COM_JOOMDLE_STATUS').": </b>";
switch ($this->app_info['state'])
{
	case JOOMDLE_APPLICATION_STATE_APPROVED:
		echo '<img src="images/tick.png" width="16" height="16" border="0" alt="" />';
		break;
	case JOOMDLE_APPLICATION_STATE_REJECTED:
		echo '<img src="images/publish_x.png" width="16" height="16" border="0" alt="" />';
		break;
	default:
		echo JText::_('COM_JOOMDLE_PENDING');
}
echo "<br>";

echo "<b>".JText::_('COM_JOOMDLE_MOTIVATION').": </b><br>".$this->app_info['motivation'] . "<br>";
echo "<b>".JText::_('COM_JOOMDLE_EXPERIENCE').": </b><br>".$this->app_info['experience'] . "<br>";

?>
<input type="hidden" name="task" value=""/>
<input type="hidden" name="app_id" value="<?php echo $this->app_info['id']; ?>"/>
<input type="hidden" name="course_id" value="<?php echo $this->app_info['course_id']; ?>"/>
</form>

