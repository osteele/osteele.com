<?php

$aspell_word_list   = dirname(__FILE__)."/aspell-word-list.txt";

if (! isset($wp_version))
{
    require_once (dirname(dirname(dirname(__FILE__))) . "/wp-config.php");
    global $wp_version;
}

global $wp_13;

if (substr($wp_version, 0, 3) == "1.1" || substr($wp_version, 0, 3) == "1.0")
{
    echo "SPELL CHECKER NEEDS AT LEAST WP VERSION 1.2";
    return;

}
elseif (substr($wp_version, 0, 3) == "1.2")
{ // ADD WP 1.2 Compatibility

    $insert_html = true;
}
else
{
    $wp_13 = true;
    $insert_html = false;
}   

if (! function_exists('speller_update_option'))
{
    if ($wp_13) 
    {
        function speller_update_option($option, $new_settings) 
        {
            update_option($option, $new_settings);
        }
        function speller_get_settings($option)
        {
            $settings = get_settings($option);

            // HACK for problems with some WordPress 1.3 installations.
            if( is_string($settings) )
            {
                $unserialized = @ unserialize(stripslashes($settings));
                if( $unserialized !== FALSE )
                    $settings = $unserialized;
            }
            return $settings;
        }
    }
    else
    {
        function speller_update_option($option, $new_settings) 
        {
            update_option($option, base64_encode(serialize($new_settings)));
        }
        function speller_get_settings($option)
        {
            return unserialize(base64_decode(get_settings($option)));
        }
    }
}

if (! function_exists('speller_option_set'))
{
    function speller_option_set($option) 
    {
        if (! $options = speller_get_settings('speller_options'))
            return false;
        else
            return (in_array($option, $options));
    }

}

if( ! function_exists('create_dictionary') )
{
    function create_dictionary()
    {
        $current_settings = speller_get_settings('speller_settings');
        $aspell_dict_create = "create personal --lang=".$current_settings['language']." --personal=".$current_settings['aspell_dict']." < ";
        global $aspell_word_list;

        if( speller_option_set( 'broken_aspell_support' ) )
        {
            $dict = @fopen( $current_settings['aspell_dict'], 'wb' );
            if( file_exists( $aspell_word_list ) ) 
            {
                $lines = file( $aspell_word_list );
                $count = count( $lines );
                @fwrite( $dict, "personal_ws-1.1 ".$current_settings['language']." $count\n" );
                foreach( $lines as $line )
                {
                    @fwrite( $dict, $line );
                }
            }
            else
            {
                return false;
            }
            @fclose( $dict );

            if( file_exists( $current_settings['aspell_dict'] ) ) {
                return true;
            } else {
                return false;
            }

        }
        else
        {
            $cmd = $current_settings['aspell_path']." $aspell_dict_create $aspell_word_list  2>&1";
            shell_exec( $cmd );
            if( !file_exists( $current_settings['aspell_dict'] ) ) {
                // Great, aspell is broken. Automatically use the 
                // "broken aspell support" option.
                $current_options = speller_get_settings('speller_options');
                $current_options[] = 'broken_aspell_support';
                speller_update_option( 'speller_options', $current_options );

                // Call recursively to reach the "broken aspell" option.
                create_dictionary();
            }
            else
            {
                return true;
            }
        }
    }
}

if( ! function_exists('update_personal_dictionary') )
{
    function update_personal_dictionary($words)
    {
        $current_settings = speller_get_settings('speller_settings');
        $success = true;

        if( file_exists( $current_settings['aspell_dict'] ) )
        {
            $oldlines = file( $current_settings['aspell_dict'] );
            $words = preg_replace( "#[\s]+#", " ", rtrim($words) );
            $lines = explode( " ", $words );
            $count = count( $lines );

            if( ( $dict = @fopen( $current_settings['aspell_dict'], 'wb' ) ) !== false )
            {
                // Empty the file.
                @ftruncate( $dict, 0);    

                // Now, update the word count on the first line. It's the last of three 
                // space-delimited strings (e.g. personal_ws-1.1 english 15)
                $elements = explode( ' ', $oldlines[0] );
                $elements[1] = $current_settings['language'];
                $elements[2] = strval( $count );
                $oldlines[0] = implode( ' ', $elements ) . "\n";

                @fwrite( $dict, $oldlines[0] );

                // Write the new file back out. 
                foreach( $lines as $line ) 
                {
					$word_to_add = str_replace("\\'", "'", $line );
                    if( !@fwrite( $dict, $word_to_add."\n" ) )
                    {
                        $success = false;
                    } 
                }
                @fclose( $dict );

            }
            else
            {
                $success = false;
            } 
        }
        else
        {
            $success = false;
        } 

        return $success;

    }
}


if( ! function_exists( 'add_word_to_dictionary_manually' ) )
{
    function add_word_to_dictionary_manually($word_to_add) {
        $current_settings = speller_get_settings('speller_settings');
        
        if( file_exists( $current_settings['aspell_dict'] ) )
        {
            // Write the new word to the end of the file.
            $lines = @file( $current_settings['aspell_dict'] );
            $lines[0] = $word_to_add."\n";
            $words = implode( "",$lines);

            return update_personal_dictionary($words);
        }
        else
        {
            return false;
        } 
    }
}


?>
