<?php

namespace MyListing\Ext\Visits;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Visits {
	use \MyListing\Src\Traits\Instantiatable;

	private $table_version, $current_version;

	public function __construct() {
		$this->table_version = '0.63';
		$this->current_version = get_option( 'mylisting_visits_table_version' );

		// Setup ACF settings page.
		add_action( 'acf/init', [ $this, 'setup_options_page' ] );

		// Setup DB.
		$this->setup_tables();

		// Count visits.
		add_action( 'template_redirect', [ $this, 'maybe_add_visit' ] );

		// Get visit stats.
		add_filter( 'mylisting/stats/user', [ $this, 'set_user_visit_stats' ], 10, 2 );
		add_filter( 'mylisting/stats/listing', [ $this, 'set_listing_visit_stats' ], 10, 2 );
		add_filter( 'mylisting/stats/admin', [ $this, 'set_admin_visit_stats' ], 10 );

		// Schedule event to check for and delete old stats.
		if ( ! wp_next_scheduled( 'mylisting/stats/cleanup-visits-table' ) ) {
			wp_schedule_event( time(), 'twicedaily', 'mylisting/stats/cleanup-visits-table' );
		}

		// Cleanup visit stats table.
		add_action( 'mylisting/stats/cleanup-visits-table', [ $this, 'cleanup_visits_table' ], 30 );
	}

	public function maybe_add_visit() {
		if ( ! is_singular( 'job_listing' ) ) {
			return;
		}

		global $post;
		$cookie_key = md5( sprintf( 'mylisting_recent_visit_%s', $post->ID ) );

		// If this user already viewed this listing recently, then skip
		// to prevent visit spamming. See mylisting/stats/visit-throttle filter.
		if ( mylisting()->cookies()->get( $cookie_key ) === 'yes' ) {
			return;
		}

		// Set recent visit cookie.
		mylisting()->cookies()->set( $cookie_key, 'yes', time() + ( (int) apply_filters( 'mylisting/stats/visit-throttle', 3 ) ) );

		// Get visitor data and insert visit.
    	$visitor = \MyListing\Src\Visitor::instance();
    	$ref = $visitor->get_referrer();
    	$os = $visitor->get_os();
    	$location = $visitor->get_location();
		$this->add_visit( [
			'listing_id' => $post->ID,
			'fingerprint' => $visitor->get_fingerprint(),
			'ip_address' => $visitor->get_ip(),
			'language' => $visitor->get_language(),
			'ref_url' => $ref ? $ref['url'] : null,
			'ref_domain' => $ref ? $ref['domain'] : null,
			'os' => $os ? $os['os'] : null,
			'device' => $os ? $os['device'] : null,
			'browser' => $visitor->get_browser(),
			'http_user_agent' => $visitor->get_user_agent(),
			'country_code' => $location ? $location['country_code'] : null,
			'city' => $location ? $location['city'] : null,
		] );
	}

	public function add_visit( $args ) {
		global $wpdb;

		if ( empty( $args['fingerprint'] ) || empty( $args['listing_id'] ) ) {
			return;
		}

		// Get values.
		$values = array_filter( [
			'listing_id' => $args['listing_id'],
			'fingerprint' => $args['fingerprint'],
			'ip_address' => ! empty( $args['ip_address'] ) ? $args['ip_address'] : null,
			'language' => ! empty( $args['language'] ) ? $args['language'] : null,
			'ref_url' => ! empty( $args['ref_url'] ) ? $args['ref_url'] : null,
			'ref_domain' => ! empty( $args['ref_domain'] ) ? $args['ref_domain'] : null,
			'os' => ! empty( $args['os'] ) ? $args['os'] : null,
			'device' => ! empty( $args['device'] ) ? $args['device'] : null,
			'browser' => ! empty( $args['browser'] ) ? $args['browser'] : null,
			'http_user_agent' => ! empty( $args['http_user_agent'] ) ? $args['http_user_agent'] : null,
			'country_code' => ! empty( $args['country_code'] ) ? $args['country_code'] : null,
			'city' => ! empty( $args['city'] ) ? $args['city'] : null,
		] );

		$values['time'] = gmdate('Y-m-d H:i:s');

		// Insert visit to db.
		$wpdb->insert( $wpdb->prefix.'mylisting_visits', $values );
	}

	public function setup_tables() {
		if ( $this->table_version === $this->current_version ) {
			return;
		}

		global $wpdb;
		$table_name = $wpdb->prefix . 'mylisting_visits';
		$sql = "CREATE TABLE $table_name (
			id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
			listing_id bigint(20) unsigned NOT NULL,
			time datetime NOT NULL,
			fingerprint varchar(64) NOT NULL,
			ip_address varchar(32),
			language varchar(32),
			ref_url varchar(512),
			ref_domain varchar(256),
			os varchar(32),
			device varchar(32),
			browser varchar(32),
			http_user_agent varchar(512),
			country_code varchar(32),
			city varchar(64),
			PRIMARY KEY  (id)
		);";

		require_once ABSPATH . 'wp-admin/includes/upgrade.php';
		dbDelta( $sql );

		$wpdb->query( "
			ALTER TABLE {$wpdb->prefix}mylisting_visits
			ADD CONSTRAINT FK_VIEWS_LISTING_ID
			FOREIGN KEY (listing_id)
			REFERENCES {$wpdb->posts}(ID) ON DELETE CASCADE;
		" );

		update_option( 'mylisting_visits_table_version', $this->table_version );
	}

	public function _apply_query_rules( $sql, $args ) {
		global $wpdb;

		// Get stats for a single author.
		if ( ! empty( $args['user_id'] ) ) {
			$sql[] = sprintf( " AND {$wpdb->posts}.post_author = %d ", $args['user_id'] );
		}

		// Get stats for a single listing.
		if ( ! empty( $args['listing_id'] ) ) {
			$sql[] = sprintf( " AND {$wpdb->prefix}mylisting_visits.listing_id = %d ", $args['listing_id'] );
		}

		// Limit visit timeframe.
		if ( ! empty( $args['time'] ) && in_array( $args['time'], ['lastday', 'lastweek', 'lastmonth'] ) ) {
			$time_modifiers = [ 'lastday' => '-1 day', 'lastweek' => '-7 days', 'lastmonth' => '-30 days' ];
			$sql[] = sprintf(
				" AND {$wpdb->prefix}mylisting_visits.time >= '%s' ",
				c27()->utc()->modify( $time_modifiers[ $args['time'] ] )->format('Y-m-d H:i:s')
			);
		}

		return $sql;
	}

	public function get_visits( $args = [] ) {
		global $wpdb;

		$args = wp_parse_args( $args, [
			'listing_id' => false,
			'user_id' => false,
			'time' => false,
			'unique' => false,
		] );

		$sql = [];

		if ( $args['unique'] ) {
			$sql[] = "SELECT COUNT( DISTINCT( {$wpdb->prefix}mylisting_visits.fingerprint ) ) AS count";
		} else {
			$sql[] = "SELECT COUNT( {$wpdb->prefix}mylisting_visits.id ) AS count";
		}

		$sql[] = "FROM {$wpdb->prefix}mylisting_visits";
		$sql[] = "INNER JOIN {$wpdb->posts} ON ( {$wpdb->posts}.ID = {$wpdb->prefix}mylisting_visits.listing_id )";
		$sql[] = "WHERE {$wpdb->posts}.post_status = 'publish'";

		$sql = $this->_apply_query_rules( $sql, $args );
		$sql = join( "\n", $sql );

		$query = $wpdb->get_row( $sql, OBJECT );

		return is_object( $query ) && ! empty( $query->count ) ? (int) $query->count : 0;
	}

	public function get_grouped_visits( $args = [] ) {
		global $wpdb;

		$args = wp_parse_args( $args, [
			'listing_id' => false,
			'user_id' => false,
			'time' => false,
			'group_by' => 'day',
		] );

		$visits = [];
		$by_hour = $args['group_by'] === 'hour';
		$grouped = $by_hour
			? "DATE_FORMAT( {$wpdb->prefix}mylisting_visits.time, '%Y-%m-%d %H:00:00' )"
			: "DATE( {$wpdb->prefix}mylisting_visits.time )";

		$sql = [];
		$sql[] = "
			SELECT
				COUNT( {$wpdb->prefix}mylisting_visits.id ) AS views,
				COUNT( DISTINCT( {$wpdb->prefix}mylisting_visits.fingerprint ) ) AS unique_views,
				$grouped as date
		";

		$sql[] = "FROM {$wpdb->prefix}mylisting_visits";
		$sql[] = "INNER JOIN {$wpdb->posts} ON ( {$wpdb->posts}.ID = {$wpdb->prefix}mylisting_visits.listing_id )";
		$sql[] = "WHERE {$wpdb->posts}.post_status = 'publish'";

		$sql = $this->_apply_query_rules( $sql, $args );

		$sql[] = "GROUP BY date";
		$sql = join( "\n", $sql );

		$query = $wpdb->get_results( $sql, OBJECT );
		if ( ! is_array( $query ) || empty( $query ) ) {
			$query = [];
		}

		$date = c27()->utc();
		$modifier = $by_hour ? '-1 hour' : '-1 day';

		// lastmonth -> 30 days, lastweek -> 7 days, lastday -> 1 day.
		$count = ( $args['time'] === 'lastmonth' ? 30 : ( $args['time'] === 'lastweek' ? 7 : 1 ) );

		// if the `group_by` param is set to `hour`, multiple count by 24 for hours.
		if ( $by_hour ) {
			$count *= 24;
		}

		// Prepare $visits array.
		for ( $i = 0; $i < $count; $i++ ) {
			$id = $date->format( $by_hour ? 'Y-m-d H:00:00' : 'Y-m-d' );
			$label = $date->format( $by_hour ? 'H:00' : 'd' );
			$visits[ $id ] = [
				'views' => 0,
				'unique_views' => 0,
				'date' => $id,
				'label' => $label,
				'formatted' => [
					'views' => _x( 'No views', 'User dashboard', 'my-listing' ),
					'unique_views' => _x( 'No views', 'User dashboard', 'my-listing' ),
					'date' => date_i18n( 'M j' . ( $by_hour ? ' H:00' : '' ), strtotime( $id ) ),
				],
			];
			$date->modify( $modifier );
		}

		// Merge $query results to $visits array.
		foreach ( $query as $day ) {
			if ( empty( $day ) || ! isset( $visits[ $day->date ] ) ) {
				continue;
			}

			$format = _x( '%1$s visit(s) on %2$s', 'User dashboard', 'my-listing' );
			$day->views = isset( $day->views ) ? $day->views : 0;
			$day->unique_views = isset( $day->unique_views ) ? $day->unique_views : 0;

			$visits[ $day->date ]['views'] = $day->views;
			$visits[ $day->date ]['unique_views'] = $day->unique_views;

			$visits[ $day->date ]['formatted']['views'] = sprintf( _n( '%s view', '%s views', $day->views, 'my-listing' ), number_format_i18n( $day->views ) );
			$visits[ $day->date ]['formatted']['unique_views'] = sprintf( _n( '%s unique view', '%s unique views', $day->unique_views, 'my-listing' ), number_format_i18n( $day->unique_views ) );
		}

		return array_reverse( $visits );
	}

	public function get_referrers( $args = [] ) {
		global $wpdb;
		$referrers = [];

		$args = wp_parse_args( $args, [
			'listing_id' => false,
			'user_id' => false,
			'time' => false,
		] );

		$sql = [];
		$sql[] = "
			SELECT
				{$wpdb->prefix}mylisting_visits.ref_domain AS ref_domain,
				COUNT( {$wpdb->prefix}mylisting_visits.ref_domain ) AS ref_domain_count
			FROM {$wpdb->prefix}mylisting_visits
			INNER JOIN {$wpdb->posts} ON ( {$wpdb->posts}.ID = {$wpdb->prefix}mylisting_visits.listing_id )
			WHERE
				{$wpdb->posts}.post_status = 'publish'
				AND {$wpdb->prefix}mylisting_visits.ref_domain IS NOT NULL
				AND {$wpdb->prefix}mylisting_visits.ref_url IS NOT NULL
		";

		$sql = $this->_apply_query_rules( $sql, $args );
		$sql[] = "GROUP BY ref_domain";
		$sql[] = "ORDER BY ref_domain_count DESC";
		$sql[] = "LIMIT 15";

		$sql = join( "\n", $sql );
		$query = $wpdb->get_results( $sql, OBJECT );

		if ( ! is_array( $query ) || empty( $query ) ) {
			return $referrers;
		}

		foreach ( $query as $domain ) {
			if ( empty( $domain->ref_domain ) || empty( $domain->ref_domain_count ) ) {
				continue;
			}

			$referrers[] = [
				'domain' => $domain->ref_domain,
				'count' => $domain->ref_domain_count,
				'subrefs' => $this->get_referrers_for_domain( $domain->ref_domain, $args ),
			];
		}

		return $referrers;
	}

	public function get_countries( $args = [] ) {
		$rows = $this->get_column_count( 'country_code', $args );
		$countries = [];

		foreach ( $rows as $country ) {
			if ( empty( $country->country_code ) || empty( $country->country_code_count ) ) {
				continue;
			}

			$countries[] = [
				'code' => $country->country_code,
				'name' => mylisting()->strings()->get_country( $country->country_code ),
				'count' => $country->country_code_count,
			];
		}

		return $countries;
	}

	public function get_browsers( $args = [] ) {
		$rows = $this->get_column_count( 'browser', $args );
		$browsers = [];

		foreach ( $rows as $browser ) {
			if ( empty( $browser->browser ) || empty( $browser->browser_count ) ) {
				continue;
			}

			$browsers[] = [
				'name' => $browser->browser,
				'count' => $browser->browser_count,
			];
		}

		if ( ! empty( $_GET['dummy_data'] ) ) {
			$browsers = array_map( function( $browser ) {
				return [
					'name' => $browser,
					'count' => rand( 30, 240 ),
				];
			}, [ 'Internet Explorer', 'Firefox', 'Safari', 'Chrome', 'Edge', 'Opera', 'Handheld Browser' ] );
		}

		return $browsers;
	}

	public function get_devices( $args = [] ) {
		$rows = $this->get_column_count( 'device', $args );
		$devices = [];

		foreach ( $rows as $device ) {
			if ( empty( $device->device ) || empty( $device->device_count ) ) {
				continue;
			}

			$label = $device->device === 'desktop'
				? _x( 'Desktop', 'User dashboard', 'my-listing' )
				: _x( 'Mobile', 'User dashboard', 'my-listing' );

			$devices[] = [
				'name' => $device->device,
				'label' => $label,
				'count' => $device->device_count,
			];
		}

		return $devices;
	}

	public function get_platforms( $args = [] ) {
		$rows = $this->get_column_count( 'os', $args );
		$platforms = [];

		foreach ( $rows as $platform ) {
			if ( empty( $platform->os ) || empty( $platform->os_count ) ) {
				continue;
			}

			$platforms[] = [
				'name' => $platform->os,
				'count' => $platform->os_count,
			];
		}

		if ( ! empty( $_GET['dummy_data'] ) ) {
			$platforms = array_map( function( $os ) {
				return [
					'name' => $os,
					'count' => rand( 30, 240 ),
				];
			}, [ 'Windows 10', 'Windows 8', 'Windows 7', 'macOS', 'Linux', 'Ubuntu', 'iOS', 'Android', 'webOS' ] );
		}

		return $platforms;
	}

	public function get_column_count( $column, $args = [] ) {
		global $wpdb;
		$args = wp_parse_args( $args, [
			'listing_id' => false,
			'user_id' => false,
			'time' => false,
		] );

		$sql = [];
		$sql[] = "
			SELECT
				{$wpdb->prefix}mylisting_visits.{$column} AS {$column},
				COUNT( {$wpdb->prefix}mylisting_visits.{$column} ) AS {$column}_count
			FROM {$wpdb->prefix}mylisting_visits
			INNER JOIN {$wpdb->posts} ON ( {$wpdb->posts}.ID = {$wpdb->prefix}mylisting_visits.listing_id )
			WHERE
				{$wpdb->posts}.post_status = 'publish'
				AND {$wpdb->prefix}mylisting_visits.{$column} IS NOT NULL
		";

		$sql = $this->_apply_query_rules( $sql, $args );
		$sql[] = "GROUP BY {$column}";
		$sql[] = "ORDER BY {$column}_count DESC";
		$sql[] = "LIMIT 15";

		$sql = join( "\n", $sql );
		$query = $wpdb->get_results( $sql, OBJECT );

		if ( ! is_array( $query ) || empty( $query ) ) {
			return [];
		}

		return $query;
	}

	public function get_referrers_for_domain( $domain, $args = [] ) {
		global $wpdb;

		$refs = [];
		$args = wp_parse_args( $args, [
			'listing_id' => false,
			'user_id' => false,
			'time' => false,
		] );

		if ( empty( $domain ) ) {
			return $refs;
		}

		$sql[] = "
			SELECT
				{$wpdb->prefix}mylisting_visits.ref_url AS ref_url,
				COUNT( {$wpdb->prefix}mylisting_visits.ref_url ) AS ref_url_count
			FROM {$wpdb->prefix}mylisting_visits
			INNER JOIN {$wpdb->posts} ON ( {$wpdb->posts}.ID = {$wpdb->prefix}mylisting_visits.listing_id )
			WHERE
				{$wpdb->posts}.post_status = 'publish'
				AND {$wpdb->prefix}mylisting_visits.ref_domain = %s
				AND {$wpdb->prefix}mylisting_visits.ref_url IS NOT NULL
		";

		$sql = $this->_apply_query_rules( $sql, $args );
		$sql[] = "GROUP BY ref_url";
		$sql[] = "ORDER BY ref_url_count DESC";
		$sql[] = "LIMIT 10";

		$sql = join( "\n", $sql );
		$query = $wpdb->get_results( $wpdb->prepare( $sql, $domain ), OBJECT );

		if ( ! is_array( $query ) || empty( $query ) ) {
			return $refs;
		}

		foreach ( $query as $domain ) {
			if ( empty( $domain->ref_url ) || empty( $domain->ref_url_count ) ) {
				continue;
			}

			$refs[] = [
				'url' => $domain->ref_url,
				'count' => $domain->ref_url_count,
			];
		}

		return $refs;
	}

	public function get_grouped_stats( $args = [] ) {
		$stats = [];
		$user_id = ! empty( $args['user_id'] ) ? $args['user_id'] : false;
		$listing_id = ! empty( $args['listing_id'] ) ? $args['listing_id'] : false;

		$stats['views'] = [
			'lastday' => $this->get_visits( [ 'user_id' => $user_id, 'listing_id' => $listing_id, 'time' => 'lastday' ] ),
			'lastweek' => $this->get_visits( [ 'user_id' => $user_id, 'listing_id' => $listing_id, 'time' => 'lastweek' ] ),
			'lastmonth' => $this->get_visits( [ 'user_id' => $user_id, 'listing_id' => $listing_id, 'time' => 'lastmonth' ] ),
		];

		$stats['unique_views'] = [
			'lastday' => $this->get_visits( [ 'user_id' => $user_id, 'listing_id' => $listing_id, 'time' => 'lastday', 'unique' => true ] ),
			'lastweek' => $this->get_visits( [ 'user_id' => $user_id, 'listing_id' => $listing_id, 'time' => 'lastweek', 'unique' => true ] ),
			'lastmonth' => $this->get_visits( [ 'user_id' => $user_id, 'listing_id' => $listing_id, 'time' => 'lastmonth', 'unique' => true ] ),
		];

		$stats['referrers'] = $this->get_referrers( [ 'user_id' => $user_id, 'listing_id' => $listing_id ] );
		$stats['countries'] = $this->get_countries( [ 'user_id' => $user_id, 'listing_id' => $listing_id ] );
		$stats['browsers'] = $this->get_browsers( [ 'user_id' => $user_id, 'listing_id' => $listing_id ] );
		$stats['platforms'] = $this->get_platforms( [ 'user_id' => $user_id, 'listing_id' => $listing_id ] );
		$stats['devices'] = $this->get_devices( [ 'user_id' => $user_id, 'listing_id' => $listing_id ] );
		$stats['charts'] = [
			'lastday' => $this->get_grouped_visits( [ 'time' => 'lastday', 'group_by' => 'hour', 'user_id' => $user_id, 'listing_id' => $listing_id ] ),
			'lastweek' => $this->get_grouped_visits( [ 'time' => 'lastweek', 'group_by' => 'day', 'user_id' => $user_id, 'listing_id' => $listing_id ] ),
			'lastmonth' => $this->get_grouped_visits( [ 'time' => 'lastmonth', 'group_by' => 'day', 'user_id' => $user_id, 'listing_id' => $listing_id ] ),
		];

		return $stats;
	}

	public function set_user_visit_stats( $stats, $user_id ) {
		$stats['visits'] = $this->get_grouped_stats( [
			'user_id' => $user_id,
		] );

		return $stats;
	}

	public function set_listing_visit_stats( $stats, $listing_id ) {
		$stats['visits'] = $this->get_grouped_stats( [
			'listing_id' => $listing_id,
		] );

		return $stats;
	}

	public function set_admin_visit_stats( $stats ) {
		$stats['visits'] = $this->get_grouped_stats();
		return $stats;
	}

	/**
	 * Setup listing stats options page in WP Admin > Theme Options > Listing Stats.
	 *
	 * @since 2.0
	 */
	public function setup_options_page() {
		acf_add_options_sub_page( [
			'page_title' 	=> _x( 'Listing Stats', 'Listing Stats page title in WP Admin', 'my-listing' ),
			'menu_title'	=> _x( 'Listing Stats', 'Listing Stats menu title in WP Admin', 'my-listing' ),
			'menu_slug' 	=> 'theme-stats-settings',
			'capability'	=> 'edit_posts',
			'redirect'		=> false,
			'parent_slug'   => 'case27/tools.php',
		] );
	}

	/**
	 * Check for, and remove old stats.
	 *
	 * @since 2.0
	 */
	public function cleanup_visits_table() {
		global $wpdb;

		$delete_after = c27()->get_setting( 'stats_general_delete_after', 30 ); // delete visits older than 30 days.
		$time_modifier = sprintf( '-%d days', absint( $delete_after ) );

		$sql = sprintf(
			"DELETE FROM {$wpdb->prefix}mylisting_visits WHERE {$wpdb->prefix}mylisting_visits.time < '%s'",
			c27()->utc()->modify( $time_modifier )->format('Y-m-d H:i:s')
		);

		// Execute delete query.
		$wpdb->query( $sql );
	}
}