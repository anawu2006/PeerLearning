<?php
/**
 * @author Antonio Durán Terrés
 * @package Joomdle
 * @license GNU/GPL
 *
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

// Import Joomla! libraries
jimport( 'joomla.application.component.view');
class JoomdleViewDefault extends JViewLegacy {

	function showButton( $link, $image, $text)
	{
			$lang           = JFactory::getLanguage();
			?>
				<a href="<?php echo $link; ?>">
					<div class="btn" style="width:140px;height:80px">
						<?php echo JHTML::_('image', 'administrator/components/com_joomdle/assets/images/' . $image , NULL, NULL ); ?>
						<br>
						<span><?php echo $text; ?></span>
					</div>
				</a>
			<?php
	}

	function renderAbout ()
	{
		$xmlfile = JPATH_ADMINISTRATOR.'/components/com_joomdle/manifest.xml';

		if (file_exists($xmlfile))
		{
			if ($data = JApplicationHelper::parseXMLInstallFile($xmlfile)) {
				$version =  $data['version'];
			}
		}


		$output = '<div style="padding: 5px;">';
		$output .= JText::sprintf('COM_JOOMDLE_ABOUT_TEXT_VERSION', $version);
		$output .= '<P>'.JText::sprintf('COM_JOOMDLE_ABOUT_TEXT_PROVIDES');
		$output .= '<P>'.JText::sprintf('COM_JOOMDLE_ABOUT_TEXT_SUPPORT');
		$output .= '<P>'.JText::sprintf('COM_JOOMDLE_ABOUT_SUBSCRIBE');
		$output .= '<P>'.JText::sprintf('COM_JOOMDLE_ABOUT_TEXT_JED');
		$output .= '</div>';

		return $output;
	}

	function display($tpl = null) {
		parent::display($tpl);
	}

}
?>
