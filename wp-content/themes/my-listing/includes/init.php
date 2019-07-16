<?php

MyListing\Ext\Advanced_Custom_Fields\Advanced_Custom_Fields::instance();
MyListing\Src\Explore::init();
MyListing\Src\Quick_View\Quick_View::instance();
MyListing\Src\Queries\Explore_Listings::instance();
MyListing\Src\Queries\Related_Listings::instance();
MyListing\Src\Queries\Similar_Listings::instance();
MyListing\Src\Queries\User_Listings::instance();
MyListing\Src\Queries\Term_List::instance();
MyListing\Src\Queries\Posts_List::instance();
MyListing\Src\Queries\Products_List::instance();
MyListing\Src\Queries\Quick_Search::instance();
MyListing\Includes\Filters::instance();
MyListing\Includes\Shortcodes::instance();
MyListing\Includes\Assets::instance();
MyListing\Includes\Admin::instance();
MyListing\Ext\Admin_Tips\Admin_Tips::instance();
MyListing\Includes\Strings::instance();
MyListing\Includes\Shortcodes::instance();
MyListing\Ext\Social_Login\Social_Login::instance();
MyListing\Ext\Promotions\Promotions::instance();
MyListing\Ext\Listing_Types\Permalinks::instance();
MyListing\Ext\Custom_Taxonomies\Custom_Taxonomies::instance();
MyListing\Ext\Typography\Typography::instance();
MyListing\Ext\Report_Listings\Report_Listings::instance();
MyListing\Int\Elementor\Elementor::instance();
MyListing\Ext\Contact_Form_7\Contact_Form_7::instance();
MyListing\Ext\Demo_Import\Demo_Import::instance();

mylisting()->register( 'sharer', MyListing\Ext\Sharer\Sharer::instance() );
mylisting()->register( 'cookies', MyListing\Src\Cookies::instance() );

MyListing\Ext\Visits\Visits::instance();

mylisting()->register( 'maps', MyListing\Ext\Maps\Maps::instance() );
mylisting()->register( 'stats', MyListing\Ext\Stats\Stats::instance() );

MyListing\Ext\Reviews\Reviews::instance();

mylisting()->register( 'woocommerce', MyListing\Ext\WooCommerce\WooCommerce::instance() );

MyListing\Ext\Paid_Listings\Paid_Listings::instance();

do_action( 'mylisting/init' );

/*
 * Configure theme textdomain, supported features, nav menus, etc.
 */
add_action( 'after_setup_theme', function() {

	// Add default posts and comments RSS feed links to head.
	add_theme_support( 'automatic-feed-links' );

	// Add support for the WooCommerce plugin.
	add_theme_support( 'woocommerce' );

	// Let WordPress manage the document title.
	add_theme_support( 'title-tag' );

	// WP Job Manager templates support.
	add_theme_support( 'job-manager-templates' );

	// Set content width
	if ( ! isset( $content_width ) ) $content_width = 550;

	// Enable support for Post Thumbnails on posts and pages.
	add_theme_support( 'post-thumbnails' );

	// Remove WP Admin Bar inline styles.
	add_theme_support( 'admin-bar', [ 'callback' => '__return_false' ] );

	// This theme uses wp_nav_menu() in one location.
	register_nav_menus([
		'primary' 	  		  => esc_html__( 'Primary Menu', 'my-listing' ),
		'footer'	  		  => esc_html__( 'Footer Menu', 'my-listing' ),
		'mylisting-user-menu' => esc_html__( 'Woocommerce Menu', 'my-listing' )
	]);

	// Allow shortcodes in menu item labels.
	add_filter( 'wp_nav_menu_items', 'do_shortcode' );

	/*
	 * Switch default core markup for search form, comment form, and comments
	 * to output valid HTML5.
	 */
	add_theme_support( 'html5', [
		'search-form',
		'comment-form',
		'comment-list',
		'gallery',
		'caption',
	]);

	add_theme_support( 'custom-background', [
		'default-color' => '#fafafa',
	]);
});


/*
 * Register theme sidebars.
 */
add_action( 'widgets_init', function() {
	register_sidebar([
		'name'          => __( 'Footer', 'my-listing' ),
		'id'            => 'footer',
		'before_widget' => '<div class="col-md-4 col-sm-6 col-xs-12 c_widget woocommerce">',
		'after_widget'  => '</div>',
		'before_title'  => '<div class="c_widget_title"><h5>',
		'after_title'   => '</h5></div>',
		]);

	register_sidebar([
		'name'          => __( 'Sidebar', 'my-listing' ),
		'id'            => 'sidebar',
		'before_widget' => '<div class="element c_widget woocommerce">',
		'after_widget'  => '</div>',
		'before_title'  => '<div class="pf-head"><div class="title-style-1"><h5>',
		'after_title'   => '</h5></div></div>',
		]);

	register_sidebar([
		'name'          => __( 'Shop Page', 'my-listing' ),
		'id'            => 'shop-page',
		'before_widget' => '<div class="element c_widget woocommerce">',
		'after_widget'  => '</div>',
		'before_title'  => '<div class="pf-head"><div class="title-style-1"><h5>',
		'after_title'   => '</h5></div></div>',
		]);

	do_action( 'case27_widgets_init' );
});

// Actions to be used by my-listing-addons plugin to add post types and taxonomies.
add_action( 'init', function() {
	do_action( 'case27_register_post_types' );
	do_action( 'case27_register_taxonomies' );
} );

/**
 * Insert required code in site footer through get_footer hook, so it will
 * be added when using custom footer templates which completely override the theme footer.
 *
 * @since 1.6.6
 */
add_action( 'mylisting/get-footer', function() {
	// Modals markup.
    c27()->get_partial( 'login-modal' );
    c27()->get_partial( 'quick-view-modal' );
    c27()->get_partial( 'shopping-cart-modal' );
    c27()->get_partial( 'photoswipe-template' );
    c27()->get_partial( 'dialog-template' );

    // 'Back to Top' button.
    if ( c27()->get_setting( 'footer_show_back_to_top_button', false ) ): ?>
        <a href="#" class="back-to-top">
            <i class="mi keyboard_arrow_up"></i>
        </a>
    <?php endif;
}, 1 );

// Temporary fix for a bug in WC Vendors plugin, which breaks most AJAX functionality.
if ( apply_filters( 'mylisting\wc-vendors\apply-ajax-bugfix', true ) ) {

	/**
	 * Make sure the function does not exist before defining it
	 *
	 * @link https://gist.github.com/tripflex/c6518efc1753cf2392559866b4bd1a53
	 */
	if( ! function_exists( 'remove_class_filter' ) ) {
		/**
		 * Remove Class Filter Without Access to Class Object
		 */
		function remove_class_filter( $tag, $class_name = '', $method_name = '', $priority = 10 ) {
			global $wp_filter;
			// Check that filter actually exists first
			if ( ! isset( $wp_filter[ $tag ] ) ) {
				return FALSE;
			}
			if ( is_object( $wp_filter[ $tag ] ) && isset( $wp_filter[ $tag ]->callbacks ) ) {
				// Create $fob object from filter tag, to use below
				$fob       = $wp_filter[ $tag ];
				$callbacks = &$wp_filter[ $tag ]->callbacks;
			} else {
				$callbacks = &$wp_filter[ $tag ];
			}
			// Exit if there aren't any callbacks for specified priority
			if ( ! isset( $callbacks[ $priority ] ) || empty( $callbacks[ $priority ] ) ) {
				return FALSE;
			}
			// Loop through each filter for the specified priority, looking for our class & method
			foreach ( (array) $callbacks[ $priority ] as $filter_id => $filter ) {
				// Filter should always be an array - array( $this, 'method' ), if not goto next
				if ( ! isset( $filter['function'] ) || ! is_array( $filter['function'] ) ) {
					continue;
				}
				// If first value in array is not an object, it can't be a class
				if ( ! is_object( $filter['function'][0] ) ) {
					continue;
				}
				// Method doesn't match the one we're looking for, goto next
				if ( $filter['function'][1] !== $method_name ) {
					continue;
				}
				// Method matched, now let's check the Class
				if ( get_class( $filter['function'][0] ) === $class_name ) {
					// WordPress 4.7+ use core remove_filter() since we found the class object
					if ( isset( $fob ) ) {
						// Handles removing filter, reseting callback priority keys mid-iteration, etc.
						$fob->remove_filter( $tag, $filter['function'], $priority );
					} else {
						// Use legacy removal process (pre 4.7)
						unset( $callbacks[ $priority ][ $filter_id ] );
						// and if it was the only filter in that priority, unset that priority
						if ( empty( $callbacks[ $priority ] ) ) {
							unset( $callbacks[ $priority ] );
						}
						// and if the only filter for that tag, set the tag to an empty array
						if ( empty( $callbacks ) ) {
							$callbacks = array();
						}
						// Remove this filter from merged_filters, which specifies if filters have been sorted
						unset( $GLOBALS['merged_filters'][ $tag ] );
					}
					return TRUE;
				}
			}
			return FALSE;
		}
	}

	add_action( 'init', function() {
	    if ( class_exists( 'WCV_Admin_Users' ) ) {
	        remove_class_filter( 'product_type_selector', 'WCV_Admin_Users', 'filter_product_types', 99 );
	    }
	} );
}