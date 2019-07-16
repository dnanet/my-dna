<?php

namespace MyListing\Ext\Social_Login;

class Social_Login {
    use \MyListing\Src\Traits\Instantiatable;

    public $networks = [];

	public function __construct() {
		// Setup ACF settings page.
		add_action( 'acf/init', [ $this, 'setup_options_page' ] );

		// Initialize social login.
		add_action( 'init', [ $this, 'initialize' ], 30 );
	}

	/**
	 * Initialize social login.
	 *
	 * @since 1.6.6
	 */
	public function initialize() {
		// Only run past this for logged out users, or for logged in users when editing account details.
		if ( ! class_exists( 'WooCommerce' ) ) {
			return false;
		}

		// Setup supported networks.
		$this->setup_networks();

		// Setup login endpoints.
		add_action( 'wp_ajax_cts_login_endpoint', [ $this, 'login_endpoint' ] );
        add_action( 'wp_ajax_nopriv_cts_login_endpoint', [ $this, 'login_endpoint' ] );

        // Display login buttons.
        add_action( 'woocommerce_login_form_end', [ $this, 'display_buttons' ] );
        add_action( 'woocommerce_register_form_end', [ $this, 'display_buttons' ] );

        // Display connected accounts.
        add_action( 'woocommerce_edit_account_form', [ $this, 'display_connected_accounts' ], 30 );

        // Display profile picture settings.
        add_action( 'woocommerce_edit_account_form', [ $this, 'display_profile_picture_settings' ], 35 );

        // Save profile picture settings.
        add_action( 'woocommerce_save_account_details', [ $this, 'save_profile_picture_settings' ], 35 );

        // Modify user avatar based on picture settings.
		add_filter( 'get_avatar_url', [ $this, 'set_user_picture' ], 35, 3 );
	}

	/**
	 * Init supported social login networks.
	 *
	 * @since 1.6.3
	 */
	public function setup_networks() {
		$this->networks['google']   = new Networks\Google();
		$this->networks['facebook'] = new Networks\Facebook();

		$this->networks = array_filter( $this->networks, function( $network ) {
			return $network->is_enabled();
		} );
	}

	/**
	 * Social login endpoint. Handles general request validation,
	 * and instantiates the requested network's class.
	 *
	 * @since 1.6.3
	 */
	public function login_endpoint() {
		check_ajax_referer( 'c27_ajax_nonce', 'security' );

		if ( empty( $_POST['network'] ) || empty( $this->networks[ $_POST['network'] ] ) ) {
			return false;
		}

		$network = $this->networks[ $_POST['network'] ];
		$network->handle_request( $_POST );
	}

	/**
	 * Get list of active networks.
	 *
	 * @since  1.6.6
	 * @return array $networks
	 */
	public function get_networks() {
		$networks = apply_filters( 'mylisting\social-login\networks', array_keys( $this->networks ) );

		// Filter out networks that aren't active or don't exist.
		foreach ( $networks as $key => $network ) {
			if ( empty( $this->networks[ $network ] ) ) {
				unset( $networks[ $key ] );
				continue;
			}
		}

		return $networks;
	}

	/**
	 * Output social login buttons.
	 *
	 * @since 1.6.3
	 */
	public function display_buttons() {
		if ( ! ( $networks = $this->get_networks() ) ) {
			return false;
		}

		// Output buttons.
		?><div class="cts-social-login-wrapper">
			<p class="connect-with"><?php _ex( 'Or connect with', 'Social login message', 'my-listing' ) ?></p>
			<div class="cts-network-wrapper">
				<?php foreach ( $networks as $network ): ?>
				<?php echo $this->networks[ $network ]->display_button() ?>
			<?php endforeach ?>
		</div>
		</div><?php
	}

	/**
	 * Display Connected Accounts section in WooCommerce edit account page.
	 *
	 * @since 1.6.6
	 */
	public function display_connected_accounts() {
		if ( ! ( $networks = $this->get_networks() ) || ! is_user_logged_in() ) {
			return false;
		}

		// Output buttons.
		?><div class="cts-connected-accounts">
			<h5><?php _e( 'Connected Accounts', 'my-listing' ) ?></h5>
			<?php foreach ( $networks as $network ): ?>
				<?php echo $this->networks[ $network ]->display_connected_account() ?>
			<?php endforeach ?>
		</div><?php
	}

	/**
	 * Display profile picture settings in WooCommerce edit account page.
	 *
	 * @since 1.6.6
	 */
	public function display_profile_picture_settings() {
		if ( ! ( $networks = $this->get_networks() ) || ! is_user_logged_in() ) {
			return false;
		}

		// Get all networks with a user picture.
		$networks = array_filter( $networks, function( $network ) {
			return $this->networks[ $network ]->get_user_picture();
		} );

		if ( empty( $networks ) ) {
			return false;
		}

		$current_value = get_user_meta( get_current_user_id(), 'mylisting_profile_picture', true );
		if ( ! in_array( $current_value, $networks ) ) {
			$current_value = 'default';
		}

		// Output settings.
		?><div class="cts-user-picture-settings">
			<h5><?php _e( 'Profile Picture', 'my-listing' ) ?></h5>
			<?php foreach ( $networks as $network ): $network_id = sprintf( 'cts-user-picture-use-%s', $this->networks[ $network ]->name ); ?>
				<div class="md-checkbox">
					<input id="<?php echo esc_attr( $network_id ) ?>" type="radio" name="cts-user-picture-settings" value="<?php echo esc_attr( $this->networks[ $network ]->name ) ?>" <?php checked( $network, $current_value ); ?>>
					<label for="<?php echo esc_attr( $network_id ) ?>"><?php printf( __( 'Use my %s account picture', 'my-listing' ), ucwords( $this->networks[ $network ]->name ) ) ?></label>
				</div>
			<?php endforeach ?>
			<div class="md-checkbox">
				<input id="cts-user-picture-use-default" type="radio" name="cts-user-picture-settings" value="default" <?php checked( 'default', $current_value ); ?>>
				<label for="cts-user-picture-use-default"><?php _ex( 'Default', 'Profile picture settings', 'my-listing' ) ?></label>
			</div>
		</div><?php
	}

	/**
	 * Save profile picture settings.
	 *
	 * @since 1.6.6
	 */
	public function save_profile_picture_settings( $user_id ) {
		if ( ! ( $networks = $this->get_networks() ) || ! is_user_logged_in() || empty( $_POST['cts-user-picture-settings'] ) ) {
			return false;
		}

		// Picture settings.
		$picture_settings = sanitize_text_field( $_POST['cts-user-picture-settings'] );

		// Get all networks with a user picture.
		$networks = array_filter( $networks, function( $network ) {
			return $this->networks[ $network ]->get_user_picture();
		} );

		if ( empty( $networks ) || ! in_array( $picture_settings, array_merge( $networks, ['default'] ) ) ) {
			return false;
		}

		// Save settings.
		update_user_meta( $user_id, 'mylisting_profile_picture', $picture_settings );
	}

	/**
	 * Set user avatar based on profile picture settings.
	 *
	 * @since 1.6.6
	 */
	public function set_user_picture( $url, $id_or_email, $args ) {
		if ( ! ( $networks = $this->get_networks() ) ) {
			return $url;
		}

		// Process the user identifier.
		if ( is_numeric( $id_or_email ) ) {
		    $user = get_user_by( 'id', absint( $id_or_email ) );
		} elseif ( $id_or_email instanceof \WP_User ) {
		    // User Object
		    $user = $id_or_email;
		} elseif ( $id_or_email instanceof \WP_Post ) {
		    // Post Object
		    $user = get_user_by( 'id', (int) $id_or_email->post_author );
		} elseif ( $id_or_email instanceof \WP_Comment && ! empty( $id_or_email->user_id ) ) {
	    	$user = get_user_by( 'id', (int) $id_or_email->user_id );
		} elseif ( is_string( $id_or_email ) && is_email( $id_or_email ) ) {
			$user = get_user_by( 'email', $id_or_email );
		} else {
			$user = false;
		}

		if ( ! ( $user instanceof \WP_User ) ) {
			return $url;
		}

		$picture_to_use = get_user_meta( $user->ID, 'mylisting_profile_picture', true );
		if ( ! in_array( $picture_to_use, $networks ) ) {
			return $url;
		}

		// Return custom picture.
		if ( $picture_url = $this->networks[ $picture_to_use ]->get_user_picture( $user->ID ) ) {
			return $picture_url;
		}

		return $url;
	}

	/**
	 * Setup social login options page in WP Admin > Theme Options > Social Login.
	 *
	 * @since 1.6.3
	 */
	public function setup_options_page() {
		acf_add_options_sub_page( [
			'page_title' 	=> _x( 'Social Login', 'Social Login page title in WP Admin', 'my-listing' ),
			'menu_title'	=> _x( 'Social Login', 'Social Login menu title in WP Admin', 'my-listing' ),
			'menu_slug' 	=> 'theme-social-login-settings',
			'capability'	=> 'edit_posts',
			'redirect'		=> false,
			'parent_slug'   => 'case27/tools.php',
		] );
	}
}
