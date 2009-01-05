<?php
/*
Plugin Name: Excerpts Deluxe
Plugin URI: http://www.spacebabies.nl
Description: Publish excerpts with style. Choose length of excerpt and cutoff character. Adds a <a href="/wp-admin/options-general.php?page=sb_excerpt_options">submenu in the Options page</a>.
Version: 1
Author: <a href="mailto:joost@spacebabies.nl">Joost Baaij</a>
*/


// Defaults

define('SB_EXCERPTS_CUTOFF_LENGTH_DEFAULT', 25);
define('SB_EXCERPTS_CUTOFF_MODE_DEFAULT', 'words');
define('SB_EXCERPTS_CUTOFF_CHARACTER_DEFAULT', '&hellip;');


// Hooks

add_action('activate_excerpts-deluxe.php', 'sb_excerpts_install');
add_action('the_excerpt', 'sb_excerpts_display');
add_action('admin_menu', 'sb_excerpts_add_pages');


// Functions

/**
 * Install this plugin. Sets defaults for the options.
 */
function sb_excerpts_install() {
	update_option('sb_excerpts_cutoff_length', SB_EXCERPTS_CUTOFF_LENGTH_DEFAULT);
	update_option('sb_excerpts_cutoff_mode', SB_EXCERPTS_CUTOFF_MODE_DEFAULT);
	update_option('sb_excerpts_cutoff_character', SB_EXCERPTS_CUTOFF_CHARACTER_DEFAULT);
}

/**
 * Main function. Displays the excerpt.
 */
function sb_excerpts_display($excerpt) {
	global $post;
	
	// Posts that have an actual excerpt should display that and return.
	if($post->post_excerpt) {
		echo $post->post_excerpt;
		return;
	}
	
	
	// Otherwise we'll build the excerpt ourselves.
	$content = strip_tags($post->post_content);
	$cutoff_length = (int) get_option('sb_excerpts_cutoff_length');
	
	if(get_option('sb_excerpts_cutoff_mode')=='words') {
		$words = preg_split("/\s+/", $content);
		echo implode(" ", array_slice($words, 0, $cutoff_length));
		echo get_option('sb_excerpts_cutoff_character');
	} else if(get_option('sb_excerpts_cutoff_mode')=='characters') {
		echo substr($content, 0, $cutoff_length);
		echo get_option('sb_excerpts_cutoff_character');
	} else {
		// Display the originally intended excerpt as a last resort.
		echo $excerpt;
	}
}

/**
 * Add admin pages.
 */
function sb_excerpts_add_pages() {
	add_options_page('Excerpts', 'Excerpts', 8, 'sb_excerpt_options', 'sb_excerpts_options');
}

/**
 * Add submenu to Options admin page.
 */
function sb_excerpts_options() {
	    if($_SERVER['REQUEST_METHOD'] == 'POST' ) {
	        update_option("sb_excerpts_cutoff_length", $_POST['sb_excerpts_cutoff_length']);
	        update_option("sb_excerpts_cutoff_mode", $_POST['sb_excerpts_cutoff_mode']);
	        update_option("sb_excerpts_cutoff_character", $_POST['sb_excerpts_cutoff_character']);

	        // Put a message on the screen
			?><div class="updated"><p><strong>Options saved</strong></p></div><?php
	    }

	    // Now display the options editing screen
	    echo '<div class="wrap dbx-content">';
	    echo "<h2>Excerpt Options</h2>";
	
	    ?>
	<form method="post" action="<?php echo $_SERVER['REQUEST_URI']; ?>">
		<ol>
			<li>
				<label for="sb_excerpts_cutoff_length">Cutoff after</label>
				<input id="sb_excerpts_cutoff_length" name="sb_excerpts_cutoff_length" type="text" size="5" maxlength="8" value="<?php echo get_option('sb_excerpts_cutoff_length') ?>" />
				<input name="sb_excerpts_cutoff_mode" <?php if(get_option('sb_excerpts_cutoff_mode')=='words') echo "checked=\"checked\""?> type="radio" value="words" /> words
				<input name="sb_excerpts_cutoff_mode" <?php if(get_option('sb_excerpts_cutoff_mode')=='characters') echo "checked=\"checked\""?> type="radio" value="characters" /> characters
			</li>
		
			<li>
				<label for="sb_excerpts_cutoff_character">Cutoff character</label>
				<input id="sb_excerpts_cutoff_character" name="sb_excerpts_cutoff_character" type="text" size="5" maxlength="255" value="<?php echo get_option('sb_excerpts_cutoff_character') ?>" />
				<em>(default: <?php echo SB_EXCERPTS_CUTOFF_CHARACTER_DEFAULT ?>)</em>
			</li>
		</ol>

		<p class="submit">
			<input type="submit" name="Submit" value="Update Options" />
		</p>
	</form>
	</div>

	<?php
}
?>