<?php
// $Id: drutex_example.inc,v 1.2 2006/09/06 02:25:41 dfg Exp $

/**
 * @file
 * Just an example DruTeX submodule.
 *
 * For developers.
 */

/**
 * Implementation of subhook_info().
 */
function drutex_example_info($format = -1) {
  return (object) array(
    'title' => t('Exampe submodule'),
    'description' => t('Provides some senseless stuff.'),
    'toggle' => true, /* tells drutex if the user can activate/deactivate this submodule */
    'weight' => 20 /* helps ordering the fieldsets on the configuration page */
  );
}

/**
 * Implementation of subhook_defaults().
 */
function drutex_example_defaults() {
  $D['drutex_blahtex_active'] = false;
  $D['drutex_example_sometext'] = 'This is the default text for drutex_example_sometext_$format.';

  return $D;
}

/**
 * Implementation of subhook_node2html().
 */
function drutex_example_node2html() {
  $E = array();

  /* blabla{hello world} => <xyz>hellp world</xyz> */
  $E[] = (object) array(
      'type' => 'latex-command',
      'pattern' => 'blabla',
      'replacement' => '<xyz>$1</xyz>',
    );

  /* abcde => edcba */
  $E[] = (object) array(
      'pattern' => '@abcde@s',
      'replacement' => 'edcba',
      'weight' => 100,
      /* type is regex! */
    );

  return $E;
}

/**
 * Implementation of subhook_filter_settings().
 */
function drutex_example_filter_settings($format = -1) {
  $form["drutex_example_sometext_$format"] = array(
    '#type' => 'textarea',
    '#title' => t('Some text'),
    '#rows' => 4,
    '#cols' => 50,
    '#default_value' => drutex_var_get("drutex_example_sometext_$format")
  );

  return $form;
}

/**
 * Implementation of (sub)hook_perm().
 */
function drutex_example_perm() {
  return array('access examples');
}
