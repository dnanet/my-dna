<?php

if ( ! defined( 'CASE27_THEME_DIR' ) ) {
	define( 'CASE27_THEME_DIR', get_template_directory() );
}

if ( ! defined( 'CASE27_INTEGRATIONS_DIR' ) ) {
	define( 'CASE27_INTEGRATIONS_DIR', CASE27_THEME_DIR . '/includes/integrations' );
}

if ( ! defined( 'CASE27_ASSETS_DIR' ) ) {
	define( 'CASE27_ASSETS_DIR', CASE27_THEME_DIR . '/assets' );
}

if ( ! defined( 'CASE27_ENV' ) ) {
	define( 'CASE27_ENV', 'production' );
}

if ( ! defined( 'PT_OCDI_PATH' ) ) {
	define( 'PT_OCDI_PATH', trailingslashit( CASE27_THEME_DIR ) . 'includes/extensions/demo-import/plugin/' );
}

if ( ! defined( 'PT_OCDI_URL' ) ) {
	define( 'PT_OCDI_URL', trailingslashit( get_template_directory_uri() ) . 'includes/extensions/demo-import/plugin/' );
}

if ( ! defined( 'CASE27_THEME_VERSION' ) ) {
	if (CASE27_ENV == 'dev') {
		define( 'CASE27_THEME_VERSION', rand(1, 10e3) );
	} else {
		define( 'CASE27_THEME_VERSION', wp_get_theme( get_template() )->get('Version') );
	}
}

if ( ! defined( 'ELEMENTOR_PARTNER_ID' ) ) {
	define( 'ELEMENTOR_PARTNER_ID', 2124 );
}

// Load textdomain early to include strings that are localized before
// the 'after_setup_theme' is called.
load_theme_textdomain( 'my-listing', CASE27_THEME_DIR . '/languages' );

// Load classes.
require_once CASE27_THEME_DIR . '/includes/autoload.php';
