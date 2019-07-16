<?php
/**
 * Dropdown for switching between listing types in Explore page.
 *
 * @since 2.0
 */

if ( ! defined('ABSPATH') ) {
    exit;
}

if ( count( $explore->store['listing_types'] ) <= 1 ) {
    return;
}
?>
<div class="form-group explore-filter md-group dropdown-filter listing-types-dropdown">
    <select @select:change="state.activeListingType = $event.detail.value" required="true" class="custom-select">
        <?php foreach ( $explore->store['listing_types'] as $type ): ?>
            <option value="<?php echo esc_attr( $type->get_slug() ) ?>" <?php selected( $explore->get_active_listing_type()->get_slug(), $type->get_slug() ) ?>>
                <?php echo esc_attr( $type->get_plural_name() ) ?>
            </option>
        <?php endforeach ?>
    </select>
    <label><?php _ex( 'Listing Type', 'Explore page > Listing types dropdown', 'my-listing' ) ?></label>
</div>
