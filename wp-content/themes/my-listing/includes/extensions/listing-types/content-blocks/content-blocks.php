<?php
/**
 * Listing layout blocks. These options will appear
 * in the listing type editor, in the order they're added here.
 *
 * @since 1.2
 */

$blocks = apply_filters( 'case27/listingtypes/profile_layout_blocks', [
	[
		'type' => 'text',
		'icon' => 'mi view_headline',
		'title' => 'Textarea',
		'show_field' => '',
		'allowed_fields' => ['text', 'texteditor', 'wp-editor', 'checkbox', 'radio', 'select', 'multiselect', 'textarea', 'email', 'url', 'number', 'location'],
	],

	[
		'type' => 'gallery',
		'icon' => 'mi insert_photo',
		'title' => 'Gallery',
		'show_field' => 'job_gallery',
		'allowed_fields' => ['file'],
		'options' => [[
			'label' => 'Gallery Type',
			'name' => 'gallery_type',
			'type' => 'select',
			'choices' => ['carousel' => 'Carousel', 'carousel-with-preview' => 'Carousel with image preview', 'grid' => 'Grid View'],
			'value' => 'carousel',
		]],
	],

	[
		'type' => 'categories',
		'icon' => 'mi view_module',
		'title' => 'Categories',
	],

	[
		'type' => 'tags',
		'icon' => 'mi view_module',
		'title' => 'Tags',
	],

	[
		'type' => 'terms',
		'icon' => 'mi view_module',
		'title' => 'Terms',
		'options' => [
			[
				'label'   => __( 'Taxonomy', 'my-listing' ),
				'name'    => 'taxonomy',
				'type'    => 'select',
				'choices' => array_column( array_map( function( $tax ) {
								return [ 'name' => $tax->label, 'slug' => $tax->name ];
							 }, \MyListing\Ext\Listing_Types\Designer::$store['taxonomies'] ), 'name', 'slug' ),
				'value'   => 'job_listing_category',
			],
			[
				'label'   => __( 'Style', 'my-listing' ),
				'name'    => 'style',
				'type'    => 'select',
				'choices' => [
					'listing-categories-block' => __( 'Colored Icons', 'my-listing' ),
					'list-block' => __( 'Outlined Icons', 'my-listing' ),
				],
				'value'   => 'listing-categories-block',
			]
		],
	],

	[
		'type' => 'location',
		'icon' => 'mi map',
		'title' => 'Location',
		'show_field' => 'job_location',
		'allowed_fields' => ['text', 'location'],
		'options' => [[
			'label' => 'Map Skin',
			'name' => 'map_skin',
			'type' => 'select',
			'value' => 'skin1',
			'choices' => c27()->get_map_skins(),
		]],
	],

	[
		'type' => 'contact_form',
		'icon' => 'mi email',
		'title' => 'Contact Form',
		'options' => [
			['label' => 'Contact Form ID', 'name' => 'contact_form_id', 'type' => 'number', 'value' => false],
			['label' => 'Send email to', 'name' => 'email_to', 'type' => 'multiselect', 'choices' => ['email'], 'value' => ['job_email']],
		],
	],

	[
		'type' => 'related_listing',
		'icon' => 'mi layers',
		'title' => 'Related Listing',
	],

	[
		'type' => 'countdown',
		'icon' => 'mi av_timer',
		'title' => 'Countdown',
		'show_field' => 'job_countdown',
		'allowed_fields' => ['text', 'date'],
	],

	[
		'type' => 'table',
		'icon' => 'mi view_module',
		'title' => 'Table',
		'options' => [[
			'label' => 'Table Rows',
			'name' => 'rows',
			'type' => 'repeater',
			'fields' => ['label', 'show_field', 'content'],
			'value' => [],
		]],
	],

	[
		'type' => 'details',
		'icon' => 'mi view_module',
		'title' => 'Details',
		'options' => [[
			'label' => 'Rows',
			'name' => 'rows',
			'type' => 'repeater',
			'fields' => ['icon', 'show_field', 'content'],
			'value' => [],
		]],
	],

	[
		'type' => 'file',
		'icon' => 'mi attach_file',
		'title' => 'Files',
		'show_field' => '',
		'allowed_fields' => ['file'],
	],

	[
		'type' => 'social_networks',
		'icon' => 'mi view_module',
		'title' => 'Social Networks',
	],

	[
		'type' => 'accordion',
		'icon' => 'mi view_module',
		'title' => 'Accordion',
		'options' => [[
			'label' => 'Rows',
			'name' => 'rows',
			'type' => 'repeater',
			'fields' => ['label', 'show_field', 'content'],
			'value' => [],
		]],
	],

	[
		'type' => 'tabs',
		'icon' => 'mi view_module',
		'title' => 'Tabs',
		'options' => [[
			'label' => 'Rows',
			'name' => 'rows',
			'type' => 'repeater',
			'fields' => ['label', 'show_field', 'content'],
			'value' => [],
		]],
	],

	[
		'type' => 'work_hours',
		'icon' => 'mi alarm',
		'title' => 'Work Hours',
	],

	[
		'type' => 'video',
		'icon' => 'mi videocam',
		'title' => 'Video',
		'show_field' => 'job_video_url',
		'allowed_fields' => ['url'],
	],

	[
		'type' => 'author',
		'icon' => 'mi account_circle',
		'title' => 'Author',
	],

	[
		'type' => 'code',
		'icon' => 'mi view_headline',
		'title' => 'Shortcode',
		'content' => '',
		'allowed_fields' => ['text', 'texteditor', 'wp-editor', 'checkbox', 'radio', 'select', 'multiselect', 'textarea', 'email', 'url', 'number', 'location', 'file'],
	],

	[
		'type' => 'raw',
		'icon' => 'mi view_module',
		'title' => 'Static Code',
		'conditional_logic' => false,
		'conditions' => [],
		'options' => [[
			'label' => 'Enter any shortcode here. This block isn\'t specific to the active listing, so it can be used for ads and similar stuff added through a shortcode or embed code.',
			'name' => 'content',
			'type' => 'textarea',
			'value' => '',
		]],
	],
] );

// Convert list of blocks to an associative array,
// using the block type as key.
$blocks = array_combine( array_column( $blocks, 'type' ), $blocks );

// Include data that will be the same for all blocks by default.
$blocks = array_map( function( $block) {
	$block['title_l10n'] = ['locale' => 'en_US'];
	$block['class'] = '';
	$block['id'] = '';

	return $block;
}, $blocks );

return $blocks;