<?php

namespace MyListing\Ext\Social_Login\Networks;

class Facebook extends Network {
    protected
        $request,
        $userdata,
        $app_id;

    public
        $name     = 'facebook',
        $user_key = 'mylisting_facebook_account_id',
        $custom_fields = ['mylisting_facebook_account_name', 'mylisting_facebook_account_picture'];

    /**
     * Include required scripts and setup settings for Facebook login.
     *
     * @since 1.6.3
     */
    public function __construct() {
        if ( ! $this->is_enabled() ) {
            return false;
        }

        $this->app_id = c27()->get_setting( 'social_login_facebook_app_id' );

        add_action( 'wp_enqueue_scripts', function() {
            if ( ! is_user_logged_in() || is_wc_endpoint_url( 'edit-account' ) ) {
                wp_add_inline_script( 'c27-main', $this->login_script(), 'before' ); // @todo: Add support for redirect_to url parameter (pass it with JS)
            }
        }, 50 );
    }

    /**
     * Check if Sign-In with Facebook is enabled,
     * and an app ID has been provided.
     *
     * @since 1.6.3
     * @return bool
     */
    public function is_enabled() {
        return c27()->get_setting( 'social_login_facebook_enabled' ) && c27()->get_setting( 'social_login_facebook_app_id' );
    }

    /**
     * Attach handler to "Login with Facebook" button.
     *
     * @since 1.6.3
     */
    public function login_script() { ob_start(); ?>
        <script type="text/javascript">
            // Load the SDK asynchronously
            (function(d, s, id) {
              var js, fjs = d.getElementsByTagName(s)[0];
              if (d.getElementById(id)) return;
              js = d.createElement(s); js.id = id;
              js.src = "https://connect.facebook.net/en_US/sdk.js";
              fjs.parentNode.insertBefore(js, fjs);
            }(document, 'script', 'facebook-jssdk'));

            window.fbAsyncInit = function() {
                FB.init({
                    appId      : '<?php echo esc_attr( $this->app_id ) ?>',
                    cookie     : true,
                    xfbml      : true,
                    version    : 'v3.0'
                });
            };

            jQuery( 'body' ).on( 'click', '.cts-facebook-signin', function(e) {
                e.preventDefault();

                // process can be 'login', 'connect', or 'disconnect'.
                if ( jQuery(this).data('process') ) {
                    var process_request = jQuery(this).data('process');
                } else {
                    var process_request = 'login';
                }

                var container = jQuery(this).parents('.sign-in-box, .cts-connected-account');

                function success_handler( response ) {
                    if ( typeof response === 'object' ) {
                        if ( response.redirect ) {
                            return window.location.replace( response.redirect );
                        }

                        if ( response.status === 'error' && response.message ) {
                            alert( response.message );
                        }
                    }
                }

                if ( process_request === 'disconnect' ) {
                    if ( container.length ) { container.addClass('cts-processing-login'); }
                    jQuery.ajax( {
                        url: CASE27.ajax_url + '?action=cts_login_endpoint&security=' + CASE27.ajax_nonce,
                        type: 'POST',
                        dataType: 'json',
                        data: { network: 'facebook', process: process_request },
                        success: success_handler,
                        error: function( xhr, status, error ) { console.log('Failed', xhr, status, error); },
                        complete: function() { if ( container.length ) { container.removeClass('cts-processing-login'); } }
                    } );
                } else {
                    FB.login( function(response) {
                        if ( response.authResponse ) {
                            if ( container.length ) { container.addClass('cts-processing-login'); }
                            jQuery.ajax( {
                                url: CASE27.ajax_url + '?action=cts_login_endpoint&security=' + CASE27.ajax_nonce,
                                type: 'POST',
                                dataType: 'json',
                                data: { network: 'facebook', token: response.authResponse.accessToken, process: process_request },
                                success: success_handler,
                                error: function( xhr, status, error ) { console.log('Failed', xhr, status, error); },
                                complete: function() { if ( container.length ) { container.removeClass('cts-processing-login'); } }
                            } );
                        }
                    }, { scope: 'public_profile,email' } );
                }
            } );
        </script><?php
        // wp_add_inline_script() throws a warning when including <script> tags.
        return trim( preg_replace( '#<script[^>]*>(.*)</script>#is', '$1', ob_get_clean() ) );
    }

    /**
     * Display "Login with Facebook" button in auth forms.
     *
     * @since 1.6.3
     */
    public function display_button() { ?>
        <div class="buttons button-2 cts-facebook-signin"><i class="fa fa-facebook"></i> <?php _ex( 'Facebook', 'Login with Facebook button', 'my-listing' ) ?></div>
    <?php }

    /**
     * Display connected account info in user account edit page.
     *
     * @since 1.6.6
     */
    public function display_connected_account() {
        $account_id = get_user_meta( get_current_user_id(), $this->user_key, true );
        $account_name = get_user_meta( get_current_user_id(), 'mylisting_facebook_account_name', true );
        $account_picture = $this->get_user_picture();
        $is_connected = ! empty( $account_id );
        ?>
        <div class="cts-connected-account cts-account-facebook cts-is-<?php echo $is_connected ? 'connected' : 'disconnected' ?>">
            <div class="cts-account-header">
                <i class="fa fa-facebook"></i>
                <?php _ex( 'Facebook', 'Connect to Facebook button', 'my-listing' ) ?>
            </div>
            <div class="cts-account-actions">
                <?php if ( $is_connected ): ?>
                    <span>
                        <?php if ( $account_picture ): ?>
                            <span style="background-image: url('<?php echo esc_url( $account_picture ) ?>');" class="cts-account-picture"></span>
                        <?php endif ?>
                        <?php if ( $account_name ): ?>
                            <?php echo $account_name ?>
                        <?php endif ?>
                    </span>

                    <a href="#" class="cts-facebook-signin" data-process="disconnect"><?php _ex( 'Disconnect', 'Social login connected accounts', 'my-listing' ) ?></a>
                <?php else: ?>
                    <a href="#" class="cts-facebook-signin" data-process="connect"><?php _ex( 'Connect', 'Social login connected accounts', 'my-listing' ) ?></a>
                <?php endif ?>
            </div>
        </div>
    <?php }

    /**
     * Return the user picture url.
     *
     * @since 1.6.6
     */
    public function get_user_picture( $user_id = null ) {
        if ( ! $user_id ) {
            $user_id = get_current_user_id();
        }

        $account_picture = get_user_meta( $user_id, 'mylisting_facebook_account_picture', true );
        $picture_url = false;
        if ( is_object( $account_picture ) && ! empty( $account_picture->data ) && ! empty( $account_picture->data->url ) ) {
            $picture_url = $account_picture->data->url;
        }

        // Check if it's a valid image, and return it if so.
        if ( $picture_url ) {
            $image = wp_remote_get( $picture_url );
            if (
                ! is_wp_error( $image ) && wp_remote_retrieve_body( $image )
                && ! empty( $image['response']['code'] ) && $image['response']['code'] === 200
                && ! empty( $image['headers']['content-type'] ) && $image['headers']['content-type'] === 'image/jpeg'
            ) {
                return $picture_url;
            }
        }

        // Otherwise, request a new image, since the older url has most likely expired.
        if ( $user_facebook_id = get_user_meta( $user_id, 'mylisting_facebook_account_id', true ) ) {
            $request_url = add_query_arg( [
                'format' => 'json',
                'method' => 'get',
                'pretty' => '0',
                'redirect' => 'false',
            ], sprintf( 'https://graph.facebook.com/v3.0/%s/picture', $user_facebook_id ) );

            $request = wp_remote_get( $request_url );
            if (
                ! is_wp_error( $request ) && ( $body = json_decode( wp_remote_retrieve_body( $request ) ) )
                && ! empty( $request['response']['code'] ) && $request['response']['code'] === 200
                && ! empty( $body->data ) && ! empty( $body->data->url )
            ) {
                update_user_meta( $user_id, 'mylisting_facebook_account_picture', $body );
                return $body->data->url;
            }
        }

        return false;
    }

    /**
     * Get user data from their Facebook profile.
     *
     * @since 1.6.3
     */
    public function get_user_data() {
        if ( empty( $this->request['token'] ) ) {
            return false;
        }

        $response = wp_remote_get( sprintf( 'https://graph.facebook.com/v2.12/me?fields=id,first_name,last_name,name,picture,email&access_token=%s', $this->request['token'] ) );
        $data = wp_remote_retrieve_body( $response );

        if ( is_wp_error( $data ) ) {
            return false;
        }

        $this->transform_userdata( json_decode( $data ) );
    }

    /**
     * Transform user data object to the format expected by login() method.
     * email      -> data.email
     * first_name -> data.first_name
     * last_name  -> data.last_name
     */
    public function transform_userdata( $data ) {
        $this->userdata = [];

        if ( ! is_object( $data ) || empty( $data->id ) ) {
            return false;
        }

        if ( ! empty( $data->email ) ) {
            $this->userdata['email'] = $data->email;
        }

        if ( ! empty( $data->first_name ) ) {
            $this->userdata['first_name'] = $data->first_name;
        }

        if ( ! empty( $data->last_name ) ) {
            $this->userdata['last_name'] = $data->last_name;
        }

        // Used to tell if this account has already been connected to the user.
        $this->userdata['connected_account'] = [
            'key' => $this->user_key,
            'value' => $data->id,
        ];

        $this->userdata['custom_fields'] = [];

        if ( ! empty( $data->name ) ) {
            $this->userdata['custom_fields']['mylisting_facebook_account_name'] = $data->name;
        }

        if ( ! empty( $data->picture ) ) {
            $this->userdata['custom_fields']['mylisting_facebook_account_picture'] = $data->picture;
        }

        // Woocommerce Confirmation email plugin fix
        if ( defined( 'XLWUEV_SLUG' ) ) {

            $user = get_user_by( 'email', $this->userdata['email'] );

            if ( ! $user ) {
                return false;
            }

            if ( ! get_user_meta( $user->ID, 'wcemailverified', true ) ) {
                update_user_meta( $user->ID, 'wcemailverified', true );
            }
        }
    }
}