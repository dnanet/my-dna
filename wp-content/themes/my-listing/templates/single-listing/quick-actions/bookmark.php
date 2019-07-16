<?php
/**
 * `Bookmark` quick action.
 *
 * @since 2.0
 */

$is_bookmarked = mylisting()->bookmarks()->is_bookmarked( $listing->get_id(), get_current_user_id() );
?>

<li id="<?php echo esc_attr( $action['id'] ) ?>" class="<?php echo esc_attr( $action['class'] ) ?>">
    <a
    	href="#"
    	class="mylisting-bookmark-item <?php echo $is_bookmarked ? 'bookmarked' : '' ?>"
    	data-listing-id="<?php echo esc_attr( $listing->get_id() ) ?>"
    	data-nonce="<?php echo esc_attr( wp_create_nonce( 'c27_bookmark_nonce' ) ) ?>"
    >
    	<?php echo c27()->get_icon_markup( $action['icon'] ) ?>
    	<span><?php echo $action['label'] ?></span>
    </a>
</li>