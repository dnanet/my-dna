<?php
/**
 * Term select field frontend template. If 'terms-template' option is provided,
 * display terms in the requested template. Default to 'multiselect' template.
 *
 * @since 1.5.1
 */

$listing_id = ! empty( $_REQUEST[ 'job_id' ] ) ? absint( $_REQUEST[ 'job_id' ] ) : 0;
$type_slug  = ! empty( $_GET['listing_type'] ) ? sanitize_text_field( $_GET['listing_type'] ) : false;
$type_id    = 0;

// In submit listing form, get the active listing type from the url.
if ( $type_slug && ( $type = get_page_by_path( $type_slug, OBJECT, 'case27_listing_type' ) ) ) {
	$type_id = $type->ID;
}

// In edit listing form, get the active listing type from the post meta.
if ( $listing_id && ( $type = get_page_by_path( get_post_meta( $listing_id, '_case27_listing_type', true ), OBJECT, 'case27_listing_type' ) ) ) {
	$type_id = $type->ID;
}

// Get selected terms, if it's edit page.
$selected = [];
if ( $listing_id ) {
	$selected = wp_get_object_terms( $listing_id, $field['taxonomy'], [ 'orderby' => 'term_order', 'order' => 'ASC' ] );
	if ( is_wp_error( $selected ) ) {
		return;
	}
}

if ( ! empty( $field['terms-template'] ) && ( $template = locate_job_manager_template( "form-fields/term-{$field['terms-template']}-field.php" ) ) ) {
	require $template;
} else {
	require locate_job_manager_template( 'form-fields/term-multiselect-field.php' );
}
