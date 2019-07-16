<?php
/**
 * Shows the `file` form field on listing forms.
 *
 * @since 2.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$classes     = ['input-text', 'review-gallery-input'];
$is_multiple = ! empty( $field['multiple'] );
$allowed_mime_types = array_keys( ! empty( $field['allowed_mime_types'] ) ? $field['allowed_mime_types'] : get_allowed_mime_types() );
$field_name         = isset( $field['name'] ) ? $field['name'] : $key;
$field_name         .= $is_multiple ? '[]' : '';
$uploaded_files = ( empty( $field['value'] ) ? [] : ( is_array( $field['value'] ) ? $field['value'] : [ $field['value'] ] ) );

if ( ! empty( $field['ajax'] ) && job_manager_user_can_upload_file_via_ajax() ) {
	wp_enqueue_script( 'wp-job-manager-ajax-file-upload' );
	$classes[] = 'wp-job-manager-file-upload';
}
?>

<div class="file-upload-field <?php echo esc_attr( $is_multiple ? 'multiple-uploads' : 'single-upload' ) ?> form-group-review-gallery">
	<input
		type="file"
		class="<?php echo esc_attr( implode( ' ', $classes ) ) ?>"
		data-file_types="<?php echo esc_attr( implode( '|', $allowed_mime_types ) ) ?>"
		<?php if ( ! empty( $field['multiple'] ) ) echo 'multiple' ?>
		name="<?php echo esc_attr( isset( $field['name'] ) ? $field['name'] : $key ) ?><?php if ( ! empty( $field['multiple'] ) ) echo '[]' ?>"
		id="<?php echo esc_attr( $key ) ?>"
		placeholder="<?php echo empty( $field['placeholder'] ) ? '' : esc_attr( $field['placeholder'] ) ?>"
		style="display: none;"
	>
	<div class="uploaded-files-list review-gallery-images">
		<label class="upload-file review-gallery-add" for="<?php echo esc_attr( $key ) ?>">
			<i class="mi file_upload"></i>
			<div class="content"></div>
		</label>

		<div class="job-manager-uploaded-files">
			<?php foreach ( $uploaded_files as $file ): ?>
				<?php get_job_manager_template( 'form-fields/uploaded-file-html.php', [ 'key' => $key, 'name' => 'current_' . $field_name, 'value' => $file, 'field' => $field ] ) ?>
			<?php endforeach ?>
		</div>
	</div>

	<small class="description">
		<?php printf( _x( 'Maximum file size: %s.', 'Add listing form', 'my-listing' ), size_format( wp_max_upload_size() ) ); ?>
	</small>
</div>
