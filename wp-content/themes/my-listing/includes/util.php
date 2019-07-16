<?php
// Debugging helper
if ( ! function_exists('mlog') ) {
	function mlog( $message = null ) {
		if ( $message !== null ) {
			return MyListing\Utils\Logger\Logger::instance()->info( $message );
		}

		return MyListing\Utils\Logger\Logger::instance();
	}
}

// Debugging helper
if ( ! function_exists('dump') ) {
	function dump() {
		call_user_func_array( [ MyListing\Utils\Logger\Logger::instance(), 'dump' ], func_get_args() );
	}
}

// Debugging helper
if ( ! function_exists('dd') ) {
	function dd() {
		call_user_func_array( [ MyListing\Utils\Logger\Logger::instance(), 'dd' ], func_get_args() );
	}
}

// Helper function for accessing mylisting\includes\app instance.
function mylisting() {
	return MyListing\Includes\App::instance();
}

// Alias for `mylisting()->helpers()`
function c27() {
	return mylisting()->helpers();
}

// locate_template wrapper, with $data parameter for
// a standard way to pass data to templates.
function mylisting_locate_template( $template, $data = [] ) {
	if ( $template = locate_template( $template ) ) {
		require $template;
	}
}

function mylisting_check_ajax_referrer( $action = 'c27_ajax_nonce', $query_arg = 'security', $die = true ) {
	if ( CASE27_ENV === 'dev' ) {
		return true;
	}

	return check_ajax_referer( $action, $query_arg, $die );
}

function mylisting_custom_taxonomies( $key = 'slug', $value = 'label' ) {
	return MyListing\Ext\Custom_Taxonomies\Custom_Taxonomies::instance()->get_custom_taxonomies_list( $key, $value );
}

// Start.
mylisting();

// Helpers.
mylisting()->register( 'helpers', MyListing\Utils\Helpers\Helpers::instance() );

// Logger.
mylisting()->register( 'logger', MyListing\Utils\Logger\Logger::instance() );