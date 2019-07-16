<?php
/**
 * Display a dashboard stat in card style.
 *
 * @since 2.0
 */

$data = c27()->merge_options( [
	'value' => '',
	'description' => '',
	'icon' => 'icon-window',
	'background' => '',
	'classes' => '',
], $data ) ?>

<div class="col-md-3 col-sm-6 <?php echo esc_attr( $data['classes'] ) ?>">
	<div class="mlduo-stat-box second" style="background: <?php echo esc_attr( $data['background'] ) ?>;">
		<h2><?php echo $data['value'] ?></h2>
		<p><?php echo $data['description'] ?></p>
		<?php echo c27()->get_icon_markup( $data['icon'] ) ?>
	</div>
</div>