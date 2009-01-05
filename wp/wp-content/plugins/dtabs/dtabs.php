<?php
/*
Plugin Name: dTabs
Plugin URI: http://dynamictangentconceptions.dtcnet.co.uk/downloads/wp-plugins/dtabs-dynamic-tabs-wordpress-plugin/
Description: Allows themes to include an optional user controled dynamically tabbed navigation system.
Version: 1.3
Author: David Burton
Author URI: http://dynamictangentconceptions.dtcnet.co.uk/category/downloads/wp-plugins/
*/

/*  Copyright 2005  dtcnet  (email : info@dynamictangentconceptions.dtcnet.co.uk)

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
if ( !isset($dtabs_current_version) ) :

global $dtabs_current_version, $dtabs_default;
$dtabs_current_version = '1.3';

// Welcome message
function dtabs_welcome_message() {
	global $dtabs_info, $dtabs_current_version;
	$file_name = plugin_basename(__FILE__);
	?>
	<style type="text/css">
	#dtabs_welcome_container {
		display : none;
	}
	#fade_bg {
		width: 100%;
		height: 100%;
		background-color: white;
		z-index: 100;
		position: fixed;
		top: 0;
		left: 0;
		filter:alpha(opacity='20');
		opacity: .5;
	}
	#window_area {
		width: 100%;
		height: 100%;
		z-index: 101;
		position: fixed;
		top: 0;
		left: 0;
	}
	#welcome_window {
		width: 600px;
		background-color: white;
		border: grey solid 1px;
		margin: 50px auto 0 auto;
		padding: 20px 40px 40px 40px;
	}
	.floatright {
		float:right;
	}
	.floatleft {
		float:left;
	}
	.changes {
		font-size: 10px;
		height: 100px;
		overflow: auto;
		border: rgb(204,204,204) solid 1px;
		padding: 0 20px 20px 20px;
		margin: 20px 0 30px 0;
	}
	.importantchanges {
		color: red;
	}
	</style>
	<script type="text/javascript">
	function show(sid,time) {
		var e=document.getElementById(sid);
		time = time || 2000;
		p=1000/20;
		t=0;
		s= 100/(time/p);
		o=0;
		changeOpac(o,sid);
		e.style.display = "block";
		while (o<=100) {
			setTimeout("changeOpac("+Math.round(o)+",'"+sid+"')",t);
			o=o+s;
			t = t+p;
		}
	}
	function hide(hid) {
		var e=document.getElementById(hid);
		time = 2000;
		p=1000/20;
		t=0;
		s= 100/(time/p);
		o=100;
		changeOpac(o,hid);
		while (o>=0) {
			setTimeout("changeOpac("+Math.round(o)+",'"+hid+"')",t);
			o=o-s;
			t = t+p;
		}
		setTimeout('document.getElementById("'+hid+'").style.display = "none";changeOpac(100,"'+hid+'"); hidingmenu_'+hid+'=false;',t+p);
	}
	function changeOpac(opacity, id) { 
	    var object = document.getElementById(id).style; 
	    object.opacity = (opacity / 100); 
	    object.MozOpacity = (opacity / 100); 
	    object.KhtmlOpacity = (opacity / 100); 
	    object.filter = "alpha(opacity=" + opacity + ")"; 
	}
			
	</script>
	<div id="dtabs_welcome_container">
		<div id="fade_bg">	</div>
		<div id="window_area">
			<div id="welcome_window">
			<?php
			if ($dtabs_info['fresh_installation']) {
				?>
				<h2><?php echo sprintf(__('dTabs plugin version %s successfully installed!','dtabs'),$dtabs_current_version); ?></h2>
				<?php
			} elseif ($dtabs_info['fresh_activation']) {
				?>
				<h2><?php echo sprintf(__('dTabs plugin version %s successfully activated!','dtabs'),$dtabs_current_version); ?></h2>
				<?php
			} else {
				?>
				<h2><?php echo sprintf(__('dTabs plugin successfully updated to version %s!','dtabs'),$dtabs_current_version); ?></h2>
				<?php
			}
			?>
				<p><?php echo sprintf(__('Please note that if your theme is not pre-enabled for dTabs you need to <a href="%s">paste a snippet of code</a> into your theme before your tabs will appear on your blog, alternatively/in addition you could ask your theme\'s author to pre-enable your theme.','dtabs'),'http://dynamictangentconceptions.dtcnet.co.uk/downloads/wp-plugins/dtabs-dynamic-tabs-wordpress-plugin/#enabletheme'); ?></p>
				<div class="changes">
				<h3><?php _e('Changes in version 1.3 requiring action','dtabs'); ?></h3>
				<ul>
					<li><?php _e('Increased accessability by hiding tabs off page (left:-999em) rather than using display:none.','dtabs'); ?>
						<ul>
							<li class="importantchanges"><?php _e('Changes to your css are required in order to benfit from this: .dmenu {display: none;} should be replaced with .dmenu {left: -999em;}.','dtabs'); ?></li>
						</ul>
					</li>
				</ul>
				
				<h3><?php _e('Changes in version 1.3','dtabs'); ?></h3>
				<ul>
					<li><?php _e('Fixed a problem with default css in Firefox 2 where tabs always appear on seperate lines (reported by G Dan Mitchell and fixed by Ian Brown).','dtabs'); ?></li>
					<li><?php _e('Added "between" argument for dtab_list_tabs for adding content between tabs.','dtabs'); ?></li>
					<li><?php _e('Added "fadetime" argument for dtab_list_tabs to enable control over the length of time it takes to fade menus and add the option of disabling fading all together.','dtabs'); ?></li>
					<li><?php _e('Fixed flickering of dropdown menus in certain circumstances.','dtabs'); ?></li>
					<li><?php _e('Increased accessability by hiding tabs off page (left:-999em) rather than using display:none.','dtabs'); ?></li>
					<li><?php _e('Added provisional (not suported by IE and not fully suppported by Firefox 2) support for javascript free css menus .','dtabs'); ?></li>
					<li><?php _e('Added "fadetype" argument for dtab_list_tabs for switching to javascript free menus.','dtabs'); ?></li>
					<li><?php _e('Updated default css to support  javascript free css menus.','dtabs'); ?></li>
					<li><?php _e('Added auto css for javascript free css layered menus.','dtabs'); ?></li>
				</ul>
				<br />
				</div>
		<?php
		if ($dtabs_info['fresh_installation']) {
			?>
				<p><?php _e('A single tab has been created pointing to the front page of your blog.  To make changes or set up more tabs go to the Tabs admin Panel under Manage.','dtabs'); ?></p>
				<p><b><?php _e('Would you like to set up some more tabs now?','dtabs'); ?></b></p>
				<div class="tablenav">
					<a href="edit.php?page=dtabs.php" class="button-highlighted floatleft"><?php _e('Set up more tabs','dtabs'); ?></a>
					<a href="javascript:javascript:hide('dtabs_welcome_container');" class="button floatright"><?php _e('I\'ll set some up later','dtabs'); ?></a>
				</div>
			<?php
		} else {
			?>
				<p><?php _e('Your existing settings have been used.  To make changes or set up more tabs go to the Tabs admin Panel under Manage.','dtabs'); ?></p>
				<div class="tablenav">
					<a href="edit.php?page=dtabs.php" class="button-highlighted floatleft"><?php _e('Manage tabs','dtabs'); ?></a>
					<a href="javascript:javascript:hide('dtabs_welcome_container');" class="button floatright"><?php _e('Close','dtabs'); ?></a>
				</div>
			<?php
		}
		?>
				<br class="clear" />
			</div>
		</div>
	</div>
	<script type="text/javascript">
		show('dtabs_welcome_container');
	</script>
	
	<?php
	update_option('dtabs_info',array('current_version'=>$dtabs_current_version, 'fresh_activation'=>false, 'fresh_installation'=>false));
}
register_activation_hook(__file__,'dtabs_activate');
$dtabs_default = array(1 => array('name' => __('front_page','dtabs'), 'label' => __('Home','dtabs'), 'url' => '/', 'title' => __('Back to the front page','dtabs'), 'type' => 'front'));
function dtabs_activate() {
	global $dtabs_current_version, $dtabs_default;
	add_option('dtabs_options', array('css' => ''));
	add_option('dtabs', $dtabs_default);
	add_option('dtabs_info',array('current_version'=>$dtabs_current_version, 'fresh_activation'=>true, 'fresh_installation'=>true));
	$dtabs_info = get_option('dtabs_info');
	if ($dtabs_info['fresh_activation']!=true) {
		$dtabs_info['fresh_activation']=true;
		update_option('dtabs_info',$dtabs_info);
	}
}
function dtabs_uninstall() {
	delete_option('dtabs_options');
	delete_option('dtabs');
	delete_option('dtabs_info');
	$load_admin_panel = false;
	$plugin_file = plugin_basename(__FILE__);
	wp_redirect(str_replace('&#038;','&',wp_nonce_url('plugins.php?action=deactivate&amp;plugin='.$plugin_file, 'deactivate-plugin_' . $plugin_file)));
}
if (is_admin()) {
	$dtabs_info = get_option('dtabs_info');
	if ((isset($_GET['activate']) AND $_GET['activate']=='true' AND $dtabs_info['fresh_activation']==true) OR $dtabs_info['current_version']!=$dtabs_current_version) {
		add_action('admin_footer', 'dtabs_welcome_message');
	} elseif (isset($_GET['action']) AND $_GET['action'] == 'uninstall_dtabs') {
		add_action('init','dtabs_uninstall');
	}
}
// dtc_add_pages() is the sink function for the 'admin_menu' hook
function dtab_add_pages() {
    if (function_exists('add_management_page')) {
		add_management_page('Manage Tabs', __('Tabs','dtabs'), 8, basename(__FILE__), 'dtab_manage_page');
	}
	
}

function dtab_checked($option) {
	if ($option == true) {
		return ' checked';
	}
}


// dtab_manage_page outputs the tabs preference pain
function dtab_manage_page() {
	global $wpdb, $dtabs_default;
	$load_admin_panel=true;
	$dtab = get_option('dtabs');
	$dtabs_options = get_option('dtabs_options');
	$total_tabs = count($dtab);
	$title = __('Manage Tabs','dtabs');
	$complete_message =  '<div id="message" class="updated fade"><p>';
	$success_message = '<strong>'.__('Save successful.','dtabs').'</strong>'; 
	$error_message = '<strong>'.__('Error!','dtabs').'</strong><br>';
	$delete_message = __('Tab successfully deleted.','dtabs');
	$save_message = __('Settings successfully saved.','dtabs');
	$reset_message = __('dTab settings successfully reset to defaults.','dtabs');
	
	if (isset($_GET['action'])) {
		if ($_GET['action'] == 'edit') {
			if (isset($_GET['new']) AND $_GET['new'] == true) {
				$title = __('New Tab','dtabs');
			} else {
				$title = __('Edit Tab','dtabs');
			}
		
		} elseif ($_GET['action'] == 'save') {
			$message = true;
			$number = trim($_POST['number']);
			$label = trim($_POST['label']);
			$title = trim($_POST['title']);
			$type = trim($_POST['type']);
			$url = trim($_POST['url']);
			$name = trim($_POST['name']);
			$menu = $_POST['menu'];
			$menuid = trim($_POST['id']);
			
			if (!is_numeric($number) && empty($number)) {
				$number = $total_tabs+1;
				$success_message .= '
					<li>'.sprintf(__('The tab number was set to %s.','dtabs'),$number).'</li>'; 
			
			} 
			
			if (!is_numeric($number)) {

				$number = ($total_tabs+1);
				
			}
								
			if ($number != $_GET['otab']) {
			
				if ($number < 1) {
					$success_message .= '
						<li>'.__('The tab number should be at least 1 and so was changed to 1.','dtabs').'</li>';
					$number = 1;
				} elseif ($number > ($total_tabs+1)) {
					$success_message .= '
						<li>'.__('The tab number was changed to be only one higher than the previous total number of tabs.','dtabs').'</li>';
					$number = ($total_tabs+1);
				}
				if ($number < $_GET["otab"]) {
					$temp = $dtab;
					foreach ($temp as $i => $v) {
						if (($i >= $number) && ($i < $_GET["otab"])) {
							$dtab[$i+1] = $v;
						}
					}
				} elseif ($number > $_GET["otab"]) {
					$temp = $dtab;
					foreach ($temp as $i => $v) {
						if (($i <= $number) && ($i > $_GET["otab"])) {
							$dtab[$i-1] = $v;
						}
					}
				}
			}
				
			if (empty($label)) {
				$success_message .= '
					<li>'.__('The tab name was also used for it\'s label.','dtabs').'</li>';
				$label = strtoupper(substr($name,0,1)).strtolower(substr($name,1)); 
			}
			$dtab[$number] = array( 'name' => $name, 'label' => $label, 'url' => $url, 'title' => $title, 'type' => $type, 'menu' => $menu, 'id' => $menuid);
			$success = true;
			update_option('dtabs',$dtab);
			$total_tabs = count($dtab);
		
		} elseif ($_GET['action'] == 'savecss') {
			$dtabs_options = array( 'auto_css' => $_POST['auto_css'], 'css' => $_POST['css_code']);
			update_option('dtabs_options',$dtabs_options);
			?>
				<div id="message" class="updated fade"><p>
					<strong><?php _e('Save successful.','dtabs'); ?></strong>
				</p></div>
			<?php
		} elseif ($_GET['action'] == 'delete') {
			$message = true;
			$complete_message .= $delete_message;
			if (isset($_POST) AND is_array($_POST)) {
				foreach ($_POST as $n => $v) {
					if (substr($n,0,4) == "del_" AND $v == 'on') {
						unset($dtab[substr($n,4)]);
					}
				}
			}
			$temp = array_values($dtab);
			$dtab = NULL;
			foreach ($temp as $i => $v) {
				$dtab[$i+1] = $v;
			}
			update_option('dtabs',$dtab);
		
		} elseif ($_GET['action'] == 'reset') {
			$message = true;
			$complete_message .= $reset_message;
			update_option('dtabs',$dtabs_default);
		}
	}

	if ( $message == true ) {
		if  ( $error == true ) {
			$complete_message .= $error_message;
		} 
		if ( $success == true ) {
			$complete_message .= $success_message;
		}
		$complete_message .= '</p></div>';
		
		echo $complete_message;
	}
	if($load_admin_panel) {
		if (isset($_GET['action']) AND $_GET['action'] == 'edit') {
			?>
		<script type="text/javascript">
			function hide(id){
				document.getElementById(id).style.display='none';
			}
			function show(id){
				document.getElementById(id).style.display='inline';
			}
			<?php
			$pcats = $wpdb->get_results("SELECT $wpdb->terms.term_id, name FROM $wpdb->terms INNER JOIN $wpdb->term_taxonomy USING (term_id) WHERE taxonomy = 'category' ORDER BY name");
			$pposts = $wpdb->get_results("SELECT ID, post_name FROM $wpdb->posts WHERE post_type = 'post' ORDER BY post_name");
			$ppages = $wpdb->get_results("SELECT ID, post_name FROM $wpdb->posts WHERE post_type = 'page' ORDER BY post_name");
			echo '
			function sendform() {
				document.blogform.url.disabled = false;
				document.blogform.menu.disabled = false;
			}
	
			function updatetype() {
				hide("front");
				hide("posts");
				hide("page");
				hide("post");
				hide("cat");
				hide("other");
				if(document.blogform.type.value!="archives" && document.blogform.type.value!="bookmarks") {
					show(document.blogform.type.value);
				}
				if(document.blogform.type.value=="cat" || document.blogform.type.value=="page") {
					document.blogform.menu.disabled = false;
				}
				else if (document.blogform.type.value=="archives" || document.blogform.type.value=="bookmarks") {
					document.blogform.url.value = "#"+document.blogform.type.value;
					document.blogform.url.disabled = true;
					document.blogform.menu.checked = true;
					document.blogform.menu.disabled = true;
					if (document.blogform.type.value=="archives") {
					';
			if ($dtab[$_GET["tab"]]["type"] == 'archives') {
				echo '
							document.blogform.name.value = "'.$dtab[$_GET["tab"]]["name"].'";
							';
			} else {
				echo'
							document.blogform.name.value = "'.__('archives','dtabs').'";
							';
			}
			echo '
						}
						else if (document.blogform.type.value=="bookmarks") {
							';
			if ($dtab[$_GET["tab"]]["type"] == 'bookmarks') {
				echo '
							document.blogform.name.value = "'.$dtab[$_GET["tab"]]["name"].'";
							';
			} else {
				echo'
						document.blogform.name.value = "'.__('bookmarks','dtabs').'";
						';
			}
			echo '
						}
						show("other");
					}
					else {
						document.blogform.menu.checked = false;
						document.blogform.menu.disabled = true;
					}
				}
				function updatename() {
					if (document.blogform.type.value == "front") {
						document.blogform.id.value = "";
						document.blogform.url.value = "'.get_bloginfo("url").'";
						document.blogform.url.disabled = true;
						document.blogform.name.value = "'.__('front_page','dtabs').'";
					}
					else if (document.blogform.type.value == "posts") {';
			if (get_option('show_on_front')=='posts') {
				echo '
						document.blogform.id.value = "";
						document.blogform.url.value = "'.get_bloginfo("url").'";';
			} else {
				$posts_page_id = get_option('page_for_posts');
				echo '
						document.blogform.id.value = "'.$posts_page_id.'";
						document.blogform.url.value = "'.get_permalink($posts_page_id).'";';
			} 
			echo '
						document.blogform.url.disabled = true;								
						document.blogform.name.value = "'.__('posts_page','dtabs').'";
					}
					else if (document.blogform.type.value == "page") {
						document.blogform.url.disabled = true;
						switch (document.blogform.pageselect.value) {';
			if (is_array($ppages)) {
				foreach ($ppages as $id => $page) {
					echo '
									case "'.$id.'":
									document.blogform.id.value = "'.$page->ID.'";
									document.blogform.url.value = "'.get_permalink($page->ID).'";
									document.blogform.name.value = "'.$page->post_name.'";
									break;
							';
				}
			}
			echo '
						}
					}
					else if (document.blogform.type.value == "post") {
						document.blogform.url.disabled = true;
						switch (document.blogform.postselect.value) {';
			if (is_array($pposts)) {
				foreach ($pposts as $id => $post) {
					echo '
									case "'.$id.'":
									document.blogform.id.value = "'.$post->ID.'";
									document.blogform.url.value = "'.get_permalink($post->ID).'";
									document.blogform.name.value = "'.$post->post_name.'";
									break;
							';
				}
			}
			echo '
						}
					}
					else if (document.blogform.type.value == "cat") {
						document.blogform.url.disabled = true;
						switch (document.blogform.catselect.value) {';
			if (is_array($pcats)) {
				foreach ($pcats as $id => $cat) {
					echo '
									case "'.$id.'":
									document.blogform.id.value = "'.$cat->term_id.'";
									document.blogform.url.value = "'.get_category_link($cat->term_id).'";
									document.blogform.name.value = "'.$cat->name.'";
									break;
							';
				}
			}
			echo '
						}
					}
					else if (document.blogform.type.value == "other") {
						document.blogform.url.disabled = false;
						';
			if ($dtab[$_GET["tab"]]["type"] == 'other') {
				echo '
						document.blogform.id.value = "";
						document.blogform.url.value = "'.$dtab[$_GET["tab"]]["url"].'";
						document.blogform.name.value = "'.$dtab[$_GET["tab"]]["name"].'";
						';
			} else {
				echo'
						document.blogform.id.value = "";
						document.blogform.url.value = "http://";
						document.blogform.name.value = "";
						';
			}
			echo '
					}
				}
				';
		} else {
			wp_nonce_field( 'closedpostboxes', 'closedpostboxesnonce', false ); ?>
		<script type='text/javascript'>
		/* <![CDATA[ */
			postboxL10n = {
				requestFile: "http://wptest2.dtcnet.co.uk/wp-admin/admin-ajax.php"
			}
		/* ]]> */
		</script>
		<script type='text/javascript' src='http://wptest2.dtcnet.co.uk/wp-admin/js/postbox.js?ver=20080128'></script>
		<script type='text/javascript'>
			jQuery(document).ready( function() {
				// close postboxes that should be closed
				jQuery('.if-js-closed').removeClass('if-js-closed').addClass('closed');
			
				// show things that should be visible, hide what should be hidden
				jQuery('.hide-if-no-js').show();
				jQuery('.hide-if-js').hide();
			
				// postboxes
				add_postbox_toggles('dtabs');

			});
		</script>
		<script type='text/javascript'>
			
			function hide(id){
				document.getElementById(id).style.display='none';
			}
			function toggle_check(c) {
		 		c=c.checked;
		 		t = document.getElementById('manage_tabs_form').getElementsByTagName('input');
		 		for(var i=0;i<t.length;i++) {
					if (t[i].name.substring(0,4)=='del_') {
						t[i].checked=c;
					}
		 		}
		 	}
			function extra(contentid,titleid) {
				if (document.getElementById(titleid).checked==true) {
					document.getElementById(contentid).style.display='table-row';
				}
				else {
					hide(contentid);
				}
			}
			
			function set_css(to) {
				layered_css = "/* Layered menu CSS generated by dTabs */\n\
\n\
/* style the tabs in IE (the trailing comma prevents other browsers from reading this) */\n\
#tabbar li, #tabbar ul li, {\n\
	\n\
	/* make them horizontal in IE*/\n\
	display: inline;\n\
	\n\
	/* space them a little in IE*/\n\
	margin: 0 5px;\n\
}\n\
\n\
/* style the tabs */\n\
.tab, .tabselected {\n\
	\n\
	/* make them horizontal in Firefox 2*/\n\
	display: -moz-inline-box;\n\
	\n\
	/* make them horizontal in all other browsers*/\n\
	display: inline-block;\n\
	\n\
	/* space them a little */\n\
	padding: 5px;\n\
	\n\
	/* set a grey background for non-selected tabs (which we will overide for selected tabs later) */\n\
	background-color: rgb(240,240,240);\n\
	\n\
	/* set a border, make it rounded at the top */\n\
	border: 1px solid rgb(150,150,150);\n\
	-moz-border-radius-topleft: 5px;\n\
	-moz-border-radius-topright: 5px;\n\
	-khtml-border-radius-top-left: 5px;\n\
	-khtml-border-radius-top-right: 5px;\n\
	-webkit-border-top-left-radius: 5px;\n\
	-webkit-border-top-right-radius: 5px;\n\
}\n\
\n\
/* make changes to the selected tab */\n\
.tabselected {\n\
	\n\
	/* set a white background */\n\
	background-color: white;\n\
	\n\
	/* make the border along the bottom blend into the white background */\n\
	border-bottom-color: white;\n\
}\n\
\n\
/* style the drop down menus and submenus */\n\
.dmenu, #tabbar .fademenu .dmenu ul li ul {\n\
	/* left align the text */\n\
	text-align: left;\n\
	\n\
	/* REQUIRED */\n\
	position: absolute;\n\
	\n\
	/* js fade method should display them 23px below the top of the tabs */\n\
	margin: 23px 0 0 0;\n\
	\n\
	/* put some space around the contents */\n\
	padding: 5px 15px;\n\
	\n\
	/* set a grey background */\n\
	background-color: rgb(240,240,240);\n\
	\n\
	/* set a border, round all the corners except the top left */\n\
	border: 1px solid rgb(150,150,150);\n\
	-moz-border-radius: 5px;\n\
	-moz-border-radius-topleft: 0;\n\
	-khtml-border-radius: 5px;\n\
	-khtml-border-radius-top-left: 0;\n\
	-webkit-border-radius: 5px;\n\
	-webkit-border-top-left-radius: 0;\n\
}\n\
#tabbar .dmenu ul {\n\
	\n\
	/* put space at the top and bottom of top-level menus */\n\
	padding: 5px 0 0 10px;\n\
	\n\
	/* stop ie going crazy */\n\
	margin: 0;\n\
	\n\
}\n\
\n\
/* REQUIRED: hide menus and submenus off screen by default, as well as child menus when their parent is selected */\n\
.dmenu, \n\
#tabbar .fademenu .dmenu ul li ul, \n\
#tabbar .fademenu .dmenu ul li:hover ul li ul, \n\
#tabbar .fademenu .dmenu ul li ul li:hover ul li ul {\n\
	left: -999em;\n\
}\n\
\n\
/* REQUIRED: show menus on hovering */\n\
#tabbar .fademenu:hover .dmenu, \n\
#tabbar .fademenu .dmenu ul li:hover ul, \n\
#tabbar .fademenu .dmenu ul li ul li:hover ul, \n\
#tabbar .fademenu .dmenu ul li ul li ul li:hover ul {\n\
	left: auto;\n\
}\n\
\n\
/* style the options in the menus */\n\
#tabbar .fademenu .dmenu a {\n\
	\n\
	/* make them so wide */\n\
	width: 8em;\n\
	\n\
	/* make them span the full width of that particular (sub)menu */\n\
	display: block;\n\
	\n\
	/* space them out a little and leave a gap between the edge of the (sub)menu */\n\
	padding: 2px 5px;\n\
}\n\
\n\
/* style menus */\n\
#tabbar .fademenu .dmenu {\n\
	\n\
	/* position top-level menus correctly */\n\
	margin: 5px 0 0 -6px;\n\
	\n\
	/* reset spacing */\n\
	padding: 0;\n\
}\n\
#tabbar .fademenu .dmenu ul {\n\
	\n\
	/* put space at the top and bottom of top-level menus */\n\
	padding: 5px 0;\n\
}\n\
\n\
#tabbar .fademenu .dmenu ul li ul, \n\
#tabbar .fademenu .dmenu ul li ul li ul {\n\
	\n\
	/* put space at the top and bottom of submenus */\n\
	padding: 5px 0;\n\
	\n\
	/* position submenus correctly */\n\
	margin: -22px 0 0 80px;\n\
}\n\
#tabbar .fademenu .dmenu ul li {\n\
	\n\
	/* dont use bullets */\n\
	list-style-type: none;\n\
	\n\
	/* no space around list-items */\n\
	padding: 0;\n\
	margin: 0;\n\
}\n\
#tabbar .fademenu .dmenu ul li:hover {\n\
	\n\
	/* change the background colour for options in the menus as they are hovered over */\n\
	background-color: rgb(250,250,250);\n\
}";
				default_css = "/* Default CSS generated by dTabs */\n\
\n\
/* style the tabs in IE (the trailing comma prevents other browsers from reading this) */\n\
#tabbar li, #tabbar ul li, {\n\
	\n\
	/* make them horizontal in IE*/\n\
	display: inline;\n\
	\n\
	/* space them a little in IE*/\n\
	margin: 0 5px;\n\
}\n\
\n\
/* style the tabs */\n\
.tab, .tabselected {\n\
	\n\
	/* make them horizontal in Firefox 2*/\n\
	display: -moz-inline-box;\n\
	\n\
	/* make them horizontal in all other browsers*/\n\
	display: inline-block;\n\
	\n\
	/* space them a little */\n\
	padding: 5px;\n\
	\n\
	/* set a grey background for non-selected tabs (which we will overide for selected tabs later) */\n\
	background-color: rgb(240,240,240);\n\
	\n\
	/* set a border, make it rounded at the top */\n\
	border: 1px solid rgb(150,150,150);\n\
	-moz-border-radius-topleft: 5px;\n\
	-moz-border-radius-topright: 5px;\n\
	-khtml-border-radius-top-left: 5px;\n\
	-khtml-border-radius-top-right: 5px;\n\
	-webkit-border-top-left-radius: 5px;\n\
	-webkit-border-top-right-radius: 5px;\n\
}\n\
\n\
/* make changes to the selected tab */\n\
.tabselected {\n\
	\n\
	/* set a white background */\n\
	background-color: white;\n\
	\n\
	/* make the border along the bottom blend into the white background */\n\
	border-bottom-color: white;\n\
}\n\
\n\
/* style the drop down menus */\n\
.dmenu {\n\
	/* left align the text */\n\
	text-align: left;\n\
	\n\
	/* REQUIRED */\n\
	position: absolute;\n\
	\n\
	/* js fade method should display them 23px below the top of the tabs */\n\
	margin: 23px 0 0 0;\n\
	\n\
	/* put some space around the contents */\n\
	padding: 5px 15px;\n\
	\n\
	/* set a grey background */\n\
	background-color: rgb(240,240,240);\n\
	\n\
	/* set a border, round all the corners except the top left */\n\
	border: 1px solid rgb(150,150,150);\n\
	-moz-border-radius: 5px;\n\
	-moz-border-radius-topleft: 0;\n\
	-khtml-border-radius: 5px;\n\
	-khtml-border-radius-top-left: 0;\n\
	-webkit-border-radius: 5px;\n\
	-webkit-border-top-left-radius: 0;\n\
}\n\
#tabbar .dmenu ul {\n\
	\n\
	/* put space at the top and bottom of top-level menus */\n\
	padding: 5px 0 0 10px;\n\
	\n\
	/* stop ie going crazy */\n\
	margin: 0;\n\
	\n\
}\n\
#tabbar .dmenu ul li {\n\
	\n\
	/* stop ie from displaying list items inline */\n\
	display: list-item;\n\
	\n\
}\n\
\n\
/* REQUIRED: hide menus off screen by default */\n\
.dmenu {\n\
	left: -999em;\n\
}\n\
\n\
/* STYLING JUST FOR CSS MENUS */\n\
#tabbar .fademenu .dmenu {\n\
	\n\
	/* position menus correctly */\n\
	margin: 5px 0 0 -6px;\n\
}\n\
/* REQUIRED: show menus on hovering */\n\
#tabbar .fademenu:hover .dmenu {\n\
	left: auto;\n\
}";
			<?php
		
			if ($dtabs_options['css'] != NULL) {
				?>
				if (to=='layered') {
					document.getElementById('css_code').value = layered_css;
				} 
				else if (to=='default') {
					document.getElementById('css_code').value = default_css;
				}
				else {
					document.getElementById('css_code').value = "<?php echo str_replace('<br />','\n\\', nl2br($dtabs_options['css'])); ?>";
				}
				<?php
			} else {
				?>
				if (to=='layered') {
					document.getElementById('css_code').value = layered_css;
				} 
				else {
					document.getElementById('css_code').value = default_css;
				}
				<?php
			}
			?>
			}
			<?php
		}
	
								
		echo '
				</script>
			
				<div class="wrap">
				<h2>'.$title.'</h2>
				<br class="clear" />
		';
		if ($_GET['action'] == 'edit') {
			echo '
				<form name="blogform" method="POST" action="'.$_SERVER["PHP_SELF"].'?page='.basename(__FILE__).'&amp;action=save&amp;otab='.$_GET['tab'].'"> 

					<table class="form-table">
						<tr valign="top">
							<th scope="row">Tab Number</th>
							<td><input name="number" type="text" value="'.($_GET["tab"]).'" size="3" /> '.__('The order in which the tab will appear in your tab bar, with 1 being the first.','dtabs').'</td>
						</tr>
						<tr valign="top">
							<th scope="row">'.__('Tab type','dtabs').':</th>
							<td>
							
							';?>
					<select name="type" onChange="updatetype();updatename()" style="width:150px">
						<option value="front" <?php if ($dtab[$_GET["tab"]]["type"] == 'front') {echo 'selected';} ?>>
							<?php _e('Front Page','dtabs'); ?>
						</option>
						<option value="posts" <?php if ($dtab[$_GET["tab"]]["type"] == 'posts') {echo 'selected';} ?>>
							<?php _e('Posts Page','dtabs'); ?>
						</option>
						<option value="page"  <?php if ($dtab[$_GET["tab"]]["type"] == 'page') {echo 'selected';} ?>>
							<?php _e('Page','dtabs'); ?>
						</option>
						<option value="post"  <?php if ($dtab[$_GET["tab"]]["type"] == 'post') {echo 'selected';} ?>>
							<?php _e('Post','dtabs'); ?>
						</option>
						<option value="cat" <?php if ($dtab[$_GET["tab"]]["type"] == 'cat') {echo 'selected';} ?>>
							<?php _e('Category','dtabs'); ?>
						</option>
						<option value="archives" <?php if ($dtab[$_GET["tab"]]["type"] == 'archives') {echo 'selected';} ?>>
							<?php _e('Archives','dtabs'); ?>
						</option>
						<option value="bookmarks" <?php if ($dtab[$_GET["tab"]]["type"] == 'bookmarks') {echo 'selected';} ?>>
							<?php _e('Bookmarks','dtabs'); ?> 
						</option>
						<option value="other" <?php if ($dtab[$_GET["tab"]]["type"] == 'other') {echo 'selected';} ?>>
							<?php _e('Other','dtabs'); ?>
						</option>
					</select>
							<?php _e('Refers to the destination of the tab.  Select "Other" to link to an external destiniation (ie not within Wordpress).','dtabs'); ?>
							</td>
						</tr>
						<tr valign="top">
							<th scope="row"><?php _e('Tab name','dtabs'); ?>:</th>
							<td>
								<div id="front">
									<input type="text" disabled value="front_page" size="50">
								</div>
								<div id="posts">
									<input type="text" disabled value="posts_page" size="50">
								</div>
								<div id="cat">
									<select name="catselect" onChange="updatename()"  style="width:150px">
										<?php
										if (is_array($pcats)) {
											foreach ($pcats as $id => $cat) {
												if ($cat->name == $dtab[$_GET["tab"]]["name"]) {
													echo '<option value="'.$id.'" selected>'.$cat->name.'</option>
													';
												} else {
													echo '<option value="'.$id.'">'.$cat->name.'</option>
													';
												}
											}
										}
										?>
									</select>
								</div>
								<div id="page">
									<select name="pageselect" onChange="updatename()" style="width:150px">
										<?php
										if (is_array($ppages)) {
											foreach ($ppages as $id => $page) {
												if ($page->post_name == $dtab[$_GET["tab"]]["name"]) {
													echo '<option value="'.$id.'" selected>'.$page->post_name.'</option>
													';
												} else {
													echo '<option value="'.$id.'">'.$page->post_name.'</option>
													';
												}
											}
										}
										?>
									</select>
								</div>
								<div id="post">
									<select name="postselect" onChange="updatename()" style="width:150px">
										<?php
										if (is_array($pposts)) {
											foreach ($pposts as $id => $post) {
												if ($post->post_name == $dtab[$_GET["tab"]]["name"]) {
													echo '<option value="'.$id.'" selected>'.$post->post_name.'</option>
													';
												} else {
													echo '<option value="'.$id.'">'.$post->post_name.'</option>
													';
												}
											}
										}
										?>
									</select>
								</div>
								<div id="other">
								<?php
									echo '
									<input type="text" name="name" value="'.$dtab[$_GET["tab"]]["name"].'" size="50"/>
									';
									?>
								</div>	
							</td>
						</tr>
						<tr valign="top">
							<th scope="row"><?php _e('Tab Label','dtabs'); ?>:</th>
							<td>
							<input name="label" type="text" value="<?php echo $dtab[$_GET["tab"]]["label"]; ?>" size="50" /></td>
						</tr>
						<tr valign="top">
							<th scope="row"><?php _e('Tab URL/Path/Permalink','dtabs'); ?>:</th>
							<td><input name="url" type="text" value="<?php echo $dtab[$_GET["tab"]]["url"]; ?>" size="50" /></td>
						</tr>
						<tr valign="top">
							<th scope="row"><?php _e('Tab Description','dtabs'); ?>:</th>
							<td>
								<input name="title" type="text" value="<?php echo $dtab[$_GET["tab"]]["title"]; ?>" size="50" />
								<?php _e('Will be set as the tab\'s <i>title</i> attribute.','dtabs'); ?>
							</td>
						</tr>
						<tr valign="top">
							<th scope="row"><?php _e('Menu','dtabs'); ?>:</th>
							<td>
								<?php _e('Show menu on hover?','dtabs'); ?> <input type="checkbox" name="menu"<?php echo dtab_checked($dtab[$_GET["tab"]]["menu"]); ?> /> 
								<input type="hidden" name="id" value="<?php echo $dtab[$_GET["tab"]]["id"]; ?>" />
							</td>
						</tr>
					</table>
					<?php
					if ($_GET["new"] == true) {
						echo '<p class="submit"><input type="submit" name="submit" value="'.__('Save Tab','dtabs').' &raquo;" onclick="sendform();" /></p>';
					} else {
						echo '<p class="submit"><input type="submit" name="submit" value="'.__('Update Tab','dtabs').' &raquo;" onclick="sendform();" /></p>';
					}
				echo '
				</form>
				<p><a href="'.$_SERVER["PHP_SELF"].'?page='.basename(__FILE__).'">&laquo; '.__('Return to Tab List','dtabs').'</a></p>
				</div>
				
				<script type="text/javascript">
					updatetype();
					updatename();
				</script>
				
				
			';
		} else {
			?>
			<p><?php echo sprintf(__('Please note that if your theme is not pre-enabled for dTabs you need to <a href="%s">paste a snippet of code</a> into your theme before your tabs will appear on your blog, alternatively/in addition you could ask your theme\'s author to pre-enable your theme.','dtabs'),'http://dynamictangentconceptions.dtcnet.co.uk/downloads/wp-plugins/dtabs-dynamic-tabs-wordpress-plugin/#enabletheme'); ?></p>
			<?php
			echo '
			<style type="text/css">
				.action_buttons {
					vertical-align: middle !important;
					margin-right: 20px;
				}
			</style>
			<form id="manage_tabs_form" method="POST" action="'.$_SERVER["PHP_SELF"].'?page='.basename(__FILE__).'&amp;action=delete"> 
				
			<div class="tablenav">
				<div class="alignleft">
					<input type="submit" value="'.__('Delete','dtabs').'" name="delete" class="button-secondary delete action_buttons" />
					<a class="button-highlighted action_buttons" href="'.$_SERVER['PHP_SELF'].'?page='.basename(__FILE__).'&amp;action=edit&amp;new=1&amp;tab='.($total_tabs+1).'">'.__('Create New Tab','dtabs').'</a>
					<a class="button delete action_buttons" href="'.$_SERVER['PHP_SELF'].'?page='.basename(__FILE__).'&amp;action=reset">Reset</a>
				</div>
				<br class="clear" />
			</div>
			<br class="clear" />
			
			<table class="widefat"> 
					<thead>
					<tr> 
						<th scope="col" class="check-column"><input type="checkbox" onclick="toggle_check(this)" /></th> 
						<th scope="col">'.__('Number','dtabs').'</th> 
						<th scope="col">'.__('Name','dtabs').'</th> 
						<th scope="col">'.__('Label','dtabs').'</th>
						<th scope="col">'.__('URL/Path/Permalink','dtabs').'</th>
						<th scope="col">'.__('Description','dtabs').'</th> 
					</tr>
					</thead>
			';
			if (is_array($dtab)) {
				ksort($dtab);
				foreach ($dtab as $i => $v ) {
					
					if ($i%2) { 
						echo '<tr class="alternate">';
					 } else {
						echo '<tr>';
					}
					
					echo '
						<th scope="row" class="check-column"><input type="checkbox" name="del_'.$i.'" /></th>
						<td>'.$i.'</td> 
						<td><a href="'.$_SERVER["PHP_SELF"].'?page='.basename(__FILE__).'&amp;action=edit&amp;new=0&amp;tab='.$i.'" title="'.__('Edit','dtabs').' '.$dtab[$i]["name"].'" class="edit">'.$dtab[$i]["name"].'</a></td>
						<td>'.$dtab[$i]["label"].'</td>
						';
						if (strlen($dtab[$i]["url"]) >= 44) {
							$short_url = substr($dtab[$i]["url"],0,20).'....'.substr($dtab[$i]["url"],strlen($dtab[$i]["url"])-20,20);
							echo '<td>'.$short_url.'</td> ';
						} else {
							echo '<td>'.$dtab[$i]["url"].'</td>';
						}
						echo '
						<td>'.$dtab[$i]["title"].'</td> 
						</tr> 
					';
				}
			}
			
			echo '
				</table> 
				</form>
				<div class="tablenav">
					<br class="clear" />
				</div>
				<br class="clear" />
				
			';
			echo '
			<div id="poststuff">
				<div id="css" class="postbox '.postbox_classes('css', 'dtabs').'">
					<h3>'.__('CSS','dtabs').'</h3>
					<div class="inside">
						<p>'.sprintf(__('Tick "Automatic css generation" to have CSS outputed into the header section of each page generated by your Wordpress installation.  You can use the default CSS as a guide for how to generate your own CSS which can be directedtly entered into the CSS textarea.  For more advanced CSS examples take a look at the <a href="%s">dTabs homepage</a>.','dtabs'),'http://dynamictangentconceptions.dtcnet.co.uk/downloads/wp-plugins/dtabs-dynamic-tabs-wordpress-plugin/#eg').'</p>
						<form name="blogform" method="POST" action="'.$_SERVER["PHP_SELF"].'?page='.basename(__FILE__).'&amp;action=savecss"> 
							<table class="form-table">
								<tr valign="top">
									<th scope="row">'.__('Automatic CSS generation','dtabs').':</th>
									<td>
										<input name="auto_css" id="auto_css" type="checkbox" onclick="extra(\'css_row\',\'auto_css\')"'.dtab_checked($dtabs_options['auto_css']).'>
									</td>
								</tr>
								<tr valign="top" id="css_row">
									<th scope="row">'.__('CSS styling','dtabs').':</th>
									<td>
										<textarea name="css_code" id="css_code" rows="20" style="width:100%; font: 12px \'Courier New\', Courier, mono;">
										</textarea>
										<br />
										<a href="javascript:set_css(\'default\');">'.__('Use default','dtabs').'</a>  <a href="javascript:set_css(\'reset\');">'.__('Reset','dtabs').'</a> (<a href="javascript:set_css(\'layered\');">'.__('Try layered menu css','dtabs').'</a> '.__('- requires css fadetype and not currently supported by IE or Firefox 2','dtabs').') <a href="http://www.w3schools.com/css/" target="_blank">'.__('CSS tutorial and reference','dtabs').'</a>
									</td>
								</tr>
							</table>
							<p class="submit"><input type="submit" name="submit" value="'.__('Update CSS Settings','dtabs').' &raquo;" /></p>
		
						</form>
					</div>
				</div>
				<div id="uninstall" class="postbox '.postbox_classes('uninstall', 'dtabs').'">
					<h3>'.__('Uninstall','dtabs').'</h3>
					<div class="inside">
						<table class="form-table">
						<tr valign="top">
							<th scope="row">
							<a href="'.$_SERVER["PHP_SELF"].'?page='.basename(__FILE__).'&amp;action=uninstall_dtabs" class="button delete">'.__('Uninstall dTabs','dtabs').'</a>
							</th>
							<td>
							'.sprintf(__('Please note this will completely remove all of dTab\'s settings from your database.  If you would like to save your current settings for possible future use, please deactivate dTabs in the <a %s>Plugins</a> admin panel or delete %s from your plugins directory.','dtabs'),'href="plugins.php"',plugin_basename(__FILE__)).'</p>
							</td>
						</tr>
						</table>
					</div>
				</div>
			</div>
			<script type="text/javascript">
			set_css(\'reset\');
			extra(\'css_row\',\'auto_css\');
			</script>
			';
			
			?>
					
			</div>
			<?php
		}
	}
}

// for use in the templates - writes the links with the class of the link to the current page set to 'tabselected', 
// and the rest set to 'tab'

	
function dtab_list_tabs($args = '') {
	global $post, $wp_query, $wpdb, $dtabs_options, $dtabs_current_version;
	
	$defaults = array(
		'before' => '',
		'after' => '',
		'between' => '',
		'fadetype' => 'js',
		'outputjs' => 1,
		'fadetime' => 500
	);

	$r = wp_parse_args( $args, $defaults );
	
	if (is_array($dtabs = get_option('dtabs'))) {
		if ($r['fadetype']=='js' AND $r['outputjs']) {
			$needoutputjs = false;
			foreach ($dtabs as $dtab) {
				if ($dtab['menu'] == true) {
					$needoutputjs = true;
				}
			}
			if ($needoutputjs) {
				if ($r['fadetime']>0) {
					?>
		<script type="text/javascript">
			var zindex=100;
			function showmenu(menuid,buttonid) {
				if (eval('typeof(menuisvisible_'+menuid+')==\'undefined\'')) {
					eval('menuisvisible_'+menuid+'=false; showingmenu_'+menuid+'=false; hidingmenu_'+menuid+'=false; menubuttonid_'+menuid+'=\''+buttonid+'\'');
				}
				eval('shouldshowmenu_'+menuid+'=true;');
				showmenunow(menuid);
			}
			function showmenunow(menuid) {
				if (eval('menuisvisible_'+menuid+'==false') && eval('shouldshowmenu_'+menuid+'==true') && eval('showingmenu_'+menuid+'==false') && eval('hidingmenu_'+menuid+'==false')) {
					eval('shouldhidemenu_'+menuid+'=false; showingmenu_'+menuid+'=true');
					
					var obj = document.getElementById(eval('menubuttonid_'+menuid));
					var curleft = curtop = 0;
					var i = 1;
					while (obj) {
						curleft += obj.offsetLeft;
						curtop += obj.offsetTop;
						obj = obj.offsetParent;
						i++;
					}
					
					var e=document.getElementById(menuid);
					e.style.position="absolute";
					e.style.top=curtop+"px";
					e.style.left=curleft+"px";
					e.style.display="inline";
					e.style.zIndex=zindex++;
					time = <?php echo $r['fadetime']; ?>;
					p=50;
					t=0;
					s= 100/(time/p);
					o=0;
					changeOpac(o,menuid);
					while (o<=100) {
						setTimeout("changeOpac("+Math.round(o)+",'"+menuid+"')",t);
						o=o+s;
						t = t+p;
					}
					setTimeout('showingmenu_'+menuid+'=false; menuisvisible_'+menuid+'=true; hidemenunow(\''+menuid+'\');',t+p);
				}
				
			}
			function hidemenu(menuid) {
				eval('shouldshowmenu_'+menuid+'=false');
				setTimeout('hidemenunow(\''+menuid+'\')', <?php echo $r['fadetime']+100; ?>);
			}
			function hidemenunow(menuid) {
				if (eval('menuisvisible_'+menuid+'==true') && eval('shouldshowmenu_'+menuid+'==false') && eval('hidingmenu_'+menuid+'==false') && eval('showingmenu_'+menuid+'==false')) {
					eval('hidingmenu_'+menuid+'=true');
					time = <?php echo $r['fadetime']; ?>;
					p=50;
					t=0;
					s= 100/(time/p);
					o=100;
					changeOpac(o,menuid);
					while (o>=0) {
						setTimeout("changeOpac("+Math.round(o)+",'"+menuid+"')",t);
						o=o-s;
						t = t+p;
					}
					setTimeout('document.getElementById(\''+menuid+'\').style.left= \'-999em\';changeOpac(100,\''+menuid+'\'); hidingmenu_'+menuid+'=false; menuisvisible_'+menuid+'=false; showmenunow(\''+menuid+'\');',t+p);
				}
			}
			function changeOpac(opacity, id) { 
			    var object = document.getElementById(id).style; 
			    object.opacity = (opacity / 100); 
			    object.MozOpacity = (opacity / 100); 
			    object.KhtmlOpacity = (opacity / 100); 
			    object.filter = "alpha(opacity=" + opacity + ")"; 
			}
		</script>
					<?php
				} else {
					?>
		<script type="text/javascript">
			var activemenuid = "";
			var keepshowingmenu=0;
			function showmenu(menuid,buttonid) {
				if (activemenuid!=menuid) {
					keepshowingmenu=0;
					hidemenunow();
					activemenuid = menuid;
				}
				keepshowingmenu=1;
				var obj = document.getElementById(buttonid);
				var curleft = curtop = 0;
				var i = 1;
				while (obj) {
					curleft += obj.offsetLeft;
					curtop += obj.offsetTop;
					obj = obj.offsetParent;
					i++;
				}
				var e=document.getElementById(menuid);
				e.style.position="absolute";
				e.style.top=curtop+"px";
				e.style.left=curleft+"px";
				e.style.left= "auto";
				
			}
			function hidemenu() {
				keepshowingmenu=0;
				menutimeout=window.setTimeout("hidemenunow()", 1000);
			}
			function hidemenunow() {
				if (activemenuid != "") {
					if (keepshowingmenu==0 && activemenuid!="") {
						e=document.getElementById(activemenuid);
						e.style.left= "-999em";
						
					}
				}
			}
		</script>
					<?php
				}
			}
		}
		
		$show_on_front = get_option('show_on_front');
		$page_on_front = get_option('page_on_front');
		$page_for_posts = get_option('page_for_posts');
		
		if (is_page()) {
			if ($page_list = $wpdb->get_results("SELECT id, post_parent FROM $wpdb->posts", ARRAY_A)) {
				$post = $wp_query->post;
				
				foreach ($page_list as $page) {
					$pages[$page['id']] = $page;
				}
				$id = $post->ID;
				$parents = true;
				while ($parents != false) {
					$page_parents[$id] = $id;
					$id = $pages[$id]['post_parent'];
					if ($id == 0) {
						$parents = false;
					}
				}
			}
		} elseif (is_single() OR is_category()) {	
			if ($cat_list = $wpdb->get_results("SELECT $wpdb->terms.term_id, parent FROM $wpdb->terms INNER JOIN $wpdb->term_taxonomy USING (term_id) WHERE taxonomy = 'category'", ARRAY_A)) {
				foreach ($cat_list as $cat) {
					$cats[$cat['term_id']] = $cat;
					
				}
				if (is_single()) {
					$categories = get_the_category();
					foreach ($categories as $cat) {
						$id = $catid = $cat->term_id;
						$parents = true;
						while ($parents != false) {
							$category_parents[$catid][$id] = $id;
							$id = $cats[$id]['parent'];
							if ($id == 0) {
								$parents = false;
							}
						}
					}
				} else {
					$id = $catid = intval(get_query_var('cat'));
					$parents = true;
					while ($parents != false) {
						$category_parents[$catid][$id] = $id;
						$id = $cats[$id]['parent'];
						if ($id == 0) {
							$parents = false;
						}
					}
				}
			}
		}
			

		$have_menus = NULL;
		foreach ($dtabs as $i => $tab) {
			if ((is_page() AND $tab['type']=='page') OR ($show_on_front=='page' AND is_page($page_on_front) AND $tab['type']=='front')) {
				$i = 0;
				foreach ($page_parents as $page_id) {
					if ($tab['id'] == $page_id) {
						$current_tabs[] = array('name' => $tab['name'], 'level' => $i);
					}
					$i++;
				}
			} 
			if ((is_single() AND ($tab['type'] == 'post' OR $tab['type'] == 'cat')) OR (is_category() AND $tab['type'] == 'cat')) {
				if (is_single() AND $tab['type'] == 'post' AND $tab['id'] == $post->ID) {
					$current_tabs[] = array('name' => $tab['name'], 'level' => 0);
				}
				
				foreach ($category_parents as $parents) {
					$i = 0;
					foreach ($parents as $cat_id) {
						if ($tab['id'] == $cat_id) {
							$current_tabs[] = array('name' => $tab['name'], 'level' => $i);
						}
						$i++;
					}
				}
			}
			if (is_archive() AND !is_category() AND $tab['type']=='archives') {
				$current_tabs[] = array('name' => $tab['name'], 'level' => 0);
			}
			if ( $tab['type']=='front' AND (( is_home() AND $show_on_front=='posts' ) OR ( is_page($page_on_front) AND $show_on_front=='page' )) ) {
				$current_tabs[] = array('name' => $tab['name'], 'level' => -2);
			}
			if ( $tab['type']=='posts' AND is_home() ) {
				$current_tabs[] = array('name' => $tab['name'], 'level' => -1);
			}
			if (isset($tab['menu']) AND $tab['menu']==true AND is_null($have_menus)) {
				$have_menus = true;
			}
		}
		
		if (isset($current_tabs) AND is_array($current_tabs)) {
			// defaut to first current_tab if all at the same level
			$current_tab = array('name' => $current_tabs[0]['name'], 'level' => $current_tabs[0]['level']);
			// find the one with the lowest level
			foreach ($current_tabs as $ctab) {
				if ($ctab['level'] < $current_tab['level']) {
					$current_tab = $ctab;
				}
			}
			$current_tab = $current_tab['name'];
		} else {
			$current_tab = NULL;
		}
		
		$tabs = NULL;
		$total_tabs = count($dtabs);
		
		echo '
		<ul id="tabbar">';
		
		foreach ($dtabs as $i => $tab) {
			
			$c_tab_name = preg_replace("[\W]", "_", $tab['name']);
			
			if ($have_menus AND $r['fadetype']=='js') {
				if (isset($tab['menu']) AND $tab['menu'] == true) {
					$onmouseover = ' onmouseover="showmenu(\''.$c_tab_name.'_menu\',\''.$c_tab_name.'_button\');"';
					$onmouseout = ' onmouseout="hidemenu(\''.$c_tab_name.'_menu\');"';
				} else {
					if ($r['fadetime']>0) {
						$onmouseover = '';
					} else {
						$onmouseover = ' onmouseover="hidemenunow();"';
					}
					$onmouseout = '';
				}
			} else {
				$onmouseover = '';
				$onmouseout = '';
			}
			if ($have_menus AND $r['fadetype']=='css' AND isset($tab['menu']) AND $tab['menu'] == true) {
				$cssfade = ' fademenu';
			} else {
				$cssfade = '';	
			}
			
			if ($tab['archives'] == 'post' OR $tab['type'] == 'bookmarks') {
				$onclick = ' onclick="return false"'; 
			} else {
				$onclick = '';
			}
			
			if ($tab['type'] == 'front') {
				$url = get_bloginfo("url");
			} elseif ($tab['type'] == 'posts') {
				if ($show_on_front=='posts') {
					$url = get_bloginfo("url");
				} else {
					$url = get_permalink($page_for_posts);
				}
			} elseif ($tab['type'] == 'cat') {
				$url = get_category_link($tab['id']);
			} elseif ($tab['type'] == 'post' OR $tab['type'] == 'page') {
				$url = get_permalink($tab['id']);
			} else {
				$url = $tab['url'];
			}
			
			if ($current_tab == $tab['name']) {
				$class = 'tabselected';
			} else {
				$class = 'tab';
			}
			if (!is_null($have_menus)) {
				$id = ' id="'.$c_tab_name.'_button"';
			} else {
				$id = NULL;
			}
			if ($i<$total_tabs) {
				$between = $r['between'];
			} else {
				$between = "";
			}
			echo '
					<li class="'.$class.$cssfade.'"'.$id.$onmouseover.$onmouseout.'>'.$r['before'].'<a href="'.$url.'"'.$onclick.' title="'.$tab['title'].'" id="'.$c_tab_name.'_tab">'.stripslashes($tab['label']).'</a>'.$r['after'];
					
			if (isset($tab['menu']) AND $tab['menu'] == true) {
				echo '<div class="dmenu" id="'.$c_tab_name.'_menu"'.$onmouseover.$onmouseout.'>
						<ul>';
				if ($tab['type'] == 'cat') {
					wp_list_categories('orderby=name&child_of='.$tab['id'].'&title_li=');
				} elseif ($tab['type'] == 'page') {
					wp_list_pages('child_of='.$tab['id'].'&title_li=');
				} elseif ($tab['type'] == 'archives') {
					wp_get_archives('type=monthly');
				} elseif ($tab['type'] == 'bookmarks') {
					wp_list_bookmarks('title_before=<b>&title_after=</b>');
				}
				echo '
						</ul>
					</div>
					';
			}
			echo '</li>
			'.$between;
		}
		echo '
		<!-- 
		'.__('current tab name','dtabs').' = "'.$current_tab.'". 
		'.sprintf(__('dynamic tabs made and maintained using dTabs version %s','dtabs'),$dtabs_current_version).' http://dynamictangentconceptions.dtcnet.co.uk/downloads/wp-plugins/dtabs-dynamic-tabs-wordpress-plugin/
		-->
		</ul>
		';
	}
			
}

// included for compatability with pre 1.2 themes
function dtab_echo_dtabs($before="",$after="",$outputjs=1) {
	dtab_list_tabs("before=".$before."&after=".$after."&outputjs=".$outputjs);
}

/*CSS Styling*/
function dtabs_css() {
	global $dtabs_options;
	
	echo '
		<style type="text/css" media="screen">
		'.$dtabs_options['css'].'
		</style>
		';
}

// Internationalise
load_plugin_textdomain('dtabs', 'wp-content/plugins');

// Insert the dtc_add_pages() sink into the plugin hook list for 'admin_menu'
add_action('admin_menu', 'dtab_add_pages');

// Add auto css if necessary
if (is_array($dtabs_options = get_option('dtabs_options'))) {
	if ($dtabs_options['auto_css']) {
	    add_filter('wp_head', 'dtabs_css', 2);
	}
}
endif;
?>