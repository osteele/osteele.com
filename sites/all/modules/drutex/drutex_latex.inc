<?php
// $Id: drutex_latex.inc,v 1.11 2006/09/06 02:25:41 dfg Exp $

/**
 * @file
 * Provide the LaTeX-to-HTML transformation rules.
 */

/**
 * Implementation of subhook_info().
 */
function drutex_latex_info($format = -1) {
  return (object) array(
    'title' => t('LaTeX to HTML'),
    'description' => t('Converts many LaTeX commands and environments to plain HTML.'),
    'toggle' => true,
    'weight' => 4
  );
}

/**
 * Implementation of subhook_defaults().
 */
function drutex_latex_defaults() {
  $D['drutex_latex_active'] = false;
  return $D;
}

/**
 * Implementation of subhook_node2html().
 */
function drutex_latex_node2html() {
  $E = array();

  $E[] = (object) array(
      'type' => 'latex-command',
      'pattern' => 'section',
      'replacement' => '<h2 class="section">$1</h2>'
    );

  $E[] = (object) array(
      'type' => 'latex-command',
      'pattern' => 'subsection',
      'replacement' => '<h3 class="subsection">$1</h3>'
    );

  $E[] = (object) array(
      'type' => 'latex-command',
      'pattern' => 'minisection',
      'replacement' => '<h3 class="minisection">$1</h3>'
    );

  $E[] = (object) array(
      'type' => 'latex-command',
      'pattern' => 'paragraph',
      'replacement' => '<h4 class="paragraph">$1</h4>'
    );

  $E[] = (object) array(
      'type' => 'latex-command',
      'pattern' => 'textbf',
      'replacement' => '<b>$1</b>'
    );

  $E[] = (object) array(
      'type' => 'latex-command',
      'pattern' => 'textit',
      'replacement' => '<i>$1</i>'
    );

  $E[] = (object) array(
      'type' => 'latex-command',
      'pattern' => 'emph',
      'replacement' => '<em>$1</em>'
    );


  /*
    regex hacks for:
      enumeration aka ordered list
      itemize aka unordered list
  */

  $E[] = (object) array(
      'pattern' => '@\\\\begin\{enumerate\}@s',
      'replacement' => '<ol class="enumerate">',
      'weight' => 100
    );

  $E[] = (object) array(
      'pattern' => '@\\\\begin\{itemize\}@s',
      'replacement' => '<ul class="itemize">',
      'weight' => 100
    );

  $E[] = (object) array(
      'pattern' => '@\\\\item(.*?)(?=\\\\item|\\\\end\{enumerate\}|\\\\end\{itemize\})@s',
      'replacement' => "<li>$1</li>",
      'weight' => 101
    );

  $E[] = (object) array(
      'pattern' => '@\\\\end\{enumerate\}@s',
      'replacement' => "</ol>",
      'weight' => 102
    );

  $E[] = (object) array(
      'pattern' => '@\\\\end\{itemize\}@s',
      'replacement' => "</ul>",
      'weight' => 102
    );

  return $E;
}
