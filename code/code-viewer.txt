<?php
/*
Plugin Name: Code Viewer
Plugin URI: http://elasticdog.com/2004/09/code-viewer/
Description: Pulls source code from an external file and displays the associated code with line numbers.
Version: 1.1
Author: Aaron Schaefer
Author URI: http://elasticdog.com/
*/

/* Configuration Settings */
$default_path = "http://osteele.com/code/";  // the absolute path of your code folder

/* --- STOP EDITING ---  */

function code_viewer($text) {
	global $default_path;

	$count = preg_match_all('/<viewcode src="([^"]+)"(?: link="(?i:(yes|no))")?\s?\/>/', $text, $matches);

	for ($i = 0; $i < $count; $i++) {
		// Determine if the specified path is absolute, or relative to the root path
		// If it's neither, assume it's relative to the default path set on line 12
		if (strpos(($matches[1][$i]), 'http://') !== false) {
			$path = $matches[1][$i];
		} else if (substr(($matches[1][$i]), 0, 1) == '/') {
			$path = $_SERVER['DOCUMENT_ROOT'] . $matches[1][$i];
		} else {
			$path = $default_path . $matches[1][$i];
		}

		// Open the file
		// If the file can't be found, print an error message
		if ($lines = @file($path)) {
			$codelist = '<ol class="codelist">' . "\n";

			foreach ($lines as $line_num => $line) {
				$toggle = (($line_num % 2 == 0) ? "odd" : "even");  // set alternating class names for each line

				// If the line is blank, insert a space to prevent collapsing
				// Otherwise insert the line
				if (ltrim($line) == "") {
					$codelist .= "\t" . '<li class="' . $toggle . '">&nbsp;</li>' . "\n";
				} else {
					$numtabs = strlen($line) - strlen(ltrim($line));  // determine the number of tabs
					$line = trim($line);                              // trim leading/trailing whitespace

					$codelist .= "\t" . '<li class="tab' . $numtabs . ' ' . $toggle . '"><code>' . htmlspecialchars($line) . '</code></li>' . "\n";
				}
			}

			// If requested, insert a link to the source file
			if (strtolower($matches[2][$i]) == "yes") {
				$filename = substr(strrchr($path, '/'), 1);

				$codelist .= "\t" . '<li class="sourcelink"><strong>Download this code:</strong> <a href="' . $path . '">' . $filename . '</a></li>' . "\n";
			}

			$codelist .= "</ol>";
		} else {
			$codelist = '<p class="warning">[The requested file <kbd>' . $path . '</kbd> could not be found]</p>';
		}

		$text = str_replace(($matches[0][$i]), $codelist, $text);
	}

	return $text;
}

function fix_bad_p($text) {
	$text = str_replace('<p><ol class="codelist">', '<ol class="codelist">', $text);
	$text = str_replace('</ol></p>', '</ol>', $text);

	return $text;
}

add_filter('the_content', 'code_viewer', 9);
add_filter('the_content', 'fix_bad_p');
?>