<?php
// $Id: drutex_linebreak.inc,v 1.9 2007/04/25 14:46:28 darthsteven Exp $

/**
 * @file
 * Provide line-breaking algorithms for DruTeX.
 *
 * There is currently the choice between Drupal-style linebreaking and
 * LaTeX-style linebreaking.
 */

/**
 * Implementation of subhook_info().
 */
function drutex_linebreak_info($format = -1) {
  return (object) array(
    'title' => t('Line break converter'),
    'description' => check_plain(t('Converts line breaks into HTML (i.e. <br /> and <p> tags).')),
    'toggle' => true,
    'weight' => 12
  );
}

/**
 * Implementation of subhook_defaults().
 */
function drutex_linebreak_defaults() {
  $D['drutex_linebreak_active'] = true;
  $D['drutex_linebreak_behaviour'] = 'drupal';

  return $D;
}

/**
 * Implementation of subhook_node2html_postprocess().
 */
function drutex_linebreak_node2html_postprocess($text, $format = -1) {
  $method = drutex_var_get("drutex_linebreak_behaviour_$format");

  if ($method == 'drupal') {
    $text =_filter_autop($text);
  }
  else if ($method == 'latex') {
    $text = preg_replace('/(?<!\n)\n(?!\n)/s', ' ', $text);
    $text = _filter_autop($text);
  }

  return $text;
}

/**
 * Implementation of subhook_filter_settings().
 */
function drutex_linebreak_filter_settings($format = -1) {
  $choices = array('drupal' => 'Drupal style',
                   'latex'  => 'LaTeX style');

  $form["drutex_linebreak_behaviour_$format"] = array(
    '#type' => 'select',
    '#title' => t('Linebreaking Method'),
    '#options' => $choices,
    '#default_value' => drutex_var_get("drutex_linebreak_behaviour_$format"),
    '#description' => t('How to handle new and empty lines in your input.')
  );

  return $form;
}
