<?php
namespace Jet_Theme_Core\Endpoints;

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}
/**
 * Define Posts class
 */
class Get_Product_Tags extends Base {

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
		return 'get-product-tags';
	}

	/**
	 * @return array[]
	 */
	public function get_args() {
		return array(
			'query' => array(
				'default'    => '',
				'required'   => false,
			),
			'values' => array(
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
		$query = isset( $args['query'] ) ? $args['query'] : '';
		$values = isset( $args['values'] ) ? $args['values'] : [];
		$tags = \Jet_Theme_Core\Utils::get_terms_by_tax( 'product_tag', $query, $values );

		return rest_ensure_response( $tags );
	}

}
