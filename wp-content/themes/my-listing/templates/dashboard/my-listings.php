<?php
/**
 * User listings dashboard page.
 *
 * @since 1.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$listings = array_map( function( $item ) {
	return \MyListing\Src\Listing::get( $item );
}, (array) $jobs );

$stats = mylisting()->stats()->get_user_stats( get_current_user_id() );
?>

<div class="row my-listings-stat-box">
	<?php
	mylisting_locate_template( 'templates/dashboard/stats/card.php', [
		'icon' => 'icon-window',
		'value' => number_format_i18n( absint( $stats->get( 'listings.published' ) ) ),
		'description' => _x( 'Published', 'Dashboard stats', 'my-listing' ),
		'background' => mylisting()->stats()->color_one,
	] );

	// Pending listing count (pending_approval + pending_payment).
	mylisting_locate_template( 'templates/dashboard/stats/card.php', [
		'icon' => 'mi info_outline',
		'value' => number_format_i18n( absint( $stats->get( 'listings.pending_approval' ) ) ),
		'description' => _x( 'Pending Approval', 'Dashboard stats', 'my-listing' ),
		'background' => mylisting()->stats()->color_two,
	] );

	// Promoted listing count.
	mylisting_locate_template( 'templates/dashboard/stats/card.php', [
		'icon' => 'mi info_outline',
		'value' => number_format_i18n( absint( $stats->get( 'listings.pending_payment' ) ) ),
		'description' => _x( 'Pending Payment', 'Dashboard stats', 'my-listing' ),
		'background' => mylisting()->stats()->color_three,
	] );

	// Recent views card.
	mylisting_locate_template( 'templates/dashboard/stats/card.php', [
		'icon' => 'mi timer',
		'value' => number_format_i18n( absint( $stats->get( 'listings.expired' ) ) ),
		'description' => _x( 'Expired', 'Dashboard stats', 'my-listing' ),
		'background' => mylisting()->stats()->color_four,
	] );
	?>
</div>

<div id="job-manager-job-dashboard">
	<?php if ( ! $listings ) : ?>
		<div class="no-listings">
			<i class="no-results-icon material-icons">mood_bad</i>
			<?php _e( 'You do not have any active listings.', 'my-listing' ); ?>
		</div>
	<?php else : ?>
		<table class="job-manager-jobs">
			<tbody>
			<?php foreach ( $listings as $listing ): ?>
				<tr>
					<td class="l-type">
						<div class="info listing-type">
							<div class="value">
								<?php echo $listing->type ? $listing->type->get_singular_name() : '&ndash;'; ?>
							</div>
						</div>
					</td>
					<td class="c27_listing_logo">
						<img src="<?php echo $listing->get_logo('thumbnail') ?: c27()->image( 'marker.jpg' ) ?>">
					</td>
					<td class="job_title">
						<?php if ( $listing->get_data('post_status') === 'publish' ) : ?>
							<a href="<?php echo esc_url( $listing->get_link() ) ?>"><?php echo esc_html( $listing->get_name() ) ?></a>
						<?php else : ?>
							<?php echo esc_html( $listing->get_name() ) ?><small>(<?php the_job_status( $listing->get_data() ); ?>)</small>
						<?php endif; ?>
					</td>
					<td class="listing-actions">
						<ul class="job-dashboard-actions">
							<?php
								$actions = array();

								switch ( $listing->get_data('post_status') ) {
									case 'publish' :
										$actions['edit'] = array( 'label' => __( 'Edit', 'my-listing' ), 'nonce' => false );
										$actions['duplicate'] = array( 'label' => __( 'Duplicate', 'my-listing' ), 'nonce' => true );
										break;
									case 'pending_payment' :
									case 'pending' :
										if ( job_manager_user_can_edit_pending_submissions() ) {
											$actions['edit'] = array( 'label' => __( 'Edit', 'my-listing' ), 'nonce' => false );
										}
									break;
								}

								$actions['delete'] = array( 'label' => __( 'Delete', 'my-listing' ), 'nonce' => true );
								$actions           = apply_filters( 'job_manager_my_job_actions', $actions, $listing->get_data() );

								foreach ( $actions as $action => $value ) {
									$value['type'] = ! empty( $value['type'] ) ? $value['type'] : 'link';

									if ( $value['type'] === 'plain' ) {
										if ( empty( $value['content'] ) ) {
											continue;
										}

										echo $value['content'];
									} else {
										$action_url = add_query_arg( array( 'action' => $action, 'job_id' => $listing->get_id() ) );
										if ( $value['nonce'] ) {
											$action_url = wp_nonce_url( $action_url, 'job_manager_my_job_actions' );
										}
										echo '<li><a href="' . esc_url( $action_url ) . '" class="job-dashboard-action-' . esc_attr( $action ) . '">' . esc_html( $value['label'] ) . '</a></li>';
									}
								}
							?>

							<?php do_action( 'mylisting/dashboard/listing-actions', $listing ) ?>
						</ul>
					</td>
					<td class="listing-info">
						<div class="info created-at">
							<div class="label"><?php _ex( 'Created:', 'User listings dashboard', 'my-listing' ) ?></div>
							<div class="value"><?php echo date_i18n( get_option( 'date_format' ), strtotime( $listing->get_data('post_date') ) ) ?></div>
						</div>
						<div class="info expires-at">
							<div class="label"><?php _ex( 'Expires:', 'User listings dashboard', 'my-listing' ) ?></div>
							<div class="value">
								<?php echo $listing->get_data('_job_expires') ? date_i18n( get_option( 'date_format' ), strtotime( $listing->get_data('_job_expires') ) ) : '&ndash;'; ?>
							</div>
						</div>
						<?php foreach ( $job_dashboard_columns as $key => $column ):
							if ( in_array( $key, [ 'job_title', 'listing_type', 'c27_listing_logo', 'filled', 'date', 'expires' ] ) ) {
								continue;
							}
							?>
							<div class="info <?php echo esc_attr( $key ) ?>">
								<?php do_action( 'job_manager_job_dashboard_column_' . $key, $listing->get_data() ); ?>
							</div>
						<?php endforeach; ?>
					</td>
				</tr>
			<?php endforeach ?>
			</tbody>
		</table>
	<?php endif ?>
	<?php get_job_manager_template( 'pagination.php', array( 'max_num_pages' => $max_num_pages ) ); ?>
</div>
