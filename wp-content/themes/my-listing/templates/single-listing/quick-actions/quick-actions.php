<?php
/**
 * Single listing quick actions template.
 *
 * @since 2.0
 */

if ( empty( $layout['quick_actions'] ) ) {
	$layout['quick_actions'] = [];
	$actions = require locate_template( 'includes/extensions/listing-types/quick-actions/quick-actions.php' );
	$defaults = ['get-directions', 'call-now', 'send-email', 'visit-website', 'leave-review', 'claim-listing', 'bookmark', 'share', 'report-listing'];
	foreach ( $defaults as $default_action ) {
		if ( empty( $actions[ $default_action ] ) ) {
			continue;
		}

		$layout['quick_actions'][] = $actions[ $default_action ];
	}
}
?>

<div class="container">
	<div class="quick-listing-actions">
		<ul>
			<?php foreach ( $layout['quick_actions'] as $action ):
				if ( empty( $action['id'] ) ) {
					$action['id'] = sprintf( 'qa-%s', substr( md5( json_encode( $action ) ), 0, 6 ) );
				}

				$action['original_label'] = $action['label'];
				$action['label'] = do_shortcode( $listing->compile_string( $action['label'] ) );

				$template = sprintf( 'templates/single-listing/quick-actions/%s.php', $action['action'] ); ?>
				<?php if ( locate_template( $template ) ): ?>
					<?php require locate_template( $template ) ?>
				<?php elseif ( has_action( sprintf( 'mylisting/single/quick-actions/%s', $action['action'] ) ) ): ?>
					<?php do_action( sprintf( 'mylisting/single/quick-actions/%s', $action['action'] ), $action, $listing ) ?>
				<?php else: ?>
					<?php // dump($action) ?>
				<?php endif ?>
			<?php endforeach ?>

		    <?php
		    /**
		     * Old cover buttons. Disabled by default, will be removed in future versions.
		     */
		    if ( ! empty( $layout['buttons_as_quick_action'] ) && $layout['buttons_as_quick_action'] === true ) {
		        require locate_template( 'partials/single/buttons/buttons.php' );
		    } ?>
		</ul>
	</div>
</div>