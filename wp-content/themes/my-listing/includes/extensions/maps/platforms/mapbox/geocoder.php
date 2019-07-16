<?php

namespace MyListing\Ext\Maps\Platforms\Mapbox;

class Geocoder extends \MyListing\Ext\Maps\Geocoder {
	use \MyListing\Src\Traits\Instantiatable;

	public function client_geocode( $location ) {
		$url = 'https://api.mapbox.com/geocoding/v5/mapbox.places/%s.json?%s';
		$params = [
			'access_token' => $this->platform->api_key,
			'language' => $this->platform->language !== 'default' ? $this->platform->language : 'en',
			'limit' => 1,
		];

		$request = wp_remote_get( sprintf( $url, urlencode( $location ), http_build_query( $params ) ), [
			'httpversion' => '1.1',
			'sslverify' => false,
		] );

		if ( is_wp_error( $request ) ) {
			return false;
		}

		$response = json_decode( wp_remote_retrieve_body( $request ) );
		if ( ! is_object( $response ) || empty( $response->features ) ) {
			return false;
		}

		return $response->features[0];
	}

	public function transform_response( $response ) {
		$feature = [
			'latitude'  => $response->geometry->coordinates[1],
			'longitude' => $response->geometry->coordinates[0],
			'address'   => $response->place_name,
			'provider'  => 'mapbox',
			'meta'      => [],
		];

		return $feature;
	}
}