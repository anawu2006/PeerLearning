<?php
/**
 * @version		
 * @package		Joomdle
 * @subpackage	Content
 * @copyright	Copyright (C) Antonio Duran Terres
 * @license		GNU/GPL, see LICENSE.php
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

jimport('joomla.user.helper');
require_once(JPATH_ADMINISTRATOR.'/components/com_joomdle/helpers/content.php');
require_once(JPATH_ADMINISTRATOR.'/components/com_joomdle/helpers/parents.php');

class JoomdleHelperShop
{

	static function is_course_on_sell ($course_id)
	{
		$params = JComponentHelper::getParams( 'com_joomdle' );
		$shop = $params->get( 'shop_integration' );

		if (!$shop)
			return false;

		$on_sell = false;
		JPluginHelper::importPlugin( 'joomdleshop' );
		$dispatcher = JDispatcher::getInstance();
		$result = $dispatcher->trigger('onIsCourseOnSell', array($course_id));
		foreach ($result as $on_sell)
		{
			if ($on_sell !== FALSE) // We check for FALSE, as returned by non configured plugins
				break;
		}

		return $on_sell;
	}

	static function getShopCourses ()
	{
		$params = JComponentHelper::getParams( 'com_joomdle' );
		$shop = $params->get( 'shop_integration' );

		$courses = array ();
		JPluginHelper::importPlugin( 'joomdleshop' );
		$dispatcher = JDispatcher::getInstance();
		$result = $dispatcher->trigger('onGetShopCourses', array());
		foreach ($result as $courses)
		{
			if (count ($courses))
				break;
		}

		return $courses;
	}

	static function get_bundles ()
	{
		$db           = JFactory::getDBO();
        $query = 'SELECT * ' .
            ' FROM #__joomdle_bundles' ;
		$db->setQuery($query);
		$data = $db->loadAssocList();

		if (!$data)
			$data = array ();
		
		$i = 0;
		$c = array ();
		foreach ($data as $bundle)
		{
				$c[$i] = new JObject ();
				$c[$i]->id = $bundle['id'];
				$c[$i]->name = $bundle['name'];
				$c[$i]->description = $bundle['description'];
				$c[$i]->cost = $bundle['cost'];
				$c[$i]->currency = $bundle['currency'];
				$c[$i]->published = JoomdleHelperShop::is_course_on_sell ('bundle_'.$bundle['id']);
				$i++;
		}

		return $c;
	}

	static function get_sell_url ($course_id)
	{
		$params = JComponentHelper::getParams( 'com_joomdle' );
		$shop = $params->get( 'shop_integration' );

		JPluginHelper::importPlugin( 'joomdleshop' );
		$dispatcher = JDispatcher::getInstance();
		$result = $dispatcher->trigger('onGetSellUrl', array ($course_id));
		foreach ($result as $url)
		{
			if ($url != '')
				break;
		}
		$shop_itemid = $params->get( 'shop_itemid' );
		if ($shop_itemid)
			$url .= "&Itemid=$shop_itemid";

		return $url;
	}

	static function publish_courses ($courses)
	{
		foreach ($courses as $course_id)
		{
			$course_array = array ($course_id);
			if (JoomdleHelperShop::is_course_on_sell ($course_id))
				JoomdleHelperShop::dont_sell_courses ($course_array);
			else
				JoomdleHelperShop::sell_courses ($course_array);
		}
	}

	static function sell_courses ($courses)
	{
		$params = JComponentHelper::getParams( 'com_joomdle' );
		$shop = $params->get( 'shop_integration' );

		JPluginHelper::importPlugin( 'joomdleshop' );
		$dispatcher = JDispatcher::getInstance();
		$dispatcher->trigger('onSellCourses', array ($courses));
	}

	static function dont_sell_courses ($courses)
	{
		$params = JComponentHelper::getParams( 'com_joomdle' );
		$shop = $params->get( 'shop_integration' );

		JPluginHelper::importPlugin( 'joomdleshop' );
		$dispatcher = JDispatcher::getInstance();
		$dispatcher->trigger('onDontSellCourses', array ($courses));
	}

	static function reload_courses ($courses)
	{
		$params = JComponentHelper::getParams( 'com_joomdle' );
		$shop = $params->get( 'shop_integration' );

		JPluginHelper::importPlugin( 'joomdleshop' );
		$dispatcher = JDispatcher::getInstance();
		$dispatcher->trigger('onReloadCourses', array ($courses));
	}

	static function delete_courses ($courses)
	{
		$params = JComponentHelper::getParams( 'com_joomdle' );
		$shop = $params->get( 'shop_integration' );

		$db           = JFactory::getDBO();
		foreach ($courses as $sku)
		{
			if (strncmp ($sku, 'bundle_', 7) == 0)
			{
				$bundle_id = substr ($sku, 7);
				$query = "DELETE FROM  #__joomdle_bundles  where id = " . $db->Quote($bundle_id);
				$db->setQuery($query);
				if (!$db->query()) {
					return JError::raiseWarning( 500, $db->getError() );
				 }
			}
		}

		JPluginHelper::importPlugin( 'joomdleshop' );
		$dispatcher = JDispatcher::getInstance();
		$dispatcher->trigger('onDeleteCourses', array ($courses));
	}

	static function create_bundle ($bundle)
	{
		$params = JComponentHelper::getParams( 'com_joomdle' );
		$shop = $params->get( 'shop_integration' );

		// Insert record in bundles table
		$db           = JFactory::getDBO();


		$b->courses = implode (',', $bundle['courses']);
		$b->name = $bundle['name'];
		$b->description = $bundle['description'];
		$b->cost = $bundle['cost'];
		$b->currency = $bundle['currency'];

		 /* Update record */
        if (array_key_exists ('id', $bundle))
		{
			$b->id = $bundle['id'];
            $db->updateObject ('#__joomdle_bundles', $b, 'id');
		}
        else
        {
            /* Insert new record */
            $db->insertObject ('#__joomdle_bundles', $b);
			$bundle['id'] = $db->insertid();
        }


		JPluginHelper::importPlugin( 'joomdleshop' );
		$dispatcher = JDispatcher::getInstance();
		$dispatcher->trigger('onCreateBundle', array ($bundle));
	}

	static function get_bundle_info ($bundle_id)
	{
		$db           = JFactory::getDBO();
        $query = 'SELECT * ' .
            ' FROM #__joomdle_bundles' .
			  " WHERE id = " . $db->Quote($bundle_id);
		$db->setQuery($query);
		$data = $db->loadAssoc();

		return $data;
	}

	/* General functions */
	static function send_confirmation_email ($email, $course_id)
	{
		$app = JFactory::getApplication();

		$comp_params = JComponentHelper::getParams( 'com_joomdle' );
		$linkstarget = $comp_params->get( 'linkstarget' );
		$moodle_url = $comp_params->get( 'MOODLE_URL' );
		$email_subject = $comp_params->get( 'enrol_email_subject' );
		$email_text = $comp_params->get( 'enrol_email_text' );


		if ($linkstarget == 'wrapper')
		{
			/* XXX After and hour tryng and searching I could not find the GOOD way
			   to do this, so I do this kludge and it seems to work ;) 
			   */
			$url            = JURI::base();
			$pos =  strpos ($url, '/administrator/');
			if ($pos)
				$url = substr ($url, 0, $pos);
			$url = trim ($url, '/');
			$url            = $url.'/index.php?option=com_joomdle&view=wrapper&moodle_page_type=course&id='.$course_id;
//			$default_itemid = $comp_params->get( 'default_itemid' );
//			$url .= "&Itemid=".$default_itemid;
		} else {
			$url = $moodle_url.'/course/view.php?id='.$course_id;
		}

		$course_info = JoomdleHelperContent::getCourseInfo ((int) $course_id);
		$name = $course_info['fullname'];

		$email_text = str_replace ('COURSE_NAME', $name, $email_text);
		$email_text = str_replace ('COURSE_URL', $url, $email_text);
		$email_subject = str_replace ('COURSE_NAME', $name, $email_subject);
		$email_subject = str_replace ('COURSE_URL', $url, $email_subject);


		// Set the e-mail parameters
		$from           = $app->getCfg('mailfrom');
        $fromname       = $app->getCfg('fromname');


		// Send the e-mail
		if (!JFactory::getMailer()->sendMail($from, $fromname, $email, $email_subject, $email_text))
		{
				return false;
		}

		return true;
	}

    static function enrol_bundle ($username, $sku)
    {
		$user_id = JUserHelper::getUserId ($username);
        $user = JFactory::getUser ($user_id);
		$email =  $user->email;

        $bundle_id = substr ($sku, 7);
        $bundle = JoomdleHelperShop::get_bundle_info ($bundle_id);
        $courses = explode (',', $bundle['courses']);

		$comp_params = JComponentHelper::getParams( 'com_joomdle' );
		$send_bundle_emails = $comp_params->get( 'send_bundle_emails' );
        $c = array ();
        foreach ($courses as $course_id)
        {
			if ($send_bundle_emails)
				JoomdleHelperShop::send_confirmation_email ($email, $course_id);
            $course['id'] = (int) $course_id;
            $c[] = $course;
        }
    
        JoomdleHelperContent::call_method ('multiple_enrol', $username, $c, 5);
    }
}
