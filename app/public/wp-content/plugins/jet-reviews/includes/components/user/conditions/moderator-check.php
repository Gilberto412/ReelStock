<?php
namespace Jet_Reviews\User\Conditions;

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

class Moderator_Check extends Base_Condition {

	/**
	 * [$slug description]
	 * @var string
	 */
	private $slug = 'moderator-check';

	/**
	 * [$invalid_message description]
	 * @var boolean
	 */
	private $invalid_message = false;

	/**
	 * [__construct description]
	 */
	public function __construct() {
		$this->invalid_message = __( '*Your review must be approved by the moderator', 'jet-reviews' );
	}

	/**
	 * @return string
	 */
	public function get_type() {
		return 'can-review';
	}

	/**
	 * [get_slug description]
	 * @return [type] [description]
	 */
	public function get_slug() {
		return $this->slug;
	}

	/**
	 * [get_valid_message description]
	 * @return [type] [description]
	 */
	public function get_invalid_message() {
		return apply_filters( 'jet-reviews/user/conditions/invalid-message/{$this->slug}', $this->invalid_message, $this );
	}

	/**
	 * [check description]
	 * @return [type] [description]
	 */
	public function check( $source = 'post', $user_data = [] ) {

		if ( empty( $user_data ) ) {
			return true;
		}

		$source_instance = jet_reviews()->reviews_manager->sources->get_source_instance( $source );
		$source_id = $source_instance->get_current_id();

		$table_name = jet_reviews()->db->tables( 'reviews', 'name' );

		$query = jet_reviews()->db->wpdb()->prepare(
			"SELECT * FROM $table_name WHERE source=%s AND post_id=%s AND author=%s AND approved=0",
			$source,
			$source_id,
			$user_data['id']
		);

		$raw_result = jet_reviews()->db->wpdb()->get_results( $query );

		if ( ! empty( $raw_result) ) {
			return false;
		}

		return true;
	}

}
