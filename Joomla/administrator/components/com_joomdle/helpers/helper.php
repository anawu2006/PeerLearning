<?php
/**
 * Joomdle
 *
 * @author Antonio Durán Terrés
 * @package Joomdle
 * @license GNU/GPL
 *
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

class JoomdleHelper {


	public static function addSubmenu($vName)
    {
        JHtmlSidebar::addEntry(
            JText::_('COM_JOOMDLE_CONTROL_PANEL'),
            'index.php?option=com_joomdle',
            $vName == 'default'
        );
        JHtmlSidebar::addEntry(
            JText::_('COM_JOOMDLE_CONFIGURATION'),
            'index.php?option=com_joomdle&view=config',
            $vName == 'config'
        );
        JHtmlSidebar::addEntry(
            JText::_('COM_JOOMDLE_USERS'),
            'index.php?option=com_joomdle&view=users',
            $vName == 'users'
        );
        JHtmlSidebar::addEntry(
            JText::_('COM_JOOMDLE_MAPPINGS'),
            'index.php?option=com_joomdle&view=mappings',
            $vName == 'mappings'
        );
        JHtmlSidebar::addEntry(
            JText::_('COM_JOOMDLE_CUSTOM_PROFILES'),
            'index.php?option=com_joomdle&view=customprofiletypes',
            $vName == 'customprofiletypes'
        );
        JHtmlSidebar::addEntry(
            JText::_('COM_JOOMDLE_APPLICATIONS'),
            'index.php?option=com_joomdle&view=courseapplications',
            $vName == 'courseapplications'
        );
        JHtmlSidebar::addEntry(
            JText::_('COM_JOOMDLE_SHOP'),
            'index.php?option=com_joomdle&view=shop',
            $vName == 'shop'
        );
        JHtmlSidebar::addEntry(
            JText::_('COM_JOOMDLE_MAILING_LIST'),
            'index.php?option=com_joomdle&view=mailinglist',
            $vName == 'mailinglist'
        );
        JHtmlSidebar::addEntry(
            JText::_('COM_JOOMDLE_FORUMS'),
            'index.php?option=com_joomdle&view=forums',
            $vName == 'forums'
        );
        JHtmlSidebar::addEntry(
            JText::_('COM_JOOMDLE_SYSTEM_CHECK'),
            'index.php?option=com_joomdle&view=check',
            $vName == 'check'
        );


    }

}
?>
