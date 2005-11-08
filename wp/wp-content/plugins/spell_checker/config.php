<?php

// Options
$dictlang = "en"; //"en_UK" "en_SG";
$google_spell = true;//false

//if pspell doesn't exist, then include the pspell wrapper for aspell
if(!function_exists('pspell_suggest')){
	define('ASPELL_BIN','/usr/bin/aspell'); //set path to aspell if you need to and uncomment this line
	require_once ("pspell_comp.php");
}


if (empty($dictlang)) $dictlang = "en";
//these three lines create and configure a link to the pspell module
$pspell_config = pspell_config_create($dictlang);
pspell_config_mode($pspell_config, PSPELL_FAST);
$pspell_link = pspell_new_config($pspell_config);

?>