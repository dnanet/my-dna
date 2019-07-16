<?php

namespace MyListing\Ext\Admin_Tips;

class Admin_Tips {
    use \MyListing\Src\Traits\Instantiatable;

    protected $tips = [];

	public function __construct() {
		if ( ! is_user_logged_in() || ! is_admin() ) {
			return false;
		}

		// Run on init, so tips can be added through filters by other theme extensions.
		add_action( 'init', [ $this, 'initialize' ], 50 );
	}

	/**
	 * Initialize admin tips.
	 *
	 * @since 1.7.0
	 */
	public function initialize() {
		/**
		 * Register tips, as tip-name -> tip-file-location.
		 * Allow for custom tips through the mylisting/admin-tips filter.
		 *
		 * @since 1.7.0
		 */
		$this->tips = apply_filters( 'mylisting/admin-tips', [
			'bracket-syntax' => CASE27_THEME_DIR . '/includes/extensions/admin-tips/tips/bracket-syntax.php',
		] );

		// Register ajax route.
		add_action( 'wp_ajax_cts_get_tip', [ $this, 'get_tip' ] );

		// Output tip html wrapper.
        add_action( 'admin_footer', [ $this, 'output_template' ] );
	}

	/**
	 * Handle cts_get_tip Ajax request.
	 *
	 * @since  1.6.6
	 */
	public function get_tip() {
		// Validate request.
		if ( ! is_user_logged_in() || empty( $_GET['tip'] ) ) {
			return false;
		}

		$tip = sanitize_text_field( $_GET['tip'] );
		if ( ! array_key_exists( $tip, $this->tips ) ) {
			return false;
		}

		// Valid, send html.
		include $this->tips[ $tip ];
		exit;
	}

	/**
	 * Display tip wrapper markup in wp-admin footer.
	 *
	 * @since  1.6.6
	 */
	public function output_template() { ?>
		<div class="cts-tip-wrapper">
			<div class="cts-tip-container">
				<div class="tip-content"></div>
				<div class="tip-footer">
					<div class="button button-primary close-dialog">Got it!</div>
				</div>
			</div>

			<?php c27()->get_partial( 'spinner', [
				'color' => '#fff',
				'size' => 24,
				'width' => 2.5,
			] ) ?>
		</div>
	<?php }
}