<div class="element">
	<div class="pf-head round-icon">
		<div class="title-style-1">
			<i class="mi graphic_eq"></i>
			<h5><?php _ex( 'Visits', 'User Dashboard', 'my-listing' ) ?></h5>
			<div class="visit-chart-actions">
				<a href="#" data-toggle="lastday"><?php _ex( 'Last 24 hours', 'User dashboard', 'my-listing' ) ?></a>
				<a href="#" data-toggle="lastweek"><?php _ex( 'Last 7 days', 'User dashboard', 'my-listing' ) ?></a>
				<a href="#" data-toggle="lastmonth"><?php _ex( 'Last 30 days', 'User dashboard', 'my-listing' ) ?></a>
			</div>
		</div>
	</div>
	<div class="pf-body">
		<div id="visits-chart-wrapper">
			<div id="cts-visits-chart" data-stats="<?php echo c27()->encode_attr( $stats->get('visits.charts') ) ?>"></div>
		</div>
		<div class="chart-legend">
			<ul>
				<li>
					<span class="lg-blue"></span>
					<?php _ex( 'Views', 'User Dashboard', 'my-listing' ) ?>
				</li>
				<li>
					<span class="lg-purple"></span>
					<?php _ex( 'Unique views', 'User Dashboard', 'my-listing' ) ?>
				</li>
			</ul>
		</div>
	</div>
</div>

<style type="text/css">
	#visits-chart-wrapper .ct-series-a .ct-area { fill: <?php echo esc_attr( mylisting()->stats()->color_views ) ?>; }
	#visits-chart-wrapper .ct-series-a .ct-line, #visits-chart-wrapper .ct-series-a .ct-point { stroke: <?php echo esc_attr( mylisting()->stats()->color_views ) ?>; }
	.chart-legend .lg-blue { background: <?php echo esc_attr( mylisting()->stats()->color_views ) ?>; }

	#visits-chart-wrapper .ct-series-b .ct-area { fill: <?php echo esc_attr( mylisting()->stats()->color_unique_views ) ?>; }
	#visits-chart-wrapper .ct-series-b .ct-line, #visits-chart-wrapper .ct-series-b .ct-point { stroke: <?php echo esc_attr( mylisting()->stats()->color_unique_views ) ?>; }
	.chart-legend .lg-purple { background: <?php echo esc_attr( mylisting()->stats()->color_unique_views ) ?>; }
</style>