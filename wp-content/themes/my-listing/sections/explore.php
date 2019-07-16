<?php

if ( ! class_exists( 'WP_Job_Manager' ) ) {
	return false;
}

/**
 * Explore page options.
 */
$data = c27()->merge_options([
	'title'    		 => '',
	'template' 		 => 'explore-default', // explore-default or explore-no-map
    'map_skin' 		 => 'skin1',
	'active_tab'     => 'listing-types',
    'categories'     => [
	    'count'      => 10,
	    'order'      => 'DESC',
	    'order_by'   => 'count',
	    'hide_empty' => true,
    ],
    'is_edit_mode'   => false,
    'scroll_to_results' => false,
    'scroll_wheel' => false,
    'disable_live_url_update' => false,
	'listing-wrap'   => '',
    'listing_types'  => [],
    'types_template' => 'topbar',
	'finder_columns' => 'finder-one-columns',
	'categories_overlay' => [
		'type' => 'gradient',
		'gradient' => 'gradient1',
		'solid_color' => 'rgba(0, 0, 0, .1)',
	],
], $data);

$GLOBALS['c27-explore'] = new MyListing\Src\Explore( $data );
$explore = &$GLOBALS['c27-explore'];

/*
 * Global variables.
 */
$GLOBALS['c27-facets-vue-object'] = [];

if ( ! in_array( $data['types_template'], ['topbar', 'dropdown'] ) ) {
	$data['types_template'] = 'topbar';
}


/*
 * The maximum number of columns for explore-2 template is "two". So, if the user sets
 * the option to "three" in Elementor settings, convert it to "two" columns.
 */
if ( $data['template'] == 'explore-2' && $data['finder_columns'] == 'finder-three-columns' ) {
	$data['finder_columns'] = 'finder-two-columns';
}
?>

<?php if (!$data['template'] || $data['template'] == 'explore-1' || $data['template'] == 'explore-2'): ?>
	<?php require locate_template( 'templates/explore/regular.php' ) ?>
<?php endif ?>

<?php if ($data['template'] == 'explore-no-map'): ?>
	<?php require locate_template( 'templates/explore/alternate.php' ) ?>
<?php endif ?>

<script type="text/javascript">
	var CASE27_Explore_Settings = {
		Facets: <?php echo json_encode( $GLOBALS['c27-facets-vue-object'] ) ?>,
		ListingWrap: <?php echo json_encode( $data['listing-wrap'] ) ?>,
		ActiveTab: <?php echo json_encode( $explore->get_active_tab() ) ?>,
		Taxonomies: <?php echo json_encode( $explore->get_taxonomy_data() ) ?>,
		ActiveMobileTab: <?php echo json_encode( $explore->get_active_mobile_tab() ) ?>,
		ScrollToResults: <?php echo json_encode( $data['scroll_to_results'] ) ?>,
		IsFirstLoad: true,
		DisableLiveUrlUpdate: <?php echo json_encode( $data['disable_live_url_update'] ) ?>,
		FieldAliases: <?php echo json_encode( array_flip( array_merge(
			\MyListing\Src\Listing::$aliases,
			[
				'date_from' => 'job_date_from',
				'date_to' => 'job_date_to',
				'lat' => 'search_location_lat',
				'lng' => 'search_location_lng',
			]
		) ) ) ?>,
	};

	<?php if ( $explore->get_active_listing_type() ): ?>
		CASE27_Explore_Settings.ActiveListingType = <?php echo json_encode( [
			'name' => $explore->get_active_listing_type()->get_plural_name(),
			'icon' => $explore->get_active_listing_type()->get_setting( 'icon' ),
			'slug' => $explore->get_active_listing_type()->get_slug(),
		] ) ?>;
	<?php else: ?>
		CASE27_Explore_Settings.ActiveListingType = <?php echo json_encode( [
			'name' => null,
			'icon' => null,
			'slug' => null,
		] ) ?>;
	<?php endif ?>
</script>


<?php if ($data['is_edit_mode']): ?>
    <script type="text/javascript">case27_ready_script(jQuery); MyListing.Explore__wrapper(); MyListing.Maps.init();</script>
<?php endif ?>