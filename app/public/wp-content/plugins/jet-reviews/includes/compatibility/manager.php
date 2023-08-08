<?php
namespace Jet_Reviews\Compatibility;

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
	private $registered_plugins = array();

	/**
	 * [__construct description]
	 */
	public function __construct() {

		$this->load_files();

		$this->registered_plugins = apply_filters( 'jet-reviews/compatibility-manager/registered-plugins', array(
			'woocommerce' => array(
				'class'    => '\\Jet_Reviews\\Compatibility\\Woocommerce',
				'instance' => false,
			),
			'jet-engine' => array(
				'class'    => '\\Jet_Reviews\\Compatibility\\Jet_Engine',
				'instance' => true,
			),
		) );

		$this->load_plugin_modules();
	}

	/**
	 * [load_files description]
	 * @return [type] [description]
	 */
	public function load_files() {
		require jet_reviews()->plugin_path( 'includes/compatibility/plugins/woocommerce/woocommerce.php' );
		require jet_reviews()->plugin_path( 'includes/compatibility/plugins/jet-engine/jet-engine.php' );
	}

	/**
	 * [maybe_load_theme_module description]
	 * @return [type] [description]
	 */
	public function load_plugin_modules() {

		$this->registered_plugins = array_map( function( $plugin_data ) {
			$class = $plugin_data['class'];

			if ( ! $plugin_data['instance'] && class_exists( $class ) ) {
				$plugin_data['instance'] = new $class();
			}

			return $plugin_data;
		}, $this->registered_plugins );

	}

}
