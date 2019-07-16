<?php

namespace MyListing\Ext\Demo_Import\Demos;

if ( ! defined('ABSPATH') ) {
	exit;
}

class My_Car extends Demo {

	public function init() {
        //
	}

	public function config() {
		return [
            'demo_id' => 'my-car',
		    'import_file_name' => 'My Car',
            'import_file_url' => 'https://27collective.net/files/demo/xmlfiles/mycar.wordpress.2017-11-21.xml',
            'preview_url' => 'https://mylisting.27collective.net/mycar/',
            'import_preview_image_url' => 'https://27collective.net/files/demo/img/section4-banner.jpg',
            'import_widget_file_url' => 'https://27collective.net/files/demo/mycar/widgets.wie',
            'import_notice' => 'After you import this demo, you will have to setup the slider separately.',
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
            'general_site_logo' => 138,
            'general_brand_color' => '#018bb5',
            'general_loading_overlay' => 'material-spinner',
            'general_loading_overlay_color' => '#242833',
            'general_loading_overlay_background_color' => '#ffffff',
            'header_style' => 'default',
            'header_skin' => 'dark',
            'header_background_color' => '#018bb5',
            'header_border_color' => '#018bb5',
            'header_fixed' => 1,
            'header_menu_location' => 'right',
            'header_logo_height' => 38,
            'header_show_search_form' => 1,
            'header_search_form_placeholder' => 'What are you looking for?',
            'header_search_form_featured_categories' => [27, 25, 24],
            'header_show_call_to_action_button' => 1,
            'header_call_to_action_links_to' => 17,
            'header_call_to_action_label' => '[27-icon icon="icon-add-circle-1"] Add Listing',
            'header_show_title_bar' => 0,
            'header_scroll_logo' => '',
            'header_scroll_skin' => 'dark',
            'header_scroll_background_color' => '#018bb5',
            'header_scroll_border_color' => '#018bb5',
            'footer_show_widgets' => 1,
            'footer_show_menu' => 1,
            'footer_text' => '© Made by 27collective',
            'footer_show_back_to_top_button' => 1,
            'general_explore_listings_page' => 23,
            'general_explore_listings_per_page' => 9,
            'general_add_listing_page' => 17,
            'single_listing_header_style' => 'default',
            'single_listing_header_skin' => 'dark',
            'single_listing_header_background_color' => '#018bb5',
            'single_listing_header_border_color' => '#018bb5',
            'single_listing_header_preset' => 'default',
            'single_listing_cover_overlay_color' => '#018bb5',
            'single_listing_cover_overlay_opacity' => 1,
            'listing_preview_overlay_color' => '#15191b',
            'listing_preview_overlay_opacity' => '0.4',
        ];
    }
}