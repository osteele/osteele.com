<?php
// $Id: drutex_blahtex.inc,v 1.2 2006/09/06 02:25:41 dfg Exp $

/**
 * @file
 * Provide environments to create MathML with the blahtex binary.
 *
 * Blahtex is a tool to convert LaTeXmaths to MathML, but - of course -
 * not every LaTeXmath command/environment is supported.
 */

/**
 * Implementation of subhook_info().
 */
function drutex_blahtex_info($format = -1) {
  return (object) array(
    'title' => t('blahtex (MathML)'),
    'description' => t('Provides math environments to create MathML with blahtex.'),
    'toggle' => true,
    'weight' => 16
  );
}

/**
 * Implementation of subhook_defaults().
 */
function drutex_blahtex_defaults() {
  $D['drutex_blahtex_active'] = false;

  return $D;
}

/**
 * Implementation of subhook_node2html().
 */
function drutex_blahtex_node2html() {
  $E = array();

  /* Inline math between $ and $ */
  $E[] = (object) array(
      'pattern' => '@(?<!\\\\)\$(.*?[^\\\\])\$@se',
      'replacement' => "drutex_blahtex('$1', '<var>format</var>')",
      'weight' => -20,
    );

  return $E;
}

/**
 * Passes $text to blahtex.
 */
function drutex_blahtex($text, $format = -1) {
  $text = _drutex_unescape($text);

  $text = shell_exec("echo \"$text\" | blahtex --mathml");

  $text = preg_replace('/\n/s', ' ', $text);

  return $text;
}

