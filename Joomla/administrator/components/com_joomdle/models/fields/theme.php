<?php
/**
* @copyright    Copyright (C) 2008 - 2010 Antonio Duran Terres
* @license      GNU/GPL
*/
 
// Check to ensure this file is within the rest of the framework
defined('JPATH_BASE') or die();
require_once(JPATH_SITE.'/components/com_joomdle/helpers/content.php');

JFormHelper::loadFieldClass('list');

 
class JFormFieldTheme extends JFormFieldList
{
        /**
        * Element name
        *
        * @access       protected
        * @var          string
        */
        public    $type = 'Theme';

        function getOptions()
        {
			$options = array ();
			$val = '';
			$text = JText::_ ('COM_JOOMDLE_DEFAULT');
			$options[] = JHtml::_('select.option', $val, $text);

			// If Joomdle not configured, return
			$params = JComponentHelper::getParams( 'com_joomdle' );
			if ($params->get( 'MOODLE_URL' ) == "")
				return $options;

			// If any fatal error in system check, return
			if (!JoomdleHelperContent::system_ok ())
				return $options;

			$themes = JoomdleHelperContent::call_method ('get_themes');
			$c = array ();

			foreach ($themes as $theme)
			{
				$val = $theme['name'];
				$text = $theme['name'];
				$options[] = JHtml::_('select.option', $val, $text);
			}

			return $options;
        }
}

?>
