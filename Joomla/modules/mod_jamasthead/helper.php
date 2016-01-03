<?php
/**
 * ------------------------------------------------------------------------
 * JA Masthead Module for J25 & J34
 * ------------------------------------------------------------------------
 * Copyright (C) 2004-2011 J.O.O.M Solutions Co., Ltd. All Rights Reserved.
 * @license - GNU/GPL, http://www.gnu.org/licenses/gpl.html
 * Author: J.O.O.M Solutions Co., Ltd
 * Websites: http://www.joomlart.com - http://www.joomlancers.com
 * ------------------------------------------------------------------------
 */
// no direct access
defined('_JEXEC') or die('Restricted access');
jimport('joomla.application.component.model');
jimport('joomla.html.parameter');

require_once (JPATH_SITE . '/components/com_content/helpers/route.php');
/**
 *
 * JA Masthead HELPER CLASS
 * @author JoomlArt
 *
 */
class ModJAMastheadHelper
{
    protected $_item = array();


    /**
     *
     * reference to the global ModJAMastheadHelper object
     * @Returns a reference to the global ModJAMastheadHelper object
     */
    static function getInstance()
    {
        static $instance = null;
        if (!$instance) {
            $instance = new ModJAMastheadHelper();
        }
        return $instance;
    }


    /**
     *
     * Get all information of Masthead
     * @param object $params
     * @return Array
     */
    public function getMasthead($params)
    {
        //global $mainframe;
        $Masthead 			= array();
        $Masthead['title'] 	= '';
        $Masthead['description'] = '';
        $Masthead['params'] = array();
        
        //default title & description in configuration
        $default_title 			= trim($params->get('default-title'));
        $default_description 	= trim($params->get('default-description'));
        
        //config use for menuitem 
        $config_menuItem = $params->get('menuitem_config');
        
        //config use for option
        $config_option = $params->get('option_config');
        
        $config = '';
        if(!empty($config_menuItem->item_id) && is_array($config_menuItem->item_id)){
            if(count($config_menuItem->item_id)){
                for($i = 0; $i < count($config_menuItem->item_id); $i++){
                    if(isset($config_menuItem->item_id[$i]))
                    {
                        $config .= '[Masthead Itemid="'. $config_menuItem->item_id[$i].'" title="'.$config_menuItem->title[$i].'" background="'.$config_menuItem->background[$i].'"]'.$config_menuItem->description[$i].'[/Masthead]';    
                    }
                }
            }    
        }else if((!empty($config_menuItem->item_id)) && (is_object($config_menuItem->item_id))){
            foreach($config_menuItem as $menuitem){
                foreach($menuitem as $index => $value){
                    if($menuitem->{$index}){
                        $config .= '[Masthead Itemid="'. $config_menuItem->item_id->{$index}.'" title="'.$config_menuItem->title->{$index}.'" background="'.$config_menuItem->background->{$index}.'"]'.$config_menuItem->description->{$index}.'[/Masthead]';
                    }
                }
            }
        }
        if($config_option && $config_option != NULL){
            for($j = 0; $j < count($config_option->option); $j++){
                $config .= '[Masthead option="'. $config_option->option[$j].'" title="'.$config_option->title[$j].'"';
				if($config_option->view[$j]){
					$config .= ' view="'.$config_menuItem->view[$j].'"';
				}
				if($config_option->layout[$j]){
					$config .= ' layout="'.$config_menuItem->layout[$j].'"';
				}
				
				if($config_option->task[$j]){
					$config .= ' task="'.$config_menuItem->task[$j].'"';
				}
				$config .= 'Itemid="'.$config_option->id[$j].'" background="'.$config_option->background[$j].'"]'.$config_option->description[$j].'[/Masthead]';
            }
        }
        //get the inputs from request
        $view 	= JRequest::getCmd('view');
        $option = JRequest::getCmd('option');
        $layout = JRequest::getCmd('layout');
        $task 	= JRequest::getCmd('task');
        $id 	= JRequest::getVar('id');
        $Itemid = JRequest::getInt('Itemid');

        if (isset($config) && ($config != '')) {

            //support for multiple language
            $configArr 	 = preg_split('/<lang=([^>]*)>/', $config, -1, PREG_SPLIT_NO_EMPTY | PREG_SPLIT_DELIM_CAPTURE);
            $description = '';
            
            //if language configured
            if (count($configArr) > 1) {

                //get the atribute configured for current language
                for ($i = 0; $i < count($configArr); $i = $i + 2) {
                    if ($configArr[$i] == $iso_client_lang) {
                        $description = $configArr[($i + 1)];
                        break;
                    }
                }
                //not found, get the first one
                if (!$description) {
                    $description = $configArr[1];
                }

            } else if (isset($configArr[0])) {

                //all languages are configured the same
                $description = $configArr[0];

            }
            
            //parse the configuration
            $configArr = $this->parseDescNew($description);

            foreach ($configArr as $config) {
                if(isset($config['Itemid'])){
                    $ItemidArray = explode(',', $config['Itemid']) ;
                    if (!empty($ItemidArray) && in_array($Itemid, $ItemidArray)) {

                        //if config for current page found
                        $Masthead['title'] = @$config['title'];
                        $Masthead['description'] = @$config['description'];
						$Masthead['params'] = $config;

                        //don't need check for other condition if found
                        break;

                    }
                }
                
                if (isset($config['option']) && ($config['option'] != '')) {

                    //if config for current component found
                    if ($config['option'] == $option || "com_".$config['option'] == $option) {

                        $check = true;

                        //check if not match view/layout/task/id
                        if ((($config['view'] != '') 	&& ($config['view'] != $view)) ||
                            (($config['layout'] != '') 	&& ($config['layout'] != $layout)) ||
                            (($config['task'] != '') 	&& ($config['task'] != $task)) ||
                            (($config['id'] != '') 		&& ($config['id'] != $id))) {

                            $check = false;

                        }

                        if ($check) {

                            $Masthead['title'] 			= $config['title'];
                            $Masthead['description'] 	= $config['description'];

							$Masthead['params'] = $config;
                           //don't need check for other condition if found
                            //break;
                        }
                    }
                }

            }
        }

        //not specific configured, detect title & desc base on input
        if (!$Masthead['title'] && !$Masthead['description']) {

            $id = JRequest::getInt('id');

            if (($option == 'com_content') && ($view == 'article')) {

                //Get title & desc if this is article view
                $item = $this->loadArticle($id, $params);
				if ($item) {
					$Masthead['title'] = trim($item->title);
					$Masthead['description'] = trim($item->metadesc);
				}
            } else {

                //get from page title or default title configured in module
				$app	= JFactory::getApplication();
				$menus	= $app->getMenu();

				// Because the application sets a default page title,
				// we need to get it from the menu item itself
				$menu = $menus->getActive();

				if($menu && $menu->params->get('page_heading', '') != '') {
					$Masthead['title'] = $menu->params->get('page_heading', '');
				}

            }
        }

        //default value if empty
        if (!$Masthead['title']) {
            $Masthead['title'] = $default_title;
            if (!$Masthead['description']) {
                $Masthead['description'] = $default_description;
            }
        }
        return $Masthead;
    }


    /**
     * Parse the description to array
     * description in format
     * Format 1: [Masthead option="com_name" view="view_name" layout="layout_name" task="task_name" id="id" title="Title" ]Description here[/Masthead]
     * Format 2: [Masthead Itemid="page_id" title="Title" ]Description here[/Masthead]
     * @param string $description
     * @return array
     */
    public function parseDescNew($description)
    {

        $regex = '#\[Masthead ([^\]]*)\]([^\[]*)\[/Masthead\]#m';
        preg_match_all($regex, $description, $matches, PREG_SET_ORDER);
        $descriptionArray = array();

        foreach ($matches as $match) {

            $params = $this->parseParams($match[1]);
            $description = $match[2];

            if (is_array($params)) {

                if (isset($params['option'])) {
					$params['view'] 	= isset($params['view']) 	? trim($params['view']) : '';
					$params['layout'] 	= isset($params['layout']) 	? trim($params['layout']) : '';
					$params['task'] 	= isset($params['task']) 	? trim($params['task']) : '';
					$params['id'] 		= isset($params['id']) 		? trim($params['id']) : '';
					$params['title'] 	= isset($params['title']) 	? trim($params['title']) : '';
				}
                $params['description'] = $description;

				$descriptionArray[] = $params;
            }
        }
        
        return $descriptionArray;
    }


    /**
     *
     * Parse Params
     * @param string $string
     * @return array
     */
    public function parseParams($string)
    {
        $string = html_entity_decode($string, ENT_QUOTES);
        $regex = "/\s*([^=\s]+)\s*=\s*('([^']*)'|\"([^\"]*)\"|([^\s]*))/";
        $params = null;
        if (preg_match_all($regex, $string, $matches)) {
            for ($i = 0; $i < count($matches[1]); $i++) {
                $key = $matches[1][$i];
                $value = $matches[3][$i] ? $matches[3][$i] : ($matches[4][$i] ? $matches[4][$i] : $matches[5][$i]);
                $params[$key] = $value;
            }
        }
        return $params;
    }


    /**
     *
     * Load Article title and metadesc
     * @param int $id
     * @param object $params
     * @return object
     */
    public function loadArticle($id, $params)
    {
		if (!$id) {
			return;
		}

        $mainframe = JFactory::getApplication();

        // Get the dbo
        $db = JFactory::getDbo();

        // Get an instance of the generic articles model
        
		if (version_compare(JVERSION, '3.0', 'ge')) {
			$model = JModelLegacy::getInstance('Article', 'ContentModel', array('ignore_request' => true));
		} else if (version_compare(JVERSION, '2.5', 'ge')) {
		   	$model = JModel::getInstance('Article', 'ContentModel', array('ignore_request' => true));
		} else {
			
			$model = JModel::getInstance('Article', 'ContentModel', array('ignore_request' => true));
		}
        // Set application parameters in model
        $appParams = $mainframe->getParams();

        $model->setState('params', $appParams);

        $model->setState('filter.published', 1);
		$model->setState('filter.archived',2);

        $access = !JComponentHelper::getParams('com_content')->get('show_noauth');
        $authorised = JAccess::getAuthorisedViewLevels(JFactory::getUser()->get('id'));
        $model->setState('filter.access', $access);

        $data = $model->getItem($id);

        return $data;

    }
}
