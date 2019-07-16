<?php
if ( is_numeric( $value ) ) {
	$image_src = wp_get_attachment_image_src( absint( $value ) );
	$image_src = $image_src ? $image_src[0] : '';
} else {
	$image_src = $value;
}

$extension = ! empty( $extension ) ? $extension : substr( strrchr( $image_src, '.' ), 1 );
$is_image = in_array( $extension, array( 'jpg', 'gif', 'png', 'jpeg', 'jpe' ) ); ?>

<div class="uploaded-file <?php echo $is_image ? 'uploaded-image' : '' ?> review-gallery-image job-manager-uploaded-file">
	<span class="uploaded-file-preview">
		<?php if ( $is_image ): ?>
			<span class="job-manager-uploaded-file-preview">
				<img src="<?php echo esc_url( job_manager_get_resized_image( $image_src, 'medium' ) ?: $image_src ) ?>">
			</span>
		<?php else: ?>
			<span class="job-manager-uploaded-file-name">
				<i class="mi insert_drive_file uploaded-file-icon"></i>
				<code><?php echo esc_html( basename( $image_src ) ) ?></code>
			</span>
		<?php endif ?>

		<a class="remove-uploaded-file review-gallery-image-remove job-manager-remove-uploaded-file"><i class="mi delete"></i></a>
	</span>
	<input type="hidden" class="input-text" name="<?php echo esc_attr( $name ) ?>" value="<?php echo esc_attr( $value ) ?>">
</div>
