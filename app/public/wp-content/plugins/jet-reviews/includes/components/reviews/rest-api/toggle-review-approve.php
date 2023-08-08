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
class Toggle_Review_Approve extends Base {

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
		return 'toggle-review-approve';
	}

	/**
	 * Returns arguments config
	 *
	 * @return [type] [description]
	 */
	public function get_args() {
		return array(
			'itemsList' => array(
				'default'    => [],
				'required'   => false,
			),
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
		$itemsList = isset( $args['itemsList'] ) ? $args['itemsList'] : [];
		$table_name = jet_reviews()->db->tables( 'reviews', 'name' );

		foreach ( $itemsList as $itemData ) {
			$query = jet_reviews()->db->wpdb()->update(
				$table_name,
				array(
					'approved' => filter_var( $itemData['approved'], FILTER_VALIDATE_BOOLEAN ) ? 1 : 0,
				),
				array(
					'id' => $itemData['id'],
				)
			);

			if ( false === $query ) {
				return rest_ensure_response( array(
					'success' => false,
					'message' => __( 'Error', 'jet-reviews' ),
				) );
			}
		}

		$params = $args['pageArgs'];

		$page = isset( $params['page'] ) ? $params['page'] : 0;
		$page_size = isset( $params['page_size'] ) ? $params['page_size'] : 20;
		$title = isset( $params['title'] ) ? $params['title'] : '';
		$post_type = isset( $args['post_type'] ) ? $args['post_type'] : '';

		$reviews_query = Reviews_Data::get_instance()->get_admin_reviews_list_by_page( false, $page, $page_size, $title, $post_type );

		return rest_ensure_response( array(
			'success' => true,
			'message' => __( 'Success', 'jet-reviews' ),
			'data'    => array(
				'page_list' => $reviews_query['page_list'],
				'total_count' => $reviews_query['total_count'],
			),
		) );
	}
}
