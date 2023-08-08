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
class Add_Review_Type extends Base {

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
		return 'add-review-type';
	}

	/**
	 * Returns arguments config
	 *
	 * @return [type] [description]
	 */
	public function get_args() {

		return array(
			'name' => array(
				'default'    => '',
				'required'   => false,
			),
			'slug' => array(
				'default'    => '',
				'required'   => false,
			),
			'fields' => array(
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

		$name   = $args['name'];
		$slug   = $args['slug'];
		$fields = $args['fields'];

		$prepared_data = array(
			'name'   => $name,
			'slug'   => $slug,
			'fields' => maybe_serialize( $fields ),
		);

		$is_exist = Reviews_Data::get_instance()->is_review_type_exist( $slug );

		if ( $is_exist ) {
			return rest_ensure_response( array(
				'success' => false,
				'message' => __( 'Type with this slug is already exist', 'jet-reviews' ),
			) );
		}

		$insert_id = Reviews_Data::get_instance()->add_new_review_type( $prepared_data );

		if ( ! $insert_id ) {
			return rest_ensure_response( array(
				'success' => false,
				'message' => __( 'Error', 'jet-reviews' ),
			) );
		}

		return rest_ensure_response( array(
			'success' => true,
			'message' => __( 'New Review type has been created', 'jet-reviews' ),
			'data'    => array(
				'insert_id' => $insert_id,
			),
		) );
	}

}
