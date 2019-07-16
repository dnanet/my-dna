<?php

if ( ! defined('ABSPATH') ) {
	exit;
}

/**
 * Autoload classes.
 *
 * MyListing\Src      -> my-listing/includes/classes
 * MyListing\Ext      -> my-listing/includes/extensions
 * MyListing\Int      -> my-listing/includes/integrations
 * MyListing\Utils    -> my-listing/includes/utils
 * MyListing\Includes -> my-listing/includes
 */
spl_autoload_register( function( $classname ) {
	$parts = explode( '\\', $classname );

	if ( $parts[0] !== 'MyListing' ) {
		return false;
	}

	if ( $parts[1] === 'Src' ) {
		$parts[1] = 'Includes' . DIRECTORY_SEPARATOR . 'Src';
	}

	if ( $parts[1] === 'Ext' ) {
		$parts[1] = 'Includes' . DIRECTORY_SEPARATOR . 'Extensions';
	}

	if ( $parts[1] === 'Int' ) {
		$parts[1] = 'Includes' . DIRECTORY_SEPARATOR . 'Integrations';
	}

	if ( $parts[1] === 'Utils' ) {
		$parts[1] = 'Includes' . DIRECTORY_SEPARATOR . 'Utils';
	}

	$path_parts = array_map( function( $part) {
		return strtolower( str_replace( '_', '-', $part ) );
	}, $parts );

	// unset my-listing path part since that's already known.
	unset( $path_parts[0] );

	$path = join( DIRECTORY_SEPARATOR, $path_parts ) . '.php';

	if ( locate_template( $path ) ) {
		require_once locate_template( $path );
	}
} );

require_once locate_template( 'includes/util.php' );
require_once locate_template( 'includes/init.php' );
require_once locate_template( 'includes/plugins/activator.php' );

$classes = [
	// Integrations.
	// @todo: Convert integrations to autoloadable namespace format.
	'extensions/listing-types/designer.php',
	'integrations/wp-job-manager/wp-job-manager.php',
	'integrations/buddypress/buddypress.php',
	'integrations/elementor/elementor.php',
	'integrations/27collective/bookmarks/bookmark.php',
	'integrations/27collective/breadcrumbs/breadcrumbs.php',
	'extensions/listing-types/listing-type.php',
	'integrations/27collective/product-vendors/vendors.php',
];

foreach ( $classes as $classPath ) {
	require_once locate_template( "includes/{$classPath}" );
}