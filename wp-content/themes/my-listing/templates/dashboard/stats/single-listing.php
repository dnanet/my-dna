<?php
/**
 * Display stats for a single listing in user dashboard.
 *
 * @since 2.0
 */
if ( ! defined('ABSPATH') ) {
	exit;
}

// Get selected listing stats.
$stats = mylisting()->stats()->get_listing_stats( $listing->get_id() );
?>

<div class="row">
	<div class="col-md-9 mlduo-welcome-message">
		<h1>
			<?php printf(
				_x( '"%s" &mdash; Statistics', 'User dashboard', 'my-listing' ),
				$listing->get_name()
			) ?>
		</h1>
	</div>
	<div class="col-md-3">
		<?php require locate_template( 'templates/dashboard/stats/select-listing.php' ) ?>
	</div>
</div>


<div class="row">
	<div class="col-md-4">
		<?php if ( c27()->get_setting( 'stats_views_section_enabled', true ) !== false ): ?>
			<?php require locate_template( 'templates/dashboard/stats/widgets/views.php' ) ?>
		<?php endif ?>

		<?php if ( c27()->get_setting( 'stats_unique_views_section_enabled', true ) !== false ): ?>
			<?php require locate_template( 'templates/dashboard/stats/widgets/unique-views.php' ) ?>
		<?php endif ?>

		<?php if ( c27()->get_setting( 'stats_devices_enabled', true ) !== false ): ?>
			<?php require locate_template( 'templates/dashboard/stats/widgets/devices.php' ) ?>
		<?php endif ?>

		<?php if ( c27()->get_setting( 'stats_countries_enabled', true ) !== false ): ?>
			<?php require locate_template( 'templates/dashboard/stats/widgets/countries.php' ) ?>
		<?php endif ?>
	</div>

	<div class="col-md-8">
		<?php if ( c27()->get_setting( 'stats_visits_chart_enabled', true ) !== false ): ?>
			<?php require locate_template( 'templates/dashboard/stats/widgets/visits-chart.php' ) ?>
		<?php endif ?>

		<?php if ( c27()->get_setting( 'stats_referrers_enabled', true ) !== false ): ?>
			<?php require locate_template( 'templates/dashboard/stats/widgets/referrers.php' ) ?>
		<?php endif ?>

		<div class="row custom-row">
			<?php if ( c27()->get_setting( 'stats_platforms_enabled', true ) !== false ): ?>
				<div class="col-md-6">
					<?php require locate_template( 'templates/dashboard/stats/widgets/platforms.php' ) ?>
				</div>
			<?php endif ?>

			<?php if ( c27()->get_setting( 'stats_browsers_enabled', true ) !== false ): ?>
				<div class="col-md-6">
					<?php require locate_template( 'templates/dashboard/stats/widgets/browsers.php' ) ?>
				</div>
			<?php endif ?>
		</div>
	</div>
</div>