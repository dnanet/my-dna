<?php
/**
 * `Get Directions` quick action.
 *
 * @since 2.0
 */

if ( ! ( ( $lat = $listing->get_data('geolocation_lat') ) && ( $lng = $listing->get_data('geolocation_long') ) ) ) {
	return;
}

$query = $listing->get_field('location') ? : join( ',', [ $lat, $lng ] );
$link = sprintf( 'http://maps.google.com/maps?daddr=%s', urlencode( $query ) );
?>

<li id="<?php echo esc_attr( $action['id'] ) ?>" class="<?php echo esc_attr( $action['class'] ) ?>">
    <a href="<?php echo esc_url( $link ) ?>" target="_blank">
    	<?php echo c27()->get_icon_markup( $action['icon'] ) ?>
    	<span><?php echo $action['label'] ?></span>
    </a>
</li>