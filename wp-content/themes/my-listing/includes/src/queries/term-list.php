<?php

namespace MyListing\Src\Queries;

class Term_List {
	use \MyListing\Src\Traits\Instantiatable;

	public function __construct() {
		add_action( 'wp_ajax_mylisting_list_terms', [ $this, 'handle' ] );
		add_action( 'wp_ajax_nopriv_mylisting_list_terms', [ $this, 'handle' ] );
	}

	public function handle() {
		mylisting_check_ajax_referrer();

		try {
			// Validate taxonomy.
			if ( empty( $_REQUEST['taxonomy'] ) ) {
				throw new \Exception( _x( 'Invalid request.', 'Term dropdown list', 'my-listing' ) );
			}

			$taxonomy = get_taxonomy( $_REQUEST['taxonomy'] );
			$page = ! empty( $_REQUEST['page'] ) ? ( absint( $_REQUEST['page'] ) - 1 ) : 0;
			$search = ! empty( $_REQUEST['search'] ) ? sanitize_text_field( $_REQUEST['search'] ) : '';
			$type_id = ! empty( $_REQUEST['listing-type-id'] ) ? absint( $_REQUEST['listing-type-id'] ) : 0;
			$value_key = ! empty( $_REQUEST['term-value'] ) && $_REQUEST['term-value'] === 'slug' ? 'slug' : 'term_id';
			$parent_slug = ! empty( $_REQUEST['parent'] ) ? sanitize_text_field( $_REQUEST['parent'] ) : false;
			$parent_id = null;

			if ( ! ( $taxonomy && $taxonomy->publicly_queryable ) ) {
				throw new \Exception( _x( 'There was an error.', 'Term dropdown list', 'my-listing' ) );
			}

			if ( $parent_slug && ( $parent = get_term_by( 'slug', $parent_slug, $taxonomy->name ) ) && ! is_wp_error( $parent ) ) {
				$parent_id = absint( $parent->term_id );
			} elseif ( isset( $_REQUEST['parent'] ) && is_numeric( $_REQUEST['parent'] ) && (int) $_REQUEST['parent'] === 0 ) {
				$parent_id = 0;
			}

			// Limit items per page only when searching terms.
			$per_page = ! empty( trim( $search ) )
				? apply_filters( 'mylisting/queries/term-list/items-per-page', 25 )
				: 0;

			$args = [
				'taxonomy' => $taxonomy->name,
				'hide_empty' => false,
				'number' => $per_page,
				'offset' => $page * $per_page,
				'search' => $search,
				'orderby' => 'name',
				'order' => 'ASC',
				'listing_type' => $type_id,
				'value_key' => $value_key,
				'parent' => $parent_id,
			];

			$terms = $this->get_terms( $args );
			if ( empty( $terms ) || is_wp_error( $terms ) ) {
				throw new \Exception( _x( 'No terms found.', 'Term dropdown list', 'my-listing' ) );
			}

			$results = [];
			foreach ( $terms as $term_id => $term_name ) {
				$results[] = [
					'id' => $term_id,
					'text' => $term_name,
				];
			}

			wp_send_json( [
				'success' => true,
				'results' => $results,
				'more' => count( $results ) === $per_page,
			] );
		} catch ( \Exception $e ) {
			wp_send_json( [
				'success' => false,
				'message' => $e->getMessage(),
			] );
		}
	}

	public function get_terms( $args = [] ) {
		$args = c27()->merge_options( [
			'taxonomy' => '',
			'orderby' => 'name',
			'order' => 'ASC',
			'hide_empty' => false,
			'listing_type' => '',
			'value_key' => 'term_id',
			'number' => 0,
			'offset' => 0,
			'search' => '',
			'meta_query' => [],
			'parent' => null,
		], $args );

		$args['search'] = trim( $args['search'] );

		if ( empty( $args['taxonomy'] || ! taxonomy_exists( $args['taxonomy'] ) ) ) {
			return [];
		}

		$query_args = [
			'taxonomy' => $args['taxonomy'],
			'orderby' => $args['orderby'],
			'order' => $args['order'],
			'hide_empty' => $args['hide_empty'],
			'number' => $args['number'],
			'offset' => $args['offset'],
			'meta_query' => $args['meta_query'],
		];

		// Match terms against search term.
		if ( ! empty( $args['search'] ) ) {
			$query_args['search'] = $args['search'];
		}

		if ( $args['parent'] !== null ) {
			$query_args['parent'] = $args['parent'];
		}

		// Filter by listing type.
		if ( ! empty( $args['listing_type'] ) ) {
			$query_args['meta_query'][] = [
				'relation' => 'OR',
				[
					'key' => 'listing_type',
					'value' => '"' . $args['listing_type'] . '"',
					'compare' => 'LIKE',
				],
				[
					'key' => 'listing_type',
					'value' => '',
				],
				[
					'key' => 'listing_type',
					'compare' => 'NOT EXISTS',
				]
			];
		}

		// If available, fetch terms from cache.
		$cache_version = get_option( sprintf( 'listings_tax_%s_version', $args['taxonomy'] ), 100 );
		$terms_hash = sprintf( 'c27_cats_%s_v%s', md5( json_encode( $query_args ) ), $cache_version );
		$terms = get_transient( $terms_hash );

		// Otherwise, query.
		if ( empty( $terms ) ) {
		    $terms = get_terms( $query_args );
		    set_transient( $terms_hash, $terms, HOUR_IN_SECONDS * 6 );
		}

		if ( is_wp_error( $terms ) ) {
			return [];
		}

		// If there's no search input, then sort items hierarchically.
		if ( empty( $args['search'] ) && empty( $args['parent'] ) ) {
			$items = [];
			\MyListing\Src\Term::iterate_recursively(
			    function( $term, $depth ) use ( &$items, $args ) {
			       	$items[ $term->{$args['value_key']} ] = str_repeat( '&mdash; ', ( $depth - 1 ) ) . $term->name;
			    },
			    \MyListing\Src\Term::get_term_tree( $terms )
			);

			return $items;
		}

		// Otherwise, return in matched order.
		$items = [];
		foreach ( $terms as $term ) {
			$items[ $term->{$args['value_key']} ] = preg_replace( '/('.$args['search'].')/i', "<span class=\"hh\">$1</span>", $term->name );
		}

		return $items;
	}
}