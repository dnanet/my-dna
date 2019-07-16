<?php
/**
 * Claim Listing Package Selection Step.
 *
 * @since 1.0.0
 *
 * @var array $packages       Product list. Each item is MyListing\Ext\Paid_Listings\Product object.
 * @var int   $packages_count Packages `$packages` count.
 * @var array $user_packages  User package list. Each item is MyListing\Ext\Paid_Listings\Package object.
 */
?>

<?php if ( $packages || $user_packages ) : ?>

	<?php
	$checked = 1;
	$selected = isset( $_GET['selected_package'] ) ? absint( $_GET['selected_package'] ) : null;
	?>

	<?php if ( $packages && is_array( $packages ) ) : ?>

		<div class="row section-body">

			<?php if ( 1 === $packages_count ): ?>
				<div class="col-md-4 col-sm-3 hidden-xs"></div>
			<?php elseif ( 2 === $packages_count ): ?>
				<div class="col-md-2 hidden-sm hidden-xs"></div>
			<?php endif; ?>

			<?php foreach ( $packages as $key => $product ) : ?>

				<?php
				// Skip if not the right product type or not purchaseable.
				if ( ! $product->is_type( array( 'job_package', 'job_package_subscription' ) ) ) {
					continue;
				}

				$owned_packages = [];
				if ( ! empty( $user_packages ) && is_array( $user_packages ) ) {
					// Get list of owned packages for this product.
					$owned_packages = array_filter( $user_packages, function( $pckg ) use ( $product ) {
						return absint( $pckg->get_product_id() ) === absint( $product->get_id() );
					} );

					// Unset these owned packages from the global owned packages.
					$user_packages = array_filter( $user_packages, function( $pckg ) use ( $owned_packages ) {
						return ! isset( $owned_packages[ $pckg->get_id() ] );
					} );
				}


				$title = $product->get_name();
				$description = $product->get_description();
				$featured = false;

				// If a custom title, description, or other options are set on this product
				// for this specific listing type, then replace the default ones with the custom one.
				if ( $type && ( $_package = $type->get_package( $product->get_id() ) ) ) {
					$title = $_package['label'] ?: $title;
					$featured = $_package['featured'] ?: $featured;

					// Split the description textarea into new lines,
					// so it can later be reconstructed to an html list.
					$description = $_package['description'] ? preg_split( '/\r\n|[\r\n]/', $_package['description'] ) : $description;
				}


				// Set checked item.
				$checked = ( intval( $selected ) === intval( $product->get_id() ) ) ? 1 : 0;

				$_product_image = get_field( 'pricing_plan_image', $product->get_id() );
				if ( is_array( $_product_image ) && ! empty( $_product_image['sizes'] ) && ! empty( $_product_image['sizes']['large'] ) ) {
					$product_image = $_product_image['sizes']['large'];
				} else {
					$product_image = false;
				}
				?>

				<div class="col-md-4 col-sm-6 col-xs-12 reveal">
					<div class="pricing-item c27-pick-package cts-claim-package <?php echo $checked ? 'c27-picked' : ''; ?> <?php echo $featured ? 'featured' : '' ?> <?php echo ! $product->is_purchasable() ? 'not-purchasable' : '' ?>">
						<?php if ( $featured ): ?>
							<div class="featured-plan-badge">
								<span class="icon-flash"></span>
							</div>
						<?php endif ?>
						<h2 class="plan-name"><?php echo $title ?></h2>

						<?php if ( $product_image ): ?>
							<img src="<?php echo esc_url( $product_image ) ?>" class="plan-image">
						<?php endif ?>

						<h2 class="plan-price case27-primary-text"><?php echo $product->get_price_html(); ?></h2>
						<p class="plan-desc"><?php echo $product->get_short_description(); ?></p>
						<div class="plan-features">
							<?php if ( is_array( $description ) ): ?>
								<ul>
									<?php foreach ( $description as $line ): ?>
										<li><?php echo $line ?></li>
									<?php endforeach ?>
								</ul>
							<?php else: ?>
								<?php echo $description ?>
							<?php endif ?>
						</div>
						<div class="select-package">

							<?php if ( $owned_packages ): ?>
								<div class="package-available dropup">
									<a type="button" class="use-package-toggle dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
										<?php _e( 'You already own this package', 'my-listing' ) ?>
										<i class="mi arrow_drop_up"></i>
									</a>

									<div class="dropdown-menu">
										<ul class="checkbox-plan-list owned-product-packages">
											<?php foreach ( $owned_packages as $pckg ): ?>
												<li>
													<a href="<?php echo esc_url( add_query_arg( array( 'listing_id' => $listing_id, '_package_id' => $pckg->get_id() ), get_permalink() ) ); ?>">
														<div class="md-checkbox">
															<i class="mi check"></i>
														</div>
														<label for="user-package-<?php echo esc_attr( $pckg->get_id() ) ?>" class="checkbox-plan-name"><?php echo $title ?></label>
														<p class="checkbox-plan-desc">
														<?php
														if ( $pckg->get_limit() ) {
															printf( _n( '%s listing posted out of %d', '%s listings posted out of %d', $pckg->get_count(), 'my-listing' ), $pckg->get_count(), $pckg->get_limit() );
														} else {
															printf( _n( '%s listing posted', '%s listings posted', $pckg->get_count(), 'my-listing' ), $pckg->get_count() );
														}

														if ( $pckg->get_duration() ) {
															printf(  ', ' . _n( 'listed for %s day', 'listed for %s days', $pckg->get_duration(), 'my-listing' ), $pckg->get_duration() );
														}
														?>
														</p>
													</a>
												</li>
											<?php endforeach ?>

											<?php if ( $product->is_purchasable() ): ?>
												<li>
													<a class="buttons button-5" href="<?php echo esc_url( add_query_arg( array( 'listing_id' => $listing_id, '_product_id' => $product->get_id() ), get_permalink() ) ); ?>">
														<?php _e( 'Or buy new', 'my-listing' ) ?><i class="mi arrow_forward"></i>
													</a>
												</li>
											<?php else: ?>
												<li class="purchase-disabled"><p><?php _e( 'This item can only be purchased once.', 'my-listing' ) ?></p></li>
											<?php endif ?>
										</ul>
									</div>
								</div>
							<?php endif ?>

							<?php if ( $owned_packages ): ?>
								<a class="select-plan buttons button-2" href="<?php echo esc_url( add_query_arg( array( 'listing_id' => $listing_id, '_package_id' => array_values( $owned_packages )[0]->get_id() ), get_permalink() ) ); ?>">
									<?php _e( 'Use Available Package', 'my-listing' ); ?>
									<i class="mi arrow_forward"></i>
								</a>
							<?php else: ?>
								<?php if ( $product->is_purchasable() ): ?>
									<a class="select-plan buttons button-2" href="<?php echo esc_url( add_query_arg( array( 'listing_id' => $listing_id, '_product_id' => $product->get_id() ), get_permalink() ) ); ?>">
										<?php _e( 'Buy Package', 'my-listing' ); ?>
										<i class="mi arrow_forward"></i>
									</a>
								<?php else: ?>
									<p class="purchase-disabled"><?php _e( 'This item can only be purchased once.', 'my-listing' ) ?></p>
								<?php endif ?>
							<?php endif ?>
						</div>
					</div><!-- .pricing-item -->
				</div><!-- .reveal -->

			<?php endforeach; ?>

		</div><!-- .section-body -->

	<?php endif; ?>

<?php endif; ?>

<style type="text/css">
	.elementor:not(.elementor-type-header):not(.elementor-type-footer) .elementor-widget:not(.elementor-widget-case27-add-listing-widget) { display: none !important; }
	.elementor:not(.elementor-type-header):not(.elementor-type-footer) .elementor-container { max-width: 100% !important; }
	.elementor:not(.elementor-type-header):not(.elementor-type-footer) .elementor-column-wrap { padding: 0 !important; }
</style>
