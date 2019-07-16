<?php
/**
 * Handles reported listings.
 *
 * @since 1.0
 */

namespace MyListing\Ext\Report_Listings;

if ( ! defined('ABSPATH') ) {
    exit;
}

class Report_Listings {
    use \MyListing\Src\Traits\Instantiatable;

	public function __construct() {
        if ( is_admin() ) {
            add_action( 'load-post.php', [ $this, 'init_metabox' ] );
            add_action( 'load-post-new.php', [ $this, 'init_metabox' ] );
            add_filter( 'manage_case27_report_posts_columns', [ $this, 'admin_columns' ] );
            add_action( 'manage_case27_report_posts_custom_column', [ $this, 'admin_columns_content' ], 10, 2 );
            add_action( 'admin_menu',  [ $this, 'add_reports_as_listings_submenu' ], 250 );
        }

        add_action( 'wp_ajax_report_listing', [ $this, 'report_listing' ] );
        add_action( 'wp_ajax_nopriv_report_listing', [ $this, 'report_listing' ] );
	}

    /**
     * Add Reports as Listings Submenu.
     *
     * @link  https://shellcreeper.com/how-to-add-wordpress-cpt-admin-menu-as-sub-menu/
     * @since 1.7.0
     */
    public function add_reports_as_listings_submenu() {
        $cpt_obj = get_post_type_object( 'case27_report' );
        if ( ! is_object( $cpt_obj ) || $cpt_obj === null ) {
            return;
        }

        add_submenu_page(
            'edit.php?post_type=job_listing',   // Parent slug.
            $cpt_obj->labels->name,             // Page title.
            _x( 'Reported Listings', 'Reported Listings menu title in wp-admin', 'my-listing' ), // Menu title.
            $cpt_obj->cap->edit_posts,          // Capability.
            'edit.php?post_type=case27_report' // Menu slug.
        );
    }

    /**
     * Modify the columns for the reports post type page in backend.
     *
     * @since 1.0
     */
    public function admin_columns( $columns ) {
        unset( $columns['title'] );

        $columns = [
            'cb' => $columns['cb'],
            'reported_listing' => _x( 'Listing', 'Reported listings', 'my-listing' ),
            'report_reason' => _x( 'Reason', 'Reported listings', 'my-listing' ),
            'reported_by' => _x( 'Reported By', 'Reported listings', 'my-listing' ),
            'date' => $columns['date'],
            'report_actions' => _x( 'Actions', 'Reported listings', 'my-listing' ),
        ];

        return $columns;
    }

    /**
     * Add content for the custom columns.
     *
     * @since 1.0
     */
    public function admin_columns_content( $column, $post_id ) {
        switch ( $column ) {
            case 'reported_listing':
                $listingID = get_post_meta( $post_id, '_report_listing_id', true );
                echo $listingID ? esc_html( get_the_title( $listingID ) ) : ( '<em>' . _x( 'This listing does not exist.', 'Reported listings', 'my-listing' ) . '</em>' );
                break;

            case 'report_reason':
                echo c27()->the_text_excerpt( get_post_meta( $post_id, '_report_content', true ), 200 );
                break;

            case 'reported_by':
                $userID = get_post_meta( $post_id, '_report_user_id', true );
                $user = $userID ? get_user_by( 'id', $userID ) : false;

                echo $user ? $user->data->display_name : ( '<em>' . _x( 'This account does not exist.', 'Reported listings', 'my-listing' ) . '</em>' );
            break;

            case 'report_actions':
                $listingID = get_post_meta( $post_id, '_report_listing_id', true );
                $review_link = $listingID ? get_permalink( $listingID ) : false;

                if ( $review_link ) {
                    printf( '<a href="%1$s" class="button button-primary button-large" title="%2$s" target="_blank"><i class="fa fa-eye"></i></a> ', $review_link, _x( 'Review Listing', 'Reported listings', 'my-listing' ) );
                }

                printf( '<a href="%1$s" class="button button-large" title="%2$s"><i class="icon-pencil-2"></i></a> ',  get_edit_post_link( $post_id ), _x( 'View Report', 'Reported listings', 'my-listing' ) );
                printf( '<a href="%1$s" class="button button-large" title="%2$s"><i class="fa fa-check"></i></a>',  get_delete_post_link( $post_id ), _x( 'Close Report', 'Reported listings', 'my-listing' ) );
                break;
        }
    }

    /**
     * Add report details metabox in admin backend.
     *
     * @since 1.0
     */
	public function init_metabox() {
        add_action( 'add_meta_boxes', [ $this, 'add_metabox' ] );
	}

    /**
     * Add report details metabox content in admin backend.
     *
     * @since 1.0
     */
    public function add_metabox() {
        add_meta_box(
            'case27-report',
            _x( 'Report Details', 'Reported listings', 'my-listing' ),
            [ $this, 'render_metabox' ],
            'case27_report',
            'advanced',
            'high'
        );
    }

    /**
     * Renders the report details meta box.
     *
     * @since 1.0
     */
    public function render_metabox( $post ) {
        // Add nonce for security and authentication.
        wp_nonce_field( 'reported_listings_nonce', 'custom_nonce' );

        require locate_template( 'templates/admin/reported-listings/report.php' );
    }

    /**
     * Ajax endpoint for submitting a report.
     *
     * @since 1.0
     */
    public function report_listing() {
        // security check
        check_ajax_referer( 'c27_ajax_nonce', 'security' );

        $listing_id = ! empty( $_POST['listing_id'] ) ? (int) $_POST['listing_id'] : false;
        $report_content = ! empty( $_POST['content'] ) ? sanitize_textarea_field( $_POST['content'] ) : false;
        $user_id = get_current_user_id();

        if ( ! ( $listing_id && $report_content ) ) {
            wp_send_json( [
                'status' => 'error',
                'message' => _x( 'Please fill in all the necessary data.', 'Reported listings', 'my-listing' )
            ] );
        }

        // user must be logged in
        if ( ! ( is_user_logged_in() && $user_id ) ) {
            wp_send_json( [
                'status' => 'error',
                'message' => _x( 'You need to be logged in to perform this action.', 'Reported listings', 'my-listing' )
            ] );
        }

        // make sure a report from this user on this listings doesn't already exist
        $report_exists = get_posts( [
            'post_type' => 'case27_report',
            'post_status' => 'publish',
            'meta_query' => [
                [ 'key' => '_report_listing_id', 'value' => $listing_id ],
                [ 'key' => '_report_user_id', 'value' => $user_id ],
            ],
        ] );

        if ( $report_exists ) {
            wp_send_json( [
                'status' => 'error',
                'message' => _x( 'You\'ve already reported this listing. It is currently being reviewed.', 'Reported listings', 'my-listing' )
            ] );
        }

        // insert report
        $report_id = wp_insert_post( [
            'post_type' => 'case27_report',
            'post_author' => $user_id,
            'post_title' => _x( 'New user report submitted.', 'Reported listings', 'my-listing' ),
            'post_status' => 'publish',
            'meta_input' => [
                '_report_listing_id' => $listing_id,
                '_report_user_id' => $user_id,
                '_report_content' => $report_content,
            ],
        ] );

        // error inserting report
        if ( ! $report_id || is_wp_error( $report_id ) ) {
            wp_send_json( [
                'status' => 'error',
                'message' => _x( 'There was an error with processing your request.', 'Reported listings', 'my-listing' )
            ] );
        }

        // success
        wp_send_json( [
            'status' => 'success',
            'message' => _x( 'Your report was submitted successfully. It will be reviewed by our team.', 'Reported listings', 'my-listing' )
        ] );
    }
}
