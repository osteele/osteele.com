<?php
/*
Plugin Name: GeshiSyntaxColorer
Version: 0.2
Plugin URI: http://www.regex.ru
Description: Program languages colorer based on <a href="http://qbnz.com/highlighter/index.php">GeSHI</a> engine
Author: Mikhail Kyurshin
Author URI: http://www.regex.ru
*/

include(ABSPATH.'/wp-content/plugins/geshi.php');

function GeshiSyntaxColorer($text) {

    $codes = array();
    $str = uniqid('');
    $i=0;
    while (preg_match("#<code lang=['\"]([a-zA-Z0-9_-]+)['\"]>(.*?)</code>#s", $text, $matches)) {
        $i++;
        $codes[$i]['lang'] = $matches[1];
        $codes[$i]['code'] = $matches[2];
        $text = preg_replace("#<code lang=['\"][a-zA-Z0-9_-]+['\"]>.*?</code>#s", $str.$i, $text, 1);
    }
    
    
    
    $i=0;
    while (preg_match("#".$str."([0-9]+)#", $text, $matches)) {
        $i++;
        $geshi = new GeSHi($codes[$i]['code'], $codes[$i]['lang'], ABSPATH.'/wp-content/plugins/geshi/');
        
        $code = $geshi->parse_code();
        
        $text = preg_replace("#".$str."([0-9]+)#", "<code>".$code."</code>", $text, 1);
    }
    return $text;
}

function GeshiSyntaxColorer_popup_help($name,$helpvar,$windowW,$windowH) {
    $out = $name;
    $out .= 'Use <code lang="lang">your code</code> tag to highlight code. Available langs can be found in GeShi documentation';
    print $out;
}


remove_filter('the_content', 'wpautop');
remove_filter('the_excerpt', 'wpautop');
remove_filter('comment_text', 'wpautop');

add_filter('the_content', 'GeshiSyntaxColorer', 6);
add_filter('the_excerpt', 'GeshiSyntaxColorer', 6);
add_filter('comment_text', 'GeshiSyntaxColorer', 6);

?>