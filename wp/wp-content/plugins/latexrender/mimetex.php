<?php
/*
* Use mimetex instead of LatexRender
If you find that you get an error message similar to
	Warning: system() has been disabled for security reasons in /your.path/mimetex.php on line 31
	Output lines = 0
then change the $system_disabled line so that it reads $system_disabled=1
*/

function mimetex($text) {
		// --------------------------------------------------------------------------------------------------
		// adjust this to match your system configuration
		$mimetex_path = "/home/domain_name/public_html/cgi-bin/mimetex.cgi";
		$mimetex_path_http = "http://domain_name/mimetex";
		$mimetex_cgi_path_http="http://domain_name/cgi-bin/mimetex.cgi";
		$pictures_path = "/home/domain_name/public_html/mimetex/pictures";
		// --------------------------------------------------------------------------------------------------

		// change $system_disabled to 1 if you get an error message similar to
		// Warning: system() has been disabled for security reasons
		$system_disabled=0;

		$pictures_path_http = $mimetex_path_http."/pictures";

        preg_match_all("#\[tex\](.*?)\[/tex\]#si",$text,$tex_matches);

        for ($i=0; $i < count($tex_matches[0]); $i++) {
			$pos = strpos($text, $tex_matches[0][$i]);
			$mimetex_formula = $tex_matches[1][$i];

		    $formula_hash = md5($mimetex_formula);

			$filename = $formula_hash.".gif";
			$full_path_filename = $pictures_path."/".$filename;

			if (is_file($full_path_filename)) {
				$url = $pictures_path_http."/".$filename;
			} else {
			//	$command = "$mimetex_path -d ".escapeshellarg($mimetex_formula)." >$full_path_filename";
				$command = "$mimetex_path -e ".$full_path_filename." ".escapeshellarg($mimetex_formula);
				if ($system_disabled==0) {
					system($command,$status_code);
				} else {
					$status_code=0;
				}

				if ($status_code != 0) {
					$url=false;
				} else {
					$url = $pictures_path_http."/".$filename;
				}
			}

			$alt_mimetex_formula = htmlentities($mimetex_formula, ENT_QUOTES);
			$alt_mimetex_formula = str_replace("\r","&#13;",$alt_mimetex_formula);
			$alt_mimetex_formula = str_replace("\n","&#10;",$alt_mimetex_formula);

			if ($url != false) {
				if ($system_disabled==0) {
					$text = substr_replace($text, "<img src='".$url."' title='".$alt_mimetex_formula."' alt='".$alt_mimetex_formula."' align=absmiddle>",$pos,strlen($tex_matches[0][$i]));
				} else {
					$text = substr_replace($text, "<img src='".$mimetex_cgi_path_http."?".$mimetex_formula."' title='".$alt_mimetex_formula."' alt='".$alt_mimetex_formula."' align=absmiddle>",$pos,strlen($tex_matches[0][$i]));
				}
			} else {
				$text = substr_replace($text, "[Mimetex cannot convert this formula]",$pos,strlen($tex_matches[0][$i]));
			}
		}
    return $text;
}

?>