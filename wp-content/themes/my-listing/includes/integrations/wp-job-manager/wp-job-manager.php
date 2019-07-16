<?php

namespace CASE27\Integrations\WPJM;

class ListingManager {
	use \MyListing\Src\Traits\Instantiatable;

	public function __construct() {
		// Add submit/edit/admin fields to listing types.
		require_once CASE27_INTEGRATIONS_DIR . '/wp-job-manager/listing-fields/index.php';

		// Rewrite post type slug from 'job' to 'listing'.
		add_filter('register_post_type_job_listing', [$this, 'register_post_type_job_listing']);

		add_filter( 'option_category_base', function( $base ) {
			if ( ! $base || $base == 'category' ) {
				return 'post-category';
			}

			return $base;
		});

		add_filter( 'option_tag_base', function( $base ) {
			if ( ! $base || $base == 'tag' ) {
				return 'post-tag';
			}

			return $base;
		});

		add_filter( 'pre_option_job_category_base', function( $base ) {
			if ( ! $base || $base == 'listing-category' || $base == 'job-category' ) {
				return 'category';
			}

			return $base;
		});

		add_filter( 'enter_title_here', function( $text, $post ) {
			if ( $post->post_type == 'job_listing' ) {
				return _x( 'Title', 'Listing title placeholder', 'my-listing' );
			}

			return $text;
		}, 30, 2 );

		// Update WP Job Manager template override folder.
		add_filter( 'job_manager_locate_template', [ $this, 'locate_template' ], 10, 3 );

		// Edit 'Listings' page columns in admin area.
		add_filter( 'manage_edit-job_listing_columns', [$this, 'admin_columns_head'], 10 );

		// Replace the default company name, url, and logo with the listing type and listing logo.
		add_filter( 'the_company_website', '__return_false', 10, 2 );
		add_filter( 'the_company_name', '__return_false', 10, 2 );
		add_filter( 'manage_job_listing_posts_custom_column', [ $this, 'admin_columns' ], 35 );
		add_filter( 'job_manager_default_company_logo', [$this, 'job_manager_default_company_logo'], 10, 2 );
		add_filter( 'pre_option_job_manager_enable_categories', '__return_true' );

		add_filter( 'job_manager_update_job_data', [$this, 'save_listing'], 30, 2 );

		// Add support for comments/reviews on listings.
		add_post_type_support('job_listing', 'comments');

		// Remove company name, location, and job type - from the listing permalink structure.
		// This way, only the listing name is left in a shorter and cleaner url format.
		add_filter( 'submit_job_form_prefix_post_name_with_company', '__return_false' );
		add_filter( 'submit_job_form_prefix_post_name_with_location', '__return_false' );
		add_filter( 'submit_job_form_prefix_post_name_with_job_type', '__return_false' );

		// Save terms order done through drag & drop.
		add_action( 'job_manager_update_job_data', [ $this, 'save_terms_order' ], 10, 2 );
		add_filter( 'submit_job_form_validate_fields', [$this, 'validate_fields'], 10, 3 );

		// My Listings Dashboard.
		add_action( 'wp', array( $this, 'my_listings_action_handler' ) );
		add_filter( 'job_manager_my_job_actions', [$this, 'my_listings_actions'], 10, 2 );
		add_filter( 'job_manager_get_dashboard_jobs_args', [$this, 'my_listings_args'] );
		add_filter( 'job_manager_pagination_args', [$this, 'my_listings_pagination'] );
		add_filter( 'job_manager_chosen_enabled', '__return_false');
		add_filter( 'list_product_cats', [ $this, 'list_product_cats' ], 10, 2 );
		add_filter( 'job_manager_settings', [ $this, 'wpjm_settings' ], 30 );
		add_action( 'init', function() {
			global $wp_post_statuses;

			if ( ! is_array( $wp_post_statuses ) ) {
				$wp_post_statuses = [];
			}

			// @todo: make 'public' status optional, so expired listings can also be accessed from url (optionally).
			if ( ! empty( $wp_post_statuses['expired'] ) ) {
				$wp_post_statuses['expired']->public = false;
			}
		}, 30 );
		add_filter( 'pre_option_job_manager_enable_types', function($opt) {
			return "0";
		}, 1050 );

        add_filter( 'case27\listing\cover\field\job_location', [ $this, 'filter_listing_address' ] );
        add_filter( 'case27\listing\preview\info_field\job_location', [ $this, 'filter_listing_address' ] );
        add_filter( 'case27\listing\preview\button\job_location', [ $this, 'filter_listing_address' ] );
        add_filter( 'case27\listing\preview\detail\job_location', [ $this, 'filter_listing_address' ] );
        add_filter( 'case27\listing\preview\quick_view\job_location', [ $this, 'filter_listing_address' ] );
        add_filter( 'wpjm_output_job_listing_structured_data', '__return_false', 10e3 );

		add_filter( 'job_manager_get_posted_term_select_field', function() {
			return function( $key, $field ) {
				if ( ! empty( $field['terms-template'] ) && in_array( $field['terms-template'], [ 'single-select', 'multiselect', 'checklist'] ) ) {
					$template = $field['terms-template'];
				} else {
					$template = 'multiselect';
				}

				$value = ! empty( $_POST[ $key ] ) ? (array) $_POST[ $key ] : [];

				if ( $template == 'single-select' ) {
					return ! empty( $value[0] ) && $value[0] > 0 ? absint( $value[0] ) : '';
				}

				if ( $template == 'multiselect' || $template == 'checklist' ) {
					return array_map( 'absint', $value );
				}
			};
		} );

		add_filter( 'job_manager_get_posted_url_field', function() {
			return function( $key, $field ) {
				$value = ! empty( $_POST[ $key ] ) ? $_POST[ $key ] : '';
				return esc_url_raw( $value );
			};
		} );

		add_filter( 'job_manager_get_posted_links_field', function() {
			return function( $key, $field ) {
				$value = ! empty( $_POST[ $key ] ) ? (array) $_POST[ $key ] : [];
				$links = array_map(  function( $val ) {
					if ( ! is_array( $val ) || empty( $val['network'] ) || empty( $val['url'] ) ) {
						return false;
					}

					return [
						'network' => sanitize_text_field( stripslashes( $val['network'] ) ),
						'url' => esc_url_raw( $val['url'] ),
					];
				}, $value );

				return array_filter( $links );
			};
		} );

		add_filter( 'job_manager_mime_types', function() {
			return get_allowed_mime_types();
		}, 30 );

		add_filter( 'job_manager_show_addons_page', '__return_false' );

		add_action( 'current_screen', [ $this, 'setup_permalink_settings' ], 10 );
		add_action( 'register_taxonomy_args', [ $this, 'setup_permalinks' ], 10, 2 );

		add_filter( 'job_manager_enable_registration', '__return_false', 50 );
		add_filter( 'wpjm_get_registration_fields', '__return_empty_array', 50 );

		// "Skip preview" functionality.
		add_action( 'submit_job_steps', [ $this, 'maybe_skip_preview' ] );
		add_action( 'job_manager_job_submitted', [ $this, 'skip_preview_handler' ] );

		// Prevent external listing images from creating attachments.
		add_filter( 'wp_insert_post_empty_content', [ $this, 'handle_external_images' ], 30, 2 );

		add_action( 'admin_init', [ $this, 'admin_listing_taxonomies' ] );

		// Filter listings by listing type in WP Admin > Listings.
		add_filter( 'parse_query', [ $this, 'filter_listings_by_type' ] );

		/**
	     * Prevent WP Job Manager from un-indexing listings.
		 *
	     * @link  https://helpdesk.27collective.net/questions/question/noindex-on-listings/
	     * @since 2.0
		 */
		add_filter( 'wpjm_allow_indexing_job_listing', '__return_true' );

		add_action( 'admin_menu', [ $this, 'admin_menu' ], 25 );
	}


	public function my_listings_action_handler() {
		global $post;

		if ( $post && has_shortcode($post->post_content, 'woocommerce_my_account' ) ) {
			\WP_Job_Manager_Shortcodes::instance()->job_dashboard_handler();
		}
	}


	public function my_listings_actions($actions, $job) {
		unset( $actions['mark_filled'] );
		unset( $actions['duplicate'] );
		unset( $actions['relist'] );

		return $actions;
	}


	public function my_listings_args( $args ) {
		unset($args['offset']);
		$args['paged'] = isset($_GET['listings_page']) ? absint($_GET['listings_page']) : 1;

		return $args;
	}


	public function my_listings_pagination($args) {
		global $post;

		if (is_page() && has_shortcode($post->post_content, 'woocommerce_my_account')) {
			unset($args['base']);
			$args['format'] = '?listings_page=%#%';
			$args['current'] = isset($_GET['listings_page']) ? absint($_GET['listings_page']) : 1;
		}

		return $args;
	}

	public function register_post_type_job_listing($args) {
		unset( $args['supports'][ array_search( 'editor', $args['supports'] ) ] );
		unset( $args['labels']['featured_image'] );
		unset( $args['labels']['set_featured_image'] );
		unset( $args['labels']['remove_featured_image'] );
		unset( $args['labels']['use_featured_image'] );

		return $args;
	}

	/**
	 * Override WPJM templates within the theme.
	 *
	 * @since 1.0
	 */
	public function locate_template( $template, $template_name, $template_path ) {
		$aliases = [
			'job-dashboard.php' => 'templates/dashboard/my-listings.php',
			'job-submit.php' => 'templates/add-listing/submit-form.php',
			'job-preview.php' => 'templates/add-listing/preview.php',
		];

		if ( isset( $aliases[ $template_name ] ) ) {
			$location = locate_template( $aliases[ $template_name ] );
		} else {
			$location = locate_template( sprintf( 'includes/integrations/wp-job-manager/templates/%s', $template_name ) );
		}

		// Only override if file exists.
		if ( $location ) {
			$template = $location;
		}

		return apply_filters( 'case27_job_manager_locate_template', $template, $template_name, $template_path );
	}

	public function admin_columns_head( $defaults ) {
		unset($defaults['filled']);
		unset($defaults['job_listing_type']);

		return [
			'cb' => $defaults['cb'],
			'job_position' => 'Name',
			'job_location' => '<span class="dashicons dashicons-location"></span> ' . __( 'Location', 'my-listing' ),
			'job_listing_category' => '<span class="dashicons dashicons-paperclip"></span> ' . __( 'Categories', 'my-listing' ),
			'taxonomy-case27_job_listing_tags' => '<span class="dashicons dashicons-tag"></span> ' . __( 'Tags', 'my-listing' ),
			'comments' => '<span class="dashicons dashicons-admin-comments"></span> ' . __( 'Reviews', 'my-listing' ),
			'job_expires' => '<span class="dashicons dashicons-clock"></span> ' . __( 'Expires', 'my-listing' ),
		] + $defaults;
	}

	public function admin_columns( $column ) {
		global $post;
		if ( ! ( $listing = \MyListing\Src\Listing::get( $post ) ) ) {
			return;
		}

		if ( $column === 'job_position' && $listing->type ) {
			$url = add_query_arg( [
				'post_type'        => 'job_listing',
				'filter_by_type' => $listing->type->get_slug(),
			], admin_url( 'edit.php' ) );

			printf( '<a href="%s">%s</a>', esc_url( $url ), esc_html( $listing->type->get_singular_name() ) );
		}
	}

	public function job_manager_default_company_logo( $logo ) {
		global $post;

		if ( ! ( $listing = \MyListing\Src\Listing::get( $post ) ) ) {
			return false;
		}

		if ( ! ( $logo = $listing->get_logo( 'thumbnail' ) ) ) {
        	$logo = c27()->image( 'marker.jpg' );
		}

		return $logo;
	}

	/**
	 * Save terms order done through drag & drop, for listing
	 * categories, tags, and regions.
	 *
	 * @since 1.0.0
	 */
	public function save_terms_order( $listing_id, $values ) {
		foreach ( array_merge( ['job_category', 'job_tags', 'region'], mylisting_custom_taxonomies( 'slug', 'slug' ) ) as $taxonomy ) {
			if ( empty( $values['job'][ $taxonomy ] ) ) {
				continue;
			}

			$this->set_taxonomy_terms_order( $listing_id, $values['job'][ $taxonomy ] );
		}
	}

	public function set_taxonomy_terms_order( $listing_id, $terms, $taxonomy = '' ) {
		global $wpdb;

		$counter = 0;
		foreach ( (array) $terms as $term ) {
			$wpdb->query( sprintf(
				"UPDATE {$wpdb->term_relationships} SET term_order = '%d' WHERE object_id = '%d' AND term_taxonomy_id = '%d'",
				++$counter,
				(int) $listing_id,
				(int) $term
			) );
		}
	}

	public function validate_fields( $isValid, $fields, $values ) {
		if ( ! empty( $_REQUEST['job_id'] ) ) {
			$listing = \MyListing\Src\Listing::get( $_REQUEST['job_id'] );
			$type = $listing ? $listing->type : false;
		} elseif ( $type_slug = c27()->get_submission_listing_type() ) {
			$type = \MyListing\Ext\Listing_Types\Listing_Type::get_by_name( $type_slug );
		}

		$values = $values['job'];

		foreach ( $fields['job'] as $key => $field ) {
			if ($field['slug'] == 'job_tagline' && isset($values['job_tagline']) && strlen($values['job_tagline']) > 90) {
				return new \WP_Error( 'validation-error', sprintf( __( '%s can\'t be longer than 90 characters.', 'my-listing' ), $field['label'] ) );
			}

			if ( $field['type'] == 'number' && ! empty( $values[ $field['slug'] ] ) ) {
				if ( ! is_numeric( $values[ $field['slug'] ] ) ) {
					return new \WP_Error( 'validation-error', sprintf( __( '%s must be a number.', 'my-listing' ), $field['label'] ) );
				}

				$val  = (float) $values[ $field[ 'slug' ] ];
				$min  = is_numeric( $field['min'] )  ? (float) $field['min']  : false;
				$max  = is_numeric( $field['max'] )  ? (float) $field['max']  : false;
				$step = is_numeric( $field['step'] ) ? (float) $field['step'] : false;

				if ( $min !== false && $val < $min ) {
					return new \WP_Error( 'validation-error', sprintf( __( '%s can\'t be smaller than %s.', 'my-listing' ), $field['label'], $min ) );
				}

				if ( $max !== false && $val > $max ) {
					return new \WP_Error( 'validation-error', sprintf( __( '%s can\'t be bigger than %s.', 'my-listing' ), $field['label'], $max ) );
				}
			}

			if ($field['type'] == 'email' && isset($values[$field['slug']]) && $values[$field['slug']]) {
				if (!filter_var($values[$field['slug']], FILTER_VALIDATE_EMAIL)) {
					return new \WP_Error( 'validation-error', sprintf( __( '%s must be a valid email address.', 'my-listing' ), $field['label'] ) );
				}
			}

			if ($field['type'] == 'url' && isset($values[$field['slug']]) && $values[$field['slug']]) {
				if ( preg_match( '@^(https?|ftp)://[^\s/$.?#].[^\s]*$@iS', $values[ $field['slug'] ] ) !== 1 ) {
					return new \WP_Error( 'validation-error', sprintf( __( '%s must be a valid url address.', 'my-listing' ), $field['label'] ) );
				}
			}

			if ( in_array( $field['slug'], array_merge( ['job_category', 'job_tags', 'region'], mylisting_custom_taxonomies( 'slug', 'slug' ) ) ) && ! empty( $values[ $field['slug'] ] ) && $type ) {
				foreach ( (array) $values[ $field['slug'] ] as $term_id ) {
					$term_meta = get_term_meta( $term_id, 'listing_type', true );

					if ( is_array( $term_meta ) && ! empty( $term_meta ) && ! in_array( $type->get_id(), $term_meta ) ) {
						return new \WP_Error( 'validation-error', sprintf( __( 'Invalid category.', 'my-listing' ), $field['label'] ) );
					}
				}
			}
		}

		// dd($fields, $values);

		return $isValid;
	}

	public function save_listing( $id, $values ) {
		if ( ! empty( $_POST['job_location'] ) && ! empty( $_POST['job_location__latitude'] ) && ! empty( $_POST['job_location__longitude'] ) ) {
			$lockpin   =  ! empty( $_POST['job_location__lock_pin'] ) && $_POST['job_location__lock_pin'] == 'yes';
			$latitude  = (float) $_POST['job_location__latitude'];
			$longitude = (float) $_POST['job_location__longitude'];

			if ( $latitude && $longitude && ( $latitude <= 90 ) && ( $latitude >= -90 ) && ( $longitude <= 180 ) && ( $longitude >= -180 ) ) {
				update_post_meta( $id, 'geolocation_lat', $latitude );
				update_post_meta( $id, 'geolocation_long', $longitude );
			}

			update_post_meta ( $id, 'job_location__lock_pin', $lockpin ? 'yes' : false );
		}
	}

	public function list_product_cats( $name, $object )
	{
		return $this->get_parent_category_name( $object, $name );
	}

	public function get_parent_category_name( $object, $name ) {
		if ( $object->parent && ( $parent = get_term( $object->parent, 'job_listing_category' ) ) ) {
			return $this->get_parent_category_name( $parent, "{$parent->name} &#9656; {$name}" );
		}

		return $name;
	}

	public function filter_listing_address( $address ) {
		if ( ! apply_filters( 'case27\listing\location\short_address', true ) ) {
			return $address;
		}

		$parts = explode(',', $address);
		return trim( $parts[0] );
	}

	public function wpjm_settings( $settings ) {
		$remove_settings = [
			'general' => [
				'job_manager_google_maps_api_key',
				'job_manager_date_format',
			],
			'job_listings' => [
				'job_manager_per_page',
				'job_manager_hide_filled_positions',
				'job_manager_hide_expired',
				'job_manager_hide_expired_content',
				'job_manager_enable_categories',
				'job_manager_enable_default_category_multiselect',
				'job_manager_enable_types',
				'job_manager_multi_job_type',
				'job_manager_date_format',
			],
			'job_submission' => [
				'job_manager_enable_registration',
				'job_manager_generate_username_from_email',
				'job_manager_use_standard_password_setup_email',
				'job_manager_registration_role',
				'job_manager_allowed_application_method',
			],
		];

		foreach ( $remove_settings as $remove_setting_group_key => $remove_setting_group ) {
			if ( ! empty( $settings[ $remove_setting_group_key ] ) ) {
				foreach ( $settings[ $remove_setting_group_key ] as $setting_group_key => $setting_group ) {
					if ( ! is_array( $setting_group ) ) {
						continue;
					}

					foreach ( $setting_group as $setting_key => $setting ) {
						if ( ! is_array( $setting ) || empty( $setting['name'] ) ) {
							continue;
						}

						if ( in_array( $setting['name'], $remove_setting_group ) ) {
							unset( $settings[ $remove_setting_group_key ][ $setting_group_key ][ $setting_key ] );
						}
					}
				}
			}
		}

		return $settings;
	}

	public function setup_permalink_settings( $screen ) {
		if ( ! $screen || ! $screen->id == 'options-permalink' ) {
			return false;
		}

    	$bases = c27()->get_permalink_structure();

    	// Save settings.
    	if ( isset( $_POST['permalink_structure'] ) ) {
    		if ( ! empty( $_POST['ml_region_slug'] ) ) {
				$bases['region_base'] = sanitize_text_field( $_POST['ml_region_slug'] );
    		} elseif ( isset( $_POST['ml_region_slug'] ) ) {
				$bases['region_base'] = '';
    		}

    		if ( ! empty( $_POST['ml_tag_slug'] ) ) {
				$bases['tag_base'] = sanitize_text_field( $_POST['ml_tag_slug'] );
    		} elseif ( isset( $_POST['ml_tag_slug'] ) ) {
				$bases['tag_base'] = '';
    		}

    		// WPJM uses sanitize_title_with_dashes(), which doesn't allow non-latin characters.
    		$bases['job_base']       = sanitize_text_field( $_POST['wpjm_job_base_slug'] );
			$bases['category_base']  = sanitize_text_field( $_POST['wpjm_job_category_slug'] );
			$bases['type_base']      = sanitize_text_field( $_POST['wpjm_job_type_slug'] );

			update_option( 'wpjm_permalinks', $bases );
		}

		add_settings_field(
			'ml_region_slug',
			__( 'Region base', 'my-listing' ),
			function() use( $bases ) { ?>
				<input name="ml_region_slug" type="text" class="regular-text code" value="<?php echo esc_attr( $bases['region_base'] ); ?>"
					   placeholder="<?php echo esc_attr_x( 'region', 'Region slug - resave permalinks after changing this', 'my-listing' ) ?>">
			<?php },
			'permalink',
			'optional'
		);

		add_settings_field(
			'ml_tag_slug',
			__( 'Tag base', 'my-listing' ),
			function() use( $bases ) { ?>
				<input name="ml_tag_slug" type="text" class="regular-text code" value="<?php echo esc_attr( $bases['tag_base'] ); ?>"
					   placeholder="<?php echo esc_attr_x( 'tag', 'Tag slug - resave permalinks after changing this', 'my-listing' ) ?>">
			<?php },
			'permalink',
			'optional'
		);
	}

	public function setup_permalinks( $args, $taxonomy ) {
    	$bases = c27()->get_permalink_structure();

		if ( $taxonomy === 'region' ) {
			if ( ! isset( $args['rewrite'] ) ) {
				$args['rewrite'] = [];
			}

			$args['rewrite']['slug'] = $bases['region_base'];
		}

		if ( $taxonomy === 'case27_job_listing_tags' ) {
			if ( ! isset( $args['rewrite'] ) ) {
				$args['rewrite'] = [];
			}

			$args['rewrite']['slug'] = $bases['tag_base'];
		}

		return $args;
	}

	/**
	 * Handle "Skip preview" button functionality in Add Listing page.
	 *
	 * @since 2.0
	 */
	public function maybe_skip_preview( $steps ) {
		if ( ! empty( $_POST['submit_job'] ) && $_POST['submit_job'] === 'submit--no-preview' && isset( $steps['preview'] ) ) {
			unset( $steps['preview'] );
		}

		return $steps;
	}

	/**
	 * Handle "Skip preview" when paid listings are disabled.
	 *
	 * @since 2.0
	 */
	public function skip_preview_handler( $listing_id ) {
		$listing = \MyListing\Src\Listing::get( $listing_id );
		if ( ! $listing || ! in_array( $listing->get_status(), [ 'preview', 'expired' ] ) ) {
			return;
		}

		delete_post_meta( $listing->get_id(), '_job_expires' );
		wp_update_post( [
			'ID' => $listing->get_id(),
			'post_status' => apply_filters( 'submit_job_post_status', get_option( 'job_manager_submission_requires_approval' ) ? 'pending' : 'publish', $listing->get_data() ),
			'post_date' => current_time( 'mysql' ),
			'post_date_gmt' => current_time( 'mysql', 1 ),
		] );
	}

	/**
	 * Prevent external images from being added to media library
	 * and attached to the listing, which causes "HTTP wrapper does not support
	 * writeable connections" error in frontend Add/Edit listing forms.
	 *
	 * @since 2.0
	 */
	public function handle_external_images( $maybe_empty, $postarr ) {
		if ( $postarr['post_type'] !== 'attachment' || empty( $postarr['file'] ) ) {
			return $maybe_empty;
		}

		if ( empty( $postarr['post_parent'] ) || get_post_type( $postarr['post_parent'] ) !== 'job_listing' ) {
			return $maybe_empty;
		}

		if ( ! is_writable( $postarr['file'] ) ) {
			mlog( sprintf( 'External image used, skipping attachment. <a href="%s" target="_blank">[link]</a>', $postarr['file'] ) );
			$maybe_empty = true;
		}

		return $maybe_empty;
	}

	public function admin_listing_taxonomies() {
		remove_meta_box( 'job_listing_categorydiv', 'job_listing', 'normal' );
		remove_meta_box( 'regiondiv', 'job_listing', 'normal' );
		remove_meta_box( 'tagsdiv-case27_job_listing_tags', 'job_listing', 'normal' );
		foreach ( mylisting_custom_taxonomies() as $slug => $label ) {
			remove_meta_box( $slug.'div', 'job_listing', 'normal' );
		}
	}


	/**
	 * Filter listings by listing type in admin via URL.
	 *
	 * @since 2.0
	 */
	public function filter_listings_by_type( $query ) {
		global $typenow;

		if ( $typenow !== 'job_listing' || empty( $_GET['filter_by_type'] ) || ! is_admin() ) {
			return $query;
		}

		if ( ! ( $type = \MyListing\Ext\Listing_Types\Listing_Type::get_by_name( $_GET['filter_by_type'] ) ) ) {
			return $query;
		}

		$query->query_vars['meta_key']   = '_case27_listing_type';
		$query->query_vars['meta_value'] = $type->get_slug();

		// Display admin notice to inform user that they are viewing filtered listings.
		add_action( 'admin_notices', function() use ($type) {
			// Display this notice only once.
			global $_case27_filter_listings_by_type;
			if ( isset( $_case27_filter_listings_by_type ) ) {
				return;
			}
			$_case27_filter_listings_by_type = 1;

			$back_url = add_query_arg( [
				'post_type'        => 'job_listing',
			], admin_url( 'edit.php' ) );
			?>
			<div class="notice notice-info">
				<p>
					<?php printf( _x( 'Showing all %s.', 'WP Admin > Listings > Filter by type', 'my-listing' ), $type->get_plural_name() ) ?>
					<?php printf( '<a href="%s">%s</a>', esc_url( $back_url ), _x( 'Go back.', 'WP Admin > Listings > Filter by type', 'my-listing' ) ) ?>
				</p>
			</div>
			<?php
		} );

		return $query;
	}

	public function admin_menu() {
		add_submenu_page(
			'edit.php?post_type=job_listing',
			_x( 'Taxonomies', 'Taxonomies link title', 'my-listing' ),
			_x( 'Taxonomies', 'Taxonomies link title', 'my-listing' ),
			'manage_options',
			admin_url( 'edit.php?post_type=job_listing&page=job-manager-settings#settings-taxonomies' )
		);
	}
}

ListingManager::instance();