<?php
/**
 * `Report Listing` quick action.
 *
 * @since 2.0
 */
?>

<li id="<?php echo esc_attr( $action['id'] ) ?>" class="<?php echo esc_attr( $action['class'] ) ?>">
    <a href="#" data-toggle="modal" data-target="#report-listing-modal">
    	<?php echo c27()->get_icon_markup( $action['icon'] ) ?>
    	<span><?php echo $action['label'] ?></span>
    </a>
</li>