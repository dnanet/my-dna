<?php

if ( ! defined('ABSPATH') ) {
	exit;
}

class CASE27_Listings {
	use \MyListing\Src\Traits\Instantiatable;

	public function __construct() {
		add_filter( 'submit_job_form_fields', [ $this, 'frontend_form_fields' ], 35 );
		add_action( 'save_post', [ $this, 'admin_save_fields' ], 10, 2 );
		add_action( 'job_manager_job_listing_data_start', [ $this, 'listing_meta_data_start' ] );
		add_action( 'job_manager_job_listing_data_end', [ $this, 'listing_meta_data_end' ] );
		add_filter( 'job_manager_job_listing_data_fields', [ $this, 'backend_form_fields' ] );
		add_filter( 'job_manager_job_listing_data_fields', [ $this, 'admin_add_expire_field' ], 20 );
		add_action( 'job_manager_save_job_listing', [ $this, 'save_listing_data' ], 30, 2 );
		add_filter( 'submit_job_form_fields_get_job_data', [ $this, 'populate_listing_fields' ], 30, 2 );
		add_action( 'job_manager_update_job_data', [ $this, 'frontend_update_listing_data' ], 50, 2 );

		// Delete attachment on delete post. 'delete_post' hook is too late.
		add_action( 'before_delete_post', function( $post_id ) {
			if ( 'job_listing' !== get_post_type( $post_id ) ) {
				return;
			}

			// Get all attachments IDs. Maybe need settings to enable this.
			$att_ids = get_posts( array(
				'numberposts' => -1,
				'post_type'   => 'attachment',
				'fields'      => 'ids',
				'post_status' => 'any',
				'post_parent' => $post_id,
			) );

			// Delete each attachments.
			if ( $att_ids && is_array( $att_ids ) ) {
				foreach( $att_ids as $id ) {
					wp_delete_attachment( $id, true );
				}
			}
		} );

		add_action( 'init', function() {
			// Unset preview listing id stored in cookies.
			// @todo: Keep the cookie functionality, but only if the newly added listing
			// belongs to the selected listing type.
			if ( isset( $_GET['new'] ) && ! empty( $_GET['listing_type'] ) ) {
			    unset( $_COOKIE['wp-job-manager-submitting-job-id'] );
			    setcookie( 'wp-job-manager-submitting-job-id', '', time() - 3600, COOKIEPATH, COOKIE_DOMAIN, false );
			}
		});
	}

	/**
     * Fields on Submit Listing Form.
     *
     * @since 1.0
	 */
	public function frontend_form_fields( $fields ) {
		// Default fields.
		$default_fields = [ 'job' => [
			'job_title' => $fields['job']['job_title'],
			'job_description' => $fields['job']['job_description'],
		], 'company' => [] ];
		$default_fields['job']['job_title']['slug'] = 'job_title';
		$default_fields['job']['job_description']['slug'] = 'job_description';

		$listing = null;
		$type = null;

		// Submit listing form: Listing type is passed as a POST parameter.
		if ( $type_slug = c27()->get_submission_listing_type() ) {
			$type = \MyListing\Ext\Listing_Types\Listing_Type::get_by_name( $type_slug );
		}

		// Edit listing form: Listing ID is available as a GET parameter.
		if ( ! empty( $_REQUEST['job_id'] ) ) {
			$listing = \MyListing\Src\Listing::get( $_REQUEST['job_id'] );
			if ( ! ( $listing && $listing->type ) ) {
				return $default_fields;
			}

			$type = $listing->type;
		}

		// If a listing type wasn't retrieved, return empty fields.
		if ( ! $type ) {
			return $default_fields;
		}

		// Filter out fields set to be hidden from the frontend submission form.
		$new_fields = array_filter( $type->get_fields(), function( $field ) {
			return isset( $field['show_in_submit_form'] ) && $field['show_in_submit_form'] == true;
		} );

		$new_fields = apply_filters( 'mylisting/submission/fields', $new_fields, $listing );

		foreach ( $fields['job'] as $key => $field ) {
			if ( ! isset( $new_fields[ $key ] ) ) {
				continue;
			}

			$new_fields[ $key ] = array_merge( $field, $new_fields[ $key ] );
		}

		return [ 'job' => $new_fields, 'company' => [] ];
	}

	/**
     * Fields on Admin Edit Listing Form.
     *
     * @since 1.0
	 */
	public function backend_form_fields( $fields ) {
		global $post;

		$listing = \MyListing\Src\Listing::get( $post );
		if ( ! ( $listing && $listing->type ) ) {
			return [];
		}

		// Filter out fields set to be hidden from the backend submission form.
		$new_fields = array_filter( $listing->type->get_fields(), function( $field ) {
			return isset( $field['show_in_admin'] ) && $field['show_in_admin'] == true;
		} );

		$new_fields = apply_filters( 'mylisting/admin/submission/fields', $new_fields, $listing );

		foreach ( $new_fields as $key => $field ) {
			if ( substr( $key, 0, 1 ) !== '_' ) {
				$new_fields["_{$key}"] = $field;
				unset( $new_fields[ $key ] );
			}
		}

		foreach ( $fields as $key => $field ) {
			if ( ! isset( $new_fields[$key] ) ) {
				continue;
			}

			$new_fields[ $key ] = array_merge( $field, $new_fields[ $key ] );
		}

		if ( isset( $new_fields['_job_title'] ) ) {
			unset( $new_fields['_job_title'] );
		}

		$new_fields = [
			'_job_description' => isset( $new_fields['_job_description'] ) ? $new_fields['_job_description'] : null,
		] + $new_fields;

		$new_fields['_job_description']['priority'] = 0.2;

		return array_filter( $new_fields );
	}

	public function admin_add_expire_field( $fields ) {
		global $post;

		$listing = \MyListing\Src\Listing::get( $post );
		if ( ! ( $listing && $listing->type ) ) {
			return [];
		}

		$fields['_job_expires'] = [
			'slug' => '_job_expires',
			'label' => __( 'Listing Expiry Date', 'my-listing' ),
			'type' => 'text',
			'required' => false,
			'placeholder' => '',
			'priority' => 250,
			'description' => '',
		];

		return $fields;
	}

	public function admin_save_fields( $post_id, $post ) {
        // Check if user has permissions to save data.
        if ( ! current_user_can( 'edit_post', $post_id ) || wp_is_post_autosave( $post_id ) || wp_is_post_revision( $post_id ) ) {
            return;
        }

        // save listing type
        if ( isset( $_POST['_case27_listing_type'] ) ) {
        	update_post_meta( $post_id, '_case27_listing_type', $_POST['_case27_listing_type'] );
        }
	}

	public function listing_meta_data_start( $post_id ) {
		global $post;

		require locate_template( 'includes/integrations/wp-job-manager/templates/form-fields/admin/select-listing-type.php' );

		echo '</div></div></div><div class="ml-admin-listing-form">';
		wp_enqueue_style( 'mylisting-admin-add-listing' );

		$handled_fields = [];
		$fields = \WP_Job_Manager_Writepanels::instance()->job_listing_fields();
		foreach ( $fields as $key => $field ) {
			$type = ! empty( $field['type'] ) ? $field['type'] : 'text';

			// Make sure action is only executed once per field type.
			if ( in_array( $type, $handled_fields ) ) {
				continue;
			}

			$handled_fields[] = $type;
			add_action( 'job_manager_input_'.$type, function( $key, $field ) {
				return require locate_template( 'includes/integrations/wp-job-manager/templates/form-fields/admin/default.php' );
			}, 35, 2 );
		}
	}

	public function listing_meta_data_end() {
		echo '</div><div><div><div>';
	}

	public function save_listing_data( $post_id, $post ) {
		foreach ( WP_Job_Manager_Writepanels::instance()->job_listing_fields() as $key => $field ) {
			$type = ! empty( $field['type'] ) ? $field['type'] : '';

			if ( $type == 'wp-editor' ) {
				update_post_meta( $post_id, $key, wp_kses_post( $_POST[ $key ] ) );
			}

			if ( $type == 'texteditor' ) {
				$editor_type = ! empty( $field['editor-type'] ) ? $field['editor-type'] : 'wp-editor';

				if ( $editor_type == 'wp-editor' ) {
					update_post_meta( $post_id, $key, wp_kses_post( $_POST[ $key ] ) );
				}

				if ( $editor_type == 'textarea' ) {
					update_post_meta( $post_id, $key, wp_kses_post( stripslashes( $_POST[ $key ] ) ) );
				}
			}

			if ( $key == '_job_location' ) {
				if ( ! empty( $_POST['_job_location'] ) && ! empty( $_POST['_job_location__latitude'] ) && ! empty( $_POST['_job_location__longitude'] ) ) {
					$lockpin   =  ! empty( $_POST['_job_location__lock_pin'] ) && $_POST['_job_location__lock_pin'] == 'yes';
					$latitude  = (float) $_POST['_job_location__latitude'];
					$longitude = (float) $_POST['_job_location__longitude'];

					if ( $latitude && $longitude && ( $latitude <= 90 ) && ( $latitude >= -90 ) && ( $longitude <= 180 ) && ( $longitude >= -180 ) ) {
						update_post_meta( $post_id, 'geolocation_lat', $latitude );
						update_post_meta( $post_id, 'geolocation_long', $longitude );
					}

					update_post_meta( $post_id, 'job_location__lock_pin', $lockpin ? 'yes' : false );
				}
			}

			if ( isset( $field['taxonomy'] ) ) {
				$terms = ! empty( $_POST[ $key ] )
					? array_map( 'absint', (array) $_POST[ $key ] )
					: null;

				wp_set_object_terms( $post_id, $terms, $field['taxonomy'], false );
				\CASE27\Integrations\WPJM\ListingManager::instance()->set_taxonomy_terms_order( $post_id, $terms );
			}

			if ( in_array( $type, [ 'work-hours', 'links' ] ) ) {
        		update_post_meta( $post_id, $key, $_POST[ $key ] );
			}
		}

		// Set job_title field
		update_post_meta( $post_id, '_job_title', $post->post_title );

		// Avoid infinite loop.
		remove_action( 'save_post', [ \WP_Job_Manager_Writepanels::instance(), 'save_post' ], 1, 2 );
		// Update post description to have the same value as 'job_description'
		wp_update_post( [
			'ID' => $post_id,
			'post_content' => get_post_meta( $post_id, '_job_description', true ),
		] );
		add_action( 'save_post', [ \WP_Job_Manager_Writepanels::instance(), 'save_post' ], 1, 2 );
	}

	public function populate_listing_fields( $fields, $listing ) {
		if ( ! empty( $fields['job']['job_description'] ) && $description = get_post_meta( $listing->ID, '_job_description', true ) ) {
			$fields['job']['job_description']['value'] = $description;
		}

		if ( ! empty( $fields['job']['job_tags'] ) && isset( $fields['job']['job_tags'] ) ) {
			$fields['job']['job_tags']['value'] = wp_get_object_terms( $listing->ID, 'case27_job_listing_tags', ['fields' => 'ids'] );
		}

		if ( ! empty( $fields['job']['region'] ) && isset( $fields['job']['region'] ) ) {
			$fields['job']['region']['value'] = wp_get_object_terms( $listing->ID, 'region', ['fields' => 'ids'] );
		}

		return $fields;
	}

	public function frontend_update_listing_data( $listing_id, $values ) {
		if ( isset( $_POST['job_description'] ) ) {
			update_post_meta( $listing_id, '_job_description', wp_kses_post( $_POST['job_description'] ) );
		}

		/**
		 * Attach the listing type to the listing. Ensures it's the submission form,
		 * before the listing with preview status has been created.
		 *
		 * @since 2.0
		 */
		if ( empty( $_REQUEST['job_id'] ) && ( $listing_type = c27()->get_submission_listing_type() ) ) {
			if ( $type = \MyListing\Ext\Listing_Types\Listing_Type::get_by_name( $listing_type ) ) {
				update_post_meta( $listing_id, '_case27_listing_type', $type->get_slug() );
			}
		}
	}
}

CASE27_Listings::instance();