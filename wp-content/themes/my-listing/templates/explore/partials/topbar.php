<div class="explore-head">
	<?php if ( count( $explore->store['listing_types'] ) > 1 ): ?>
		<div class="explore-types">
			<div class="finder-title">
				<h2 class="case27-primary-text"><?php echo esc_html( $data['title'] ) ?></h2>
			</div>
			<?php foreach ( $explore->store['listing_types'] as $listing_type ): ?>
				<div class="type-<?php echo esc_attr( $listing_type->get_slug() ) ?>"
					 :class="state.activeListingType == '<?php echo esc_attr( $listing_type->get_slug() ) ?>'  ? 'active' : ''">
					<a @click.prevent="state.activeListingType = <?php echo c27()->encode_attr( $listing_type->get_slug() ) ?>; state.activeListingTypeData.name = <?php echo c27()->encode_attr( $listing_type->get_plural_name() ) ?>; state.activeListingTypeData.icon = '<?php echo esc_attr( $listing_type->get_setting( 'icon' ) ) ?>'; state.activeTab = 'search-form';">
						<div class="type-info">
							<i class="<?php echo esc_attr( $listing_type->get_setting( 'icon' ) ) ?>"></i>
							<h4><?php echo esc_html( $listing_type->get_plural_name() ) ?></h4>
						</div>
					</a>
				</div>
			<?php endforeach ?>
		</div>
	<?php endif ?>
</div>