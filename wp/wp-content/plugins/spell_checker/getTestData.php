<?php

	$words = $_GET["words"];
	
	
	include_once("google_spell.php");
	
	print_r(google_spell($words));
	die();
		
	// GOOGLE'S FORMAT WAS, APPARENTLY, THIS...
	// THIS WAS DETERMINED USING THE Live HTTP Headers EXTENSION FOR FIREFOX: http://livehttpheaders.mozdev.org/
	$words = "<spellrequest textalreadyclipped=\"0\" ignoredups=\"1\" ignoredigits=\"1\" ignoreallcaps=\"0\"><text>" . $words . "</text></spellrequest>";

	$server = "www.google.com";
	$port = 443;
	
	// lang and hl are substituted based on the current setting in the toolbar (it's multi-lingual).
	// All I know is english, so I didn't bother including that. It'd be easy enough...
	$path = "/tbproxy/spell?lang=en&hl=en";
	$host = "www.google.com";
	
	$url = "https://" . $server;
	$page = $path;
	
	// THE BELOW CODE WAS TAKEN STRAIGHT FROM THE PHP MANUAL: http://us2.php.net/manual/en/ref.curl.php
	// I WOULD HAVE PREFERRED TO HAVE DONE IT WITHOUT CURL, BUT RAW SOCKETS DIDN'T WANT TO COOPERATE WITH SSL, DESPITE SUPPOSEDLY SUPPORTING IT...
	
	$post_string = $words;

       $header  = "POST ".$page." HTTP/1.0 \r\n";
       $header .= "MIME-Version: 1.0 \r\n";
       $header .= "Content-type: application/PTI26 \r\n";
       $header .= "Content-length: ".strlen($post_string)." \r\n";
       $header .= "Content-transfer-encoding: text \r\n";
       $header .= "Request-number: 1 \r\n";
       $header .= "Document-type: Request \r\n";
       $header .= "Interface-Version: Test 1.4 \r\n";
       $header .= "Connection: close \r\n\r\n";
       $header .= $post_string;
      
       $ch = curl_init();
       curl_setopt($ch, CURLOPT_URL,$url);
       curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
       curl_setopt($ch, CURLOPT_TIMEOUT, 4);
       curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $header);

       $data = curl_exec($ch);
       if (curl_errno($ch)) {
           print curl_error($ch);
       } else {
           curl_close($ch);
       }

// use XML Parser on $data, and your set!

       $xml_parser = xml_parser_create();
       xml_parser_set_option($xml_parser,XML_OPTION_CASE_FOLDING,0);
       xml_parser_set_option($xml_parser,XML_OPTION_SKIP_WHITE,1);
       xml_parse_into_struct($xml_parser, $data, $vals, $index);
       xml_parser_free($xml_parser);
      
// $vals = array of XML tags.  Go get em!
for ($i=1;$i<count($vals)-1;$i++) {
	echo "$i";
	echo $vals[$i]["value"]."<br>";
}
// THIS SPITS OUT EXACTLY WHAT YOU'D HAVE GOTTEN FROM GOOGLE'S SCRIPT


// THIS WAS FOR DEBUGGING, IT SPIT OUT EVERYTHING IN A SLIGHTLY MORE READABLE FORMAT
// YOU COULD INCLUDE SOME MORE ADVANCED PARSING IN THIS SCRIPT IF YOU'RE LESS FAMILIAR WITH JAVASCRIPT

/* */
echo strip_tags($data);
echo "<pre>\n";
print_r($vals);
print_r($index);

echo "</pre>\n";

?>