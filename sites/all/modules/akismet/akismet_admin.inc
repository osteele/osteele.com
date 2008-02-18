<?php
// $Id: akismet_admin.inc,v 1.3 2006/06/20 01:13:54 markus_petrux Exp $

/**
 * Build the akismet settings form.
 */
function _akismet_settings_form() {
  $form = array();

  $enable_options = array('1' => t('Enabled'), '0' => t('Disabled'));

  $akismet_wpapikey = variable_get('akismet_wpapikey', '');
  if (empty($akismet_wpapikey)) {
    $service_fieldset_collapsed = FALSE;
  }
  else {
    $service_fieldset_collapsed = $is_valid = (akismet_api_cmd_verify_key($akismet_wpapikey) == AKISMET_API_RESULT_SUCCESS ? TRUE : FALSE);
  }
  if ($service_fieldset_collapsed) {
    $service_fieldset_collapsed = variable_get('akismet_connection_enabled', 1);
  }

  $form['service'] = array(
    '#type' => 'fieldset', '#title' => t('Akismet Service Options'),
    '#collapsible' => TRUE, '#collapsed' => $service_fieldset_collapsed
  );
  $form['service']['akismet_wpapikey'] = array(
    '#type' => 'textfield', '#title' => t('WordPress.com API key'),
    '#size' => 30, '#maxlength' => 60,
    '#default_value' => $akismet_wpapikey,
    '#description' => t('Please, enter here your <a href="%wpapikey">WordPress.com API key</a>. If you don\'t have one already, you can get it by simply signing up for a free account at <a href="%wordpress-com">WordPress.com</a>. Note that this information is required in order to use the <a href="%akismet">Akismet Service</a>. Please, consult the <a href="%akismet-faq">Akismet FAQ</a> for further information.',
      array(
        '%wpapikey' => 'http://wordpress.com/api-keys/',
        '%wordpress-com' => 'http://wordpress.com',
        '%akismet' => 'http://akismet.com',
        '%akismet-faq' => 'http://akismet.com/faq/'
      ))
  );
  if (!empty($akismet_wpapikey) && !$is_valid) {
    $form['service']['akismet_wpapikey']['#description'] .= '<div class="marker">'. t('<strong>WARNING: Your API Key doesn\'t seem to be valid!</strong>') .'</div>';
  }
  $form['service']['akismet_connection_enabled'] = array(
    '#type' => 'radios', '#title' => t('Akismet connections'),
    '#options' => $enable_options,
    '#default_value' => variable_get('akismet_connection_enabled', 1),
    '#description' => t('<strong>This option must be enabled in order to perform real requests to the <a href="%akismet">Akismet Service</a>.</strong> You may want to disable this option for testing purposes, however. In this case, the <em>akismet module</em> will operate as normal, except sending real requests to the <a href="%akismet">Akismet Service</a>. ie. no automatic spam detection will be performed and no remote requests will be made when content is manually <em>marked</em>/<em>unmarked</em> as spam.<br />Note: regardless of this option, the <em>akismet module</em> will still connect, from this panel, to validate your <a href="%wpapikey">WordPress.com API key</a>, if specified.',
      array(
        '%akismet' => 'http://akismet.com',
        '%wpapikey' => 'http://wordpress.com/api-keys/'
      ))
  );
  $timeout_options = array();
  for ($n = 1; $n <= 30; $n++) {
    $timeout_options[$n] = $n;
  }
  $form['service']['akismet_connection_timeout'] = array(
    '#type' => 'select', '#title' => t('Connection timeout'),
    '#default_value' => variable_get('akismet_connection_timeout', 10),
    '#options' => $timeout_options,
    '#description' => t('This option allows you to specify the connection timeout in seconds that is used for real time Akismet connections.')
  );

  $form['general'] = array(
    '#type' => 'fieldset', '#title' => t('General Options'),
    '#collapsible' => TRUE, '#collapsed' => TRUE
  );
  $age_options = drupal_map_assoc(array(0, 86400, 259200, 604800, 1209600), 'format_interval');
  $age_options[0] = t('never');
  $age_options[2592000] = t('1 month');
  $form['general']['akismet_version_check_age'] = array(
    '#type' => 'select', '#title' => t('Check for updates every'),
    '#default_value' => variable_get('akismet_version_check_age', 604800),
    '#options' => $age_options,
    '#description' => t('This option allows you to customize how often the akismet module will check for updates. To disable automatic version checks you can set this option to <em>none</em>. This process is automated through cron task, but it may also be triggered when module version is displayed on top of this panel.')
  );
  $age_options = drupal_map_assoc(array(0, 3600, 10800, 21600, 32400, 43200, 86400, 172800, 259200, 604800, 1209600, 1814400), 'format_interval');
  $age_options[0] = t('never');
  $age_options[2592000] = t('1 month');
  $age_options[5184000] = t('2 months');
  $age_options[7776000] = t('3 months');
  $age_options[10368000] = t('4 months');
  $age_options[15768000] = t('6 months');
  $age_options[31536000] = t('1 year');
  $form['general']['akismet_remove_spam_age'] = array(
    '#type' => 'select', '#title' => t('Remove spam older than'),
    '#default_value' => variable_get('akismet_remove_spam_age', 259200),
    '#options' => $age_options,
    '#description' => t('Content marked as <em>spam</em> is still saved into database so it can be reviewed by content administrators. This option allows you to specify how long this information will be kept in the database. <em>Spam</em> older than the age specified here will be automatically removed. Requires crontab.')
  );
  $form['general']['akismet_records_per_page'] = array(
    '#type' => 'select', '#title' => t('Records per page'),
    '#default_value' => variable_get('akismet_records_per_page', 50),
    '#options' => drupal_map_assoc(array(10, 20, 30, 40, 50, 60, 70, 80, 90, 100, 200)),
    '#description' => t('The maximum number of records per page on moderation queue.')
  );
  $form['general']['akismet_blocks_counter'] = array(
    '#type' => 'select', '#title' => t('Number of blocks'),
    '#default_value' => variable_get('akismet_blocks_counter', 1),
    '#options' => array(0=>t('none'), 1=>1, 2=>2, 3=>3, 4=>4, 5=>5),
    '#description' => t('The akismet module may generate a number of blocks for you to display the current spam counter anywhere on your site. The number of blocks is variable to help you keep your <a href="%admin-block">blocks administration panel</a> as clean as possible. This option allows you to specify how many blocks you wish to use. If you do not plan to show the spam counter to your visitors, set this option to <em>none</em>.',
      array(
        '%admin-block' => url('admin/block')
      ))
  );
  $form['general']['akismet_email_enabled'] = array(
    '#type' => 'radios', '#title' => t('E-mail notifications'),
    '#options' => $enable_options,
    '#default_value' => variable_get('akismet_email_enabled', 1),
    '#description' => t('Use this option to <em>enable</em> or <em>disable</em> e-mail notifications to content moderators. If enabled, users with proper permissions are allowed to set, from their user profiles, whether they wish to receive e-mail notications for all new (or updated) posts, just for content needing approval or no notifications at all. Users are notified about content types they are allowed to moderate only.')
  );

  $form['node_options'] = array(
    '#type' => 'fieldset', '#title' => t('Node Options'),
    '#collapsible' => TRUE, '#collapsed' => TRUE
  );
  $form['node_options']['akismet_check_nodetypes'] = array(
    '#type' => 'checkboxes', '#title' => t('Check for spam in these node types'),
    '#options' => node_get_types(),
    '#default_value' => variable_get('akismet_check_nodetypes', NULL),
    '#description' => t('Use this option to <em>enable</em> or <em>disable</em> spam check for nodes of types specified here. When this option is enabled, a request will be sent to the <a href="%akismet">Akismet Service</a>, in real time. If the <a href="%akismet">Akismet Service</a> was down, nodes would simply be queued for manual moderation. Users with <a href="%admin-access">%admin-nodes</a> permission and <a href="%admin-access">spam moderators</a> are exempt from this check.',
      array(
        '%akismet' => 'http://akismet.com',
        '%admin-nodes' => t('administer nodes'),
        '%admin-access' => url('admin/access')
      ))
  );
  $form['node_options']['akismet_node_publish_links'] = array(
    '#type' => 'radios', '#title' => t('Show publish/unpublish links'),
    '#options' => $enable_options,
    '#default_value' => variable_get('akismet_node_publish_links', 0),
    '#description' => t('Use this option to <em>enable</em> or <em>disable</em> links for <em>publish</em>/<em>unpublish</em> operations in nodes. If enabled, these links will only be displayed to <a href="%admin-access">spam moderators</a> and users with <a href="%admin-access">%admin-nodes</a> permission.',
      array(
        '%admin-nodes' => t('administer nodes'),
        '%admin-access' => url('admin/access')
      ))
  );
  $form['node_options']['akismet_node_spam_links'] = array(
    '#type' => 'radios', '#title' => t('Show submit spam/ham links'),
    '#options' => $enable_options,
    '#default_value' => variable_get('akismet_node_spam_links', 0),
    '#description' => t('Use this option to <em>enable</em> or <em>disable</em> links for <em>submit spam</em>/<em>ham</em> operations in nodes. If enabled, these links will only be displayed to <a href="%admin-access">spam moderators</a> and users with <a href="%admin-access">%admin-nodes</a> permission.',
      array(
        '%admin-nodes' => t('administer nodes'),
        '%admin-access' => url('admin/access')
      ))
      .'<br />'. t('<strong>Note:</strong> To interact fully with the <a href="%akismet">Akismet Service</a> you really should try putting data back into the system as well as just taking it out. If it is at all possible, please use these links to submit missed spam and false positives (ham), otherwise Akismet will never learn from its mistakes. Thank you.',
      array(
        '%akismet' => 'http://akismet.com'
      ))
  );

  if (module_exist('comment')) {
    $form['comment_options'] = array(
      '#type' => 'fieldset', '#title' => t('Comment Options'),
      '#collapsible' => TRUE, '#collapsed' => TRUE
    );
    $form['comment_options']['akismet_check_comments'] = array(
      '#type' => 'radios', '#title' => t('Check for spam in comments'),
      '#options' => $enable_options,
      '#default_value' => variable_get('akismet_check_comments', 1),
      '#description' => t('Use this option to <em>enable</em> or <em>disable</em> spam check for comments. When this option is enabled, a request will be sent to the <a href="%akismet">Akismet Service</a>, in real time. If the <a href="%akismet">Akismet Service</a> was down, comments would simply be queued for manual moderation. Users with <a href="%admin-access">%admin-comments</a> permission and <a href="%admin-access">spam moderators</a> are exempt from this check.',
        array(
          '%akismet' => 'http://akismet.com',
          '%admin-comments' => t('administer comments'),
          '%admin-access' => url('admin/access')
        ))
    );
    $form['comment_options']['akismet_comment_publish_links'] = array(
      '#type' => 'radios', '#title' => t('Show publish/unpublish links'),
      '#options' => $enable_options,
      '#default_value' => variable_get('akismet_comment_publish_links', 1),
      '#description' => t('Use this option to <em>enable</em> or <em>disable</em> links for <em>publish</em>/<em>unpublish</em> operations in comments. If enabled, these links will only be displayed to <a href="%admin-access">spam moderators</a> and users with <a href="%admin-access">%admin-comments</a> permission.',
        array(
          '%admin-comments' => t('administer comments'),
          '%admin-access' => url('admin/access')
        ))
    );
    $form['comment_options']['akismet_comment_spam_links'] = array(
      '#type' => 'radios', '#title' => t('Show submit spam/ham links'),
      '#options' => $enable_options,
      '#default_value' => variable_get('akismet_comment_spam_links', 1),
      '#description' => t('Use this option to <em>enable</em> or <em>disable</em> links for <em>submit spam</em>/<em>ham</em> operations in comments. If enabled, these links will only be displayed to <a href="%admin-access">spam moderators</a> and users with <a href="%admin-access">%admin-comments</a> permission.',
        array(
          '%admin-comments' => t('administer comments'),
          '%admin-access' => url('admin/access')
        ))
        .'<br />'. t('<strong>Note:</strong> To interact fully with the <a href="%akismet">Akismet Service</a> you really should try putting data back into the system as well as just taking it out. If it is at all possible, please use these links to submit missed spam and false positives (ham), otherwise Akismet will never learn from its mistakes. Thank you.',
        array(
          '%akismet' => 'http://akismet.com'
        ))
    );
  }

  $date_formats = array(
    'F j, Y', 'j F, Y', 'Y, F j',
    'M j, Y', 'j M, Y', 'Y, M j',
    'Y/m/d', 'm/d/Y', 'd/m/Y',
    'Y-m-d', 'm-d-Y', 'd-m-Y'
  );
  $date_options = array();
  $now = time();
  foreach ($date_formats as $format) {
    $date_options[$format] = format_date($now, 'custom', $format);
  }

  $form['counter_options'] = array(
    '#type' => 'fieldset', '#title' => t('Spam Counter Options'),
    '#collapsible' => TRUE, '#collapsed' => TRUE
  );
  $form['counter_options']['akismet_counter_spam'] = array(
    '#type' => 'textfield', '#title' => t('Spam counter'),
    '#default_value' => akismet_get_spam_counter(),
    '#size' => 10, '#maxlength' => 10,
    '#description' => t('This counter is incremented for every spam caught by Akismet.')
  );
  $form['counter_options']['akismet_counter_since'] = array(
    '#type' => 'date', '#title' => t('Counting since'),
    '#default_value' => variable_get('akismet_counter_since', array('day' => date('j'), 'month' => date('n'), 'year' => date('Y'))),
    '#description' => t('This is the date that will tell your visitors when your Akismet spam counter started to increment.')
  );
  $form['counter_options']['akismet_counter_date_format'] = array(
    '#type' => 'select', '#title' => t('Date format'),
    '#default_value' => variable_get('akismet_counter_date_format', $date_formats[0]),
    '#options' => $date_options,
    '#description' => t('Date format used to render the <em>Counting since</em> date.')
  );

  $form['anti_spambot'] = array(
    '#type' => 'fieldset', '#title' => t('Anti-Spambot Options'),
    '#collapsible' => TRUE, '#collapsed' => TRUE,
    '#description' => t('The goal of this section is not to replace anything that the <a href="%akismet">Akismet Service</a> itself can do a lot better than us, but to provide a set of simple rules aimed to prevent Denial of Service (DoS) situations that could be caused by certain spambots.',
      array(
        '%akismet' => 'http://akismet.com'
      ))
  );
  $delay_options = drupal_map_assoc(array(0, 30, 60, 90, 120, 150, 180), 'format_interval');
  $delay_options[0] = t('none');
  $form['anti_spambot']['akismet_antispambot_delay'] = array(
    '#type' => 'select', '#title' => t('Delay when spam is detected'),
    '#default_value' => variable_get('akismet_antispambot_delay', 60),
    '#options' => $delay_options,
    '#description' => t('Use this option to delay the response when content has been identified as spam or to requests that match the rules defined below.')
  );
  $anti_spambot_rules = array(
    'ip' => t('IP addresses used by known spammers.'),
    'mail' => t('E-mail addresses used by known spammers.'),
    'body' => t('Content that has already been identified as spam.'),
  );
  $form['anti_spambot']['akismet_antispambot_rules'] = array(
    '#type' => 'checkboxes', '#title' => t('Identify spambots by'),
    '#options' => $anti_spambot_rules,
    '#default_value' => variable_get('akismet_antispambot_rules', NULL),
    '#description' => t('These rules will be applied before sending any request to the <a href="%akismet">Akismet Service</a>. If a request to send content matches any of these rules, the actions defined below will be triggered. Requests to send content are checked against spam that is stored locally (visible from the <a href="%moderation-queue">moderation queue</a>).',
      array(
        '%akismet' => 'http://akismet.com',
        '%moderation-queue' => url('admin/akismet')
      ))
  );
  $anti_spambot_actions = array(
    'none' => t('None (only the delay specified above, if any).'),
    '503' => t('HTTP error 503 (Service Unavailable), showing a simple blank page.'),
    '403' => t('HTTP error 403 (Forbidden), showing a simple blank page.'),
    '403d' => t('HTTP error 403 (Forbidden), showing a Drupal generated page.')
  );
  $form['anti_spambot']['akismet_antispambot_action'] = array(
    '#type' => 'radios', '#title' => t('Actions against spambots'),
    '#options' => $anti_spambot_actions,
    '#default_value' => variable_get('akismet_antispambot_action', '503'),
    '#description' => t('Use this option to specify what to do against spambots identified by any of the above rules. When a <em>HTTP error</em> is generated (403 or 503), no request to the <a href="%akismet">Akismet Service</a> will be made, the request to post content will not be stored into database and no further moderator notifications will be sent. In any case, when a rule matches, a record of the event will be <a href="%admin-logs">logged</a> for further analysis.',
      array(
        '%akismet' => 'http://akismet.com',
        '%admin-logs' => url('admin/logs')
      ))
  );

  return $form;
}

/**
 * Moderation queue operations.
 */
function akismet_moderator_operations($mode, $submode) {
  // Build operations array; based on current mode.
  if ($mode == 'nodes') {
    $operations = array(
      'submit-spam' => array(
        'title' => (variable_get('akismet_connection_enabled', 1) ? t('Submit selected nodes as spam') : t('Mark selected nodes as spam')),
        'confirm' => (variable_get('akismet_connection_enabled', 1) ? t('Are you sure you want to submit these nodes as spam?') : t('Are you sure you want to mark these nodes as spam?')),
        'button' => (variable_get('akismet_connection_enabled', 1) ? t('Submit nodes as spam') : t('Mark nodes as spam')),
      ),
      'submit-ham' => array(
        'title' => (variable_get('akismet_connection_enabled', 1) ? t('Submit selected nodes as ham') : t('Mark selected nodes as ham')),
        'confirm' => (variable_get('akismet_connection_enabled', 1) ? t('Are you sure you want to submit these nodes as ham?') : t('Are you sure you want to mark these nodes as ham?')),
        'button' => (variable_get('akismet_connection_enabled', 1) ? t('Submit nodes as ham') : t('Mark nodes as ham')),
      ),
      'publish' => array(
        'title' => t('Publish selected nodes'),
        'confirm' => t('Are you sure you want to publish these nodes?'),
        'button' => t('Publish nodes'),
      ),
      'unpublish' => array(
        'title' => t('Unpublish selected nodes'),
        'confirm' => t('Are you sure you want to unpublish these nodes?'),
        'button' => t('Unpublish nodes'),
      ),
      'delete' => array(
        'title' => t('Delete selected nodes'),
        'confirm' => t('Are you sure you want to delete these nodes and all their comments?'),
        'button' => t('Delete nodes'),
        'warning' => t('This action cannot be undone.')
      )
    );
  }
  else if ($mode == 'comments') {
    $operations = array(
      'submit-spam' => array(
        'title' => (variable_get('akismet_connection_enabled', 1) ? t('Submit selected comments as spam') : t('Mark selected comments as spam')),
        'confirm' => (variable_get('akismet_connection_enabled', 1) ? t('Are you sure you want to submit these comments as spam?') : t('Are you sure you want to mark these comments as spam?')),
        'button' => (variable_get('akismet_connection_enabled', 1) ? t('Submit comments as spam') : t('Mark comments as spam')),
      ),
      'submit-ham' => array(
        'title' => (variable_get('akismet_connection_enabled', 1) ? t('Submit selected comments as ham') : t('Mark selected comments as ham')),
        'confirm' => (variable_get('akismet_connection_enabled', 1) ? t('Are you sure you want to submit these comments as ham?') : t('Are you sure you want to mark these comments as ham?')),
        'button' => (variable_get('akismet_connection_enabled', 1) ? t('Submit comments as ham') : t('Mark comments as ham')),
      ),
      'publish' => array(
        'title' => t('Publish selected comments'),
        'confirm' => t('Are you sure you want to publish these comments?'),
        'button' => t('Publish comments'),
      ),
      'unpublish' => array(
        'title' => t('Unpublish selected comments'),
        'confirm' => t('Are you sure you want to unpublish these comments?'),
        'button' => t('Unpublish comments'),
      ),
      'delete' => array(
        'title' => t('Delete selected comments'),
        'confirm' => t('Are you sure you want to delete these comments and all their replies?'),
        'button' => t('Delete comments'),
        'warning' => t('This action cannot be undone.')
      )
    );
  }
  else { // Unknown mode!
    return array();
  }

  // Unset redundant operations; based on current submode.
  if ($submode == 'spam') {
    unset($operations['submit-spam']);
  }
  else if ($submode == 'unpublished') {
    unset($operations['unpublish']);
  }
  else if ($submode == 'published') {
    unset($operations['publish']);
  }
  else { // Unknown submode!
    return array();
  }

  return $operations;
}

/**
 * Menu callback; Moderation queue.
 *
 * @param string Mode: overview (default), nodes, comments.
 * @param string Submode: spam (default), unpublished, published.
 */
function akismet_callback_queue($mode = '', $submode = '') {
  // Make sure we're dealing with a valid mode and submode.
  $valid_modes = array('nodes', 'comments');
  $valid_submodes = array(
    'spam' => t('Spam'),
    'unpublished' => t('Unpublished'),
    'published' => t('Published')
  );
  if (empty($mode)) {
    $mode = 'overview';
  }
  else if (!in_array($mode, $valid_modes)) {
    drupal_not_found();
    return;
  }
  if (empty($submode)) {
    $submode = 'spam';
  }
  else if (!isset($valid_submodes[$submode])) {
    drupal_not_found();
    return;
  }

  // Compute exactly what the current user is allowed to moderate.
  $moderator_types = akismet_get_moderator_types();
  $moderator_types_count = count($moderator_types);
  $allowed_comments = (isset($moderator_types['comments']) ? TRUE : FALSE);
  $allowed_nodes = $moderator_types;
  if ($allowed_comments) {
    unset($allowed_nodes['comments']);
  }
  $allowed_nodes_count = count($allowed_nodes);

  // Make sure the user has some kind of content administration/moderation permission.
  if ($allowed_nodes_count <= 0 && !$allowed_comments) {
    drupal_access_denied();
    return;
  }

  // Dynamically build the queries using a write once method.
  if ($allowed_nodes_count > 0) {
    $sql_nodetypes = array();
    foreach ($allowed_nodes as $type => $name) {
      $sql_nodetypes[] = '\''. $type .'\'';
    }
    $sql_nodetypes = implode(', ', $sql_nodetypes);
    $sql_from = 'FROM {node} n LEFT JOIN {akismet_spam_marks} s ON s.content_id = n.nid AND s.content_type = \'node\'';
    $sql_where = 'WHERE n.type IN ('. $sql_nodetypes .') AND (%cond)';
    $sql_nodes_cond = array(
      'spam' => 's.content_id IS NOT NULL',
      'unpublished' => 'n.status = 0',
      'published' => 'n.status = 1'
    );
    $sql_nodes_stmt = 'SELECT n.*, u.name, IFNULL(s.content_id, 0) AS spam_mark '. $sql_from .' INNER JOIN {users} u ON n.uid = u.uid '. $sql_where;
    $sql_nodes_cnt = 'SELECT COUNT(*) AS cnt '. $sql_from .' '. $sql_where;
  }
  if ($allowed_comments) {
    $sql_from = 'FROM {comments} c LEFT JOIN {akismet_spam_marks} s ON s.content_id = c.cid AND s.content_type = \'comment\'';
    $sql_where = 'WHERE (%cond)';
    $sql_comments_cond = array(
      'spam' => 's.content_id IS NOT NULL',
      'unpublished' => 'c.status = '. COMMENT_NOT_PUBLISHED,
      'published' => 'c.status = '. COMMENT_PUBLISHED
    );
    $sql_comments_stmt = 'SELECT c.*, u.name AS registered_name, IFNULL(s.content_id, 0) AS spam_mark '. $sql_from .' INNER JOIN {users} u ON c.uid = u.uid '. $sql_where;
    $sql_comments_cnt = 'SELECT COUNT(*) AS cnt '. $sql_from .' '. $sql_where;
  }

  // Present the overview page (default).
  if ($mode == 'overview') {
    $items = array();
    if ($allowed_nodes_count > 0) {
      $subitems = array();
      foreach ($valid_submodes as $key => $title) {
        $sql_cnt = str_replace('%cond', $sql_nodes_cond[$key], $sql_nodes_cnt);
        $count = db_result(db_query(db_rewrite_sql($sql_cnt)));
        $path = 'admin/akismet/nodes'. ($key == 'spam' ? '' : '/'. $key);
        $label = ($count > 0 ? l($title, $path) : $title);
        $subitems[] = '<p><strong>'. $label .': '. $count .'</strong></p>';
      }
      $items[] = '<h4>'. t('Nodes') .'</h4>'. theme('item_list', $subitems);
    }
    if ($allowed_comments) {
      $subitems = array();
      foreach ($valid_submodes as $key => $title) {
        $sql_cnt = str_replace('%cond', $sql_comments_cond[$key], $sql_comments_cnt);
        $count = db_result(db_query(db_rewrite_sql($sql_cnt)));
        $path = 'admin/akismet/comments'. ($key == 'spam' ? '' : '/'. $key);
        $label = ($count > 0 ? l($title, $path) : $title);
        $subitems[] = '<p><strong>'. $label .': '. $count .'</strong></p>';
      }
      $items[] = '<h4>'. t('Comments') .'</h4>'. theme('item_list', $subitems);
    }
    return '<h3>'. t('Summary of content:') .'</h3>'. theme('item_list', $items);
  }

  // Request confirmation for an already submitted operation?
  $edit = (isset($_POST['edit']) ? $_POST['edit'] : array());
  if (count($edit['items']) > 0) {
    return akismet_confirm_multiple_operation();
  }

  // Build the moderation queue form.
  $form = array();
  $form['options'] = array(
    '#type' => 'fieldset', '#title' => t('Moderator actions'),
    '#prefix' => '<div class="container-inline">', '#suffix' => '</div>'
  );
  $options = array('' => t('<select operation>'));
  foreach (akismet_moderator_operations($mode, $submode) as $key => $operation_info) {
    $options[$key] = $operation_info['title'];
  }
  $form['options']['operation'] = array('#type' => 'select', '#options' => $options, '#default_value' => '');
  $form['options']['submit'] = array('#type' => 'submit', '#value' => t('Submit'));

  if ($mode == 'nodes') {
    $sql_stmt = str_replace('%cond', $sql_nodes_cond[$submode], $sql_nodes_stmt);
    $sql_cnt = str_replace('%cond', $sql_nodes_cond[$submode], $sql_nodes_cnt);
    $form['header'] = array('#type' => 'value', '#value' => array(
      theme('akismet_check_all'),
      array('data' => t('Title'), 'field' => 'title'),
      array('data' => t('Type'), 'field' => 'type'),
      array('data' => t('Author'), 'field' => 'name'),
      array('data' => t('Status'), 'field' => 'status'),
      array('data' => t('Last changed'), 'field' => 'changed', 'sort' => 'desc')
    ));
  }
  else { // comments
    $sql_stmt = str_replace('%cond', $sql_comments_cond[$submode], $sql_comments_stmt);
    $sql_cnt = str_replace('%cond', $sql_comments_cond[$submode], $sql_comments_cnt);
    $form['header'] = array('#type' => 'value', '#value' => array(
      theme('akismet_check_all'),
      array('data' => t('Subject'), 'field' => 'subject'),
      array('data' => t('Author'), 'field' => 'name'),
      array('data' => t('Status'), 'field' => 'status'),
      array('data' => t('Last changed'), 'field' => 'timestamp', 'sort' => 'desc')
    ));
  }

  $records_per_page = variable_get('akismet_records_per_page', 50);
  $result = pager_query($sql_stmt . tablesort_sql($form['header']['#value']), $records_per_page, 0, $sql_cnt);
  $items = array();
  $now = time();
  while ($content = db_fetch_object($result)) {
    if ($mode == 'nodes') {
      $items[$content->nid] = '';
      $form['title'][$content->nid] = array('#value' => l($content->title, 'node/'. $content->nid, array('title' => truncate_utf8($content->body, 128))) .' '. theme('mark', node_mark($content->nid, $content->changed)));
      $form['type'][$content->nid] = array('#value' => node_get_name($content));
      $form['author'][$content->nid] = array('#value' => theme('username', $content));
      $form['status'][$content->nid] = array('#value' => ($content->status ? t('published') : t('not published')));
      if ($content->spam_mark) {
        $form['status'][$content->nid]['#value'] .= t('/spam');
      }
      $form['created'][$content->nid] = array('#value' => t('%time ago', array('%time' => format_interval($now - $content->changed))));
    }
    else { // comments
      $items[$content->cid] = '';
      $content->name = $content->uid ? $content->registered_name : $content->name;
      $form['title'][$content->cid] = array('#value' => l($content->subject, 'node/'. $content->nid, array('title' => truncate_utf8($content->comment, 128)), NULL, 'comment-'. $content->cid) .' '. theme('mark', node_mark($content->nid, $content->timestamp)));
      $form['author'][$content->cid] = array('#value' => theme('username', $content));
      $form['status'][$content->cid] = array('#value' => ($content->status == COMMENT_PUBLISHED ? t('published') : t('not published')));
      if ($content->spam_mark) {
        $form['status'][$content->cid]['#value'] .= t('/spam');
      }
      $form['created'][$content->cid] = array('#value' => t('%time ago', array('%time' => format_interval($now - $content->timestamp))));
    }
  }

  $form['mode'] = array('#type' => 'hidden', '#value' => $mode);
  $form['submode'] = array('#type' => 'hidden', '#value' => $submode);
  $form['items'] = array('#type' => 'checkboxes', '#options' => $items);
  $form['pager'] = array('#value' => theme('pager', NULL, $records_per_page, 0));
  return drupal_get_form('akismet_callback_queue', $form);
}

function theme_akismet_check_all() {
  $title = check_plain(t('Click here to mark/unmark all items'));
  $output = <<<EOT
<input id="akismet-check-all" type="checkbox" onclick="akismet_check_all(this.form, this.checked)" title="{$title}" />
<script type="text/javascript"><!--//--><![CDATA[//><!--
function akismet_check_all(f, checked) {
  for (var i=0; i < f.elements.length; i++) {
    if (f.elements[i].type == 'checkbox') {
      f.elements[i].checked = checked;
    }
  }
}
//--><!]]></script>
EOT;
  return $output;
}

/**
 * Theme callback; render the moderation queue form.
 */
function theme_akismet_callback_queue($form) {
  $mode = $form['mode']['#value'];
  $submode = $form['submode']['#value'];
  $output = form_render($form['options']);
  $rows = array();
  if (isset($form['author']) && is_array($form['author'])) {
    foreach (element_children($form['author']) as $key) {
      $row = array();
      $row[] = array('data' => form_render($form['items'][$key]), 'align' => 'center');
      $row[] = form_render($form['title'][$key]);
      if ($mode == 'nodes') {
        $row[] = form_render($form['type'][$key]);
      }
      $row[] = form_render($form['author'][$key]);
      $row[] = form_render($form['status'][$key]);
      $row[] = form_render($form['created'][$key]);
      $rows[] = $row;
    }
  }
  else {
    if ($submode == 'spam') {
      $message = ($mode == 'nodes' ? t('There is no spam in the nodes moderation queue.') : t('There is no spam in the comments moderation queue.')) .'<br />'. t('It must be your lucky day! ;-)');
    }
    else if ($submode == 'unpublished') {
      $message = ($mode == 'nodes' ? t('There are no unpublished nodes in the moderation queue.') : t('There are no unpublished comments in the moderation queue.'));
    }
    else { // published
      $message = ($mode == 'nodes' ? t('There are no published nodes.') : t('There are no published comments.'));
    }
    $rows[] = array(array('data' => $message, 'align' => 'center', 'colspan' => ($mode == 'nodes' ? '6' : '5')));
  }
  $output .= theme('table', $form['header']['#value'], $rows);
  if ($form['pager']['#value']) {
    $output .= form_render($form['pager']);
  }
  $output .= form_render($form);
  return $output;
}

/**
 * Form API callback; Validate the moderation queue form.
 */
function akismet_callback_queue_validate($form_id, $edit) {
  $mode = $edit['mode'];
  $submode = $edit['submode'];
  $operation = $edit['operation'];
  $valid_operations = akismet_moderator_operations($mode, $submode);

  if (!isset($valid_operations[$operation])) {
    form_set_error('', t('Please, choose a valid operation.'));
  }
  $edit['items'] = array_diff($edit['items'], array(0));
  if (count($edit['items']) == 0) {
    if ($operation == 'delete') {
      form_set_error('', t('Please, select some items to perform the delete operation.'));
    }
    else {
      form_set_error('', t('Please, select some items to perform the action on.'));
    }
  }
}

/**
 * List the selected items and verify that the admin really wants to delete them.
 */
function akismet_confirm_multiple_operation() {
  $edit = $_POST['edit'];
  $mode = $edit['mode'];
  $submode = $edit['submode'];
  $operation = $edit['operation'];
  $valid_operations = akismet_moderator_operations($mode, $submode);

  // Make sure we deal with a valid combination of mode, submode and operation.
  if (!isset($valid_operations[$operation])) {
    return;
  }

  $confirm_message = '<strong>'. $valid_operations[$operation]['confirm'] .'</strong>';
  $confirm_button = $valid_operations[$operation]['button'];
  $confirm_warning = '<p>'. (isset($valid_operations[$operation]['warning']) ? $valid_operations[$operation]['warning'] : '') .'</p>';
  $content_type = ($mode == 'nodes' ? 'node' : 'comment');

  $form = array();
  $form['items'] = array('#prefix' => '<ul>', '#suffix' => '</ul>', '#tree' => TRUE);
  // array_filter() returns only elements with actual values
  foreach (array_filter($edit['items']) as $content_id => $value) {
    if ($content = akismet_content_load($content_type, $content_id)) {
      $title = '&quot;'. check_plain($content_type == 'node' ? $content->title : $content->subject) .'&quot;, '. t('by') .' '. theme('username', $content);
      $form['items'][$content_id] = array('#type' => 'hidden', '#value' => $content_id, '#prefix' => '<li>', '#suffix' => $title .'</li>');
    }
  }
  $form['mode'] = array('#type' => 'hidden', '#value' => $mode);
  $form['submode'] = array('#type' => 'hidden', '#value' => $submode);
  $form['operation'] = array('#type' => 'hidden', '#value' => $operation);

  // Redirect to a non-existent menu item to make tabs disappear.
  menu_set_active_item('');

  $path = 'admin/akismet/'. $mode;
  if ($submode != 'spam') {
    $path .= '/'. $submode;
  }
  return confirm_form('akismet_confirm_multiple_operation', $form, $confirm_message, $path, $confirm_warning, $confirm_button, t('Cancel'));
}

/**
 * confirm_form callback; perform the actual operation against selected content.
 */
function akismet_confirm_multiple_operation_submit($form_id, $edit) {
  $mode = $edit['mode'];
  $submode = $edit['submode'];
  $operation = $edit['operation'];
  $valid_operations = akismet_moderator_operations($mode, $submode);

  // Make sure we deal with a valid combination of mode, submode and operation.
  if (!isset($valid_operations[$operation])) {
    return 'admin/akismet';
  }

  if ($edit['confirm']) {
    $content_type = ($mode == 'nodes' ? 'node' : 'comment');
    foreach ($edit['items'] as $content_id => $value) {
      if ($operation == 'delete') {
        akismet_content_delete($content_type, $content_id);
        $message = ($mode == 'nodes' ? t('The nodes have been deleted.') : t('The comments have been deleted.'));
      }
      else if ($content = akismet_content_load($content_type, $content_id)) {
        if ($content_type == 'node') {
          $is_published = ($content->status ? TRUE : FALSE);
        }
        else { // comment
          $is_published = ($content->status == COMMENT_PUBLISHED ? TRUE : FALSE);
        }
        $is_spam = akismet_content_is_spam($content_type, $content_id);

        if ($operation == 'submit-spam' && !$is_spam) {
          akismet_content_spam_operation($content_type, $content, 'submit-spam');
        }
        else if ($operation == 'submit-ham' && $is_spam) {
          akismet_content_spam_operation($content_type, $content, 'submit-ham');
        }

        if (in_array($operation, array('unpublish','submit-spam')) && $is_published) {
          akismet_content_publish_operation($content_type, $content, 'unpublish');
        }
        else if (in_array($operation, array('publish','submit-ham')) && !$is_published) {
          akismet_content_publish_operation($content_type, $content, 'publish');
        }

        $message = ($mode == 'nodes' ? t('The nodes have been updated.') : t('The comments have been updated.'));
      }
    }
    drupal_set_message($message);
  }

  $path = 'admin/akismet/'. $mode;
  if ($submode != 'spam') {
    $path .= '/'. $submode;
  }
  return $path;
}
