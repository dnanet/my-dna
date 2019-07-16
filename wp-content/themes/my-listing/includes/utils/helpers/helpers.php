<?php

namespace MyListing\Utils\Helpers;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Helpers {
	use \MyListing\Src\Traits\Instantiatable;

    // Get theme template path, with the given $path appended to it.
	public function template_path( $path ) {
		return get_template_directory() . "/$path";
	}

    // Get theme template uri, with the given $uri appended to it.
	public function template_uri( $uri = '' ) {
		return get_template_directory_uri() . "/$uri";
	}

    // URI to asset folder.
	public function asset( $asset ) {
		return $this->template_uri( "assets/$asset" );
	}

    // URI to images folder.
	public function image( $image ) {
		return $this->asset( "images/$image" );
	}

    // Retrieve the featured_image url for the given post, on the given size.
	public function featured_image( $postID, $size = 'large' ) {
		$image = wp_get_attachment_image_src( get_post_thumbnail_id( $postID ), $size );
		return $image ? array_shift( $image ) : false;
	}

    // Get post terms from the given taxonomy.
	public function get_terms( $postID, $taxonomy = 'category' ) {
		$raw_terms = (array) wp_get_post_terms( $postID, $taxonomy );

		$terms = [];
		if ( ! empty( $raw_terms['errors'] ) ) {
			return $terms;
		}

		foreach ( $raw_terms as $raw_term ) {
			$terms[] = [
				'name' => $raw_term->name,
				'link' => get_term_link( $raw_term )
			];
		}

		return $terms;
	}

    // Print the post excerpt, limiting it to a given number of characters.
	public function the_excerpt( $charlength, $after = "&hellip;" ) {
		$excerpt = get_the_excerpt();
		$charlength++;

		if ( mb_strlen( $excerpt ) > $charlength ) {
			$subex = mb_substr( $excerpt, 0, $charlength - 5 );
			$exwords = explode( ' ', $subex );
			$excut = - ( mb_strlen( $exwords[ count( $exwords ) - 1 ] ) ) - 1;
			if ( $excut < 0 ) {
				echo mb_substr( $subex, 0, $excut );
			} else {
				echo $subex;
			}
			echo $after;
		} else {
			echo $excerpt;
		}
	}

	public function the_text_excerpt( $text, $charlength, $after = "&hellip;", $echo = true ) {
		$charlength++;
		$output = '';

		if ( mb_strlen( $text ) > $charlength ) {
			$subex = mb_substr( $text, 0, $charlength - 5 );
			$exwords = explode( ' ', $subex );
			$excut = - ( mb_strlen( $exwords[ count( $exwords ) - 1 ] ) ) - 1;
			if ( $excut < 0 ) {
				$output .= mb_substr( $subex, 0, $excut );
			} else {
				$output .= $subex;
			}
			$output .= $after;
		} else {
			$output .= $text;
		}

		if ( $echo ) {
			echo $output;
			return;
		}

		return $output;
	}

	public function merge_options( $defaults, $options ) {
		return array_replace_recursive( $defaults, $options );
	}

	public function get_partial( $template, $data = [] ) {
		if (!locate_template("partials/{$template}.php")) return;

		require locate_template("partials/{$template}.php");
	}

	public function get_section( $template, $data = [] ) {
		if (!locate_template("sections/{$template}.php")) return;

		require locate_template("sections/{$template}.php");
	}

	public function get_terms_dropdown_array($args = [], $key = 'term_id', $value = 'name') {
		$options = [];
		$terms = get_terms($args);

		if (is_wp_error($terms)) {
			return [];
		}

		foreach ((array) $terms as $term) {
			$options[$term->{$key}] = $term->{$value};
		}

		return $options;
	}


	public function get_posts_dropdown_array($args = [], $key = 'ID', $value = 'post_title') {
		$options = [];
		$posts = get_posts($args);

		foreach ((array) $posts as $term) {
			$options[$term->{$key}] = $term->{$value};
		}

		return $options;
	}

	public function get_icon_markup($icon_string)
	{
		// For icon fonts that require the icon name to be the contents of the <i> tag,
		// provide a string that can be exploded into two parts by '://', and use the
		// first part as the tag's class name, and the second part as the contents
		// of the tag. Example: material-icons://view_headline
		if (strpos($icon_string, '://') !== false) {
			$icon_arr = explode('://', $icon_string);

			return "<i class=\"{$icon_arr[0]}\">{$icon_arr[1]}</i>";
		}

		return "<i class=\"{$icon_string}\"></i>";
	}


	public function get_setting( $setting, $default = '' ) {
		return function_exists('get_field') && get_field($setting, 'option') !== null ? get_field($setting, 'option') : $default;
	}


	public function get_site_logo() {
		if ($logo_obj = c27()->get_setting('general_site_logo')) {
			return $logo_obj['sizes']['large'];
		}

		return '';
	}


	public function upload_file($file, $allowed_mime_types = [])
	{
		include_once( ABSPATH . 'wp-admin/includes/file.php' );
		include_once( ABSPATH . 'wp-admin/includes/media.php' );
		include_once( ABSPATH . 'wp-admin/includes/image.php' );

		$uploaded_file = new \stdClass();

		if ( ! in_array( $file['type'], $allowed_mime_types ) ) {
			return new \WP_Error( 'upload', sprintf( __( 'Uploaded files need to be one of the following file types: %s', 'my-listing' ), implode( ', ', array_keys( $allowed_mime_types ) ) ) );
		}

		$upload = wp_handle_upload($file, ['test_form' => false]);

		if ( ! empty( $upload['error'] ) ) {
			return new \WP_Error( 'upload', $upload['error'] );
		}

		$wp_filetype = wp_check_filetype($upload['file']);
		$attach_id = wp_insert_attachment([
			'post_mime_type' => $wp_filetype['type'],
			'post_title' => sanitize_file_name($upload['file']),
			'post_content' => '',
			'post_status' => 'inherit'
			], $upload['file']);

		$attach_data = wp_generate_attachment_metadata($attach_id, $upload['file']);
		wp_update_attachment_metadata( $attach_id, $attach_data );

		return $attach_id;
	}

	public function get_gradients() {
		return [
	    		'gradient1' => ['from' => '#7dd2c7', 'to' => '#f04786'],
				'gradient2' => ['from' => '#71d68b', 'to' => '#00af9c'],
				'gradient3' => ['from' => '#FF5F6D', 'to' => '#FFC371'],
				'gradient4' => ['from' => '#EECDA3', 'to' => '#EF629F'],
				'gradient5' => ['from' => '#114357', 'to' => '#F29492'],
				'gradient6' => ['from' => '#52EDC7', 'to' => '#F29492'],
				'gradient7' => ['from' => '#C644FC', 'to' => '#5856D6'],
	    	];
	}

	public function get_map_skins() {
		return (array) apply_filters( 'mylisting/helpers/get_map_skins', [] );
	}

	public function new_admin_page( $type = 'menu', $args = [] ) {
		if ( ! in_array( $type, [ 'menu', 'submenu', 'theme' ] ) ) return;

		call_user_func_array('add_' . $type . '_page', $args);
	}

	public function hexToRgb( $hex, $alpha = 1 ) {
		$rgb = [];

		if ( strpos( $hex, 'rgb' ) !== false ) {
			$hex = str_replace( ['rgba', 'rgb', '(', ')', ' '], '', $hex );
			$hexArr = explode( ',', $hex );

			$rgb['r'] = isset( $hexArr[0] ) ? absint( $hexArr[0] ) : 0;
			$rgb['g'] = isset( $hexArr[1] ) ? absint( $hexArr[1] ) : 0;
			$rgb['b'] = isset( $hexArr[2] ) ? absint( $hexArr[2] ) : 0;
			$rgb['a'] = isset( $hexArr[3] ) ? (float) $hexArr[3] : 1;

			return $rgb;
		}

		$hex      = str_replace( '#', '', $hex );
		$length   = strlen( $hex );
		$rgb['r'] = hexdec( $length == 6 ? substr( $hex, 0, 2 ) : ( $length == 3 ? str_repeat( substr( $hex, 0, 1 ), 2 ) : 0 ) );
		$rgb['g'] = hexdec( $length == 6 ? substr( $hex, 2, 2 ) : ( $length == 3 ? str_repeat( substr( $hex, 1, 1 ), 2 ) : 0 ) );
		$rgb['b'] = hexdec( $length == 6 ? substr( $hex, 4, 2 ) : ( $length == 3 ? str_repeat( substr( $hex, 2, 1 ), 2 ) : 0 ) );
		$rgb['a'] = $alpha;

		return $rgb;
	}

	public function getVideoEmbedUrl( $url )
	{
		// Check if youtube
		$rx = '~^(?:https?://)?(?:www\.)?(?:youtube\.com|youtu\.be)/watch\?v=(?<id>[^&]+)~x';
		preg_match($rx, $url, $matches);
		if (isset($matches['id']) && trim($matches['id']) != "") {
			return ['url' => "https://www.youtube.com/embed/{$matches['id']}?origin=*", 'type' => 'external', 'service' => 'youtube', 'video_id' => $matches['id']];
		}

		// Check if vimeo
		$rx = "/(https?:\/\/)?(www\.)?(player\.)?vimeo\.com\/([a-z]*\/)*(?<id>[0-9]{6,11})[?]?.*/";
		preg_match($rx, $url, $matches);
		if (isset($matches['id']) && trim($matches['id']) != "") {
			return ['url' => "https://player.vimeo.com/video/{$matches['id']}?api=1&player_id=".$matches['id'], 'type' => 'external', 'service' => 'vimeo', 'video_id' => $matches['id']];
		}

		// Check if dailymotion
		$rx = "/^.+dailymotion.com\/(video|hub)\/(?<id>[^_]+)[^#]*(#video=(?<id2>[^_&]+))?/";
		preg_match($rx, $url, $matches);
		if (isset($matches['id']) && trim($matches['id']) != "") {
			return ['url' => "https://www.dailymotion.com/embed/video/{$matches['id']}", 'type' => 'external', 'service' => 'dailymotion', 'video_id'=>$matches['id']];
		}

		return false;
	}

	/**
	 * Get WPJM permalinks structure, with default values.
	 *
	 * @since 1.6.2
	 */
	public function get_permalink_structure() {
		return wp_parse_args( array_filter( (array) get_option( 'wpjm_permalinks', [] ) ), [
			'category_base' => 'category',
			'region_base' => 'region',
			'tag_base' => 'tag',
		] );
	}

	/**
	 * Safely output encoded data as html attribute.
	 *
	 * @since 1.6.2
	 */
	public function encode_attr( $string ) {
		return htmlspecialchars( json_encode( $string ), ENT_QUOTES, 'UTF-8' );
	}

	/**
	 * Escape WordPress shortcode brackets.
	 * Used mainly to sanitize user input.
	 *
	 * @since 1.5.1
	 * @param string $value String to escape.
	 * @return string
	 */
	public function esc_shortcodes( $value ) {
		return str_replace( [ "[" , "]" ] , [ "&#91;" , "&#93;" ] , $value );
	}

	public function get_timezone() {
		$timezone_string = get_option( 'timezone_string' );
		if ( ! empty( $timezone_string ) ) {
			return new \DateTimeZone( $timezone_string );
		}

		$offset  = get_option( 'gmt_offset' );
		$hours   = (int) $offset;
		$minutes = abs( ( $offset - (int) $offset ) * 60 );
		$offset  = sprintf( '%+03d:%02d', $hours, $minutes );

		return new \DateTimeZone( $offset );
	}

	public function reset_listing_submission_cookies() {
		if (
			isset( $_COOKIE[ 'wp-job-manager-submitting-job-id' ] ) &&
			isset( $_COOKIE[ 'wp-job-manager-submitting-job-key' ] ) &&
			get_post_meta( $_COOKIE[ 'wp-job-manager-submitting-job-id' ], '_submitting_key', true ) == $_COOKIE['wp-job-manager-submitting-job-key']
		) {
			delete_post_meta( $_COOKIE[ 'wp-job-manager-submitting-job-id' ], '_submitting_key' );
			unset( $_COOKIE[ 'wp-job-manager-submitting-job-id' ] );
			unset( $_COOKIE[ 'wp-job-manager-submitting-job-key' ] );
			// setcookie( 'wp-job-manager-submitting-job-id', '', time() - 3600, COOKIEPATH, COOKIE_DOMAIN, false );
			// setcookie( 'wp-job-manager-submitting-job-key', '', time() - 3600, COOKIEPATH, COOKIE_DOMAIN, false );
		}
	}

	/**
	 * Retrieve object class name.
	 *
	 * @since 1.7.2
	 * @param bool $namespaced Whether to include the namespace or only the basename.
	 */
	public function get_class_name( $object, $namespaced = false ) {
		if ( $namespaced ) {
			return get_class( $object );
		}

		$parts = explode( '\\', get_class( $object ) );
		return end( $parts );
	}

	/**
	 * Determine if the requested field has a value that should be displayed,
	 * including values that are considered falsy but should still be shown,
	 * such as 0 and '0'.
	 *
	 * @since 1.7.2
	 */
	public function is_valid_field_value( $value ) {
		if ( $value || $value === 0 || $value === '0' ) {
			return true;
		}

		return false;
	}

	/**
	 * Receive a date object with current UTC time.
	 *
	 * @since 2.0
	 */
	public function utc() {
		return new \DateTime( 'now', new \DateTimeZone( 'UTC' ) );
	}

	public function get_listing_packages( $args = [] ) {
		if ( function_exists( 'case27_paid_listing_get_products' ) ) {
			return case27_paid_listing_get_products( $args );
		}

		return [];
	}

	public function get_flag( $country_code ) {
		if ( ! wp_style_is( 'flag-sprites-css', 'enqueued' ) ) {
			wp_enqueue_style( 'flag-sprites-css', $this->asset( 'vendor/flag-sprites/flags.min.css' ), [], CASE27_THEME_VERSION );
		}

		if ( ! ( $country = mylisting()->strings()->get_country( strtoupper( $country_code ) ) ) ) {
			$country = '';
			$country_code = 'unknown';
		}

		return sprintf(
			'<div class="flag-wrapper">' .
				'<img src="%s" class="flag flag-%s" alt="%s">' .
			'</div>',
			esc_url( $this->asset( 'vendor/flag-sprites/blank.gif' ) ),
			esc_attr( strtolower( $country_code ) ),
			esc_attr( $country )
		);
	}

	/**
	 * Determine the current listing type in submit listing form.
	 *
	 * @since 2.0
	 * @todo move to wp-job-manager integration dir as a util.
	 */
	public function get_submission_listing_type() {
		if ( ! empty( $_GET['listing_type'] ) ) {
			return $_GET['listing_type'];
		} elseif ( ! empty( $_GET['type'] ) ) {
			return $_GET['type'];
		} elseif ( ! empty( $_REQUEST['listing_type'] ) ) {
			return $_REQUEST['listing_type'];
		}

		return '';
	}
}
