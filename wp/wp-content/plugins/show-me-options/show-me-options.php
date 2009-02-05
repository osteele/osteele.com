<?php

// pluginname Show Me Options
// shortname ShowMeOptions
// dashname show-me-options

/*
Plugin Name: Show Me Options
Version: 0.2
Plugin URI: http://www.prelovac.com/vladimir/wordpress-plugins/show-me-options
Author: Vladimir Prelovac
Author URI: http://www.prelovac.com/vladimir
Description: Allows you to quckly access plugin options upon activation.


*/

/*  
Copyright 2008  Vladimir Prelovac  (email : vprelovac@gmail.com)

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 2 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

// Avoid name collisions.
if ( !class_exists('ShowMeOptions') ) :

class ShowMeOptions {
	
	// Name for our options in the DB
	var $DB_option = 'ShowMeOptions_options';
	var $plugin_url;
	
	function ShowMeOptions() {	
		
			$this->plugin_url=trailingslashit( get_bloginfo('wpurl') ).PLUGINDIR.'/'. dirname( plugin_basename(__FILE__) );
			
			add_action('admin_head', array(&$this, 'AdminHead'));
			add_action('admin_init', array(&$this, 'AdminInit'));
	}

// finding deeply into arrays
function array_search_recursive( $needle, $haystack, $strict=false, $path=array() )
{
    if( !is_array($haystack) ) {
        return false;
    }

    foreach( $haystack as $key => $val ) {
        if( is_array($val) && $subPath = $this->array_search_recursive($needle, $val, $strict, $path) ) {
            $path = array_merge($path, array($key), $subPath);
            return $path;
        } elseif( (!$strict && $val == $needle) || ($strict && $val === $needle) ) {
            $path[] = $key;
            return $path;
        }
    }
    return false;
}

// array diff engine
function array_diff_assoc_recursive($array1, $array2)
{
    foreach($array1 as $key => $value)
    {
        if(is_array($value))
        {
              if(!isset($array2[$key]))
              {
                  $difference[$key] = $value;
              }
              elseif(!is_array($array2[$key]))
              {
                  $difference[$key] = $value;
              }
              else
              {
                  $new_diff = $this->array_diff_assoc_recursive($value, $array2[$key]);
                  if($new_diff != FALSE)
                  {
                        $difference[$key] = $new_diff;
                  }
              }
          }
          elseif(!isset($array2[$key]) || $array2[$key] != $value)
          {
              $difference[$key] = $value;
          }
    }
    return !isset($difference) ? 0 : $difference;
} 

// check for plugin activation
function AdminInit()
{
	if ($_GET['action']=='activate')
	{
			global $submenu;					
			
			$options=array();
			$options['plugin']=$_GET['plugin'];
			$options['submenu']=$submenu;
			update_option($this->DB_option, $options);	
	}
}

// print options link
function AdminHead()
{
	
	if ($_GET['activate']=='true')
	{
		
		$options=get_option($this->DB_option);
		
		$saved_submenu=$options['submenu'];
		$plugin=$options['plugin'];
		
		if ($plugin	)
		{
			
			global $submenu;
			
			// find out plugin options based on plugin name		
			$key=$this->array_search_recursive( $plugin, $submenu );
			if (!$key)
			{
				$plugin=basename($plugin);
				$key=$this->array_search_recursive( $plugin, $submenu );
			}
			$page=$key[0];
			$title=$submenu[$page][$key[1]][0];
			//print_r($key);
			
			if ($page)
				$link='<p>Proceed to plugin <a href="'.$page.'?page='.$plugin.'">options</a> ('.$title.').</p>';
			else
			{
				 // the author decided to be creative.. we need to try harder
				 $res=$this->array_diff_assoc_recursive($submenu,$saved_submenu);
			
			
					
					//print_r($submenu);
					//print_r($saved_submenu);	
				 	//print_r($res);
				 
				 
				 if ($res)
				 foreach ($res as $key => $value)
				 {
				 		$page=$key;
				 		foreach ($value as $key2 => $value2)
				 		{
				 			$name=$value2[0];
				 			$slug=$value2[2];
				 			$title=$value2[3];
				 			break;
				 		}
				 		break;
				 	}
				 	if ($page)
						$link='<p>Proceed to plugin <a href="'.$page.'?page='.$slug.'">options</a> ('.$name.').</p>';
					else
						$link='<p>No options page detected.</p>';
				}
			 
			 // little jquery to show what we need
				echo '
					<script type="text/javascript">		
					jQuery(document).ready( function($) {		 	
					   $("#message").append(\''.$link.'\');			
					});
					</script>
					';
		}
	}
	else 
	{
		$options=array();
		$options['plugin']='';
		update_option($this->DB_option, $options);
	}
	
}


}

endif; 

if ( class_exists('ShowMeOptions') ) :
	
	$ShowMeOptions = new ShowMeOptions();
	
endif;



?>