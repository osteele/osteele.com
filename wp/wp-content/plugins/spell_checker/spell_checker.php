<?
/*************************************************************
 * AJAX Spell Checker - Version 2.2
 * (C) 2005 - Garrison Locke
 * 
 * This spell checker is built in the style of the Gmail spell
 * checker.  It uses AJAX to communicate with the backend without
 * requiring the page be reloaded.  If you use this code, please
 * give me credit and a link to my site would be nice.
 * http://www.broken-notebook.com.
 *
 * Copyright (c) 2005, Garrison Locke
 * All rights reserved.
 *
 * Redistribution and use in source and binary forms, with or without 
 * modification, are permitted provided that the following conditions are met:
 *
 *   * Redistributions of source code must retain the above copyright notice, 
 *     this list of conditions and the following disclaimer.
 *   * Redistributions in binary form must reproduce the above copyright notice, 
 *     this list of conditions and the following disclaimer in the documentation 
 *     and/or other materials provided with the distribution.
 *   * Neither the name of the http://www.broken-notebook.com nor the names of its 
 *     contributors may be used to endorse or promote products derived from this 
 *     software without specific prior written permission.
 *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS" AND 
 * ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED 
 * WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE DISCLAIMED. 
 * IN NO EVENT SHALL THE COPYRIGHT OWNER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, 
 * INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT 
 * NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE, DATA, OR 
 * PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY THEORY OF LIABILITY, 
 * WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE) 
 * ARISING IN ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY 
 * OF SUCH DAMAGE.
 *
 *************************************************************/

//user configurable list of allowed HTML tags.  Thanks to Jake Olefsky for this little addition.
$allowed_html = '<p><a><br><b><i><img><strong><small><ul><li><!--more--><!--nextpage-->';

require_once("config.php");
require_once("cpaint.inc.php"); //AJAX library file

/*************************************************************
 * showSuggestions($word, $id)
 *
 * The showSuggestions function creates the list of up to 10
 * suggestions to return for the given misspelled word.
 *
 * $word - The misspelled word that was clicked on
 * $id - The id of the span containing the misspelled word.
 *
 *************************************************************/
function showSuggestions($word, $id){ 

	global $pspell_link; //the global link to the pspell module
	$retVal = "";
	
	$suggestions = pspell_suggest($pspell_link, $word);  //an array of all the suggestions that psepll returns for $word.
	
	$numSuggestions = count($suggestions);
	
	$numSuggestionsToReturn = 10;  //the maximum number of suggestions to return.
	
	//if the number of suggestions returned by pspell is less than the maximum number, just use the number of suggestions returned.
	if($numSuggestions < $numSuggestionsToReturn){
		$tmpNum = $numSuggestions;
	}
	else{ //else, just the custom number
		$tmpNum = $numSuggestionsToReturn;
	}
	
	if ($tmpNum > 0) {
		$retVal .= "<table>";

		//this creates the table of suggestions.
		//in the onClick event it has a call to the replaceWord javascript function which does the actual replacing on the page
		for($i=0; $i<$tmpNum; $i++) {
			$retVal .= "<tr><td onmouseover=\"this.style.backgroundColor='004080'; this.style.color='FFFFFF';\" onmouseout=\"this.style.backgroundColor='E8F1FF'; this.style.color='000000';\"><span class=\"suggestion\" onClick=\"replaceWord('" . addslashes_custom($id) . "', '" . addslashes($suggestions[$i]) . "')\">$suggestions[$i]</span></td></tr>";
		}
	
		$retVal .= "</table>";
	}
	else {
		$retVal .= "No Suggestions";
	}
	
	return $retVal;  //a string containing the table of suggestions.
	
} //end showSuggestions function


/*************************************************************
 * spellCheck($string)
 *
 * The spellCheck function takes the string of text entered
 * in the text box and spell checks it.  It splits the text
 * on anything inside of < > in order to prevent html from being
 * spell checked.  Then any text is split on spaces so that only
 * one word is spell checked at a time.  This creates a multidimensional
 * array.  The array is flattened.  The array is looped through
 * ignoring the html lines and spell checking the others.  If a word
 * is misspelled, code is wrapped around it to highlight it and to
 * make it clickable to show the user the suggestions for that
 * misspelled word.
 *
 * $string - The string of text from the text box that is to be
 *           spell checked.
 *
 *************************************************************/
function spellCheck($string) {
   
   global $pspell_link; //the global link to the pspell module
   $retVal = "";
   $isThereAMisspelling = false; //at first we just assume that the text is all correctly spelled unless we find differently.
   
   $string = stripslashes_custom($string); //we only need to strip slashes if magic quotes are on
   
   //make all the returns in the text look the same
   $string = preg_replace("/\r?\n/", "\n", $string);
   
   //splits the string on any html tags, preserving the tags and putting them in the $words array
   $words = preg_split("/(<[^>]*>)/", $string, -1, PREG_SPLIT_DELIM_CAPTURE);
   
   $numResults = count($words); //the number of elements in the array.
   
  //this loop looks through the words array and splits any lines of text that aren't html tags on space, preserving the spaces.
  for($x=0; $x<$numResults; $x++){
   	if(!preg_match("/<[^>]*>/", $words[$x])){ //if the line is not an html tag
	 	$words[$x] = preg_split("/(\s+)/", $words[$x], -1, PREG_SPLIT_DELIM_CAPTURE); //then split it on the spaces
	}
	else{ //otherwise, we wrap all the html tags in comments to make them not displayed
		$words[$x] = preg_replace("/</", "<!--<", $words[$x]);
   		$words[$x] = preg_replace("/>/", ">-->", $words[$x]);
	}
  }
  
  $words = flattenArray($words); //flatten the array to be one dimensional.
  
  $numResults = count($words); //the number of elements in the array after it's been flattened.
  
  
  //goes through the words array again and spell checks the words as long as it's not an html and it's matches /[A-Z']{1,16}/i
  for ($i=0; $i<$numResults; $i++) {	
	if(!preg_match("/<[^>]*>/", $words[$i])){ //ignore any html tags
	
		  preg_match("/[A-Z']{1,16}/i", $words[$i], $tmp); //get the word that is in the array slot $i
		  $tmpWord = $tmp[0]; //should only have one element in the array anyway, so it's just assign it to $tmpWord
		  
		  //if it's misspelled then we set isAllCorrect to false because we found a misspelling.
		  //And we replace the word in the array with the span that highlights it and gives it an onClick parameter to show the suggestions.
		  if (!pspell_check($pspell_link, $tmpWord)) {			 
			 $isThereAMisspelling = true;		 
			 $onClick = "onClick=\"showSuggestions('" . addslashes($tmpWord) . "', '" . $i . "_" . addslashes($tmpWord) . "');\"";
			 $words[$i] = str_replace($tmpWord, "<span " . $onClick . " id=\"" . $i . "_" . $tmpWord . "\" class=\"highlight\">" . stripslashes($tmpWord) . "</span>", $words[$i]); 
		  }
		  
		  $words[$i] = preg_replace("/\n/", "<br />", $words[$i]); //replace any breaks with <br />'s, for html display
	  }
   }//end for

   $string = ""; //return string
   
   //if there were no misspellings, start the string with a 0.
   if(!$isThereAMisspelling){
     $string = "0";
   }
   else{ //else, there were misspellings, start the string with a 1.
   	$string = "1";
   }
	
   for($i=0; $i<$numResults; $i++){
	$string .= $words[$i];
   }
	

	//remove comments from around all html tags except for <a> because we don't want the links to be clickable
	//but we want the html to be rendered in the div for preview purposes.
	$string = preg_replace("/<!--<br( [^>]*)?>-->/i", "<br />", $string);
	$string = preg_replace("/<!--<p( [^>]*)?>-->/i", "<p>", $string);
	$string = preg_replace("/<!--<\/p>-->/i", "</p>", $string);
	$string = preg_replace("/<!--<b( [^>]*)?>-->/i", "<b>", $string);
	$string = preg_replace("/<!--<\/b>-->/i", "</b>", $string);
	$string = preg_replace("/<!--<strong( [^>]*)?>-->/i", "<strong>", $string);
	$string = preg_replace("/<!--<\/strong>-->/i", "</strong>", $string);
	$string = preg_replace("/<!--<i( [^>]*)?>-->/i", "<i>", $string);
	$string = preg_replace("/<!--<\/i>-->/i", "</i>", $string);
	$string = preg_replace("/<!--<small( [^>]*)?>-->/i", "<small>", $string);
	$string = preg_replace("/<!--<\/small>-->/i", "</small>", $string);
	$string = preg_replace("/<!--<ul( [^>]*)?>-->/i", "<ul>", $string);
	$string = preg_replace("/<!--<\/ul>-->/i", "</ul>", $string);
	$string = preg_replace("/<!--<li( [^>]*)?>-->/i", "<li>", $string);
	$string = preg_replace("/<!--<\/li>-->/i", "</li>", $string);
	$string = preg_replace("/<!--<img (?:[^>]+ )?src=\"?([^\"]*)\"?[^>]*>-->/i", "<img src=\"\\1\" />", $string);
	
	return $string;  //the string containing all the markup for the misspelled words.

} //end spellCheck function



/*************************************************************
 * flattenArray($array)
 *
 * The flattenArray function is a recursive function that takes a
 * multidimensional array and flattens it to be a one-dimensional
 * array.  The one-dimensional flattened array is returned.
 *
 * $array - The array to be flattened.
 *
 *************************************************************/
function flattenArray($array)
{
   $flatArray = array();
   foreach($array as $subElement){
       if(is_array($subElement))
           $flatArray = array_merge($flatArray, flattenArray($subElement));
       else
           $flatArray[] = $subElement;
   }
   return $flatArray;
   
} //end flattenArray function


/*************************************************************
 * stripslashes_custom($string)
 *
 * This is a custom stripslashes function that only strips
 * the slashes if magic quotes are on.  This is written for
 * compatibility with other servers in the event someone doesn't
 * have magic quotes on.
 *
 * $string - The string that might need the slashes stripped.
 *
 *************************************************************/
function stripslashes_custom($string){

	if(get_magic_quotes_gpc()){
		return stripslashes($string);
	}
	else {
		return $string;
	}
}

/*************************************************************
 * addslashes_custom($string)
 *
 * This is a custom addslashes function that only adds
 * the slashes if magic quotes are off.  This is written for
 * compatibility with other servers in the event someone doesn't
 * have magic quotes on.
 *
 * $string - The string that might need the slashes added.
 *
 *************************************************************/
function addslashes_custom($string){

	if(!get_magic_quotes_gpc()){
		return addslashes($string);
	}
	else {
		return $string;
	}
}


/*************************************************************
 * switchText($string)
 *
 * This function prepares the text to be sent back to the text
 * box from the div.  The comments are removed and breaks are
 * converted back into \n's.  All the html tags that the user
 * might have entered that aren't on the approved list:
 * <p><br><a><b><strong><i><small><ul><li> are stripped out.
 * The user-entered returns have already been replaced with
 * $u2026 so that they can be preserved.  I replace all the 
 * \n's that might have been added by the browser (Firefox does
 * this in trying to pretty up the HTML) with " " so that 
 * everything will look the way it did when the user typed it
 * in the box the first time.
 *
 * $string - The string of html from the div that will be sent
 *           back to the text box.
 *
 *************************************************************/
function switchText($string){
	global $allowed_html;
	$string = preg_replace("/<!--/", "", $string);
	$string = preg_replace("/-->/", "", $string);
	$string = preg_replace("/\r?\n/", " ", $string);
	$string = strip_tags($string, $allowed_html);
	$string = stripslashes_custom($string); //we only need to strip slashes if magic quotes are on
	$string = html_entity_decode($string);
	return $string;
	
} //end switchText function

?>