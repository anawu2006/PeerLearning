<?php
/**
 * ------------------------------------------------------------------------
 * JA Login module for J25 & J3x
 * ------------------------------------------------------------------------
 * Copyright (C) 2004-2011 J.O.O.M Solutions Co., Ltd. All Rights Reserved.
 * @license - GNU/GPL, http://www.gnu.org/licenses/gpl.html
 * Author: J.O.O.M Solutions Co., Ltd
 * Websites: http://www.joomlart.com - http://www.joomlancers.com
 * ------------------------------------------------------------------------
 */

// no direct access
defined('_JEXEC') or die('Restricted access');
/**
 * JA Login Module Helper
 *
 * @package		Joomla
 * @subpackage	Content
 * @since 		1.7
 */
class modJALoginHelper
{


    /**
     * Get url return after login or logout
     * @param object param of ja module
     * @param string $type
     * @return string url redirect
     */
    public static function getReturnURL($params, $type)
    {
        if ($itemid = $params->get($type)) {
			$menu = JFactory::getApplication()->getMenu();
            $item = $menu->getItem($itemid);
            if ($item) {
                $url = JRoute::_($item->link . '&Itemid=' . $itemid, false);
            } else {
                // stay on the same page
                $uri = JFactory::getURI();
                $url = $uri->toString(array('path', 'query', 'fragment'));
            }

        } else {
            // stay on the same page
            $uri = JFactory::getURI();
            $url = $uri->toString(array('path', 'query', 'fragment'));
        }

        return base64_encode($url);
    }


    /**
     * Get type user action
     * @return string type
     */
    public static function getType()
    {
        $user = JFactory::getUser();
        return (!$user->get('guest')) ? 'logout' : 'login';
    }
}
