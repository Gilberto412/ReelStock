<?php
namespace Jet_Theme_Core\Endpoints;

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}
/**
 * Define Posts class
 */
class Get_Tax_Terms extends Base {

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
		return 'get-tax-terms';
	}

	/**
	 * Returns arguments config
	 *
	 * @return [type] [description]
	 */
	public function get_args() {

		return array(
			'tax_name' => array(
				'default'    => '',
				'required'   => false,
			),
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
		$tax_name = $args['tax_name'];
		$query = isset( $args['query'] ) ? $args['query'] : '';
		$values = isset( $args['values'] ) ? $args['values'] : [];
		$terms_options = \Jet_Theme_Core\Utils::get_terms_options_by_taxonomy( $tax_name, $query, $values );

		return rest_ensure_response( $terms_options );
	}

}
