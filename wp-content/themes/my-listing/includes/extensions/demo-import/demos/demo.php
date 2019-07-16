<?php

namespace MyListing\Ext\Demo_Import\Demos;

if ( ! defined('ABSPATH') ) {
	exit;
}

abstract class Demo {

    public $config;
    private $_options_map;

	public function __construct() {
        $this->config = $this->config();
        $this->_set_theme_options_map();

		add_filter( 'mylisting/demo-import/files', [ $this, 'register_demo' ] );
        add_action( 'mylisting/demo-import/'.$this->config['demo_id'].'/after-import', [ $this, 'set_theme_options' ] );
        add_action( 'mylisting/demo-import/'.$this->config['demo_id'].'/before-import', [ $this, 'before_import' ] );
        add_action( 'mylisting/demo-import/'.$this->config['demo_id'].'/after-import', [ $this, 'after_import' ] );

        $this->init();
	}

    abstract public function init();
    abstract public function config();
    abstract public function theme_options();
    abstract public function before_import();
    abstract public function after_import();

	public function register_demo( $demos ) {
		$demos[] = $this->config;
		return $demos;
	}

    public function set_theme_options() {
        $options = $this->theme_options();

        foreach ( (array) $options as $option => $value ) {
            if ( ! isset( $this->_options_map[ $option ] ) ) {
                continue;
            }

            // save option key (required by acf)
            update_option( '_options_'.$option, $this->_options_map[ $option ] );

            // save option value
            update_option( 'options_'.$option, $value );
        }
    }

    private function _set_theme_options_map() {
        $this->_options_map = [
            'general_site_logo' => 'field_595b7eda34dc9',
            'general_brand_color' => 'field_5998c6c12e783',
            'general_loading_overlay' => 'field_598dd43d705fa',
            'general_loading_overlay_color' => 'field_59ba134da1abd',
            'general_loading_overlay_background_color' => 'field_59ba138ca1abe',
            'header_style' => 'field_595b7d8981914',
            'header_skin' => 'field_59a1982a24d8f',
            'header_background_color' => 'field_595b7e899d6ac',
            'header_border_color' => 'field_59a3566469433',
            'header_fixed' => 'field_595b7dd181915',
            'header_menu_location' => 'field_595b80b1a931a',
            'header_logo_height' => 'field_59eeaac62c1c5',
            'header_show_search_form' => 'field_595b8055a9318',
            'header_search_form_placeholder' => 'field_595b8071a9319',
            'header_search_form_featured_categories' => 'field_5964e0d3bbed9',
            'header_show_call_to_action_button' => 'field_595b820157999',
            'header_call_to_action_links_to' => 'field_595b82555799a',
            'header_call_to_action_label' => 'field_595b82b95799b',
            'header_show_cart' => 'field_5c0490b2397ec',
            'header_show_title_bar' => 'field_59a3660f98ace',
            'header_scroll_logo' => 'field_59ac724a6000a',
            'header_scroll_skin' => 'field_59a350150bddf',
            'header_scroll_background_color' => 'field_59a34ff80bdde',
            'header_scroll_border_color' => 'field_59ac71706c392',
            'footer_show' => 'field_5c0b1d9b0092e',
            'footer_show_widgets' => 'field_595b85b15dbec',
            'footer_show_menu' => 'field_595b85cc5dbed',
            'footer_text' => 'field_595b85e35dbee',
            'footer_show_back_to_top_button' => 'field_598719cf8d4c3',
            'general_explore_listings_page' => 'field_595bd2fffffff',
            'general_explore_listings_per_page' => 'field_59770a24cb27d',
            'general_add_listing_page' => 'field_59a455e61eccc',
            'single_listing' => 'field_59a3619133e32',
            'single_listing_header_preset' => 'field_5963dbc3f9cbe',
            'single_listing_cover_overlay_color' => 'field_59a056ca65404',
            'single_listing_cover_overlay_opacity' => 'field_59a056ef65405',
            'listing_preview_overlay_color' => 'field_59a169755eeef',
            'listing_preview_overlay_opacity' => 'field_59a1697b5eef0',
            'single_listing_content_block_icon_color' => 'field_5aefa8d64df18',
            'shop_page_product_columns' => 'field_5af19f2bd8eed',
            'shop_page_sidebar' => 'field_5af1a04387483',
        ];
    }
}