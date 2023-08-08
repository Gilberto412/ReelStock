<?php
namespace Jet_Theme_Core\Compatibility;

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Compatibility Manager
 */
class Polylang {

	/**
	 * set_pll_translated_location_id
	 *
	 * @param $post_id
	 *
	 * @return false|int|null
	 */
	public function set_pll_translated_location_id( $post_id ) {

		if ( function_exists( 'pll_get_post' ) ) {

			$translation_post_id = pll_get_post( $post_id );

			if ( null === $translation_post_id ) {
				// the current language is not defined yet
				return $post_id;
			} elseif ( false === $translation_post_id ) {
				//no translation yet
				return $post_id;
			} elseif ( $translation_post_id > 0 ) {
				// return translated post id
				return $translation_post_id;
			}
		}

		return $post_id;
	}

	/**
	 * [__construct description]
	 */
	public function __construct() {

		if ( ! defined( 'POLYLANG_VERSION' ) ) {
			return false;
		}

		add_filter( 'jet-theme-core/location/render/template-id', array( $this, 'set_pll_translated_location_id' ) );
	}

}
