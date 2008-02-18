<?php
// $Id: drutex_eukleides.inc,v 1.2 2006/09/06 02:25:41 dfg Exp $

/**
 * @file
 * Provide an Eukleides environment.
 *
 * http://www.eukleides.org/
 */

/**
 * Implementation of subhook_info().
 */
function drutex_eukleides_info($format = -1) {
  return (object) array(
    'title' => t('Eukleides'),
    'description' => t('Provides an Eukleides environment.'),
    'toggle' => true,
    'weight' => 16
  );
}

/**
 * Implementation of subhook_defaults().
 */
function drutex_eukleides_defaults() {
  $D['drutex_eukleides_active'] = false;
  return $D;
}

/**
 * Implementation of subhook_node2html().
 */
function drutex_eukleides_node2html() {
  $E = array();

  $E[] = (object) array(
      'pattern' => '@<eukleides([^>]*)>(.+?)</eukleides>@se',
      'replacement' => "drutex_eukleides('$2', '<var>format</var>', '$1')",
      'weight' => -10,
      'info' => t('Eukleides environment'),
      'id' => 'eukleides'
    );

  return $E;
}