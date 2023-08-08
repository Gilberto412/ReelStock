<?php
namespace Jet_Theme_Core\Compatibility;

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Compatibility Manager
 */
class Astra_Theme {

	/**
	 * Include files
	 */
	public function load_files() {}

	/**
	 * Load admin assets
	 *
	 * @param  string $hook Current page hook.
	 * @return void
	 */
	public function register_frontend_styles() {
		wp_register_style(
			'jet-theme-core-astra-theme-styles',
			jet_theme_core()->plugin_url( 'includes/compatibility/themes/astra/assets/css/frontend.css' ),
			[],
			JET_THEME_CORE_VERSION
		);
	}

	/**
	 * @param $dependencies
	 *
	 * @return mixed
	 */
	public function modify_public_styles_dependencies( $dependencies ) {
		$dependencies[] = 'jet-theme-core-astra-theme-styles';

		return $dependencies;
	}

	/**
	 * [__construct description]
	 */
	public function __construct() {

		if ( ! defined( 'ASTRA_THEME_VERSION' ) ) {
			return false;
		}

		$this->load_files();

		add_action( 'wp_enqueue_scripts', [ $this, 'register_frontend_styles' ], 9 );
		add_filter( 'jet-theme-core/assets/public-styles-dependencies', [ $this, 'modify_public_styles_dependencies' ] );
	}

}
