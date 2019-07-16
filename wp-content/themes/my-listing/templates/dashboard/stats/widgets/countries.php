<div class="element">
	<div class="pf-head round-icon">
		<div class="title-style-1">
			<i class="mi flag"></i>
			<h5><?php _ex( 'Top Countries', 'User Dashboard', 'my-listing' ) ?></h5>
		</div>
	</div>
	<div class="pf-body">
		<?php if ( $countries = $stats->get('visits.countries') ): ?>
			<ul class="dash-table">
				<?php foreach ( $countries as $country ): ?>
					<li><i class="mi flag"></i>
						<?php printf(
							'</span> <strong>%s</strong> (%s views)',
							$country['name'],
							$country['count']
						) ?>
					</li>
				<?php endforeach ?>
			</ul>
		<?php endif ?>
	</div>
</div>