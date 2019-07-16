<div class="lf-item <?php echo esc_attr( 'lf-item-'.$options['template'] ) ?>" data-template="list-view">
    <a href="<?php echo esc_url( $listing->get_link() ) ?>">
        <div class="lf-item-info">
            <?php if ( $logo = $listing->get_logo() ): ?>
                <div class="lf-avatar" style="background-image: url('<?php echo esc_url( $logo ) ?>')"></div>
            <?php endif ?>

            <h4 class="case27-secondary-text listing-preview-title">
                <?php echo $listing->get_name() ?>
                <?php if ( $listing->is_verified() ): ?>
                    <span class="verified-badge"><i class="fa fa-check"></i></span>
                <?php endif ?>
            </h4>

            <?php if ( ! empty( $info_fields ) ): ?>
                <ul>
                    <?php foreach ( $info_fields as $info_field ): ?>
                         <li>
                            <i class="<?php echo esc_attr( $info_field['icon'] ) ?> sm-icon"></i>
                            <?php echo esc_html( $info_field['content'] ) ?>
                        </li>
                    <?php endforeach ?>
                </ul>
            <?php endif ?>
        </div>
    </a>
</div>

<?php
/**
 * Include footer sections template.
 *
 * @since 1.0
 */
require locate_template( 'templates/single-listing/previews/partials/footer-sections.php' ) ?>
