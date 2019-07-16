<?php

namespace MyListing\Ext\WooCommerce;

if ( ! defined('ABSPATH') ) {
	exit;
}

class WooCommerce {
	use \MyListing\Src\Traits\Instantiatable;

	public $endpoints;

	public function __construct() {
		if ( ! class_exists( '\Woocommerce' ) ) {
			return;
		}

		$this->endpoints = Endpoints::instance();
		$this->templates = Templates::instance();
		$this->shop = Shop::instance();
		require_once locate_template( 'includes/extensions/woocommerce/general.php' );

		// Init request handlers.
		Requests\Get_Products::instance();

		// Add 'My Listings' dashboard endpoint.
		$this->dashboard_listings_endpoint();

        // WooCommerce scripts.
        add_action( 'wp_enqueue_scripts', [ $this, 'enqueue_scripts' ], 30 );
	}

	// Wrapper.
	public function add_dashboard_page( $page ) {
		if ( ! $this->endpoints ) {
			return false;
		}

		$this->endpoints->add_page( $page );
	}

	// My Listings page.
	public function dashboard_listings_endpoint() {
		$this->add_dashboard_page( [
			'endpoint' => 'my-listings',
			'title' => __( 'My Listings', 'my-listing' ),
			'template' => locate_template( 'includes/integrations/wp-job-manager/templates/my-listings.php' ), // @todo: refactor to bypass wpjm...
			'show_in_menu' => true,
			'order' => 2,
		] );
	}

    /**
     * Register/deregister WooCommerce scripts.
     *
     * @since 1.7.0
     */
    public function enqueue_scripts() {
        if ( ! is_user_logged_in() ) {
            wp_enqueue_script( 'wc-password-strength-meter' );
        }

        if ( is_account_page() ) {
            // Include charting library.
            wp_enqueue_script( 'chartist', c27()->template_uri( 'assets/vendor/chartist/chartist.js' ), [], CASE27_THEME_VERSION, true );
            wp_enqueue_style( 'chartist', c27()->template_uri( 'assets/vendor/chartist/chartist.css' ), [], CASE27_THEME_VERSION );

            // Dashboard scripts and styles.
            wp_enqueue_style( 'mylisting-dashboard' );
            wp_enqueue_script( 'mylisting-dashboard' );
        }
    }

	// Wrapper.
	public function wrap_page_in_block( $page ) {
		$this->templates->wrap_page_in_block( $page );
	}
}
