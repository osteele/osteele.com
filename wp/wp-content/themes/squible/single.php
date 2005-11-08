<?php
ob_start();
get_header();
?>
<?php include("squible_options.php"); ?>

<?php
if ($_POST['action']) {
    $action=$_POST['action'];
} else {
    $action=$_GET['action'];
}
?>

<?php if (have_posts()) : while (have_posts()) : the_post(); ?>

<div class="navigation">
                <p class="alignleft"><?php previous_post('&laquo; %','','yes') ?></p>
                <p class="alignright"><?php next_post(' % &raquo;','','yes') ?></p>
                <div class="clear"></div>
        </div>
<br />

<?php

function verify_login() {
    global $cookiehash;
    global $tableusers, $wpdb;

    if (!empty($_COOKIE['wordpressuser_' . $cookiehash])) {
        $user_login = $_COOKIE['wordpressuser_' . $cookiehash];
        $user_pass_md5 = $_COOKIE['wordpresspass_' . $cookiehash];
    } else {
        return false;
    }

    if ('' == $user_login)
        return false;
    if (!$user_pass_md5)
        return false;

    $login = $wpdb->get_row("SELECT user_login, user_pass FROM $tableusers WHERE user_login = '$user_login'");

    if (!$login) {
        return false;

    } else {
        if ($login->user_login == $user_login && md5($login->user_pass) == $user_pass_md5) {
            return true;
        } else {
            return false;
        }
    }
}


global $user_level;
get_currentuserinfo();
switch($action) {

case 'addcat':

   if ( verify_login() ) {
        $catstring= wp_specialchars($_POST['cat_name']);
	$mycats = split(',',$catstring);
	foreach($mycats as $cat_name) {
        	$id_result = $wpdb->get_row("SHOW TABLE STATUS LIKE '$wpdb->categories'");
        	$cat_ID = $id_result->Auto_increment;
        	$category_nicename = sanitize_title($cat_name, $cat_ID);
        	$category_description = $_POST['category_description'];
        	$cat = intval($_POST['cat']);

        	//First let's see if the category exists
        	$catexists = $wpdb->get_row("SELECT * FROM $wpdb->categories WHERE cat_name = '$cat_name'");

        	if (!$catexists) {

                	$wpdb->query("INSERT INTO $wpdb->categories (cat_ID, cat_name, category_nicename, category_description, category_parent) VALUES ('0', '$cat_name', '$category_nicename', '$category_description', '$cat')");
                	$catexists = $wpdb->get_row("SELECT * FROM $wpdb->categories WHERE cat_name = '$category_nicename'");
                	$wpdb->query("
                	INSERT INTO $wpdb->post2cat
                	(post_id, category_id)
                	VALUES
                	($post->ID, $cat_ID)
                	");
                	$added=TRUE;
        	} else {
                	$exists = $wpdb->get_row("SELECT * FROM $wpdb->post2cat WHERE post_id = $post->ID AND category_id = $catexists->cat_ID");
                	if (!$exists) {
                        	$wpdb->query("
                        	INSERT INTO $wpdb->post2cat
                        	(post_id, category_id)
                        	VALUES
                        	($post->ID, $catexists->cat_ID)
                        	");
                        	$added=TRUE;
                	}
        	}
	}
   }

   $thepost=get_permalink($post->ID);
   $blogname = get_bloginfo('name');
   mail ($tagemail, "[$blogname] New tag added", "tag \"$cat_name\" added to $thepost by $user_login");
break;

case 'delete':
        $cat_ID = (int) $_GET['cat_ID'];
        if ( $user_level > 3) {
                $wpdb->query("DELETE from $wpdb->post2cat WHERE post_id = '$post->ID' AND category_id='$cat_ID'");
                $deleted = $cat_ID;
                //echo "DELETE from $wpdb->post2cat WHERE post_id = '$post->ID' AND category_id='$cat_ID'";
        }
break;
}

if ($added) {
	header("Location: $thepost#extras");
}

?>

<?php $the_date = the_date('','','', false);?>


<div class="post">
<h2 class="storytitle" id="post-<?php the_ID(); ?>"><?php the_title(); ?><span style="display:inline; color:#ccc; font-weight: normal;"> <?php the_time('F j, Y'); ?></span></h2>
<?php if ($show_author) { ?>
<div class="author"><small><?php the_author('namefl'); ?></small></div>
<?php } ?>


	<div class="storycontent">
	<?php the_content(); ?>
	</div>
</div>

<div style="background-color: #f5f5f5">

<div style="padding: 10px">
<a id="extras"></a>
<table cellpadding="0" cellspacing="0"><tr><td valign="top" style="width: 140px; padding-left: 25px;">
        <div class="tooltitle">Tags</div>
         <p style="margin-top: -2px; margin-bottom: 4px;" class="post-footer"><em>
		<?php
		if (function_exists('UTW_ShowTagsForCurrentPost')) {
			UTW_ShowTagsForCurrentPost("commalist");
		} else {
                	show_tags(); 
		}
		?>
        </em></p>
	</td><td valign="top" style="padding-left: 20px;">
        <div class="tooltitle">Conversation</div>
        <p style="margin-top: -2px; margin-bottom: 4px;" class="post-footer"><em>
        <a href="http://www.technorati.com/search/<?php the_permalink() ?>">Technorati Cosmos</a><br />
        <a href="http://feedster.com/links.php?url=<?php the_permalink() ?>">Feedster</a><br />
        <a href="http://www.bloglines.com/citations?url=<?php the_permalink() ?>">Bloglines</a>
        </em></p>
	</td><td valign="top" style=" width: 150px; padding-left: 20px;">
        <div class="tooltitle">Related Tags</div>
        <p style="margin-top: -2px; margin-bottom: 4px;" class="post-footer"><em>
	<?php 
	if (function_exists('UTW_ShowRelatedTagsForCurrentPost')) {
		UTW_ShowRelatedTagsForCurrentPost('commalist','',15);
	} else {
		related_tags($post->ID, 15, '', ''); 
	}
	?>
        </em></p>
	</td><td valign="top" style="padding-left: 20px;">
        <div class="tooltitle">Comments</div>
        <p style="margin-top: -2px; margin-bottom: 4px;" class="post-footer"><em>
        <a href="<?php the_permalink() ?>#comments" rel="bookmark">view comments</a><br />
        <a href="<?php the_permalink() ?>#postcomment" rel="bookmark">Post comment</a><br />
        <?php edit_post_link(__('Edit This Post')); ?><br />
        </em></p>
	</td><td valign="top" style="padding-left: 20px; padding-right: 20px;">
        <div class="tooltitle">Trackback</div>
        <p style="margin-top: -2px; margin-bottom: 4px;" class="post-footer"><em>
        <a href="<?php the_permalink() ?>trackback" rel="bookmark">Trackback <abbr title="Uniform Resource Identifier">URI</abbr></a>
        </em></p>
</td>
</tr><tr>
<td style="padding-left: 25px;" colspan="4">
		<p class="post-footer"><em>
		<?php if ( verify_login() && !function_exists('UTW_ShowTagsForCurrentPost')) { ?>
		<form action="<?php $_SERVER['PHP_SELF']; ?>#extras" method="post">
   		<input type="hidden" name="action" value="addcat" />
  		<p><input type="text" name="cat_name" value="" style="font-size: 88%" /> <input style="font-size: 88%" type="submit" value="Add"><br /><font color="#000000">Add Tags:</font> seperate <br />multiple tags with commas</p>
  		</form>

		<?php if ($deleted) { ?>
		<br />Tag deleted, refresh list:
		<form method="post" action="<?php bloginfo('stylesheet_directory') ?>/refresh.php">
		<input type="hidden" name="nurl" value="<?php the_permalink() ?>#extras" />
		<input type="submit" value="refresh" />
		</form>
		<?php } ?>
		
        	<?php } else { ?>
		<?php if (!function_exists('UTW_ShowTagsForCurrentPost')) { ?>
		<br /><a href="<?php bloginfo('url'); ?>/wp-admin">Logged in</a> users can add tags
		<br />Create an account <a href="<?php bloginfo('url'); ?>/wp-register.php">here</a>
		<?php } ?>
        	<?php } ?>
		</em></p>
</td></tr></table>

</div>

<?php comments_template(); ?>

<?php endwhile; else: ?>
<p><?php _e('Sorry, no posts matched your criteria.'); ?></p>
<?php endif; ?>

<?php get_footer(); ?>
