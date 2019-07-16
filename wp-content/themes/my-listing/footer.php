<?php

// End of #c27-site-wrapper div.
printf('</div>');

/**
 * Only include the default footer if an Elementor custom one doesn't exist.
 *
 * @link  https://developers.elementor.com/theme-locations-api/migrating-themes/
 * @since 2.0
 */
if ( ! function_exists( 'elementor_theme_do_location' ) || ! elementor_theme_do_location( 'footer' ) ) {
	$show_footer = c27()->get_setting( 'footer_show', true ) !== false;
	if ( $show_footer && isset( $GLOBALS['c27_elementor_page'] ) && $page = $GLOBALS['c27_elementor_page'] ) {
		if ( ! $page->get_settings('c27_hide_footer') ) {
			$args = [
				'show_widgets'      => $page->get_settings('c27_footer_show_widgets'),
				'show_footer_menu'  => $page->get_settings('c27_footer_show_footer_menu'),
			];

			c27()->get_section('footer', ($page->get_settings('c27_customize_footer') == 'yes' ? $args : []));
		}
	} elseif ( $show_footer ) {
		c27()->get_section('footer');
	}
}

// MyListing footer hooks.
do_action( 'case27_footer' );
do_action( 'mylisting/get-footer' );

wp_footer();

?>
</body>
</html>