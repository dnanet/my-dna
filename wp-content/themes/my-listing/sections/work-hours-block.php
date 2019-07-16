<?php
	$data = c27()->merge_options([
			'icon' => '',
			'icon_style' => 1,
			'title' => '',
			'hours' => [],
			'wrapper_class' => 'grid-item reveal',
			'wrapper_id' => '',
			'ref' => '',
		], $data);

	$unique_id = sprintf( 'work-hours--%s', MyListing\Utils\Random_Id::generate(8) );
	$schedule = new MyListing\Src\Work_Hours( $data['hours'] );

	if ( $schedule->is_empty() ) {
		return false;
	}
?>

<div class="<?php echo esc_attr( $data['wrapper_class'] ) ?>" <?php echo $data['wrapper_id'] ? sprintf( 'id="%s"', $data['wrapper_id'] ) : '' ?>>
	<div class="element work-hours-block">
		<div class="pf-head" data-toggle="collapse" data-target="#<?php echo esc_attr( $unique_id ) ?>">
			<div class="title-style-1">
				<?php echo c27()->get_icon_markup( $data['icon'] ) ?>
				<h5><span class="<?php echo esc_attr( $schedule->get_status() ) ?> work-hours-status"><?php echo esc_html( $schedule->get_message() ) ?></span></h5>
				<div class="timing-today">
					<?php echo $schedule->get_todays_schedule() ?>
					<span class="mi expand_more" data-toggle="tooltip" data-placement="top" title="<?php esc_attr_e( 'Toggle weekly schedule', 'my-listing' ) ?>"></span>
				</div>
			</div>
		</div>
		<div class="open-hours-wrapper pf-body collapse" id="<?php echo esc_attr( $unique_id ) ?>">
			<div id="open-hours">
				<ul class="extra-details">
					<?php foreach ( $schedule->get_schedule() as $weekday ): ?>
						<li>
							<p class="item-attr"><?php echo esc_html( $weekday['day_l10n'] ) ?></p>
							<p class="item-property"><?php echo $schedule->get_day_schedule( $weekday['day'] ) ?></p>
						</li>
					<?php endforeach ?>

					<?php if ( ! empty( $data['hours']['timezone'] ) ):
						$localTime = new DateTime( 'now', new DateTimeZone( $data['hours']['timezone'] ) );
						?>
						<p class="work-hours-timezone">
							<em><?php printf( __( '%s local time', 'my-listing' ), date_i18n( get_option( 'date_format' ) . ' ' . get_option( 'time_format' ), strtotime( $localTime->format('Y-m-d H:i:s') ) ) ) ?></em>
						</p>
					<?php endif ?>
				</ul>
			</div>
		</div>
	</div>
</div>
