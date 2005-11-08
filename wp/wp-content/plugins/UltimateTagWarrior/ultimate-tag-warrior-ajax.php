<?php
ini_set("include_path", ini_get('include_path') . PATH_SEPARATOR . ".");

require('../../../wp-blog-header.php');
include_once('ultimate-tag-warrior-core.php');

$keywordAPISite = "tagyu.com";
$keywordAPIUrl = "/api/suggest/";

$appID = "wp-UltimateTagWarrior";

$action = $_REQUEST['action'];
$tag = $_REQUEST['tag'];
$post = $_REQUEST['post'];
$format = $_REQUEST['format'];

switch($action) {
	case 'del':
		if ( $user_level > 3 ) {
			$utw->RemoveTag($post, $tag);
			echo $post . "|";
			$utw->ShowTagsForPost($post, $utw->GetFormatForType("superajax"));
		}
		break;

	case 'add':
		$utw->AddTag($post, $tag);
		echo $post . "|";
		if("" == $format) {
			$format = "superajax";
		}
		$utw->ShowTagsForPost($post, $utw->GetFormatForType($format));
		break;

	case 'expand':
		echo "$post-$tag|";
		echo $utw->FormatTags($utw->GetTagsForTagString('"' . $tag . '"'), $utw->GetFormatForType("linkset"));
		break;

	case 'expandrel':
		echo "$post-$tag|";
		echo $utw->FormatTags($utw->GetTagsForTagString('"' . $tag . '"'), $utw->GetFormatForType("linksetrel"));
		break;

	case 'shrink':
		echo "$post-$tag|";
		echo $utw->FormatTags($utw->GetTagsForTagString('"' . $tag . '"'), $utw->GetFormatForType($format));
		break;

	case 'shrinkrel':
		echo "$post-$tag|";
		echo $utw->FormatTags($utw->GetTagsForTagString('"' . $tag . '"'), $utw->GetFormatForType($format . "item"));
		break;


	case 'requestKeywords':
		$sock = fsockopen($keywordAPISite, 80, $errno, $errstr, 30);
		if (!$sock) die("$errstr ($errno)\n");

		$data = urlencode(strip_tags(urldecode($HTTP_RAW_POST_DATA)));

		$xml = "";
		$tagyu_url = 'http://' . $keywordAPISite . $keywordAPIUrl . $data;

		if ($bypost) {

			fputs($sock, "POST $keywordAPIUrl HTTP/1.0\r\n");
			fputs($sock, "Host: $keywordAPISite\r\n");
			fputs($sock, "Content-type: application/x-www-form-urlencoded\r\n");
			fputs($sock, "Content-length: " . strlen($data) . "\r\n");
			fputs($sock, "Accept: */*\r\n");
			fputs($sock, "\r\n");
			fputs($sock, "$data\r\n");
			fputs($sock, "\r\n");

			$headers = "";
			while ($str = trim(fgets($sock, 4096)))
			  $headers .= "$str\n";

			print "\n";

			while (!feof($sock))
			  $xml .= fgets($sock, 4096);

			fclose($sock);
		} else {

			fputs($sock, "GET " . $keywordAPIUrl . $data . " HTTP/1.0\r\n\r\n");
			fputs($sock, "Host: $keywordAPISite\r\n");
			fputs($sock, "Accept: */*\r\n");

			$headers = "";
			while ($str = trim(fgets($sock, 4096)))
			  $headers .= "$str\n";

			print "\n";

			while (!feof($sock))
			  $xml .= fgets($sock, 4096);

			fclose($sock);
		} /* else {
			// Fall back to whatever this approach is called if it isn't.

			$xml = file_get_contents($tagyu_url);
		} */

		if (strpos($xml,'<error>') === FALSE) {
			$loc = strpos($xml, "<tag>", 0);
			while($loc < strlen($xml) && $loc != false) {
				$loc += 5; // start of the tag
				$end = strpos($xml, "</tag>", $loc);

				echo "<a href=\"javascript:addTag('" . str_replace(' ','_',substr($xml, $loc, $end-$loc)) . "')\">" . substr($xml, $loc, $end-$loc) . "</a> ";
				$tagstr .= "'" . str_replace(' ','_',substr($xml, $loc, $end-$loc)) . "',";

				$loc = strpos($xml, "<tag>", $end);
			}

			// eat the trailing comma.
			$tagstr = substr($tagstr,0,-1);

		} else {
			echo $xml;
		}

		$utw->ShowRelatedTags($utw->GetTagsForTagString($tagstr), "<a href=\"javascript:addTag('%tag%')\">%tagdisplay%</a> ");

		break;

	case 'editsynonyms':

		echo '<input type="text" name="synonyms" value="' . $utw->FormatTags($utw->GetSynonymsForTag("", $tag), array("first"=>"%tag%", "default"=>", %tag%")) . '" />';
	}

?>