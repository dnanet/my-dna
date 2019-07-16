<?php
$data = c27()->merge_options( [
    'facet' => '',
    'options' => [
    	'count' => 8,
    	'multiselect' => false,
    	'hide_empty' => true,
        'order_by' => 'count',
    	'order' => 'DESC',
        'placeholder' => __( 'Select an option', 'my-listing' ),
    ],
    'facet_data' => [
    	'choices' => [],
    ],
    'is_vue_template' => true,
], $data );

$type = $data['type'];
$facet = $data['facet'];
$fieldkey = sprintf( 'facets["%s"]["%s"]', $data['listing_type'], $facet['show_field'] );

$facet_show_field = $facet['show_field'];
if ( $facet_show_field == 'job_category' ) {
    $facet_show_field = 'category';
} elseif ( $facet_show_field == 'job_tags' ) {
    $facet_show_field = 'tag';
}

if ( ! empty( $_GET[$facet['url_key']] ) ) {
    $selected = (array) $_GET[$facet['url_key']];
} elseif ( ! empty( $_GET[$facet['show_field']] ) ) {
    $selected = (array) $_GET[$facet['show_field']];
} elseif ( ( $selected_val = get_query_var( sprintf( 'explore_%s', $facet_show_field ) ) ) ) {
    $selected = (array) $selected_val;
} else {
    $selected = [];
}

$GLOBALS['c27-facets-vue-object'][$data['listing_type']][$facet['show_field']] = $selected;

if ( ! $facet || ! ( $field = $type->get_field( $facet[ 'show_field' ] ) ) ) {
    return;
}

foreach( (array) $facet['options'] as $option ) {
    if ( isset( $data['options'][ $option['name'] ] ) ) {
        $data['options'][ $option['name'] ] = $option['value'];
    }
}

if ( ! $data['is_vue_template'] ) {
    $data['options']['multiselect'] = false;
}

$placeholder = ! empty( $data['options']['placeholder'] ) ? $data['options']['placeholder'] : false;

if ( ! empty( $field['taxonomy'] ) && taxonomy_exists( $field['taxonomy'] ) ) {
    /* @todo: orderby, order, hide_empty params in term-list.php query. */
    $selected_terms = ! empty( $selected ) ? get_terms( [
            'taxonomy' => $field['taxonomy'],
            'hide_empty' => false,
            'slug' => $selected,
    ] ) : [];

    if ( is_wp_error( $selected_terms ) ) {
        $selected_terms = [];
    }

    $is_single = ! $data['options']['multiselect'];
    $ajax_params = [
        'taxonomy' => $field['taxonomy'],
        'listing-type-id' => $type->get_id(),
        'orderby' => $data['options']['order_by'],
        'order' => $data['options']['order'],
        'hide_empty' => $data['options']['hide_empty'],
        'term-value' => 'slug',
    ];
    if ( $is_single && $data['is_vue_template'] ) {
        $ajax_params['parent'] = 0;
    }
    ?>
    <div class="form-group explore-filter <?php echo esc_attr( ! $placeholder ? 'md-group' : '' ) ?> dropdown-filter <?php echo esc_attr( ! empty( $selected ) ? 'md-active' : '' ) ?> <?php echo ! $is_single ? 'dropdown-filter-multiselect' : 'terms-filter-single' ?>">

        <div class="main-term">
            <select
                <?php echo ! $is_single ? 'multiple="multiple"' : '' ?>
                <?php printf( 'placeholder="%s"', esc_attr( $placeholder ?: " " ) ) ?>
                class="custom-select"
                name="<?php echo esc_attr( $facet['url_key'] ) . ( $is_single ? '' : '[]' )?>"
                <?php echo $is_single
                    ? sprintf( '@select:change="_handleTermSelect($event, %s, %s)"', esc_attr( json_encode( $facet['show_field'] ) ), esc_attr( json_encode( $data['listing_type'] ) ) )
                    : sprintf( '@select:change="%s = $event.detail.value; getListings();"', esc_attr( $fieldkey ) ) ?>
                data-mylisting-ajax="true"
                data-mylisting-ajax-url="mylisting_list_terms"
                data-mylisting-ajax-params="<?php echo c27()->encode_attr( $ajax_params ) ?>"
            >
                <option></option>
                <?php foreach ( (array) $selected_terms as $term ):
                    if ( ! $term instanceof \WP_Term ) continue; ?>
                    <option value="<?php echo esc_attr( $term->slug ) ?>" selected="selected">
                        <?php echo esc_attr( $term->name ) ?>
                    </option>
                <?php endforeach ?>
            </select>
            <label><?php echo esc_html( $facet['label'] ) ?></label>
        </div>
    </div>

    <?php if ( $is_single && $data['is_vue_template'] ): ?>
        <div class="child-terms" v-pre></div>
    <?php endif ?>
    <?php
    return;
}

if ( $data['options']['order_by'] == 'include' ) {
    if ( $data['options']['order'] == 'DESC' ) {
        $field['options'] = array_reverse( (array) $field['options'] );
    }

    if ( is_numeric( $data['options']['count'] ) && $data['options']['count'] >= 1 ) {
        $field['options'] = array_slice( (array) $field['options'], 0, $data['options']['count'] );
    }

    foreach ( (array) $field['options'] as $option ) {
        $data['facet_data']['choices'][] = [
            'value' => $option,
            'label' => $option,
            'selected' => false,
        ];
    }
} else {
    // dump($facet, $field);
    if (!function_exists('c27_dropdown_facet_query_group_by_filter')) {
        function c27_dropdown_facet_query_group_by_filter( $groupby ) { global $wpdb;
            return $wpdb->postmeta . '.meta_value ';
        }
    }

    if (!function_exists('c27_dropdown_facet_query_fields_filter')) {
        function c27_dropdown_facet_query_fields_filter( $fields ) { global $wpdb;
            return $wpdb->postmeta . '.meta_value ';
        }
    }

    add_filter('posts_fields', 'c27_dropdown_facet_query_fields_filter');
    add_filter('posts_groupby', 'c27_dropdown_facet_query_group_by_filter');

	$posts = query_posts( [
		'post_type' => 'job_listing',
		'posts_per_page' => $data['options']['count'],
        'orderby' => $data['options']['order_by'],
        'order' => $data['options']['order'],
        'meta_query' => [
            ['key' => "_{$facet['show_field']}"],
            ['key' => '_case27_listing_type', 'value' => $type->get_slug()],
        ],
	] );

    remove_filter('posts_fields', 'c27_dropdown_facet_query_fields_filter');
    remove_filter('posts_groupby', 'c27_dropdown_facet_query_group_by_filter');

	foreach ((array) $posts as $post) {
        if ( empty( $post->meta_value ) ) {
            continue;
        }

        if ( is_serialized( $post->meta_value ) ) {
            foreach ( array_filter( (array) unserialize( $post->meta_value ) ) as $value ) {
                $data['facet_data']['choices'][] = [
                    'value' => $value,
                    'label' => $value,
                    'selected' => false,
                ];
            }

            continue;
        }

		$data['facet_data']['choices'][] = [
			'value' => $post->meta_value,
            'label' => "{$post->meta_value}",
			'selected' => false,
		];
	}

    $data['facet_data']['choices'] = array_map( 'unserialize', array_unique( array_map( 'serialize', $data['facet_data']['choices'] ) ) );
}

$choices_flat = (array) array_column( $data['facet_data']['choices'], 'value' );
$selected = array_filter( array_filter( $selected, function( $value ) use ( $choices_flat ) {
    return in_array( $value, $choices_flat );
} ) );

$GLOBALS['c27-facets-vue-object'][$data['listing_type']][$facet['show_field']] = $selected;
?>

<div class="form-group explore-filter <?php echo esc_attr( ! $placeholder ? 'md-group' : '' ) ?> dropdown-filter <?php echo esc_attr( ! empty( $selected ) ? 'md-active' : '' ) ?> <?php echo $data['options']['multiselect'] ? 'dropdown-filter-multiselect' : '' ?>">
    <?php if ($data['is_vue_template']): ?>
        <select
            @select:change="<?php echo esc_attr( $fieldkey ) ?> = $event.detail.value; getListings();"
            class="custom-select"
            <?php echo $data['options']['multiselect'] ? 'multiple="multiple"' : '' ?>
            <?php printf( 'placeholder="%s"', esc_attr( $placeholder ?: " " ) ) ?>
        >
            <option></option>
            <?php foreach ( $data['facet_data']['choices'] as $choice ): ?>
                <option value="<?php echo esc_attr( $choice['value'] ) ?>" <?php selected( in_array( $choice['value'], $selected ), true ) ?>>
                    <?php echo esc_attr( $choice['label'] ) ?>
                </option>
            <?php endforeach ?>
        </select>
    <?php else: ?>
        <select name="<?php echo esc_attr( $facet['url_key'] ) ?>[]"
                placeholder="<?php echo esc_attr( $data['options']['placeholder'] ) ?>" class="custom-select">
                <option></option>
            <?php foreach ($data['facet_data']['choices'] as $choice): ?>
                <option value="<?php echo esc_attr( $choice['value'] ) ?>"><?php echo esc_html( $choice['label'] ) ?></option>
            <?php endforeach ?>
        </select>
    <?php endif ?>

    <label><?php echo esc_html( $facet['label'] ) ?></label>
</div>