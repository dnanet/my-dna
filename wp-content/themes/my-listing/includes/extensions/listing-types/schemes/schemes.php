<?php

namespace MyListing\Ext\Listing_Types\Schemes;

class Schemes {
	use \MyListing\Src\Traits\Instantiatable;

	private $path, $schemes;

	public function __construct() {
        $this->path = locate_template( 'includes/extensions/listing-types/schemes' );
	}

	public function get( $scheme ) {
		if ( isset( $this->schemes[ $scheme ] ) ) {
			return $this->schemes[ $scheme ];
		}

		if ( file_exists( trailingslashit( $this->path ) . $scheme . '.php' ) ) {
			$this->schemes[ $scheme ] = require_once trailingslashit( $this->path ) . $scheme . '.php';
			return $this->schemes[ $scheme ];
		}

		return false;
	}
}

mylisting()->register( 'schemes', Schemes::instance() );
