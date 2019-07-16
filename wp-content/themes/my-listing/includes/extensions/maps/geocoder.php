<?php

namespace MyListing\Ext\Maps;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

abstract class Geocoder {
	public $platform;

	public function __construct() {
		$this->platform = mylisting()->maps()->platform;

		// Disable WPJM geocoding service.
		add_filter( 'job_manager_geolocation_enabled', '__return_false' );

		// Listen for location changes.
		add_action( 'job_manager_update_job_data', [ $this, 'frontend_update' ], 10, 2 );
		add_action( 'job_manager_save_job_listing', [ $this, 'backend_update' ], 40, 2 );
	}

	/**
	 * Geocode location on listing submit/edit through the front-end forms.
	 *
	 * @since 1.7.2
	 */
	public function frontend_update( $listing_id, $values ) {
		if ( empty( $values['job']['job_location'] ) ) {
			return;
		}

		if ( ! empty( $_POST['job_location__latitude'] ) && ! empty( $_POST['job_location__longitude'] ) ) {
			mlog( 'Skipping server-side address geocoding, data already passed through the frontend geocoder.' );
			return;
		}

		$this->save_location( $listing_id, $values['job']['job_location'] );
	}

	/**
	 * Geocode address when listing is created/edited through wp-admin.
	 *
	 * @since 1.7.2
	 */
	public function backend_update( $listing_id, $listing ) {
		if ( ! is_admin() || empty( $_POST['_job_location'] ) ) {
			return;
		}

		if ( ! empty( $_POST['_job_location__latitude'] ) && ! empty( $_POST['_job_location__longitude'] ) ) {
			mlog( 'Skipping server-side address geocoding, data already passed through the frontend geocoder.' );
			return;
		}

		$this->save_location( $listing_id, sanitize_text_field( $_POST['_job_location'] ) );
	}

	public function geocode( $location, $ignore_cache = false ) {
		if ( ! is_string( $location ) || empty( trim( $location ) ) ) {
			return false;
		}

		$location = trim( $location );
		$cache_key = sprintf( 'mylisting_address_%s', md5( $location ) );
		if ( $cached_address = get_transient( $cache_key ) && $ignore_cache !== true ) {
			mlog( 'Retrieving address geocoding information from cache.' );
			return $cached_address;
		}

		if ( ! ( $response = $this->client_geocode( $location ) ) ) {
			return false;
		}

		mlog( sprintf( 'Geocoding address through %s.', c27()->get_class_name( mylisting()->maps()->platform ) ) );
		$address = $this->transform_response( $response );

		// Cache response.
		set_transient( $cache_key, $address, WEEK_IN_SECONDS );

		return $address;
	}

	abstract public function client_geocode( $location );

	abstract public function transform_response( $response );

	public function save_location( $listing_id, $location ) {
		$this->clear_location_data( $listing_id );
		if ( ! ( $feature = $this->geocode( $location ) ) ) {
			mlog()->warn( sprintf( 'Server-side geocoding for location "%s" failed.', $location ) );
			return false;
		}

		mlog( 'Server-side location geocoding successful.' );
		update_post_meta( $listing_id, 'geolocation_lat', $feature['latitude'] );
		update_post_meta( $listing_id, 'geolocation_long', $feature['longitude'] );
		update_post_meta( $listing_id, 'geolocation_formatted_address', $feature['address'] );
		update_post_meta( $listing_id, 'geolocation_meta', $feature );
	}

	public function clear_location_data( $listing_id ) {
		// MyListing 1.7.2+ geolocation fields.
		delete_post_meta( $listing_id, 'geolocation_lat' );
		delete_post_meta( $listing_id, 'geolocation_long' );
		delete_post_meta( $listing_id, 'geolocation_formatted_address' );
		delete_post_meta( $listing_id, 'geolocation_meta' );

		// Old geolocation fields saved by WPJM (pre 1.7.2)
		delete_post_meta( $listing_id, 'geolocated' );
		delete_post_meta( $listing_id, 'geolocation_city' );
		delete_post_meta( $listing_id, 'geolocation_country_long' );
		delete_post_meta( $listing_id, 'geolocation_country_short' );
		delete_post_meta( $listing_id, 'geolocation_state_long' );
		delete_post_meta( $listing_id, 'geolocation_state_short' );
		delete_post_meta( $listing_id, 'geolocation_street' );
		delete_post_meta( $listing_id, 'geolocation_street_number' );
		delete_post_meta( $listing_id, 'geolocation_zipcode' );
		delete_post_meta( $listing_id, 'geolocation_postcode' );
	}
}