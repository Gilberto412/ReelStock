<?php
namespace Jet_Theme_Core\Template_Conditions;

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

class Woo_Product_Checkout_Endpoints extends Base {

	/**
	 * Condition slug
	 *
	 * @return string
	 */
	public function get_id() {
		return 'woo-product-checkout-endpoints';
	}

	/**
	 * Condition label
	 *
	 * @return string
	 */
	public function get_label() {
		return __( 'Products Checkout Endpoints', 'jet-theme-core' );
	}

	/**
	 * Condition group
	 *
	 * @return string
	 */
	public function get_group() {
		return 'woocommerce';
	}

	/**
	 * @return string
	 */
	public function get_sub_group() {
		return 'woocommerce-сheckout';
	}

	/**
	 * @return int
	 */
	public function get_priority() {
		return 59;
	}

	/**
	 * @return string
	 */
	public function get_body_structure() {
		return 'jet_products_checkout_endpoint';
	}

	/**
	 * [get_control description]
	 * @return [type] [description]
	 */
	public function get_control() {
		return [
			'type'        => 'f-select',
			'placeholder' => __( 'Select endpoint', 'jet-theme-core' ),
		];
	}

	/**
	 * [get_avaliable_options description]
	 * @return [type] [description]
	 */
	public function get_avaliable_options() {
		return [
			[
				'label' => __( 'Pay', 'jet-theme-core' ),
				'value' => 'order-pay',
			],
			[
				'label' => __( 'Order received', 'jet-theme-core' ),
				'value' => 'order-received',
			],
			[
				'label' => __( 'Add payment method', 'jet-theme-core' ),
				'value' => 'add-payment-method',
			],
			[
				'label' => __( 'Delete payment method', 'jet-theme-core' ),
				'value' => 'delete-payment-method',
			],
			[
				'label' => __( 'Set default payment method', 'jet-theme-core' ),
				'value' => 'set-default-payment-method',
			],
		];
	}

	/**
	 * Condition check callback
	 *
	 * @return bool
	 */
	public function check( $args ) {

		if ( ! is_checkout() ) {
			return false;
		}

		if ( empty( $args ) ) {
			return false;
		}

		foreach ( $args as $endpoint ) {

			if ( is_wc_endpoint_url( $endpoint ) ) {
				return true;
			}
		}

		return false;
	}

}
