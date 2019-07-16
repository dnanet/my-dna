<?php
$data = c27()->merge_options([
    'facet' => '',
    'facetID' => uniqid() . '__facet',
    'listing_type' => '',
], $data);

if ( empty( $data['facet'] ) ) {
    return false;
}

if ( ! empty( $_GET[ $data['facet']['url_key'] ] ) ) {
    $value = $_GET[ $data['facet']['url_key'] ];
} elseif ( ! empty( $_GET[ $data['facet']['show_field'] ] ) ) {
    $value = $_GET[ $data['facet']['show_field'] ];
} else {
    $value = '';
}

$GLOBALS['c27-facets-vue-object'][$data['listing_type']][$data['facet']['show_field']] = $value;

$placeholder = ! empty( $data['facet']['placeholder'] ) ? $data['facet']['placeholder'] : false;
?>

<div class="form-group explore-filter <?php echo esc_attr( ! $placeholder ? 'md-group' : '' ) ?> text-filter <?php echo esc_attr( trim( $value ) ? 'md-active' : '' ) ?>">
	<input type="text"
		   id="<?php echo esc_attr( $data['facetID'] ) ?>"
		   name="<?php echo esc_attr( $data['facet']['url_key'] ) ?>"
		   v-model="facets['<?php echo esc_attr( $data['listing_type'] ) ?>']['<?php echo esc_attr( $data['facet']['show_field'] ) ?>']"
		   placeholder="<?php echo esc_attr( $placeholder ) ?>"
		   @keyup="getListings"
		   >
	<label for="<?php echo esc_attr( $data['facetID'] ) ?>"><?php echo esc_html( $data['facet']['label'] ) ?></label>
    <div class="md-border-line"></div>
</div>