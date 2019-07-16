<div class="element">
	<div class="pf-head round-icon">
		<div class="title-style-1">
			<i class="mi devices"></i>
			<h5><?php _ex( 'Devices', 'User Dashboard', 'my-listing' ) ?></h5>
		</div>
	</div>
	<div class="pf-body">
		<?php if ( $devices = $stats->get('visits.devices') ): ?>
			<ul class="dash-table">
				<?php foreach ( $devices as $device ): ?>
					<?php printf(
						'<li data-device="%s"><strong>%s</strong> (%s views)</li>',
						esc_attr( $device['name'] ),
						$device['label'],
						$device['count']
					) ?>
				<?php endforeach ?>
			</ul>
		<?php endif ?>
	</div>
</div>