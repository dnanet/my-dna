<?php
/**
 * Shows the 'checkbox' form field on listing forms.
 *
 * @since 1.7.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>

<div class="md-checkbox">
	<input
		type="checkbox"
		name="<?php echo esc_attr( isset( $field['name'] ) ? $field['name'] : $key ); ?>"
		id="<?php echo esc_attr( $key ); ?>"
		value="1"
		<?php checked( ! empty( $field['value'] ), true ); ?>
		<?php if ( ! empty( $field['required'] ) ) echo 'required'; ?>
	>
	<label for="<?php echo esc_attr( $key ); ?>">
		<?php if ( ! empty( $field['description'] ) ) : ?><?php echo $field['description']; ?><?php endif; ?>
	</label>
</div>
