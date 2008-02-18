<?php
// $Id: template.php,v 1.5 2007/05/12 02:18:56 webchick Exp $

/**
 * @file
 * Template.php for Blue Zinfandel theme.
 */

/**
 * Define block regions; there's only one, so...
 */
function blue_zinfandel_regions() {
  return array(
    'left' => t('left sidebar'),
  );
}

/**
 * Override phptemplate_variables().
 */
function _phptemplate_variables($hook, $vars) {
  switch ($hook) {
    case 'node':
      $node = $vars['node'];

      // Variables for the calendar date display.
      $vars['month'] = date('M', $node->created);
      $vars['day'] = date('j', $node->created);

      // Separate out comment link.
      if (isset($node->links['comment_comments'])) {
        $vars['comment_link'] = l($node->links['comment_comments']['title'], "node/$node->nid", array('title' => t('Jump to the first comment of this posting.')), NULL, 'commentblock');
      }
      elseif (isset($node->links['comment_add'])) {
        $vars['comment_link'] = l(t('Add new comment'), "comment/reply/$node->nid", array('title' => t('Add a new comment to this page.')), NULL, 'comment-form');
      }

      // Kill comment links that are already added above.
      if (isset($node->links)) {
        $links = $node->links;
        if (isset($links['comment_comments'])) {
          unset($links['comment_comments']);
        }
        if (isset($links['comment_add'])) {
          unset($links['comment_add']);
        }
        $vars['links'] = theme('links', $links, array('class' => 'links inline'));
      }
      break;
  }

  return $vars;
}

/**
 * Override theme_comment_wrapper() to turn comments into ordered list. 
 */
function blue_zinfandel_comment_wrapper($content, $type = null) {
  $output  = '<div id="commentblock">';
//  $output .= '<h3 id="comments">X Responses to TITLE</h3>';
  $output .= '<ol class="commentlist">';
  $output .= $content;
  $output .= '</ol>';
  $output .= '</div>';

  return $output;
}

/**
 * Override for theme_textarea(); reduce size of comments field.
 */
function blue_zinfandel_textarea($element) {
  if ($element['#id'] == 'edit-comment') {
    $element['#cols'] = 50;
    $element['#rows'] = 10;
  }
  return theme_textarea($element);
}

