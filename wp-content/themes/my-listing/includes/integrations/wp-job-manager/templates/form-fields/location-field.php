<?php
$listing_id = ! empty( $_REQUEST[ 'job_id' ] ) ? absint( $_REQUEST[ 'job_id' ] ) : 0;
$latitude = $longitude = $lock_pin = false;
if ( $listing_id ) {
	$latitude = get_post_meta($listing_id, 'geolocation_lat', true);
	$longitude = get_post_meta($listing_id, 'geolocation_long', true);
	$lock_pin = get_post_meta($listing_id, 'job_location__lock_pin', true);
	// dump($latitude, $longitude, $lock_pin);
}

$lock_pin_id = esc_attr( isset( $field['name'] ) ? $field['name'] : $key ) . '__lock_pin';
$latitude_id = esc_attr( isset( $field['name'] ) ? $field['name'] : $key ) . '__latitude';
$longitude_id = esc_attr( isset( $field['name'] ) ? $field['name'] : $key ) . '__longitude';

$map_options = [
	'skin' => ! empty( $field['map-skin'] ) ? $field['map-skin'] : false,
	'cluster_markers' => false,
	'scrollwheel' => true,
];

$options = [
	'default-lat' => ! empty( $field['map-default-location'] ) && ! empty( $field['map-default-location']['lat'] ) ? $field['map-default-location']['lat'] : 51.5072,
	'default-lng' => ! empty( $field['map-default-location'] ) && ! empty( $field['map-default-location']['lng'] ) ? $field['map-default-location']['lng'] : -0.1280,
];
?>

<div class="location-field-wrapper" data-options="<?php echo c27()->encode_attr( $options ) ?>">
	<input
		type="text"
		class="input-text address-input"
		id="<?php echo esc_attr( $key ); ?>"
		name="<?php echo esc_attr( isset( $field['name'] ) ? $field['name'] : $key ); ?>"
		placeholder="<?php echo esc_attr( $field['placeholder'] ); ?>"
		value="<?php echo isset( $field['value'] ) ? esc_attr( $field['value'] ) : ''; ?>"
		maxlength="<?php echo ! empty( $field['maxlength'] ) ? esc_attr( $field['maxlength'] ) : ''; ?>"
		<?php if ( ! empty( $field['required'] ) ) echo 'required'; ?>
		>
    <i class="mi my_location cts-get-location" data-input="#<?php echo esc_attr( $key ); ?>" data-map="location-picker-map"></i>

	<div class="location-actions">
		<div class="lock-pin">
			<input id="<?php echo esc_attr( $lock_pin_id ) ?>" type="checkbox" name="<?php echo esc_attr( $lock_pin_id ) ?>" value="yes" <?php echo $lock_pin == 'yes' ? 'checked="checked"' : '' ?>>
			<label for="<?php echo esc_attr( $lock_pin_id ) ?>" class="locked"><i class="mi lock_outline"></i><?php _e( 'Unlock Pin Location', 'my-listing' ) ?></label>
			<label for="<?php echo esc_attr( $lock_pin_id ) ?>" class="unlocked"><i class="mi lock_open"></i><?php _e( 'Lock Pin Location', 'my-listing' ) ?></label>
		</div>

		<div class="enter-coordinates-toggle">
			<span><?php _e( 'Enter coordinates manually', 'my-listing' ) ?></span>
		</div>
	</div>

	<div class="location-coords hide">
		<div class="form-group">
			<label for="<?php echo esc_attr( $latitude_id ) ?>"><?php _e( 'Latitude', 'my-listing' ) ?></label>
			<input type="text" name="<?php echo esc_attr( $latitude_id ) ?>" id="<?php echo esc_attr( $latitude_id ) ?>" class="latitude-input" value="<?php echo esc_attr( $latitude ) ?>">
		</div>
		<div class="form-group">
			<label for="<?php echo esc_attr( $longitude_id ) ?>"><?php _e( 'Longitude', 'my-listing' ) ?></label>
			<input type="text" name="<?php echo esc_attr( $longitude_id ) ?>" id="<?php echo esc_attr( $longitude_id ) ?>" class="longitude-input" value="<?php echo esc_attr( $longitude ) ?>">
		</div>
	</div>

	<div class="c27-map picker" id="location-picker-map" data-options="<?php echo htmlspecialchars( json_encode( $map_options ), ENT_QUOTES, 'UTF-8' ); ?>"></div>
</div>