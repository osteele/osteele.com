<?php
// $Id: drutex_remote.inc,v 1.4 2008/01/09 12:33:53 darthsteven Exp $

/**
 * @file
 * Provide environments to remotely render maths and/or arbitrary TeX.
 */

/**
 * Implementation of subhook_info().
 */
function drutex_remote_info($format = -1) {
  return (object) array(
    'title' => t('Remote LaTeX Renderer'),
    'description' => t('Provides different environments to create rendered (remotely) images (especially maths).'),
    'toggle' => true,
    'weight' => 2
  );
}

/**
 * Implementation of subhook_defaults().
 */
function drutex_remote_defaults() {
  $D['drutex_remote_active'] = false;
  return $D;
}

/**
 * Implementation of subhook_node2html().
 */
function drutex_remote_node2html() {
  $E = array();

  /* Plain TeX environment */
  $E[] = (object) array(
      'pattern' => '@<tex( [^>]*)?>(.+?)</tex>@se',
      'replacement' => "drutex_remote('$2', '<var>format</var>', '$1', 'tag=\"span\" class=\"plaintex\"')",
      'weight' => -10
    );

  /* <equation> environment */
  $E[] = (object) array(
      'pattern' => '@<equation( [^>]*)?>(.+?)</equation>@se',
      'replacement' => "drutex_remote('\[ $2 \]', '<var>format</var>', '$1', 'tag=\"table\" class=\"displaymath\"')",
      'weight' => -7
    );

  /* <equations> environment */
  $E[] = (object) array(
      'pattern' => '@<equations( [^>]*)?>(.+?)</equations>@se',
      'replacement' => "drutex_remote('\begin{align*} $2 \end{align*}', '<var>format</var>', '$1', 'tag=\"table\" class=\"displaymath\"')",
      'weight' => -6
    );

  /* Paragraph math between $$ and $$ */
  $E[] = (object) array(
      'pattern' => '@(^|[^\\\\])(\$\$.*?[^\\\\]\$\$)@se',
      'replacement' => "'$1' . drutex_remote('$2', '<var>format</var>', '', 'tag=\"table\" class=\"displaymath\"')",
      'weight' => -5
    );

  /* Paragraph math between \[ and \] */
  $E[] = (object) array(
      'pattern' => '@\\\\\\[(.+?)\\\\\\]@se',
      'replacement' => "drutex_remote('$0', '<var>format</var>', '', 'tag=\"table\" class=\"displaymath\"')",
      'weight' => -5
    );

  /* Inline math (display style) between $! and $ */
  $E[] = (object) array(
      'pattern' => '@(^|[^\\\\])\$!(.*?[^\\\\])\$@se',
      'replacement' => "'$1' . drutex_remote('$\displaystyle $2 $', '<var>format</var>')",
      'weight' => -4
    );

  /* Inline math between $ and $ */
  $E[] = (object) array(
      'pattern' => '@(^|[^\\\\])\$(.*?[^\\\\])\$@se',
      'replacement' => "'$1' . drutex_remote('$ $2 $', '<var>format</var>')",
      'weight' => -3
    );

  /* unescape escaped dollar signs */
  $E[] = (object) array(
      'pattern' => '@\\\\\\$@s',
      'replacement' => '$',
      'weight' => 1000 /*  very late */
    );

  return $E;
}

/**
 * Implementation of subhook_filter_settings().
 */
function drutex_remote_filter_settings($format = -1) {
  $form = array();

  $form["drutex_server_remote_$format"] = array(
      '#type' => 'textfield',
      '#title' => t('Remote server path'),
      '#description' => t('The address of the remote server that will do the rendering. Should include the path to the script that will complete the rendering, i.e. end in drutex/render'),
      '#default_value' => drutex_var_get("drutex_server_remote_$format"),
    );

  $form["drutex_dpi_remote_$format"] = array(
    '#type' => 'textfield',
    '#title' => t('Resolution (DPI)'),
    '#size' => 15,
    '#maxlength' => 3,
    '#default_value' => drutex_var_get("drutex_dpi_remote_$format"),
    '#description' => t('100 is small, 107 is default, 115 is big')
  );

  return $form;
}

/**
 * Renders $text in LaTeX.
 */
function drutex_remote($text, $format = -1, $attributes = '', $default_attributes = '') {

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

  $dpi = drutex_var_get("drutex_dpi_remote_$format");

  if (!empty($attributes['dpi']) AND is_numeric($attributes['dpi']) AND $attributes['dpi'] > 0) {
    $dpi = $attributes['dpi'];
  }

  $image_type = drutex_var_get("drutex_imagetype_$format");

  $hash = sha1($text . $displaystyle . $dpi);
  $image_file = "$image_dir/$hash.$image_type";


  $success = TRUE;

  if (!is_file($image_file)) {

    // Need to curl to the remote url, sending the variables $text, $dpi, $displaystyle
    $data = array(
        'text' => $text,
        'dpi' => $dpi,
        'displaystyle' => $displaystyle,
      );
    $response = drupal_http_request(
        drutex_var_get("drutex_server_remote_$format"),
        array('Content-Type' => 'application/x-www-form-urlencoded'),
        'POST',
        http_build_query($data,'','&'));
    if ($response->code == 200) {
      // We get the image back from the server.
      $errorcode = file_save_data($response->data, $image_file, FILE_EXISTS_REPLACE);
    }
    elseif ($response->code == 403) {
      watchdog('DruTeX', 'The remote server denied the request to render LaTeX', WATCHDOG_WARNING);
    }
    elseif ($response->code == 404) {
      watchdog('DruTeX', 'The remote server doesn\'t appear to have the renderer installed.', WATCHDOG_WARNING);
    }
    else {
      watchdog('DruTeX', 'Unknown response ('. $response->code .') from the remote server when rendering LaTeX', WATCHDOG_WARNING);
    }
  }
  $success = is_file($image_file);
  if (drutex_var_get("drutex_debug")) {
    if ($success) {
      watchdog('DruTeX', "$image_file was created remotely and saved locally.", WATCHDOG_NOTICE);
    }
    else {
      watchdog('DruTeX', "$image_file couldn\'t be created remotely.", WATCHDOG_WARNING);
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

if(!function_exists('http_build_query')) {
  function http_build_query($data,$prefix=null,$sep='',$key='') {
    $ret = array();
    foreach($data as $k => $v) {
      $k = urlencode($k);
      if(is_int($k) && $prefix != null) {
        $k    = $prefix.$k;
      };
      if(!empty($key)) {
        $k    = $key."[".$k."]";
      };
      if(is_array($v) || is_object($v)) {
        array_push($ret,http_build_query($v,"",$sep,$k));
      }
      else {
        array_push($ret,$k."=".urlencode($v));
      };
    };
    if(empty($sep)) {
      $sep = ini_get("arg_separator.output");
    };
    return    implode($sep, $ret);
  };
};

function drutex_remote_do_render() {

  if (variable_get('drutex_remote_rendering_enabled',0) == 1) {
    // Check the incoming ip:
    //Wilcard character is an asterix, *
    $ip_allow = explode("\n",variable_get('drutex_remote_allowed_ips',''));

    function ip_first($ips) {
      if (($pos = strpos($ips, ',')) != false) {
	return substr($ips, 0, $pos);
      }
      else {
	return $ips;
      }
    }

    function ip_valid($ips) {
      if (isset($ips)) {
	$ip = ip_first($ips);
	$ipnum = ip2long($ip);
	  if ($ipnum !== -1 && $ipnum !== false && (long2ip($ipnum) === $ip)) { // PHP 4 and PHP 5
	    if (($ipnum < 167772160   || $ipnum >   184549375) && // Not in 10.0.0.0/8
	        ($ipnum < -1408237568 || $ipnum > -1407188993) && // Not in 172.16.0.0/12
		($ipnum < -1062731776 || $ipnum > -1062666241)) {   // Not in 192.168.0.0/16
	      return true;
            }
	  }
	}
      return false;
    }

    function ip() {
      $check = array('HTTP_X_FORWARDED_FOR', 'HTTP_X_FORWARDED',
	  'HTTP_FORWARDED_FOR', 'HTTP_FORWARDED',
	  'HTTP_VIA', 'HTTP_X_COMING_FROM', 'HTTP_COMING_FROM'
        );
      foreach ($check as $c) {
	if (ip_valid($_SERVER[$c])) {
	  return ip_first($_SERVER[$c]);
	}
      }
      return $_SERVER['REMOTE_ADDR'];
    }

    $ip = ip();

    $allowed_to_run = FALSE;

    if(in_array($ip, $ip_allow)) {
      $allowed_to_run = TRUE;
    }
    else {
      foreach($ip_allow as $allow) {
        if (trim($allow) != '') {
	  if(eregi($allow, $ip)) {
	    $allowed_to_run = TRUE;
	  }
        }
      }
    }


    if ($allowed_to_run) {
      // Get the variable and do the render:
      $text = $_REQUEST['text'];
      $dpi = $_REQUEST['dpi'];
      $template_dir = drutex_var_get('drutex_dir') . '/templates/render';
      $template_file = variable_get('drutex_remote_render_template', 'default.tex');
      $template = $template_dir . '/' . $template_file;
      $hash = sha1('latex_image'. $text . $dpi);
      $temporary_dir = _drutex_create_temporary_dir();
      $image_file = $temporary_dir .'/'. $hash .'.png';

      /* conversion method */
      $conversion = variable_get('drutex_remote_render_conversion', 'dvipng');

      /* command patterns */
      $pattern_convert = variable_get('drutex_remote_render_custom_pattern', '');

      /* replacement arrays */
      $map = array('[TMP_DIR]' => $temporary_dir, '[IMG_FILE]' => $image_file,
        '[DPI]' => $dpi, '[HASH]' => $hash,
        '[DRUTEX_DIR]' => drutex_var_get('drutex_dir')
      );

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
        }
      }

      $success = is_file($image_file);

      if ($success) {
        // Transfer file in 1024 byte chunks to save memory usage.
        if ($fd = fopen($image_file, 'rb')) {
          while (!feof($fd)) {
            print fread($fd, 1024);
          }
          fclose($fd);
        }
        else {
        drupal_not_found();
        }
      }
      /* remove temporary dir */
      if (!drutex_var_get("drutex_debug")) {
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
    else {
      // Not an allowed ip:
      drupal_access_denied();
    }

  }
  else {
    // Rendering disabled:
    drupal_access_denied();
  }

exit();

}

function drutex_remote_settings() {
  /* conversion methods */
  $conversions = array('dvipng'        => 'dvipng',
                       'imagemagick'   => 'ImageMagick (convert)',
                       'custom'        => 'custom');
  $form = array();

  $form['remote_fieldset'] = array(
      '#type' => 'fieldset',
      '#title' => t('Remote rendering settings'),
      '#collapsible' => true,
    );

  $form['remote_fieldset']['drutex_remote_rendering_enabled'] = array(
      '#type' => 'checkbox',
      '#title' => t('Allow remote LaTeX rendering on THIS server'),
      '#default_value' => variable_get('drutex_remote_rendering_enabled',0),
    );

  $form['remote_fieldset']['drutex_remote_allowed_ips'] = array(
      '#type' => 'textarea',
      '#title' => t('Allowed remote IPs'),
      '#description' => t('Put the address of each remote IP that is allowed to render LaTeX on this server.'."<br />".'Put each address on a new line.'."<br />".'You may use * as a wildcard character.'."<br />".'e.g. 10.0.0.5, or 192.168.*.*'),
      '#default_value' => variable_get('drutex_remote_allowed_ips',''),
    );


  /* determine whether temporary dir is writeable */
  $is_writable = is_writable(drutex_var_get('drutex_dir_temporary'));

  $form['remote_fieldset']['drutex_dir_temporary'] = array(
    '#type' => 'textfield',
    '#title' => t('Temporary directory'),
    '#size' => 80,
    '#maxlength' => 100,
    '#default_value' => drutex_var_get('drutex_dir_temporary'),
    '#description' => t('Directory for temporaray actions. Has to be writable by apache.'),
    '#attributes' => $is_writable ? array() : array('style' => 'border-color:red')
  );



  $form['remote_fieldset']['drutex_remote_render_template'] = array(
      '#type' => 'select',
      '#title' => t('Template'),
      '#options' => _drutex_get_templates(),
      '#default_value' => variable_get('drutex_remote_render_template', 'default.tex')
  );

  $form['remote_fieldset']['drutex_remote_render_conversion'] = array(
    '#type' => 'select',
    '#title' => t('Conversion Method'),
    '#options' => $conversions,
    '#default_value' => variable_get('drutex_remote_render_conversion', 'dvipng')
  );

  /* Custom Conversion Method */

  /* determine default value for <custom conversion method> */
  $default_value = variable_get('drutex_remote_render_custom_pattern', 'cd [TMP_DIR]; TEXINPUTS="[DRUTEX_DIR]//:" latex -interaction=batchmode [HASH].tex'."\n".
      'dvipng -o [IMG_FILE] -D [DPI] -T tight [TMP_DIR]/[HASH].dvi');

  /* description for <custom conversion method> */
  $description = t('* Leave blank and select dvipng or ImageMagick, to display a template for one of these commands in this box.') . '<br />'
    . t('* Every line is executed in sequence, but in its own shell (so path changes aren\'t inherited).') . '<br />'
    . t('Placeholders available:<br />
        [HASH] - hash (name) for the image<br />
        [TMP_DIR] - temporary dir (automatically cleaned)<br />
        [DPI] - dots per inch (see above)<br />
        [IMG_FILE] - abbreviation for [TMP_DIR]/[HASH].png<br />
        [DRUTEX_DIR] - full path to DruTeX base dir');


  $form['remote_fieldset']['drutex_remote_render_custom_pattern'] = array(
    '#type' => 'textarea',
    '#title' => t('Conversion Method (Custom)'),
    '#rows' => 3,
    '#cols' => 50,
    '#default_value' => $default_value,
    '#description' => $description
  );

  return system_settings_form($form);

}
