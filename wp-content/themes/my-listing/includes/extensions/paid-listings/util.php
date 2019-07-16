<?php

namespace MyListing\Ext\Paid_Listings;

class Util {
	public static function get_package_tree_for_listing_type( $type ) {
		$package_ids = array_column( $type->get_packages(), 'package' );
		$tree = [];

		// Get the products that are allowed for this listing type.
		$products = case27_paid_listing_get_products( array(
			'post__in' => $package_ids,
			'product_objects' => true,
		) );

		// Get user bought packages that are allowed for this listing type.
		$packages = self::get_current_user_packages( $package_ids );

		foreach ( (array) $products as $product ) {
			// Skip if not the right product type or not purchaseable.
			if ( ! $product->is_type( [ 'job_package', 'job_package_subscription' ] ) ) {
				continue;
			}

			// Item data.
			$item = [];
			$item['product'] = $product;
			$item['packages'] = [];

			// Get owned packages of this product.
			foreach ( (array) $packages as $key => $package ) {
				if ( absint( $package->get_product_id() ) !== absint( $product->get_id() ) ) {
					continue;
				}

				$item['packages'][] = $package;
				unset( $packages[ $key ] );
			}

			$item['title'] = $product->get_name();
			$item['description'] = $product->get_description();
			$item['featured'] = false;
			$item['image'] = false;

			// If a custom title, description, or other options are set on this product
			// for this specific listing type, then replace the default ones with the custom one.
			if ( $type && ( $_package = $type->get_package( $product->get_id() ) ) ) {
				$item['title'] = $_package['label'] ?: $item['title'];
				$item['featured'] = $_package['featured'] ?: $item['featured'];

				// Split the description textarea into new lines,
				// so it can later be reconstructed to an html list.
				$item['description'] = $_package['description'] ? preg_split( '/\r\n|[\r\n]/', $_package['description'] ) : $item['description'];
			}

			// Get product image.
			$_product_image = get_field( 'pricing_plan_image', $product->get_id() );
			if ( is_array( $_product_image ) && ! empty( $_product_image['sizes'] ) && ! empty( $_product_image['sizes']['large'] ) ) {
				$item['image'] = $_product_image['sizes']['large'];
			}

			$tree[ $product->get_id() ] = $item;
		}

		return $tree;
	}

	public static function get_current_user_packages( $product_ids = [], $format = 'object' ) {
		// Get packages.
		$package_ids = case27_paid_listing_get_user_packages( [
			'post_status'  => 'publish', // Exclude full packages.
			'meta_query' => [
				'relation' => 'AND',
				[
					'key'     => '_user_id',
					'value'   => get_current_user_id(),
					'compare' => 'IN',
				],
				[
					'key'     => '_product_id',
					'value'   => $product_ids,
					'compare' => 'IN',
				],
			],
		] );

		if ( $format === 'ids' ) {
			return $package_ids;
		}

		// Set package object.
		$packages = [];
		foreach ( $package_ids as $package_id ) {
			$packages[ $package_id ] = case27_paid_listing_get_package( $package_id );
		}

		return $packages;
	}

	/**
	 * Check if a package can be
	 * used in the given listing type.
	 *
	 * @since 2.0
	 * @param (int) $package_id   ID of the user package or wc-product.
	 * @param (str) $listing_type Listing type slug.
	 *
	 * @return bool $valid
	 */
	public static function validate_package( $package_id, $listing_type ) {
		if ( ! ( $type_obj = ( get_page_by_path( $listing_type, OBJECT, 'case27_listing_type' ) ) ) ) {
			return false;
		}

		$package = get_post( $package_id );
		$type = \MyListing\Ext\Listing_Types\Listing_Type::get( $type_obj );
		$allowed_product_ids = array_column( $type->get_packages(), 'package' );

		// Paid packages disabled for listing type.
		if ( $type->settings['packages']['enabled'] === false ) {
			return false;
		}

		// Couldn't retrieve package post object.
		if ( ! $package ) {
			return false;
		}

		// Package is a wc-product.
		if ( $package->post_type === 'product' ) {
			$product = wc_get_product( $package->ID );
			if ( ! $product || ! ( $product->is_type( [ 'job_package', 'job_package_subscription' ] ) ) ) {
				return false;
			}

			// Make sure this product type is allowed in the listing type.
			// If no products have been set in the listing type, then allow all.
			if ( ! empty( $allowed_product_ids ) && ! in_array( $product->get_id(), $allowed_product_ids ) ) {
				return false;
			}

			return true;
		}

		// Package is a case27_user_package
		if ( $package->post_type === 'case27_user_package' && is_user_logged_in() ) {
			$allowed_package_ids = self::get_current_user_packages( $allowed_product_ids, 'ids' );

			// If no products have been set in the listing type, then allow all.
			if ( ! empty( $allowed_product_ids ) && ! in_array( $package->ID, $allowed_package_ids ) ) {
				return false;
			}

			return true;
		}

		// Package post-type is not a product or user package, invalidate.
		return false;
	}

	public static function assign_package_to_listing( $package_id, $listing_id ) {
		$package = get_post( $package_id );
		$listing = \MyListing\Src\Listing::get( $listing_id );
		if ( ! $package || ! $listing || ! $listing->editable_by_current_user() ) {
			return false;
		}

		// Handle listings in preview mode.
		if ( $listing->get_data('post_status') === 'preview' ) {
			wp_update_post( [
				'ID'            => $listing->get_id(),
				'post_status'   => 'pending_payment',
				'post_date'     => current_time( 'mysql' ),
				'post_date_gmt' => current_time( 'mysql', 1 ),
				'post_author'   => get_current_user_id(),
			] );
		}

		// Package is already owned by the user, assign it to the listing.
		if ( $package->post_type === 'case27_user_package' ) {
			// If this listing already has this user package, and the listing
			// is already published, then return.
			$current_package = get_post_meta( $listing->get_id(), '_user_package_id', true );
			if ( $listing->get_data('post_status') === 'publish' && ! empty( $current_package ) && absint( $current_package ) === absint( $package->ID ) ) {
				return false;
			}

			case27_paid_listing_use_user_package_to_listing( $package->ID, $listing->get_id() );
			return true;
		}

		// Package is a wc-product.
		if ( $package->post_type === 'product' ) {
			$product = wc_get_product( $package->ID );
			if ( ! $product ) {
				return false;
			}

			$skip_checkout = apply_filters( 'mylisting\packages\free\skip-checkout', true ) === true;

			// If `skip-checkout` setting is enabled for free products,
			// create the user package and assign it to the listing.
			if ( $product->get_price() == 0 && $skip_checkout && $product->get_meta( '_disable_repeat_purchase' ) !== 'yes' ) {
				$user_package_id = case27_paid_listing_add_package( [
					'user_id'    => get_current_user_id(),
					'product_id' => $product->get_id(),
					'duration'   => $product->get_duration(),
					'limit'      => $product->get_limit(),
					'featured'   => $product->is_listing_featured(),
				] );

				if ( ! $user_package_id ) {
					return false;
				}

				// Assign user package to listing.
				case27_paid_listing_use_user_package_to_listing( $user_package_id, $listing->get_id() );
				return true;
			}

			// Otherwise, add the product to cart.
			case27_paid_listing_use_product_to_listing( $product->get_id(), $listing->get_id() );
			return true;
		}

		// Invalid package.
		return false;
	}
}