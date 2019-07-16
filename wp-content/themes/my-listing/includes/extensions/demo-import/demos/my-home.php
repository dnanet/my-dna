<?php

namespace MyListing\Ext\Demo_Import\Demos;

if ( ! defined('ABSPATH') ) {
	exit;
}

class My_Home extends Demo {

    public function init() {
        //
    }

	public function config() {
		return [
            'demo_id' => 'my-home',
		    'import_file_name' => 'My Home',
            'import_file_url' => 'https://27collective.net/files/demo/xmlfiles/myhome.wordpress.2017-11-21.xml',
            'preview_url' => 'https://mylisting.27collective.net/myhome',
            'import_preview_image_url' => 'https://27collective.net/files/demo/img/section3-banner.jpg',
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
            'general_site_logo' => 140,
            'general_brand_color' => '#ff5a64',
            'general_loading_overlay' => 'site-logo',
            'general_loading_overlay_color' => '#ff5a64',
            'general_loading_overlay_background_color' => '#ffffff',
            'header_style' => 'default',
            'header_skin' => 'light',
            'header_background_color' => '#ffffff',
            'header_border_color' => 'rgba(25, 28, 31, 0)',
            'header_fixed' => 0,
            'header_menu_location' => 'right',
            'header_logo_height' => 42,
            'header_show_search_form' => 0,
            'header_search_form_placeholder' => 'Type your search...',
            'header_search_form_featured_categories' => [27, 25, 24],
            'header_show_call_to_action_button' => 1,
            'header_call_to_action_links_to' => 17,
            'header_call_to_action_label' => '[27-icon icon="icon-add-circle-1"] Add Listing',
            'header_show_title_bar' => 0,
            'header_scroll_logo' => '',
            'header_scroll_skin' => 'light',
            'header_scroll_background_color' => '#ffffff',
            'header_scroll_border_color' => 'rgba(0, 0, 0, 0.10)',
            'footer_show_widgets' => 0,
            'footer_show_menu' => 1,
            'footer_text' => '© Made with <i class="fa fa-heart-o" style="color: #f2498b;"></i> by <a href="http://27collective.net" target="_blank">27collective</a>',
            'footer_show_back_to_top_button' => 1,
            'general_explore_listings_page' => 23,
            'general_explore_listings_per_page' => 9,
            'general_add_listing_page' => 17,
            'single_listing_header_style' => 'default',
            'single_listing_header_skin' => 'light',
            'single_listing_header_background_color' => '#ffffff',
            'single_listing_header_border_color' => 'rgba(25, 28, 31, 0)',
            'single_listing_header_preset' => 'header4',
            'single_listing_cover_overlay_color' => '#242429',
            'single_listing_cover_overlay_opacity' => 0.5,
            'listing_preview_overlay_color' => '#242429',
            'listing_preview_overlay_opacity' => '0.4',
        ];
    }
}