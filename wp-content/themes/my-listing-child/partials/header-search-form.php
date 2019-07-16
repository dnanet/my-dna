<?php
	$data = c27()->merge_options([
			'placeholder' => __( 'Search...', 'my-listing' ),
		], $data);
?>

	<div>
		<form action="<?php echo esc_url( home_url('/') ) ?>" method="GET">
			<div class="dark-forms header-search">
				<i class="material-icons">search</i>
				<input type="search" placeholder="<?php echo esc_attr( $data['placeholder'] ) ?>" value="<?php echo isset($_GET['s']) ? esc_attr( sanitize_text_field($_GET['s']) ) : '' ?>" name="s">
				<div class="instant-results"></div>
			</div>
		</form>
	</div>