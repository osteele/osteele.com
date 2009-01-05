<?php
/*
Plugin Name: Manageable
Plugin URI: http://www.aaronharp.com/dev/wp-manageable/
Description: Inline editing of the date, title, author, categories, tags, status and more on both posts and pages without leaving the "Manage" admin sections. No need to load each post or page individually.  Simply double-click anywhere in the post or page row and when you're done, press enter.  Requires WordPress 2.5 or above.
Author: Aaron Harp
Version: 1.1
Author URI: http://www.aaronharp.com/
*/ 

/*
Copyright 2008 Aaron Harp
  
This program is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 3 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program.  If not, see <http://www.gnu.org/licenses/>.
*/

//-----------------//
//--AJAX-REQUESTS--//
//-----------------//

if(strstr($_SERVER['PHP_SELF'], 'manageable.php') && isset($_POST['action'])) {
  if(!defined('WP_ADMIN')) 
    define('WP_ADMIN', TRUE);
  require_once('../../../wp-config.php');
  require_once('../../../wp-admin/includes/admin.php');
  
  mgbl_integrate();
  
  switch($_POST['action']) {
    case 'edit':
      if(!isset($_POST['id'])) break;
      mgbl_edit_row($_POST['id'], $_POST['type']);
      break;
    case 'save':
      mgbl_save_row($_POST, $_POST['post_type']);
      mgbl_get_row($_POST['post_ID'], $_POST['post_type']);
      break;
    case 'get':
      if(!isset($_POST['id'])) break;
      mgbl_get_row($_POST['id'], $_POST['type']);
      break;
    case 'ajax':
      if(!isset($_POST['tags'])) break;
      mgbl_tag_suggestions($_POST['tags']);
      break;
  }
  exit;
}

//------------------//
//--WP-INTEGRATION--//
//------------------//

add_action('load-edit.php', 'mgbl_integrate');
add_action('load-edit-pages.php', 'mgbl_integrate');

function mgbl_integrate() {
  global $pagenow;

  define('MGBL_PATH', dirname(__FILE__).'/');
  define('MGBL_URL', get_settings('siteurl').'/'.str_replace('\\', '/', substr(MGBL_PATH, strpos(MGBL_PATH, 'wp-content'))));
  
  if( mgbl_is_compatible() && ( 
      $pagenow == 'edit.php' && current_user_can('edit_posts') ||
      $pagenow == 'edit-pages.php' && current_user_can('edit_pages') ||
      strstr($_SERVER['PHP_SELF'], 'manageable.php'))) {
  
    mgbl_actions();
    wp_enqueue_script('jquery');
    add_action('admin_head', 'mgbl_head', 150);
  }
}

function mgbl_head($a) {
  echo '<link rel="stylesheet" type="text/css" href="'.MGBL_URL.'styles.css" />'."\n";
  echo '<script src="'.MGBL_URL.'jquery.dimensions.js" type="text/javascript" charset="utf-8"></script>'."\n";
  echo '<script src="'.MGBL_URL.'jquery.autocomplete.js" type="text/javascript" charset="utf-8"></script>'."\n";
  echo '<script src="'.MGBL_URL.'script.js" type="text/javascript" charset="utf-8"></script>'."\n";
  echo '<script type="text/javascript">var mgblUrl = "'.MGBL_URL.'";</script>'."\n";
}

function mgbl_column($columns) {
  $columns['edit'] = 'Edit';
  return $columns;
}

function mgbl_column_td($name) {
  global $post;
  if($name == 'edit' && (current_user_can('edit_post', $post->ID) || current_user_can('edit_page', $post->ID))) {
    if($_POST['action'] == 'edit') { ?>
    <div class="edit-column">
      <input type="button" id="save-<?php echo $post->ID ?>" class="button" value="Save" /><br />
      <a href="#" id="cancel-<?php echo $post->ID ?>">Cancel</a><br />
      <img src="<?php echo MGBL_URL ?>loading.gif" id="loading-<?php echo $post->ID ?>" style="display: none" />
    </div>
    <?php } else { ?>
    <div class="edit-column">
      <a href="#" id="edit-<?php echo $post->ID ?>" class="edit-link">Edit</a><br />
      <img src="<?php echo MGBL_URL ?>loading.gif" id="loading-<?php echo $post->ID ?>" style="display: none" />
    </div>
    <?php 
    }
  } 
}

function mgbl_is_compatible() {
  return get_bloginfo('version') >= 2.5;
}

function mgbl_actions() {
  if(!mgbl_is_compatible())
    return false;
    
  add_action('manage_posts_columns', 'mgbl_column');
  add_action('manage_posts_custom_column', 'mgbl_column_td');
  add_action('manage_pages_columns', 'mgbl_column');
  add_action('manage_pages_custom_column', 'mgbl_column_td');
}

//----------------------//
//--MAIN-FUNCTIONALITY--//
//----------------------//

function mgbl_save_row($data) {
  // merge the new data with the old
  $id = $data['post_ID'];
  $post = get_post($id, ARRAY_A);
  $data = array_merge($post, $data);
  
  if($data['post_type'] == 'post') {
    $data['post_category'] = explode(',', $data['categories']);
  }
  
  if($data['post_type'] == 'page') {
    if($data['page_private'] == 'private') $data['post_status'] = 'private';
  }
  
  if($data['comment_status'] != 'open') $data['comment_status'] = 'closed';
  if($data['ping_status'] != 'open')    $data['ping_status']    = 'closed';
  
  // rename
  $data['user_ID'] = $GLOBALS['user_ID'];
  $data['content'] = $data['post_content'];
  $data['excerpt'] = $data['post_excerpt'];
  $data['trackback_url'] = $data['to_ping'];
  $data['parent_id'] = $data['post_parent'];
  
  // update the post
  $_POST = $data;
  edit_post();
}

function mgbl_edit_row($ID, $type) { 
  global $post, $current_user;
  $GLOBALS['post'] = get_post($ID);
  $GLOBALS['post_ID'] = $ID;
  
  if( ($type == 'post' && !current_user_can('edit_post', $ID)) || 
      ($type == 'page' && !current_user_can('edit_page', $ID)) || 
      ($type != 'post' && $type != 'page'))
    return false;
  
  $columns = $type == 'post' ? wp_manage_posts_columns() : wp_manage_pages_columns();
  
  foreach($columns as $column_name=>$column_display_name) {

    switch($column_name) {

    case 'cb': ?>
      <td></td>
      <?php
      break;
      
    case 'modified':
    case 'date': ?>
      <td class="date">
        <?php if (current_user_can('publish_posts')): ?>
        <div id="date-<?php echo $post->ID ?>"><?php touch_time(1,1,4); ?></div>
        <?php else: ?>
        <?php echo get_the_modified_time(__('Y/m/d g:i:s A')); ?>
        <?php endif; ?>
      </td>
      <?php
      break;
      
    case 'title': ?>
      <td<?php if($type == 'page'): ?> class="page-title"<?php endif; ?>>
        <div class="title">
          <input type="text" id="title-<?php echo $post->ID ?>" value="<?php echo $post->post_title ?>" /><br />
          <label><?php _e('Slug'); ?></label><input type="text" id="slug-<?php echo $post->ID ?>" value="<?php echo $post->post_name ?>" class="slug" />
        </div>
        <?php if($type == 'page'): ?>
        <div class="other">
          <label><?php _e('Parent'); ?></label>
          <select id="parent-<?php echo $post->ID ?>">
            <option value="0"><?php _e('Main Page (no parent)'); ?></option>
            <?php parent_dropdown($post->post_parent); ?>
          </select><br />
          <label><?php _e('Template'); ?></label>
          <select id="template-<?php echo $post->ID ?>">
            <option value='default'><?php _e('Default Template'); ?></option>
            <?php page_template_dropdown(get_post_meta($post->ID, '_wp_page_template', true)) ?>
          </select>
        </div>
        <div class="more">
          <label><?php _e('Order'); ?></label><input type="text" id="order-<?php echo $post->ID ?>" value="<?php echo $post->menu_order ?>" />
          <label><?php _e('Password'); ?></label><input type="text" id="password-<?php echo $post->ID ?>" value="<?php echo $post->post_password ?>" />      
        </div>
        <?php endif; ?>
      </td>
      <?php
      break;
      
    case 'categories': ?>
      <td class="categories">
        <ul id="categories-<?php echo $post->ID ?>" class="categories">
          <?php get_bloginfo('version') == '2.5' ? dropdown_categories($post->ID) : wp_category_checklist($post->ID) ?>
        </ul>
      </td>
      <?php
      break;
      
    case 'tags': ?>
      <td class="tags">
        <textarea id="tags-<?php echo $post->ID ?>"><?php echo get_tags_to_edit( $post->ID ); ?></textarea>
      </td>
      <?php
      break;

    case 'comments': ?>
      <td class="comments num">
        <input title="Allow Comments" type="checkbox" id="comment-<?php echo $post->ID ?>" value="open"<?php if($post->comment_status == 'open'): ?> checked="checked"<?php endif; ?> /><br />
        <input title="Allow Pings" type="checkbox" id="ping-<?php echo $post->ID ?>" value="open"<?php if($post->ping_status == 'open'): ?> checked="checked"<?php endif; ?> />
      </td>
      <?php
      break;

    case 'author': ?>
      <td class="author">
        <?php
        $authors = get_editable_user_ids( $current_user->id ); // TODO: ROLE SYSTEM
        if ( $post->post_author && !in_array($post->post_author, $authors) )
          $authors[] = $post->post_author;
        if ( $authors && count( $authors ) > 1 ) {
          wp_dropdown_users( array('include' => $authors, 'name' => 'author-'.$post->ID, 'selected' => $post->post_author) ); 
        } else {
          echo $current_user->user_nicename.'<input type="hidden" value="'.$post->post_author.'" id="author-'.$post->ID.'" />';
        }
        ?>
      </td>
      <?php
      break;

    case 'status': ?>
      <td class="status">
        <select id='status-<?php echo $post->ID ?>'>
          <?php if ( current_user_can('publish_posts') ) : // Contributors only get "Unpublished" and "Pending Review" ?>
          <option<?php selected( $post->post_status, 'publish' ); selected( $post->post_status, 'private' );?> value='publish'><?php _e('Published') ?></option>
          <?php if ( 'future' == $post->post_status ) : ?>
          <option<?php selected( $post->post_status, 'future' ); ?> value='future'><?php _e('Scheduled') ?></option>
          <?php endif; ?>
          <?php endif; ?>
          <option<?php selected( $post->post_status, 'pending' ); ?> value='pending'><?php _e('Pending Review') ?></option>
          <option<?php selected( $post->post_status, 'draft' ); ?> value='draft'><?php _e('Unpublished') ?></option>
        </select><br />
        <?php if($type == 'page'): ?>
        <input id="private-<?php echo $post->ID ?>" type="checkbox" value="private" <?php checked($post->post_status, 'private'); ?> /> <label for="private-<?php echo $post->ID ?>"><?php _e('Private') ?></label></p>
        <?php endif; ?>
      </td>
      <?php
      break;

    case 'control_view': ?>
      <td><a href="<?php the_permalink(); ?>" rel="permalink" class="view"><?php _e('View'); ?></a></td>
      <?php
      break;

    case 'control_edit': ?>
      <td><?php if ( current_user_can('edit_post',$post->ID) ) { echo "<a href='post.php?action=edit&amp;post=$id' class='edit'>" . __('Edit') . "</a>"; } ?></td>
      <?php
      break;

    case 'control_delete': ?>
      <td><?php if ( current_user_can('delete_post',$post->ID) ) { echo "<a href='" . wp_nonce_url("post.php?action=delete&amp;post=$id", 'delete-post_' . $post->ID) . "' class='delete'>" . __('Delete') . "</a>"; } ?></td>
      <?php
      break;

    default: ?>
      <td><?php do_action('manage_posts_custom_column', $column_name, $post->ID); ?></td>
      <?php
      break;
    }
  }
}

function mgbl_get_row($ID, $type) {
  global $post;

  if($type == 'page' && current_user_can('edit_page', $ID)) {
    $posts = query_posts('page_id='.$ID);

    // capture it so we can strip the tr tags
    ob_start();
    page_rows($posts);
    echo preg_replace('/<\/?tr[^>]*>/iU', '', ob_get_clean());
  }

  if($type == 'post' && current_user_can('edit_post', $ID)) {
    query_posts('p='.$ID);

    /*  This code is taken from edit-post-rows.php. For some reason the post rows aren't encapsulated 
        in a single function like the pages so I had to resort to copy and pasting the code.  Therefore 
        this could break if WP makes major changes to the posts table.  If anybody has any better ideas
        please let me know. */

    $posts_columns = wp_manage_posts_columns();  
    while (have_posts()) : the_post();
    global $current_user;
    $post_owner = ( $current_user->ID == $post->post_author ? 'self' : 'other' );
    $title = get_the_title();
    if ( empty($title) )
      $title = __('(no title)');
    ?>

    <?php
    foreach($posts_columns as $column_name=>$column_display_name) {

      switch($column_name) {

      case 'cb':
        ?>
        <th scope="row" class="check-column"><?php if ( current_user_can( 'edit_post', $post->ID ) ) { ?><input type="checkbox" name="delete[]" value="<?php the_ID(); ?>" /><?php } ?></th>
        <?php
        break;
      case 'modified':
      case 'date':
        if ( '0000-00-00 00:00:00' == $post->post_date && 'date' == $column_name ) {
          $t_time = $h_time = __('Unpublished');
        } else {
          if ( 'modified' == $column_name ) {
            $t_time = get_the_modified_time(__('Y/m/d g:i:s A'));
            $m_time = $post->post_modified;
            $time = get_post_modified_time('G', true);
          } else {
            $t_time = get_the_time(__('Y/m/d g:i:s A'));
            $m_time = $post->post_date;
            $time = get_post_time('G', true);
          }
          if ( ( abs(time() - $time) ) < 86400 ) {
            if ( ( 'future' == $post->post_status) )
              $h_time = sprintf( __('%s from now'), human_time_diff( $time ) );
            else
              $h_time = sprintf( __('%s ago'), human_time_diff( $time ) );
          } else {
            $h_time = mysql2date(__('Y/m/d'), $m_time);
          }
        }
        ?>
        <td><abbr title="<?php echo $t_time ?>"><?php echo apply_filters('post_date_column_time', $h_time, $post, $column_name) ?></abbr></td>
        <?php
        break;
      case 'title':
        ?>
        <td><strong><?php if ( current_user_can( 'edit_post', $post->ID ) ) { ?><a class="row-title" href="post.php?action=edit&amp;post=<?php the_ID(); ?>" title="<?php echo attribute_escape(sprintf(__('Edit "%s"'), $title)); ?>"><?php echo $title ?></a><?php } else { echo $title; } ?></strong>
        <?php if ( !empty($post->post_password) ) { _e(' &#8212; <strong>Protected</strong>'); } elseif ('private' == $post->post_status) { _e(' &#8212; <strong>Private</strong>'); } ?></td>
        <?php
        break;

      case 'categories':
        ?>
        <td><?php
        $categories = get_the_category();
        if ( !empty( $categories ) ) {
          $out = array();
          foreach ( $categories as $c )
            $out[] = "<a href='edit.php?category_name=$c->slug'> " . wp_specialchars(sanitize_term_field('name', $c->name, $c->term_id, 'category', 'display')) . "</a>";
          echo join( ', ', $out );
        } else {
          _e('Uncategorized');
        }
        ?></td>
        <?php
        break;

      case 'tags':
        ?>
        <td><?php
        $tags = get_the_tags();
        if ( !empty( $tags ) ) {
          $out = array();
          foreach ( $tags as $c )
            $out[] = "<a href='edit.php?tag=$c->slug'> " . wp_specialchars(sanitize_term_field('name', $c->name, $c->term_id, 'post_tag', 'display')) . "</a>";
          echo join( ', ', $out );
        } else {
          _e('No Tags');
        }
        ?></td>
        <?php
        break;

      case 'comments':
        ?>
        <td class="num"><div class="post-com-count-wrapper">
        <?php
        $left = isset($comment_pending_count) ? $comment_pending_count[$post->ID] : 0;
        $pending_phrase = sprintf( __('%s pending'), number_format( $left ) );
        if ( $left )
          echo '<strong>';
        comments_number("<a href='edit.php?p=$id' title='$pending_phrase' class='post-com-count'><span class='comment-count'>" . __('0') . '</span></a>', "<a href='edit.php?p=$id' title='$pending_phrase' class='post-com-count'><span class='comment-count'>" . __('1') . '</span></a>', "<a href='edit.php?p=$id' title='$pending_phrase' class='post-com-count'><span class='comment-count'>" . __('%') . '</span></a>');
        if ( $left )
          echo '</strong>';
        ?>
        </div></td>
        <?php
        break;

      case 'author':
        ?>
        <td><a href="edit.php?author=<?php the_author_ID(); ?>"><?php the_author() ?></a></td>
        <?php
        break;

      case 'status':
        ?>
        <td>
        <a href="<?php the_permalink(); ?>" title="<?php echo attribute_escape(sprintf(__('View "%s"'), $title)); ?>" rel="permalink">
        <?php
        switch ( $post->post_status ) {
          case 'publish' :
          case 'private' :
            _e('Published');
            break;
          case 'future' :
            _e('Scheduled');
            break;
          case 'pending' :
            _e('Pending Review');
            break;
          case 'draft' :
            _e('Unpublished');
            break;
        }
        ?>
        </a>
        </td>
        <?php
        break;

      case 'control_view':
        ?>
        <td><a href="<?php the_permalink(); ?>" rel="permalink" class="view"><?php _e('View'); ?></a></td>
        <?php
        break;

      case 'control_edit':
        ?>
        <td><?php if ( current_user_can('edit_post',$post->ID) ) { echo "<a href='post.php?action=edit&amp;post=$id' class='edit'>" . __('Edit') . "</a>"; } ?></td>
        <?php
        break;

      case 'control_delete':
        ?>
        <td><?php if ( current_user_can('delete_post',$post->ID) ) { echo "<a href='" . wp_nonce_url("post.php?action=delete&amp;post=$id", 'delete-post_' . $post->ID) . "' class='delete'>" . __('Delete') . "</a>"; } ?></td>
        <?php
        break;

      default:
        ?>
        <td><?php do_action('manage_posts_custom_column', $column_name, $post->ID); ?></td>
        <?php
        break;
      }
    }
    ?>
    <?php
    endwhile;
  }
}

?>