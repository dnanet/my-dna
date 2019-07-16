<?php

namespace MyListing\Includes;

class Assets {
	use \MyListing\Src\Traits\Instantiatable;

	protected $styles, $scripts;

	public function __construct() {
		// register scripts and styles
		add_action( 'wp_enqueue_scripts', [ $this, 'register_scripts' ] );
		add_action( 'admin_enqueue_scripts', [ $this, 'register_scripts' ] );

		// enqueue assets
		add_action( 'wp_enqueue_scripts', [ $this, 'enqueue_scripts' ], 30 );
		add_action( 'wp_enqueue_scripts', [ $this, 'enqueue_dynamic_styles' ], 1000 );
		add_action( 'wp_head', [ $this, 'print_head_content' ] );
		add_action( 'admin_head', [ $this, 'print_head_content' ] );
	}

	public function register_scripts() {
		$suffix = is_rtl() ? '-rtl' : '';

		// frontend styles
		wp_register_style( 'mylisting-vendor', c27()->template_uri( 'assets/dist/frontend/vendor'.$suffix.'.css' ), [], CASE27_THEME_VERSION );
		wp_register_style( 'mylisting-frontend', c27()->template_uri( 'assets/dist/frontend/frontend'.$suffix.'.css' ), [], CASE27_THEME_VERSION );

		// frontend dashboard
        wp_register_style( 'mylisting-dashboard', c27()->template_uri( 'assets/dist/frontend/dashboard'.$suffix.'.css' ), [], CASE27_THEME_VERSION );
        wp_register_script( 'mylisting-dashboard', c27()->template_uri( 'assets/dist/frontend/dashboard.js' ), [], CASE27_THEME_VERSION, true );

        // admin styles
        wp_register_style( 'mylisting-admin-general', c27()->template_uri( 'assets/dist/admin/admin'.$suffix.'.css' ), [], CASE27_THEME_VERSION );

        // backend dashboard
        wp_register_style( 'mylisting-admin-dashboard', c27()->template_uri( 'assets/dist/admin/dashboard'.$suffix.'.css' ), [], CASE27_THEME_VERSION );

        // backend shortcodes page
        wp_register_style( 'mylisting-admin-shortcodes', c27()->template_uri( 'assets/dist/admin/shortcodes'.$suffix.'.css' ), [], CASE27_THEME_VERSION );
        wp_register_script( 'mylisting-admin-shortcodes', c27()->template_uri( 'assets/dist/admin/shortcodes.js' ), [], CASE27_THEME_VERSION, true );

        // frontend add listing
		wp_register_style( 'mylisting-add-listing', c27()->template_uri( 'assets/dist/frontend/add-listing'.$suffix.'.css' ), [], CASE27_THEME_VERSION );
		wp_register_script( 'mylisting-listing-form', c27()->template_uri( 'assets/dist/frontend/listing-form.js' ), ['jquery'], CASE27_THEME_VERSION, true );

		// backend add listing
		wp_register_style( 'mylisting-admin-add-listing', c27()->template_uri( 'assets/dist/admin/add-listing'.$suffix.'.css' ), [], CASE27_THEME_VERSION );

		// frontend single listing
		wp_register_script( 'mylisting-single', c27()->template_uri( 'assets/dist/frontend/single-listing.js' ), ['jquery'], CASE27_THEME_VERSION, true );

		// admin type editor
        wp_register_script( 'mylisting-admin-type-editor', c27()->template_uri( 'assets/dist/admin/type-editor.js' ), ['jsoneditor', 'theme-script-vendor', 'theme-script-main'], CASE27_THEME_VERSION, true );

		// jsoneditor
        wp_register_script( 'jsoneditor', c27()->template_uri( 'assets/vendor/jsoneditor/jsoneditor.js' ), [], CASE27_THEME_VERSION, true );
        wp_register_style( 'jsoneditor', c27()->template_uri( 'assets/vendor/jsoneditor/jsoneditor.css' ), [], CASE27_THEME_VERSION );

        // custom taxonomies
        wp_register_script( 'mylisting-admin-custom-taxonomies', c27()->template_uri( 'assets/dist/admin/custom-taxonomies.js' ), ['wp-util'], CASE27_THEME_VERSION, true );

        // icons
		wp_register_style( 'mylisting-material-icons', 'https://fonts.googleapis.com/icon?family=Material+Icons' );
		wp_register_style( 'mylisting-icons', c27()->template_uri( 'assets/dist/icons/icons'.$suffix.'.css' ), [], CASE27_THEME_VERSION );

		// select2
        wp_register_script( 'select2', c27()->template_uri( 'assets/vendor/select2/select2.js' ), ['jquery'], CASE27_THEME_VERSION, true );
        wp_register_style( 'select2', c27()->template_uri( 'assets/vendor/select2/select2.css' ), [], CASE27_THEME_VERSION );

        // momentjs
        wp_register_script( 'moment', c27()->template_uri( 'assets/vendor/moment/moment.js' ), [], CASE27_THEME_VERSION, true );

        // editor styles
	    $this->add_editor_style( c27()->template_uri( sprintf( 'assets/dist/admin/editor.css?ver=%s', CASE27_THEME_VERSION ) ) );
	}

    /**
     * Load WP Editor custom styles.
     *
     * @since 1.0
     */
	public function add_editor_style( $stylesheet ) {
		// for backend editors
		if ( is_admin() ) {
			return add_editor_style( $stylesheet );
		}

	    global $editor_styles;
	    $stylesheet = (array) $stylesheet;
	    if ( is_rtl() ) {
	        $stylesheet[] = str_replace( '.css', '-rtl.css', $stylesheet[0] );
	    }

	    $editor_styles = array_merge( (array) $editor_styles, $stylesheet );
	}

	/**
     * Enqueue theme scripts.
     *
     * @since 1.0.0
	 */
	public function enqueue_scripts() {
		global $wp_query;

		// icons
		wp_enqueue_style( 'mylisting-icons' );
		wp_enqueue_style( 'mylisting-material-icons' );

		// sortable
		wp_enqueue_script( 'jquery-ui-sortable' );

		// moment
		wp_enqueue_script( 'moment' );
		$this->load_moment_locale();

		wp_enqueue_script( 'select2' );
		wp_enqueue_style( 'select2' );

		// Frontend scripts.
		wp_enqueue_script( 'mylisting-vendor', c27()->template_uri( 'assets/dist/frontend/vendor.js' ), ['jquery'], CASE27_THEME_VERSION, true );
		wp_enqueue_script( 'c27-main', c27()->template_uri( 'assets/dist/frontend/frontend.js' ), ['jquery'], CASE27_THEME_VERSION, true );

		// Comment reply script
		if ( is_singular() && comments_open() && get_option('thread_comments') ) {
			wp_enqueue_script( 'comment-reply' );
		}

		if ( is_singular( 'job_listing' ) ) {
			wp_enqueue_script( 'mylisting-single' );
		}

		// Custom JavaScript
		wp_add_inline_script( 'c27-main', c27()->get_setting('custom_js') );

		// Disable WooCommerce pretty photo plugin.
		if ( class_exists( 'WooCommerce' ) ) {
			wp_dequeue_style( 'woocommerce_prettyPhoto_css' );
			wp_dequeue_script( 'prettyPhoto' );
			wp_dequeue_script( 'prettyPhoto-init' );
		}

		// frontend styles
		wp_enqueue_style( 'mylisting-vendor' );
		wp_enqueue_style( 'mylisting-frontend' );

		// theme style.css
		wp_enqueue_style( 'theme-styles-default', c27()->template_uri( 'style.css' ) );

		// custom styles
		wp_add_inline_style( 'theme-styles-default', $this->custom_styles() );
	}

	/**
	 * Enqueue dynamic styles.
	 *
	 * @since 2.0
	 */
	public function enqueue_dynamic_styles() {
		wp_enqueue_style( 'thems-styles-dynamic', c27()->template_uri( 'assets/dynamic/frontend.css.php?' . http_build_query( [
			'brand' => c27()->get_setting('general_brand_color', '#f24286'),
			'loader' => c27()->hexToRgb( c27()->get_setting( 'general_loading_overlay_color' ,'#000000' ) ),
		] ) ) );
	}

    // Get custom styles.
	public function custom_styles() {
		ob_start();

		echo c27()->get_setting('custom_css');

		return str_replace( "\n", '', ob_get_clean() );
	}

	public function load_moment_locale() {
		$locales = [
			'af', 'ar-dz', 'ar-kw', 'ar-ly', 'ar-ma', 'ar-sa', 'ar-tn', 'ar', 'az', 'be', 'bg', 'bm', 'bn', 'bo', 'br', 'bs', 'ca', 'cs', 'cv', 'cy',
			'da', 'de-at', 'de-ch', 'de', 'dv', 'el', 'en-au', 'en-ca', 'en-gb', 'en-ie', 'en-il', 'en-nz', 'eo', 'es-do', 'es-us', 'es', 'et', 'eu',
			'fa', 'fi', 'fo', 'fr-ca', 'fr-ch', 'fr', 'fy', 'gd', 'gl', 'gom-latn', 'gu', 'he', 'hi', 'hr', 'hu', 'hy-am', 'id', 'is', 'it', 'ja', 'jv',
			'ka', 'kk', 'km', 'kn', 'ko', 'ky', 'lb', 'lo', 'lt', 'lv', 'me', 'mi', 'mk', 'ml', 'mr', 'ms-my', 'ms', 'mt', 'my', 'nb', 'ne', 'nl-be',
			'nl', 'nn', 'pa-in', 'pl', 'pt-br', 'pt', 'ro', 'ru', 'sd', 'se', 'si', 'sk', 'sl', 'sq', 'sr-cyrl', 'sr', 'ss', 'sv', 'sw', 'ta', 'te',
			'tet', 'tg', 'th', 'tl-ph', 'tlh', 'tr', 'tzl', 'tzm-latn', 'tzm', 'ug-cn', 'uk', 'ur', 'uz-latn', 'uz', 'vi', 'x-pseudo', 'yo', 'zh-cn', 'zh-hk', 'zh-tw'
		];

		$load_locale = false;
		$locale = str_replace( '_', '-', strtolower( get_locale() ) );

		if ( in_array( $locale, $locales ) ) {
			$load_locale = $locale;
		} elseif ( strpos( $locale, '-') !== false ) {
			$locale = explode( '-', $locale );
			if ( in_array( $locale[0], $locales ) ) {
				$load_locale = $locale[0];
			}
		}

		if ( $load_locale ) {
			wp_enqueue_script( 'moment-locale-' . $load_locale, sprintf( 'https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.21.0/locale/%s.js', $load_locale ), ['moment'], '1.0', true );
			wp_add_inline_script( 'moment-locale-' . $load_locale, sprintf( 'window.MyListing_Moment_Locale = \'%s\';', $load_locale ) );
		}
	}

	/**
	 * Print content within the site <head></head>.
	 *
	 * @since 1.7.2
	 */
	public function print_head_content() {
		// MyListing object.
		$data = apply_filters( 'mylisting/localize-data', [
			'Helpers' => new \stdClass,
		] );

		foreach ( (array) $data as $key => $value ) {
			if ( ! is_scalar( $value ) ) {
				continue;
			}
			$data[ $key ] = html_entity_decode( (string) $value, ENT_QUOTES, 'UTF-8' );
		}

		printf( '<script type="text/javascript">var MyListing = %s;</script>', wp_json_encode( (object) $data ) );

		// CASE27 object.
		$case27 = [
			'ajax_url' => admin_url( 'admin-ajax.php' ),
			'ajax_nonce' => wp_create_nonce('c27_ajax_nonce'),
			'smooth_scroll_enabled' => c27()->get_setting('general_enable_smooth_scrolling', false),
			'l10n' => [
				'selectOption' => _x( 'Select an option', 'Dropdown placeholder', 'my-listing' ),
				'errorLoading' => _x( 'The results could not be loaded.', 'Dropdown could not load results', 'my-listing' ),
				'loadingMore'  => _x( 'Loading more results…', 'Dropdown loading more results', 'my-listing' ),
				'noResults'    => _x( 'No results found', 'Dropdown no results found', 'my-listing' ),
				'searching'    => _x( 'Searching…', 'Dropdown searching', 'my-listing' ),
				'datepicker'   => mylisting()->strings()->get_datepicker_locale(),
				'irreversible_action' => _x( 'This is an irreversible action. Proceed anyway?', 'Alerts: irreversible action', 'my-listing' ),
				'copied_to_clipboard' => _x( 'Copied!', 'Alerts: Copied to clipboard', 'my-listing' ),
				'nearby_listings_location_required' => _x( 'Enter a location to find nearby listings.', 'Nearby listings dialog', 'my-listing' ),
				'nearby_listings_retrieving_location' => _x( 'Retrieving location...', 'Nearby listings dialog', 'my-listing' ),
				'nearby_listings_searching' => _x( 'Searching for nearby listings...', 'Nearby listings dialog', 'my-listing' ),
				'something_went_wrong' => __( 'Something went wrong.', 'my-listing' ),
				'all_in_category' => _x( 'All in "%s"', 'Category dropdown', 'my-listing' ),
			],
			'woocommerce' => [],
		];

		if ( is_admin() ) {
			$case27['map_skins'] = c27()->get_map_skins();
			$case27['icon_packs'] = \MyListing\Includes\Admin::instance()->get_icon_packs();
		}

		foreach ( (array) $case27 as $key => $value ) {
			if ( ! is_scalar( $value ) ) {
				continue;
			}
			$case27[ $key ] = html_entity_decode( (string) $value, ENT_QUOTES, 'UTF-8' );
		}

		printf( '<script type="text/javascript">var CASE27 = %s;</script>', wp_json_encode( (object) $case27 ) );
	}
}
