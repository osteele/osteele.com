<?php

do_action( 'wphone_output_init' );

?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<title><?php bloginfo('name'); _e(' &rsaquo; WPhone', 'wphone'); ?></title>
	<meta http-equiv="Content-Type" content="<?php bloginfo('html_type'); ?>; charset=<?php bloginfo('charset'); ?>" />
<?php if ( $this->iscompat ) : ?>
	<meta name="viewport" content="width=device-width; initial-scale=1.0; maximum-scale=1.0; user-scalable=0;"/>
	<style type="text/css" media="screen">@import "<?php echo $this->interface_url; ?>/gzip.php?file=iui.css&amp;wphone=<?php echo $this->version; ?>";</style>
	<style type="text/css" media="screen">@import "<?php echo $this->interface_url; ?>/gzip.php?file=wphone.css&amp;wphone=<?php echo $this->version; ?>";</style>
	<script type="application/x-javascript" src="<?php echo $this->interface_url; ?>/gzip.php?file=iui.js&amp;wphone=<?php echo $this->version; ?>"></script>
	<script type="application/x-javascript" src="<?php echo $this->interface_url; ?>/gzip.php?file=wphone.js&amp;wphone=<?php echo $this->version; ?>"></script>
<?php else : ?>
	<meta name="viewport" content="width=device-width; initial-scale=1.4; user-scalable=1;"/>
	<style type="text/css" media="screen handheld">
<?php
	// Inline for rendering speed boost on slow devices
	$file       = 'wphone-alt.css';
	$cache_key  = 'x_' . $file . floatval($this->version);
	$cache_flag = 'wphone';
	
	if ( function_exists('wp_cache_get') ) {
		$cache = wp_cache_get( $cache_key, $cache_flag );
	}

	if ( empty($cache) ) {
		$file_contents = preg_replace( '/(\n|\t)/', '', file_get_contents( ABSPATH . $this->folder . '/includes/css/' . $file ) );
		if ( function_exists('wp_cache_set') ) {
			wp_cache_set( $cache_key, $file_contents, $cache_flag );
		}
	} else {
		$file_contents = $cache;
	}
	
	echo apply_filters( 'wphone_litecss', $file_contents );
?>	
	</style>
<?php
	endif;
	do_action( 'wphone_adminhead' );
?>
</head>
<body>
	<div class="toolbar">
<?php if ( $this->iscompat ) : ?>
		<h1 id="pageTitle"></h1>
		<a id="backButton" class="button" href="#"></a>
		<a class="button" href="#primarynav" ><?php _e('Go...', 'wphone'); ?></a>
<?php else : ?>
		<h1 id="pageTitle"><?php _e('WP Admin', 'wphone'); ?></h1>
<?php endif; ?>
	</div>
