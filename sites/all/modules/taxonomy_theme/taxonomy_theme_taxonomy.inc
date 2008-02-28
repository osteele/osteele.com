<?php
// $Id: taxonomy_theme_taxonomy.inc,v 1.4 2006/10/18 10:01:56 profix898 Exp $

define('TAXOTHEME_SKIP',     0);
define('TAXOTHEME_TERM',     1);
define('TAXOTHEME_VOCAB',    2);
define('TAXOTHEME_ALLTAXO',  3);
define('TAXOTHEME_NODETYPE', 4);

/**
 * function _taxonomy_theme_select_forum().
 * (assign theme to forum related nodes and comments)
 */
function _taxonomy_theme_select_forum(&$theme_method, &$custom_theme, $default_theme, $uri) {
  // theme forum dependent pages/nodes
  $forum_vid = variable_get('forum_nav_vocabulary', '');
  $theme_forums = (module_exists('forum') && variable_get('taxonomy_theme_forums_enable', 0));
  $theme_forum_dep = ($theme_forums && variable_get('taxonomy_theme_forum_dep', 1));
  if ($theme_forum_dep) {
    if (preg_match('#^node/add/forum/#', $uri)) {
      $fid = basename($uri);
      $custom_theme = variable_get('taxonomy_theme_forum|'.$fid, $default_theme);
      $theme_method = TAXOTHEME_SKIP;
    }
    if (preg_match('#comment/reply/([0-9]*).*#', $uri, $matches)) {
      $term = db_fetch_object(db_query('SELECT tid FROM {forum} WHERE nid = %d', $matches[1]));
      if ($term) {
        $custom_theme = variable_get('taxonomy_theme_forum|'.$term->tid, $default_theme);
        $theme_method = TAXOTHEME_SKIP;
      }
    }
    if (preg_match('#comment/(delete|edit)/([0-9]*).*#', $uri, $matches)) {
      $term = db_fetch_object(db_query('SELECT f.tid FROM {forum} f INNER JOIN {comments} c ON f.nid = c.nid WHERE c.cid = %d', $matches[2]));
      if ($term) {
        $custom_theme = variable_get('taxonomy_theme_forum|'.$term->tid, $default_theme);
        $theme_method = TAXOTHEME_SKIP;
      }
    }
  }
}

/**
 * function _taxonomy_theme_select_term().
 * (term-based theme selection)
 */
function _taxonomy_theme_select_term(&$theme_method, &$custom_theme, $default_theme, $uri) {
  $selector_vid = variable_get('taxonomy_theme_selector', '');
  if ((arg(0) == 'node') && is_numeric(arg(1))) {
    $nid = arg(1);
    $terms = taxonomy_node_get_terms_by_vocabulary($nid, $selector_vid);
    if ($terms) {
      $term = current($terms);
      $custom_theme = variable_get('taxonomy_theme_term|'.$term->tid, $default_theme);
    }
    else if ($theme_forum_dep) {
      $term = db_fetch_object(db_query('SELECT tid FROM {forum} WHERE nid = %d', $nid));
      $custom_theme = variable_get('taxonomy_theme_forum|'.$term->tid, $default_theme);
    }
  }
  else if ((arg(0) == 'taxonomy_menu') && is_numeric(arg(1)) && arg(2)) {
    $vid = arg(1);
    $tid = basename($uri);
    if ($theme_forums && ($vid == $forum_vid)) {
      $custom_theme = variable_get('taxonomy_theme_forum|'.$tid, $default_theme);
    }
    else if ($vid == $selector_vid) {
      $custom_theme = variable_get('taxonomy_theme_term|'.$tid, $default_theme);
    }
  }
  else if ((arg(0) == 'taxonomy') && (arg(1) == 'term')) {
    if (is_numeric(arg(2))) {
      $tid = arg(2);
      $tobj = taxonomy_get_term($tid);
      if ($tobj->vid == $selector_vid) {
        $custom_theme = variable_get('taxonomy_theme_term|'.$tid, $default_theme);
      }
      else if ($tobj->vid == $forum_vid) {
        $custom_theme = variable_get('taxonomy_theme_forum|'.$tid, $default_theme);
      }
    }
  }
  else if ((arg(0) == 'forum') && is_numeric(arg(1)) && $theme_forums) {
    $tid = arg(1);
    $custom_theme = variable_get('taxonomy_theme_forum|'.$tid, $default_theme);
  }
}

/**
 * function _taxonomy_theme_select_vocabulary().
 * (vocabulary-based theme selection)
 */
function _taxonomy_theme_select_vocabulary(&$theme_method, &$custom_theme, $default_theme, $uri) {
  if ((arg(0) == 'node') && is_numeric(arg(1))) {
    $nid = arg(1);
    $terms = taxonomy_node_get_terms($nid);
    $term = current($terms);
    $vid = $term->vid;
    $custom_theme = variable_get('taxonomy_theme_vocab|'.$vid, $default_theme);
    if ($theme_forum_dep && ($vid == $forum_vid)) {
      $term = db_fetch_object(db_query('SELECT tid FROM {forum} WHERE nid = %d', $nid));
      $custom_theme = variable_get('taxonomy_theme_forum|'.$term->tid, $default_theme);
    }
  }
  else if ((arg(0) == 'taxonomy_menu') && is_numeric(arg(1))) {
    $vid = arg(1);
    if ($theme_forums && ($vid == $forum_vid) && arg(2)) {
      $tid = basename($uri);
      $custom_theme = variable_get('taxonomy_theme_forum|'.$tid, $default_theme);
    }
    else {      
      $custom_theme = variable_get('taxonomy_theme_vocab|'.$vid, $default_theme);
    }
  }
  else if ((arg(0) == 'taxonomy')) {
    if (is_numeric(arg(2))) {
      $tid = arg(2);
      $tobj = taxonomy_get_term($tid);
      $vid = $tobj->vid;
      if ($theme_forums && ($vid == $forum_vid)) {
        $custom_theme = variable_get('taxonomy_theme_forum|'.$tid, $default_theme);
      }
      else {      
        $custom_theme = variable_get('taxonomy_theme_vocab|'.$vid, $default_theme);
      }
    }
  }
  else if ((arg(0) == 'forum') && is_numeric(arg(1)) && $theme_forums) {
    $tid = arg(1);
    $custom_theme = variable_get('taxonomy_theme_forum|'.$tid, $default_theme);
  }
}

/**
 * function _taxonomy_theme_select_alltaxonomy().
 * (all-taxonomy theme selection = term-based + vocab-based)
 */
function _taxonomy_theme_select_alltaxonomy(&$theme_method, &$custom_theme, $default_theme, $uri) {
  if ((arg(0) == 'node') && is_numeric(arg(1))) {
    $nid = arg(1);
    $terms = taxonomy_node_get_terms($nid);
    $term = current($terms);
    $vid = $term->vid;
    $custom_theme = variable_get('taxonomy_theme_all|'.$term->tid, $default_theme);
    if ($theme_forum_dep && ($vid == $forum_vid)) {
      $term = db_fetch_object(db_query('SELECT tid FROM {forum} WHERE nid = %d', $nid));
      $custom_theme = variable_get('taxonomy_theme_forum|'.$term->tid, $default_theme);
    }
  }
  else if ((arg(0) == 'taxonomy_menu') && is_numeric(arg(1)) && arg(2)) {
    $vid = arg(1);
    $tid = basename($uri);
    if ($theme_forums && ($vid == $forum_vid)) {
      $custom_theme = variable_get('taxonomy_theme_forum|'.$tid, $default_theme);
    }
    else {
      $custom_theme = variable_get('taxonomy_theme_all|'.$tid, $default_theme);
    }
  }
  else if ((arg(0) == 'taxonomy')) {
    if (is_numeric(arg(2))) {
      $tid = arg(2);
      $tobj = taxonomy_get_term($tid);
      $custom_theme = variable_get('taxonomy_theme_all|'.$tid, $default_theme);
      if ($tobj->vid == $forum_vid) {
        $custom_theme = variable_get('taxonomy_theme_forum|'.$tid, $default_theme);
      }
    }
  }
  else if ((arg(0) == 'forum') && is_numeric(arg(1)) && $theme_forums) {
    $tid = arg(1);
    $custom_theme = variable_get('taxonomy_theme_forum|'.$tid, $default_theme);
  }
}

/**
 * function _taxonomy_theme_select_nodetype().
 * (nodetype-based theme selection)
 */
function _taxonomy_theme_select_nodetype(&$theme_method, &$custom_theme, $default_theme, $uri) {
  if ((arg(0) == 'node') && is_numeric(arg(1))) {
    $nid = arg(1);
    $nodetype = db_fetch_object(db_query('SELECT type FROM {node} WHERE nid = %d', $nid));
    if ($nodetype->type) {
      $custom_theme = variable_get('taxonomy_theme_nodetype|'.$nodetype->type, $default_theme);
      if (($nodetype->type == 'forum topic') && $theme_forum_dep) {
        $term = db_fetch_object(db_query('SELECT tid FROM {forum} WHERE nid = %d', $nid));
        $custom_theme = variable_get('taxonomy_theme_forum|'.$term->tid, $default_theme);
      }
    }
  }
  else if ((arg(0) == 'forum') && is_numeric(arg(1)) && $theme_forums) {
    $tid = arg(1);
    $custom_theme = variable_get('taxonomy_theme_forum|'.$tid, $default_theme);
  }
}

?>
