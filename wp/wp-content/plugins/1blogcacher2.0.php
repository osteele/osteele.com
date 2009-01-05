<?php

// This file (1blogcacher2.0.php) must be placed under the /wp-content/plugins/ WordPress directory (<your wordpress directory>/wp-content/plugins/1blogcacher2.0.php)

/*
Plugin Name: 1 Blog Cacher
Plugin URI: http://1blogcacher.com/
Description: 1 Blog Cacher is a WordPress plugin that caches your pages in order to increase the response speed and minimize the server load.
Version: 2.0.4
Author: Javier Garc&iacute;a
Date: 2007-09-18
Author URI: http://1blogr.com/
License: Attribution - Non Commercial - Share Alike 2.5 - http://creativecommons.org/licenses/by-nc-sa/2.5/


INSTALLATION:

- (Optional) Edit the values in the advanced-cache.php file (define...) for your convenience (further information in that file).
- Create the cache directory /wp-cache/ in your WordPress directory (<your wordpress directory>/wp-cache/) and make it writeable (chmod 777).
- Upload 1blogcacher2.0.php file to /wp-content/plugins/ WordPress directory (<your wordpress directory>/wp-content/plugins/1blogcacher2.0.php).
- Upload advanced-cache.php file to /wp-content/ (<your wordpress directory>/wp-content/advanced-cache.php).
- Add this line to the wp-config.php file ("<yourwordpressdirectory>/wp-config.php"): define('WP_CACHE', true);
- Activate the plugin and take a look to "Options > 1 Blog Cacher" in the WordPress panel.

READ README.txt FILE

*/

IF (!function_exists("obc_uncache_index")){
	FUNCTION obc_uncache_index($id){
		$url = obc_clean_url(get_option("home"));
		IF (substr($url,-1) != "/") $url .= "/";

		global $obc_uncached_urls;
		IF ($obc_uncached_urls[$url]) RETURN false;
		$obc_uncached_urls[$url] = true;
		$file = obc_url_to_file($url);
		IF ($file === false) RETURN false;

		obc_remove_cache(OBC_PATH."/".$file);
		RETURN true;
	};
};
IF (!function_exists("obc_uncache_post")){
	FUNCTION obc_uncache_post($id){
		IF (!strlen($id)) RETURN false;
		$url = obc_clean_url(get_permalink($id));

		global $obc_uncached_urls;
		IF ($obc_uncached_urls[$url]) RETURN false;
		$obc_uncached_urls[$url] = true;
		$file = obc_url_to_file($url);
		IF ($file === false) RETURN false;

		obc_remove_cache(OBC_PATH."/".$file);
		IF (OBC_CACHE_DIRECTORIES){
			$directories = explode("/",$file);
			FOR ($x = count($directories)-1; $x > 0; $x--){
				$file_path = implode("/",array_slice($directories,0,$x));
				IF (!@rmdir(OBC_PATH."/".$file_path)) break;
			};
		};
		RETURN true;
	};
};
IF (!function_exists("obc_remove_cache")){
	FUNCTION obc_remove_cache($cache){
		IF (OBC_CACHE_DIRECTORIES) obc_remove_directory($cache);
		ELSEIF (function_exists("glob")){
			FOREACH (glob("{$cache}-*.*") as $file_path) @unlink($file_path);
		}ELSE{
			$file_handler = @opendir(OBC_PATH);
			IF (!$file_handler) RETURN false;
			WHILE (($file = readdir($file_handler)) !== false){
				$file_path = OBC_PATH."/".$file;
				IF (preg_match("'^{$cache}-'",$file_path)) @unlink($file_path);
			};
		};
		RETURN true;
	};
};
IF (!function_exists("obc_remove_directory")){
	FUNCTION obc_remove_directory($file){
		obc_remove_files($file,"all",true);
	};
};
IF (!function_exists("obc_get_files")){
	FUNCTION obc_get_files($dir = ".",$files = 0,$expired_files = 0,$size = 0,$expired_size = 0){
		IF (!is_dir($dir)) RETURN false;
		$file_handler = @opendir($dir);
		IF (!$file_handler) RETURN false;
		WHILE (($file = readdir($file_handler)) !== false){
			$file_path = $dir."/".$file;
			IF (in_array($file,array(".","..")) || in_array($file_path,array(OBC_PATH."/.htaccess",OBC_PATH."/obc_configuration"))) CONTINUE;
			IF (is_dir($file_path)){
				$get_files = obc_get_files($file_path);
				$files += $get_files["files"];
				$expired_files += $get_files["expired_files"];
				$size += $get_files["size"];
				$expired_size += $get_files["expired_size"];
			}ELSE{
				$files++;
				$file_size = filesize($file_path);
				$size += $file_size;
				IF (@filemtime($file_path) < time()-OBC_EXPIRATION){
					$expired_files++;
					$expired_size += $file_size;
				};
			};
		}
		closedir($file_handler);
		RETURN array("files"=>$files,"expired_files"=>$expired_files,"size"=>$size,"expired_size"=>$expired_size);
	};
};
IF (!function_exists("obc_remove_files")){
	FUNCTION obc_remove_files($dir,$type = "all",$remove_directory = false){
		IF (is_dir($dir)){
			$file_handler = @opendir($dir);
			IF ($file_handler){
				WHILE (($file = readdir($file_handler)) !== false) {
					$file_path = $dir."/".$file;
					IF (in_array($file,array(".","..")) || in_array($file_path,array(OBC_PATH."/.htaccess",OBC_PATH."/obc_configuration"))) CONTINUE;
					IF (is_dir($file_path)) obc_remove_files($file_path,$type,true);
					ELSE{
						IF ($type == "all") @unlink($file_path);
						ELSEIF (@filemtime($file_path) < time()-OBC_EXPIRATION) @unlink($file_path);
					};
				};
				closedir($file_handler);
				IF ($remove_directory) IF (!@rmdir($dir)) RETURN false;
				RETURN true;
			}ELSE RETURN false;
		}ELSE RETURN false;
	};
};
IF (!function_exists("obc_admin_menu")){
	FUNCTION obc_admin_menu(){
		IF (function_exists("add_options_page")) add_options_page("1 Blog Cacher","1 Blog Cacher",8,basename(__FILE__),"obc_options_page");	
	};
};
IF (!function_exists("obc_options_page")){
	FUNCTION obc_options_page(){
		$abspath = str_replace("\\","/",ABSPATH);
		$obc_path = "{$abspath}wp-cache";
		IF (substr($abspath,-1) == "/") $abspath = substr($abspath,0,-1);
		IF (basename(__FILE__) != "1blogcacher2.0.php"){
			echo "<div class=\"wrap\">\n";
			echo "<h2>1 Blog Cacher</h2>\n";
			echo "<h3>Installation error</h3>\n";
			echo "<p>This file should be named <strong>1blogcacher2.0.php</strong>, not ".basename(__FILE__).".</p>\n";
		}ELSEIF (!file_exists(ABSPATH."wp-content/plugins/1blogcacher2.0.php")){
			echo "<div class=\"wrap\">\n";
			echo "<h2>1 Blog Cacher</h2>\n";
			echo "<h3>Installation error</h3>\n";
			echo "<p>This file - 1blogcacher2.0.php - shouldn't be here. Move it to the directory {$abspath}/wp-content/plugins/.</p>\n";
		}ELSEIF (!is_file("{$abspath}/wp-content/advanced-cache.php")){
			echo "<div class=\"wrap\">\n";
			echo "<h2>1 Blog Cacher</h2>\n";
			echo "<h3>Installation error</h3>\n";
			echo "<p>The file - {$abspath}/wp-content/advanced-cache.php - doesn't exist.</p>\n";
		}ELSEIF (!is_dir($obc_path)){
			echo "<div class=\"wrap\">\n";
			echo "<h2>1 Blog Cacher</h2>\n";
			echo "<h3>Installation error</h3>\n";
			echo "<p>The cache directory - {$obc_path}/ - doesn't exist.</p>\n";
		}ELSEIF (!is_writable($obc_path)){
			echo "<div class=\"wrap\">\n";
			echo "<h2>1 Blog Cacher</h2>\n";
			echo "<h3>Installation error</h3>\n";
			echo "<p>The cache directory - {$obc_path}/ - is not writeable (chmod 777).</p>\n";
		}ELSEIF (!defined("OBC_ADVANCED_CACHE")){
			echo "<div class=\"wrap\">\n";
			echo "<h2>1 Blog Cacher</h2>\n";
			echo "<h3>Installation error</h3>\n";
			echo "<p>The file - {$abspath}/wp-content/advanced-cache.php - is incorrect or you haven't added the code <code>define('WP_CACHE', true);</code> to the file {$abspath}/wp-config.php</p>\n";
		}ELSE{
			IF ($_POST["remove_all"]){
				obc_remove_files(OBC_PATH,"all");
				echo "<div id=\"message\" class=\"updated fade\"><p><strong>All files removed.</strong></p></div>\n";
			}ELSEIF ($_POST["remove_expired"]){
				obc_remove_files(OBC_PATH,"expired");
				echo "<div id=\"message\" class=\"updated fade\"><p><strong>All expired files removed.</strong></p></div>\n";
			};
			echo "<div class=\"wrap\">\n";
			echo "<h2>1 Blog Cacher</h2>\n";
			echo "<h3>Correct installation</h3>\n";
			echo "<p>The plugin has been correctly installed and cache files will be saved in the directory - ".OBC_PATH."/.</p>\n";
			echo "<h3>Opciones</h3>\n";
			echo "<p>Edit the file - {$abspath}/wp-content/advanced-cache.php - in order to change the following options:</p>\n";
			echo "<table width=\"100%\" cellspacing=\"2\" cellpadding=\"5\" class=\"optiontable editform\">\n";
			echo "<tr valign=\"top\">\n";
			echo "<td width=\"33%\" align=\"right\"><p><big>Cache expiration</big></p></td>\n";
			echo "<td><p>".OBC_EXPIRATION." seconds.</p></td>\n";
			echo "</tr>\n";
			echo "<tr valign=\"top\">\n";
			echo "<td align=\"right\"><p><big>Logged users</big></p></td>\n";
			echo "<td><p>";
			IF (OBC_CACHE_USERS == 0) echo "Use no cache";
			ELSEIF (OBC_CACHE_USERS == 2) echo "Use an individual cache for each user";
			ELSE  echo "Use a single global cache";
			echo "</p></td>\n";
			echo "</tr>\n";
			echo "<tr valign=\"top\">\n";
			echo "<td align=\"right\"><p><big>Commenters</big></p></td>\n";
			echo "<td><p>";
			IF (OBC_CACHE_COMMENTERS == 0) echo "Use no cache";
			ELSEIF (OBC_CACHE_COMMENTERS == 2) echo "Use an individual cache for each commenter";
			ELSE  echo "Use a single global cache";
			echo "</p></td>\n";
			echo "</tr>\n";
			echo "<tr valign=\"top\">\n";
			echo "<td align=\"right\"><p><big>Cache error pages</big</big></p></td>\n";
			echo "<td><p>".((OBC_CACHE_ERROR_PAGES)? "Yes" : "No").".</p></td>\n";
			echo "</tr>\n";
			echo "<tr valign=\"top\">\n";
			echo "<td align=\"right\"><p><big>Cache redirections</big></p></td>\n";
			echo "<td><p>".((OBC_CACHE_REDIRECTIONS)? "Yes" : "No").".</p></td>\n";
			echo "</tr>\n";
			echo "<tr valign=\"top\">\n";
			echo "<td align=\"right\"><p><big>Avoid trailing slash duplication \"/\"</big></p></td>\n";
			echo "<td><p>".((OBC_AVOID_TRAILING_SLASH_DUPLICATION)? "Yes" : "No").".</p></td>\n";
			echo "</tr>\n";
			echo "<tr valign=\"top\">\n";
			echo "<td align=\"right\"><p><big>Enable browser cache</big></p></td>\n";
			echo "<td><p>".((OBC_ENABLE_BROWSER_CACHE)? "Yes" : "No").".</p></td>\n";
			echo "</tr>\n";
			echo "<tr valign=\"top\">\n";
			echo "<td align=\"right\"><p><big>Look for dynamic code</big></p></td>\n";
			echo "<td><p>".((OBC_LOOK_FOR_DYNAMIC_CODE)? "Yes" : "No").".</p></td>\n";
			echo "</tr>\n";
			echo "<tr valign=\"top\">\n";
			echo "<td align=\"right\"><p><big>Use cache directories</big></p></td>\n";
			echo "<td><p>".((OBC_USE_CACHE_DIRECTORIES)? "Yes" : "No").".</p></td>\n";
			echo "</tr>\n";
			echo "<tr valign=\"top\">\n";
			echo "<td align=\"right\"><p><big>Rejected strings</big></p></td>\n";
			echo "<td><p>If the url contains one of these strings, that url won't be cached:</p>\n";
			$obc_rejected_strings = explode(",",OBC_REJECTED_STRINGS);
			$obc_rejected_strings = array_filter($obc_rejected_strings);
			IF (count($obc_rejected_strings)){
				echo "<ul>\n";
				FOREACH ($obc_rejected_strings as $x=>$rejected_string){
					$rejected_string = trim($rejected_string);
					echo "<li> {$rejected_string}</li>\n";
				};
				echo "</ul>\n";
			};
			echo "</td>\n";
			echo "</tr>\n";
			echo "<tr valign=\"top\">\n";
			echo "<td align=\"right\"><p><big>Accepted strings</big></p></td>\n";
			echo "<td><p>Exceptions to the previous rule:</p>\n";
			$obc_accepted_strings = explode(",",OBC_ACCEPTED_STRINGS);
			$obc_accepted_strings = array_filter($obc_accepted_strings);
			IF (count($obc_accepted_strings)){
				echo "<ul>\n";
				FOREACH ($obc_accepted_strings as $x=>$accepted_string){
					$rejected_string = trim($accepted_string);
					echo "<li> {$accepted_string}</li>\n";
				};
				echo "</ul>\n";
			};
			echo "</td>\n";
			echo "</tr>\n";
			echo "<tr valign=\"top\">\n";
			echo "<td align=\"right\"><p><big>Rejected User Agents</big></p></td>\n";
			echo "<td><p>If the user-agent (name of the cient's browser, bot, etc.) contains one of these strings, its requests won't be cached, though cached pages will be displayed if they exist and haven't expired:</p>\n";
			$obc_rejected_user_agents = explode(",",OBC_REJECTED_USER_AGENTS);
			$obc_rejected_user_agents = array_filter($obc_rejected_user_agents);
			IF (count($obc_rejected_user_agents)){
				echo "<ul>\n";
				FOREACH ($obc_rejected_user_agents as $x=>$rejected_user_agent){
					$rejected_string = trim($rejected_user_agent);
					echo "<li> {$rejected_user_agent}</li>\n";
				};
				echo "</ul>\n";
			};
			echo "</td>\n";
			echo "</tr>\n";
			echo "</table>\n";
			echo "<h3>Cached files</h3>\n";
			$get_files = obc_get_files(OBC_PATH);
			echo "<form method=\"post\" action=\"{$_SERVER["REQUEST_URI"]}\">\n";
			echo "<table width=\"100%\" cellspacing=\"2\" cellpadding=\"5\" class=\"optiontable editform\">\n";
			echo "<tr valign=\"top\">\n";
			echo "<td width=\"33%\" align=\"right\"><p><big>All files</big></p></td>\n";
			echo "<td><p>{$get_files["files"]} files saved in your cache directory (".round($get_files["size"]/(1024),2)." KB = ".round($get_files["size"]/(1024*1024),2)." MB)";
			IF ($get_files["files"]) echo "</p>\n<p><input type=\"submit\" name=\"remove_all\" value=\"Remove all files\" onclick=\"return confirm('Sure you want to remove all files?');\">";
			echo "</p></td>\n";
			echo "</tr>\n";
			echo "<tr valign=\"top\">\n";
			echo "<td align=\"right\"><p><big>Expired files</big></p></td>\n";
			echo "<td><p>{$get_files["expired_files"]} expired files saved in your cache directory (".round($get_files["expired_size"]/(1024),2)." KB = ".round($get_files["expired_size"]/(1024*1024),2)." MB)";
			IF ($get_files["expired_files"]) echo "</p>\n<p><input type=\"submit\" name=\"remove_expired\" value=\"Remove expired files\" onclick=\"return confirm('Sure you want to remove all expired files?');\">";
			echo "</p></td>\n";
			echo "</tr>\n";
			echo "</table>\n";
			echo "</form>\n";
		};
	};
};
IF (!function_exists("obc_write_file")){
	FUNCTION obc_write_file($file,$content){
		$file_pointer = @fopen($file,"w+");
		IF ($file_pointer){
			@flock($file_pointer,LOCK_EX);
			@fwrite($file_pointer,$content);
			@flock($file_pointer,LOCK_UN);
			fclose($file_pointer);
		};
	};
};
IF (!function_exists("obc_load_configuration")){
	FUNCTION obc_load_configuration(){
		$configuration = @file(ABSPATH."wp-cache/obc_configuration");
		$configuration = unserialize($configuration[0]);
		RETURN $configuration;
	};
};
IF (!function_exists("obc_check_configuration")){
	FUNCTION obc_check_configuration(){
		global $obc_configuration;
		IF (!is_array($obc_configuration)) $obc_configuration = obc_load_configuration();
		$configuration = array();
		$configuration["plugin_active"] = 1;
		$configuration["home"] = get_option("home");
		$configuration["cookiehash"] = COOKIEHASH;
		$configuration["gzip_enabled"] = get_option("gzipcompression");
		IF ($obc_configuration != $configuration) obc_write_file(ABSPATH."wp-cache/obc_configuration",serialize($configuration));
		IF (!file_exists(ABSPATH."wp-cache/.htaccess")) obc_write_file(ABSPATH."wp-cache/.htaccess","order deny,allow\ndeny from all");
	};
};
IF (!function_exists("obc_deactivate")){
	FUNCTION obc_deactivate(){
		$configuration = obc_load_configuration();
		IF ($configuration["plugin_active"]){
			$configuration["plugin_active"] = 0;
			obc_write_file(ABSPATH."wp-cache/obc_configuration",serialize($configuration));
		};
	};
};
IF (defined("OBC_CACHE_DIRECTORIES")){
	add_action("publish_post","obc_uncache_post");
	add_action("publish_post","obc_uncache_index");
	add_action("edit_post","obc_uncache_post");
	add_action("edit_post","obc_uncache_index");
	add_action("delete_post","obc_uncache_post");
	add_action("delete_post","obc_uncache_index");
};
add_action("admin_menu","obc_admin_menu");
add_action("deactivate_1blogcacher2.0.php","obc_deactivate");
obc_check_configuration();
?>