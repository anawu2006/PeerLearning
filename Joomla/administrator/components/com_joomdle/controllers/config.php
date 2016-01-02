<?php
/**
 * @version     1.0.0
 * @package     com_joomdle
 * @copyright   Copyright (C) 2012. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access
defined('_JEXEC') or die;

jimport('joomla.application.component.controllerform');

/**
 * Item controller class.
 */
class JoomdleControllerConfig extends JControllerForm
{
    protected $text_prefix = 'COM_JOOMDLE_CONFIG';

	function __construct() {
        $this->view_list = 'default';
        parent::__construct();
    }

}
