<?php
//if pspell doesn't exist, then include the pspell wrapper for aspell

require_once("config.php");

print "data = [";
foreach($_GET as $key => $value) //for loop will also do
{
	$word = $value;
	$suggestions = pspell_suggest($pspell_link, $word);
	
	if (!is_array($suggestions)) {//pspell_check($pspell_link, $word)
		print "[1]";
	}
	else {
		print "[0,[";
		
		
		if (count($suggestions)>0)
		print '"'.implode('","', $suggestions).'"';
		
		print ']]';
	}
		
};
print "];\n";

?>