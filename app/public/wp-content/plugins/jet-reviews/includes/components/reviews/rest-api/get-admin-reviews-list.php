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
class Get_Admin_Reviews_List extends Base {

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
		return 'get-admin-reviews-list';
	}

	/**
	 * Returns arguments config
	 *
	 * @return [type] [description]
	 */
	public function get_args() {

		return array(
			'pageArgs' => array(
				'default'    => [],
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

		if ( empty( $args['pageArgs'] ) ) {
			return rest_ensure_response( array(
				'success' => false,
				'message' => __( 'Error', 'jet-reviews' ),
			) );
		}

		$params = $args['pageArgs'];

		$id = isset( $params['id'] ) ? $params['id'] : false;
		$page = isset( $params['page'] ) ? $params['page'] : 0;
		$page_size = isset( $params['page_size'] ) ? $params['page_size'] : 20;
		$title = isset( $params['title'] ) ? $params['title'] : '';
		$post_type = isset( $args['post_type'] ) ? $args['post_type'] : '';

		$reviews_query = Reviews_Data::get_instance()->get_admin_reviews_list_by_page( $id, $page, $page_size, $title, $post_type );

		if ( ! $reviews_query ) {
			return rest_ensure_response( array(
				'success' => false,
				'message' => __( 'Error', 'jet-reviews' ),
			) );
		}

		return rest_ensure_response( array(
			'success'  => true,
			'message' => __( 'Success', 'jet-reviews' ),
			'data'    => array(
				'page_list' => $reviews_query['page_list'],
				'total_count' => $reviews_query['total_count'],
			),
		) );
	}

}
