<?php

namespace MyListing\Includes;

class Admin {
    use \MyListing\Src\Traits\Instantiatable;

	public function __construct() {
        // Enqueue Admin Scripts and Styles.
        add_action( 'admin_enqueue_scripts', [ $this, 'enqueue_scripts' ], 30 );

        // Output iconpicker markup in admin footer.
        add_action( 'admin_footer', [ $this, 'output_iconpicker_template' ] );

        // Add custom WP Admin menu pages.
        add_action( 'admin_menu', [ $this, 'admin_menu' ] );

        // Reorder WP Admin menu.
        add_action( 'admin_menu', [$this, 'reorder_admin_menu'], 999 );

        // Load editor styles.
        add_action( 'admin_init', [ $this, 'load_editor_styles' ], 30 );

        // Init Listing_Settings class.
        \MyListing\Src\Admin\Listing_Settings::instance();

        foreach ( array_merge( [ 'job_listing_category', 'case27_job_listing_tags', 'region' ], mylisting_custom_taxonomies( 'slug', 'slug' ) ) as $taxonomy ) {
            add_filter( sprintf( 'manage_edit-%s_columns', $taxonomy ), [ $this, 'add_taxonomy_columns' ] );
            add_filter( sprintf( 'manage_%s_custom_column', $taxonomy ), [ $this, 'taxonomy_columns' ], 50, 3 );
        }
	}

    /**
     * Enqueue theme assets in wp-admin.
     *
     * @since 1.0
     */
    public function enqueue_scripts() {
        wp_enqueue_script( 'jquery-ui-sortable' );

        // icons
        wp_enqueue_style( 'mylisting-icons' );
        wp_enqueue_style( 'mylisting-material-icons' );

        // select2
        wp_enqueue_script( 'select2' );
        wp_enqueue_style( 'select2' );

        // momentjs
        wp_enqueue_script( 'moment' );

        // assets
        wp_enqueue_style( 'mylisting-admin-general' );
        wp_enqueue_script( 'theme-script-vendor', c27()->template_uri( 'assets/dist/admin/vendor.js' ), ['jquery'], CASE27_THEME_VERSION, true );
        wp_enqueue_script( 'theme-script-main', c27()->template_uri( 'assets/dist/admin/admin.js' ), ['jquery'], CASE27_THEME_VERSION, true );
    }

    /**
     * Get list of classnames for icon packs used by the theme.
     *
     * @since 1.0
     */
    public function get_icon_packs() {
        if ( ! is_user_logged_in() ) {
            return;
        }

        $font_awesome_icons = require CASE27_INTEGRATIONS_DIR . '/27collective/icons/font-awesome.php';
        $material_icons = require CASE27_INTEGRATIONS_DIR . '/27collective/icons/material-icons.php';
        $theme_icons = require CASE27_INTEGRATIONS_DIR . '/27collective/icons/theme-icons.php';

        return [
            'font-awesome' => array_map( function( $icon ) {
                return "fa {$icon}";
            }, array_values( $font_awesome_icons ) ),

            'material-icons' => array_map( function( $icon ) {
                return "mi {$icon}";
            }, array_values( $material_icons ) ),

            'theme-icons' => array_values( $theme_icons ),
        ];
    }

    /**
     * Create custom menu pages in WP Admin.
     *
     * @since 1.0
     */
    public function admin_menu() {
        c27()->new_admin_page( 'menu', [
                __( '<strong>27 &mdash; </strong> Options', 'my-listing' ),
                __( '<strong>Theme Tools</strong>', 'my-listing' ),
                'manage_options',
                'case27/tools.php',
                '',
                c27()->image('27.jpg'),
                '0.527',
        ] );

        c27()->new_admin_page( 'submenu', [
                'case27/tools.php',
                __( 'Knowledgebase', 'my-listing' ),
                __( 'Knowledgebase', 'my-listing' ),
                'manage_options',
                'case27-tools-docs',
                function() { ?>
                    <div id="case27-docs-wrapper">
                        <iframe src="https://helpdesk.27collective.net/knowledgebase/" frameborder="0">
                    </div>
                <?php },
        ] );
    }

    /**
     * Reorder menu items in WP Admin.
     *
     * @since 1.0
     */
    public function reorder_admin_menu() {
        global $menu, $submenu;

        // Main menu (top) items.
        $main = [
            'case27/tools.php' => null,
            'edit.php?post_type=case27_listing_type' => null,
            'edit.php?post_type=job_listing' => null,
        ];

        // Theme Options submenu items.
        $theme_options = [
            'theme-general-settings'      => null,
            'theme-stats-settings'        => null,
            'theme-maps-settings'         => null,
            'theme-promotions-settings'   => null,
            'theme-social-login-settings' => null,
            'theme-integration-settings'  => null,
            'case27-tools-shortcodes'     => null,
            'pt-one-click-demo-import'    => null,
            'case27-tools-docs'           => null,
        ];

        // Listing Tools submenu items.
        $listing_tools = [
            'edit.php?post_type=case27_listing_type' => null,
            'edit.php?post_type=case27_report'       => null,
        ];

        // Reorder main menu items.
        foreach ( (array) $menu as $menu_key => $menu_item ) {
            if ( in_array( $menu_item[2], array_keys( $main ) ) ) {
                $main[ $menu_item[2] ] = $menu_item;
                unset( $menu[ $menu_key ] );
            }
        }

        $counter = 0;
        foreach ( $main as $main_item ) { $counter++;
            if ( $main_item ) {
                $menu[ sprintf( '1.%d27', $counter ) ] = $main_item;
            }
        }

        // Make sure submenu items exist.
        if ( isset( $submenu['case27/tools.php'] ) ) {
            foreach ( $submenu['case27/tools.php'] as $submenu_item ) {
                if ( in_array( $submenu_item[2], array_keys( $theme_options ) ) ) {
                    $theme_options[ $submenu_item[2] ] = $submenu_item;
                }
            }
        }

        // Make sure submenu items exist.
        if ( isset( $submenu['case27/listing-tools.php'] ) ) {
            foreach ( $submenu['case27/listing-tools.php'] as $submenu_item ) {
                if ( in_array( $submenu_item[2], array_keys( $listing_tools ) ) ) {
                    $listing_tools[ $submenu_item[2] ] = $submenu_item;
                }
            }
        }

        // Update submenu with existing items and new ordering.
        $submenu['case27/tools.php'] = array_filter( apply_filters( 'mylisting\admin-menu\theme-options', $theme_options ) );
        $submenu['case27/listing-tools.php'] = array_filter( apply_filters( 'mylisting\admin-menu\listing-tools', $listing_tools ) );
    }

    /**
     * Output the HTML markup for the Iconpicker component.
     *
     * @since 1.6.3
     */
    public function output_iconpicker_template() {
        c27()->get_partial( 'admin/iconpicker' );
    }

    /**
     * Load WP Editor custom styles.
     *
     * @since 1.0
     */
    public function load_editor_styles() {
        // add_editor_style( c27()->template_uri( sprintf( 'assets/dist/admin/editor.css?ver=%s', CASE27_THEME_VERSION ) ) );
    }

    public function add_taxonomy_columns( $columns ) {
        $cols = [];
        foreach ( (array) $columns as $key => $label ) {
            $cols[ $key ] = $label;

            if ( $key === 'slug' ) {
                $cols[ 'listing-type' ] = _x( 'Listing Type(s)', 'WP Admin > Terms List > Listing Type column title', 'my-listing' );
            }
        }

        return $cols;
    }

    public function taxonomy_columns( $content, $column, $term_id ) {
        if ( $column !== 'listing-type' ) {
            return $content;
        }

        $types = get_term_meta( $term_id, 'listing_type', true );
        $output = [];
        foreach ( (array) $types as $type_id ) {
            if ( $type = \MyListing\Ext\Listing_Types\Listing_Type::get( $type_id ) ) {
                $output[] = sprintf( '<a href="%s">%s</a>', get_edit_post_link( $type_id ), $type->get_singular_name() );
            }
        }

        return $output ? join(', ', $output) : '&mdash;';
    }
}
