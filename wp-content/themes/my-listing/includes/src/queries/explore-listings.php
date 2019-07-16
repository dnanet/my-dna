<?php

namespace MyListing\Src\Queries;

class Explore_Listings extends Query {
	use \MyListing\Src\Traits\Instantiatable;

	public $action = 'get_listings';

	/**
	 * Handle AJAX listing queries.
	 *
	 * @since 1.0.0
	 */
	public function handle() {
		check_ajax_referer( 'c27_ajax_nonce', 'security' );

		$result = $this->run( $_POST );

		wp_send_json( $result );
	}

	/**
	 * Handle Explore Listings requests, typically $_POST.
	 * Request can be manually constructed, which allows using
	 * this function outside Ajax/POST context.
	 *
	 * @since 1.7.0
	 */
	public function run( $request ) {
		global $wpdb;

		if ( empty( $request['form_data'] ) || ! is_array( $request['form_data'] ) || empty( $request['listing_type'] ) ) {
			return false;
		}

		if ( ! ( $listing_type_obj = ( get_page_by_path( $request['listing_type'], OBJECT, 'case27_listing_type' ) ) ) ) {
			return false;
		}

		$type = new \MyListing\Ext\Listing_Types\Listing_Type( $listing_type_obj );
		$form_data = $request['form_data'];

		$page = absint( isset($form_data['page']) ? $form_data['page'] : 0 );
		$per_page = absint( isset($form_data['per_page']) ? $form_data['per_page'] : c27()->get_setting('general_explore_listings_per_page', 9));
		$orderby = sanitize_text_field( isset($form_data['orderby']) ? $form_data['orderby'] : 'date' );
		$context = sanitize_text_field( isset( $form_data['context'] ) ? $form_data['context'] : 'advanced-search' );
		$args = [
			'order' => sanitize_text_field( isset($form_data['order']) ? $form_data['order'] : 'DESC' ),
			'offset' => $page * $per_page,
			'orderby' => $orderby,
			'posts_per_page' => $per_page,
			'tax_query' => [],
			'meta_query' => [],
		];

		$this->get_ordering_clauses( $args, $type, $form_data );

		$tax_query_operator = 'all' === get_option( 'job_manager_category_filter_type', 'all' ) ? 'AND' : 'IN';

		// Make sure we're only querying listings of the requested listing type.
		if ( ! $type->is_global() ) {
			$args['meta_query']['listing_type_query'] = [
				'key'     => '_case27_listing_type',
				'value'   =>  $type->get_slug(),
				'compare' => '='
			];
		}

		if ( $context === 'term-search' ) {
			$taxonomy = ! empty( $form_data['taxonomy'] ) ? sanitize_text_field( $form_data['taxonomy'] ) : false;
			$term = ! empty( $form_data['term'] ) ? sanitize_text_field( $form_data['term'] ) : false;

			if ( ! $taxonomy || ! $term || ! taxonomy_exists( $taxonomy ) ) {
				return false;
			}

			$args['tax_query'][] = [
				'taxonomy' => $taxonomy,
				'field' => 'term_id',
				'terms' => $term,
				'operator' => $tax_query_operator,
				'include_children' => $tax_query_operator !== 'AND',
			];

			// Remove listing type filtering to get accurate listing counts.
			// @todo: custom term counts, based on each listing type.
			if ( ! empty( $args['meta_query']['listing_type_query'] ) ) {
				unset( $args['meta_query']['listing_type_query'] );
			}
		} else {
			foreach ( (array) $type->get_search_filters() as $facet ) {
				// wp-search -> search_keywords
				// location -> search_location
				// text -> facet.show_field
				// proximity -> proximity
				// date -> show_field
				// range -> show_field
				// dropdown -> show_field
				// checkboxes -> show_field

				if ( $facet['type'] === 'wp-search' && ! empty( $form_data['search_keywords'] ) ) {
					// dd($form_data['search_keywords']);
					$args['search_keywords'] = sanitize_text_field( stripslashes( $form_data['search_keywords'] ) );
				}

				if ( $facet['type'] === 'location' && ! empty( $form_data['search_location'] ) ) {
					$args['search_location'] = sanitize_text_field( stripslashes( $form_data['search_location'] ) );
				}

				if ($facet['type'] == 'text' && isset($form_data[$facet['show_field']]) && $form_data[$facet['show_field']]) {
					$args['meta_query'][] = [
						'key'     => "_{$facet['show_field']}",
						'value'   => sanitize_text_field( stripslashes( $form_data[$facet['show_field']] ) ),
						'compare' => 'LIKE',
					];
				}

				if ($facet['type'] == 'proximity' && isset($form_data['proximity']) && isset($form_data['search_location_lat']) && isset($form_data['search_location_lng'])) {
					$proximity = absint( $form_data['proximity'] );
					$location = isset($form_data['search_location']) ? sanitize_text_field( stripslashes( $form_data['search_location'] ) ) : false;
					$lat = (float) $form_data['search_location_lat'];
					$lng = (float) $form_data['search_location_lng'];
					$units = isset($form_data['proximity_units']) && $form_data['proximity_units'] == 'mi' ? 'mi' : 'km';

					if ( $lat && $lng && $proximity && $location ) {
						// dump($lat, $lng, $proximity);

						$earth_radius = $units == 'mi' ? 3959 : 6371;

						$sql = $wpdb->prepare( $this->get_proximity_sql(), $earth_radius, $lat, $lng, $lat, $proximity );

						// dump($sql);

						$post_ids = (array) $wpdb->get_results( $sql, OBJECT_K );

						if (empty($post_ids)) $post_ids = ['none'];

						$args['post__in'] = array_keys( (array) $post_ids );

						// Remove search_location filter when using proximity filter.
						$args['search_location'] = '';
					}
				}

				if ($facet['type'] == 'date') {
					$date_type = 'exact';
					$format = 'ymd';

					foreach ($facet['options'] as $option) {
						if ($option['name'] == 'type') $date_type = $option['value'];
						if ($option['name'] == 'format') $format = $option['value'];
					}

					// Exact date search.
					if ($date_type == 'exact' && isset($form_data[$facet['show_field']]) && $form_data[$facet['show_field']]) {
						// Y-m-d format search.
						if ($format == 'ymd') {
							$date = date('Y-m-d', strtotime( $form_data[$facet['show_field']] ));
							$compare = '=';
						}

						// Year search. The year is converted to a date format, and the query instead runs a 'BETWEEN' comparison,
						// to include the requested year from January 01 to December 31.
						if ($format == 'year') {
							$date = [
								date('Y-01-01', strtotime($form_data[$facet['show_field']] . '-01-01' )),
								date('Y-12-31', strtotime($form_data[$facet['show_field']] . '-12-31')),
							];
							$compare = 'BETWEEN';
						}

						$args['meta_query'][] = [
							'key'     => "_{$facet['show_field']}",
							'value'   => $date,
							'compare' => $compare,
							'type' => 'DATE',
						];
					}

					// Range date search.
					if ($date_type == 'range') {
						$date_from = false;
						$date_to = false;
						$values = [];

						if (isset($form_data["{$facet['show_field']}_from"]) && $form_data["{$facet['show_field']}_from"]) {
							$date_from = $values['date_from'] = date(($format == 'ymd' ? 'Y-m-d' : 'Y'), strtotime( $form_data["{$facet['show_field']}_from"] ));

							if ($format == 'ymd') {
								$date_from = $values['date_from'] = date('Y-m-d', strtotime($form_data["{$facet['show_field']}_from"]));
							}

							if ($format == 'year') {
								$date_from = $values['date_from'] = date('Y-m-d', strtotime($form_data["{$facet['show_field']}_from"] . '-01-01'));
							}
						}

						if (isset($form_data["{$facet['show_field']}_to"]) && $form_data["{$facet['show_field']}_to"]) {
							if ($format == 'ymd') {
								$date_to = $values['date_to'] = date('Y-m-d', strtotime($form_data["{$facet['show_field']}_to"]));
							}

							if ($format == 'year') {
								$date_to = $values['date_to'] = date('Y-m-d', strtotime($form_data["{$facet['show_field']}_to"] . '-12-31'));
							}
						}

						if (empty($values)) continue;
						if (count($values) == 1) $values = array_pop($values);

						$args['meta_query'][] = [
							'key'     => "_{$facet['show_field']}",
							'value'   => $values,
							'compare' => is_array($values) ? 'BETWEEN' : ($date_from ? '>=' : '<='),
							'type' => 'DATE',
						];
					}
				}

				if ($facet['type'] == 'range' && isset($form_data[$facet['show_field']]) && $form_data[$facet['show_field']] && isset($form_data["{$facet['show_field']}_default"])) {
					$range_type = 'range';
					$range = $form_data[$facet['show_field']];
					$default_range = $form_data["{$facet['show_field']}_default"];

					// In case the range values include the maximum and minimum possible field values,
					// then skip, since the meta query is unnecessary, and would only make the query slower.
					if ($default_range == $range) continue;

					foreach ($facet['options'] as $option) {
						if ($option['name'] == 'type') $range_type = $option['value'];
					}

					if ($range_type == 'range' && strpos($range, '::') !== false) {
						$args['meta_query'][] = [
							'key'     => "_{$facet['show_field']}",
							'value'   => array_map('intval', explode('::', $range)),
							'compare' => 'BETWEEN',
							'type'    => 'NUMERIC',
						];
					}

					if ($range_type == 'simple') {
						$args['meta_query'][] = [
							'key'     => "_{$facet['show_field']}",
							'value'   => intval( $range ),
							'compare' => '<=',
							'type'    => 'NUMERIC',
						];
					}
				}

				if (($facet['type'] == 'dropdown' || $facet['type'] == 'checkboxes') && ! empty( $form_data[$facet['show_field']] ) ) {
					$dropdown_values = array_filter( array_map('stripslashes', (array) $form_data[$facet['show_field']] ) );

					if (!$dropdown_values) continue;

					if ( empty( $facet['options'] ) ) {
						$facet['options'] = [];
					}

					$facet_behavior = 'any';
					foreach ( (array) $facet['options'] as $facet_option ) {
						if ( $facet_option['name'] === 'behavior' ) {
							$facet_behavior = $facet_option['value'];
						}
					}

					// Tax query.
					if (
						$type->get_field( $facet[ 'show_field' ] ) &&
						! empty( $type->get_field( $facet[ 'show_field' ] )['taxonomy'] ) &&
						taxonomy_exists( $type->get_field( $facet[ 'show_field' ] )['taxonomy'] )
					) {
						$args['tax_query'][] = [
							'taxonomy' => $type->get_field( $facet[ 'show_field' ] )['taxonomy'],
							'field' => 'slug',
							'terms' => $dropdown_values,
							'operator' => $facet_behavior === 'all' ? 'AND' : 'IN',
							'include_children' => $facet_behavior !== 'all',
						];

						continue;
					}

					// If the meta value is serialized.
					if ( $type->get_field( $facet[ 'show_field' ] ) && $type->get_field( $facet[ 'show_field' ] )['type'] == 'multiselect' ) {
						$subquery = [
							'relation' => $facet_behavior === 'all' ? 'AND' : 'OR',
						];

						foreach ( $dropdown_values as $dropdown_value ) {
							$subquery[] = [
								'key'     => "_{$facet['show_field']}",
								'value'   => '"' . $dropdown_value . '"',
								'compare' => 'LIKE',
							];
						}

						$args['meta_query'][] = $subquery;
						continue;
					}

					$args['meta_query'][] = [
						'key'     => "_{$facet['show_field']}",
						'value'   => $dropdown_values,
						'compare' => 'IN',
					];
				}
			}
		}

		$results = [];
		$result['found_jobs'] = false;
		$listing_wrap = ! empty( $request['listing_wrap'] ) ? sanitize_text_field( $request['listing_wrap'] ) : '';

		/* Promotions v1 code (deprecated) */
		$result['promoted_ids']  = [];
		$result['promoted_html'] = '';

		/**
		 * Hook after the search args have been set, but before the query is executed.
		 *
		 * @since 1.7.0
		 */
		do_action_ref_array( 'mylisting/get-listings/before-query', [ &$args, &$type, &$result ] );

		$listings = $this->query( $args );

		if ( ! empty( $request['return_query'] ) ) {
			return $listings;
		}

		ob_start();

		if ( CASE27_ENV === 'dev' ) {
			$result['args'] = $args;
			$result['sql'] = $listings->request;
		}

		if ( $listings->have_posts() ) : $result['found_jobs'] = true;
			while ( $listings->have_posts() ) : $listings->the_post();
				/* Promotions v1 code (deprecated) */
				if ( absint( $listings->post_count ) > 3 && in_array( absint( get_the_ID() ), $result['promoted_ids'] ) ) {
					continue;
				}

				global $post;
				mylisting_locate_template( 'partials/listing-preview.php', [
					'listing' => $post,
					'wrap_in' => $listing_wrap,
				] );
			endwhile;

			$result['listings_html'] = ob_get_clean();

			if ( absint( $listings->post_count ) <= 3 ) {
				$result['html'] = $result['listings_html'];
			} else {
				$result['html'] = $result['promoted_html'] . $result['listings_html'];
			}

			wp_reset_postdata();
		else:
			get_job_manager_template_part( 'content', 'no-jobs-found' );
			$result['html'] = ob_get_clean();
		endif;

		/* Promotions v1 code (deprecated) */
		unset( $result['promoted_ids'] );

		// Generate pagination
		$result['pagination'] = get_job_listing_pagination( $listings->max_num_pages, ($page + 1) );

		$result['showing'] = sprintf( __( '%d results', 'my-listing' ), $listings->found_posts);

		if ($listings->found_posts == 1) {
			$result['showing'] = __( 'One result', 'my-listing');
		}

		if ($listings->found_posts < 1) {
			$result['showing'] = __( 'No results', 'my-listing' );
		}

		$result['max_num_pages'] = $listings->max_num_pages;

		return $result;
	}

	/**
	 * Generate the 'orderby' argument, allowing for custom 'orderby' clauses.
	 *
	 * @since 1.6.0
	 */
	public function get_ordering_clauses( &$args, $type, $form_data ) {
		$options = (array) $type->get_ordering_options();
		$sortby  = ! empty( $form_data['sort'] ) ? sanitize_text_field( $form_data['sort'] ) : false;

		if ( ! $sortby || empty( $options ) ) {
			return false;
		}

		if ( ( $key = array_search( $sortby, array_column( $options, 'key' ) ) ) === false ) {
			return false;
		}

		$option  = $options[$key];
		$clauses = $option['clauses'];
		$orderby = [];

		foreach ( $clauses as $clause ) {
			if ( empty( $clause['context'] ) || empty( $clause['orderby'] ) || empty( $clause['order'] ) || empty( $clause['type'] ) ) {
				continue;
			}
			$clause_hash = substr( md5( json_encode( $clause ) ), 0, 16 );
			$clause_id = sprintf( 'clause-%s-%s', $option['key'], $clause_hash );

			if ( $clause['context'] === 'option' ) {
				if ( $clause['orderby'] === 'rand' ) {
					// Randomize every 3 hours.
					$seed = apply_filters( 'mylisting/explore/rand/seed', floor( time() / 10800 ) );
					$orderby[ "RAND({$seed})" ] = $clause['order'];
				} elseif ( $clause['orderby'] === 'rating' ) {
					add_filter( 'posts_join', [ $this, 'rating_field_join' ], 35, 2 );
					add_filter( 'posts_orderby', [ $this, 'rating_field_orderby' ], 35, 2 );
					$args['mylisting_orderby_rating'] = true; // Note the custom order to $args, so it's cached properly.
					$orderby[ $clause_id ] = []; // Add a dummy orderby, to override the default one.
				} elseif ( $clause['orderby'] === 'proximity' ) {
					$orderby = 'post__in';

					add_filter( 'mylisting/explore/args', function( $args ) use ( $clause ) {
						// Support descending order for distance/proximity.
						if ( $clause['order'] === 'DESC' && ! empty( $args['post__in'] ) ) {
							$args['post__in'] = array_reverse( $args['post__in'] );
						}

						return $args;
					} );
				} else {
					$orderby[ $clause['orderby'] ] = $clause['order'];
				}
			}

			if ( $clause['context'] == 'meta_key' ) {
				$args['meta_query'][ $clause_id ] = [
					'key' => '_' . $clause['orderby'],
					'compare' => 'EXISTS',
					'type' => $clause['type'],
				];

				$orderby[ $clause_id ] = $clause['order'];
			}

			if ( $clause['context'] == 'raw_meta_key' ) {
				$args['meta_query'][ $clause_id ] = [
					'key' => $clause['orderby'],
					'compare' => 'EXISTS',
					'type' => $clause['type'],
				];

				$orderby[ $clause_id ] = $clause['order'];
			}
		}

		if ( ! empty( $orderby ) ) {
			$args['orderby'] = $orderby;

			if ( isset( $args['order'] ) ) {
				unset( $args['order'] );
			}

			// Ignore order by priority if set.
			if ( ! empty( $option['ignore_priority'] ) ) {
				$args['mylisting_ignore_priority'] = true;
				remove_filter( 'mylisting/preview-card/show-badge', [ mylisting()->promotions(), 'show_promoted_badge' ], 30 );
			}
		}

		// dd($clauses, $option);
		// dd($args, $orderby);
	}

	/**
	 * Get the SQL query for getting listings within a given proximity.
	 *
	 * @link  https://wordpress.stackexchange.com/a/206560/123815
	 * @since 1.0.0
	 */
	public function get_proximity_sql() {
		global $wpdb;

		return "
			SELECT $wpdb->posts.ID,
				( %s * acos(
					cos( radians(%s) ) *
					cos( radians( latitude.meta_value ) ) *
					cos( radians( longitude.meta_value ) - radians(%s) ) +
					sin( radians(%s) ) *
					sin( radians( latitude.meta_value ) )
				) )
				AS distance, latitude.meta_value AS latitude, longitude.meta_value AS longitude
				FROM $wpdb->posts
				INNER JOIN $wpdb->postmeta
					AS latitude
					ON $wpdb->posts.ID = latitude.post_id
				INNER JOIN $wpdb->postmeta
					AS longitude
					ON $wpdb->posts.ID = longitude.post_id
				WHERE 1=1
					AND ($wpdb->posts.post_status = 'publish' )
					AND latitude.meta_key='geolocation_lat'
					AND longitude.meta_key='geolocation_long'
				HAVING distance < %s
				ORDER BY distance ASC";
	}
}
