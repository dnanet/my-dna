<?php

/**
 * Listing type settings tab structure.
 *
 * @since 1.5.1
 */
return [
    'icon' => '',
    'singular_name' => '',
    'plural_name' => '',
    'permalink' => '',
    'global' => false,

    'packages' => [
    	'enabled' => true,
        'used' => [],
    ],

	'reviews' => [
		'multiple' => false,
		'ratings' => [
			'enabled' => true,
			'categories' => [],
			'mode' => 10, // 10 stars or 5 stars
		],

		'gallery' => [
			'enabled' => false,
		],
	],

	'seo' => [
		'markup' => new stdClass,
	],
];