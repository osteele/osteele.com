<?php 

require_once( dirname( __FILE__ ) . "/spellInclude.php");

function response_xml($return_val) {
	header("Content-type: text/xml");

	print '<?xml version="1.0" encoding="iso-8859-1" ?>';
	print '<data>';
	print "   <returnvalue>$return_val</returnvalue>";
	print '</data>';
}

function add_word_to_dictionary($word_to_add) {
	$current_settings = speller_get_settings('speller_settings');
	$aspell_dict_merge = "merge personal --lang=".$current_settings['language']." --personal=".$current_settings['aspell_dict']." < ";
    $success = true;
	$aspell_err = "";
    
	# create temp file
	$tempfilename = tempnam( $current_settings['tmpfiledir'], 'aspell_data_' );
	# open temp file, add the submitted text.
	if( $tempfile = fopen( $tempfilename, 'w' )) {
		$return_val = 0;
		$command_array = array();

		fwrite( $tempfile, $word_to_add );
		# exec aspell command - redirect STDERR to STDOUT
		$cmd = $current_settings['aspell_path']." $aspell_dict_merge ".$tempfilename." 2>&1";
		$aspellret = exec( $cmd, $command_array, $return_val );
		if( $return_val != 0 )
		{
			fwrite( $debug, "-------------------------------------------\nFAILED TO ADD WORDS TO DICTIONARY:\n" );
			foreach( $command_array as $line )
				fwrite( $debug, $line."\n" );
			fclose($debug);
			$success = false;
		}
	}
    else
	{
		$success = false;
	}

	unlink( $tempfilename );

    return $success;
}

# we only authorize a certain level of user to add words to the 
# dictionary.

get_currentuserinfo();
if( !speller_option_set('must_be_logged_in') || (speller_option_set('must_be_logged_in') && ($user_level >= speller_option_set('minimum_user_level_to_add'))))
{
	if( isset($_REQUEST["word"] ) )
	{
        $success = false;

		$word_to_add = str_replace("\\'", "'", $_REQUEST["word"] );
        if( speller_option_set('broken_aspell_support') )
		{
			$success = add_word_to_dictionary_manually( $word_to_add );
		}
        else
		{
			$success = add_word_to_dictionary( $word_to_add );
		}

		if( $success )
		{
			response_xml("SUCCESS");
		}
		else
		{
			response_xml("FAILURE");
		}  

	}
}

?>
