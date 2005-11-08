function mt_supportedTextFilters_($args) {

	$supported_textFilters = array();

	// Archaic Markdown check
	/* 
	$current_plugins = get_settings('active_plugins');
	if (!empty($current_plugins)
		&& file_exists(ABSPATH . 'wp-content/plugins/markdown.php')) 
	{	
		$textFilters['label'] = 'Markdown';
		$textFilters['key'] = 'markdown';
		$supported_textFilters[] = $textFilters;
	}
	*/

	include_once(ABSPATH . WPINC . '/functions.php');
	global $wp_filter;
	$tag = 'the_content';
	ksort($wp_filter[$tag]);
	foreach ($wp_filter[$tag] as $priority => $functions) {
		if (!is_null($functions)) {
			foreach($functions as $function) {
				$textFilters['label'] = $function;
				$textFilters['key'] = $function;
				$supported_textFilters[] = $textFilters;
			}
		}
	}
	return $supported_textFilters;
}