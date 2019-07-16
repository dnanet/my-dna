<?php

namespace MyListing\Ext\Demo_Import\Demos;

if ( ! defined('ABSPATH') ) {
	exit;
}

class My_Listing extends Demo {

	public function init() {
        //
	}

	public function config() {
		return [
            'demo_id' => 'mylisting-main',
		    'import_file_name' => 'MyListing (Main Demo)',
            'import_file_url' => 'https://27collective.net/files/demo/xmlfiles/mylisting.wordpress.2018-12-16.xml',
            'preview_url' => 'https://mylistingtheme.com/',
            'import_preview_image_url' => 'https://27collective.net/files/demo/img/section2-bannernew.jpg',
            'import_widget_file_url' => 'https://27collective.net/files/demo/xmlfiles/mylistingtheme.com-widgets.wie',
		];
	}

    public function before_import() {
        // mlog('Adding custom taxonomies for Main demo.');

        // Add required custom taxonomies for MyListing Main demo.
        $custom_taxonomies = \MyListing\Ext\Custom_Taxonomies\Custom_Taxonomies::instance();
        $taxonomies = array_merge( (array) get_option( 'job_manager_custom_taxonomy' ), [
            [ 'slug' => 'custom-taxonomy-car-brand', 'label' => 'Custom Taxonomy: Car Brand' ],
            [ 'slug' => 'job-vacancy-type', 'label' => 'Custom Taxonomy: Vacancy type' ],
            [ 'slug' => 'job-qualification', 'label' => 'Custom Taxonomy: Qualification' ],
            [ 'slug' => 'job-salary', 'label' => 'Custom Taxonomy: Salary' ],
        ] );

        update_option( 'job_manager_custom_taxonomy', $taxonomies );
        $custom_taxonomies->_custom_taxonomies = $taxonomies;
        $custom_taxonomies->register_taxonomies();
    }

    public function after_import() {
        //
    }

    public function theme_options() {
    	return [
    		'general_site_logo' => 21,
    		'general_brand_color' => '#6c1cff',
    		'general_loading_overlay' => 'site-logo',
    		'general_loading_overlay_background_color' => '#202125',
    		'general_loading_overlay_color' => '#fff',
            'header_style' => 'default',
            'header_skin' => 'dark',
            'header_background_color' => '#202125',
            'header_border_color' => '#202125',
            'header_fixed' => 1,
            'header_menu_location' => 'right',
            'header_logo_height' => 38,
            'header_show_search_form' => 1,
            'header_search_form_placeholder' => 'What are you looking for?',
            'header_search_form_featured_categories' => [34, 35, 28],
            'header_show_call_to_action_button' => 1,
            'header_call_to_action_links_to' => 17,
            'header_call_to_action_label' => '[27-icon icon="icon-location-pin-check-2"] Add a listing',
            'header_show_cart' => 1,
            'header_show_title_bar' => 0,
            'header_scroll_logo' => '',
            'header_scroll_skin' => 'dark',
            'header_scroll_background_color' => '#202125',
            'header_scroll_border_color' => '#202125',
            'footer_show' => 1,
            'footer_show_widgets' => 1,
            'footer_show_menu' => 1,
            'footer_text' => '© Made by 27collective',
            'footer_show_back_to_top_button' => 1,
            'general_explore_listings_page' => 2808,
            'general_explore_listings_per_page' => 20,
            'general_add_listing_page' => 17,
            'single_listing_header_preset' => 'header2',
            'single_listing_cover_overlay_color' => '#242429',
            'single_listing_cover_overlay_opacity' => '0.4',
            'listing_preview_overlay_color' => '#242429',
            'listing_preview_overlay_opacity' => '0.4',
            'single_listing_content_block_icon_color' => '#c7cdcf',
            'shop_page_product_columns' => 3,
            'shop_page_sidebar' => 'shop-page',
    	];
    }
}