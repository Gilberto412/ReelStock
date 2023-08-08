<?php
namespace Jet_Reviews\Endpoints;

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}
/**
 * Define Posts class
 */
class Save_Settings extends Base {

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
		return 'save-settings';
	}

	/**
	 * Returns arguments config
	 *
	 * @return [type] [description]
	 */
	public function get_args() {

		return array(
			'settings' => array(
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

		$data = $request->get_params();

		$settings = $data['settings'];

		$current = get_option( jet_reviews()->settings->key, array() );

		if ( is_wp_error( $current ) ) {
			return rest_ensure_response( [
				'success' => false,
				'message' => __( 'Server Error', 'jet-reviews' ),
			] );
		}

		foreach ( $settings as $key => $value ) {
			$current[ $key ] = is_array( $value ) ? $value : esc_attr( $value );
		}

		update_option( jet_reviews()->settings->key, $current );

		return rest_ensure_response( [
			'success' => true,
			'message' => __( 'Settings have been saved', 'jet-reviews' ),
		] );
	}

}
