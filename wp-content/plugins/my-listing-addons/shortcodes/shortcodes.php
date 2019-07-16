<?php

add_action( 'mylisting/shortcodes/init', function( $shortcodes ) {
	$shortcodes->register( [
		trailingslashit( CASE27_PLUGIN_DIR ) . 'shortcodes/icon.php',
		trailingslashit( CASE27_PLUGIN_DIR ) . 'shortcodes/button.php',
		trailingslashit( CASE27_PLUGIN_DIR ) . 'shortcodes/format.php',
		trailingslashit( CASE27_PLUGIN_DIR ) . 'shortcodes/search-form.php',
		trailingslashit( CASE27_PLUGIN_DIR ) . 'shortcodes/categories.php',
		trailingslashit( CASE27_PLUGIN_DIR ) . 'shortcodes/quick-search.php',
	] );
});