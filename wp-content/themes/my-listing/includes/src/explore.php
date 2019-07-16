<?php

namespace MyListing\Src;

use \MyListing\Src\Term;
use \MyListing\Ext\Listing_Types\Listing_Type as Listing_Type;

class Explore {
	public
		$active_tab = false,
		$active_category = false,
		$active_listing_type = false,
		$active_terms = [],
		$data = [];

	public $store = [];
	public static
		$custom_taxonomies,
		$explore_page;

	public static function init() {
		self::$custom_taxonomies = mylisting_custom_taxonomies();

		add_action( 'init', [ __CLASS__, 'add_rewrite_rules' ] );
		add_action( 'template_redirect', [ __CLASS__, 'handle_term_landing_page' ], 30 );
		add_filter( 'pre_get_document_title', [ __CLASS__, 'filter_page_title' ], 10e3, 1 );
		add_filter( 'wpseo_metadesc', [ __CLASS__, 'filter_page_description' ], 30 );
	}

	public function __construct( $data ) {
		$this->data = $data;
		$this->parse_active_tab();
		$this->parse_listing_types();
		$this->parse_categories();
	}

	public function get_data( $key = null ) {
		if ( $key && isset( $this->data[ $key ] ) ) {
			return $this->data[ $key ];
		}

		return $this->data;
	}

	public function get_active_tab() {
		return $this->active_tab;
	}

	public function get_active_mobile_tab() {
		$tabs = ['f' => 'filters', 'r' => 'results', 'm' => 'map'];
		if ( ! empty( $_GET['mt'] ) && isset( $tabs[ $_GET['mt'] ] ) ) {
			return $tabs[ $_GET['mt'] ];
		}
		return 'results';
	}

	public function get_active_listing_type() {
		return $this->active_listing_type;
	}

	public function get_active_category() {
		return $this->active_category;
	}

	public function parse_listing_types() {
		$this->store['listing_types'] = array_filter( array_map( function( $listing_type ) {
			if ( ! ( $listing_type_obj = ( get_page_by_path( $listing_type, OBJECT, 'case27_listing_type' ) ) ) ) {
				return false;
			}

			return new Listing_Type( $listing_type_obj );
		}, array_column( (array) $this->data['listing_types'], 'type') ) );

		$this->parse_active_listing_type();

		// If the active tab is 'listing-types', but there's only one listing type,
		// show the filters tab instead.
		if ( $this->active_tab == 'listing-types' && count( $this->store['listing_types'] ) === 1 ) {
			$this->active_tab = 'search-form';
		}
	}

	public function parse_active_listing_type() {
		if ( empty( $this->store[ 'listing_types' ] ) ) {
			$this->active_listing_type = false;
			return false;
		}

		$this->active_listing_type = $this->store['listing_types'][0];

		if ( isset( $_GET['type'] ) && ( $getType = sanitize_text_field( $_GET['type'] ) ) ) {
			foreach ($this->store['listing_types'] as $listing_type) {
				if ( $listing_type->get_slug() == $getType ) {
					$this->active_listing_type = $listing_type;
					break;
				}
			}
		}
	}

	public function parse_active_tab() {
		$possible_tabs = [ 'search-form', 'categories', 'regions', 'tags' ];

		foreach ( self::$custom_taxonomies as $slug => $label ) {
			$possible_tabs[] = $slug;
		}

		// First check if the tab is available as a query var. This has the highest priority.
		if ( in_array( get_query_var( 'explore_tab' ), $possible_tabs ) ) {
			$this->active_tab = get_query_var( 'explore_tab' );
		}
		// Then check if the tab is provided as a GET param.
		// This is needed to maintain backwards compatibility.
		elseif ( ! empty( $_GET['tab'] ) && in_array( $_GET['tab'], $possible_tabs ) ) {
			$this->active_tab = sanitize_text_field( $_GET['tab'] );
		}
		// See if the active tab is provided through Elementor widget settings.
		elseif ( in_array( $this->data['active_tab'], $possible_tabs ) ) {
			$this->active_tab = $this->data['active_tab'];
		}
		// Otherwise, default to the 'search-form' tab.
		else {
			$this->active_tab = 'search-form';
		}
	}

	public function parse_categories() {
		$this->store['category-items'] = [];
		foreach ($this->store['listing_types'] as $type) {
			$this->store['category-items'][ $type->get_slug() ] = [];

			$args = [
				'number'     => $this->data['categories']['count'],
				'order'      => $this->data['categories']['order'],
				'orderby'    => $this->data['categories']['order_by'],
				'taxonomy'   => 'job_listing_category',
				'hide_empty' => $this->data['categories']['hide_empty'],
				'pad_counts' => false,
				'meta_query' => [
					'relation' => 'OR',
					[
						'key' => 'listing_type',
						'value' => '"' . $type->get_id() . '"',
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
				],
			];

            $cache_version = get_option( 'listings_tax_' . 'job_listing_category' . '_version', 100 );
            // dump($cache_version);
            $categories_hash = 'c27_cats_' . md5( json_encode( $args ) ) . '_v' . $cache_version;
            $terms = get_transient( $categories_hash );

            if ( empty( $terms ) ) {
                $terms = get_terms( $args );
                set_transient( $categories_hash, $terms, HOUR_IN_SECONDS * 6 );
                // dump( 'Loaded via db query' );
            } else {
                // dump( 'Loaded from cache' );
            }


			if ( is_wp_error( $terms ) ) {
				continue;
			}

			foreach ($terms as $key => $term) {
				if ( is_wp_error( $term ) ) {
					unset( $terms[ $key ] );
					continue;
				}

				$terms[ $key ]->listing_type = array_filter( array_map( function( $type_id ) {
					if ( is_numeric( $type_id ) && $slug = get_post_field( 'post_name', absint( $type_id ) ) ) {
						return $slug;
					}

					return false;
				}, (array) get_term_meta( $term->term_id, 'listing_type', true ) ) );
			}
			// endcache

			foreach ( $terms as $term ) {
				// $this->store['category-items'][ 'term_' . $term->term_id ] = new Term( $term );
				$this->store['category-items'][ $type->get_slug() ][ 'term_' . $term->term_id ] = new Term( $term );
			}
		}

		$this->parse_active_category();
	}

	public function parse_active_category() {
		// Determine active category.
		if ( get_query_var( 'explore_category' ) && ( $term = get_term_by( 'slug', get_query_var( 'explore_category' ), 'job_listing_category' ) ) ) {
			$this->active_category = new Term( $term );
			$this->active_category->active = true;
		} elseif ( ! empty( $_GET['cid'] ) && ( $term = get_term_by( 'id', absint( $_GET['cid'] ), 'job_listing_category' ) ) ) {
			$this->active_category = new Term( $term );
			$this->active_category->active = true;
		} elseif ( ! empty( $this->store['category-items'] ) && $this->active_listing_type && ! empty( $this->store['category-items'][ $this->active_listing_type->get_slug() ] ) ) {
			foreach ($this->store['category-items'][ $this->active_listing_type->get_slug() ] as $term) {
				$this->active_category = $term;
				break;
			}
		} else {
			$this->active_category = false;
		}

		// Insert the active category as the first item in the "Categories" tab.
		if ( $this->active_category && $this->active_category->is_active() ) {
			foreach ($this->store['category-items'] as $group_key => $term_group) {
				if ( isset( $term_group[ 'term_' . $this->active_category->get_id() ] ) ) {
					unset( $term_group[ 'term_' . $this->active_category->get_id() ] );
				}

				$this->store['category-items'][ $group_key ] = [
						'term_' . $this->active_category->get_id() => $this->active_category,
					] + $this->store['category-items'][ $group_key ];
			}
		}

		$this->parse_active_taxonomies();
	}

	public function parse_active_taxonomies() {
		if ( get_query_var( 'explore_region' ) && ( $term = get_term_by( 'slug', sanitize_title( get_query_var( 'explore_region' ) ), 'region' ) ) ) {
			$this->active_terms['regions'] = new Term( $term );
		}

		if ( get_query_var( 'explore_tag' ) && ( $term = get_term_by( 'slug', sanitize_title( get_query_var( 'explore_tag' ) ), 'case27_job_listing_tags' ) ) ) {
			$this->active_terms['tags'] = new Term( $term );
		}

		foreach ( self::$custom_taxonomies as $slug => $label ) {
			if ( get_query_var( 'explore_'.$slug ) && ( $term = get_term_by( 'slug', sanitize_title( get_query_var( 'explore_'.$slug ) ), $slug ) ) ) {
				$this->active_terms[ $slug ] = new Term( $term );
			}
		}
	}

	public function get_active_taxonomy() {
		if ( in_array( $this->active_tab, array_merge( ['categories', 'regions', 'tags'], array_keys( self::$custom_taxonomies ) ) ) ) {
			return $this->active_tab;
		}

		return false;
	}

	public function get_taxonomy_data() {
        $taxonomies = [];
        $taxonomies['categories'] = [
        	'tax' => 'job_listing_category',
        	'term' => $this->active_category ? $this->active_category->get_id() : false,
        	'page' => 0,
        ];

        $taxonomies['regions'] = [
        	'tax' => 'region',
        	'term' => ! empty( $this->active_terms['regions'] ) ? $this->active_terms['regions']->get_id() : false,
        	'page' => 0,
        ];

        $taxonomies['tags'] = [
        	'tax' => 'case27_job_listing_tags',
        	'term' => ! empty( $this->active_terms['tags'] ) ? $this->active_terms['tags']->get_id() : false,
        	'page' => 0,
        ];

        foreach ( self::$custom_taxonomies as $slug => $label ) {
	        $taxonomies[ $slug ] = [
	        	'tax' => $slug,
	        	'term' => ! empty( $this->active_terms[ $slug ] ) ? $this->active_terms[ $slug ]->get_id() : false,
	        	'page' => 0,
	        ];
        }

        return $taxonomies;
	}

	/**
	 * Add rewrite rules for pretty url-s in Explore page.
	 * e.g. site/explore/category/category-name
	 * 		site/explore/regions/region-name
	 * 		site/explore/tags/tag-name
	 */
	public static function add_rewrite_rules() {
		// Stack overflow link: https://wordpress.stackexchange.com/questions/89164/passing-parameters-to-a-custom-page-template-using-clean-urls
		if ( ! ( $explore_page_id = c27()->get_setting( 'general_explore_listings_page', false ) ) ) {
			return;
		}

		if ( ! ( $explore_page = get_post( url_to_postid( $explore_page_id ) ) ) ) {
			return;
		}

		self::$explore_page = $explore_page;

		// Add query vars.
		global $wp;
	    $wp->add_query_var( 'explore_tab' );

    	$bases = c27()->get_permalink_structure();

		// default taxonomies
		self::_rewrite_listing_taxonomy( 'category', $bases['category_base'], 'categories' );
		self::_rewrite_listing_taxonomy( 'region', $bases['region_base'], 'regions' );
		self::_rewrite_listing_taxonomy( 'tag', $bases['tag_base'], 'tags' );

		// custom taxonomies
		foreach ( self::$custom_taxonomies as $slug => $label ) {
			self::_rewrite_listing_taxonomy( $slug, $slug );
		}
	}

	private static function _rewrite_listing_taxonomy( $taxonomy, $base, $explore_tab = null ) {
		if ( $explore_tab === null ) {
			$explore_tab = $taxonomy;
		}

		// rewrite tag
    	add_rewrite_tag( '%explore_'.$taxonomy.'%', '([^/]+)' );

    	// rewrite rule
	    add_rewrite_rule(
	    	sprintf( '^%s/([^/]+)?', $base ),
	    	sprintf( 'index.php?page_id=%d&explore_tab=%s&explore_%s=$matches[1]', self::$explore_page->ID, $explore_tab, $taxonomy ),
	    	'top'
	    );
	}

	/**
	 * Since taxonomy archive pages are redirected to the Explore page,
	 * Filter the page title to show the taxonomy information, instead of
	 * the default Explore page title.
	 *
	 * @since 1.6.2
	 */
	public static function filter_page_title( $title ) {
	    $taxonomies = [
	        ['tax' => 'region',                  'query_var' => 'explore_region',   'name_filter' => 'single_term_title'],
	        ['tax' => 'job_listing_category',    'query_var' => 'explore_category', 'name_filter' => 'single_cat_title'],
	        ['tax' => 'case27_job_listing_tags', 'query_var' => 'explore_tag',      'name_filter' => 'single_tag_title'],
	    ];

	    foreach ( $taxonomies as $tax ) {
	        if ( get_query_var( $tax['query_var'] ) && ( $term = get_term_by( 'slug', sanitize_title( get_query_var( $tax['query_var'] ) ), $tax['tax'] ) ) ) {
	            $title = apply_filters( $tax['name_filter'], $term->name );
	            $title .= ' ' . apply_filters( 'document_title_separator', '-' ) . ' ';
	            $title .= get_bloginfo( 'name', 'display' );

	            $title = convert_chars( wptexturize( $title ) );
	            $title = capital_P_dangit( esc_html( $title ) );

	            return $title;
	        }
	    }

	    return $title;
	}

	/**
	 * Filter the Explore page description added through Yoast SEO plugin,
	 * in case it's showing single term (category, region, tag) results.
	 *
	 * @since 1.6.2
	 */
	public static function filter_page_description( $description ) {
	    $taxonomies = [
	        ['tax' => 'region',                  'query_var' => 'explore_region',   'name_filter' => 'single_term_title'],
	        ['tax' => 'job_listing_category',    'query_var' => 'explore_category', 'name_filter' => 'single_cat_title'],
	        ['tax' => 'case27_job_listing_tags', 'query_var' => 'explore_tag',      'name_filter' => 'single_tag_title'],
	    ];

	    foreach ( $taxonomies as $tax ) {
	        if ( get_query_var( $tax['query_var'] ) && ( $term = get_term_by( 'slug', sanitize_title( get_query_var( $tax['query_var'] ) ), $tax['tax'] ) ) ) {
	            if ( $term->description ) {
	                return $term->description;
	            }

	            break;
	        }
	    }

	    return $description;
	}

	/**
	 * Handle redirects for terms with custom landing pages.
	 *
	 * @since 1.7.0
	 */
	public static function handle_term_landing_page() {
	    $taxonomies = [
	        ['tax' => 'region',                  'query_var' => 'explore_region'  ],
	        ['tax' => 'job_listing_category',    'query_var' => 'explore_category'],
	        ['tax' => 'case27_job_listing_tags', 'query_var' => 'explore_tag'     ],
	    ];

	    foreach ( $taxonomies as $tax ) {
	        if (
	            get_query_var( $tax['query_var'] ) &&
	            ( $term = get_term_by( 'slug', sanitize_title( get_query_var( $tax['query_var'] ) ), $tax['tax'] ) ) &&
	            ( $redirect_page = get_term_meta( $term->term_id, '_landing_page', true ) ) &&
	            is_numeric( $redirect_page ) &&
	            ( $redirect_url = get_permalink( absint( $redirect_page ) ) )
	        ) {
	            wp_redirect( $redirect_url );
	            exit;
	        }
	    }
	}
}
