<?php
/***************************************************************************

Plugin Name: WP Calais Archive Tagger
Plugin URI: http://www.dangrossman.info/wp-calais-archive-tagger
Description: Tags your entire post archive by performing semantic analysis on the post text.
Version: 1.5
Author: Dan Grossman
Author URI: http://www.dangrossman.info

***************************************************************************/

//Initialization to add the menu item to the plugins page
add_action('admin_menu', 'calais_archive_init');
function calais_archive_init() {
        add_submenu_page('plugins.php', 'Calais Archive Tagger', 'Calais Archive Tagger', 'manage_options', 'calais-archive-tagger', 'calais_archive');
}

function calais_archive() {

        if (isset($_POST['calais-api-key'])) {
		update_option('calais-api-key', $_POST['calais-api-key']);
	}
					
	?>
	<script type='text/javascript' src='<?php bloginfo( 'wpurl' ); ?>/wp-includes/js/jquery/jquery.js?ver=1.1.4'></script>
	<script type="text/javascript">
	//<![CDATA[

        function calais_archive_run(os) {
                jQuery.post('<?php bloginfo( 'wpurl' ); ?>/wp-admin/admin-ajax.php', {action: 'calais_archive_run', offset: os, cookie: document.cookie}, calais_archive_return);
        }
	
	function calais_archive_return(text) {
		if (text == '#DONE#') {
			jQuery('#archive_status').html('<b>Status:</b> Tagging complete.');
		} else if (text == '#KEY#') {
			jQuery('#archive_status').html('<b>Status:</b> Error');
			jQuery('#archive_tags').html('You must enter and save your Calais API Key first.');
		} else {
			jQuery('#archive_status').html('<b>Status:</b> Tagging in progress...');
			var old = jQuery('#archive_tags').html();

			var parts = text.split("|");
			var offset = parseInt(parts[0]) + 1;
			var tags = parts[2];
			var taglist = tags.replace(/,/g, ", ");	
			
			var utext = "Tagged post #" + parts[1] + ": <b>" + taglist + "</b>";
			
			jQuery('#archive_tags').html(old + utext + "<br/><br />");
			calais_archive_run(offset);
		}
	}

	//]]>
	</script>

        <div class="wrap">
        <h2>Calais Archive Tagger</h2>

	<div style="float: left; width: 25%; padding-right: 20px">
		<h3>Configuration</h3>	
		<form action="" method="post">

		<p>
			<label for="calais-api-key">What is your Calais API key?</label>
			<br />
			<input type="text" name="calais-api-key" value="<?php echo get_option('calais-api-key'); ?>" />
			<br />
			<input type="submit" value="Save" />
		</p>

		</form>
	</div>

	<div style="float: left; width: 70%">
		<h3>Archive Tagger</h3>
		
		<div id="archive_status"><b>Status:</b> <a href="#" onclick="calais_archive_run(0)">Click here</a> to start tagging your posts.</div>
		<div id="archive_tags" style="margin: 10px 0"></div>
	</div>

	</div>
  <?php

}

//Register an AJAX hook for the function to get the tags
add_action('wp_ajax_calais_archive_run', 'calais_archive_run');

//This is the function that runs when the author requests tags for
//their post. It connects to the Open Calais API, sends the post text,
//parses the entities returned and puts them into a tag list to return
function calais_archive_run() {

	//Include the class
    if (!class_exists('OpenCalais'))
    	require_once('opencalais.php');

	//Check the API key is set
    $key = get_option('calais-api-key');
    if (empty($key))
    	die("#KEY#");

	//Determine which post to analyze
    $offset = 0;
    if (isset($_POST['offset']))
    	$offset = $_POST['offset'];
    
    //Retrieve the post
    $post = get_posts("&numberposts=1&orderby=ID&order=ASC&offset=$offset");
    $post = $post[0];
    
    if (empty($post) || empty($post->ID))
    	die("#DONE#");

    $entities = array();
    $tags = array();
    $newtags = array();
    $content = $post->post_title . " " . $post->post_content;

    $existing_tags = wp_get_post_tags($post->ID);

    if (count($existing_tags) > 0) {
        foreach ($existing_tags as $tag) {
            if ($tag->taxonomy == 'post_tag')
                $tags[] = $tag->name;
        }
    }

    try {
            $oc = new OpenCalais($key);
            $entities = $oc->getEntities($content);
    } catch (Exception $e) {
    }

    if (count($entities) > 0) {
        foreach ($entities as $type => $values) {
            if (count($values) > 0) {
                foreach ($values as $entity) {
                    if (strpos($entity, "http://") === false && strpos($entity, "@") === false && !in_array($entity, $newtags)) {
                        $tags[] = $entity;
                        $newtags[] = $entity;
                    }
                }
            }
        }
    }

    wp_set_post_tags($post->ID, implode($tags, ','));

    die($offset . "|" . $post->ID . "|" . implode($newtags, ','));

}