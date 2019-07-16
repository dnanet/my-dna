<?php
/**
 * Display listing priority settings in listing
 * edit page in wp-admin.
 *
 * @since 1.7.0
 *
 * @var   \MyListing\Src\Listing               $listing Current listing instance.
 * @var   \MyListing\Ext\Promotions\Promotions $this    The global promotions instance.
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) OR exit;

$priority = $listing->get_priority();
$is_custom_priority = false;
$package_label = '';

$choices = [
	[ 'value' => 0, 'label' => 'Normal' ],
	[ 'value' => 1, 'label' => 'Featured' ],
	[ 'value' => 2, 'label' => 'Promoted' ],
];

// Check if this listing has a custom priority value.
if ( ! in_array( $priority, array_column( $choices, 'value' ) ) ) {
	$is_custom_priority = true;
}

if ( ( $package = get_post( $listing->get_data( '_promo_package_id' ) ) ) && $package->post_type === 'cts_promo_package' ) {
	$package_label .= sprintf(
		'&ndash; <a href="%s" target="_blank">Package #%d</a>',
		esc_url( get_edit_post_link( $package ) ),
		$package->ID
	); ?>
	<script type="text/javascript">
		jQuery( function( $ ) {
			var confirmed = false;
			$( '.set-listing-priority .cts-radio-item' ).on( 'click', function(e) {
				if ( confirmed ) {
					return true;
				}

				confirmed = confirm( 'Modifying listing priority here will override the one set by package #<?php echo $package->ID ?>. Proceed anyway?' );
				return confirmed;
			});
		} );
	</script>
<?php } ?>

<div class="set-listing-priority cts-setting">
	<label for="cts-listing-priority">Priority <em><?php echo $package_label ?></em></label>
	<div class="cts-radio-list">

		<?php foreach ( $choices as $choice ): ?>
			<div class="cts-radio-item">
				<input
					type="radio"
					name="cts-listing-priority"
					id="cts-listing-priority-<?php echo esc_attr( $choice['value'] ) ?>"
					value="<?php echo esc_attr( $choice['value'] ) ?>"
					<?php checked( $choice['value'], $priority ) ?>
				>
				<label for="cts-listing-priority-<?php echo esc_attr( $choice['value'] ) ?>">
					<?php echo $choice['label'] ?>
					<i class="mi radio_button_unchecked"></i>
					<i class="mi radio_button_checked"></i>
				</label>
			</div>
		<?php endforeach ?>

		<div class="cts-radio-item custom-priority">
			<input
				type="radio"
				name="cts-listing-priority"
				id="cts-listing-priority-custom"
				value="custom"
				<?php checked( $is_custom_priority, true ) ?>
			>
			<label for="cts-listing-priority-custom">
				Custom priority <span class="cts-show-tip material-icons help_outline" data-tip="priority-docs" title="Click here to learn more about custom priority."></span></span>
				<input type="number" name="cts-listing-custom-priority" min="0" value="<?php echo esc_attr( $priority ) ?>">
			</label>
		</div>

	</div>
	<p class="description">
		Set what priority will be given to this listing in search results.
	</p>
	<?php if ( ! $this->active ): ?>
		<p class="description">
			<strong>You must <a href="<?php echo esc_url( admin_url( 'admin.php?page=theme-promotions-settings' ) ) ?>" target="_blank">enable listing promotions</a> to take advantage of this feature.</strong>
		</p>
	<?php endif ?>
</div>
