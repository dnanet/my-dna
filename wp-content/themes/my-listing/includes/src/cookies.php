<?php

namespace MyListing\Src;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Cookies {
	use \MyListing\Src\Traits\Instantiatable;

	private $path, $domain;

	public function __construct() {
		$this->path = COOKIEPATH ?: '/';
		$this->domain = COOKIE_DOMAIN;
	}

	public function set( $name, $value = '', $expires = 0, $secure = false, $httponly = false ) {
		if ( headers_sent() ) {
			if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
		        headers_sent( $file, $line );
		        trigger_error( "{$name} cookie cannot be set - headers already sent by {$file} on line {$line}", E_USER_NOTICE );
		    }

		    return false;
		}

		setcookie( $name, $value, $expires, $this->path, $this->domain, $secure, $httponly );
	}

	public function get( $name ) {
		if ( ! isset( $_COOKIE[ $name ] ) ) {
			return false;
		}

		return $_COOKIE[ $name ];
	}

	public function delete( $name ) {
		if ( ! isset( $_COOKIE[ $name ] ) ) {
			return false;
		}

		unset( $_COOKIE[ $name ] );
		$this->set( $name, '', 1 ); // Expire cookie.
	}
}