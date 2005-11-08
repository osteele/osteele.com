<?php
/*
Plugin Name: Subscribe me
Plugin URI: http://www.semiologic.com/projects/subscribe-me/
Description: <a href="http://www.semiologic.com/projects/subscribe-me/">Doc/FAQ</a> &bull; <a href="http://wordpress.org/tags/semiologic">Support forum</a> &#8212; Displays a tile with subscribe buttons. To use, call sem_subscribe_me(); where you want the tile to appear. Alternatively, do nothing and the tile will display when wp_meta(); is called. Admin interface is courtesy of <a href="http://blog.dukethor.info/">Duke Thor</a>.
Author: Denis de Bernardy
Version: 1.2
Author URI: http://www.semiologic.com
*/

/*
 * Terms of use
 * ------------
 * Except where otherwise noted, this software is:
 * - Copyright 2005, Denis de Bernardy
 * - Licensed under the terms of the CC/GNU GPL
 *   http://creativecommons.org/licenses/GPL/2.0/
 * - Provided as is, with NO WARRANTY whatsoever
**/


/*
 * Acknowledgements:
 * - James Huff (http://www.macmanx.com/), for cleaned up gifs
 * - Duke Thor (http://blog.dukethor.info/), for the admin interface
**/

// sem_subscribe_me_menu()

function sem_subscribe_me_menu() {
    if (function_exists('add_options_page')) {
        add_options_page('Subscribe Me', 'Subscribe Me', 9, basename(__FILE__), 'sem_subscribe_me_admin');
    }
}

// sem_subscribe_me_admin()

function sem_subscribe_me_admin() {
    if (isset($_POST['sem_subscribe_me_info_update'])) {
        $sem_subscribe_me_services = array('sem_subscribe_me_service_local' => $_POST['sem_subscribe_me_service_local'], 
            'sem_subscribe_me_service_bloglines' => $_POST['sem_subscribe_me_service_bloglines'], 
            'sem_subscribe_me_service_mymsn' => $_POST['sem_subscribe_me_service_mymsn'], 
            'sem_subscribe_me_service_myyahoo' => $_POST['sem_subscribe_me_service_myyahoo'], 
            'sem_subscribe_me_service_newsgator' => $_POST['sem_subscribe_me_service_newsgator'], 
            'sem_subscribe_me_service_newsisfree' => $_POST['sem_subscribe_me_service_newsisfree'], 
            'sem_subscribe_me_service_myfeedster' => $_POST['sem_subscribe_me_service_myfeedster']);
        update_option('sem_subscribe_me_options', $sem_subscribe_me_services);
        echo '<div class="updated">
';
        echo '<p>
';
        echo '<strong>';
        echo _e('Subscribe Me Information Saved');
        echo '</strong>
';
        echo '</p>
';
        echo '</div>
';
	}
	
	$sem_subscribe_me_services = get_option('sem_subscribe_me_options');

	echo '<div class="wrap">
';
	echo '<h2>';
	echo _e('Subscribe Me Options');
	echo '</h2>
';
	echo '<form method="post" action="">
';
	echo '<input type="hidden" name="sem_subscribe_me_info_update" value="updated" />
';
	echo '<fieldset class="options">
';
	echo '<legend>Services</legend>
';
	echo '<ul>
';
	echo '<li>
';
	echo '<label for="sem_subscribe_me_service_local">
';
	echo '<input type="checkbox" name="sem_subscribe_me_service_local" id="sem_subscribe_me_service_local" value="1" ';
	checked('1', $sem_subscribe_me_services['sem_subscribe_me_service_local']);
	echo ' /> ';
	echo _e('Local RSS2 Feed');
	echo '
';
	echo '</label>
';
	echo '</li>
';
	echo '<li>
';
	echo '<label for="sem_subscribe_me_service_bloglines">
';
	echo '<input type="checkbox" name="sem_subscribe_me_service_bloglines" id="sem_subscribe_me_service_bloglines" value="1" ';
	checked('1', $sem_subscribe_me_services['sem_subscribe_me_service_bloglines']);
	echo ' /> ';
	echo _e('Bloglines');
	echo '
';
	echo '</label>
';
	echo '</li>
';
	echo '<li>
';
	echo '<label for="sem_subscribe_me_service_mymsn">
';
	echo '<input type="checkbox" name="sem_subscribe_me_service_mymsn" id="sem_subscribe_me_service_mymsn" value="1" ';
	checked('1', $sem_subscribe_me_services['sem_subscribe_me_service_mymsn']);
	echo ' /> ';
	echo _e('My MSN');
	echo '
';
	echo '</label>
';
	echo '</li>
';
	echo '<li>
';
	echo '<label for="sem_subscribe_me_service_myyahoo">
';
	echo '<input type="checkbox" name="sem_subscribe_me_service_myyahoo" id="sem_subscribe_me_service_myyahoo" value="1" ';
	checked('1', $sem_subscribe_me_services['sem_subscribe_me_service_myyahoo']);
	echo ' /> ';
	echo _e('My Yahoo!');
	echo '
';
	echo '</label>
';
	echo '</li>
';
	echo '<li>
';
	echo '<label for="sem_subscribe_me_service_newsgator">
';
	echo '<input type="checkbox" name="sem_subscribe_me_service_newsgator" id="sem_subscribe_me_service_newsgator" value="1" ';
	checked('1', $sem_subscribe_me_services['sem_subscribe_me_service_newsgator']);
	echo ' /> ';
	echo _e('Newsgator');
	echo '
';
	echo '</label>
';
	echo '</li>
';
	echo '<li>
';
	echo '<label for="sem_subscribe_me_service_newsisfree">
';
	echo '<input type="checkbox" name="sem_subscribe_me_service_newsisfree" id="sem_subscribe_me_service_newsisfree" value="1" ';
	checked('1', $sem_subscribe_me_services['sem_subscribe_me_service_newsisfree']);
	echo ' /> ';
	echo _e('NewsIsFree');
	echo '
';
	echo '</label>
';
	echo '</li>
';
	echo '<li>
';
	echo '<label for="sem_subscribe_me_service_myfeedster">
';
	echo '<input type="checkbox" name="sem_subscribe_me_service_myfeedster" id="sem_subscribe_me_service_myfeedster" value="1" ';
	checked('1', $sem_subscribe_me_services['sem_subscribe_me_service_myfeedster']);
	echo ' /> ';
	echo _e('My Feedster');
	echo '
';
	echo '</label>
';
	echo '</li>
';
	echo '</ul>
';
	echo '</fieldset>
';
	echo '<p class="submit">
';
    echo '<input type="submit" name="Submit" value="';
    echo _e('Update Options');
    echo '" />
'; 
	echo '</p>
'; 
	echo '</form>
';
	echo '</div>
';
}



/*
 * sem_subscribe_me()
 * -------------------
 * 
**/

function sem_subscribe_me()
{
global $sem_captions;
global $sem_subscribe_me_done;

if ( $sem_subscribe_me_done )
	return;

// get new options from database

if (FALSE === ($sem_subscribe_me_services = get_option('sem_subscribe_me_options'))) {
    return;
}

$blog_url = get_settings('home');
$feed_url = apply_filters('bloginfo',get_feed_link('rss2'),'rss2_url');

if ( @file_exists( ABSPATH . 'wp-content/plugins/addbloglines.gif' ) )
	$img_dir = trailingslashit(get_settings('home')) . 'wp-content/plugins/';
elseif ( @file_exists( ABSPATH . 'wp-content/plugins/sem-subscribe-me/addbloglines.gif' ) )
	$img_dir = trailingslashit(get_settings('home')) . 'wp-content/plugins/sem-subscribe-me/';

if ( $sem_captions['cap_subscribe'] )
	$cap_subscribe = $sem_captions['cap_subscribe'];
else
	$cap_subscribe = 'Subscribe';

if ( $sem_captions['cap_rss_feed'] )
	$cap_rss_feed = $sem_captions['cap_rss_feed'];
else
	$cap_rss_feed = 'RSS feed';

?>
<h2><?php echo $cap_subscribe; ?></h2>

<ul>

<?php if( '1' == $sem_subscribe_me_services['sem_subscribe_me_service_local']) { ?>
<li><a href="<?php echo $feed_url; ?>"><img src="<?php echo $img_dir . "xml.gif"; ?>" align="middle" alt="" style="margin-right: 4px; border: none;" /><?php echo $cap_rss_feed; ?></a>
</li>
<?php } ?>
<?php if( '1' == $sem_subscribe_me_services['sem_subscribe_me_service_bloglines']) { ?>
<li><a href="http://www.bloglines.com/sub/<?php echo $feed_url; ?>"><img src="<?php echo $img_dir . "addbloglines.gif"; ?>" align="middle" alt="Bloglines" style="border: none;" /></a>
</li>
<?php } ?>
<?php if( '1' == $sem_subscribe_me_services['sem_subscribe_me_service_mymsn']) { ?>
<li><a href="http://my.msn.com/addtomymsn.armx?id=rss&ut=<?php echo $feed_url; ?>&ru=<?php echo $blog_url; ?>"><img src="<?php echo $img_dir . "addmymsn.gif"; ?>" align="middle" alt="MyMSN" style="border: none;" /></a>
</li>
<?php } ?>
<?php if( '1' == $sem_subscribe_me_services['sem_subscribe_me_service_myyahoo']) { ?>
<li><a href="http://add.my.yahoo.com/rss?url=<?php echo $feed_url; ?>"><img src="<?php echo $img_dir . "addmyyahoo.gif"; ?>" align="middle" alt="MyYahoo!" style="border: none;" /></a>
</li>
<?php } ?>
<?php if( '1' == $sem_subscribe_me_services['sem_subscribe_me_service_newsgator']) { ?>
<li><a href="http://www.newsgator.com/ngs/subscriber/subext.aspx?url=<?php echo $feed_url; ?>"><img src="<?php echo $img_dir . "addnewsgator.gif"; ?>" align="middle" alt="Newsgator" style="border: none;" /></a></li>
<?php } ?>
<?php if( '1' == $sem_subscribe_me_services['sem_subscribe_me_service_newsisfree']) { ?>
<li><a href="http://www.newsisfree.com/user/sub/?url=<?php echo $feed_url; ?>"><img src="<?php echo $img_dir . "sub_nif4.gif"; ?>" align="middle" alt="NewsIsFree" style="border: none;" /></a></li>
<?php } ?>
<?php if( '1' == $sem_subscribe_me_services['sem_subscribe_me_service_myfeedster']) { ?>
<li><a href="http://www.feedster.com/myfeedster.php?action=addrss&amp;rssurl=<?php echo $feed_url; ?>&amp;confirm=no"><img src="<?php echo $img_dir . "addmyfeedster.gif"; ?>" align="middle" alt="MyFeedster" style="border: none;" /></a></li>
<?php } ?>
</ul>
<?php

$sem_subscribe_me_done = true;
} // end sem_subscribe_me()

add_action('wp_meta', 'sem_subscribe_me', 1);

// Add to admin menu

add_action('admin_menu', 'sem_subscribe_me_menu');

?>
