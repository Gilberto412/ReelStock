<?php
namespace Jet_Reviews\User\Verifications;

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

class Product_Customer extends Base_Verification {

	/**
	 * [$slug description]
	 * @var string
	 */
	private $slug = 'product-customer';

	/**
	 * [$name description]
	 * @var boolean
	 */
	private $name = false;

	/**
	 * [$icon description]
	 * @var boolean
	 */
	private $icon = false;

	/**
	 * [$invalid_message description]
	 * @var boolean
	 */
	private $message = false;

	/**
	 * [__construct description]
	 */
	public function __construct() {
		$this->name = __( 'Woo Product Customer', 'jet-reviews' );
		$this->icon = false;
		$this->message = __( 'Verified owner', 'jet-reviews' );
	}

	/**
	 * [get_slug description]
	 * @return [type] [description]
	 */
	public function get_slug() {
		return $this->slug;
	}

	/**
	 * [get_slug description]
	 * @return [type] [description]
	 */
	public function get_name() {
		return $this->name;
	}

	/**
	 * [get_valid_message description]
	 * @return [type] [description]
	 */
	public function get_icon() {
		return apply_filters( 'jet-reviews/user/verification/successful-icon/{$this->slug}', $this->icon, $this );
	}

	/**
	 * [get_valid_message description]
	 * @return [type] [description]
	 */
	public function get_message() {
		return apply_filters( 'jet-reviews/user/verification/successful-message/{$this->slug}', $this->message, $this );
	}

	/**
	 * [check description]
	 * @return [type] [description]
	 */
	public function check( $args = array() ) {

		if ( ! isset( $args['user_id'] ) || ! isset( $args['post_id'] ) ) {
			return false;
		}

		$user_id = $args['user_id'];
		$post_id = $args['post_id'];

		$user_data = jet_reviews()->user_manager->get_raw_user_data( $user_id );

		$post_type = get_post_type( $post_id );

		if ( 'product' !== $post_type ) {
			return false;
		}

		$customer_id   = $user_data['id'];
		$customer_mail = $user_data['mail'];

		if ( in_array( 'guest', $user_data['roles'] ) ) {

			global $wpdb;

			$customer_data_query = $wpdb->get_row( $wpdb->prepare(
				"SELECT customer_id FROM wp_wc_customer_lookup WHERE email=%s",
				$customer_mail
			), ARRAY_A );

			if ( ! empty( $customer_data_query ) ) {
				$customer_id = $customer_data_query['customer_id'];
			}
		}

		$bought_product = wc_customer_bought_product( $customer_mail, $customer_id, $post_id );

		return $bought_product;
	}

}
