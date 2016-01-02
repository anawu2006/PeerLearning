<?php
defined('_JEXEC') or die('Restricted access');
jimport( 'joomla.html.html.tabs' );

JToolBarHelper::title(JText::_('Joomdle'), 'generic.png');

?>

       <table class="adminlist">

             <tbody>
	     	<tr>
			<td width="55%" valign="top">
			<div id="cpanel">
			<table>
			<tr>
				<td>
			<?php
			$link = 'index.php?option=com_joomdle&amp;view=config';
			$this->showButton( $link, 'config.png', JText::_( 'COM_JOOMDLE_CONFIGURATION' ) );
			?>
				</td>
				<td>
			<?php
			$link = 'index.php?option=com_joomdle&amp;view=users';
			$this->showButton( $link, 'users.png', JText::_( 'COM_JOOMDLE_USERS' ) );
			?>
				</td>
				<td>
			<?php
			$link = 'index.php?option=com_joomdle&amp;view=mappings';
			$this->showButton( $link, 'mappings.png', JText::_( 'COM_JOOMDLE_DATA_MAPPINGS' ) );
			?>
				</td>
			</tr>
			<tr>
				<td>
			<?php
			$link = 'index.php?option=com_joomdle&amp;view=customprofiletypes';
			$this->showButton( $link, 'profiletypes.png', JText::_( 'COM_JOOMDLE_CUSTOM_PROFILETYPES' ) );
			?>
				</td>
				<td>
			<?php
			$link = 'index.php?option=com_joomdle&amp;view=shop';
			$this->showButton( $link, 'vmart.png', JText::_( 'COM_JOOMDLE_SHOP_INTEGRATION' ) );
			?>
				</td>
				<td>
			<?php
			$link = 'index.php?option=com_joomdle&amp;view=mailinglist';
            $this->showButton( $link, 'lists.png', JText::_( 'COM_JOOMDLE_MAILING_LIST_INTEGRATION' ) );
			?>
				</td>
			</tr>
			<tr>
				<td>
			<?php
			$link = 'index.php?option=com_joomdle&amp;view=courseapplications';
			$this->showButton( $link, 'applications.png', JText::_( 'COM_JOOMDLE_APPLICATIONS' ) );
			?>
				</td>
				<td>
			<?php
	//		$link = 'index.php?option=com_joomdle&amp;view=forums';
	//		$this->showButton( $link, 'forum.png', JText::_( 'COM_JOOMDLE_FORUMS' ) );

	//		echo '<div style="clear: both;" />';
			$link = 'index.php?option=com_joomdle&amp;view=check';
			$this->showButton( $link, 'info.png', JText::_( 'COM_JOOMDLE_SYSTEM_CHECK' ) );
			?>
				</td>
			</tr>
			</table>
			</div>
			</td>
			<td width="45%" valign="top">
			<div style="width: 100%">
<?php
			$title = JText::_("COM_JOOMDLE_ABOUT");
			$options = array ();
			 echo JHTML :: _ ('tabs.start', 'content-panel', $options);
			 echo JHTML :: _ ('tabs.panel', $title, 'content-panel');

			$renderer = 'renderAbout';
			echo $this->$renderer();
 
			 echo JHTML :: _ ('tabs.end');
?>

			</div>
			</td>
		</tr>
             </tbody>
       </table>
 <input type="hidden" name="option" value="<?php echo JRequest::getVar( 'option' );?>"/>
       <input type="hidden" name="task" value=""/>
       <input type="hidden" name="boxchecked" value="0"/>
       <input type="hidden" name="hidemainmenu" value="0"/>

                <?php echo JHTML::_( 'form.token' ); ?>

