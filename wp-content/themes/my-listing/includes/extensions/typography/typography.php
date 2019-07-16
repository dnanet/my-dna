<?php

namespace MyListing\Ext\Typography;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Typography {
    use \MyListing\Src\Traits\Instantiatable;

    /*
     * Constructor.
     *
     * @access public
     * @return void
     */
    public function __construct() {
        add_action( 'wp_head', [ $this, 'load_custom_style'], 99 );
        add_action( 'acf/save_post', [ $this, 'update_style_code' ], 99 );
        add_action( 'admin_init', [ $this, 'add_selector_field' ] );
    }

    public function load_custom_style() {
        $style_code = get_option('mylisting_typography_style');

        if ( ! $style_code ) {
            return null;
        } ?>

        <style type="text/css" id="mylisting-typography-styles"><?php echo strip_tags( str_replace( "\n", '', $style_code ) ) ?></style>
        <?php
    }

    public function update_style_code( $post_id ) {
        if ( ! isset( $_GET['page'] ) || 'theme-general-settings' != $_GET['page'] || empty( $_POST['acf'] ) ) {
            return null;
        }

        $css_values = [];
        foreach( $_POST['acf'] as $field_key => $field_value ) {

            $field = acf_get_field( $field_key );
            $sub_fields = acf_get_fields( $field );

            if ( ! $field || ! isset( $field['name'] ) || empty( $field['css_selector'] ) || ! $sub_fields ) {
                continue;
            }

            $css_selector = $field['css_selector'];

            $group_settings = c27()->get_setting( $field['name'], [] );

            $css_properties = [];
            foreach ( $sub_fields as $field ) {

                if ( ! is_array( $field ) || ! isset( $field['name'] )
                    || empty( $group_settings[ $field['name'] ] ) ) {
                    continue;
                }

                $field_name = $field['name'];
                $field_value = $group_settings[ $field_name ];

                if ( $pixelated_value = $this->value_with_unit( $field_name, $field_value ) ) {
                    $css_properties[ $field_name ] = $field_name . ': ' . $pixelated_value . ';';
                }
            }

            // Do not include the css code if the values are empty
            if ( ! $css_properties ) {
                continue;
            }

            $css_values[] = $css_selector . " {\n" . implode( "\n", $css_properties ) . "\n}";
        }

        // Save the updates
        update_option( 'mylisting_typography_style', implode( '', $css_values ), true );
    }

    public function add_selector_field() {
        global $pagenow;

        if ( 'post.php' != $pagenow || ! isset( $_GET['post'] ) || 'acf-field-group' != get_post_type( $_GET['post'] ) || 'theme-options' != get_post( $_GET['post'] )->post_excerpt ) {
            return null;
        }

        add_action( 'acf/render_field_settings', [ $this, 'render_selector_field' ] );
    }

    public function render_selector_field( $field ) {
        acf_render_field_setting( $field, [
            'label'         => esc_html__('CSS Selector','my-listing'),
            'type'          => 'textarea',
            'name'          => 'css_selector',
        ], true );
    }

    public function value_with_unit( $field_name, $field_value ) {
        if ( ! trim( $field_value ) || trim( $field_value ) === 'default' || ! is_numeric( trim( $field_value ) ) ) {
            return false;
        }

        $unit = 'px';
        $css_properties = [
            'font-size',
            'font-weight',
            'height',
            'width',
            'left',
            'right',
            'padding-top',
            'padding-left',
            'padding-right',
            'padding-bottom',
            'margin-top',
            'margin-left',
            'margin-right',
            'margin-bottom',
        ];

        if ( ! in_array( $field_name, $css_properties ) ) {
            return false;
        }

        if ( $field_name === 'font-weight' ) {
            $unit = '';
        }

        return trim( $field_value ) . $unit;
    }
}