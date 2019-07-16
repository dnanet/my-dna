<?php

namespace MyListing\Ext\Custom_Taxonomies;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Custom_Taxonomies {
    use \MyListing\Src\Traits\Instantiatable;

    /**
     * List of custom taxonomies
     * @var array
     */
    public $_custom_taxonomies = [];

    public function __construct() {

        $this->_custom_taxonomies = self::custom_taxonomies_list();

        $saved_option = get_option( 'job_manager_custom_taxonomy' );

        if ( ! $saved_option || ! is_array( $saved_option ) ) {
            add_option( 'job_manager_custom_taxonomy', [] );
        }

        add_action( 'admin_enqueue_scripts', [ $this, 'admin_scripts' ], 50 );
        add_action( 'admin_footer', [ $this, 'footer_template' ] );

        add_filter( 'job_manager_settings', [ $this, 'extend_job_manager_settings'] );
        add_action( 'wp_job_manager_admin_field_inline', [ $this, 'inline_admin_field_html' ], 10, 4);

        if ( ! $this->_custom_taxonomies ) {
            return $this->_custom_taxonomies = [];
        }

        add_action( 'case27_register_taxonomies', [ $this, 'register_taxonomies' ], 99 );
        add_filter( 'mylisting/types/fields/presets', [ $this, 'add_term_select_field' ], 99 );
    }

    public function admin_scripts() {
        if ( ! $this->is_edit_taxonomy_page() ) {
            return null;
        }

        wp_enqueue_script( 'mylisting-admin-custom-taxonomies' );
        wp_localize_script( 'mylisting-admin-custom-taxonomies', 'c27_custom_taxonomies', [
            'deleteMsg'  => esc_html__('Are you sure you want to delete this taxonomy?', 'my-listing'),
            'requiredMsg'=> esc_html__('All fields values are required.', 'my-listing'),
            'taxonomies' => $this->_custom_taxonomies
        ]);
    }

    public function footer_template() {
        // Do nothing if this is not taxonomy page
        if ( ! $this->is_edit_taxonomy_page() ) {
            return null;
        } ?>

        <script type="text/html" id="tmpl-c27-custom-taxonomies">

            <# data.taxonomies.forEach( ( settings, id ) => { #>

            <div class="head-button field" draggable="false" data-taxonomy>
                <h5>
                    <span class="prefix">+</span>
                    <span data-label>{{{ settings.label }}}</span>
                    <span class="actions">
                        <span title="<?php esc_attr_e('Delete this button', 'my-listing'); ?>" data-delete-btn><i class="mi delete"></i></span>
                    </span>
                </h5>

                <div class="edit">
                    <div class="form-group">
                        <label><?php esc_html_e('Label', 'my-listing'); ?></label>
                        <input name="job_manager_custom_taxonomy[{{{data.count}}}][label]" type="text" class="regular-text" value="{{{ settings.label }}}" data-field-label />
                    </div>

                    <div class="form-group">
                        <label><?php esc_html_e('Taxonomy Slug', 'my-listing'); ?></label>
                        <input name="job_manager_custom_taxonomy[{{{data.count}}}][slug]" type="text" class="regular-text" value="{{{ settings.slug }}}" data-field-slug {{{ ! settings.can_edit_slug ? 'readonly' : '' }}}/>
                    </div>
                </div>
            </div>

            <# data.count++
            }); #>
        </script>
    <?php
    }

    public function extend_job_manager_settings( $settings_menu_list ) {
        $settings_menu_list['taxonomies'] = [
            esc_html__( 'Taxonomies', 'my-listing' ), [
                [
                    'name'       => 'job_manager_custom_taxonomy',
                    'std'        => '',
                    'label'      => esc_html__( 'Custom Taxonomies', 'my-listing' ),
                    'type'       => 'inline',
                ],
            ],
        ];

        return $settings_menu_list;
    }

    public function inline_admin_field_html( $option, $attributes, $value, $placeholder ) { ?>
        <section class="section tabs-content" id="section-result-template">
            <div class="card fields-wrapper">
                <div class="fields-draggable">
                    <div class="taxonomy-fields" id="c27-custom-taxonomies"></div>

                    <a class="btn btn-outline-dashed" id="c27-add-taxonomy">
                        <?php esc_html_e('Add Taxonomy', 'my-listing'); ?>
                    </a>
                </div>
            </div>
        </section>
    <?php
    }

    public function add_term_select_field( $default_fields ) {
        foreach ( $this->_custom_taxonomies as $key => $value ) {

            $default_fields[ $value['slug'] ] = new \MyListing\Ext\Listing_Types\Fields\TermSelectField([
                'slug'           => $value['slug'],
                'label'          => $value['label'],
                'required'       => false,
                'priority'       => 5,
                'taxonomy'       => $value['slug'],
                'is_custom'      => false,
                'terms-template' => 'multiselect',
            ]);
        }

        return $default_fields;
    }

    public function register_taxonomies() {
        foreach ( $this->_custom_taxonomies as $ct => $value ) {

            $title = $value['label'];

            $labels = array(
                'name'                  => _x( $title, 'Taxonomy plural name', 'my-listing' ),
                'singular_name'         => _x( $title, 'Taxonomy singular name', 'my-listing' ),
                'search_items'          => __( 'Search Items', 'my-listing' ),
                'popular_items'         => __( 'Popular Items', 'my-listing' ),
                'all_items'             => __( 'All Items', 'my-listing' ),
                'parent_item'           => __( 'Parent Item', 'my-listing' ),
                'parent_item_colon'     => __( 'Parent Item', 'my-listing' ),
                'edit_item'             => __( 'Edit Item', 'my-listing' ),
                'update_item'           => __( 'Update Item', 'my-listing' ),
                'add_new_item'          => __( 'Add New Item', 'my-listing' ),
                'new_item_name'         => __( 'New Item', 'my-listing' ),
                'add_or_remove_items'   => __( 'Add or remove Item', 'my-listing' ),
                'choose_from_most_used' => __( 'Choose from most used Items', 'my-listing' ),
                'menu_name'             => __( $title, 'my-listing' ),
            );

            $args = array(
                'labels'            => $labels,
                'public'            => true,
                'show_in_nav_menus' => true,
                'show_admin_column' => false,
                'hierarchical'      => true,
                'show_tagcloud'     => true,
                'show_ui'           => true,
                'query_var'         => true,
                'rewrite'           => true,
                'query_var'         => true,
                'capabilities'      => array(),
            );

            register_taxonomy( $value['slug'], array( 'job_listing' ), $args );
        }
    }

    public function get_custom_taxonomies_list( $key = 'slug', $value = 'label'  ) {
        $taxonomies = [];
        if ( ! in_array( $key, [ 'slug', 'label' ] ) || ! in_array( $value, [ 'slug', 'label' ] ) ) {
            return $taxonomies;
        }

        foreach ( $this->_custom_taxonomies as $taxonomy ) {
            if ( ! $taxonomy ) {
                continue;
            }

            $taxonomies[ $taxonomy[ $key ] ] = $taxonomy[ $value ];
        }

        return $taxonomies;
    }

    public function append_custom_taxonomies_slug( $taxonomies_list ) {

        foreach ( $this->_custom_taxonomies as $key => $value ) {

            if ( ! $value ) {
                continue;
            }

            $taxonomies_list[ $value['slug'] ] = $value['slug'];
        }

        return $taxonomies_list;
    }

    public function is_edit_taxonomy_page() {
        global $pagenow;

        return 'edit.php' == $pagenow && isset( $_REQUEST['page'] ) && 'job-manager-settings' == $_REQUEST['page'];
    }

    public static function custom_taxonomies_list() {
        $taxonomies = get_option( 'job_manager_custom_taxonomy' );

        if ( ! $taxonomies ) {
            return [];
        }

        $return_list = [];

        foreach ( (array) $taxonomies as $taxonomy ) {

            if ( empty( $taxonomy['slug'] ) || empty( $taxonomy['label'] ) ) {
                continue;
            }

            $return_list[] = [
                'slug'  => sanitize_title( $taxonomy['slug'] ),
                'label' => esc_html( $taxonomy['label'] ),
            ];
        }

        return $return_list;
    }

    private function _normalize_option_data( $option_data ) {

        $return_list = [];

        foreach ( $option_data as $data ) {
            $return_list[ $data['slug'] ] = $data['label'];
        }

        return array_filter( $return_list );
    }
}