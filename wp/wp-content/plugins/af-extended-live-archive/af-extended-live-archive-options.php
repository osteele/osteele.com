<?php
/*
	Options Page for the Extended Live Archive WordPress Plugin.
*/

/*	Extended Live Archives is extensively based on code from Jonas Rabbe and his 
	Super Archives Plugin. This is merely an extension to this already existing 
	wonderful plugin (see 
	http://www.jonas.rabbe.com/archives/2005/05/08/super-archives-plugin-for-wordpress/)
	for more info.
	
	Copyright 2005  Arnaud Froment 
	Copyright 2005  Jonas Rabbe  (email : jonas@rabbe.com)
	
	This program is free software; you can redistribute it and/or modify
	it under the terms of the GNU General Public License as published by
	the Free Software Foundation; either version 2 of the License, or
	(at your option) any later version.
	
	This program is distributed in the hope that it will be useful,
	but WITHOUT ANY WARRANTY; without even the implied warranty of
	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
	GNU General Public License for more details.
	
	You should have received a copy of the GNU General Public License
	along with this program; if not, write to the Free Software
	Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
*/
include_once(ABSPATH . WPINC . '/class-snoopy.php');
$af_ela_cache_root = ABSPATH . 'wp-content/af-extended-live-archive/';

function af_ela_info($show='') {
	switch($show) {
	case 'currentversion' :
		$plugins= get_plugins();
    	$info = $plugins['af-extended-live-archive/af-extended-live-archive.php']['Version'];
    	break;
    case 'localeversion' :
    	$info = '9905';
    	break;
    case 'born_on' :
    	$info = 'October 13, 2005';
    	break;
    case 'homeurl' :
    	$info = 'http://www.sonsofskadi.net/index.php/extended-live-archive/';
    	break;
	case 'homename' :
    	$info = 'ELA on Sons Of Skadi';
    	break;
	case 'supporturl' :
    	$info = 'http://www.flickr.com/groups/ela-support/discuss/';
    	break;
	case 'supportname' :
    	$info = 'ELA support group on Flickr';
    	break; 
    case 'remoteversion':
    	$info = 'http://www.sonsofskadi.net/elaversion.txt';
     	break;
     default:
     	$info = '';
     	break;   
     }
    return $info;
}

function af_ela_option_init() {
	global $af_ela_cache_root;
	$settings = get_option('af_ela_options');
	if (!($is_initialized=get_option('af_ela_is_initialized'))  
			|| !$settings 
			|| strstr($settings['installed_version'], $is_initialized) === false) {
		$cache = new af_ela_classCacheFile('');
		$cache->deleteFile();
		$settings = array(
	// This option is not accessible by the admin panel
		'id' => 'af-ela',
		'installed_version' => af_ela_info('currentversion'),
	// we always set the character set from the blog settings
		'charset' => get_bloginfo('charset'),
		'newest_first' => '1',
		'num_entries' => 0,
		'num_entries_tagged' => 0,
		'num_comments' => 0,
		'fade' => 0,		
		'hide_pingbacks_and_trackbacks' => 0,	
		'use_default_style' => 1,
				
		'selected_text' => '',
		'selected_class' => 'selected',
		'comment_text' => '(%)',
		'number_text' => '(%)',
		'number_text_tagged' => '(%)',
		'closed_comment_text' => '',
		'day_format' => '',
		'error_class' => 'alert',

	// allow truncating of titles
		'truncate_title_length' => 0,
		'truncate_cat_length' => 25,
		'truncate_title_text' => '&#8230;',
		'truncate_title_at_space' => 1,
		
	// default text for the tab buttons
		'menu_order' => 'chrono,cats',
		'menu_month' => 'By date',
		'menu_cat' => 'By category',
		'menu_tag' => 'By tags',
		'num_posts_by_cat' => 0,
		'cat_more_text' => 'View all posts in %s',
		'before_child' => '&nbsp;&nbsp;&nbsp;',
		'after_child' => '',
		'loading_content' => '...loading',
		'idle_content' => '',
		'excluded_categories' => '0');
		

		update_option('af_ela_options', $settings, 'Set of Options for Extended Live Archive');
		update_option('af_ela_option_mode', 0, 'ELA option mode');
		
		$res = true;
		if( !is_dir($af_ela_cache_root) ) {
			if( !($res = mkdir($af_ela_cache_root)) ) {
				?>
		<div class="updated"><p><strong>Unable to create cache directory. Check your server credentials on the wp-content directory.</strong></p></div>
	<?php		return;
			} else {
				if( $res === true ) {
					$res = af_ela_create_cache($settings);
					if( $res === true ) {?>
		<div class="updated"><p><strong>The cache files have been created for the first time. You should be up and running. Enjoy.</strong></p></div>
	<?php		 	} else {?>
		<div class="updated"><p><strong>Unable to create the cache files. Check your server credentials on the wp-content/af-extended-live-archive directory. </strong></p></div>
	<?php 			return;
					}
				}
			}
		} else {
			if( af_ela_create_cache($settings) ) {?>
		<div class="updated"><p><strong>The cache files have been updated. You should be up and running. Enjoy.</strong></p></div>
	<?php	} else {?>
		<div class="updated"><p><strong>Unable to update the cache files to the newer version of the plugin. Check your server credentials on the wp-content/af-extended-live-archive directory. </strong></p></div>
	<?php 	return;
			}
		}
		update_option('af_ela_is_initialized', af_ela_info('currentversion'), 'ELA plugin has already been initialized');
	}
}

function af_ela_option_update() {
	global $wpdb;
	$settings = get_option('af_ela_options');
	
	$settings['newest_first'] = isset($_POST['newest_first']) ? 1 : 0;
	$settings['num_entries']  = isset($_POST['num_entries']) ? 1 : 0;
	$settings['num_entries_tagged'] = isset($_POST['num_entries_tagged']) ? 1 : 0;
	$settings['num_comments'] = isset($_POST['num_comments']) ? 1 : 0;
	$settings['fade']         = isset($_POST['fade']) ? 1 : 0;
	$settings['hide_pingbacks_and_trackbacks'] = isset($_POST['hide_pingbacks_and_trackbacks']) ? 1 : 0 ;
	$settings['use_default_style'] = isset($_POST['use_default_style']) ? 1 : 0 ;

	if( isset($_POST['selected_text']) )  $settings['selected_text']  = urldecode($_POST['selected_text']);
	if( isset($_POST['selected_class']) ) $settings['selected_class'] = $_POST['selected_class'];
	if( isset($_POST['comment_text']) )   $settings['comment_text']   = urldecode($_POST['comment_text']);
	if( isset($_POST['number_text']) )    $settings['number_text']    = urldecode($_POST['number_text']);
	if( isset($_POST['number_text_tagged']) )  $settings['number_text_tagged']  = urldecode($_POST['number_text_tagged']);
	if( isset($_POST['closed_comment_text']) ) $settings['closed_comment_text'] = urldecode($_POST['closed_comment_text']);
	if( isset($_POST['day_format']) )     $settings['day_format']     = $_POST['day_format'];
	if( isset($_POST['error_class']) )    $settings['error_class']    = $_POST['error_class'];

	// allow truncating of titles
	if( isset($_POST['truncate_title_length']) ) $settings['truncate_title_length'] = $_POST['truncate_title_length'];
	if( isset($_POST['truncate_cat_length']) )   $settings['truncate_cat_length']   = $_POST['truncate_cat_length'];
	if( isset($_POST['truncate_title_text']) )   $settings['truncate_title_text']   = urldecode($_POST['truncate_title_text']);
	$settings['truncate_title_at_space'] = isset($_POST['truncate_title_at_space']) ? 1 : 0;
		
	// default text for the tab buttons
	if( isset($_POST['menu_order']) ) {
		$comma ='';
		$settings['menu_order']='';
		foreach($_POST['menu_order'] as $menu_item) {
			$settings['menu_order'].= $comma . $menu_item;
			$comma = ',';
		}
	}
	if( isset($_POST['menu_month']) )       $settings['menu_month']       = urldecode($_POST['menu_month']);
	if( isset($_POST['menu_cat']) )         $settings['menu_cat']         = urldecode($_POST['menu_cat']);	
	if( isset($_POST['menu_tag']) )         $settings['menu_tag']         = urldecode($_POST['menu_tag']);	
	if( isset($_POST['num_posts_by_cat']) ) $settings['num_posts_by_cat'] = $_POST['num_posts_by_cat'];
	if( isset($_POST['cat_more_text']) )    $settings['cat_more_text']    = urldecode($_POST['cat_more_text']);
	if( isset($_POST['before_child']) )     $settings['before_child']     = urldecode($_POST['before_child']);
	if( isset($_POST['after_child']) )      $settings['after_child']      = $_POST['after_child'];
	if( isset($_POST['loading_content']) )  $settings['loading_content']  = urldecode($_POST['loading_content']);	
	if( isset($_POST['idle_content']) )     $settings['idle_content']     = urldecode($_POST['idle_content']);
		
	$current_mode = get_option('af_ela_option_mode');
	$asides_cats = $wpdb->get_results("SELECT * from $wpdb->categories");
	$comma ='';
	if (!isset($_POST['excluded_categories'])) {?>
	<div class="updated"><p><strong>What's the point of not showing up any categories at all ?</strong></p></div> <?php
	} else {
		if ($current_mode == 0) {
			$settings['excluded_categories'] = $_POST['excluded_categories'][0];
		} else {
			$settings['excluded_categories'] = '';
			foreach ($asides_cats as $cat) {
				if(!in_array($cat->cat_ID, $_POST['excluded_categories'])) {
					$settings['excluded_categories'] .= $comma ;
					$settings['excluded_categories'] .= $cat->cat_ID;
					$comma = ',';
				}
			}
		}
	}
	update_option('af_ela_options', $settings,'',1);
	$cache = new af_ela_classCacheFile('');
	$cache->deleteFile();
}

function af_ela_remote_version_check() {
	if (class_exists(snoopy)) {
		$client = new Snoopy();
		$client->_fp_timeout = 4;
		if (@$client->fetch(af_ela_info('remoteversion')) === false) {
			return -1;
		}
	   	$remote = $client->results;
   		if (!$remote || strlen($remote) > 8 ) {
			return -1;
		} 
		if (intval($remote) > intval(af_ela_info('localeversion'))) {
			return 1;
		} else {
			return 0;
		}
	}
}

function af_ela_admin_page() {
	af_ela_option_init();
	if (($remote = af_ela_remote_version_check()) == 1) {
		echo '<div id="message" class="updated fade"><p><a href="'. af_ela_info('homeurl').'" title="'.af_ela_info('homename').'">There is a ELA update available</a></p></div>';
	}
	
	if (isset($_POST['submit_option'])) { 
		if (isset($_POST['clear_cache'])) {
			$cache = new af_ela_classCacheFile('');
			$reset_return= $cache->deleteFile();
			if ($reset_return) {
				?>
	<div class="updated"><p><strong>Cache emptied</strong></p></div> <?php
			} else {
				?>	<div class="updated"><p><strong>Cache was already empty</strong></p></div> <?php
			}
		} elseif (isset($_POST['switch_option_mode'])) {
		 	$current_mode = get_option('af_ela_option_mode');
			if ($current_mode == 0) {
				$next_mode = 1;
				$option_mode_text = 'Switch to Advanced Options Mode';
			} else {
				$next_mode = 0;
				$option_mode_text = 'Switch to Basic Options Mode';
			}			
			update_option('af_ela_option_mode', $next_mode,'',1);
		} else {		
			af_ela_option_update();
			?>	<div class="updated"><p>Extended Live Archive Options have been updated</p></div> <?php 
		}
	}
	$current_mode = get_option('af_ela_option_mode');
	if ($current_mode == 0) {
		$option_mode_text = 'Show Advanced Options Panel';
		$advancedState = 'none';
		$basicState = 'table-row';
	} else {
		$option_mode_text = 'Hide Advanced Options Panel';
		$advancedState = 'block';
		$basicState = 'none';
	}
	$settings = get_option('af_ela_options');

	af_ela_echo_scripts();

?>	<div class="wrap">
		<h2>ELA Options</h2><?php
	af_ela_echo_fieldset_info($option_mode_text);
?>		<form method="post">
		<input type="hidden" name="submit_option" value="1" /><?php
	af_ela_echo_fieldset_whattoshow($settings,$basicState, $current_mode);
?>		<hr style="clear: both; border: none;" /><?php
	af_ela_echo_fieldset_howtoshow($settings,$advancedState);
	af_ela_echo_fieldset_howtocut($settings,$advancedState);
?>		<hr style="clear: both; border: none;" /><?php
	af_ela_echo_fieldset_whataboutthemenus($settings,$advancedState);
	af_ela_echo_fieldset_whatcategoriestoshow($settings,$advancedState);
?>		<hr style="clear: both; border: none;" />
		<div class="submit">
			<input type="submit" name="update_generic" value="<?php _e('Update Options Now') ?>" /></form>
		</div>
	</div>
 
	<div class="wrap">
		<h2>ELA Cache Management</h2>
		<form method="post">
		<input type="hidden" name="submit_option" value="1" />
		<p>You need to clear the cache so that it gets re-built whenever you are making changes related to a category without editing or creating a post (like renaming, creating, deleting a category for instance</p>
		<div class="submit">
			<input type="submit" name="clear_cache" value="<?php _e('Empty Cache Now') ?>" /></form>
		</div>
	</div>
<?php
}

function af_ela_echo_scripts() {
?>	<script language="javascript" type="text/javascript">
<!--
	function disableTabs(first, disabler) {
		var maxtab = 3;
		var i;
		if (document.getElementById('menu_order_tab' + disabler).value == 'none') {
			for(i = first; i < maxtab; i++) {
				document.getElementById('menu_order_tab' + i).value = 'none';
				document.getElementById('menu_order_tab' + i).disabled = true;
			}
		} else {
			document.getElementById('menu_order_tab' + first).disabled = false;
		}
	}
	
	function selectAllCategories(list) {
		var i;
		var temp = new Array();
		temp = list.split(',');
		for(i = 0; i < temp.length-1; i++) {
			document.getElementById("category-"+temp[i]).checked=true;
		}
	}
	function unselectAllCategories(list) {
		var i;
		var temp = new Array();
		temp = list.split(',');
		for(i = 0; i < temp.length-1; i++) {
			document.getElementById("category-"+temp[i]).checked=false;
		}
	}
//-->
	</script><?php
}

function af_ela_echo_fieldset_info($option_mode_text) {
?>		<fieldset class="options" style="float: left; width: 25%;"><legend>Extended Live Archive info </legend>
		<table width="100%" cellspacing="2" cellpadding="5" class="editform">
			<tr>
				<th width="33%" valign="top" scope="row"><label>Version:</label></th>
				<td><?php echo af_ela_info('currentversion'); ?></td>
			</tr>
			<tr>
				<th width="33%" valign="top" scope="row"><label>Latest news:</label></th>
				<td><a href="<?php echo af_ela_info('homeurl'); ?>"><?php echo af_ela_info('homename'); ?></a></td>
			</tr>
			<tr>
				<th width="33%" valign="top" scope="row"><label>Get help:</label></th>
				<td><a href="<?php echo af_ela_info('supporturl'); ?>"><?php echo af_ela_info('supportname'); ?></a></td>
			</tr>
			<tr>
				<th width="33%" valign="top" scope="row"><label>Works with:</label></th>
				<td><a href="http://binarybonsai.com/k2/">Michael's K2</a>
				<br/><a href="http://photomatt.net/2004/05/19/asides/">Matt's Asides</a>
				<br/><a href="http://www.neato.co.nz/ultimate-tag-warrior"> Christine's Utimate Tag Warrior</a></td>
			</tr>
		</table> 
		<div class="submit" style="text-align:center; ">
		<form method="post"><br />
		<input type="hidden" name="submit_option" value="1" /><input type="submit" name="switch_option_mode" value="<?php _e($option_mode_text) ?>" /></form></div>
		</fieldset><?php
}

function af_ela_echo_fieldset_whattoshow($settings,$basicState, $current_mode) {
	global $utw_is_present;
?>		<fieldset class="options"><legend>What to show ? </legend>
		<table width="100%" cellspacing="2" cellpadding="5" class="editform">
			<tr>
				<th width="30%" valign="top" scope="row"><label for="newest_first">Show Newest First:</label></th>
				<td width="5%"><input name="newest_first" id="newest_first" type="checkbox" value="<?php echo $settings['newest_first']; ?>" <?php checked('1', $settings['newest_first']); ?> /></td><td><small>Enabling this will show the newest post first in the listings.</small></td>
			</tr>
			<tr>
				<th width="30%" valign="top" scope="row"><label for="num_entries" >Show Number of Entries:</label></th>
				<td width="5%"><input name="num_entries" id="num_entries" type="checkbox" value="<?php echo $settings['num_entries']; ?>" <?php checked('1', $settings['num_entries']); ?> /></td><td><small>Sets whether the number of entries for each year, month, category should be shown.</small></td>
			</tr><?php if($utw_is_present) { ?>
			<tr>
				<th width="30%" valign="top" scope="row"><label for="num_entries_tagged">Show Number of Entries Per Tag:</label></th>
				<td width="5%"><input name="num_entries_tagged" id="num_entries_tagged" type="checkbox" value="<?php echo $settings['num_entries_tagged']; ?>" <?php checked('1', $settings['num_entries_tagged']); ?> /></td><td><small>Sets whether the number of entries for each tags should be shown</small></td>
			</tr><?php } ?>
			<tr>
				<th width="30%" valign="top" scope="row"><label for="num_comments">Show Number of Comments:</label></th>
				<td width="5%"><input name="num_comments" id="num_comments" type="checkbox" value="<?php echo $settings['num_comments']; ?>" <?php checked('1', $settings['num_comments']); ?> /></td><td><small>Sets whether the number of comments for each entry should be shown</small></td>
			</tr>
			<tr>
				<th width="30%" valign="top" scope="row"><label for="fade">Fade Anything Technique:</label></th>
				<td width="5%"><input name="fade" id="fade" type="checkbox" value="<?php echo $settings['fade']; ?>" <?php checked('1', $settings['fade']); ?> /></td><td><small>Sets whether changes should fade using the Fade Anything Technique</small></td>
			</tr>
			<tr>
				<th width="30%" valign="top" scope="row"><label for="hide_pingbacks_and_trackbacks">Hide Ping- and Trackbacks:</label></th>
				<td width="5%"><input name="hide_pingbacks_and_trackbacks" id="hide_pingbacks_and_trackbacks" type="checkbox" value="<?php echo $settings['hide_pingbacks_and_trackbacks']; ?>" <?php checked('1', $settings['hide_pingbacks_and_trackbacks']); ?> /></td><td><small>Sets whether ping- and trackbacks should influence the number of comments on an entry</small></td>
			</tr>
			<tr>
				<th width="30%" valign="top" scope="row"><label for="use_default_style">Use the default CSS stylesheet:</label></th>
				<td width="5%"><input name="use_default_style" id="use_default_style" type="checkbox" value="<?php echo $settings['use_default_style']; ?>" <?php checked('1', $settings['use_default_style']); ?> /></td><td><small>Sets whether the default stylsheet will be linked and use by the plugin or not.</small></td>
			</tr>
			<tr valign="top" style="display: <?php echo $basicState; ?>">
				<th scope="row"><label for="cat_asides">Asides Category:</label></th>
				<td colspan="2"><?php
				global $wpdb;
				$asides_table = array();
				$asides_table = explode(',', $settings['excluded_categories']);
				if ($asides_table[0] != 0) {
					$id = $asides_table[0];
					$asides_title = $wpdb->get_var("SELECT cat_name from $wpdb->categories WHERE cat_ID = ${asides_table[0]}");
				} else {
					$asides_title='No Asides';
				}
				$asides_cats = $wpdb->get_results("SELECT * from $wpdb->categories");
				 if ($current_mode == 0) {
?>				<select name="excluded_categories[]" id="cat_asides" style="width: 10em;" >
				<option value="<?php echo $asides_table[0]; ?>"><?php echo $asides_title; ?></option>
				<option value="-----">----</option>
				<option value="0">No Asides</option>
				<option value="-----">----</option><?php
				foreach ($asides_cats as $cat) {
					echo '<option value="' . $cat->cat_ID . '">' . $cat->cat_name . '</option>';
            	}?>
				</select><small>&nbsp;&nbsp;&nbsp;The category you are using for your asides.</small></td><?php } ?>
			</tr>		
		</table>
		</fieldset><?php
}

function af_ela_echo_fieldset_howtoshow($settings,$advancedState) {
	global $utw_is_present;
?>		<fieldset class="options" style="display: <?php echo $advancedState; ?>; float: left; width: 52%;" ><legend>How to show it ? </legend>
		<table width="100%" cellspacing="2" cellpadding="5" class="editform" >
			<tr valign="top">
				<th width="180px" scope="row"><label for="selected_text">Selected Text:</label></th>
				<td><input name="selected_text" id="selected_text" type="text" value="<?php echo $settings['selected_text']; ?>" size="30" /><br/>
				<small>The text that is shown after the currently selected year, month or category.</small></td>
			</tr>
			<tr valign="top">
				<th scope="row"><label for="selected_class">Selected Class:</label></th>
				<td ><input name="selected_class" id="selected_class" type="text" value="<?php echo $settings['selected_class']; ?>" size="30" /><br/>
				<small>The CSS class for the currently selected year, month or category.</small></td>
			</tr>
			<tr valign="top">
				<th scope="row"><label for="number_text"># of Entries Text:</label></th>
				<td><input name="number_text" id="number_text" type="text" value="<?php echo htmlspecialchars(stripslashes($settings['number_text'])); ?>" size="30" /><br/>
				<small>The string to show for number of entries per year, month or category. Can contain HTML. % is replaced with number of entries.</small></td>
			</tr><?php if($utw_is_present) { ?>
			<tr valign="top">
				<th scope="row"><label for="number_text_tagged"># of Tagged-Entries Text:</label></th>
				<td><input name="number_text_tagged" id="number_text_tagged" type="text" value="<?php echo htmlspecialchars(stripslashes($settings['number_text_tagged'])); ?>" size="30" /><br/>
				<small>The string to show for number of entries per tag. Can contain HTML. % is replaced with number of entries.</small></td>
			</tr><?php } ?>
			<tr valign="top">
				<th scope="row"><label for="comment_text"># of Comments Text:</label></th>
				<td><input name="comment_text" id="comment_text" type="text" value="<?php echo htmlspecialchars(stripslashes($settings['comment_text'])); ?>" size="30" /><br/>
				<small>The string to show for comments. Can contain HTML. % is replaced with number of comments.</small></td>
			</tr>
			<tr valign="top">
				<th scope="row"><label for="closed_comment_text ">Closed Comment Text:</label></th>
				<td><input name="closed_comment_text" id="closed_comment_text" type="text" value="<?php echo htmlspecialchars(stripslashes($settings['closed_comment_text'])); ?>" size="30" /><br/>
				<small>The string to show if comments are closed on an entry. Can contain HTML.</small></td>
			</tr>
			<tr valign="top">
				<th scope="row"><label for="day_format">Day of Posting Format:</label></th>
				<td><input name="day_format" type="text" id="day_format" value="<?php echo $settings['day_format']; ?>" size="30" /><br/>
				<small>A date format string to show the day (or date) for each entry (ie. 'jS' to show 1st, 3rd, and 14th). Format string is in the <a href="http://www.php.net/date">php date format</a>. Leave empty to show no date.</small></td>
			</tr>
			<tr valign="top">
				<th scope="row"><label for="error_class">Error Class:</label></th>
				<td><input name="error_class" type="text" id="error_class" value="<?php echo $settings['error_class']; ?>" size="30" /><br/>
				<small>The CSS class to put on paragraphs containing errors.</small></td>
			</tr>
		</table>
		</fieldset><?php
}

function af_ela_echo_fieldset_howtocut($settings,$advancedState) {
?>		<fieldset class="options" style="display: <?php echo $advancedState; ?>;float: right; width: 40%;" ><legend>What to cut out ? </legend>
		<table width="100%" cellspacing="2" cellpadding="5" class="editform">
			<tr valign="top">
				<th width="180px" scope="row"><label for="truncate_title_length" >Max Entry Title Length:</label></th>
				<td><input name="truncate_title_length" id="truncate_title_length" type="text" value="<?php echo $settings['truncate_title_length']; ?>" size="8" /><br/>
				<small>Length at which to truncate title of entries. Set to <strong>0</strong> to leave the titles not truncated.</small></td>
			</tr>
			<tr valign="top">
				<th scope="row"><label for="truncate_cat_length" title="Sets whether the number of entries for each year, month, category should be shown.">Max Cat. Title Length:</label></th>
				<td><input name="truncate_cat_length " id="truncate_cat_length" type="text" value="<?php echo $settings['truncate_cat_length']; ?>" size="8"  /><br/>
				<small>Length at which to truncate name of categories. Set to <strong>0</strong> to leave the category names not truncated</small></td>
			</tr> 
			<tr valign="top"> 
				<th scope="row"><label for="truncate_title_text">Truncated Text:</label></th>
				<td><input name="truncate_title_text" id="truncate_title_text" type="text" value="<?php echo $settings['truncate_title_text']; ?>" size="8" /><br/>
				<small>The text that will be written after the entries titles and the categories names that have been truncated. &#8230; (<strong>&amp;#8230;</strong>) is a common example.</small></td>
			</tr>
			<tr valign="top">
				<th scope="row"><label for="truncate_title_at_space" title="Sets whether the number of comments for each entry should be shown">Truncate at space:</label></th>
				<td><input name="truncate_title_at_space" id="truncate_title_at_space" type="checkbox" value="<?php echo $settings['truncate_title_at_space']; ?>" <?php checked('1', $settings['truncate_title_at_space']); ?> /><br/>
				<small>Sets whether at title should be truncated at the last space before the length to be truncated to, or if words should be truncated mid-senten...</small></td>
			</tr>
			<tr valign="top">
				<th scope="row"><label for="num_posts_by_cat">Max # of Posts per Cat.:</label></th>
				<td><input name="num_posts_by_cat" id="num_posts_by_cat" type="text" value="<?php echo $settings['num_posts_by_cat']; ?>" size="8" /><br/>
				<small>The max number of posts that will be listed for each category. Set to <strong>0</strong> to show all the posts.</small></td>
			</tr>
		</table>
		</fieldset><?php
}

function af_ela_echo_fieldset_whataboutthemenus($settings,$advancedState) {
	if (!empty($settings['menu_order'])) {
		$menu_table = preg_split('/[\s,]+/',$settings['menu_order']);
	}
	global $utw_is_present;
?>		<fieldset class="options" style="display: <?php echo $advancedState; ?>; float: left; width: 52%" ><legend>What about the menus ? </legend>
		<table width="100%" cellspacing="2" cellpadding="5" class="editform">
			<tr valign="top">
				<th width="180px" scope="row"><label for="menu_order[]">Tab Order:</label></th>
				<td>
				<select name="menu_order[]" id="menu_order_tab0" onchange="Javascript:disableTabs(1,0);" style="width: 10em;" >
				<option value="chrono" <?php echo ($menu_table[0] == 'chrono') ? 'selected' : '' ?>>By date</option>
				<option value="cats" <?php echo ($menu_table[0] == 'cats') ? 'selected' : '' ?>>By category</option><?php if($utw_is_present) { ?>
				<option value="tags" <?php echo ($menu_table[0] == 'tags') ? 'selected' : '' ?>>By tag</option><?php } ?>
				<option value="none" <?php echo ($menu_table[0] == 'none') ? 'selected' : '' ?>>None</option></select>
				<select name="menu_order[]" id="menu_order_tab1" onchange="Javascript:disableTabs(2,1);" style="width: 10em;" >
				<option id="chrono1" value="chrono" <?php echo ($menu_table[1] == 'chrono') ? 'selected' : '' ?>>By date</option>
				<option id="cats1" value="cats" <?php echo ($menu_table[1] == 'cats') ? 'selected' : '' ?>>By category</option><?php if($utw_is_present) { ?>
				<option id="tags1" value="tags" <?php echo ($menu_table[1] == 'tags') ? 'selected' : '' ?>>By tag</option><?php } ?>
				<option id="none1" value="none" <?php echo ($menu_table[1] == 'none') ? 'selected' : '' ?>>None</option></select><?php if($utw_is_present) { ?>
				<select name="menu_order[]" id="menu_order_tab2" style="width: 10em;" >
				<option id="chrono2" value="chrono" <?php echo ($menu_table[2] == 'chrono') ? 'selected' : '' ?>>By date</option>
				<option id="cats2" value="cats" <?php echo ($menu_table[2] == 'cats') ? 'selected' : '' ?>>By category</option>
				<option id="tags2" value="tags" <?php echo ($menu_table[2] == 'tags') ? 'selected' : '' ?>>By tag</option>
				<option id="none2" value="none" <?php echo ($menu_table[2] == 'none') ? 'selected' : '' ?>>None</option></select><?php } ?>
				<br/><small>The order of the tab to display.</small></td>
			</tr>
			<tr valign="top">
				<th width="180px" scope="row"><label for="menu_month">Chronological Tab Text:</label></th>
				<td><input name="menu_month" id="menu_month" type="text" value="<?php echo htmlspecialchars(stripslashes($settings['menu_month'])); ?>" size="30" /><br/>
				<small>The text written in the chronological tab.</small></td>
			</tr>
			<tr valign="top">
				<th scope="row"><label for="menu_cat">By Category Tab Text:</label></th>
				<td><input name="menu_cat" id="menu_cat" type="text" value="<?php echo htmlspecialchars(stripslashes($settings['menu_cat'])); ?>" size="30" /><br/>
				<small>The text written in the categories tab.</small></td>
			</tr><?php if($utw_is_present) { ?>
			<tr valign="top">
				<th scope="row"><label for="menu_tag">By Tag Tab Text:</label></th>
				<td><input name="menu_tag" id="menu_tag" type="text" value="<?php echo htmlspecialchars(stripslashes($settings['menu_tag'])); ?>" size="30" /><br/>
				<small>The text written in the tags tab.</small></td>
			</tr><?php } ?>
			<tr valign="top">
				<th scope="row"><label for="cat_more_text">More Category Text:</label></th>
				<td><input name="cat_more_text" id="cat_more_text" type="text" value="<?php echo htmlspecialchars(stripslashes($settings['cat_more_text'])); ?>" size="30" /><br/>
				<small>The text written in the last line of the posts list when a is defined and reached. %s is replaced by the category name.</small></td>
			</tr>
			<tr valign="top">
				<th scope="row"><label for="before_child">Before Child Text:</label></th>
				<td><input name="before_child" id="before_child" type="text" value="<?php echo htmlspecialchars($settings['before_child']); ?>" size="30" /><br/>
				<small>The text written before each category which is a child of another. This is recursive.</small></td>
			</tr>
			<tr valign="top">
				<th scope="row"><label for="after_child">After Child Text:</label></th>
				<td><input name="after_child" id="after_child" type="text" value="<?php echo $settings['after_child']; ?>" size="30" /><br/>
				<small>The text that after each category which is a child of another. This is recursive.</small></td>
			</tr>
			<tr valign="top">
				<th scope="row"><label for="loading_content">Loading Content:</label></th>
				<td><input name="loading_content" id="loading_content" type="text" value="<?php echo htmlspecialchars(stripslashes($settings['loading_content'])); ?>" size="30" /><br/>
				<small>The text displayed when the data are being fetched from the server (basically when stuff is loading). Can contain HTML.</small></td>
			</tr>			
			<tr valign="top">
				<th scope="row"><label for="idle_content">Idle Content:</label></th>
				<td><input name="idle_content" id="idle_content" type="text" value="<?php echo htmlspecialchars(stripslashes($settings['idle_content'])); ?>" size="30" /><br/>
				<small>The text displayed when no data are being fetched from the server (basically when stuff is not loading). Can contain HTML.</small></td>
			</tr>
		</table>
		</fieldset><?php
}

function af_ela_echo_fieldset_whatcategoriestoshow($settings,$advancedState) {
?>		<fieldset class="options" style="display: <?php echo $advancedState; ?>; float: right; width: 40%" ><legend>What categories to show ?</legend><label for="cat_asides">The category you want to show in the categories tab.</label>
		<?php
			global $wpdb;
			$asides_table = array();
			$asides_table = explode(',', $settings['excluded_categories']);
			$asides_cats = $wpdb->get_results("SELECT * from $wpdb->categories");
			$asides_content = '<table width="100%" cellspacing="2" cellpadding="5" class="editform">';
			$asides_select = '';
			foreach ($asides_cats as $cat) {
				$checked = in_array($cat->cat_ID, $asides_table) ? '' : 'checked';
				$asides_select .= $cat->cat_ID.',';
				$asides_content .= '
			<tr valign="top">
				<th scope="row"><label for="category-'.$cat->cat_ID.'">'.$cat->cat_name.'</label></th>
				<td width="5%"><input value="'.$cat->cat_ID.'" type="checkbox" name="excluded_categories[]" id="category-'.$cat->cat_ID.'" '. $checked  . '/></td>
			</tr>';
		   	}
			echo $asides_content;
?>		</table>
		<input type="button" onclick="javascript:selectAllCategories('<?php echo $asides_select;?>')" value="<?php _e('Select All Categories') ?>" />
		<input type="button" onclick="javascript:unselectAllCategories('<?php echo $asides_select;?>')" value="<?php _e('Unselect All Categories') ?>" />
		</fieldset>
		<script language="javascript" type="text/javascript"> 
		disableTabs(1, 0);
		</script><?php
}


af_ela_admin_page();
?>