<?php
$data = c27()->merge_options([
    'facet' => '',
    'facetID' => uniqid() . '__facet',
    'listing_type' => '',
    'options' => [
        'type' => 'range',
        'format' => 'ymd',
    ],
    'is_vue_template' => true,
], $data);

if (!$data['facet']) return;

foreach((array) $data['facet']['options'] as $option) {
    if ($option['name'] == 'type') $data['options']['type'] = $option['value'];
    if ($option['name'] == 'format') $data['options']['format'] = $option['value'];
}

// Exact date value.
if ( ! empty( $_GET[ $data['facet']['url_key'] ] ) ) {
    $exact_date = $_GET[ $data['facet']['url_key'] ];
} elseif ( ! empty( $_GET[ $data['facet']['show_field'] ] ) ) {
    $exact_date = $_GET[ $data['facet']['show_field'] ];
} else {
    $exact_date = '';
}

// From date value.
if ( ! empty( $_GET[ $data['facet']['url_key'] . '_from' ] ) ) {
    $from_date = $_GET[ $data['facet']['url_key'] . '_from' ];
} elseif ( ! empty( $_GET[ $data['facet']['show_field'] . '_from' ] ) ) {
    $from_date = $_GET[ $data['facet']['show_field'] . '_from' ];
} else {
    $from_date = '';
}

// To date value.
if ( ! empty( $_GET[ $data['facet']['url_key'] . '_to' ] ) ) {
    $to_date = $_GET[ $data['facet']['url_key'] . '_to' ];
} elseif ( ! empty( $_GET[ $data['facet']['show_field'] . '_to' ] ) ) {
    $to_date = $_GET[ $data['facet']['show_field'] . '_to' ];
} else {
    $to_date = '';
}

$field_key = sprintf( 'facets["%s"]["%s%s"]', $data['listing_type'], $data['facet']['show_field'], '' );
$from_key = sprintf( 'facets["%s"]["%s%s"]', $data['listing_type'], $data['facet']['show_field'], '_from' );
$to_key = sprintf( 'facets["%s"]["%s%s"]', $data['listing_type'], $data['facet']['show_field'], '_to' );
?>

<?php if ($data['options']['format'] == 'ymd'): ?>
    <?php if ($data['options']['type'] == 'range'): ?>
        <?php $GLOBALS['c27-facets-vue-object'][$data['listing_type']]["{$data['facet']['show_field']}_from"] = $from_date; ?>
        <?php $GLOBALS['c27-facets-vue-object'][$data['listing_type']]["{$data['facet']['show_field']}_to"] = $to_date; ?>
        <div class="form-group explore-filter double-input datepicker-form-group">
            <label for="<?php echo esc_attr( $data['facetID'] ) ?>"><?php echo esc_html( $data['facet']['label'] ) ?></label>
            <div class="datepicker-wrapper">
                <input
                    type="text" class="mylisting-datepicker"
                    placeholder="<?php esc_attr_e( 'From...', 'my-listing' ) ?>"
                    name="<?php echo esc_attr( sprintf( '%s_from', $data['facet']['url_key'] ) ) ?>"
                    :value="<?php echo esc_attr( $from_key ) ?>"
                    @datepicker:change="<?php echo esc_attr( $from_key ) ?> = $event.detail.value; getListings();"
                >
            </div>
            <div class="datepicker-wrapper">
                <input
                    type="text" class="mylisting-datepicker"
                    placeholder="<?php esc_attr_e( 'To...', 'my-listing' ) ?>"
                    name="<?php echo esc_attr( sprintf( '%s_to', $data['facet']['url_key'] ) ) ?>"
                    :value="<?php echo esc_attr( $to_key ) ?>"
                    @datepicker:change="<?php echo esc_attr( $to_key ) ?> = $event.detail.value; getListings();"
                >
            </div>
        </div>
    <?php endif ?>

    <?php if ($data['options']['type'] == 'exact'): ?>
        <?php $GLOBALS['c27-facets-vue-object'][$data['listing_type']][$data['facet']['show_field']] = $exact_date; ?>
        <div class="form-group explore-filter datepicker-form-group">
            <label><?php echo esc_html( $data['facet']['label'] ) ?></label>
            <div class="datepicker-wrapper">
                <input
                    type="text" class="mylisting-datepicker"
                    placeholder="<?php esc_attr_e( 'Pick a date...', 'my-listing' ) ?>"
                    name="<?php echo esc_attr( $data['facet']['url_key'] ) ?>"
                    :value="<?php echo esc_attr( $field_key ) ?>"
                    @datepicker:change="<?php echo esc_attr( $field_key ) ?> = $event.detail.value; getListings();"
                >
            </div>
        </div>
    <?php endif ?>
<?php endif ?>

<?php if ($data['options']['format'] == 'year'): ?>
    <?php

    if (!function_exists('query_group_by_filter_2')) {
        function query_group_by_filter_2($groupby) { global $wpdb;
            return 'c27_year ';
        }
    }

    if (!function_exists('query_fields_filter_2')) {
        function query_fields_filter_2($fields) { global $wpdb;
            return "{$fields}, year({$wpdb->postmeta}.meta_value) as c27_year ";
        }
    }

    add_filter('posts_fields', 'query_fields_filter_2');
    add_filter('posts_groupby', 'query_group_by_filter_2');

    $listing_years = query_posts([
        'post_type' => 'job_listing',
        'posts_per_page' => -1,
        'meta_key'  => "_{$data['facet']['show_field']}",
        'orderby'   => 'meta_value',
        'order' => 'DESC',
        ]);

    $choices = [];

    foreach ($listing_years as $year) {
        if ($year->c27_year) {
            $choices[] = [
            'value' => $year->c27_year,
            'label' => $year->c27_year,
            ];
        }
    }

    remove_filter('posts_fields', 'query_fields_filter_2');
    remove_filter('posts_groupby', 'query_group_by_filter_2');
    ?>

    <?php if ($data['is_vue_template']): ?>
        <?php if ($data['options']['type'] == 'range'): ?>
            <?php $GLOBALS['c27-facets-vue-object'][$data['listing_type']]["{$data['facet']['show_field']}_from"] = $from_date; ?>
            <?php $GLOBALS['c27-facets-vue-object'][$data['listing_type']]["{$data['facet']['show_field']}_to"] = $to_date; ?>
            <div class="form-group explore-filter double-input">
                <label><?php echo esc_attr( $data['facet']['label'] ) ?></label>

                <select
                    class="custom-select"
                    placeholder="<?php echo esc_attr( _x( 'From...', 'Explore page > Date filter', 'my-listing' ) ) ?>"
                    @select:change="<?php echo esc_attr( $from_key ) ?> = $event.detail.value; getListings();"
                >
                    <option></option>
                    <?php foreach ( $choices as $choice ): ?>
                        <option value="<?php echo esc_attr( $choice['value'] ) ?>" <?php selected( $choice['value'], $from_date ) ?>>
                            <?php echo esc_attr( $choice['label'] ) ?>
                        </option>
                    <?php endforeach ?>
                </select>

                <select
                    class="custom-select"
                    placeholder="<?php echo esc_attr( _x( 'To...', 'Explore page > Date filter', 'my-listing' ) ) ?>"
                    @select:change="<?php echo esc_attr( $to_key ) ?> = $event.detail.value; getListings();"
                >
                    <option></option>
                    <?php foreach ( $choices as $choice ): ?>
                        <option value="<?php echo esc_attr( $choice['value'] ) ?>" <?php selected( $choice['value'], $to_date ) ?>>
                            <?php echo esc_attr( $choice['label'] ) ?>
                        </option>
                    <?php endforeach ?>
                </select>
            </div>
        <?php endif ?>

        <?php if ($data['options']['type'] == 'exact'): ?>
            <?php $GLOBALS['c27-facets-vue-object'][$data['listing_type']][$data['facet']['show_field']] = $exact_date; ?>
            <div class="form-group explore-filter">
                <label for="<?php echo esc_attr( $data['facetID'] ) ?>"><?php echo esc_html( $data['facet']['label'] ) ?></label>

                <select
                    class="custom-select"
                    placeholder="<?php echo esc_attr( _x( 'Choose year...', 'Explore page > Date filter', 'my-listing' ) ) ?>"
                    @select:change="<?php echo esc_attr( $field_key ) ?> = $event.detail.value; getListings();"
                >
                    <option></option>
                    <?php foreach ( $choices as $choice ): ?>
                        <option value="<?php echo esc_attr( $choice['value'] ) ?>" <?php selected( $choice['value'], $exact_date ) ?>>
                            <?php echo esc_attr( $choice['label'] ) ?>
                        </option>
                    <?php endforeach ?>
                </select>
            </div>
        <?php endif ?>
    <?php else: ?>
        <?php if ($data['options']['type'] == 'range'): ?>
            <div class="form-group explore-filter double-input">
                <label><?php echo esc_attr( $data['facet']['label'] ) ?></label>
                <select name="<?php echo esc_attr( "{$data['facet']['url_key']}_from" ) ?>" class="custom-select">
                    <option value=""><?php _e( 'From...', 'my-listing' ) ?></option>
                    <?php foreach ($choices as $choice): ?>
                        <option value="<?php echo esc_attr( $choice['value'] ) ?>"><?php echo esc_html( $choice['label'] ) ?></option>
                    <?php endforeach ?>
                </select>

                <select name="<?php echo esc_attr( "{$data['facet']['url_key']}_to" ) ?>" class="custom-select">
                    <option value=""><?php _e( 'To...', 'my-listing' ) ?></option>
                    <?php foreach ($choices as $choice): ?>
                        <option value="<?php echo esc_attr( $choice['value'] ) ?>"><?php echo esc_html( $choice['label'] ) ?></option>
                    <?php endforeach ?>
                </select>
            </div>
        <?php endif ?>

        <?php if ($data['options']['type'] == 'exact'): ?>
            <?php $GLOBALS['c27-facets-vue-object'][$data['listing_type']][$data['facet']['show_field']] = $exact_date; ?>
            <div class="form-group explore-filter">
                <label for="<?php echo esc_attr( $data['facetID'] ) ?>"><?php echo esc_html( $data['facet']['label'] ) ?></label>
                <select name="<?php echo esc_attr( $data['facet']['url_key'] ) ?>" class="custom-select">
                    <option value=""><?php _e( 'Choose year...', 'my-listing' ) ?></option>
                    <?php foreach ($choices as $choice): ?>
                        <option value="<?php echo esc_attr( $choice['value'] ) ?>"><?php echo esc_html( $choice['label'] ) ?></option>
                    <?php endforeach ?>
                </select>
            </div>
        <?php endif ?>
    <?php endif ?>
<?php endif ?>
