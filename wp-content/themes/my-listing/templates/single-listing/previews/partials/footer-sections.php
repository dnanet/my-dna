<?php ob_start() ?>
    <li class="item-preview" data-toggle="tooltip" data-placement="bottom" data-original-title="<?php esc_attr_e( 'Quick view', 'my-listing' ) ?>">
        <a href="#" type="button" class="c27-toggle-quick-view-modal" data-id="<?php echo esc_attr( $listing->get_id() ); ?>"><i class="material-icons">zoom_in</i></a>
    </li>
<?php $quick_view_button = ob_get_clean() ?>

<?php ob_start() ?>
    <li data-toggle="tooltip" data-placement="bottom" title="" data-original-title="<?php esc_attr_e( 'Bookmark', 'my-listing' ) ?>">
        <a class="c27-bookmark-button <?php echo mylisting()->bookmarks()->is_bookmarked($listing->get_id(), get_current_user_id()) ? 'bookmarked' : '' ?>"
           data-listing-id="<?php echo esc_attr( $listing->get_id() ) ?>" data-nonce="<?php echo esc_attr( wp_create_nonce('c27_bookmark_nonce') ) ?>">
           <i class="material-icons">favorite_border</i>
        </a>
    </li>
<?php $bookmark_button = ob_get_clean() ?>

<!-- FOOTER SECTIONS -->
<?php $footer_section_count = 0; ?>
<?php if ($options['footer']['sections']): ?>
    <?php foreach ((array) $options['footer']['sections'] as $section): ?>

        <!-- CATEGORIES SECTION -->
        <?php if ($section['type'] == 'categories'):
            // Keys = taxonomy name
            // Value = taxonomy field name
            $taxonomies = array_merge( [
                'job_listing_category' => 'job_category',
                'case27_job_listing_tags' => 'job_tags',
                'region' => 'region',
            ], mylisting_custom_taxonomies( 'slug', 'slug' ) );

            $taxonomy = ! empty( $section['taxonomy'] ) ? $section['taxonomy'] : 'job_listing_category';

            if ( ! isset( $taxonomies[ $taxonomy ] ) ) {
                continue;
            }

            if ( ! ( $terms = $listing->get_field( $taxonomies[ $taxonomy ] ) ) ) {
                continue;
            }

            $footer_section_count++;
            ?>
            <div class="listing-details c27-footer-section">
                <ul class="c27-listing-preview-category-list">

                    <?php if ( $terms ):
                        $category_count = count( $terms );
                        $first_category = array_shift( $terms );
                        $first_ctg = new MyListing\Src\Term( $first_category );
                        $category_names = array_map(function($category) {
                            return $category->name;
                        }, $terms);
                        $categories_string = join('<br>', $category_names);
                        ?>
                        <li>
                            <a href="<?php echo esc_url( $first_ctg->get_link() ) ?>">
                                <span class="cat-icon" style="background-color: <?php echo esc_attr( $first_ctg->get_color() ) ?>;">
                                    <?php echo $first_ctg->get_icon([ 'background' => false ]) ?>
                                </span>
                                <span class="category-name"><?php echo esc_html( $first_ctg->get_name() ) ?></span>
                            </a>
                        </li>

                        <?php if (count($terms)): ?>
                            <li data-toggle="tooltip" data-placement="bottom" data-original-title="<?php echo esc_attr( $categories_string ) ?>" data-html="true">
                                <div class="categories-dropdown dropdown c27-more-categories">
                                    <a href="#other-categories">
                                        <span class="cat-icon cat-more">+<?php echo $category_count - 1 ?></span>
                                    </a>
                                </div>
                            </li>
                        <?php endif ?>
                    <?php endif ?>
                </ul>

                <div class="ld-info">
                    <ul>
                        <?php if (isset($section['show_quick_view_button']) && $section['show_quick_view_button'] == 'yes'): ?>
                            <?php echo $quick_view_button ?>
                        <?php endif ?>
                        <?php if (isset($section['show_bookmark_button']) && $section['show_bookmark_button'] == 'yes'): ?>
                            <?php echo $bookmark_button ?>
                        <?php endif ?>
                    </ul>
                </div>
            </div>
        <?php endif ?>

        <!-- RELATED LISTING (HOST) SECTION -->
        <?php if ( $section['type'] == 'host' && ( $hostID = $listing->get_field('related_listing') ) ): ?>
            <?php $host = \MyListing\Src\Listing::get( $hostID ) ?>

            <?php if ( $host ): $footer_section_count++; ?>
                <div class="event-host c27-footer-section">
                    <a href="<?php echo esc_url( $host->get_link() ) ?>">
                        <?php if ( $host_logo = $host->get_logo() ): ?>
                            <div class="avatar">
                                <img src="<?php echo esc_url( $host_logo ) ?>" alt="<?php echo esc_attr( $host->get_name() ) ?>">
                            </div>
                        <?php endif ?>
                        <span class="host-name"><?php echo str_replace('[[listing_name]]', apply_filters( 'the_title', $host->get_name(), $host->get_id() ), $section['label']) ?></span>
                    </a>

                    <div class="ld-info">
                        <ul>
                            <?php if (isset($section['show_quick_view_button']) && $section['show_quick_view_button'] == 'yes'): ?>
                                <?php echo $quick_view_button ?>
                            <?php endif ?>
                            <?php if (isset($section['show_bookmark_button']) && $section['show_bookmark_button'] == 'yes'): ?>
                                <?php echo $bookmark_button ?>
                            <?php endif ?>
                        </ul>
                    </div>
                </div>
            <?php endif ?>
        <?php endif ?>

        <!-- AUTHOR SECTION -->
        <?php if ( $section['type'] == 'author' && ( $listing->author instanceof \MyListing\Src\User ) && $listing->author->exists() ):
            $footer_section_count++; ?>
                <div class="event-host c27-footer-section">
                    <a href="<?php echo esc_url( $listing->author->get_link() ) ?>">
                        <?php if ( $avatar = $listing->author->get_avatar() ): ?>
                            <div class="avatar">
                                <img src="<?php echo esc_url( $avatar ) ?>" alt="<?php echo esc_attr( $listing->author->get_name() ) ?>">
                            </div>
                        <?php endif ?>
                        <span class="host-name"><?php echo str_replace('[[author]]', esc_html( $listing->author->get_name() ), $section['label']) ?></span>
                    </a>

                    <div class="ld-info">
                        <ul>
                            <?php if (isset($section['show_quick_view_button']) && $section['show_quick_view_button'] == 'yes'): ?>
                                <?php echo $quick_view_button ?>
                            <?php endif ?>
                            <?php if (isset($section['show_bookmark_button']) && $section['show_bookmark_button'] == 'yes'): ?>
                                <?php echo $bookmark_button ?>
                            <?php endif ?>
                        </ul>
                    </div>
                </div>
        <?php endif ?>

        <!-- DETAILS SECTION -->
        <?php if ($section['type'] == 'details' && $section['details']): $footer_section_count++; ?>
            <div class="listing-details-3 c27-footer-section">
                <ul class="details-list">
                    <?php foreach ((array) $section['details'] as $detail):
                        if ( ! isset( $detail['icon'] ) ) {
                            $detail['icon'] = '';
                        }

                        if ( ! $listing->has_field( $detail['show_field'] ) ) {
                            continue;
                        }

                        $detail_val = $listing->get_field( $detail['show_field'] );
                        $detail_val = apply_filters( 'case27\listing\preview\detail\\' . $detail['show_field'], $detail_val, $detail, $listing );

                        if ( is_array( $detail_val ) ) {
                            $detail_val = join( ', ', $detail_val );
                        }

                        $GLOBALS['c27_active_shortcode_content'] = $detail_val; ?>
                        <li>
                            <?php if ( ! empty( $detail['icon'] ) ): ?>
                                <i class="<?php echo esc_attr( $detail['icon'] ) ?>"></i>
                            <?php endif ?>
                            <span><?php echo str_replace( '[[field]]', $detail_val, do_shortcode( $detail['label'] ) ) ?></span>
                        </li>
                    <?php endforeach ?>
                </ul>
            </div>
        <?php endif ?>

        <?php if ($section['type'] == 'actions' || $section['type'] == 'details'): ?>
            <?php if (
                ( isset($section['show_quick_view_button']) && $section['show_quick_view_button'] == 'yes' ) ||
                ( isset($section['show_bookmark_button']) && $section['show_bookmark_button'] == 'yes' )
             ): $footer_section_count++; ?>
                <div class="listing-details actions c27-footer-section">
                    <div class="ld-info">
                        <ul>
                            <?php if (isset($section['show_quick_view_button']) && $section['show_quick_view_button'] == 'yes'): ?>
                                <?php echo $quick_view_button ?>
                            <?php endif ?>
                            <?php if (isset($section['show_bookmark_button']) && $section['show_bookmark_button'] == 'yes'): ?>
                                <?php echo $bookmark_button ?>
                            <?php endif ?>
                        </ul>
                    </div>
                </div>
            <?php endif ?>
        <?php endif ?>
    <?php endforeach ?>
<?php endif ?>

<?php if ( $footer_section_count < 1 ): ?>
    <div class="c27-footer-empty"></div>
<?php endif ?>