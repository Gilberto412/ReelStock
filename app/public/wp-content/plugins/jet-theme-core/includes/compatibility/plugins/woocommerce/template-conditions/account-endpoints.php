<?php
namespace Jet_Theme_Core\Template_Conditions;

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

class Woo_Account_Endpoints extends Base {

	/**
	 * Condition slug
	 *
	 * @return string
	 */
	public function get_id() {
		return 'woo-account-endpoints';
	}

	/**
	 * Condition label
	 *
	 * @return string
	 */
	public function get_label() {
		return __( 'Account Endpoints', 'jet-theme-core' );
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
		return 'woocommerce-my-account';
	}

	/**
	 * @return int
	 */
	public function get_priority() {
		return 58;
	}

	/**
	 * @return string
	 */
	public function get_body_structure() {
		return 'jet_account_page';
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
				'label' => __( 'Dashboard', 'jet-theme-core' ),
				'value' => 'dashboard',
			],
			[
				'label' => __( 'Orders', 'jet-theme-core' ),
				'value' => 'orders',
			],
			[
				'label' => __( 'View Order', 'jet-theme-core' ),
				'value' => 'view-order',
			],
			[
				'label' => __( 'Downloads', 'jet-theme-core' ),
				'value' => 'downloads',
			],
			[
				'label' => __( 'Edit Account', 'jet-theme-core' ),
				'value' => 'edit-account',
			],
			[
				'label' => __( 'Edit Address', 'jet-theme-core' ),
				'value' => 'edit-address',
			],
			[
				'label' => __( 'Payment Methods', 'jet-theme-core' ),
				'value' => 'payment-methods',
			],
			[
				'label' => __( 'Lost Password', 'jet-theme-core' ),
				'value' => 'lost-password',
			],
			[
				'label' => __( 'Customer Logout', 'jet-theme-core' ),
				'value' => 'customer-logout',
			],
		];
	}

	/**
	 * Condition check callback
	 *
	 * @return bool
	 */
	public function check( $args ) {

		if ( ! is_account_page() ) {
			return false;
		}

		if ( empty( $args ) ) {
			return false;
		}

		if ( ! is_wc_endpoint_url() && in_array( 'dashboard', $args ) ) {
			return true;
		}

		foreach ( $args as $endpoint ) {

			if ( is_wc_endpoint_url( $endpoint ) ) {
				return true;
			}
		}

		return false;
	}

}
