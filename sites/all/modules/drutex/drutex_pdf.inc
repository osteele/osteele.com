<?php
// $Id: drutex_pdf.inc,v 1.18 2007/04/25 14:46:28 darthsteven Exp $

/**
 * @file
 * PDF filter: Convert node's content to LaTeX/PDF.
 *
 * This involves decent HTML-to-LaTeX parsing.
 */

/**
 * Implementation of subhook_info().
 */
function drutex_pdf_info($format = -1) {
  return (object) array(
    'title' => t('PDF Generator'),
    'description' => t('Allows to generate pdf-versions of a node (with decent html2latex features).'),
    'toggle' => true,
    'weight' => 6
  );
}

/**
 * Implementation of subhook_defaults().
 */
function drutex_pdf_defaults() {
  $D['drutex_pdf_active'] = true;

  /* relative to drutex/templates/pdf */
  $D['drutex_template_pdf'] = 'plain.tex';

  $D['drutex_pattern_pdflatex']  = 'cd [TMP_DIR]; TEXINPUTS="[DRUTEX_DIR]//:" pdflatex -interaction=batchmode [NAME].tex'."\n".
                                      'cp [TMP_DIR]/[NAME].pdf [IMG_DIR]/[NAME].pdf';
  return $D;
}

/**
 * Implementation of subhook_filter_settings().
 */
function drutex_pdf_filter_settings($format = -1) {
  /* conversion methods */
  $conversions = array('dvipng'        => 'dvipng',
                       'imagemagick'   => 'ImageMagick (convert)',
                       'custom'        => 'custom');

  $form["drutex_template_pdf_$format"] = array(
      '#type' => 'select',
      '#title' => t('Template'),
      '#options' => _drutex_pdf_get_templates(),
      '#default_value' => drutex_var_get("drutex_template_pdf_$format")
  );

  /* Conversion Command */

  /* description for <conversion command> */
  $description = t('* Every line is executed in sequence, but in its own shell (so path changes aren\'t inherited).') .'<br />'
    . t('Placeholders available:<br />
        [NAME] - name of the tex/pdf<br />
        [TMP_DIR] - temporary dir (automatically cleaned)<br />
        [IMG_DIR] - directory where the images/pdfs are saved<br />
        [DRUTEX_DIR] - full path to DruTeX base dir');


  $form["drutex_pattern_pdflatex_$format"] = array(
    '#type' => 'textarea',
    '#title' => t('Conversion Command'),
    '#rows' => 3,
    '#cols' => 50,
    '#default_value' => drutex_var_get("drutex_pattern_pdflatex_$format"),
    '#description' => $description
  );

  return $form;
}

/**
 * Goto wrapper for drutex_node2pdf().
 *
 * Creates the pdf file, if not already done, and redirects the user to this file.
 *
 * @param $nid
 *   The node id.
 */
function drutex_node2pdf_wrapper($nid) {
  $node = node_load($nid);
  $pdf_dir = drutex_var_get("drutex_dir_images_{$node->format}");

  if (!is_file("$pdf_dir/{$node->nid}.pdf")) {
    drutex_node2pdf($nid);
  }

  drupal_goto("$pdf_dir/{$node->nid}.pdf");
}

/**
 * The node2pdf function.
 *
 * Uses node2latex and passes the output to pdfLaTeX.
 * So the pdf file is stored in the image directory.
 *
 * @param $nid
 *   The node id.
 */
function drutex_node2pdf($nid) {
  $node = node_load($nid);

  drutex_node2latex($nid);

  $image_dir = drutex_var_get("drutex_dir_images_{$node->format}");

  /* create temporary dir */
  $temporary_dir = _drutex_create_temporary_dir();

  copy("$image_dir/{$node->nid}.tex", "$temporary_dir/{$node->nid}.tex");
  _drutex_copy_dir("modules/drutex/templates/pdf", $temporary_dir);


  /*
   * Prepare and execute the conversion commands
   */

  $pattern_convert = drutex_var_get("drutex_pattern_pdflatex_{$node->format}");

  /* replacement array */
  $map = array(
    '[NAME]' => $node->nid, '[TMP_DIR]' => $temporary_dir, '[IMG_DIR]' => $image_dir,
    '[DRUTEX_DIR]' => drutex_var_get('drutex_dir')
  );

  /* conversion commands (seperated by \n) */
  $cmd_convert = str_replace(array_keys($map), $map, $pattern_convert);

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

  /* remove temporary dir */
  if (!drutex_var_get("drutex_debug_$format")) {
    _drutex_delete_dir($temporary_dir);
  }
  else {
    watchdog('DruTeX', "Temporary directory for pdf-generation was $temporary_dir.", WATCHDOG_NOTICE);
  }
}

/**
 * Goto wrapper for drutex_node2latex().
 *
 * Creates the tex file, if not already done, and redirects the user to this file.
 *
 * @param $nid
 *   The node id.
 */
function drutex_node2latex_wrapper($nid) {
  $node = node_load($nid);
  $pdf_dir = drutex_var_get("drutex_dir_images_{$node->format}");

  if (!is_file("$pdf_dir/{$node->nid}.tex")) {
    drutex_node2latex($nid);
  }

  drupal_goto("$pdf_dir/{$node->nid}.tex");
}

/**
 * The node2latex function.
 *
 * Wraps the node's content in a LaTeX template, with some preprocessing.
 *
 * @param $nid
 *   The node id.
 * @return
 *   None, but a tex file was put to disk!
 */
function drutex_node2latex($nid) {
  $node = node_load($nid);

  /* get the pdf template file path (it's a tex file) */
  $pdf_template_file = drutex_var_get('drutex_dir')
    .'/templates/pdf/'. drutex_var_get("drutex_template_pdf_{$node->format}");

  /* read this file into buffer */
  $handle = fopen($pdf_template_file, 'r');
  $pdf_template_content = fread($handle, filesize($pdf_template_file));
  fclose($handle);

  /* convert html elements in the node's content to latex */
  $content = drutex_html2latex($node);

  /* replace the template keys by the actual values */
  $map = array(
    'DRUTEX_NODE_TITLE' => $node->title,
    'DRUTEX_NODE_URL' => url('node/'. $node->nid, null, null, true),
    'DRUTEX_DIR' => drutex_var_get('drutex_dir'),
    'DRUTEX_NODE_BODY' => $content
  );

  $text = str_replace(array_keys($map), $map, $pdf_template_content);

  /* save generated TeX file */
  $dir = drutex_var_get("drutex_dir_images_{$node->format}");
  file_save_data($text, "$dir/{$node->nid}.tex", FILE_EXISTS_REPLACE);
}

/**
 * A simple html2latex / security function.
 *
 * @param $node
 *   A node object.
 * @return
 *   Latex code / security restricted.
 */
function drutex_html2latex(&$node) {
  if (drutex_submodule_is_active('security', $node->format)) {
    if (drutex_security($node->body, $format) == false) {
      return 'Unallowed command detected!';
    }
  }

  $text = $node->body;

  /* include the rules file */
  $drutex_path = drupal_get_path('module', 'drutex');
  include_once("$drutex_path/drutex_pdf.rules");

  /* get entities */
  $A = drutex_submodule_invoke_all('drutex2latex', $node, "format=$format");
  $B = module_invoke_all('drutex2latex', $node);
  $entities = array_merge($A, $B);
  usort($entities, '_drutex_weight_sort_cmp');

  /* apply rules */
  foreach ($entities as $entity) {
    list($pattern, $replacement) = _drutex_create_regex($entity, $format);
    $text = preg_replace($pattern, $replacement, $text);
  }

  return $text;
}

/**
 * Implementation of hook_nodeapi().
 */
function drutex_pdf_nodeapi(&$node, $op, $arg) {
  if (true) {
    switch ($op) {
      case 'validate':
        break;

      case 'load':
        break;

      case 'insert':
        break;

      case 'update':
      case 'delete':
        $pdf_dir = drutex_var_get("drutex_dir_images_{$node->format}");

        if (is_file("$pdf_dir/{$node->nid}.pdf")) {
          unlink("$pdf_dir/{$node->nid}.pdf");
        }

        if (is_file("$pdf_dir/{$node->nid}.tex")) {
          unlink("$pdf_dir/{$node->nid}.tex");
        }

        break;
    }
  }
}

/**
 * Implementation of hook_perm().
 */
function drutex_pdf_perm() {
  return array('access pdf files', 'access latex source');
}

/**
 * Get a list with templates for drutex_pdf.
 */
function _drutex_pdf_get_templates() {
  $dir = drupal_get_path('module', 'drutex') .'/templates/pdf';

  $A = file_scan_directory($dir, '.*\.tex');
  $B = array();

  foreach ($A as $key => $val) {
    $key = substr($key, strlen($dir)+1);
    $B[$key] = $key;
  }

  return $B;
}
