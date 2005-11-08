<?php
/*
Plugin Name: del.icio.us cached
Plugin URI: http://www.w-a-s-a-b-i.com/
Description: Outputs a list of your del.icio.us bookmarks.
Version: 1.4
Author: Alexander Malov
Author URI: http://www.w-a-s-a-b-i.com/
*/

function delicious($username, $count=15, $extended="title", $divclass="delPost", $aclass="delLink", $tags="no", $tagclass="delTag", $tagsep="/", $tagsepclass="delTagSep", $bullet="raquo", $rssbutton="no", $extendeddiv="no", $extendedclass="") {
		$queryString = "http://del.icio.us/html/";
		$queryString .= "$username/";
		$queryString .= "?count=$count";
		$queryString .= "&extended=$extended";
		$queryString .= "&divclass=$divclass";
		$queryString .= "&aclass=$aclass";
		$queryString .= "&tags=$tags";
		$queryString .= "&tagclass=$tagclass";
		$queryString .= "&tagsep=$tagsep";
		$queryString .= "&tagsepclass=$tagsepclass";
		$queryString .= "&bullet=$bullet";
		$queryString .= "&rssbutton=$rssbutton";
		$queryString .= "&extendeddiv=$extendeddiv";
		$queryString .= "&extendedclass=$extendedclass";

		$cachetime = 30 * 60; // 30 minutes
		$cachefile = (ABSPATH . "wp-content/delicious_cache.html");

		if(file_exists($cachefile) && (time() - $cachetime < filemtime($cachefile))) {
			include($cachefile);
		} else {

		$c = curl_init($queryString);
		// cURL options
		curl_setopt($c, CURLOPT_RETURNTRANSFER,1);
		curl_setopt($c, CURLOPT_USERPWD,"$username");
		curl_setopt($c, CURLOPT_CONNECTTIMEOUT, 2);
		curl_setopt($c, CURLOPT_TIMEOUT, 4);
		curl_setopt($c, CURLOPT_USERAGENT, "del.icio.us cached 1.4 / http://www.w-a-s-a-b-i.com");
		$response = curl_exec($c);
		$info = curl_getinfo($c);
		$_curl_error_code = $info['http_code'];

		if($_curl_error_code == 200) {
			echo $response;
			
			$cachefile = (ABSPATH . "wp-content/delicious_cache.html");

			$fp = fopen($cachefile, 'w');
			
			fwrite($fp, $response);
			fclose($fp);
		} else {
			include($cachefile);
		}
	}
}
?>