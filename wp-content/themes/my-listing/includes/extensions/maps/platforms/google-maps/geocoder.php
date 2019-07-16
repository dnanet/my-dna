<?php

namespace MyListing\Ext\Maps\Platforms\Google_Maps;

class Geocoder extends \MyListing\Ext\Maps\Geocoder {
	use \MyListing\Src\Traits\Instantiatable;

	public function client_geocode( $location ) {
		$params = [
			'address' => $location,
			'key' => $this->platform->api_key,
			'language' => $this->platform->language !== 'default' ? $this->platform->language : 'en',
		];

		$request = wp_remote_get( sprintf( 'https://maps.googleapis.com/maps/api/geocode/json?%s', http_build_query( $params ) ), [
			'httpversion' => '1.1',
			'sslverify' => false,
		] );

		if ( is_wp_error( $request ) ) {
			return false;
		}

		$response = json_decode( wp_remote_retrieve_body( $request ) );
		if ( ! is_object( $response ) || $response->status !== 'OK' || empty( $response->results ) ) {
			return false;
		}

		return $response->results[0];
	}

	public function transform_response( $response ) {
		$feature = [
			'latitude'  => $response->geometry->location->lat,
			'longitude' => $response->geometry->location->lng,
			'address'   => $response->formatted_address,
			'provider'  => 'google-maps',
			'meta'      => [],
		];

		if ( ! empty( $response->address_components ) ) {
			foreach ( $response->address_components as $component ) {
				if ( empty( $component->types ) ) {
					continue;
				}

				foreach ( $component->types as $component_type ) {
					$feature['meta'][ $component_type ] = $component->long_name;
				}
			}
		}

		return $feature;
	}

}