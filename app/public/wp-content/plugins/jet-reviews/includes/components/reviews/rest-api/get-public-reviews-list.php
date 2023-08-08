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
class Get_Public_Reviews_List extends Base {

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
		return 'get-public-reviews-list';
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
				'default'    => 0,
				'required'   => false,
			),
			'page' => array(
				'default'    => 0,
				'required'   => false,
			),
			'page_size' => array(
				'default'    => 20,
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

		$source = isset( $args['source'] ) ? $args['source'] : 'post';
		$source_id = isset( $args['source_id'] ) ? $args['source_id'] : false;
		$page = isset( $args['page'] ) ? $args['page'] : 0;
		$page_size = isset( $args['page_size'] ) ? $args['page_size'] : 10;

		$reviews_data = Reviews_Data::get_instance()->get_public_reviews_list( $source, $source_id, $page, $page_size );

		if ( ! $reviews_data ) {
			return rest_ensure_response( array(
				'success' => false,
				'message' => __( 'Error', 'jet-reviews' ),
			) );
		}

		return rest_ensure_response( array(
			'success' => true,
			'message' => __( 'Success', 'jet-reviews' ),
			'data'    => array(
				'list'   => $reviews_data['list'],
				'total'  => $reviews_data['total'],
				'rating' => $reviews_data['rating'],
			),
		) );
	}

}
