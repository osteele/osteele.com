<?php
// $Id: drutex_numbering.inc,v 1.3 2007/04/25 14:46:28 darthsteven Exp $

/**
 * @file
 * Assists automatic numbering of tex, equation und equations environments.
 *
 * Note that a missing subhook_info() means that this submodule cannot be disabled (without hacking).
 */

/**
 * Implementation of subhook_info().
 */
function drutex_numbering_info($format = -1) {
  return (object) array(
    'title' => t('Numbering / Referencing'),
    'description' => t('Assists automatic numbering of tex, equation und equations environments.'),
    'toggle' => false,
    'weight' => 20
  );
}

/**
 * Implementation of subhook_defaults().
 */
function drutex_numbering_defaults() {
  $D['drutex_numbering_active'] = true;
  return $D;
}

/**
 * Implementation of subhook_node2html().
 */
function drutex_numbering_node2html() {
  $E = array();

  $E[] = (object) array(
      'pattern' => '@<(equation|equations)( [^>]*)>@se',
      'replacement' => "'<$1 ' . _drutex_numbering_rewrite_attributes(_drutex_unescape('$2')) . '>'",
      'weight' => -90 /* very early, but after drutex_verbatim.inc */
    );

  $E[] = (object) array(
      'pattern' => '@\(\\\\ref\{(.+?)\}\)@se',
      'replacement' => "drutex_reference_link($1, '(', ')')",
      'weight' => 90 /* very late */
    );

  $E[] = (object) array(
      'pattern' => '@\\\\ref\{(.+?)\}@se',
      'replacement' => "drutex_reference_link($1)",
      'weight' => 91 /* very late */
    );

  return $E;
}

function _drutex_numbering_rewrite_attributes($attribute_string) {
  static $num = 1;
  $attributes = drutex_parse_attributes($attribute_string);

  if (isset($attributes['id'])) {
    /* if id is set, but no name, make the name implicitly a number */
    if (empty($attributes['name'])) {
      $attributes['name'] = _drutex_numbers('inc counter');
    }

    /* save the id/name pair */
    _drutex_references('set', $attributes['id'], $attributes['name']);
  }

  return drutex_merge_attributes($attributes);
}

function _drutex_numbers($action) {
  /* for automatic, continuous numbering */
  static $num = 0;

  switch ($action) {
    case 'inc counter':
      return ++$num;
    case 'get conuter':
      return $num;
  }
}

function _drutex_references($action, $id = '', $name = '') {
  /* cache id's for \ref{id} */
  static $id_cache = array();

  switch ($action) {
    case 'get name by id':
      return $id_cache[$id];

    case 'get array size':
      return count($id_cache);

    case 'set':
      $id_cache[$id] = $name;
  }
}

function drutex_reference_link($id, $prepend = '', $append = '') {
  return "<a href=\"#$id\">{$prepend}". _drutex_references('get name by id', $id) ."{$append}</a>";
}
