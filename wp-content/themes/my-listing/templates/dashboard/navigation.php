<?php do_action( 'woocommerce_before_account_navigation' ); ?>

<nav class="woocommerce-MyAccount-navigation">
	<?php if ( has_nav_menu( 'mylisting-user-menu' ) ) : ?>
	    <?php wp_nav_menu([
		        'theme_location' => 'mylisting-user-menu',
		        'container'		 => false,
		        'depth'     	 => 0,
		        'menu_class' 	 => '',
		        'items_wrap'  	 => '<ul>%3$s</ul>'
		    ]) ?>
	<?php else: ?>
		<ul>
			<?php foreach ( wc_get_account_menu_items() as $endpoint => $label ) : ?>
				<li class="<?php echo wc_get_account_menu_item_classes( $endpoint ); ?>">
					<a href="<?php echo esc_url( wc_get_account_endpoint_url( $endpoint ) ); ?>"><?php echo esc_html( $label ); ?></a>
				</li>
			<?php endforeach; ?>
		</ul>
	<?php endif ?>
</nav>

<?php do_action( 'woocommerce_after_account_navigation' ); ?>
