<?php

namespace MyListing\Src\Admin;

class Listing_Settings {
	use \MyListing\Src\Traits\Instantiatable;

	public function __construct() {
		if ( ! is_admin() ) {
			return false;
		}

		add_action( 'add_meta_boxes', [ $this, 'sidebar_metabox' ] );
	}

	/**
	 * Add a sidebar settings box in listing
	 * edit page in wp-admin.
	 *
	 * @since 1.7.0
	 */
	public function sidebar_metabox() {
		// Metabox settings.
		$id       = 'cts_listing_sidebar_settings';
		$title    = _x( 'Listing Settings', 'Listing sidebar settings in wp-admin', 'my-listing' );
		$callback = [ $this, 'sidebar_metabox_content' ];
		$screen   = 'job_listing';
		$context  = 'side';
		$priority = 'default';

		// Add metabox.
		add_meta_box( $id, $title, $callback, $screen, $context, $priority );
	}

	/**
	 * The sidebar settings box contents.
	 *
	 * @since 1.7.0
	 */
	public function sidebar_metabox_content( $listing ) {
		do_action( 'mylisting/admin/listing/sidebar-settings', \MyListing\Src\Listing::get( $listing ) );
	}
}