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
    jQuery(document).ready(function($){
        <?php if ( isset ( $_GET['cookie'] ) ) { ?>
            catapultDeleteCookie('catAccCookies');
        <?php } ?>
        if(!catapultReadCookie("catAccCookies")){ // If the cookie has not been set then show the bar
            $("html").addClass("has-cookie-bar");
            $("html").addClass("cookie-bar-<?php echo $ctcc_styles_settings['position']; ?>");
            $("html").addClass("cookie-bar-<?php echo $type; ?>");
            <?php // Move the HTML down if the bar is at the top
            if ( $ctcc_styles_settings['position'] == 'top-bar' ) {
            ?>
                // Wait for the animation on the html to end before recalculating the required top margin
                $("html").on('webkitTransitionEnd otransitionend oTransitionEnd msTransitionEnd transitionend', function(e) {
                    // code to execute after transition ends
                    var barHeight = $('#catapult-cookie-bar').outerHeight();
                    $("html").css("margin-top",barHeight);
                    $("body.admin-bar").css("margin-top",barHeight-32); // Push the body down if the admin bar is active
                });
            <?php } ?>
        }
        <?php  if ( $options['closure'] == 'timed' ) {
            // Add some script if it's on a timer
            $duration = absint($options['duration']) * 1000; ?>
            setTimeout(ctccCloseNotification, <?php echo $duration; ?>);
        <?php } else if( $options['closure'] == 'scroll' ) {
            if( isset( $options['scroll_height'] ) && intval( $options['scroll_height'] ) > 0 ) {
                $height = intval( $options['scroll_height'] );
            } else {
                $height = 200;
            }
        ?>
            $(window).scroll(function(){
                var scroll = $(window).scrollTop();
                if ( scroll > <?php echo $height; ?> ) {
                    ctccCloseNotification();
                }
            });	
        <?php } ?>
        <?php  if ( ! empty ( $options['first_page'] ) ) {
            // Add some script if the notification only displays on the first page ?>
            ctccFirstPage();
        <?php  } ?>
    });
</script>
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