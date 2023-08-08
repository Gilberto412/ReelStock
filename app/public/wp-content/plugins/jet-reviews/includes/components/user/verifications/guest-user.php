<?php
namespace Jet_Reviews\User\Verifications;

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

class Guest_User extends Base_Verification {

	/**
	 * [$slug description]
	 * @var string
	 */
	private $slug = 'guest-user';

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
		$this->name = __( 'Guest User', 'jet-reviews' );
		$this->icon = false;
		$this->message = __( 'Guest', 'jet-reviews' );
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

		if ( ! isset( $args['user_id'] ) ) {
			return false;
		}

		$user_data = jet_reviews()->user_manager->get_raw_user_data( $args['user_id'] );

		if ( in_array( 'guest', $user_data['roles'] ) ) {
			return true;
		}

		return false;
	}

}
