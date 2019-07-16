<?php

namespace MyListing\Ext\Maps;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Maps {
	use \MyListing\Src\Traits\Instantiatable;

	public
		$platform,
		$geocoder;

	private $platforms = [
		'google-maps' => 'Google_Maps',
		'mapbox' => 'Mapbox'
	];

	public function __construct() {
		// Setup ACF settings page.
		add_action( 'acf/init', [ $this, 'setup_options_page' ] );

		// Initialize Maps.
		add_action( 'init', [ $this, 'initialize' ], 30 );
        add_filter( 'mylisting/localize-data', [ $this, 'localize_data' ], 20 );

		// Include marker HTML templates.
		add_action( 'mylisting/get-footer', [ $this, 'include_templates' ], 25 );
		add_action( 'admin_footer', [ $this, 'include_templates' ], 25 );
	}

	public function initialize() {
		$platform = c27()->get_setting( 'general_maps_platform', 'google-maps' );

		if ( CASE27_ENV === 'dev' && ! empty( $_GET['maps:provider'] ) ) {
			$platform = $_GET['maps:provider'];
		}

		if ( ! in_array( $platform, array_keys( $this->platforms ) ) ) {
			return false;
		}

		$maps = sprintf( '\MyListing\Ext\Maps\Platforms\%s\%s::instance', $this->platforms[ $platform ], $this->platforms[ $platform ] );
		$geocoder = sprintf( '\MyListing\Ext\Maps\Platforms\%s\Geocoder::instance', $this->platforms[ $platform ] );

		$this->platform = call_user_func( $maps );
		$this->geocoder = call_user_func( $geocoder );

		mylisting()->register( 'mapservice', $this->platform );
		mylisting()->register( 'geocoder', $this->geocoder );
	}

	/**
	 * Setup map options page in WP Admin > Theme Options > Maps.
	 *
	 * @since 1.7.2
	 */
	public function setup_options_page() {
		acf_add_options_sub_page( [
			'page_title' 	=> _x( 'Map Services', 'Maps page title in WP Admin', 'my-listing' ),
			'menu_title'	=> _x( 'Map Services', 'Maps menu title in WP Admin', 'my-listing' ),
			'menu_slug' 	=> 'theme-maps-settings',
			'capability'	=> 'edit_posts',
			'redirect'		=> false,
			'parent_slug'   => 'case27/tools.php',
		] );
	}

	public function include_templates() {
    	c27()->get_partial( 'marker-templates' );
	}

	public function localize_data( $data ) {
		$data['MapConfig'] = [];
		return $data;
	}
}