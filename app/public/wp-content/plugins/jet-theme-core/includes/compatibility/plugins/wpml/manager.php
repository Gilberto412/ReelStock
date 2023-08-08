<?php
namespace Jet_Theme_Core\Compatibility;

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Compatibility Manager
 */
class WPML {

	/**
	 * Set WPML translated location.
	 *
	 * @param $post_id
	 *
	 * @return mixed|void
	 */
	public function set_wpml_translated_location_id( $post_id ) {
		$location_type = get_post_type( $post_id );

		return apply_filters( 'wpml_object_id', $post_id, $location_type, true );
	}

	/**
	 * [__construct description]
	 */
	public function __construct() {

		if ( ! defined( 'WPML_ST_VERSION' ) ) {
			return false;
		}

		add_filter( 'jet-theme-core/location/render/template-id', array( $this, 'set_wpml_translated_location_id' ) );
	}

}
