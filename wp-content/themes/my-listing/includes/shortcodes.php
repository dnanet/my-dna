<?php

namespace MyListing\Includes;

if ( ! defined('ABSPATH') ) {
	exit;
}

class Shortcodes {
    use \MyListing\Src\Traits\Instantiatable;

	private $all = [];

	public function __construct() {
		add_action( 'admin_menu', [ $this, 'add_shortcodes_page' ], 50 );
		do_action( 'mylisting/shortcodes/init', $this );
	}

    public function add_shortcodes_page() {
        c27()->new_admin_page( 'submenu', [
            'case27/tools.php',
            __( 'Shortcodes', 'my-listing' ),
            __( 'Shortcodes', 'my-listing' ),
            'manage_options',
            'case27-tools-shortcodes',
            function() {
            	require locate_template( 'templates/admin/shortcodes.php' );
            },
       	] );
	}

	/**
	 * Register shortcodes by providing either the path to the
	 * shortcode file, or an array of paths to bulk register.
	 *
	 * @since 1.0
	 */
	public function register( $shortcodes ) {
		if ( is_string( $shortcodes ) ) $shortcodes = [ $shortcodes ];

		foreach ((array) $shortcodes as $shortcode) {
			$this->all[] = require_once $shortcode;
		}
	}

	// Get all registered shortcodes.
	public function all() {
		return $this->all;
	}

	// Get all registered shortcodes, encoded to be safely used in JavaScript.
	public function all_encoded() {
		$shortcode_data = [];

		foreach ((array) $this->all as $shortcode) {
			$shortcode_data[] = [
				'name'        => $shortcode->name,
				'title'       => $shortcode->title,
				'data'        => isset($shortcode->data) ? $shortcode->data : [],
				'content'     => isset($shortcode->content) ? $shortcode->content : null,
				'attributes'  => isset($shortcode->attributes) ? $shortcode->attributes : [],
				'description' => isset($shortcode->description) ? $shortcode->description : '',
			];
		}

		return c27()->encode_attr( $shortcode_data );
	}
}
