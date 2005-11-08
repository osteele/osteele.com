<?php
/*
Plugin Name: Flashifier
Plugin URI: http://www.ryanmeyers.com/wordpress/index.php?p=14&cat=2
Description: Create flash code by enclosing the info in [FLASH]<em>%filename%</em>,<em>%width%</em>,<em>%height%</em>[/FLASH].
Version: 0.1
Author: Ryan Meyers
Author URI: http://www.ryanmeyers.com
*/
#/

function flashifier_the_content($text) {
    
    # Parse text looking for flash tags.
    $flashedtext='';
    
	foreach (preg_split('|(\[FLASH\].+,.+,.+\[/FLASH\])|i', $text, -1, PREG_SPLIT_DELIM_CAPTURE) as $token) {
		
		
        
		if (preg_match('|(\[FLASH\].+,.+,.+\[/FLASH\])|i', $token)) {
            
			$name = $token;

			$name2=strtolower($name);

			$getrid = array('[flash]','[/flash]',' ','\n','<br>','<br />');
			
			$name2=str_replace($getrid,'',$name2);
				
	
				$namex = explode(',',$name2);

				$filename = $namex[0];
$filename = str_replace('<span>', '', $filename);
$filename = str_replace('</span>', '', $filename);
				$width = $namex[1];
$width = str_replace('<span>', '', $width);
$width = str_replace('</span>', '', $width);
				$height = $namex[2];
$height = str_replace('<span>', '', $height);
$height = str_replace('</span>', '', $height);

				$output = "<object type=\"application/x-shockwave-flash\" data=\"$filename\" width=\"$width\" height=\"$height\"><param name=\"movie\" value=\"$filename\" /></object>";
				
				$flashedtext .= $output;
                          
        } else {
            $flashedtext .= $token;}
    }

    return $flashedtext;
}

# Turn on the flashifying process.
add_filter('the_content', 'flashifier_the_content');

?>