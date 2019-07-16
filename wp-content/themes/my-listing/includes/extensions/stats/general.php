<?php

namespace MyListing\Ext\Stats;

class General {
	use \MyListing\Src\Traits\Instantiatable;

	public function __construct() {
		add_filter( 'mylisting/stats/user', [ $this, 'set_count_stats' ], 10, 2 );
		add_filter( 'mylisting/stats/user', [ $this, 'set_promotion_stats' ], 10, 2 );
	}

	public function set_count_stats( $stats, $user_id ) {
		if ( ! isset( $stats['listings'] ) ) {
			$stats['listings'] = [];
		}

		$stats['listings']['published'] = $this->query_listing_count( $user_id, 'publish' );
		$stats['listings']['pending_approval'] = $this->query_listing_count( $user_id, 'pending' );
		$stats['listings']['pending_payment'] = $this->query_listing_count( $user_id, 'pending_payment' );
		$stats['listings']['preview'] = $this->query_listing_count( $user_id, 'preview' );
		$stats['listings']['expired'] = $this->query_listing_count( $user_id, 'expired' );
		$stats['listings']['pending'] = ( absint( $stats['listings']['pending_approval'] ) ?: 0 ) + ( absint( $stats['listings']['pending_payment'] ) ?: 0 );

		return $stats;
	}

	public function set_promotion_stats( $stats, $user_id ) {
		if ( ! isset( $stats['promotions'] ) ) {
			$stats['promotions'] = [];
		}

		$stats['promotions']['count'] = $this->query_promotion_count( $user_id );
		return $stats;
	}

	public function query_listing_count( $user_id, $status = 'publish' ) {
		global $wpdb;
		$sql = $wpdb->prepare( "
			SELECT COUNT( * ) AS count
			FROM {$wpdb->posts}
			WHERE
				post_type = 'job_listing'
				AND post_status = %s
				AND post_author = %d
		", $status, $user_id );

		$query = $wpdb->get_row( $sql, OBJECT );

		return is_object( $query ) && ! empty( $query->count ) ? (int) $query->count : 0;
	}

	public function query_promotion_count( $user_id ) {
		global $wpdb;
		$sql = $wpdb->prepare( "
			SELECT COUNT( * ) AS count
			FROM {$wpdb->posts}
			INNER JOIN {$wpdb->postmeta} ON ( {$wpdb->posts}.ID = {$wpdb->postmeta}.post_id )
			WHERE
				post_type = 'cts_promo_package'
				AND post_status = 'publish'
				AND {$wpdb->postmeta}.meta_key = '_user_id'
				AND {$wpdb->postmeta}.meta_value = %d
		", $user_id );

		$query = $wpdb->get_row( $sql, OBJECT );

		return is_object( $query ) && ! empty( $query->count ) ? (int) $query->count : 0;
	}
}