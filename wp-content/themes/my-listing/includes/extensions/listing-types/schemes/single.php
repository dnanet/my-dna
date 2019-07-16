<?php

/**
 * Listing type single tab structure.
 *
 * @since 1.5.1
 */
return [
    'buttons' => [],
    'buttons_as_quick_action' => false,
    'menu_items' => [],
    'quick_actions' => [],
    'cover_details' => [],
    'cover_actions' => [],

    'cover' => [
    	'type' => 'image', // image or gallery
    ],

    'similar_listings' => [
    	'enabled' => true,
    	'match_by_type' => true,
    	'match_by_category' => true,
    	'match_by_tags' => false,
    	'match_by_region' => false,
    	'listing_count' => 3,
    	'orderby' => 'priority', // priority|rating|proximity
    	'max_proximity' => 100, // km
    ],
];