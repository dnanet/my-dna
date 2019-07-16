<?php
	$data = c27()->merge_options([
			'icon' => '',
			'icon_style' => 1,
			'title' => '',
			'related_listing' => '',
			'wrapper_class' => 'grid-item reveal',
			'wrapper_id' => '',
			'ref' => '',
		], $data);

	if ( ! ( $related_listing = \MyListing\Src\Listing::get( $data['related_listing'] ) ) ) {
		return false;
	}
?>

<div class="<?php echo esc_attr( $data['wrapper_class'] ) ?>" <?php echo $data['wrapper_id'] ? sprintf( 'id="%s"', $data['wrapper_id'] ) : '' ?>>
	<div class="element related-listing-block">
		<div class="pf-head">
			<div class="title-style-1 title-style-<?php echo esc_attr( $data['icon_style'] ) ?>">
				<?php if ($data['icon_style'] != 3): ?>
					<?php echo c27()->get_icon_markup($data['icon']) ?>
				<?php endif ?>
				<h5><?php echo esc_html( $data['title'] ) ?></h5>
			</div>
		</div>
		<div class="pf-body">
			<div class="event-host">
				<a href="<?php echo esc_url( $related_listing->get_link() ) ?>">
					<?php if ( $listing_thumbnail = $related_listing->get_logo() ): ?>
						<div class="avatar">
							<img src="<?php echo esc_url( $listing_thumbnail ) ?>">
						</div>
					<?php endif ?>
					<span class="host-name"><?php echo apply_filters( 'the_title', $related_listing->get_name(), $related_listing->get_id() ) ?></span>
				</a>
			</div>
		</div>
	</div>
</div>