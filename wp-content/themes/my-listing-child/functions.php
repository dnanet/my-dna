<?php
// Enqueue child theme style.css
add_action( 'wp_enqueue_scripts', function() {
    wp_enqueue_style( 'child-style', get_stylesheet_uri() );
    if ( is_rtl() ) {
    	wp_enqueue_style( 'mylisting-rtl', get_template_directory_uri() . '/rtl.css', [], wp_get_theme()->get('Version') );
    }
}, 500 );
// Happy Coding :)
if ( ! function_exists( 'display_related_posts_on_listing' ) ) {

    function display_related_posts_on_listing($post_tag) {
        $args = array(
            'post_type' => 'post',
            'tag' => $post_tag, // Here is where is being filtered by the tag you want
            'orderby' => 'rand',
            'order' => 'ASC',
            'posts_per_page' => 3,
        );

        $related_products = new WP_Query( $args );
        //print_r($related_products);
        if (sizeof($related_products)>0) {
            ?>
            <section class="archive-posts">
                <div class="container">
                    <?php
                        $args = array(
                            'post_type' => 'post',
                            'tag' => $post_tag, // Here is where is being filtered by the tag you want
                            'orderby' => 'rand',
                            'order' => 'ASC',
                            'posts_per_page' => 3,
                        );

                        $related_products = new WP_Query( $args );
                ?>
                <?php if ( $related_products->have_posts() ): ?>
                        <div class="row section-title">
                            <h2 class="row section-title"><?php _e('Posts', 'my-listing');?></h2>
                        </div>
                        <div class="row section-body grid">
                            <?php while ( $related_products -> have_posts() ) : $related_products -> the_post();  ?>
                                <?php global $post;

                                c27()->get_partial( 'post-preview', [
                                    'wrap_in' => 'col-md-4 col-sm-6 col-xs-12 ' . ( is_sticky() ? ' sticky ' : '' ),
                                ] );
                                ?>
                            <?php endwhile; wp_reset_query();?>
                        </div>
<!--
                        <div class="row section-button">
                            <a class="tag-link" href="<?php echo get_tag_link($post_tag);?>"><?php _e('More Posts','my-listing');  ?></a>
                    
                        </div>
-->

                    <?php endif ?>

                </div>
            </section>
        
        <?php
        }
    }
}