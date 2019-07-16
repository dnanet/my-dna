<?php
/**
 * Allow switching the listing package.
 *
 * @since 1.0
 */

namespace MyListing\Ext\Paid_Listings;

if ( ! defined('ABSPATH') ) {
	exit;
}

class Switch_Package {
	use \MyListing\Src\Traits\Instantiatable;

	public function __construct() {
		add_filter( 'submit_job_steps', [ $this, 'submission_steps' ], 150 );
		add_filter( 'job_manager_my_job_actions', [ $this, 'add_switch_plan_button' ], 10, 2 );
		add_filter( 'mylisting/paid-listings/choose-package/title', [ $this, 'choose_package_title' ] );
		add_filter( 'mylisting/paid-listings/choose-package/description', [ $this, 'choose_package_description' ] );
	}

	public function submission_steps( $steps ) {
		$actions = [ 'switch', 'relist' ];
		if ( empty( $_GET['action'] ) || ! in_array( $_GET['action'], $actions ) ) {
			return $steps;
		}

		return [ 'switch-package' => [
			'name'     => _x( 'Choose a package', 'Switch package', 'my-listing' ),
			'view'     => [ Submission::instance(), 'choose_package' ],
			'handler'  => [ $this, 'choose_package_handler' ],
			'priority' => 5,
		] ];
	}

	public function choose_package_handler() {
		$form = \WP_Job_Manager_Form_Submit_Job::instance();
		$actions = [ 'switch', 'relist' ];

		try {
			if ( ! is_user_logged_in() || empty( $_GET['action'] ) || ! in_array( $_GET['action'], $actions ) ) {
				throw new \Exception( _x( 'Invalid request.', 'Switch package', 'my-listing' ) );
			}

			if ( empty( $_POST['listing_package'] ) || empty( $_GET['listing'] ) ) {
				throw new \Exception( _x( 'Invalid request.', 'Switch package', 'my-listing' ) );
			}

			$action = $_GET['action'];
			$listing = \MyListing\Src\Listing::get( $_GET['listing'] );

			if ( ! ( $listing && $listing->type && $listing->editable_by_current_user() ) ) {
				throw new \Exception( _x( 'Something went wrong.', 'Switch package', 'my-listing' ) );
			}

			if ( ! Util::validate_package( $_POST['listing_package'], $listing->type->get_slug() ) ) {
				throw new \Exception( _x( 'Chosen package is not valid.', 'Switch package', 'my-listing' ) );
			}

			// Package is valid.
			$package = get_post( $_POST['listing_package'] );

			// Assign package to listing.
			$assignment = Util::assign_package_to_listing( $package->ID, $listing->get_id() );
			if ( $assignment === false ) {
				throw new \Exception( _x( 'Couldn\'t assign package to listing.', 'Switch package', 'my-listing' ) );
			}

			// Redirect to user dashboard.
			$message = $action === 'relist'
				? _x( 'Listing has been successfully relisted.', 'Switch Package', 'my-listing' )
				: _x( 'Listing plan has been updated.', 'Switch Package', 'my-listing' );

			wc_add_notice( $message, 'success' );
			wp_safe_redirect( wc_get_account_endpoint_url( 'my-listings' ) );
			exit;
		} catch (\Exception $e) {
			// Log error message.
			$form->add_error( $e->getMessage() );
			$form->set_step( array_search( 'switch-package', array_keys( $form->get_steps() ) ) );
		}
	}

	public function add_switch_plan_button( $actions, $listing ) {
		if ( isset( $actions['relist'] ) ) {
			unset( $actions['relist'] );
		}

		if ( ! ( $listing = \MyListing\Src\Listing::get( $listing ) ) ) {
			return $actions;
		}

		if ( ! in_array( $listing->get_data('post_status'), [ 'publish', 'expired' ] ) ) {
			return $actions;
		}

		if ( ! ( $plans_page = c27()->get_setting( 'general_add_listing_page' ) ) ) {
			return $actions;
		}

		// Paid packages disabled for listing type.
		if ( $listing->type && $listing->type->settings['packages']['enabled'] === false ) {
			return $actions;
		}

		$switch_url = add_query_arg( [
			'action' => $listing->get_data('post_status') === 'publish' ? 'switch' : 'relist',
			'listing' => $listing->get_id(),
		], $plans_page );

		$actions['cts_switch'] = [
			'type' => 'plain',
			'content' => sprintf(
				'<li><a href="%s" class="listing-action-switch">%s</a></li>',
				esc_url( $switch_url ),
				$listing->get_data('post_status') === 'publish'
					? _x( 'Switch Plan', 'User listings dashboard', 'my-listing' )
					: _x( 'Relist', 'User listings dashboard', 'my-listing' )
			),
		];

		if ( ! empty( $actions['delete'] ) ) {
			$delete = $actions['delete'];
			unset( $actions['delete'] );
			$actions = $actions + [ 'delete' => $delete ];
		}

		return $actions;
	}

	public function get_available_packages( $listing ) {
		if ( ! ( $listing = \MyListing\Src\Listing::get( $listing ) ) ) {
			return [];
		}

		// Get user packages.
		$_packages = case27_paid_listing_get_user_packages( [
			'post__not_in' => [ $listing->get_data( '_user_package_id' ) ],
			'post_status'  => 'publish', // Exclude full packages.
			'meta_query' => [
				'relation' => 'AND',
				[
					'key'     => '_user_id',
					'value'   => $listing->get_data( 'post_author' ),
					'compare' => 'IN',
				],
			],
		] );

		if ( ! $_packages ) {
			return [];
		}

		// Allowed Products.
		$allowed_products = [];
		if ( $listing->type ) {
			foreach ( $listing->type->get_packages() as $allowed_package ) {
				$pid = isset( $allowed_package['package'] ) ? $allowed_package['package'] : false;
				if ( $pid ) {
					$allowed_products[] = $pid;
				}
			}
		}

		$packages = [];
		foreach ( $_packages as $package_id ) {
			$package_object = case27_paid_listing_get_package( $package_id );
			if ( ! $package_object->has_package() ) {
				continue;
			}

			if ( $allowed_products && ( $product_id = $package_object->get_product_id() ) ) {
				if ( in_array( $product_id, $allowed_products ) ) {
					$packages[ $package_id ] = $package_object;
				}
			} else {
				$packages[ $package_id ] = $package_object;
			}
		}

		return $packages;
	}

	public function choose_package_title( $title ) {
		if ( empty( $_GET['listing'] ) || ! ( $listing = \MyListing\Src\Listing::get( $_GET['listing'] ) ) ) {
			return $title;
		}

		if ( ! empty( $_GET['action'] ) && $_GET['action'] === 'switch' ) {
			$title = sprintf( 'Switch plan for listing "<a href="%s" target="_blank">%s</a>"', esc_url( $listing->get_link() ), $listing->get_name() );
		}

		return $title;
	}

	public function choose_package_description( $desc ) {
		if ( empty( $_GET['listing'] ) || ! ( $listing = \MyListing\Src\Listing::get( $_GET['listing'] ) ) ) {
			return $desc;
		}

		if ( ! empty( $_GET['action'] ) && $_GET['action'] === 'switch' ) {
			$current_package = case27_paid_listing_get_package( $listing->get_data( '_user_package_id' ) );
			$product = $current_package->get_product();
			if ( $current_package->has_package() && $product ) {
				$desc = sprintf(
					_x( 'Current plan is <a href="%s" title="%s">%s</a>.', 'Switch package', 'my-listing' ),
					esc_url( $product->get_permalink() ),
					esc_attr( sprintf( _x( 'Package #%d', 'Switch Package', 'my-listing' ), $current_package->get_id() ) ),
					$product->get_title()
				);
			}
		}

		return $desc;
	}
}
