<?php
/**
 * Handle the "Sort By" dropdown in top of search results.
 *
 * @since 1.0.0
 */

$options = $type->get_ordering_options();
$value = ! empty( $_GET['sort'] ) ? $_GET['sort' ] : false;

// Select first option if no other value is provided via url.
if ( ( ! $value || ! in_array( $value, array_column( $options, 'key' ) ) ) && ! empty( $options[0] ) && ! empty( $options[0]['key'] ) ) {
    $value = $options[0]['key'];
}

$GLOBALS['c27-facets-vue-object'][ $type->get_slug() ]['sort'] = $value;
?>

<div v-show="state.activeListingType == '<?php echo esc_attr( $type->get_slug() ) ?>'" :class=" 'cts-explore-sort cts-sort-type-<?php echo esc_attr( $type->get_slug() ) ?>' ">
	<a class="toggle-rating" href="#" data-toggle="dropdown" aria-expanded="false"><i class="mi sort"></i>
		<?php foreach ( $options as $option ):
			$is_proximity_order = ! empty( $option['notes'] ) && in_array( 'has-proximity-clause', (array) $option['notes'] );
			$condition = sprintf( "facets['%s']['sort'] == %s", $type->get_slug(), json_encode( $option['key'] ) );
			?>
			<span v-show="<?php echo esc_attr( $condition ) ?>" class="<?php echo $is_proximity_order ? 'trigger-proximity-order' : '' ?>" :class="<?php echo esc_attr( $condition ) ?> ? 'selected' : ''">
				<?php echo esc_attr( $option['label'] ) ?>
			</span>
		<?php endforeach ?>
	</a>
	<ul class="i-dropdown dropdown-menu">
		<?php foreach ( $options as $option ):
			$is_proximity_order = ! empty( $option['notes'] ) && in_array( 'has-proximity-clause', (array) $option['notes'] );
			$method = $is_proximity_order ? 'getNearbyListings()' : '_getListings()';
			?>
			<li>
				<a href="#" @click.prevent="<?php echo esc_attr( sprintf( "facets['%s']['sort']", $type->get_slug() ) ) ?> = <?php echo esc_attr( json_encode( $option['key'] ) ) ?>; <?php echo esc_attr( $method ) ?>;">
					<?php echo esc_attr( $option['label'] ) ?>
				</a>
			</li>
		<?php endforeach ?>
	</ul>
</div>
