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
class Update_Comment extends Base {

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
		return 'update-comment';
	}

	/**
	 * Returns arguments config
	 *
	 * @return [type] [description]
	 */
	public function get_args() {

		return array(
			'data' => array(
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

		$data = $args['data'];

		$query = Comments_Data::get_instance()->update_comment( $data );

		if ( ! $query ) {
			return rest_ensure_response( array(
				'success' => false,
				'message' => __( 'Comment Update Error', 'jet-reviews' ),
			) );
		}

		return rest_ensure_response( array(
			'success'  => true,
			'message' => __( 'Comment have been updated', 'jet-reviews' ),
		) );
	}

}
