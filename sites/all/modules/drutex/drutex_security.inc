<?php
// $Id: drutex_security.inc,v 1.4 2006/09/06 04:20:08 dfg Exp $

/**
 * @file
 * Provide security features for DruTeX.
 *
 * This module covers functions to restrict the set of allowed
 * LaTeX commands and environments.
 */

/**
 * Implementation of subhook_info().
 */
function drutex_security_info($format = -1) {
  return (object) array(
    'title' => t('Security restrictions'),
    'description' => t('Restricts the set of allowed LaTeX commands.'),
    'toggle' => true,
    'weight' => 20
  );
}

/**
 * Implementation of subhook_defaults().
 */
function drutex_security_defaults() {
  $D['drutex_security_active'] = false;
  $D['drutex_security_allowedcommands'] = '\atop \binom \cdot \cfrac \choose \frac \int \ln \over \sum \to';
  $D['drutex_security_allowedenvironments'] = 'align array equation equations gather matrix split';

  return $D;
}

/**
 * Checks if $text only includes allowed commands/environments.
 */
function drutex_security($text, $format = -1) {
  static $allowed_commands = array();

  if (empty($allowed_commands[$format])) {
    $allowed_commands[$format] = explode(' ', drutex_var_get("drutex_security_allowedcommands_$format"));
    $allowed_environments = explode(' ', drutex_var_get("drutex_security_allowedenvironments_$format"));

    foreach ($allowed_environments as $env) {
      $allowed_commands[$format] = array_merge($allowed_commands[$format],
        array("\\begin\{$env}", "\\begin\{$env*}", "\\end\{$env}", "\\end\{$env*}"));
    }
  }

  $matches = array();
  preg_match_all('/(\\\\begin\{[a-zA-Z*]+\})|(\\\\end\{[a-zA-Z*]+\})|(\\\\[a-zA-Z]+)/s', $text, $matches);

  $commands = $matches[0];

  foreach ($commands as $command) {
    if (array_search($command, $allowed_commands[$format]) === false) {
      watchdog('DruTeX', "Unallowed command (by security submodule): $command", WATCHDOG_WARNING);
      return false;
    }
  }

  return true;
}

/**
 * Implementation of subhook_filter_settings().
 */
function drutex_security_filter_settings($format = -1) {
  $form["drutex_security_allowedcommands_$format"] = array(
    '#type' => 'textarea',
    '#title' => t('Allowed commands'),
    '#rows' => 4,
    '#cols' => 50,
    '#default_value' => drutex_var_get("drutex_security_allowedcommands_$format"),
    '#description' => t('Commands are seperated by single space.')
  );

  $form["drutex_security_allowedenvironments_$format"] = array(
    '#type' => 'textarea',
    '#title' => t('Allowed environments'),
    '#rows' => 4,
    '#cols' => 50,
    '#default_value' => drutex_var_get("drutex_security_allowedenvironments_$format"),
    '#description' => t('Environments are seperated by single space.')
  );

  return $form;
}

