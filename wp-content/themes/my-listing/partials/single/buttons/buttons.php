<?php
/**
 * Single listing cover buttons template.
 *
 * @since 1.6.0
 * @deprecated
 */

if ( ! $layout['buttons'] ) {
    return;
}
?>

<li id="qa-cover-buttons">
    <a href="#" data-toggle="modal" data-target="#qa-cover-buttons-modal">
        <i class="mi expand_more"></i>
        <span><?php _ex( 'More', 'Quick Actions > Cover buttons', 'my-listing' ) ?></span>
    </a>
    <div id="qa-cover-buttons-modal" class="modal modal-27">
        <div class="modal-dialog modal-md">
            <div class="modal-content">
                <ul>
                    <?php foreach ( $layout['buttons'] as $button ):
                        if ( ! isset( $button['id'] ) ) {
                            $button['id'] = sprintf( 'cover-button--%s', uniqid() );
                        }

                        if ( ! isset( $button['classes'] ) ) {
                            $button['classes'] = [];
                        }

                        $button['classes'][] = 'button-plain';

                        if ( ! empty( $button['icon'] ) && isset( $button['label'] ) ) {
                            $button['label'] = sprintf(
                                '%s<span class="button-label">%s</span>',
                                c27()->get_icon_markup( $button['icon'] ),
                                $button['label']
                            );
                        }

                        $button_template_path = sprintf( 'partials/single/buttons/%s.php', $button['action'] );

                        if ( $button_template = locate_template( $button_template_path ) ):
                            require $button_template;
                        elseif ( has_action( sprintf( 'case27\listing\cover\buttons\%s', $button['action'] ) ) ):
                            do_action( "case27\listing\cover\buttons\\{$button['action']}", $button, $listing );
                        endif;
                        ?>
                    <?php endforeach ?>
                </ul>
            </div>
        </div>
    </div>
</li>
