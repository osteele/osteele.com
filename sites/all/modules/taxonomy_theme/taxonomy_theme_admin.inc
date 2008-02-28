<?php
// $Id: taxonomy_theme_admin.inc,v 1.8.2.1 2007/02/25 16:49:08 profix898 Exp $

require_once (drupal_get_path('module', 'taxonomy_theme').'/taxonomy_theme_paths.inc');
require_once (drupal_get_path('module', 'taxonomy_theme').'/taxonomy_theme_taxonomy.inc');

/**
 * function taxonomy_theme_admin_form().
 * (main administration settings page)
 */
function taxonomy_theme_admin_form() {
  if (!variable_get('taxonomy_theme_enable', 0)) {
    $form['taxonomy_theme_enable'] = array(
      '#type' => 'radios',
      '#title' => t('Enable Taxonomy-based templates'),
      '#default_value' => variable_get('taxonomy_theme_enable', 0),
      '#options' => array(t('Disabled'), t('Enabled')),
    );
    $form['buttons']['submit'] = array('#type' => 'submit', '#value' => t('Save configuration'));
    $form['#submit']['taxonomy_theme_admin_form_submit'] = array();
    return $form;
  }
  
  $form['global'] = array(
    '#type' => 'fieldset',
    '#title' => t('Global Settings'),
    '#collapsible' => FALSE,
    '#collapsed' => FALSE,
  );
  $form['global']['taxonomy_theme_enable'] = array(
    '#type' => 'checkbox',
    '#title' => t('Enable Taxonomy-based templates'),
    '#default_value' => variable_get('taxonomy_theme_enable', 0),
    '#description' => t('You must check this to activate the taxonomy_theme module.'),
  ); 
  $form['global']['taxonomy_theme_allowall'] = array(
    '#type' => 'checkbox',
    '#title' => t('Allow all themes'),
    '#default_value' => variable_get('taxonomy_theme_allowall', 0),
    '#description' => t('Offers all installed themes for selection, not enabled ones only.'),
  );
  $extended_enabled = variable_get('taxonomy_theme_extended_enable', 0);
  $form['global']['taxonomy_theme_extended_enable'] = array(
    '#type' => 'checkbox',
    '#title' => t('Enable \'Extended\' (path-based) assignment of themes'),
    '#default_value' => $extended_enabled,
    '#description' => t('Enables you to assign themes to paths (e.g. node/21) and path aliases.'),
  );
  if (module_exists('views')) {
    $form['global']['taxonomy_theme_viewspages'] = array(
      '#type' => 'checkbox',
      '#title' => t('Enable \'Themes for Views\''),
      '#default_value' => variable_get('taxonomy_theme_viewspages', 0),
      '#description' => t('Allows you to theme pages created by views.module. You need to have \'Extended\' option enabled to use this option. It will show a \'Theme\' option in the \'Page\' section of edit view pages.'),
      '#disabled' => $extended_enabled ? FALSE : TRUE,
    );
  }
  $form['global']['taxonomy_theme_alter_node'] = array(
    '#type' => 'checkbox',
    '#title' => t('Show theme option in create/edit node forms'),
    '#default_value' => variable_get('taxonomy_theme_alter_node', 0),
    '#description' => t('Assign themes directly from create/edit node forms. You need to have \'Extended\' option enabled to use this option. It will show a \'Theme\' section on create/edit node pages.'),
    '#disabled' => $extended_enabled ? FALSE : TRUE,
  );
  if ($extended_enabled && variable_get('taxonomy_theme_alter_node', 0)) {
    $nodetypes = node_get_types('names');
    if (count($nodetypes)) {
      $form['global']['alter_node_nodetypes'] = array(
        '#type' => 'fieldset',
        '#title' => t('Show \'Theme\' option for nodes of type'),
        '#collapsible' => TRUE,
        '#collapsed' => FALSE,
      );
      $rows = array();
      $header = array(t('NodeType'), t('Enabled'));
      $form['global']['alter_node_nodetypes']['table'] = array('#theme' => 'taxonomy_theme_table');
      $form['global']['alter_node_nodetypes']['table']['header'] = array('#data' => $header);
      $form['global']['alter_node_nodetypes']['table']['type'] = array('#value' => 'taxonomy_theme_alter_nodetype|');
      foreach ($nodetypes as $type => $title) {
        $form['global']['alter_node_nodetypes']['table'][$type]['title'] = array('#value' => $title);
        $form['global']['alter_node_nodetypes']['table'][$type]['taxonomy_theme_alter_nodetype|'.$type] = array(
          '#type' => 'checkbox',
          '#default_value' => variable_get('taxonomy_theme_alter_nodetype|'.$type, 1),
        );
      }
    }
  }
  $form['global']['taxonomy_theme_alter_forms'] = array(
    '#type' => 'checkbox',
    '#title' => t('Show theme option on term/vocab/content-types pages'),
    '#default_value' => variable_get('taxonomy_theme_alter_forms', 1),
    '#description' => t('Assign themes directly from term/vocab-pages in \'categories\' and/or content-type settings.'),
  );
  
  $form['buttons']['submit'] = array('#type' => 'submit', '#value' => t('Save configuration') );
  $form['buttons']['reset'] = array('#type' => 'submit', '#value' => t('Reset to defaults') );
  $form['#submit']['taxonomy_theme_admin_form_submit'] = array();
  return $form;
}

/**
 * function taxonomy_theme_admin_taxonomy_form().
 * (taxonomy selection method)
 */
function taxonomy_theme_admin_taxonomy_form() {
  $form['taxonomy'] = array(
    '#type' => 'fieldset',
    '#title' => t('Taxonomy'),
    '#collapsible' => FALSE,
    '#collapsed' => FALSE,
  );
  // TaxonomyTheme selection method
  $options = array();
  $options[TAXOTHEME_SKIP]      = t('Disabled');
  $options[TAXOTHEME_TERM]      = t('Term-based');
  $options[TAXOTHEME_VOCAB]     = t('Vocab-based');
  $options[TAXOTHEME_ALLTAXO]   = t('All Taxonomy');
  $options[TAXOTHEME_NODETYPE]  = t('NodeType-based');
  $method = variable_get('taxonomy_theme_method', TAXOTHEME_SKIP);
  $form['taxonomy']['taxonomy_theme_method'] = array(
    '#type' => 'select',
    '#title' => t('Template selection'),
    '#default_value' => $method,
    '#options' => $options,
    '#description' => t('This value determines the method used for template selection.'),
  );
  $options_themes = _taxonomy_theme_options();
  switch(variable_get('taxonomy_theme_method', TAXOTHEME_SKIP)) {
    case TAXOTHEME_TERM:
      $form['taxonomy'][] = _taxonomy_theme_admin_term_form($options_themes);
      break;
    case TAXOTHEME_VOCAB:
      $form['taxonomy'][] = _taxonomy_theme_admin_vocab_form($options_themes);
      break;
    case TAXOTHEME_ALLTAXO:
      $form['taxonomy'][] = _taxonomy_theme_admin_alltaxo_form($options_themes);
      break;
    case TAXOTHEME_NODETYPE:
      $form['taxonomy'][] = _taxonomy_theme_admin_nodetype_form($options_themes);
      break;
    default:
  }
  
  $form['buttons']['submit'] = array('#type' => 'submit', '#value' => t('Save configuration') );
  $form['buttons']['reset'] = array('#type' => 'submit', '#value' => t('Reset to defaults') );
  $form['#submit']['taxonomy_theme_admin_form_submit'] = array();
  return $form;
}

/**
 * function _taxonomy_theme_admin_term_form().
 * (term-based administration form)
 */
function _taxonomy_theme_admin_term_form($options_themes) {
  $form['taxonomy_theme_select'] = array(
    '#type' => 'fieldset',
    '#title' => t('Terms => Themes'),
    '#collapsible' => FALSE,
    '#collapsed' => FALSE,
  );
  $options = array();
  $selector = variable_get('taxonomy_theme_selector', '');
  if (!$selector) {
    $options[0] = t('Choose a category');
  }
  $vocabs = taxonomy_get_vocabularies();
  $forum_vid = variable_get('forum_nav_vocabulary', '');
  foreach ($vocabs as $vocab) {
    if ($vocab->vid != $forum_vid) {
      $options[$vocab->vid] = $vocab->name;
    }
  }
  if (!empty($vocabs)) {
    $form['taxonomy_theme_select']['taxonomy_theme_selector'] = array(
      '#type' => 'select',
      '#title' => t('Category to use for template selection'),
      '#default_value' => $selector,
      '#options' => $options,
      '#description' => t('The value of this category will be used to determine the template to apply.'),
    );
  }
  else {
    $form['taxonomy_theme_select']['text'] = array('#value' => t('<br /><strong>Your site currently has no categories.</strong><br /><br />Please create at least one category and add the terms you will map to themes and return to this page. For example, you might create a category called Sections that has two terms, <em>blue</em> to map to the bluebeach theme and <em>grey</em> to map to the <em>box_grey</em> theme.')); 
  }
  
  if ($selector) {
    $data['key'] = 'tid';
    $data['method'] = array('type' => 'term', 'label' => t('Term'));
    $data['data'] = taxonomy_get_tree($selector);
    $form['taxonomy_theme_select'][] = taxonomy_theme_admin_table_builder($options_themes, $data);
  }
  
  return $form;
}

/**
 * function _taxonomy_theme_admin_vocab_form().
 * (vocabulary-based administration form)
 */
function _taxonomy_theme_admin_vocab_form($options_themes) {
  $form['taxonomy_theme_select'] = array(
    '#type' => 'fieldset',
    '#title' => t('Vocabularies => Themes'),
    '#collapsible' => FALSE,
    '#collapsed' => FALSE,
  );
  $data['key'] = 'vid';
  $data['method'] = array('type' => 'vocab', 'label' => t('Vocabulary'));
  $data['data'] = taxonomy_get_vocabularies();
  $form['taxonomy_theme_select'][] = taxonomy_theme_admin_table_builder($options_themes, $data);
  
  return $form;
}

/**
 * function _taxonomy_theme_admin_alltaxo_form().
 * (all-taxonomy administration form)
 */
function _taxonomy_theme_admin_alltaxo_form($options_themes) {
  $form['taxonomy_theme_select'] = array(
    '#type' => 'fieldset',
    '#title' => t('All Taxonomy => Themes'),
    '#collapsible' => FALSE,
    '#collapsed' => FALSE,
  );
  $vocabs = taxonomy_get_vocabularies();
  $forum_vid = variable_get('forum_nav_vocabulary', '');
  if (count($vocabs)) {
    foreach ($vocabs as $vocab) {
      if ($vocab->vid != $forum_vid) {
        $terms = taxonomy_get_tree($vocab->vid);
        $rows = array();
        $form['taxonomy_theme_select'][$vocab->vid] = array(
          '#type' => 'fieldset',
          '#title' => t('Vocabulary: ').$vocab->name,
          '#collapsible' => TRUE,
          '#collapsed' => TRUE,
        );
        $data['key'] = 'tid';
        $data['method'] = array('type' => 'all', 'label' => t('Term'));
        $data['data'] = $terms;
        $form['taxonomy_theme_select'][$vocab->vid][] = taxonomy_theme_admin_table_builder($options_themes, $data);
      }
    }
  }
  else {
    $form['taxonomy_theme_select']['text'] = array('#value' => t('<strong>Your site currently has no vocabularies.</strong>'));
  }
  
  return $form;
}

/**
 * function _taxonomy_theme_admin_nodetype_form().
 * (nodetype-based administration form)
 */
function _taxonomy_theme_admin_nodetype_form($options_themes) {
  $form['taxonomy_theme_select'] = array(
    '#type' => 'fieldset',
    '#title' => t('NodeType => Themes'),
    '#collapsible' => FALSE,
    '#collapsed' => FALSE,
  );
  $nodetypes = node_get_types('names');
  if (count($nodetypes)) {
    $rows = array();
    $header = array(t('NodeType'), t('Assigned to Theme'));
    $form['taxonomy_theme_select']['table'] = array('#theme' => 'taxonomy_theme_table');
    $form['taxonomy_theme_select']['table']['header'] = array('#data' => $header);
    $form['taxonomy_theme_select']['table']['type'] = array('#value' => 'taxonomy_theme_nodetype|');
    foreach ($nodetypes as $type => $title) {
      $template_type = variable_get('taxonomy_theme_nodetype|'.$type, '');
      $form['taxonomy_theme_select']['table'][$type]['title'] = array('#value' => $title);
      $form['taxonomy_theme_select']['table'][$type]['taxonomy_theme_nodetype|'.$type] = array(
        '#type' => 'select',
        '#default_value' => $template_type,
        '#options' => $options_themes,
      );
    }
  }
  else {
    $form['taxonomy_theme_select']['text'] = array('#value' => t('<strong>Your site currently has no node type defined.</strong>'));
  }
  
  return $form;
}

/**
 * function taxonomy_theme_admin_forum_form().
 * (forum administration form)
 */
function taxonomy_theme_admin_forum_form() {
  $form['taxonomy_theme_forums'] = array(
    '#type' => 'fieldset',
    '#title' => t('Forums => Themes'),
    '#collapsible' => FALSE,
    '#collapsed' => FALSE,
  );
  $form['taxonomy_theme_forums']['taxonomy_theme_forums_enable'] = array(
    '#type' => 'checkbox',
    '#title' => t('Apply themes to forums'),
    '#default_value' => variable_get('taxonomy_theme_forums_enable', 0),
  );
  $form['taxonomy_theme_forums']['taxonomy_theme_forum_dep'] = array(
    '#type' => 'checkbox',
    '#title' => t('Theme forum-dependent nodes/pages'),
    '#default_value' => variable_get('taxonomy_theme_forum_dep', 1),
    '#description' => t('Apply the forum\'s theme to (add/view/edit) comment pages also.'),
  );
  $forum_vid = variable_get('forum_nav_vocabulary', '');
  if (variable_get('taxonomy_theme_forums_enable', 0)) {
    $terms = taxonomy_get_tree($forum_vid);
    $options_themes = _taxonomy_theme_options();
    if (count($terms)) {
      $data['key'] = 'tid';
      $data['method'] = array('type' => 'forum', 'label' => t('Forum'));
      $data['data'] = $terms;
      $form['taxonomy_theme_forums'][] = taxonomy_theme_admin_table_builder($options_themes, $data);
    }
    else {
      $form['taxonomy_theme_forums']['text'] = array('#value' => t('<strong>Your site currently has no forums.</strong>'));
    }
  }
  
  $form['buttons']['submit'] = array('#type' => 'submit', '#value' => t('Save configuration') );
  $form['buttons']['reset'] = array('#type' => 'submit', '#value' => t('Reset to defaults') );
  $form['#submit']['taxonomy_theme_admin_form_submit'] = array();
  return $form;
}

/**
 * function taxonomy_theme_admin_extended_form().
 * (extended (path-based) administration form)
 */
function taxonomy_theme_admin_extended_form() {
  $form['taxonomy_theme_extended'] = array(
    '#type' => 'fieldset',
    '#title' => t('Extended'),
    '#collapsible' => FALSE,
    '#collapsed' => FALSE,
  );
  if (variable_get('taxonomy_theme_extended_enable', 0)) {
    $form['taxonomy_theme_extended']['help'] = array('#value' => t('<p>To assign a theme for a particular path enter the path into according textarea.<br />Enter one path per line as Drupal paths (or path aliases). Character \'*\' is a wildcard.<br /><i>Adding \'node/6\' to the \'bluemarine\' field will assign bluemarine theme to node 6.</i></p>'));
    $form['taxonomy_theme_extended']['theme_paths'] = array('#tree' => TRUE);
    $options_admin_themes = _taxonomy_theme_options(TRUE, FALSE);
    foreach($options_admin_themes as $themex) {
      $form['taxonomy_theme_extended']['theme_paths'][$themex] = array(
        '#type' => 'fieldset',
        '#title' => $themex,
        '#collapsible' => TRUE,
        '#collapsed' => TRUE,
      );
      unset($paths);
      $result = db_query("SELECT path FROM {theme_paths} WHERE theme = '%s'", $themex);
      while ($path = db_fetch_object($result)) {
        $paths .= $path->path."\r\n";
      }
      $form['taxonomy_theme_extended']['theme_paths'][$themex]['paths'] = array(
        '#type' => 'textarea',
        '#rows' => 10,
        '#default_value' => $paths,
      );
    }
  }
  else {
    $form['taxonomy_theme_extended']['text'] = array('#value' => t('<strong>\'Extended\' assignment of themes is currently disabled.</strong>'));
  }
  
  $form['buttons']['submit'] = array('#type' => 'submit', '#value' => t('Save configuration') );
  $form['buttons']['reset'] = array('#type' => 'submit', '#value' => t('Reset to defaults') );
  $form['#submit']['taxonomy_theme_admin_extended_form_submit'] = array();
  return $form;
}

/**
 * function taxonomy_theme_admin_table_builder().
 * (build list/table of taxonomy vocabs/terms)
 */
function taxonomy_theme_admin_table_builder($options_themes, $data) {
  if (count($data['data'])) {
    $rows = array();
    $header = array($data['method']['label'], t('Assigned to Theme'));
    $form['table'] = array('#theme' => 'taxonomy_theme_table');
    $form['table']['header'] = array('#data' => $header);
    $form['table']['type'] = array('#value' => 'taxonomy_theme_'.$data['method']['type'].'|');
    foreach ($data['data'] as $item) {
      $template_item = variable_get('taxonomy_theme_'.$data['method']['type'].'|'.$item->$data['key'], '');
      $form['table'][$item->$data['key']]['title'] = array('#value' => $item->name);
      $form['table'][$item->$data['key']]['taxonomy_theme_'.$data['method']['type'].'|'.$item->$data['key']] = array(
        '#type' => 'select',
        '#default_value' => $template_item,
        '#options' => $options_themes,
      );
    }
  }
  else {
    $form['noitems'] = array('#value' => t('<strong>No items</strong>'));
  }
  
  return $form;
}

/**
 * function theme_taxonomy_theme_table().
 */
function theme_taxonomy_theme_table($form) {
  foreach (element_children($form) as $key) {
    if (isset($form[$key]['title'])) {
      $row = array();
      $row[] = drupal_render($form[$key]['title']);
      $row[] = drupal_render($form[$key][$form['type']['#value'].$form[$key]['#parents'][0]]);
      $rows[] = $row;
    }
  }
  $header = array($form['header']['#data'][0], $form['header']['#data'][1]);  
  return theme('table', $header, $rows);
}

/**
 * function taxonomy_theme_admin_form_submit(). 
 */
function taxonomy_theme_admin_form_submit($form_id, $form_data) {
  if ($form_data['op'] == t('Save configuration')) {
    // save settings
    unset($form_data['op'], $form_data['submit'], $form_data['reset'], $form_data['form_token'], $form_data['form_id']);
    foreach ($form_data as $key => $value) {
      _taxonomy_theme_variable_set($key, $value);
    }
    menu_rebuild();
    drupal_set_message(t('The configuration options have been saved.'));
  }
  else {
    // reset settings
    db_query("DELETE FROM {variable} WHERE name LIKE 'taxonomy_theme_%'");
    db_query("UPDATE {system} SET weight = '-10' WHERE filename LIKE '%taxonomy_theme.module' LIMIT 1");
    variable_set('taxonomy_theme_enable', 1);
    drupal_set_message(t('All configuration options have been set to default values.'));
  }
}

/**
 * function taxonomy_theme_admin_extended_form_submit(). 
 */
function taxonomy_theme_admin_extended_form_submit($form_id, $form_data) {
  if ($form_data['op'] == t('Save configuration')) {
    // save settings
    db_query("DELETE FROM {theme_paths}");
    foreach ($form_data['theme_paths'] as $theme => $data) {
      $paths = preg_split('/(\r\n?|\n)/', $data['paths'], -1, PREG_SPLIT_NO_EMPTY);
      foreach ($paths as $path) {
        while ($path{0} == '/') {
          $path = substr($path, 1);
        }
        db_query("INSERT INTO {theme_paths} (path, theme) VALUES ('%s', '%s')", $path, $theme);
      }
    }
    _taxonomy_theme_expcache(); // reset paths cache
    drupal_set_message(t('The configuration options have been saved.'));
  }
  else {
    // reset settings
    db_query("DELETE FROM {theme_paths}");
    _taxonomy_theme_expcache(); // reset paths cache
    drupal_set_message(t('All configuration options have been set to default values.'));
  }
}

/**
 * function _taxonomy_theme_form_alter().
 * (hook into various form to add a theme selector field)
 */
function _taxonomy_theme_form_alter($form_id, &$form) {
  $options_themes = _taxonomy_theme_options();
  if (variable_get('taxonomy_theme_alter_forms', 1) && user_access('administer taxonomy_theme')) {
    _taxonomy_theme_alter_taxonomy($options_themes, $form_id, $form);
  }
  if (variable_get('taxonomy_theme_alter_node', 0)
          && ($form_id == $form['type']['#value'].'_node_form')
          && variable_get('taxonomy_theme_alter_nodetype|'.$form['type']['#value'], 1)
          && (user_access('administer taxonomy_theme') || user_access('assign node themes'))
        ) {
    _taxonomy_theme_alter_nodeform($options_themes, $form_id, $form);
  }
  if (variable_get('taxonomy_theme_viewspages', 0) && ($form_id == 'views_edit_view')
          && (user_access('administer taxonomy_theme') || user_access('assign node themes'))
        ) {
    _taxonomy_theme_alter_views($options_themes, $form_id, $form);
  }
}

/**
 * function _taxonomy_theme_alter_taxonomy().
 * (hook into taxonomy-related administration forms) 
 */
function _taxonomy_theme_alter_taxonomy($options_themes, $form_id, &$form) {
  $forum_vid = variable_get('forum_nav_vocabulary', '');
  $theme_forums = (module_exists('forum') && variable_get('taxonomy_theme_forums_enable', 0));
  $method = variable_get('taxonomy_theme_method', TAXOTHEME_SKIP);
  // alter taxonomy term page
  if ($form_id == 'taxonomy_form_term' && ($method == TAXOTHEME_TERM || $method == TAXOTHEME_ALLTAXO)) {
    if ($form['vid']['#value'] == $forum_vid) {
      if (!$theme_forums) {
        return;
      }
      $default_value = variable_get('taxonomy_theme_forum|'.$form['tid']['#value'], 'default');
    }
    else {
      if ($method == TAXOTHEME_TERM) {
        $default_value = variable_get('taxonomy_theme_term|'.$form['tid']['#value'], 'default');
      }
      else {
        $default_value = variable_get('taxonomy_theme_all|'.$form['tid']['#value'], 'default');
      }
    }
    $form_insert['term_theme']['theme'] = array(
      '#type' => 'select',
      '#title' => t('Theme'),
      '#default_value' => $default_value,
      '#options' => $options_themes,
      '#description' => t('Theme to be assigned to current term (by taxonomy_theme)'),
    );
    array_splice($form, 5, 0, $form_insert);
    $form['#submit']['taxonomy_theme_form_alter_submit'] = array();
  }
  // alter taxonomy vocabulary page
  else if (($form_id == 'taxonomy_form_vocabulary') && ($method == TAXOTHEME_VOCAB)) {
    $form_insert['vocab_theme']['theme'] = array(
      '#type' => 'select',
      '#title' => t('Theme'),
      '#default_value' => variable_get('taxonomy_theme_vocab|'.$form['vid']['#value'], 'default'),
      '#options' => $options_themes,
      '#description' => t('Theme to be assigned to current vocabulary (by taxonomy_theme)'),
    );
    array_splice($form, 10, 0, $form_insert);
    $form['#submit']['taxonomy_theme_form_alter_submit'] = array();
  }
  // alter edit forum page
  else if (($form_id == 'forum_form_forum') && $theme_forums) {
    $form_insert['forum_theme']['theme'] = array(
      '#type' => 'select',
      '#title' => t('Theme'),
      '#default_value' => variable_get('taxonomy_theme_forum|'.$form['tid']['#value'], 'default'),
      '#options' => $options_themes,
      '#description' => t('Theme to be assigned to current forum (by taxonomy_theme)'),
    );
    array_splice($form, 4, 0, $form_insert);
    $form['#submit']['taxonomy_theme_form_alter_submit'] = array();
  }
  // alter node settings pages
  else if (($form_id == 'node_type_form') && ($method == TAXOTHEME_NODETYPE)) {
    $form_insert['node_settings_theme'] = array(
      '#type' => 'fieldset',
      '#title' => t('Theme'),
      '#collapsible' => FALSE,
      '#collapsed' => FALSE,
    );
    $form_insert['node_settings_theme']['theme'] = array(
      '#type' => 'select',
      '#default_value' => variable_get('taxonomy_theme_nodetype|'.$form['old_type']['#value'], 'default'),
      '#options' => $options_themes,
      '#description' => t('Theme to be assigned to current node type (by taxonomy_theme)'),
    );
    array_splice($form, 3, 0, $form_insert);
    $form['#submit']['taxonomy_theme_form_alter_submit'] = array();
  }
}

/**
 * function _taxonomy_theme_alter_nodeform().
 * (hook into node administration form) 
 */
function _taxonomy_theme_alter_nodeform($options_themes, $form_id, &$form) {
  $node_path = drupal_get_path_alias('node/'.$form['nid']['#value']);
  if (module_exists('i18n')) {
    i18n_get_lang_prefix($node_path, true);
  }
  $node_theme = db_result(db_query("SELECT theme FROM {theme_paths} WHERE path = '%s'", $node_path));
  if (!$node_theme) {
    $node_theme = 'default';
  }
  $form['node_form_theme'] = array(
    '#type' => 'fieldset',
    '#title' => t('Theme'),
    '#collapsible' => TRUE,
    '#collapsed' => TRUE,
  );
  $form['node_form_theme']['theme'] = array(
    '#type' => 'select',
    '#default_value' => $node_theme,
    '#options' => $options_themes,
    '#description' => t('Theme to be assigned to current path/node (by taxonomy_theme)'),
  );
  $form['#submit']['taxonomy_theme_form_alter_submit'] = array();
}

/**
 * function _taxonomy_theme_alter_views().
 * (hook into views administration form)
 */
function _taxonomy_theme_alter_views($options_themes, $form_id, &$form) {
  $view_theme = db_result(db_query("SELECT theme FROM {theme_paths} WHERE path = '%s'", $form['page-info']['url']['#default_value']));
  if (!$view_theme) {
    $view_theme = 'default';
  }
  $form['page-info']['theme'] = array(
    '#type' => 'select',
    '#title' => t('Theme'),
    '#default_value' => $view_theme,
    '#options' => $options_themes,
    '#description' => t('Theme to be assigned to current view (by taxonomy_theme)'),
  );
  $form['#submit']['taxonomy_theme_form_alter_submit'] = array();
}

/**
 * function taxonomy_theme_form_alter_submit().
 */
function taxonomy_theme_form_alter_submit($form_id, $form_data) {
  // does the form contain a 'theme' field
  if (!isset($form_data['theme'])) {
    return;
  }
  // submit taxonomy term page
  if ($form_id == 'taxonomy_form_term') {
    if ($form_data['vid'] == variable_get('forum_nav_vocabulary', '')) {
      _taxonomy_theme_variable_set('taxonomy_theme_forum|'.$form_data['tid'], $form_data['theme']);
    }
    else {
      if (variable_get('taxonomy_theme_method', TAXOTHEME_SKIP) == TAXOTHEME_TERM) {
        _taxonomy_theme_variable_set('taxonomy_theme_term|'.$form_data['tid'], $form_data['theme']);
      }
      else {
        _taxonomy_theme_variable_set('taxonomy_theme_all|'.$form_data['tid'], $form_data['theme']);
      }
    }
  }
  // submit taxonomy vocabulary page
  else if ($form_id == 'taxonomy_form_vocabulary') {
    _taxonomy_theme_variable_set('taxonomy_theme_vocab|'.$form_data['vid'], $form_data['theme']);
  }
  // submit edit forum page
  else if ($form_id == 'forum_form_forum') {
    _taxonomy_theme_variable_set('taxonomy_theme_forum|'.$form_data['tid'], $form_data['theme']);
  }
  // submit node settings pages
  else if ($form_id == 'node_type_form') {
    _taxonomy_theme_variable_set('taxonomy_theme_nodetype|'.$form_data['type'], $form_data['theme']);
  }
  // submit node form pages
  else if ($form_id == $form_data['type'].'_node_form') {
    db_query("DELETE FROM {theme_paths} WHERE path = '%s'", 'node/'.$form_data['nid']);
    if (isset($form_data['path']) && !empty($form_data['path'])) {
      db_query("DELETE FROM {theme_paths} WHERE path = '%s'", $form_data['path']);
    }
    if ($form_data['theme'] != 'default') {
      if (isset($form_data['path']) && !empty($form_data['path'])) {
        db_query("INSERT INTO {theme_paths} (path, theme) VALUES ('%s', '%s')", $form_data['path'], $form_data['theme']);
      }
      else {
        db_query("INSERT INTO {theme_paths} (path, theme) VALUES ('%s', '%s')", 'node/'.$form_data['nid'], $form_data['theme']);
      }
    }
    _taxonomy_theme_expcache(); // reset paths cache
  }
  // submit edit view page
  else if (($form_id == 'views_edit_view') && !empty($form_data['url'])) {
    db_query("DELETE FROM {theme_paths} WHERE path = '%s'", drupal_get_normal_path($form_data['url']));
    if ($form_data['theme'] != 'default') {
      db_query("INSERT INTO {theme_paths} (path, theme) VALUES ('%s', '%s')", drupal_get_normal_path($form_data['url']), $form_data['theme']);
    }
    _taxonomy_theme_expcache(); // reset paths cache
  }
}

/**
 * function _taxonomy_theme_options().
 * (create a list of available themes)
 */
function _taxonomy_theme_options($admin_themes = FALSE, $default = TRUE) {
  $themes = system_theme_data();
  $options_themes = array();
  if ($default) {
    $options_themes['default'] = t('System default');
  }
  foreach ($themes as $themex) {
    if ($admin_themes || $themex->status || variable_get('taxonomy_theme_allowall', 0)) {
      $options_themes[$themex->name] = $themex->name;
    }
  }
  
  return $options_themes;
}

/**
 * function _taxonomy_theme_variable_set().
 * (set variable if theme != 'default' else remove variable) 
 */
function _taxonomy_theme_variable_set($key, $value) {
  if ($value == 'default') {
    variable_del($key);
  }
  else {
    variable_set($key, $value);
  }
}

?>
