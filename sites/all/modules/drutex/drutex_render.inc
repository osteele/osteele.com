<?php
// $Id: drutex_render.inc,v 1.22.2.1 2008/01/30 13:19:46 darthsteven Exp $

/**
 * @file
 * Provide environments to render maths and/or arbitrary TeX.
 */

/**
 * Implementation of subhook_info().
 */
function drutex_render_info($format = -1) {
  return (object) array(
    'title' => t('LaTeX Renderer'),
    'description' => t('Provides different environments to create rendered images (especially maths).'),
    'toggle' => true,
    'weight' => 0
  );
}

/**
 * Implementation of subhook_defaults().
 */
function drutex_render_defaults() {
  $D['drutex_render_active'] = true;

  /* relative to drutex/templates/render */
  $D['drutex_template_render'] = 'default.tex';

  $D['drutex_pattern_render_dvipng']       = 'cd [TMP_DIR]; TEXINPUTS="[DRUTEX_DIR]//:" latex -interaction=batchmode [HASH].tex'."\n".
                                      'dvipng -o [IMG_FILE] -D [DPI] -T tight [TMP_DIR]/[HASH].dvi';

  $D['drutex_pattern_render_imagemagick']  = 'cd [TMP_DIR]; TEXINPUTS="[DRUTEX_DIR]//:" latex -interaction=batchmode [HASH].tex'."\n".
                                      'convert -density [DPI] -trim [TMP_DIR]/[HASH].dvi [IMG_FILE]';

  return $D;
}

/**
 * Implementation of subhook_node2html_preprocess()
 *
 * We grab all the data destined for Drutex, and cache the render results,
 * replacing the source with a custom token
 */
function drutex_render_node2html_preprocess($text, $format) {

  $patterns = array();
  $replacements = array();

  /* Plain TeX environment */
  $patterns[] = '@<tex( [^>]*)?>(.+?)</tex>@se';
  $replacements[] = "_drutex_render_escape('$2', '$format', '$1', 'tag=\"span\" class=\"plaintex\"')";

  /* <equation> environment */
  $patterns[] ='@<equation( [^>]*)?>(.+?)</equation>@se';
  $replacements[] = "_drutex_render_escape('\[ $2 \]', '$format', '$1', 'tag=\"table\" class=\"displaymath\"')";

  /* <equations> environment */
  $patterns[] = '@<equations( [^>]*)?>(.+?)</equations>@se';
  $replacements[] = "_drutex_render_escape('\begin{align*} $2 \end{align*}', '$format', '$1', 'tag=\"table\" class=\"displaymath\"')";

  /* Paragraph math between $$ and $$ */
  $patterns[] = '@(^|[^\\\\])(\$\$.*?[^\\\\]\$\$)@se';
  $replacements[] = "'$1' . _drutex_render_escape('$2', '$format', '', 'tag=\"table\" class=\"displaymath\"')";

  /* Paragraph math between \[ and \] */
  $patterns[] = '@\\\\\\[(.+?)\\\\\\]@se';
  $replacements[] = "_drutex_render_escape('$0', '$format', '', 'tag=\"table\" class=\"displaymath\"')";

  /* Inline math (display style) between $! and $ */
  $patterns[] = '@(^|[^\\\\])\$!(.*?[^\\\\])\$@se';
  $replacements[] = "'$1' . _drutex_render_escape('$\displaystyle $2 $', '$format')";

  /* Inline math between $ and $ */
  $patterns[] = '@(^|[^\\\\])\$(.*?[^\\\\])\$@se';
  $replacements[] = "'$1' . _drutex_render_escape('$ $2 $', '$format')";





  $text = preg_replace($patterns, $replacements, $text);

  return $text;
}

function _drutex_render_escape($text, $format = -1, $attributes = '', $default_attributes = '') {
  // Render the data and cache the result in a static array:
  $rendered = drutex_render($text, $format, $attributes, $default_attributes);
  $index = _drutex_render_static_cache('write', $rendered);
  return "[drutex_render]{$index}[/drutex_render]";
}

function _drutex_render_static_cache($op, $data = NULL) {
  static $max_index = 0;
  static $storage = array();
  switch ($op) {
    case 'write':
      $max_index++;
      $storage[$max_index] = $data;
      return $max_index;
    case 'read':
    default:
      return $storage[$data];
  }
}
/**
 * Implementation of subhook_node2html().
 */
function drutex_render_node2html() {
  $E = array();

  /* Plain TeX environment */
/*  $E[] = (object) array(
      'pattern' => '@<tex( [^>]*)?>(.+?)</tex>@se',
      'replacement' => "drutex_render('$2', '<var>format</var>', '$1', 'tag=\"span\" class=\"plaintex\"')",
      'weight' => -10
    );*/

  /* <equation> environment */
  /*$E[] = (object) array(
      'pattern' => '@<equation( [^>]*)?>(.+?)</equation>@se',
      'replacement' => "drutex_render('\[ $2 \]', '<var>format</var>', '$1', 'tag=\"table\" class=\"displaymath\"')",
      'weight' => -7
    );*/

  /* <equations> environment */
  /*$E[] = (object) array(
      'pattern' => '@<equations( [^>]*)?>(.+?)</equations>@se',
      'replacement' => "drutex_render('\begin{align*} $2 \end{align*}', '<var>format</var>', '$1', 'tag=\"table\" class=\"displaymath\"')",
      'weight' => -6
    );*/

  /* Paragraph math between $$ and $$ */
  /*$E[] = (object) array(
      'pattern' => '@(^|[^\\\\])(\$\$.*?[^\\\\]\$\$)@se',
      'replacement' => "'$1' . drutex_render('$2', '<var>format</var>', '', 'tag=\"table\" class=\"displaymath\"')",
      'weight' => -5
    );*/

  /* Paragraph math between \[ and \] */
  /*$E[] = (object) array(
      'pattern' => '@\\\\\\[(.+?)\\\\\\]@se',
      'replacement' => "drutex_render('$0', '<var>format</var>', '', 'tag=\"table\" class=\"displaymath\"')",
      'weight' => -5
    );*/

  /* Inline math (display style) between $! and $ */
  /*$E[] = (object) array(
      'pattern' => '@(^|[^\\\\])\$!(.*?[^\\\\])\$@se',
      'replacement' => "'$1' . drutex_render('$\displaystyle $2 $', '<var>format</var>')",
      'weight' => -4
    );*/

  /* Inline math between $ and $ */
  /*$E[] = (object) array(
      'pattern' => '@(^|[^\\\\])\$(.*?[^\\\\])\$@se',
      'replacement' => "'$1' . drutex_render('$ $2 $', '<var>format</var>')",
      'weight' => -3
    );
  */
  /* unescape escaped dollar signs */
  $E[] = (object) array(
      'pattern' => '@\\\\\\$@s',
      'replacement' => '$',
      'weight' => 1000 /*  very late */
    );

  $E[] = (object) array(
      'pattern' => '@\[drutex_render\](\d+)\[/drutex_render\]@se',
      'replacement' => "_drutex_render_static_cache('read','$1')",
      'weight' => -3
  );

  return $E;
}

/**
 * Implementation of subhook_filter_settings().
 */
function drutex_render_filter_settings($format = -1) {
  /* conversion methods */
  $conversions = array('dvipng'        => 'dvipng',
                       'imagemagick'   => 'ImageMagick (convert)',
                       'custom'        => 'custom');

  $form["drutex_template_render_$format"] = array(
      '#type' => 'select',
      '#title' => t('Template'),
      '#options' => _drutex_get_templates(),
      '#default_value' => drutex_var_get("drutex_template_render_$format")
  );

  $form["drutex_dpi_render_$format"] = array(
    '#type' => 'textfield',
    '#title' => t('Resolution (DPI)'),
    '#size' => 15,
    '#maxlength' => 3,
    '#default_value' => drutex_var_get("drutex_dpi_render_$format"),
    '#description' => t('100 is small, 107 is default, 115 is big')
  );

  $form["drutex_conversion_render_$format"] = array(
    '#type' => 'select',
    '#title' => t('Conversion Method'),
    '#options' => $conversions,
    '#default_value' => drutex_var_get("drutex_conversion_render_$format")
  );


  /* Custom Conversion Method */

  /* determine default value for <custom conversion method> */
  $default_value = drutex_var_get("drutex_pattern_render_custom_$format");
  if (empty($default_value)) {
    /* get selected conversion method */
    $conversion = drutex_var_get("drutex_conversion_render_$format");
    $default_value = drutex_var_get("drutex_pattern_render_{$conversion}_$format");
  }

  /* description for <custom conversion method> */
  $description = t('* Leave blank and select dvipng or ImageMagick, to display a template for one of these commands in this box.') . '<br />'
    . t('* Every line is executed in sequence, but in its own shell (so path changes aren\'t inherited).') . '<br />'
    . t('Placeholders available:<br />
        [HASH] - hash (name) for the image<br />
        [TMP_DIR] - temporary dir (automatically cleaned)<br />
        [IMG_DIR] - directory where the images are saved<br />
        [IMG_TYPE] - png in most cases<br />
        [DPI] - dots per inch (see above)<br />
        [IMG_FILE] - abbreviation for [IMG_DIR]/[HASH].[IMG_TYPE]<br />
        [DRUTEX_DIR] - full path to DruTeX base dir');


  $form["drutex_pattern_render_custom_$format"] = array(
    '#type' => 'textarea',
    '#title' => t('Conversion Method (Custom)'),
    '#rows' => 3,
    '#cols' => 50,
    '#default_value' => $default_value,
    '#description' => $description
  );

  return $form;
}

/**
 * Renders $text in LaTeX.
 */
function drutex_render($text, $format = -1, $attributes = '', $default_attributes = '') {

  $text = _drutex_unescape($text);

  if (drutex_submodule_is_active('security', $format)) {
    if (drutex_security($text, $format) == false) {
      return '<em class="error">Unallowed command detected!</em>';
    }
  }

  $attributes = drutex_parse_attributes(_drutex_unescape($attributes));
  $default_attributes = drutex_parse_attributes($default_attributes);
  $attributes = array_merge($default_attributes, $attributes);

  $image_dir = drutex_var_get("drutex_dir_images_$format");

  $template_dir = drutex_var_get('drutex_dir') . '/templates/render';
  $template_file = drutex_var_get("drutex_template_render_$format");
  $template = $template_dir . '/' . $template_file;

  $dpi = drutex_var_get("drutex_dpi_render_$format");

  if (!empty($attributes['dpi']) AND is_numeric($attributes['dpi']) AND $attributes['dpi'] > 0) {
    $dpi = $attributes['dpi'];
  }

  $image_type = drutex_var_get("drutex_imagetype_$format");

  $hash = sha1($text . $displaystyle . $template . $dpi);
  $image_file = "$image_dir/$hash.$image_type";


  $success = TRUE;

  if (!is_file($image_file)) {
    /* create temporary dir */
    $temporary_dir = _drutex_create_temporary_dir();

    /* conversion method */
    $conversion = drutex_var_get("drutex_conversion_render_$format");

    /* command patterns */
    $pattern_convert = drutex_var_get("drutex_pattern_render_{$conversion}_$format");

    /* replacement arrays */
    $map = array('[HASH]' => $hash, '[TMP_DIR]' => $temporary_dir, '[IMG_FILE]' => $image_file,
      '[IMG_DIR]' => $image_dir, '[IMG_TYPE]' => $image_type, '[DPI]' => $dpi,
      '[DRUTEX_DIR]' => drutex_var_get('drutex_dir'));

    /* conversion commands (seperated by \n) */
    $cmd_convert = str_replace(array_keys($map), $map, $pattern_convert);

    /* load the appropriate template ... */
    $handle = fopen($template, 'r');
    $content = fread($handle, filesize($template));
    fclose($handle);

    /* ... and put the $text in there */
    $content = str_replace('DRUTEX_REPLACE', $text, $content);

    /* write $content to file, for LaTeX to read an render it */
    file_save_data($content, "$temporary_dir/$hash.tex", FILE_EXISTS_REPLACE);

    /* invoke all conversion commands */
    $commands = explode("\n", $cmd_convert);

    foreach ($commands as $cmd) {
      $cmd = trim($cmd);

      if ($cmd) {
        exec($cmd, $cmd_output, $cmd_retval);

        if (drutex_var_get("drutex_debug_$format")) {
          $level = ($cmd_retval == 0) ? WATCHDOG_NOTICE : WATCHDOG_WARNING;
          watchdog('DruTeX', "Command: $cmd<br />Return value: $cmd_retval", $level);
        }
      }
    }

    $success = is_file($image_file);

    /* remove temporary dir */
    if (!drutex_var_get("drutex_debug_$format")) {
      _drutex_delete_dir($temporary_dir);
    }
    else {
      if ($success) {
        watchdog('DruTeX', "$image_file was created. Temporary directory was $temporary_dir.", WATCHDOG_NOTICE);
      }
      else {
        watchdog('DruTeX', "$image_file couldn't be created. Temporary directory was $temporary_dir.", WATCHDOG_WARNING);
      }
    }
  }

  $img_url = drutex_get_image_url($format) . "/$hash.$image_type";

  if ($success) {
    $img_alt = _drutex_hide('set', check_plain($text));
    $res = "<img class=\"teximage\" src=\"$img_url\" alt=\"$img_alt\" />";
  }
  else {
    $res = '<em class="error">TeX Embedding failed!</em>';
  }

  if (!empty($attributes['tag'])) {
    $class_attr = empty($attributes['class']) ? '' : " class=\"{$attributes['class']}\"";

    if (!empty($attributes['id'])) {
      $id = $attributes['id'];
      $anchor = "<a name=\"$id\" id=\"$id\"></a>";
    }
    else {
      $anchor = '';
    }

    if ($attributes['tag'] == 'table') {
      $name = _drutex_references('get name by id', $attributes['id']);
      $name = empty($name) ? '' : "($name)";
      $res = "<table{$class_attr}><tr><td class=\"dspleft\">{$anchor}{$res}</td><td class=\"dspright\">{$name}</td></tr></table>";
    }
    else {
      $tag = $attributes['tag'];
      $res = "<{$tag}{$class_attr}>{$res}</{$tag}>";
    }
  }


  return $res;
}

/**
 * Get a list with templates for drutex_render.
 */
function _drutex_get_templates() {
  $dir = drupal_get_path('module', 'drutex') . '/templates/render';

  $A = file_scan_directory($dir, '.*\.tex');
  $B = array();

  foreach ($A as $key => $val) {
    $key = substr($key, strlen($dir)+1);
    $B[$key] = $key;
  }

  return $B;
}
