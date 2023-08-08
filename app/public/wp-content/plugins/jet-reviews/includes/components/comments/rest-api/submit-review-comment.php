<?php
namespace Jet_Reviews\Endpoints;

use Jet_Reviews\Comments\Data as Comments_Data;

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}
/**
 * Define Posts class
 */
class Submit_Review_Comment extends Base {

	/**
	 * [get_method description]
	 * @return [type] [description]
	 */
	public function get_method() {
		return 'POST';
	}

	/**
	 * Returns route name
	 *
	 * @return string
	 */
	public function get_name() {
		return 'submit-review-comment';
	}

	/**
	 * Returns arguments config
	 *
	 * @return [type] [description]
	 */
	public function get_args() {

		return array(
			'source' => array(
				'default'    => 'post',
				'required'   => false,
			),
			'source_id' => array(
				'default'    => '',
				'required'   => false,
			),
			'parent_id' => array(
				'default'    => '',
				'required'   => false,
			),
			'review_id' => array(
				'default'    => '',
				'required'   => false,
			),
			'author_id' => array(
				'default'    => '',
				'required'   => false,
			),
			'author_name' => array(
				'default'    => '',
				'required'   => false,
			),
			'author_mail' => array(
				'default'    => '',
				'required'   => false,
			),
			'content' => array(
				'default'    => '',
				'required'   => false,
			),
			'captcha_token' => array(
				'default'    => '',
				'required'   => false,
			),
		);
	}

	/**
	 * [callback description]
	 * @param  [type]   $request [description]
	 * @return function          [description]
	 */
	public function callback( $request ) {

		$args = $request->get_params();

		$allowed_html = jet_reviews_tools()->get_content_allowed_html();
		$source       = isset( $args['source'] ) ? $args['source'] : 'post';
		$source_id    = isset( $args['source_id'] ) ? $args['source_id'] : 0;

		if ( ! $source_id ) {
			return rest_ensure_response( array(
				'success' => false,
				'message' => __( 'Error', 'jet-reviews' ),
			) );
		}

		$captcha_token = isset( $args['captcha_token'] ) ? $args['captcha_token'] : '';
		$recaptcha_instance = jet_reviews()->integration_manager->get_integration_module_instance( 'recaptcha' );
		$captcha_verify = $recaptcha_instance->maybe_verify( $captcha_token );

		if ( ! $captcha_verify ) {
			return rest_ensure_response( array(
				'success' => false,
				'message' => __( 'Captcha validation failed', 'jet-reviews' ),
			) );
		}

		$source_instance = jet_reviews()->reviews_manager->sources->get_source_instance( $source );
		$source_type = $source_instance->get_type( [
			'source_id' => $source_id,
		] );
		$source_settings = jet_reviews()->settings->get_source_settings_data( $source_instance->get_slug(), $source_type );

		$author_id = isset( $args['author_id'] ) ? $args['author_id'] : '0';
		$author_name = isset( $args['author_name'] ) ? wp_kses( $args['author_name'], 'strip' ) : '';
		$author_mail = isset( $args['author_mail'] ) ? sanitize_email( $args['author_mail'] ) : '';

		$is_guest = false === strpos( $author_id, 'guest' ) ? false : true;

		if ( $is_guest ) {
			$prepared_guest_data = array(
				'guest_id' => $author_id,
				'name'     => $author_name,
				'mail'     => $author_mail,
			);

			$insert_guest_id = jet_reviews()->user_manager->add_new_guest( $prepared_guest_data );
		}

		$comments_need_approve = $source_settings['comments_need_approve'];

		$is_forbidden_content = jet_reviews_tools()->forbidden_text_validation( [ $args['content'] ] );

		if ( $is_forbidden_content ) {
			$comments_need_approve = true;
		}

		$prepared_data = array(
			'source'    => $source,
			'post_id'   => $source_id,
			'parent_id' => isset( $args['parent_id'] ) ? $args['parent_id'] : 0,
			'review_id' => isset( $args['review_id'] ) ? $args['review_id'] : 0,
			'author'    => $author_id,
			'content'   => wp_kses( $args['content'], $allowed_html ),
			'date'      => current_time( 'mysql' ),
			'approved'  => filter_var( $comments_need_approve, FILTER_VALIDATE_BOOLEAN ) ? 0 : 1,
		);

		$insert_id = Comments_Data::get_instance()->submit_review_comment( $prepared_data );

		if ( ! $insert_id ) {
			return rest_ensure_response( array(
				'success' => false,
				'message' => __( 'Error', 'jet-reviews' ),
			) );
		}

		if ( filter_var( $comments_need_approve, FILTER_VALIDATE_BOOLEAN ) ) {
			return rest_ensure_response( array(
				'success' => false,
				'message' => __( '*Your comment must be approved by the moderator', 'jet-reviews' ),
			) );
		}

		$user_data = jet_reviews()->user_manager->get_raw_user_data( $author_id );

		$review_verification_data = jet_reviews()->user_manager->get_verification_data(
			$source_settings['comment_verifications'],
			array(
				'user_id' => $author_id,
				'post_id' => $source_id,
			)
		);

		$return_data = array(
			'id'        => $insert_id,
			'source'    => $source,
			'post_id'   => $source_id,
			'parent_id' => $prepared_data['parent_id'],
			'review_id' => $prepared_data['review_id'],
			'author'    => array(
				'id'     => $user_data['id'],
				'name'   => $user_data['name'],
				'mail'   => $user_data['mail'],
				'avatar' => $user_data['avatar'],
				'roles'  => $user_data['roles'],
			),
			'date'      => array(
				'raw'        => $prepared_data['date'],
				'human_diff' => jet_reviews_tools()->human_time_diff_by_date( $prepared_data['date'] ),
			),
			'content'      => $prepared_data['content'],
			'approved'     => filter_var( $prepared_data['approved'], FILTER_VALIDATE_BOOLEAN ),
			'children'     => array(),
			'verifications' => $review_verification_data,
		);

		return rest_ensure_response( array(
			'success' => true,
			'message' => __( 'New Comment has been saved', 'jet-reviews' ),
			'data'    => $return_data,
		) );
	}

}
