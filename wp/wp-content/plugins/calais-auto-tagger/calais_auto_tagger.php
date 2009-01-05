<?php
/***************************************************************************

Plugin Name: WP Calais Auto Tagger
Plugin URI: http://www.dangrossman.info/wp-calais-auto-tagger
Description: Suggests tags for your posts based on semantic analysis of your post content with the Open Calais API.
Version: 1.0
Author: Dan Grossman
Author URI: http://www.dangrossman.info

***************************************************************************/

//Include the Open Calais Tags class by Dan Grossman
//http://www.dangrossman.info/open-calais-tags
require('opencalais.php');

//Initialization to add the box to the post page
add_action('admin_menu', 'calais_init');
function calais_init() {
	if (function_exists('add_meta_box')) {
		add_meta_box('calais', 'Calais Auto Tagger', 'calais_box', 'post', 'advanced');
	} else {
		add_action('dbx_post_sidebar', 'calais_box', 1);
	}
	add_submenu_page('plugins.php', 'Calais Configuration', 'Calais Configuration', 'manage_options', 'calais-key-config', 'calais_conf');
}

function calais_box() {
	?>
	<script type="text/javascript">
	//<![CDATA[

	function calais_gettags() {
		jQuery.post('<?php bloginfo( 'wpurl' ); ?>/wp-admin/admin-ajax.php', {text: calais_getcontent(), action: 'calais_gettags', cookie: document.cookie}, calais_showtags);
			
	}
	
	function calais_getcontent() {
		var form = document.getElementById('post');
		if (typeof tinyMCE != 'undefined' && !tinyMCE.selectedInstance.spellcheckerOn) {
			if (typeof tinyMCE.triggerSave == 'function') {
				tinyMCE.triggerSave();
			} else {
				tinyMCE.wpTriggerSave();
			}	
		}
		return form.content.value;
	}
	
	function calais_showtags(tags) {
		document.getElementById('calais_suggestions').innerHTML = tags;
		document.getElementById('calais_suggestions_label').style.display = 'inline';
		document.getElementById('calais_suggestions_add').style.display = 'inline';
	}

	function calais_savetags() {
		var newtags = jQuery('#tags-input').val() + ', ' + jQuery('#calais_suggestions').html();
		newtags = newtags.replace( /\s+,+\s*/g, ',' ).replace( /,+/g, ',' ).replace( /,+\s+,+/g, ',' ).replace( /,+\s*$/g, '' ).replace( /^\s*,+/g, '' );
		jQuery('#tags-input').val( newtags );
		if (typeof tag_update_quickclicks == 'function') {
			tag_update_quickclicks();
		}
		jQuery('#newtag').val('');
		jQuery('#newtag').focus();
	}
	
	//]]>
	</script>

	<?php if (!function_exists('add_meta_box')): ?>
	<fieldset id="calais_dbx" class="dbx-box">
	<h3 class="dbx-handle">Calais Auto Tagger</h3>
	<div class="dbx-content">
	<?php endif; ?>
	
	<input type="button" class="button" onclick="calais_gettags()" value="Get Tags" /><br /><br />

	<span style='font-size: 10pt; font-weight: bold; display: none' id="calais_suggestions_label">Suggestions:</span>
	
	<div id="calais_suggestions" style='font-size: 10pt; margin-bottom: 10px'>

	</div>

	<input type="button" class="button" id="calais_suggestions_add" onclick="calais_savetags()" value="Add These Tags" style="display: none" />

        <?php if (!function_exists('add_meta_box')): ?>
	</div>
	</fieldset>
        <?php 
	endif;	
}

function calais_conf() {

	if (isset($_POST['calais-api-key'])) {
		update_option('calais-api-key', $_POST['calais-api-key']);
	}
	
	?>

	<div class="wrap">
	<h2>Calais Configuration</h2>
	<div class="narrow">
	<form action="" method="post" id="calais-conf" style="">
	
	<p>The Calais Auto Tagger plugin requires an Open Calais API key. If you don't have one, <a href"http://www.opencalais.com/" target="_blank">visit their site</a>, and click the "Register" link at the top of the page. Once you have an account, <a href="http://developer.opencalais.com/apps/register" target="_blank">fill out this form</a> to get an API key.</p>
	
	<p>
		<label for="calais-api-key">What is your Open Calais API Key?</label><br />
		<input type="text" name="calais-api-key" value="<?php echo get_option('calais-api-key'); ?>" />
	</p>

	<p class="submit">
		<input type="submit" value="Submit" />
	</p>
		
	</form>
	</div>
	</div>

	<?php	
}

//Register an AJAX hook for the function to get the tags
add_action('wp_ajax_calais_gettags', 'calais_gettags');

//This is the function that runs when the author requests tags for 
//their post. It connects to the Open Calais API, sends the post text,
//parses the entities returned and puts them into a tag list to return
function calais_gettags() {
	
	$content = stripslashes($_POST['text']);

	$key = get_option('calais-api-key');
	if (empty($key)) {
		die("You have not yet configured this plugin. You must add your Calais API key from the plugins page.");
	}
	
	$oc = new OpenCalais($key);
	$entities = $oc->getEntities($content);
	
	if (count($entities) == 0)
		die("No Tags");

	foreach ($entities as $type => $values) {
		if (count($values) > 0) {
			foreach ($values as $entity) {
				$tags[] = $entity;
			}
		}
	}

	die(implode($tags, ', '));
	
}
