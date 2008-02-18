<?php
// $Id: drutex_verbatim.inc,v 1.8 2006/09/06 02:25:41 dfg Exp $

/**
 * @file
 * Provide verbatim environments (these will be hidden by all further DruTeX parsing).
 *
 * Note that a missing subhook_info() means that this submodule cannot be disabled (without hacking).
 */

/**
 * Implementation of subhook_info().
 */
function drutex_verbatim_info($format = -1) {
  return (object) array(
    'title' => t('Numbering / Referencing'),
    'description' => t('Provide verbatim environments (these will be hidden by all further DruTeX parsing).'),
    'toggle' => false,
    'weight' => 20
  );
}

/**
 * Implementation of subhook_defaults().
 */
function drutex_verbatim_defaults() {
  $D['drutex_verbatim_active'] = true;
  return $D;
}

/**
 * Implementation of subhook_node2html().
 */
function drutex_verbatim_node2html() {
  $E = array();

  /* <code>*</code> */
  $E[] = (object) array(
      'pattern' => '@<code>(.+?)</code>@se',
      'replacement' => "_drutex_hide('set', '$0')",
      'weight' => -100,
    );

  /* <notex>*</notex> */
  $E[] = (object) array(
      'pattern' => '@<notex>(.+?)</notex>@se',
      'replacement' => "_drutex_hide('set', '$1')",
      'weight' => -100,
    );

  return $E;
}
