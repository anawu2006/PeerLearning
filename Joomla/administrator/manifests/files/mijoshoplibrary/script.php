<?php
/**
* @package		MijoShop
* @copyright	2009-2013 Miwisoft LLC, miwisoft.com
* @license		GNU/GPL http://www.gnu.org/copyleft/gpl.html
*/

defined('_JEXEC') or die('Restricted access');

// Import Libraries
jimport('joomla.application.helper');
jimport('joomla.filesystem.file');
jimport('joomla.installer.installer');

class MijoshopLibraryInstallerScript {
	
	public function preflight($type, $parent) {
		if (!JFile::exists(JPATH_ROOT.'/components/com_mijoshop/mijoshop/mijoshop.php')) {
			JLog::add('MijoShop component (com_mijoshop) is not installed. Please, install it first.', JLog::ERROR, 'jerror');
			
			return false;
		}
	}
	
	public function postflight($type, $parent) {
        $src = $parent->getParent()->getPath('source');
		
		$_lib_zip = $src.'/library.zip';
		$_lib_gzip = $src.'/library.tar.gz';

		$_dir = JPath::clean(JPATH_ROOT.'/components/com_mijoshop/opencart');
		

		if (JFile::exists($_lib_zip)) {
			$_file = JPath::clean($_lib_zip);
		}
		else if (JFile::exists($_lib_gzip)) {
			$_file = JPath::clean($_lib_gzip);
		}

		JArchive::extract($_file, $_dir);
		
		if (JFile::exists(JPATH_ROOT.'/components/com_mijoshop/library.zip')) {
			JFile::delete(JPATH_ROOT.'/components/com_mijoshop/library.zip');
		}
		
		if (JFile::exists(JPATH_ROOT.'/components/com_mijoshop/mijoshoplibrary.xml')) {
			JFile::delete(JPATH_ROOT.'/components/com_mijoshop/mijoshoplibrary.xml');
		}
		
		if (JFile::exists(JPATH_ROOT.'/components/com_mijoshop/script.php')) {
			JFile::delete(JPATH_ROOT.'/components/com_mijoshop/script.php');
		}
	}
}