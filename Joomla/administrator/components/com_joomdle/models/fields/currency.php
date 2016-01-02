<?php
/**
* @copyright    Copyright (C) 2008 - 2010 Antonio Duran Terres
* @license      GNU/GPL
*/
 
// Check to ensure this file is within the rest of the framework
defined('JPATH_BASE') or die();
require_once(JPATH_SITE.'/components/com_joomdle/helpers/content.php');

JFormHelper::loadFieldClass('list');

 
class JFormFieldCurrency extends JFormFieldList
{
        /**
        * Element name
        *
        * @access       protected
        * @var          string
        */
        public    $type = 'Currency';

        function getOptions()
        {
			$currencies = array('USD' => 'US Dollars',
				  'EUR' => 'Euros',
				  'JPY' => 'Japanese Yen',
				  'GBP' => 'British Pounds',
				  'CAD' => 'Canadian Dollars',
				  'AUD' => 'Australian Dollars'
				 );

			$options = array ();
			$c = array ();
			foreach ($currencies as $val => $text)
			{
				$options[] = JHtml::_('select.option', $val, $text);
			}

			return $options;
        }
}

?>
