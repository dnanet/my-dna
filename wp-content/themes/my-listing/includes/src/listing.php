<?php

namespace MyListing\Src;

use \MyListing\Src\Conditions;
use \MyListing\Ext\Listing_Types\Designer;
use \MyListing\Src\User as User;
use \MyListing\Src\Schema as Schema;

class Listing {

	public static $instances = [];

	private
		$data,
		$categories,
		$field_cache = [],
		$special_keys = [];

	public
		$schedule,
		$schema,
		$type = null,
		$author = null;

	public static $aliases = [
		'title'       => 'job_title',
		'tagline'     => 'job_tagline',
		'location'    => 'job_location',
		'category'    => 'job_category',
		'tags'        => 'job_tags',
		'description' => 'job_description',
		'email'       => 'job_email',
		'logo'        => 'job_logo',
		'cover'       => 'job_cover',
		'gallery'     => 'job_gallery',
		'website'     => 'job_website',
		'phone'       => 'job_phone',
		'video_url'   => 'job_video_url',
		'date'        => 'job_date',
	];

	/**
	 * Get a new listing instance (Multiton pattern).
	 * When called the first time, listing will be fetched from database.
	 * Otherwise, it will return the previous instance.
	 *
	 * @since 1.6.0
	 * @param $listing int or \WP_Post
	 */
	public static function get( $listing ) {
		if ( is_numeric( $listing ) ) {
			$listing = get_post( $listing );
		}

		if ( ! $listing instanceof \WP_Post ) {
			return false;
		}

		if ( $listing->post_type !== 'job_listing' ) {
			return false;
		}

		if ( ! array_key_exists( $listing->ID, self::$instances ) ) {
			self::$instances[ $listing->ID ] = new self( $listing );
		}

		return self::$instances[ $listing->ID ];
	}

	public function __construct( \WP_Post $post ) {
		self::$instances[ $post->ID ] = $this;
		$this->data = $post;
		$this->schedule = new \MyListing\Src\Work_Hours( (array) get_post_meta( $this->data->ID, '_work_hours', true ) );
		$this->author = new User( $this->data->post_author );

		$this->type();
		$this->setup_special_keys();
		$this->schema = new Schema( $this );
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

	public function get_status() {
		return $this->data->post_status;
	}

	public function get_logo( $size = 'thumbnail' ) {
		if ( $this->has_field( 'logo' ) ) {
			return job_manager_get_resized_image( $this->get_field( 'logo' ), $size );
		}

		if ( $this->type && ( $default_logo = $this->type->get_default_logo( $size ) ) ) {
			return apply_filters( 'mylisting\listing\get_logo\default', $default_logo, $this );
		}

		return apply_filters( 'mylisting\listing\get_logo\default', '', $this );
	}

	public function get_cover_image( $size = 'large' ) {
		if ( $this->has_field( 'cover' ) ) {
			return job_manager_get_resized_image( $this->get_field( 'cover' ), $size );
		}

		if ( $this->type && ( $default_cover = $this->type->get_default_cover( $size ) ) ) {
			return apply_filters( 'mylisting\listing\get_cover_image\default', $default_cover, $this );
		}

		return apply_filters( 'mylisting\listing\get_cover_image\default', '', $this );
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

	public function get_link() {
		return get_permalink( $this->data );
	}

	public function get_schedule() {
		return $this->schedule;
	}

	/**
	 * Get the listing type this listing belongs to.
	 *
	 * @since  1.0
	 * @return Listing_Type|false
	 */
	public function type() {
		if ( $this->type ) {
			return $this->type;
		}

		// Get listing type based on listing type id (slug).
		$type_id = get_post_meta( $this->get_id(), '_case27_listing_type', true );
		if ( $type_id && ( $type = ( get_page_by_path( $type_id, OBJECT, 'case27_listing_type' ) ) ) ) {
			$this->type = \MyListing\Ext\Listing_Types\Listing_Type::get( $type );
			return $this->type;
		}

		// If not available, set to null.
		$this->type = null;
		return $this->type;
	}

	public function get_author() {
		return $this->author;
	}

	public function get_rating() {
		return \MyListing\Ext\Reviews\Reviews::get_listing_rating_optimized( $this->get_id() );
	}

	/**
	 * Retrieve listing priority.
	 *
	 * @since 1.7.0
	 */
	public function get_priority() {
		return absint( $this->get_data( '_featured' ) );
	}

	/**
	 * Get the amount of listing reviews (first level comments).
	 * If it's not stored in listing meta, then count them again and store the result.
	 *
	 * @since 1.6.3
	 */
	public function get_review_count() {
		if ( ( $count = $this->get_data( '_case27_review_count' ) ) !== null ) {
			return (int) $count;
		}

		return \MyListing\Ext\Reviews\Reviews::count_reviews( $this->get_id() );
	}

	/**
	 * Determine if the requested field has a value that should be displayed,
	 * including values that are considered falsy but should still be shown,
	 * such as 0 and '0'.
	 *
	 * @since 1.7.2
	 */
	public function has_field( $key ) {
		return ( $field = $this->get_field( $key, true ) ) && $field->valid();
	}

	public function get_field( $key, $object = false ) {
		if ( empty( $this->field_cache[ $key ] ) ) {
			$this->field_cache[ $key ] = $this->get_field_object( $key );
		}

		return $object === true ? $this->field_cache[$key] : $this->field_cache[$key]->value;
	}

	public function get_field_object( $key ) {
		$response = new \MyListing\Src\Field;

		if ( ! $this->type ) {
			return $response;
		}

		if ( array_key_exists( $key, $this->special_keys ) ) {
			$response->value = $this->special_keys[ $key ];
			return $response;
		}

		if ( array_key_exists( $key, self::$aliases ) ) {
			return $this->get_field( self::$aliases[ $key ], true );
		}

		if ( ! ( $field = $this->type->get_field( $key ) ) ) {
			return $response;
		}

		$response->options = $field;
		$conditions = new Conditions( $field, $this );
		if ( ! $conditions->passes() ) {
			return $response;
		}

		if ( in_array( $field['type'], [ 'term-checklist', 'term-select', 'term-multiselect' ] ) ) {
    		$value = array_filter( (array) wp_get_object_terms(
    			$this->get_id(), $field['taxonomy'],
    			[ 'orderby' => 'term_order', 'order' => 'ASC' ])
    		);

    		if ( is_wp_error( $value ) ) {
    			$value = [];
    		}
		} elseif ( isset( $this->data->{$field['slug']} ) ) {
			$value = $this->data->{$field['slug']};
		} elseif ( isset( $this->data->{'_' . $field['slug']} ) ) {
			$value = $this->data->{'_' . $field['slug']};
		} else {
			$value = '';
		}

		if ( is_serialized( $value ) ) {
			$value = unserialize( $value );
		}

		$response->value = $value;
		return $response;
	}

	public function get_social_networks() {
		if ( ! $this->has_field( 'links' ) ) {
			return [];
		}

		$networks = [];
		$allowed_networks = (array) mylisting()->schemes()->get('social-networks');

		foreach ( (array) $this->get_field( 'links' ) as $link ) {
            if ( ! is_array( $link ) || empty( $link['network'] ) ) {
            	continue;
        	}

        	if ( empty( $link['url'] ) || ! isset( $allowed_networks[ $link['network'] ] ) ) {
        		continue;
        	}

        	$network = $allowed_networks[ $link['network'] ];
        	$network['link'] = $link['url'];

        	$networks[] = $network;
		}

		return array_filter( $networks );
	}

	/**
	 * Get the text to be used when listing is shared on social networks.
	 *
	 * @since  1.6.3
	 * @return string $description
	 */
	public function get_share_description() {
		$description = wp_kses( $this->get_field( 'description' ), [] );

		if ( $this->has_field( 'tagline' ) ) {
			$description = $this->get_field( 'tagline' );
		}

		return apply_filters( 'mylisting\listing\share\description', $description, $this );
	}

	/**
	 * Get the image to be used when listing is shared on social networks.
	 *
	 * @since  1.6.3
	 * @return string $image
	 */
	public function get_share_image() {
		$field = apply_filters( 'mylisting\single\og:image', 'logo' );
		$image = '';

		if ( $field == 'logo' ) {
			$image = $this->get_logo( 'large' );
		} elseif ( $field == 'cover' ) {
			$image = $this->get_cover_image( 'large' );
		} elseif ( $this->has_field( $field ) ) {
			$image = job_manager_get_resized_image( $this->get_field( $field ), 'large' );
		}

		if ( $image && filter_var( $image, FILTER_VALIDATE_URL ) !== false ) {
			$image = esc_url( $image );
		}

		return apply_filters( 'mylisting\listing\share\image', $image, $this );
	}

	/**
	 * Replace field tags with the actual field value.
	 * Example items to be replaced: [[tagline]] [[description]] [[twitter-id]]
	 *
	 * @since  1.5.0
	 * @param  string $string String to replace values into.
	 * @return string
	 */
	public function compile_string( $string ) {
		preg_match_all('/\[\[+(?P<fields>.*?)\]\]/', $string, $matches);

		if ( empty( $matches['fields'] ) ) {
			return $string;
		}

		// Get all field values.
		foreach ( array_unique( $matches['fields'] ) as $slug ) {
			// If any of the used fields are empty, return false.
			if ( ! $this->has_field( $slug ) ) {
				return false;
			}

			$field = $this->get_field( $slug );
			$value = apply_filters( 'mylisting\listing\compile_string\field', $field, $slug, $this );
			if ( is_array( $value ) ) {
				$value = join( ', ', $value );
			}

			// Escape square brackets so any shortcode added by the listing owner won't be run.
			$value = str_replace( [ "[" , "]" ] , [ "&#91;" , "&#93;" ] , $value );
			$string = str_replace( "[[$slug]]", esc_attr( $value ), $string );
		}

		// Preserve line breaks.
		return $string;
	}

	/**
	 * Replace [[field]] with the field value in a string.
	 *
	 * @since  1.5.1
	 * @param  string $string to replace [[field]] from.
	 * @param  string $value  that will replace [[field]].
	 * @return string
	 */
	public function compile_field_string( $string, $value ) {
		$string = str_replace( '[[field]]', c27()->esc_shortcodes( esc_attr( $value ) ), $string );

		return do_shortcode( $string );
	}

	public function get_preview_options() {
		// Get the preview template options for the listing type of the current listing.
		$options = $this->type ? $this->type->get_preview_options() : [];

   		// Merge with the default options, in case the listing type options meta returns null.
		return c27()->merge_options( mylisting()->schemes()->get('result'), $options );
	}

	public function setup_special_keys() {
		$this->special_keys = [
			':id'              => $this->get_id(),
			':url'             => $this->get_link(),
			':reviews-average' => $this->get_rating(),
			':reviews-mode'    => $this->type ? $this->type->get_review_mode() : 10,
			':reviews-count'   => $this->get_review_count(),
			':lat'             => $this->get_data('geolocation_lat'),
			':lng'             => $this->get_data('geolocation_long'),
			':date'            => date_i18n( get_option( 'date_format' ), strtotime( $this->get_data('post_date') ) ),
			':rawdate'         => $this->get_data('post_date'),
			':authid'          => $this->get_data('post_author'),
			':authname'        => get_the_author_meta( 'display_name', $this->get_data('post_author') ),
			':authlogin'       => get_the_author_meta( 'user_login', $this->get_data('post_author') ),
			':currentuserid'   => get_current_user_id(),
			':currentusername' => get_the_author_meta( 'display_name', get_current_user_id() ),
			':currentuserlogin' => get_the_author_meta( 'user_login', get_current_user_id() ),
		];
	}

	/**
	 * Get preview card info fields, and validate them.
	 *
	 * @since  1.6.3
	 * @return array $fields
	 */
	public function get_info_fields() {
		$fields = [];
		$preview = $this->get_preview_options();

		if ( empty( $preview['info_fields'] ) ) {
			return $fields;
		}

		foreach ( (array) $preview['info_fields'] as $field ) {
            if ( empty( $field['icon'] ) ) {
                $field['icon'] = '';
            }

            if ( ! $this->has_field( $field['show_field'] ) ) {
            	continue;
            }

            $field_value = apply_filters( 'case27\listing\preview\info_field\\' . $field['show_field'], $this->get_field( $field['show_field'] ), $field, $this );
			if ( is_array( $field_value ) ) {
                $field_value = join( ', ', $field_value );
            }

			$GLOBALS['c27_active_shortcode_content'] = $field_value;
            $field_content = str_replace( '[[field]]', $field_value, do_shortcode( $field['label'] ) );

            if ( ! strlen( $field_content ) ) {
            	continue;
            }

        	$fields[] = [
        		'icon'    => $field['icon'],
        		'field'   => $field,
        		'content' => $field_content,
        	];
		}

		return $fields;
	}

	public function editable_by_current_user() {
		return (
			current_user_can( 'edit_others_posts', $this->get_id() ) ||
			absint( $this->get_data( 'post_author' ) ) === absint( get_current_user_id() )
		);
	}

	public function is_verified() {
		return $this->get_data( '_claimed' );
	}
}