<?php
/**
 * Template for displaying explore page sidebar,
 * containing search tabs and filters.
 *
 * @var   $explore
 * @since 2.0
 */

if ( ! isset( $explore ) ) {
	return false;
}

if ( empty( $explore->store['listing_types'] ) || ! $explore->get_active_listing_type() ) {
	return false;
}

// Results page.
$pg = ! empty( $_GET['pg'] ) ? absint( $_GET['pg'] ) : 1;
?>

<div class="finder-tabs col-md-12 <?php echo count( $explore->store['listing_types'] ) > 1 ? 'with-listing-types' : 'without-listing-types' ?>">
	<ul class="nav nav-tabs tabs-menu" role="tablist">
		<li :class="state.activeTab == 'search-form' ? 'active' : ''" v-show="state.activeListingType">
			<a href="#search-form" role="tab" class="tab-switch" @click="state.activeTab = 'search-form'; getListings();">
				<i class="mi filter_list"></i><p><?php _e( 'Filters', 'my-listing' ) ?></p>
			</a>
		</li>

		<li :class="state.activeTab == 'categories' ? 'active' : ''">
			<a href="#categories" role="tab" class="tab-switch" @click="state.activeTab = 'categories'">
				<i class="material-icons">bookmark_border</i><p><?php _e( 'Categories', 'my-listing' ) ?></p>
			</a>
		</li>
	</ul>

	<div class="tab-content">

		<div id="search-form" class="listing-type-filters search-tab tab-pane fade" :class="state.activeTab == 'search-form' ? 'in active' : ''">

			<?php if ( $data['types_template'] === 'dropdown' ): ?>
				<?php require locate_template( 'templates/explore/partials/types-dropdown.php' ) ?>
			<?php endif ?>

			<?php foreach ($explore->store['listing_types'] as $type): ?>

				<?php $GLOBALS['c27-facets-vue-object'][ $type->get_slug() ] = []; ?>

				<div v-show="state.activeListingType == '<?php echo esc_attr( $type->get_slug() ) ?>'" class="search-filters type-<?php echo esc_attr( $type->get_slug() ) ?>">
					<div class="light-forms filter-wrapper">

						<?php foreach ((array) $type->get_search_filters() as $facet): ?>

							<?php if ( $facet['type'] == 'order' ): ?>
								<?php continue; ?>
							<?php endif ?>

							<?php c27()->get_partial( "facets/{$facet['type']}", [
								'facet' => $facet,
								'listing_type' => $type->get_slug(),
								'type' => $type,
							] ) ?>

						<?php endforeach ?>

						<?php $GLOBALS['c27-facets-vue-object'][ $type->get_slug() ]['page'] = ( $pg >= 1 ? $pg - 1 : 0 ); ?>

					</div>
					<div class="form-group fc-search">
						<a
							href="#"
							class="buttons button-2 full-width button-animated c27-explore-search-button"
							@click.prevent="state.mobileTab = 'results'; mobile.matches ? _getListings() : getListings(); _resultsScrollTop();"
						>
							<?php _e( 'Search', 'my-listing' ) ?><i class="mi keyboard_arrow_right"></i>
						</a>
					</div>
				</div>

			<?php endforeach ?>

		</div>

		<div id="categories" class="listing-cat-tab tab-pane fade c27-explore-categories" :class="state.activeTab == 'categories' ? 'in active' : ''">

			<?php foreach ((array) $explore->store['category-items'] as $term_type => $term_group): ?>

				<div v-show="<?php echo "'" . esc_attr( $term_type ) . "' == state.activeListingType" ?>">

					<?php foreach ($term_group as $term):
						$image = $term->get_image();
						// dump($term->get_data('listing_type'));
						?>

						<div class="listing-cat" :class="<?php echo esc_attr( $term->get_id() ) ?> == taxonomies.categories.term ? 'active' : ''">
							<a @click.prevent="taxonomies.categories.term = '<?php echo esc_attr( $term->get_id() ) ?>'; taxonomies.categories.page = 0; getListings();">
								<div class="overlay <?php echo $explore->get_data('categories_overlay')['type'] == 'gradient' ? esc_attr( $explore->get_data('categories_overlay')['gradient'] ) : '' ?>"
									style="<?php echo $explore->get_data('categories_overlay')['type'] == 'solid_color' ? 'background-color: ' . esc_attr( $explore->get_data('categories_overlay')['solid_color'] ) . '; ' : '' ?>"></div>
								<div class="lc-background" style="<?php echo is_array($image) && !empty($image) ? "background-image: url('" . esc_url( $image['sizes']['large'] ) . "');" : ''; ?>">
								</div>
								<div class="lc-info">
									<h4 class="case27-secondary-text"><?php echo esc_html( $term->get_name() ) ?></h4>
									<h6><?php echo esc_html( $term->get_count() ) ?></h6>
								</div>
								<div class="lc-icon">
									<?php echo $term->get_icon([ 'background' => false, 'color' => false ]); ?>
								</div>
							</a>
						</div>

					<?php endforeach ?>

				</div>

			<?php endforeach ?>

		</div>

		<?php foreach ( $explore->active_terms as $taxonomy => $term ): ?>
			<div class="listing-regions-tab tab-pabe fade c27-explore-<?php echo esc_attr( $taxonomy ) ?>" :class="state.activeTab == '<?php echo esc_attr( $taxonomy ) ?>' ? 'in active' : ''">
				<div class="searching-for">
					<?php echo $term->get_icon( [ 'background' => false, 'color' => false ] ) ?>
					<?php printf( '<p class="searching-for-text">' . __( 'Searching for listings in %s', 'my-listing' ) . '</p>', '</p><h1 class="filter-label">' . $term->get_name() . '</h1><p>' ) ?>
				</div>
			</div>
		<?php endforeach ?>

	</div>
</div>
