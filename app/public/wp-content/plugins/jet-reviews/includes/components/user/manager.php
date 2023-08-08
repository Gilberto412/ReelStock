<?php
namespace Jet_Reviews\User;

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Define Base DB class
 */
class Manager {

	/**
	 * A reference to an instance of this class.
	 *
	 * @since 1.0.0
	 * @var   object
	 */
	private static $instance = null;

	/**
	 * [$_conditions description]
	 * @var array
	 */
	public $registered_conditions = array();

	/**
	 * [$register_verifications description]
	 * @var array
	 */
	public $registered_verifications = array();

	/**
	 * Returns the instance.
	 *
	 * @since  1.0.0
	 * @return object
	 */
	public static function get_instance() {

		// If the single instance hasn't been set, set it now.
		if ( null == self::$instance ) {
			self::$instance = new self;
		}
		return self::$instance;
	}

	/**
	 * Constructor for the class
	 */
	public function __construct() {
		$this->register_conditions();
		$this->register_verifications();
	}

	/**
	 * [register_conditions description]
	 * @return [type] [description]
	 */
	public function register_conditions() {

		$base_path = jet_reviews()->plugin_path( 'includes/components/user/conditions/' );

		require $base_path . 'base.php';

		$default = array(
			'\Jet_Reviews\User\Conditions\User_Guest'       => $base_path . 'user-guest.php',
			'\Jet_Reviews\User\Conditions\User_Role'        => $base_path . 'user-role.php',
			'\Jet_Reviews\User\Conditions\Moderator_Check'  => $base_path . 'moderator-check.php',
			'\Jet_Reviews\User\Conditions\Already_Reviewed' => $base_path . 'already-reviewed.php',
		);

		foreach ( $default as $class => $file ) {
			require $file;

			$this->register_condition( $class );
		}

		/**
		 * You could register custom conditions on this hook.
		 * Note - each condition should be presented like instance of class 'Jet_Reviews\User\Conditions\Base_Condition'
		 */
		do_action( 'jet-reviews/user/conditions/register', $this );

	}

	/**
	 * [register_condition description]
	 * @param  [type] $class [description]
	 * @return [type]        [description]
	 */
	public function register_condition( $class ) {

		$instance = new $class;
		$this->registered_conditions[ $instance->get_slug() ] = $instance;
	}

	/**
	 * [get_condition description]
	 * @param  [type] $class [description]
	 * @return [type]        [description]
	 */
	public function get_condition( $slug ) {

		if ( array_key_exists( $slug, $this->registered_conditions ) ) {
			return $this->registered_conditions[ $slug ];
		}

		return false;
	}

	/**
	 * [register_conditions description]
	 * @return [type] [description]
	 */
	public function register_verifications() {

		$base_path = jet_reviews()->plugin_path( 'includes/components/user/verifications/' );

		require_once $base_path . 'base.php';

		$default = array(
			'\Jet_Reviews\User\Verifications\Guest_User' => $base_path . 'guest-user.php',
		);

		foreach ( $default as $class => $file ) {
			require $file;

			$this->register_verification( $class );
		}

		/**
		 * You could register custom conditions on this hook.
		 * Note - each condition should be presented like instance of class 'Jet_Reviews\User\Verifications\Base_Verification'
		 */
		do_action( 'jet-reviews/user/verifications/register', $this );

	}

	/**
	 * [register_condition description]
	 * @param  [type] $class [description]
	 * @return [type]        [description]
	 */
	public function register_verification( $class ) {
		$instance = new $class;
		$this->registered_verifications[ $instance->get_slug() ] = $instance;
	}

	/**
	 * [get_condition description]
	 * @param  [type] $class [description]
	 * @return [type]        [description]
	 */
	public function get_verification( $slug ) {

		if ( array_key_exists( $slug, $this->registered_verifications ) ) {
			return $this->registered_verifications[ $slug ];
		}

		return false;
	}

	/**
	 * [get_verification_data description]
	 * @param  [type] $slug [description]
	 * @return [type]       [description]
	 */
	public function get_verification_data( $verifications = array(), $args = array() ) {

		if ( empty( $verifications ) ) {
			return false;
		}

		$verification_data = array();

		foreach ( $verifications as $key => $slug ) {
			$verification_instance = $this->get_verification( $slug );

			if ( ! $verification_instance ) {
				continue;
			}

			$check = $verification_instance->check( $args );

			if ( ! $check ) {
				continue;
			}

			$verification_data[] = array(
				'slug'    => $verification_instance->get_slug(),
				'icon'    => $verification_instance->get_icon(),
				'message' => $verification_instance->get_message(),
			);
		}

		return $verification_data;
	}

	/**
	 * [get_post_types_options description]
	 * @return [type] [description]
	 */
	public function get_verification_options() {

		$registered_verifications = $this->registered_verifications;

		if ( empty( $registered_verifications ) ) {
			return array();
		}

		$verification_options_options = array();

		foreach ( $registered_verifications as $slug => $verification ) {
			$verification_options_options[] = array(
				'label' => $verification->get_name(),
				'value' => $slug,
			);
		}

		return $verification_options_options;
	}

	/**
	 * @param string $source_type
	 * @param false $user_data
	 *
	 * @return array
	 */
	public function is_user_can_review( $source = 'post', $user_data = false ) {

		if ( ! $user_data ) {
			return [
				'allowed' => true,
				'code'    => 'can_review',
				'message' => __( '*Publish your review', 'jet-reviews' ),
			];
		}

		foreach ( $this->registered_conditions as $slug => $instance ) {

			$condition_type = $instance->get_type();

			if ( 'can-review' !== $condition_type ) {
				continue;
			}

			$instance_check = $instance->check( $source, $user_data );

			if ( ! $instance_check ) {
				return [
					'allowed' => false,
					'code'    => $instance->get_slug(),
					'message' => $instance->get_invalid_message(),
				];
			}
		}

		return [
			'allowed' => true,
			'code'    => 'can_review',
			'message' => __( '*Publish your review', 'jet-reviews' ),
		];
	}

	/**
	 * [get_raw_user_data description]
	 * @return [type] [description]
	 */
	public function get_raw_user_data( $user_id = false ) {

		if ( false === $user_id ) {
			$user_id = get_current_user_id();
		}

		$user_data = get_user_by( 'id', $user_id );

		if ( ! $user_data ) {

			$guest_data = $this->get_guest_by_id( $user_id );

			if ( $guest_data ) {
				return apply_filters( 'jet-reviews/user-manager/raw-user-data', array(
					'id'     => $guest_data['guest_id'],
					'name'   => $guest_data['name'],
					'mail'   => $guest_data['mail'],
					'avatar' => get_avatar( $guest_data['mail'], 64 ),
					'roles'  => [ 'guest' ],
				) );
			}

			return apply_filters( 'jet-reviews/user-manager/raw-user-data', array(
				'id'     => 0,
				'name'   => esc_html__( 'Guest', 'jet-reviews' ),
				'mail'   => 'email@example.com',
				'avatar' => get_avatar( 'email@example.com', 64 ),
				'roles'  => [ 'guest' ],
			) );
		}

		return apply_filters( 'jet-reviews/user-manager/raw-user-data', array(
			'id'     => $user_data->data->ID,
			'name'   => $user_data->data->display_name,
			'mail'   => $user_data->data->user_email,
			'avatar' => get_avatar( $user_data->data->user_email, 64 ),
			'roles'  => array_values( $user_data->roles ),
		) );
	}

	/**
	 * [add_user_reviewed_post_id description]
	 */
	public function update_user_approval_review( $review_id = false, $data = array() ) {

		if ( ! $review_id || empty( $data ) ) {
			return false;
		}

		$raw_user_data = $this->get_raw_user_data();

		/**
		 * is guest check
		 */
		if ( in_array( 'guest', $raw_user_data['roles'] ) ) {
			return false;
		}

		$user_id = $raw_user_data['id'];

		$approval_reviews = get_user_meta( $user_id, 'jet-approval-reviews', true );

		$approval_reviews = ! empty( $approval_reviews ) ? $approval_reviews : array();

		$approval_reviews[ $review_id ] = $data;

		update_user_meta( $user_id, 'jet-approval-reviews', $approval_reviews );

	}

	/**
	 * [add_user_reviewed_post_id description]
	 */
	public function delete_user_approval_review( $review_id = false ) {

		if ( ! $review_id ) {
			return false;
		}

		$raw_user_data = $this->get_raw_user_data();

		/**
		 * is guest check
		 */
		if ( in_array( 'guest', $raw_user_data['roles'] ) ) {
			return false;
		}

		$user_id = $raw_user_data['id'];

		$approval_reviews = get_user_meta( $user_id, 'jet-approval-reviews', true );

		if ( empty( $approval_reviews ) ) {
			return false;
		}

		if ( ! array_key_exists( $review_id, $approval_reviews ) ) {
			return false;
		}

		unset( $approval_reviews[ $review_id ] );

		update_user_meta( $user_id, 'jet-approval-reviews', $approval_reviews );

	}

	/**
	 * [is_post_already_reviewed description]
	 * @return boolean [description]
	 */
	public function get_review_approval_data( $review_id ) {

		$raw_user_data = $this->get_raw_user_data();

		/**
		 * is guest check
		 */
		if ( in_array( 'guest', $raw_user_data['roles'] ) ) {
			return array(
				'like'    => false,
				'dislike' => false,
			);
		}

		$user_id = $raw_user_data['id'];

		$approval_reviews = get_user_meta( $user_id, 'jet-approval-reviews', true );

		if ( empty( $approval_reviews ) ) {
			return array(
				'like'    => false,
				'dislike' => false,
			);
		}

		if ( isset( $approval_reviews[ $review_id ] ) ) {
			return $approval_reviews[ $review_id ];
		}

		return array(
			'like'    => false,
			'dislike' => false,
		);
	}


	/**
	 * [delete_review_by_id description]
	 * @param  integer $id [description]
	 * @return [type]      [description]
	 */
	public function add_new_guest( $data = array() ) {

		if ( empty( $data ) ) {
			return false;
		}

		global $wpdb;

		$table_name = jet_reviews()->db->tables( 'review_guests', 'name' );

		$prepare_data = array(
			'guest_id'  => $data['guest_id'],
			'name'      => $data['name'],
			'mail'      => $data['mail'],
			'phone'     => '',
			'meta_data' => '',
		);

		$count_query = $wpdb->get_var( $wpdb->prepare(
			"SELECT COUNT(*) FROM $table_name WHERE guest_id = %s",
			$prepare_data['guest_id']
		) );

		if ( '0' === $count_query ) {
			$query = $wpdb->insert( $table_name, $prepare_data );
		}

		if ( ! $query ) {
			return false;
		}

		return $wpdb->insert_id;
	}

	/**
	 * [get_guest_by_id description]
	 * @param  boolean $guest_id [description]
	 * @return [type]            [description]
	 */
	public function get_guest_by_id( $guest_id = false ) {

		if ( ! $guest_id ) {
			return false;
		}

		global $wpdb;

		$table_name = jet_reviews()->db->tables( 'review_guests', 'name' );

		$query = $wpdb->get_row( $wpdb->prepare(
			"SELECT * FROM $table_name WHERE guest_id = %s",
			$guest_id
		), ARRAY_A );

		if ( ! $query ) {
			return false;
		}

		return $query;

	}

}
