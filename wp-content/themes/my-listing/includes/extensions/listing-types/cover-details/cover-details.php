<?php
/**
 * Cover details.
 *
 * @since 2.0
 */

$items = apply_filters( 'mylisting/types/cover-details', [

	/* Event date */
	[
		'label' => 'Event date',
		'field' => 'job_date',
		'prefix' => '',
		'suffix' => '',
		'format' => 'date',
	],

	/* Price range */
	[
		'label' => 'Price range',
		'field' => 'price_range',
		'prefix' => '',
		'suffix' => '',
		'format' => 'plain',
	],

	/* Contact email */
	[
		'label' => 'Contact Email',
		'field' => 'job_email',
		'prefix' => '',
		'suffix' => '',
		'format' => 'plain',
	],
] );

// Convert list of items to an associative array,
// using the action name as key.
$items = array_combine( array_column( $items, 'action' ), $items );

// Include data that will be the same for all items by default.
$items = array_map( function( $item ) {
	$item['title_l10n'] = ['locale' => 'en_US'];
	$item['class'] = '';
	$item['id'] = '';

	return $item;
}, $items );

return $items;