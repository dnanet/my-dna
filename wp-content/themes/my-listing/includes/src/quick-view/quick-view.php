<?php
/**
 * Listing Quick View handler.
 *
 * @since 1.0
 */

namespace MyListing\Src\Quick_View;

if ( ! defined('ABSPATH') ) {
	exit;
}

class Quick_View {
    use \MyListing\Src\Traits\Instantiatable;

	public function __construct() {
		add_action( 'wp_ajax_get_listing_quick_view', [ $this, 'get_quick_view' ] );
		add_action( 'wp_ajax_nopriv_get_listing_quick_view', [ $this, 'get_quick_view' ] );
	}

	public function get_quick_view() {
		if ( empty( $_REQUEST['listing_id'] ) ) {
			return;
		}

		$listing = \MyListing\Src\Listing::get( absint( $_REQUEST['listing_id'] ) );
		if ( ! $listing ) {
			return;
		}

		ob_start();

		// Get quick view template.
		mylisting_locate_template( 'partials/listing-quick-view.php', compact('listing') );

		// Send response object.
		wp_send_json( [ 'html' => ob_get_clean() ] );
	}
}
