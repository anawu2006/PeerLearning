<?php
/**
 * @version		
 * @package		Joomdle
 * @copyright	Copyright (C)  2008 - 2010 Antonio Duran Terres
 * @license		GNU/GPL, see LICENSE.php
 * Joomla! is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 * See COPYRIGHT.php for copyright notices and details.
 */

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );
require_once(JPATH_SITE.'/components/com_joomdle/helpers/content.php');
jimport('joomla.html.parameter');


class plgSearchCoursecategories extends JPlugin
{
    /**
     * @return array An array of search areas
     */
    function onContentSearchAreas()
    {
        return $this->plgSearchCoursecategoriesAreas ();
    }

    function onContentSearch($text, $phrase='', $ordering='', $areas=null)
    {
        return  $this->plgSearchCoursecategories( $text, $phrase, $ordering, $areas);
    }

	public function __construct(& $subject, $config)
    {
        parent::__construct($subject, $config);
        $this->loadLanguage();
    }

	/**
	 * @return array An array of search areas
	 */
	function &plgSearchCoursecategoriesAreas()
	{
		static $areas = array(
			'coursecategories' => 'COM_JOOMDLE_COURSECATEGORIES'
		);
		return $areas;
	}

	/**
	* Joomdle Course Categories Search method
	*
	* The sql must return the following fields that are used in a common display
	* routine: href, title, section, created, text, browsernav
	*/
	function plgSearchCourseCategories( $text, $phrase='', $ordering='', $areas=null )
	{
		$db		= JFactory::getDBO();
		$user	= JFactory::getUser();

		if (is_array( $areas )) {
			if (!array_intersect( $areas, array_keys( $this->plgSearchCoursecategoriesAreas() ) )) {
				return array();
			}
		}

		// load plugin params info
		$plugin = JPluginHelper::getPlugin('search', 'coursecategories');


		$params = JComponentHelper::getParams( 'com_joomdle' );
		$moodle_xmlrpc_server_url = $params->get( 'MOODLE_URL' ).'/mnet/xmlrpc/server.php';

		$limit     = $this->params->def('search_limit', 50);



		$text = trim( $text );
		if ($text == '') {
		return array();
		}

		$section = JText::_( 'COM_JOOMDLE_COURSECATEGORIES' );

		$rows = JoomdleHelperContent::call_method ("search_categories", $text,$phrase,$ordering,  (string) $limit);


		$i = 0;
		if (!is_array ($rows))
			return array();

		$rows_result = array ();
		foreach($rows as $key => $row) {
			$cat_slug = $row['cat_id'].":".$row['cat_name'];
			$rows_result[$i] = new JObject ();
			$rows_result[$i]->href = 'index.php?option=com_joomdle&view=coursecategory&cat_id='.$cat_slug;
			$rows_result[$i]->title= $row['cat_name'];
			$rows_result[$i]->section = JText::_('COM_JOOMDLE_COURSECATEGORIES');
			$rows_result[$i]->created = "";
			$rows_result[$i]->browsernav = '2'; // 1 = new window, 2 = same window
			$rows_result[$i]->text = $row['cat_description'];
			$i++;
		}

		return $rows_result;
		}
}
