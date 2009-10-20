<?php

// Load WordPress
require_once( '../../../../wp-config.php' );

// Define a fixed list of allowed files to avoid exploits
$validfiles = apply_filters( 'wphone_gzipvalidfiles',
	array(
		'iui.css'     => array( 'path' => 'iui/iuix.css', 'mime' => 'text/css' ),
		'iui.js'      => array( 'path' => 'iui/iuix.js', 'mime' => 'application/x-javascript' ),
		'wphone.css'  => array( 'path' => 'css/wphone.css', 'mime' => 'text/css' ),
		'wphone.js'   => array( 'path' => 'js/wphone.js', 'mime' => 'application/x-javascript' )
	)
);

$file = strip_tags(trim($_GET['file']));

if ( !array_key_exists( $file, $validfiles ) ) {
	status_header( 404 );
	nocache_headers();
	_e( 'Invalid file selected.', 'wphone' );
	exit();
}

do_action( 'wphone_gzipfiles' );

// Attempt to gzip the contents
if ( FALSE == ob_start( 'ob_gzhandler' ) ) {
	ob_start();
}

// Send cache headers ( modification of cache_javascript_headers() )
if ( FALSE == headers_sent() ) {
	$expiresOffset = 864000; // 10 days
	header( 'Content-Type: ' . $validfiles[$file]['mime'] . '; charset=' . get_bloginfo('charset') );
	header( 'Vary: Accept-Encoding' ); // Handle proxies
	header( 'Expires: ' . gmdate("D, d M Y H:i:s", time() + $expiresOffset) . ' GMT' );
}

$cache_key  = 'x_' . $file . floatval($_GET['wphone']);
$cache_flag = 'wphone';

if ( function_exists('wp_cache_get') ) {
	$cache = wp_cache_get( $cache_key, $cache_flag );
}

if ( empty($cache) ) {
	$file_contents = file_get_contents( './' . $validfiles[$file]['path'] );
	
	switch ($file) {
		case 'iui.css':
			$file_contents = preg_replace( '/(\w*?)\.(png|jpg|gif|css)/', './iui/\1.\2', $file_contents );
			break;
		case 'wphone.css':
			$file_contents = str_replace( '../', './', $file_contents );
			break;
	}
	
	if ( FALSE === strstr($file, 'iui') ) {
		if ( strstr($file, '.js') ) {
			// DEBUG: having a prob when stripping new lines in JS (wphone.js).
			// Leads to js syntax error. Will find source and fix.
			$file_contents = preg_replace( '/\t/', '', $file_contents );
		} else {
			$file_contents = preg_replace( '/\n|\t/', '', $file_contents );
		}
	}
	
	if ( function_exists('wp_cache_set') ) {
		wp_cache_set( $cache_key, $file_contents, $cache_flag );
	}
} else {
	$file_contents = $cache;
}

// Output file contents
echo apply_filters( 'wphone_gzipfilecontents', $file_contents );

?>