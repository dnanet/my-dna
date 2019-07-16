<?php

namespace MyListing\Ext\Demo_Import;

if ( ! defined('ABSPATH') ) {
    exit;
}

class Demo_Import {
    use \MyListing\Src\Traits\Instantiatable;

    public $demos;

    public function __construct() {
        if ( ! is_admin() ) {
            return;
        }

        $this->demos = [
            new Demos\My_Listing,
            new Demos\My_City,
            new Demos\My_Home,
            new Demos\My_Car,
        ];

        require_once trailingslashit( __DIR__ ) . 'plugin/one-click-demo-import.php';

        // @todo menus pointing to mylistingtheme.com

        // add_filter( 'pt-ocdi/import_files', apply_filters( 'mylisting/demo-import/files', [], $this ) );
        add_filter( 'pt-ocdi/import_files', [ $this, 'set_import_files' ] );
        add_action( 'pt-ocdi/after_import', [ $this, 'after_import' ] );
        add_filter( 'pt-ocdi/disable_pt_branding', '__return_true' );
        add_action( 'wp_import_insert_term', [ $this, 'map_taxonomies_id' ], 10, 2 );
        add_filter( 'pt-ocdi/plugin_page_setup', [ $this, 'plugin_page_setup' ], 30 );
        add_action( 'pt-ocdi/before_content_import_execution', [ $this, 'before_import' ], 10, 3 );
        add_filter( 'wxr_importer.pre_process.post_meta', [$this, 'post_meta_filter'], 1, 2 );
    }

    public function post_meta_filter( $meta_item, $post_id ) {
        if ( '_elementor_data' != $meta_item['key'] ) {
            return $meta_item;
        }

        try {
            $data = json_decode( $meta_item['value'] );
        } catch( Exception $e ) {}

        if ( ! $data ) {
            return $meta_item;
        }

        foreach ( $data as &$settings ) {
            $this->parse_data( $settings );
        }

        $meta_item['value'] = json_encode( $data );

        return $meta_item;
    }

    protected function parse_data( &$data, $return_status = false ) {
        global $wpdb;

        if ( isset( $data->elements ) && is_array( $data->elements ) ) {
            foreach( $data->elements as &$element ) {
                $return_status = $this->parse_data( $element, $return_status );
            }
        }

        if ( isset( $data->settings ) ) {

            $taxonomies = [];

            foreach ( [ 'select_categories', 'select_regions', 'select_tags', '27_content' ] as $taxomony ) {

                if ( ! empty( $data->settings->{ $taxomony } ) ) {

                    if ( $taxomony == '27_content' ) {
                        $this->fix_categories_shortcode( $data->settings->{ $taxomony } );
                        continue;
                    }

                    $taxonomies[] =& $data->settings->{ $taxomony };
                }
            }

            foreach ( $taxonomies as $taxomony ) {

                foreach( $taxomony as &$category ) {

                    $meta_data = $wpdb->get_row(
                        $wpdb->prepare(
                            "select * from {$wpdb->prefix}termmeta WHERE meta_key = '_old_meta_key' and meta_value = '%d'",
                            $category->category_id
                            )
                        );

                    if ( ! $meta_data ) {
                        continue;
                    }

                     $category->category_id = $meta_data->term_id;
                }
            }

            $return_status = true;
        }

        return $return_status;
    }

    public function fix_categories_shortcode( &$content ) {
        global $wpdb;

        if ( ! has_shortcode( $content, '27-categories' ) ) {
            return null;
        }

        $pattern = get_shortcode_regex();

        preg_match_all( '/'. $pattern .'/s', $content, $matches, PREG_SET_ORDER );

        foreach ( $matches as $shortcode ) {

            if ( ! isset( $shortcode[2] ) || '27-categories' != $shortcode[2] ) {
                continue;
            }

            $attributes = (array) shortcode_parse_atts( $shortcode[3] );

            if ( empty( $attributes['ids'] ) ) {
                continue;
            }

            $ids = explode( ',', $attributes['ids'] );

            foreach ( $ids as &$category_id ) {

                $meta_data = $wpdb->get_row(
                    $wpdb->prepare(
                        "select * from {$wpdb->prefix}termmeta WHERE meta_key = '_old_meta_key' and meta_value = '%d'",
                        $category_id
                        )
                    );

                if ( ! $meta_data ) {
                    continue;
                }

                 $category_id = $meta_data->term_id;
            }

            $attributes['ids'] = implode( ',', $ids );

            $new_shortcode = ['27-categories'];

            foreach ( $attributes as $key => $value) {
               $new_shortcode[] = $key . '="' . $value . '"';
            }

            $new_shortcode = '[' . implode(' ', $new_shortcode ) . ']';

            $content = str_ireplace( $shortcode[0], $new_shortcode, $content );
        }
    }

    public function map_taxonomies_id( $term_id, $data ) {
        if ( ! isset( $data['id'] ) ) {
            return null;
        }

        add_term_meta( $term_id, '_old_meta_key', $data['id'], true );
    }

    public function set_import_files() {
        return apply_filters( 'mylisting/demo-import/files', [], $this );
    }

    public function after_import( $selected_import ) {
        $locations = get_theme_mod( 'nav_menu_locations' ); // registered menu locations in theme
        $menus = wp_get_nav_menus();

        if ( $menus ) {
            foreach( $menus as $menu ) { // assign menus to theme locations
                if ( $menu->name == 'Main Menu' ) {
                    $locations['primary'] = $menu->term_id;
                } else if( $menu->name == 'Footer Menu' ) {
                    $locations['footer'] = $menu->term_id;
                }
            }
        }

        set_theme_mod( 'nav_menu_locations', $locations ); // set menus to locations

        // Assign front page and posts page (blog page).
        $front_page_id = get_page_by_title( 'Home' );
        $blog_page_id  = get_page_by_title( 'Blog' );

        update_option( 'show_on_front', 'page' );
        update_option( 'page_on_front', $front_page_id->ID );
        update_option( 'page_for_posts', $blog_page_id->ID );

        $cart_page_id = get_page_by_title( 'Cart' );
        $checkout_page_id = get_page_by_title( 'Checkout' );
        $myaccount_page_id = get_page_by_title( 'My account' );

        update_option( 'woocommerce_cart_page_id', $cart_page_id->ID );
        update_option( 'woocommerce_checkout_page_id', $checkout_page_id->ID );
        update_option( 'woocommerce_myaccount_page_id', $myaccount_page_id->ID );

        do_action( sprintf( 'mylisting/demo-import/%s/after-import', $selected_import['demo_id'] ), $selected_import );
    }

    /**
     * Edit name and location of the plugin page in wp-admin.
     *
     * @since 1.7.0
     */
    public function plugin_page_setup( $page ) {
        // Under Theme Tools.
        $page['parent_slug'] = 'case27/tools.php';
        $page['menu_title'] = 'Demo Import';
        $page['capability'] = 'administrator';

        return $page;
    }

    public function before_import( $selected_import_files, $import_files, $selected_index ) {
        $demo = is_array( $import_files ) && isset( $import_files[ $selected_index ] ) ? $import_files[ $selected_index ] : false;
        if ( ! is_array( $demo ) || empty( $demo['demo_id'] ) ) {
            return;
        }

        do_action( sprintf( 'mylisting/demo-import/%s/before-import', $demo['demo_id'] ), $demo );
    }
}
