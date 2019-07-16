<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php esc_attr( bloginfo( 'charset' ) ) ?>" />
	<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no" />
	<link rel="pingback" href="<?php esc_attr( bloginfo( 'pingback_url' ) ) ?>">

	<?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php
/**
 * Action hook immediately after the opening <body> tag.
 *
 * @since 1.6.6
 */
do_action( 'mylisting/body/start' ) ?>

<?php
// Initialize custom styles global.
$GLOBALS['case27_custom_styles'] = '';

// Wrap site in #c27-site-wrapper div.
printf( '<div id="c27-site-wrapper">' );

// Include loading screen animation.
c27()->get_partial( 'loading-screens/' . c27()->get_setting( 'general_loading_overlay', 'none' ) ); ?>

<?php
/**
 * Only include the default header if an Elementor custom one doesn't exist.
 *
 * @link  https://developers.elementor.com/theme-locations-api/migrating-themes/
 * @since 2.0
 */
if ( ! function_exists( 'elementor_theme_do_location' ) || ! elementor_theme_do_location( 'header' ) ) {
	$pageTop = apply_filters('case27_pagetop_args', [
		'header' => [
			'show' => true,
			'args' => [],
		],

		'title-bar' => [
			'show' => c27()->get_setting('header_show_title_bar', false),
			'args' => [
				'title' => get_the_archive_title(),
				'ref' => 'default-title-bar',
			],
		]
	]);

	if ($pageTop['header']['show']) {
		c27()->get_section('header', $pageTop['header']['args']);

		if ($pageTop['title-bar']['show']) {
			c27()->get_section('title-bar', $pageTop['title-bar']['args']);
		}
	}
} ?>
