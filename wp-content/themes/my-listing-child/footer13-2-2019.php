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
?>
<script type="text/javascript">
var _userway_config = {
/* uncomment the following line to override default position*/
/* position: '5', */
/* uncomment the following line to override default size (values: small, large)*/
 size: 'small', 
/* uncomment the following line to override default language (e.g., fr, de, es, he, nl, etc.)*/
/* language: 'en-US', */
/* uncomment the following line to override color set via widget (e.g., #053f67)*/
 color: '#4d296f',
/* uncomment the following line to override type set via widget(1=person, 2=chair, 3=eye)*/
/* type: '2', */
/* uncomment the following line to override support on mobile devices*/
/* mobile: true, */
account: 'u9ozvO9UL8'
};
</script>
<script type="text/javascript" src="https://cdn.userway.org/widget.js"></script>
<?php
wp_footer();

?>
</body>
</html>