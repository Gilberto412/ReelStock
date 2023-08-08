<?php
namespace Jet_Reviews\Endpoints;

use Jet_Reviews\Reviews\Data as Reviews_Data;

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}
/**
 * Define Posts class
 */
class Update_Review extends Base {

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
		return 'update-review';
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

		$query = Reviews_Data::get_instance()->update_review( $data );

		if ( ! $query ) {
			return rest_ensure_response( array(
				'success' => false,
				'message' => __( 'Review Update Error', 'jet-reviews' ),
			) );
		}

		return rest_ensure_response( array(
			'success'  => true,
			'message' => __( 'Review data have been updated', 'jet-reviews' ),
		) );
	}

}
