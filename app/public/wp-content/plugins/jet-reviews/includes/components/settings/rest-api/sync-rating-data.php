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
class Sync_Rating_Data extends Base {

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
		return 'sync-rating-data';
	}

	/**
	 * Returns arguments config
	 *
	 * @return [type] [description]
	 */
	public function get_args() {

		return array(
			'postType' => array(
				'default'  => false,
				'required' => false,
			),
		);
	}

	/**
	 * [callback description]
	 * @param  [type]   $request [description]
	 * @return function          [description]
	 */
	public function callback( $request ) {

		$data = $request->get_params();

		$post_type = $data['postType'];

		$sync_data = Reviews_Data::get_instance()->sync_rating_post_meta( $post_type );

		if ( ! $sync_data['success'] ) {
			return rest_ensure_response( [
				'success' => false,
				'message' => __( 'Meta data sync error', 'jet-reviews' ),
				'data'    => $sync_data['data'],
			] );
		}

		return rest_ensure_response( [
			'success' => true,
			'message' => __( 'Post metadata have been updated', 'jet-reviews' ),
			'data'    => $sync_data['data'],
		] );
	}

}
