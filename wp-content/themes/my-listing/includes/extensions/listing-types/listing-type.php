<?php

namespace MyListing\Ext\Listing_Types;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Listing_Type {

	public static $instances = [];

	public
		$data,
		$fields,
		$single,
		$preview,
		$search,
		$settings;

	/**
	 * Get a new listing instance (Multiton pattern).
	 * When called the first time, listing will be fetched from database.
	 * Otherwise, it will return the previous instance.
	 *
	 * @since 1.6.0
	 * @param $listing int or \WP_Post
	 */
	public static function get( $type ) {
		if ( is_numeric( $type ) ) {
			$type = get_post( $type );
		}

		if ( ! $type instanceof \WP_Post ) {
			return false;
		}

		if ( $type->post_type !== 'case27_listing_type' ) {
			return false;
		}

		if ( ! array_key_exists( $type->ID, self::$instances ) ) {
			self::$instances[ $type->ID ] = new self( $type );
		}

		return self::$instances[ $type->ID ];
	}

	/**
	 * Retrieve a listing type object by it's post name (slug).
	 *
	 * @since 2.0
	 */
	public static function get_by_name( $postname ) {
		$type_obj = get_page_by_path( $postname, OBJECT, 'case27_listing_type' );
		if ( ! $type_obj ) {
			return false;
		}

		return self::get( $type_obj );
	}

	public function __construct( \WP_Post $post ) {
		self::$instances[ $post->ID ] = $this;
		$this->data     = $post;

        $fields = get_post_meta( $this->get_id(), 'case27_listing_type_fields', true );
        $single = get_post_meta( $this->get_id(), 'case27_listing_type_single_page_options', true );
        $preview = get_post_meta( $this->get_id(), 'case27_listing_type_result_template', true );
        $search = get_post_meta( $this->get_id(), 'case27_listing_type_search_page', true );
        $settings = get_post_meta( $this->get_id(), 'case27_listing_type_settings_page', true );

		$this->fields = is_serialized( $fields ) ? @unserialize( $fields ) : [];

		$this->single = array_replace_recursive(
			mylisting()->schemes()->get('single'),
			is_serialized( $single ) ? @unserialize( $single ) : []
		);

		$this->preview = array_replace_recursive(
			mylisting()->schemes()->get('result'),
			is_serialized( $preview ) ? @unserialize( $preview ) : []
		);

		$this->search = array_replace_recursive(
			mylisting()->schemes()->get('search'),
			is_serialized( $search ) ? @unserialize( $search ) : []
		);

		$this->settings = array_replace_recursive(
			mylisting()->schemes()->get('settings'),
			is_serialized( $settings ) ? @unserialize( $settings ) : []
		);
	}

	public function get_id() {
		return $this->data->ID;
	}

	public function get_name() {
		return $this->data->post_title;
	}

	public function get_slug() {
		return $this->data->post_name;
	}

	public function get_permalink_name() {
		return sanitize_title( $this->settings['permalink'] ? : $this->get_slug() );
	}

	public function get_singular_name() {
		return $this->settings['singular_name'] ? : $this->data->post_title;
	}

	public function get_plural_name() {
		return $this->settings['plural_name'] ? : $this->data->post_title;
	}

	public function get_data( $key = null ) {
		if ( $key ) {
			if ( isset( $this->data->$key ) ) {
				return $this->data->$key;
			}

			return null;
		}

		return $this->data;
	}

	public function get_fields() {
		return $this->fields;
	}

	public function get_layout() {
		return $this->single;
	}

	public function get_field( $key = null ) {
		if ( $key && ! empty( $this->fields[ $key ] ) ) {
			return $this->fields[ $key ];
		}

		return false;
	}

	public function get_default_logo( $size = 'thumbnail' ) {
		if ( $image = wp_get_attachment_image_src( $this->get_data( 'default_logo' ), $size ) ) {
			return $image[0];
		}

		return false;
	}

	public function get_default_cover( $size = 'large' ) {
		if ( $image = wp_get_attachment_image_src( $this->get_data( 'default_cover_image' ), $size ) ) {
			return $image[0];
		}

		return false;
	}

	public function get_settings() {
		return $this->settings;
	}

	public function get_preview_options() {
		return (array) $this->preview;
	}

	public function get_packages() {
		return $this->settings['packages']['used'];
	}

	public function is_rating_enabled() {
		return (bool) $this->settings['reviews']['ratings']['enabled'];
	}

	/**
	 * Determine whether users are allowed to submit
	 * multiple comments on listings of this listing type.
	 *
	 * @since 2.0
	 */
	public function multiple_comments_allowed() {
		return ! $this->is_rating_enabled() && $this->settings['reviews']['multiple'];
	}

	/**
	 * Check if this is a global listing type.
	 * Global types can be used in the Explore page to query
	 * results within all other listing types.
	 *
	 * @since 1.6.0
	 */
	public function is_global() {
		return (bool) $this->settings['global'];
	}

	public function get_review_mode() {
		return $this->settings['reviews']['ratings']['mode'];
	}

	public function get_review_categories() {
		$defaults = [
			'rating' => [
				'id'    => 'rating',
				'label' => esc_html__( 'Your Rating', 'my-listing' ),
			],
		];

		$_categories = $this->settings['reviews']['ratings']['categories'];

		if ( $_categories && is_array( $_categories ) ) {
			$categories = [];

			// Sanitize: make sure all required keys available.
			foreach ( $_categories as $category ) {
				$category = wp_parse_args( $category, [
					'id'    => '',
					'label' => '',
				]);

				if ( $category['id'] ) {
					$categories[ $category['id'] ] = $category;
				}
			}

			return $categories;
		}

		return $defaults;
	}

	/**
	 * Get Explore page ordering options.
	 * Values are parsed as following:
	 * Context: option; value: ':option' (prepend option name with colon)
	 * Context: meta_key; value: 'field_key'
	 * Context: raw_meta_key; value: '_raw_field_key' (prepend field key with underscore)
	 *
	 * @since 1.6.0
	 * @return array
	 */
	public function get_ordering_options() {
		$defaults = [
			[
				'label' => _x( 'Latest', 'Explore listings: Order by listing date', 'my-listing' ),
				'key' => 'latest',
				'ignore_priority' => false,
				'clauses' => [[
					'orderby' => 'date',
					'order' => 'DESC',
					'context' => 'option',
					'type' => 'CHAR',
					'custom_type' => false,
				]],
			],
			[
				'label' => _x( 'Top rated', 'Explore listings: Order by rating value', 'my-listing' ),
				'key' => 'top-rated',
				'ignore_priority' => true,
				'clauses' => [[
					'orderby' => 'rating',
					'order' => 'DESC',
					'context' => 'option',
					'type' => 'DECIMAL(10,2)',
					'custom_type' => false,
				]],
			],
			[
				'label' => _x( 'Random', 'Explore listings: Order randomly', 'my-listing' ),
				'key' => 'random',
				'ignore_priority' => false,
				'clauses' => [[
					'orderby' => 'rand',
					'order' => 'DESC',
					'context' => 'option',
					'type' => 'CHAR',
					'custom_type' => false,
				]],
			],
		];

		$_options = $this->search['order']['options'];

		if ( $_options && is_array( $_options ) ) {
			$options = [];

			foreach ( (array) $_options as $option ) {
				if ( empty( $option['key'] ) || empty( $option['label'] ) || empty( $option['clauses'] ) ) {
					continue;
				}

				if ( empty( $option['ignore_priority'] ) ) {
					$option['ignore_priority'] = false;
				}

				foreach ( (array) $option['clauses'] as $clause ) {
					if ( empty( $clause['orderby'] ) || empty( $clause['order'] ) || empty( $clause['context'] ) || empty( $clause['type'] ) ) {
						continue(2);
					}

					if ( $clause['context'] === 'option' && $clause['orderby'] === 'proximity' ) {
						if ( empty( $options['notes'] ) ) {
							$option['notes'] = [];
						}

						$option['notes'][] = 'has-proximity-clause';
					}
				}

				$options[] = $option;
			}

			if ( ! empty( $options ) ) {
				return $options;
			}
		}

		return $defaults;
	}

	public function is_gallery_enabled() {
		return (bool) $this->settings['reviews']['gallery']['enabled'];
	}

	public function get_package( $package_id ) {
		foreach ($this->settings['packages']['used'] as $package) {
			if ( $package['package'] == $package_id ) {
				return $package;
			}
		}

		return false;
	}

	/**
	 * Get list of search filters for this listing type.
	 *
	 * @since  1.5.1
	 *
	 * @param  $form Name of form to retrieve filters for. Either advanced or basic form.
	 * @return array $filters
	 */
	public function get_search_filters( $form = 'advanced' ) {
		$filters = $this->search[ $form ][ 'facets' ];

		$filters = array_map( function( $filter ) {
			if ( ! isset( $filter['show_field'] ) ) {
				$filter['show_field'] = '';
			}

			if ( ! isset( $filter['url_key'] ) ) {
				$filter['url_key'] = $filter['show_field'];
			}

			// Get clean filter names, without the 'job_' prefix, to be used in Explore page url.
			if ( ! empty( $filter['show_field'] ) && in_array( $filter['show_field'], \MyListing\Src\Listing::$aliases ) ) {
				$filter['url_key'] = array_search( $filter['show_field'], \MyListing\Src\Listing::$aliases );
			}

			return $filter;
		}, $filters );

		return $filters;
	}

	public function get_setting( $key = null ) {
		if ( $key && ! empty( $this->settings[ $key ] ) ) {
			return $this->settings[ $key ];
		}

		return false;
	}

	public function get_schema_markup() {
		if ( empty( $this->settings['seo']['markup'] ) || ! is_array( $this->settings['seo']['markup'] ) ) {
			return mylisting()->schemes()->get('schema/LocalBusiness');
		}

		return $this->settings['seo']['markup'];
	}

	public function get_image( $size = 'large' ) {
		$image = wp_get_attachment_image_src( get_post_thumbnail_id( $this->data->ID ), $size );

		return $image ? array_shift( $image ) : false;
	}

	public function get_count() {
		// @todo: Find a way to get the post count without querying on every page load.
		// Using transients or something.

		return 0;
	}

    /**
     * Get listing type config.
     *
     * @since  1.7.5
     * @return array|false
     */
	public function get_config() {
        return [
            'fields' => [ 'used' => $this->fields ],
            'single' => $this->single,
            'result' => $this->preview,
            'search' => $this->search,
            'settings' => $this->settings,
        ];
	}
}
