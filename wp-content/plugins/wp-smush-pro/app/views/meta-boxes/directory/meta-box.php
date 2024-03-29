<?php
/**
 * Directory Smush meta box.
 *
 * @package WP_Smush
 *
 * @var int    $errors       Number of errors during directory scan.
 * @var array  $images       Array of images with errors.
 * @var string $root_path    Root path.
 * @var string $upgrade_url  Upgrade URL.
 */

if ( ! defined( 'WPINC' ) ) {
	die;
}

?>

<?php wp_nonce_field( 'smush_get_dir_list', 'list_nonce' ); ?>
<?php wp_nonce_field( 'smush_get_image_list', 'image_list_nonce' ); ?>

<!-- Directory Path -->
<input type="hidden" class="wp-smush-dir-path" value="" />
<input type="hidden" name="wp-smush-base-path" value="<?php echo esc_attr( $root_path ); ?>" />

<div class="wp-smush-scan-result">
	<div class="content">
		<?php if ( ! $this->hide_wpmudev_branding() ) : ?>
			<span class="wp-smush-no-image tc">
				<img src="<?php echo esc_url( WP_SMUSH_URL . 'app/assets/images/smush-no-media.png' ); ?>" alt="<?php esc_html_e( 'Directory Smush - Choose Folder', 'wp-smushit' ); ?>">
			</span>
		<?php endif; ?>
		<p class="wp-smush-no-images-content tc roboto-regular">
			<?php esc_html_e( 'In addition to smushing your media uploads, you may want to also smush images living outside your uploads directory. Get started by adding files and folders you wish to optimize.', 'wp-smushit' ); ?>
		</p>
		<span class="wp-smush-upload-images sui-no-padding-bottom tc">
			<button type="button" class="sui-button sui-button-blue wp-smush-browse tc" data-a11y-dialog-show="wp-smush-list-dialog">
				<?php esc_html_e( 'CHOOSE DIRECTORY', 'wp-smushit' ); ?>
			</button>
		</span>
	</div>
	<!-- Notices -->
	<?php $this->smush_result_notice(); ?>
	<div class="sui-notice sui-notice-info wp-smush-dir-limit sui-hidden">
		<p>
			<?php
			printf(
				/* translators: %1$s: a tag start, %2$s: closing a tag */
				esc_html__( '%1$sUpgrade to pro%2$s to bulk smush all your directory images with one click. Free users can smush 50 images with each click.', 'wp-smushit' ),
				'<a href="' . esc_url( $upgrade_url ) . '" target="_blank" title="' . esc_html__( 'Smush Pro', 'wp-smushit' ) . '">',
				'</a>'
			);
			?>
		</p>
	</div>

	<?php if ( ! empty( $images ) ) : ?>
		<div class="smush-final-log">
			<div class="smush-bulk-errors">
				<?php foreach ( $images as $image ) : ?>
					<div class="smush-bulk-error-row">
						<div class="smush-bulk-image-data">
							<i class="sui-icon-photo-picture" aria-hidden="true"></i>
							<span class="smush-image-name"><?php echo esc_html( $image['path'] ); ?></span>
							<span class="smush-image-error"><?php echo esc_html( $image['error'] ); ?></span>
						</div>
					</div>
				<?php endforeach; ?>
			</div>
			<?php if ( $errors > 20 ) : ?>
				<p class="sui-description">
					<?php
					printf( /* translators: %d: number of images with errors */
						esc_html__( 'Showing 20 of %d failed optimizations. Fix or remove these images and run another Directory Smush.', 'wp-smushit' ),
						absint( $errors )
					);
					?>
				</p>
			<?php endif; ?>
		</div>
	<?php endif; ?>

	<?php wp_nonce_field( 'wp_smush_all', 'wp-smush-all' ); ?>
</div>

<?php
$screen = get_current_screen();
if ( ! empty( $screen ) && ! empty( $screen->base ) && ( 'toplevel_page_smush' === $screen->base || 'toplevel_page_smush-network' === $screen->base ) ) {
	$this->view( 'modals/directory-list' );
	$this->view( 'modals/progress-dialog' );
}