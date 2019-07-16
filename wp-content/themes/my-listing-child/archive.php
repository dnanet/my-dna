<?php
/**
 * The template for displaying archive pages.
 *
 * @since 1.0.0
 */
get_header();
global $wp_query;

$title = get_the_archive_title();
$description = get_the_archive_description();

if ( is_search() ) {
	$title = sprintf( __( 'Search Results for: <em>%s</em>', 'my-listing' ), get_search_query() );
}
//echo 'is archive: '.is_archive();

//print_r($current_category);

//print_r($categories);
if (is_archive()) {
    $current_category = get_queried_object();
    $categories = get_term_children($current_category->term_id, $current_category->taxonomy);
}
else {
    $args = array(
                 'taxonomy' => 'category',
                   'orderby' => 'name',
                   'order'   => 'ASC'
               );

       $categories = get_categories($args);
}
?>

<div class="archive-page">
	<section class="i-section archive-heading <?php echo ! $description ? 'no-description' : '' ?>">
		<div class="container">
			<div class="row section-title reveal">
				<h1 class="case27-primary-text"><?php echo $title ?></h1>
			</div>
            
			<?php if ( $description ): ?>
				<div class="row section-body reveal archive-description text-center">
					<div class="col-md-12">
						<?php echo $description ?>
					</div>
				</div>
			<?php endif ?>
            <div class="cover-buttons">
				<ul>

					
                    <?php foreach ( $categories as $category ): ?>
                    <a href="/post-category/<?php echo esc_url( $category->slug ) ?>">
							<li>
								<div class="buttons button-outlined medium">
									<i class="mi bookmark_border"></i>
									<?php echo esc_html( $category->name ) ?>
								</div>
							</li>
						</a>
					<?php endforeach ?>

					
				</ul>
			</div>
		</div>
	</section>

	<section class="i-section archive-posts">
		<div class="container">

		<?php if ( have_posts() ): ?>

				<div class="row section-body grid">
					<?php while ( have_posts() ): the_post(); ?>
    					<?php global $post;

						if ( get_post_type() === 'job_listing' ) {
    						mylisting_locate_template( 'partials/listing-preview.php', [
    							'listing' => $post,
    							'wrap_in' => 'col-md-4 col-sm-6 col-xs-12 reveal',
    						] );
    						continue;
						}

						c27()->get_partial( 'post-preview', [
							'wrap_in' => 'col-md-4 col-sm-6 col-xs-12 ' . ( is_sticky() ? ' sticky ' : '' ),
						] );
						?>
					<?php endwhile ?>
				</div>

				<div class="blog-footer">
					<div class="row project-changer">
						<div class="text-center">
							<?php echo paginate_links([
								'total'   => $wp_query->max_num_pages,
								'format'  => '?paged=%#%',
								'current' => 0,
								'current' => get_query_var('paged') ?: 1,
								]) ?>
						</div>
					</div>
				</div>

			<?php else: ?>

				<div class="row text-center">
					<div>
						<i class="no-results-icon material-icons">mood_bad</i>
						<p><?php _e( 'No results. Try another search?', 'my-listing' ) ?></p>
						<?php echo get_search_form() ?>
					</div>
				</div>

			<?php endif ?>

		</div>
	</section>
</div>

<?php get_footer();
