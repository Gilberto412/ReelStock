<?php
namespace Jet_Theme_Core\Endpoints;

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}
/**
 * Define Posts class
 */
class Get_Structure_Template_List extends Base {

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
		return 'get-structure-template-list';
	}

	public function get_args() {
		return array(
			'type' => array(
				'default'    => false,
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

		$type = $args['type'];

		$template_list = jet_theme_core()->templates->get_template_list();

		$template_list = array_filter( $template_list, function( $template ) use ( $type ) {
			return $template['type'] == $type;
		} );

		return rest_ensure_response( [
			'success' => true,
			'message' => __( 'Success', 'jet-theme-core' ),
			'data'    => $template_list,
		] );
	}

}
