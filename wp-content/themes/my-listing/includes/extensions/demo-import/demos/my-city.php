<?php

namespace MyListing\Ext\Demo_Import\Demos;

if ( ! defined('ABSPATH') ) {
	exit;
}

class My_City extends Demo {

	public function init() {
        //
	}

	public function config() {
		return [
            'demo_id' => 'my-city',
		    'import_file_name' => 'My City',
            'import_file_url' => 'https://27collective.net/files/demo/xmlfiles/mycity.wordpress.2017-11-21.xml',
            'preview_url' => 'https://mylisting.27collective.net/my-city/',
            'import_preview_image_url' => 'https://27collective.net/files/demo/img/section2-banner2.jpg',
		];
	}

    public function before_import() {
        //
    }

    public function after_import() {
        //
    }

    public function theme_options() {
        return [
            'general_site_logo' => 131,
            'general_brand_color' => '#ff447c',
            'general_loading_overlay' => 'material-spinner',
            'general_loading_overlay_color' => '#242833',
            'general_loading_overlay_background_color' => 'rgba(29, 35, 41, 0.98)',
            'header_style' => 'default',
            'header_skin' => 'dark',
            'header_background_color' => 'rgba(29, 35, 41, 0.97)',
            'header_border_color' => 'rgba(29, 35, 41, 0.97)',
            'header_fixed' => 1,
            'header_menu_location' => 'right',
            'header_logo_height' => 38,
            'header_show_search_form' => 1,
            'header_search_form_placeholder' => 'Type your search...',
            'header_search_form_featured_categories' => [ 27, 25, 24 ],
            'header_show_call_to_action_button' => 1,
            'header_call_to_action_links_to' => 17,
            'header_call_to_action_label' => '[27-icon icon="icon-add-circle-1"] Add Listing',
            'header_show_title_bar' => 0,
            'header_scroll_logo' => '',
            'header_scroll_skin' => 'dark',
            'header_scroll_background_color' => 'rgba(29, 35, 41, 0.97)',
            'header_scroll_border_color' => 'rgba(29, 35, 41, 0.97)',
            'footer_show_widgets' => 0,
            'footer_show_menu' => 0,
            'footer_text' => '© Made by 27collective',
            'footer_show_back_to_top_button' => 1,
            'general_explore_listings_page' => 23,
            'general_explore_listings_per_page' => 20,
            'general_add_listing_page' => 17,
            'single_listing_header_style' => 'default',
            'single_listing_header_skin' => 'dark',
            'single_listing_header_background_color' => 'rgba(25, 28, 31, 0)',
            'single_listing_header_border_color' => 'rgba(255, 255, 255, 0.2)',
            'single_listing_header_preset' => 'header2',
            'single_listing_cover_overlay_color' => '#242429',
            'single_listing_cover_overlay_opacity' => 0.5,
            'listing_preview_overlay_color' => '#242429',
            'listing_preview_overlay_opacity' => '0.5',
        ];
    }
}