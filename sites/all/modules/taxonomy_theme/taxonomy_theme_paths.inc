<?php
// $Id: taxonomy_theme_paths.inc,v 1.1.2.1 2007/02/25 16:49:08 profix898 Exp $

/**
 * function _taxonomy_theme_select_paths().
 * (assign theme to Drupal paths and path aliases (incl. i18n support))
 */
function _taxonomy_theme_select_paths(&$custom_theme, $uri) {
  $offset  = (variable_get('clean_url', 0) ? 0 : 3) + strlen(base_path());
  $alias_uri = substr(request_uri(), $offset);
  if (module_exists('i18n')) {
    i18n_get_lang_prefix($alias_uri, true);
  }
  $alias_uri = $alias_uri ? $alias_uri : '/';
  $themes = list_themes();
  foreach ($themes as $themex) {
    $paths_regexp = _taxonomy_theme_expcache($themex->name);
    if (preg_grep($paths_regexp, array($uri, $alias_uri))) {
      $custom_theme = $themex->name;
    }
  }
}

/**
 * function _taxonomy_theme_expcache().
 * (build cacheable regexps from from paths table)
 */
function _taxonomy_theme_expcache($theme_name = NULL) {
  static $cache;
  if (!$theme_name) {
    $cache = array();
    return;
  }
  // build new regexps from table and put them into cache
  if (!isset($cache[$theme_name])) {
    $result = db_query("SELECT path FROM {theme_paths} WHERE theme = '%s'", $theme_name);
    if (db_num_rows($result)) {
      while ($path = db_fetch_object($result)) {
        $paths .= empty($paths) ? '' : '|';
        $paths .= str_replace(array('*', '/'), array('.*', '\/'), drupal_get_normal_path($path->path));
      }
    }
    $cache[$theme_name] = '/^('.$paths.')$/';
  }
  
  return $cache[$theme_name];
}

?>
