<?php
/**
 * Joomla! 1.5 component Joomdle
 *
 * @version $Id: router.php 2009-04-17 03:54:05 svn $
 * @author Antonio Durán Terrés
 * @package Joomla
 * @subpackage Joomdle
 * @license GNU/GPL
 *
 * Shows information about Moodle courses
 *
 * This component file was created using the Joomla Component Creator by Not Web Design
 * http://www.notwebdesign.com/joomla_component_creator/
 *
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

/*
 * Function to convert a system URL to a SEF URL
 */
function JoomdleBuildRoute(&$query) {
       $segments = array();

	if (empty($query['Itemid'])) {

		if(isset($query['view']))
		{
			$segments[] = $query['view'];
			unset( $query['view'] );
		}
		if(isset($query['cat_id']))
		{
			$segments[] = $query['cat_id'];
			unset( $query['cat_id'] );
		};
		if(isset($query['course_id']))
		{
			$segments[] = $query['course_id'];
			unset( $query['course_id'] );
		};
		if(isset($query['username']))
        {
            $segments[] = $query['username'];
            unset( $query['username'] );
        };
		if(isset($query['start_chars']))
        {
            $segments[] = $query['start_chars'];
            unset( $query['start_chars'] );
        };
		if(isset($query['page_id']))
        {
            $segments[] = $query['page_id'];
            unset( $query['page_id'] );
        };
		if(isset($query['id']))
        {
            $segments[] = $query['id'];
            unset( $query['id'] );
        };
	} else {

		if(isset($query['view']))
		{
			$segments[] = $query['view'];
			unset( $query['view'] );
		} 
		if(isset($query['cat_id']))
		{
			$segments[] = $query['cat_id'];
			unset( $query['cat_id'] );
		};
		if(isset($query['course_id']))
		{
			$segments[] = $query['course_id'];
			unset( $query['course_id'] );
		};
		if(isset($query['username']))
        {
            $segments[] = $query['username'];
            unset( $query['username'] );
        };
		if(isset($query['start_chars']))
        {
            $segments[] = $query['start_chars'];
            unset( $query['start_chars'] );
        };
		if(isset($query['page_id']))
        {
            $segments[] = $query['page_id'];
            unset( $query['page_id'] );
        };
        if(isset($query['id']))
        {
            $segments[] = $query['id'];
            unset( $query['id'] );
        };
	} 
       return $segments;
}
/*
 * Function to convert a SEF URL back to a system URL
 */
function JoomdleParseRoute($segments) {
	$vars = array();


        //Get the active menu item
		$app        = JFactory::getApplication();
		$menu       = $app->getMenu();

        $item = $menu->getActive();


        // Count route segments
        $count = count($segments);

		if( !isset($item) )
        {
                if( $count > 0 )
                {
                        // If there are no menus we try to use the segments
                        $vars['view']  = $segments[0];

                        if(!empty($segments[1]))
                        {
                                $vars['task'] = $segments[1]; 
                        }
                        if (array_key_exists ($count - 2, $segments))
							$vars['cat_id']    = $segments[$count-2];
                        if (array_key_exists ($count - 1, $segments))
						{
							$vars['course_id'] = $segments[$count-1];
							$vars['start_chars'] = $segments[$count-1];
							$vars['username'] = $segments[$count-1];
							$vars['cat_id'] = $segments[$count-1];
						}


						if ($vars['view'] == 'page')
						{
							$vars['course_id'] = $segments[$count-2];
							$vars['page_id'] = $segments[$count-1];
						}

					return $vars;
                }
        }



		if (!$item)
			$item->query['view'] = 'detail';

	    $requestedview = isset($segments[0])? $segments[0] : $item->query['view'];
//  print_r("<br />view: ". $requestedview . "<br />");

		$vars['view'] = $requestedview;
        switch($requestedview)
        {
                case 'coursecategories' :
                {
                        if($count == 2) {
							$vars['view'] = $segments[$count-2];
							$vars['cat_id']    = $segments[$count-1];
                        }

                        if($count == 3) {
                               // $vars['view']  = 'teachers', topics, coursestats, coursegradecategories;
                                $vars['view']  = $segments[$count-3];
                                $vars['cat_id'] = $segments[$count-2];
								$vars['course_id']    = $segments[$count-1];
                        }


                } break;

                case 'mycourses'   :
                case 'teachers'   :
                case 'topics'   :
                case 'coursegradecategories'   :
                case 'coursesbycategory'   :
                case 'coursecategory'   :
                {
                        if($count == 3) {
                               // $vars['view']  = 'teachers', topics, coursestats, coursegradecategories;
                                $vars['view']  = $segments[$count-3];
                                $vars['cat_id'] = $segments[$count-2];
                                $vars['course_id']    = $segments[$count-1];
                        }
                        if($count == 2) {
                                $vars['view']  = $segments[$count-2];
                                $vars['cat_id'] = $segments[$count-1];
                        }

                } break;

                case 'joomdle'   :
                {
                        if($count == 2) { // Usado por el pathway
                                $vars['view']  = 'coursecategory';
								$vars['cat_id']    = $segments[$count-1];
								$vars['start_chars']    = $segments[$count-1];
								$vars['username']    = $segments[$count-1];
								$vars['course_id']    = $segments[$count-1];
								$vars['view']  = $segments[$count-2];
                        }

                        if($count == 3) {
                               // $vars['view']  = 'teachers', topics, coursestats, coursegradecategories;
                                $vars['view']  = $segments[$count-3];
                                $vars['cat_id'] = $segments[$count-2];
								$vars['course_id']    = $segments[$count-1];

/*								if ($vars['view'] == 'page')
								{
									$vars['page_id']    = $segments[$count-1];
									$vars['course_id']    = $segments[$count-2];
								}
  */
                      }

                } break;

                case 'detail'   :
                {
                        if($count == 2) {
                                $vars['view']  = 'detail';
								$vars['cat_id']    = $segments[$count-2];
                                $vars['course_id'] = $segments[$count-1];

				/*				if ($segments[0] == 'course')
								{
									$vars['view']  = 'course';
									$vars['course_id'] = $segments[1];
								}
								if ($segments[0] == 'coursemates')
								{
									$vars['view']  = 'coursemates';
									$vars['course_id'] = $segments[1];
								}
*/
                        }
                        if($count == 3) {
                               // $vars['view']  = 'teachers', topics, coursestats, coursegradecategories;
                                $vars['view']  = $segments[$count-3];
                                $vars['cat_id'] = $segments[$count-2];
								$vars['course_id']    = $segments[$count-1];
                        }
/*						if ($vars['view'] == 'page')
						{
							$vars['page_id']    = $segments[$count-1];
							$vars['course_id']    = $segments[$count-2];
						}
*/
                } break;

                case 'mycoursegrades' :
                {
                        if($count == 3) {
                                $vars['view']  = 'coursegrades';
                                $vars['cat_id'] = $segments[$count-2];
								$vars['course_id']    = $segments[$count-1];
				}
                } break;

                case 'stats' :
                {
                        if($count == 3) {
                                $vars['view']  = 'coursestats';
                                $vars['cat_id'] = $segments[$count-2];
								$vars['course_id']    = $segments[$count-1];
						}
                }

                case 'coursestats' :
                {
                        if($count == 3) {
                                $vars['view']  = 'coursestats';
                                $vars['cat_id'] = $segments[$count-2];
								$vars['course_id']    = $segments[$count-1];
						}
                }

				case 'coursesabc' :
                {
                        if($count == 2) {
                                $vars['view'] = $segments[$count-2];
                                $vars['start_chars'] = $segments[$count-1];
                        }
                } break;
                case 'teachersabc' :
                {
                        if($count == 2) {
                                $vars['view'] = $segments[$count-2];
                                $vars['start_chars'] = $segments[$count-1];
                        }
                } break;
                case 'teacher' :
                {
                        if($count == 2) {
                                $vars['view'] = $segments[$count-2];
                                $vars['username'] = $segments[$count-1];
                        }
                } break;

                case 'buycourse' :
                case 'course' :
                case 'coursenews' :
                case 'courseevents' :
                case 'coursegrades' :
                case 'coursemates' :
                {
                        if($count == 3) {
                               // $vars['view']  = 'teachers', topics, coursestats, coursegradecategories;
                                $vars['view']  = $segments[$count-3];
                                $vars['cat_id'] = $segments[$count-2];
								$vars['course_id']    = $segments[$count-1];
                        }
                        if($count == 2) {
                                $vars['view'] = $segments[0];
                                $vars['course_id'] = $segments[$count-1];
								$vars['page_id']    = $segments[$count-1];

}
	/*							if ($segments[0] == 'course')
								{
									$vars['view']  = 'course';
									$vars['course_id'] = $segments[1];
								}
								if ($segments[0] == 'coursemates')
								{
									$vars['view']  = 'coursemates';
									$vars['course_id'] = $segments[1];
								}

						}
						if ($vars['view'] == 'page')
						{
							$vars['page_id']    = $segments[$count-1];
							$vars['course_id']    = $segments[$count-2];
						}
*/

                } 
				break;

                case 'page' :
                {
                        if($count == 3) {
                                $vars['view'] = $segments[0];
                                $vars['course_id'] = $segments[1];
								$vars['page_id']    = $segments[2];
						}
                } break;

                case 'newsitem' :
                {
                        if($count == 3) {
                                $vars['view'] = $segments[0];
                                $vars['course_id'] = $segments[1];
                                $vars['id']    = $segments[2];
                        }
                } break;
                case 'wrapper' :
                {
                     //   if($count == 3) {
                                $vars['view'] = $segments[0];
                                $vars['mtype'] = $segments[1];
                                $vars['id']    = $segments[2];
                     //   }
                } break;
        }

	return $vars;

}
?>
