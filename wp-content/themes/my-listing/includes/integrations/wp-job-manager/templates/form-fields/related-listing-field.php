<?php
// Only logged in users can fill this field.
if ( ! is_user_logged_in() ) {
	return printf(
		'<br><em><small>%s</small></em>',
		_x( 'You must be logged in to add related listings.', 'Related listing field', 'my-listing'
	) );
}

// If it's the edit listing form, then get the listing owner's ID.
// Otherwise, it's the add listing form, so we can use the logged in user id, who'll eventualle be the author.
$author_id = ( ! empty( $_REQUEST[ 'job_id' ] ) )
	? get_post_field( 'post_author', absint( $_REQUEST[ 'job_id' ] ) )
	: get_current_user_id();

// Get field value.
$selected = ! empty( $field['value'] ) ? get_post( $field['value'] ) : false;
?>

<select
	name="<?php echo esc_attr( isset( $field['name'] ) ? $field['name'] : $key ) ?>"
	id="<?php echo esc_attr( $key ) ?>"
	<?php if ( ! empty( $field['required'] ) ) echo 'required'; ?>
	class="custom-select"
	data-mylisting-ajax="true"
	data-mylisting-ajax-url="mylisting_list_posts"
	data-mylisting-ajax-params="<?php echo c27()->encode_attr( [ 'author' => $author_id, 'listing-type' => ! empty( $field['listing_type'] ) ? $field['listing_type'] : '' ] ) ?>"
	placeholder="<?php echo esc_attr( ! empty( $field['placeholder'] ) ? $field['placeholder'] : _x( 'Select listing', 'Related listing field', 'my-listing' ) ) ?>"
>
	<?php if ( $selected instanceof \WP_Post ): ?>
		<option value="<?php echo esc_attr( $selected->ID ) ?>" selected="selected"><?php echo esc_attr( $selected->post_title ) ?></option>
	<?php endif ?>

</select>
