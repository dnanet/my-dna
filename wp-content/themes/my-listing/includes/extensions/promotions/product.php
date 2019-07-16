<?php
/**
 * Promotion package (WC Product Type).
 *
 * @since 1.7.0
 */

namespace MyListing\Ext\Promotions;

class Product extends \WC_Product_Simple {

	/**
	 * Constructor
	 *
	 * @param int|WC_Product|object $product Product ID, post object, or product object
	 */
	public function __construct( $product ) {
		$this->product_type = 'promotion_package';
		parent::__construct( $product );
	}

	/**
	 * Get product type.
	 *
	 * @since  1.7.0
	 * @return string
	 */
	public function get_type() {
		return 'promotion_package';
	}

	/**
	 * Compatibility function to retrieve product meta.
	 * Simpler than using WC 3 Getter/Setter Method.
	 *
	 * @since 1.7.0
	 *
	 * @param  string $key Meta Key.
	 * @return mixed
	 */
	public function get_product_meta( $key ) {
		return $this->get_meta( '_' . $key );
	}

	/**
	 * It's a virtual product. No shipping.
	 *
	 * @since  1.7.0
	 * @return boolean
	 */
	public function is_virtual() {
		return true;
	}

	/**
	 * Listing package is only sold individually.
	 *
	 * @since 1.7.0
	 */
	public function is_sold_individually() {
		return true;
	}


	/**
	 * Get promotion duration.
	 *
	 * @since  1.7.0
	 * @return int Amount of days listing will be prommoted.
	 */
	public function get_duration() {
		$duration = $this->get_product_meta( 'promotion_duration' );
		return absint( $duration ?: 14 );
	}

	/**
	 * Get the priority level that will be given to listings.
	 *
	 * @since  1.7.0
	 * @return int
	 */
	public function get_priority() {
		$priority = $this->get_product_meta( 'promotion_priority' );
		return absint( $priority ?: 2 );
	}
}
