<?php
// no direct access
defined('_JEXEC') or die('Restricted access');

class modJoomdleCoursesHelper 
{
	static function filter_by_value ($array, $index, $value)
	{
		$newarray = array ();
		if(is_array($array) && count($array)>0)
		{
			foreach(array_keys($array) as $key)
			{
				if (array_key_exists ($index, $array[$key]))
					$temp[$key] = $array[$key][$index];
				else $temp[$key] = 0;

				if (in_array ($temp[$key] ,$value)){
					$newarray[$key] = $array[$key];
				}
			}
		}
		return $newarray;
	}


}
