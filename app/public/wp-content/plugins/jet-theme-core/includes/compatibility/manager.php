<?php
namespace Jet_Theme_Core\Compatibility;

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Compatibility Manager
 */
class Manager {

	/**
	 * [$registered_subpage_modules description]
	 * @var array
	 */
	private $registered_mobules = array();


	/**
	 * [__construct description]
	 */
	public function __construct() {

		$this->load_files();

		$this->registered_mobules = apply_filters( 'jet-theme-core/compatibility-manager/registered-plugins', array(
			'jet-style-manager' => array(
				'class'    => '\\Jet_Theme_Core\\Compatibility\\Jet_Style_Manager',
				'instance' => false,
				'path'     => jet_theme_core()->plugin_path( 'includes/compatibility/plugins/jet-style-manager/manager.php' ),
			),
			'polylang' => array(
				'class'    => '\\Jet_Theme_Core\\Compatibility\\Polylang',
				'instance' => false,
				'path'     => jet_theme_core()->plugin_path( 'includes/compatibility/plugins/polylang/manager.php' ),
			),
			'wpml' => array(
				'class'    => '\\Jet_Theme_Core\\Compatibility\\WPML',
				'instance' => false,
				'path'     => jet_theme_core()->plugin_path( 'includes/compatibility/plugins/wpml/manager.php' ),
			),
			'woocommerce' => array(
				'class'    => '\\Jet_Theme_Core\\Compatibility\\Woocommerce',
				'instance' => false,
				'path'     => jet_theme_core()->plugin_path( 'includes/compatibility/plugins/woocommerce/manager.php' ),
			),
			'jet-woo-builder' => array(
				'class'    => '\\Jet_Theme_Core\\Compatibility\\Jet_Woo_Builder',
				'instance' => false,
				'path'     => jet_theme_core()->plugin_path( 'includes/compatibility/plugins/jet-woo-builder/manager.php' ),
			),
			'astra-theme' => array(
				'class'    => '\\Jet_Theme_Core\\Compatibility\\Astra_Theme',
				'instance' => false,
				'path'     => jet_theme_core()->plugin_path( 'includes/compatibility/themes/astra/manager.php' ),
			),
		) );

		$this->load_compatibility_modules();
	}

	/**
	 * [load_files description]
	 * @return [type] [description]
	 */
	public function load_files() {}

	/**
	 * [maybe_load_theme_module description]
	 * @return [type] [description]
	 */
	public function load_compatibility_modules() {

		$this->registered_mobules = array_map( function( $module_data ) {
			$class = $module_data['class'];

			if ( file_exists( $module_data['path'] ) ) {
				require $module_data['path'];
			}

			if ( ! $module_data['instance'] && class_exists( $class ) ) {
				$module_data['instance'] = new $class();
			}

			return $module_data;
		}, $this->registered_mobules );

	}

}
