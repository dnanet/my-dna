<?php
/**
 * Quick actions
 *
 * @since 2.0
 */

$actions = apply_filters( 'mylisting/types/quick-actions', [

	/* Get Directions */
	[
		'action' => 'get-directions',
		'label' => 'Get directions',
		'icon' => 'icon-location-pin-add-2',
	],

	/* Call Now */
	[
		'action' => 'call-now',
		'label' => 'Call now',
		'icon' => 'icon-phone-outgoing',
	],

	/* Leave Review */
	[
		'action' => 'leave-review',
		'label' => 'Leave a review',
		'icon' => 'icon-chat-bubble-square-1',
	],

	/* Bookmark */
	[
		'action' => 'bookmark',
		'label' => 'Bookmark',
		'icon' => 'mi favorite_border',
	],

	/* Share */
	[
		'action' => 'share',
		'label' => 'Share',
		'icon' => 'mi share',
	],

	/* Claim Listing */
	[
		'action' => 'claim-listing',
		'label' => 'Claim listing',
		'icon' => 'icon-location-pin-check-2',
	],

	/* Report */
	[
		'action' => 'report-listing',
		'label' => 'Report',
		'icon' => 'mi error_outline',
	],

	/* Visit Website */
	[
		'action' => 'visit-website',
		'label' => 'Website',
		'icon' => 'fa fa-link',
	],

	/* Send Email */
	[
		'action' => 'send-email',
		'label' => 'Send an email',
		'icon' => 'icon-email-outbox',
	],

	/* Plain */
	[
		'action' => 'plain',
		'label' => 'Display a field',
		'icon' => 'mi info_outline',
	],

	/* Custom */
	[
		'action' => 'custom',
		'label' => 'Custom action',
		'link' => '',
		'icon' => 'mi info_outline',
	],
] );

// Convert list of actions to an associative array,
// using the action name as key.
$actions = array_combine( array_column( $actions, 'action' ), $actions );

// Include data that will be the same for all actions by default.
$actions = array_map( function( $action ) {
	$action['title_l10n'] = ['locale' => 'en_US'];
	$action['class'] = '';
	$action['id'] = '';

	return $action;
}, $actions );

return $actions;