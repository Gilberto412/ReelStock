<?php
namespace Jet_Reviews\Compatibility;

use Jet_Reviews\Endpoints as Endpoints;

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Compatibility Manager
 */
class Woocommerce {

	/**
	 * [__construct description]
	 */
	public function __construct() {

		if ( ! class_exists( 'WooCommerce' ) ) {
			return false;
		}

		$this->load_files();

		add_action( 'jet-reviews/rest/init-endpoints', array( $this, 'init_endpoints' ), 10, 1 );

		add_action( 'jet-reviews/user/verifications/register', array( $this, 'add_verification' ) );

		add_filter( 'jet-reviews/settings/registered-subpage-modules', array( $this, 'modify_registered_subpage_modules' ), 10, 2 );

	}

	/**
	 * [load_files description]
	 * @return [type] [description]
	 */
	public function load_files() {
		require jet_reviews()->plugin_path( 'includes/compatibility/plugins/woocommerce/rest-api/pull-product-reviews.php' );
	}

	/**
	 * [init_endpoints description]
	 * @return [type] [description]
	 */
	public function init_endpoints( $rest_api_manager ) {
		$rest_api_manager->register_endpoint( new Endpoints\Pull_Product_Reviews() );
	}

	/**
	 * @param $endpoints_list
	 *
	 * @return mixed
	 */
	public function modify_registered_subpage_modules( $subpage_modules ) {

		$subpage_modules['jet-reviews-woocommerce'] = array(
			'class' => '\\Jet_Reviews\\Settings\\Woocommerce',
			'args'  => array(),
			'path'  => jet_reviews()->plugin_path( 'includes/compatibility/plugins/woocommerce/admin-pages/woocommerce-submodule.php' ),
		);

		return $subpage_modules;
	}

	/**
	 * [add_verification description]
	 */
	public function add_verification( $user_manager ) {

		require_once jet_reviews()->plugin_path( 'includes/components/user/verifications/base.php' );

		$default = array(
			'\Jet_Reviews\User\Verifications\Product_Customer' => jet_reviews()->plugin_path( 'includes/compatibility/plugins/woocommerce/verifications/product-customer.php' ),
			'\Jet_Reviews\User\Verifications\Shop_Manager'     => jet_reviews()->plugin_path( 'includes/compatibility/plugins/woocommerce/verifications/shop-manager.php' ),
		);

		foreach ( $default as $class => $file ) {
			require $file;

			$user_manager->register_verification( $class );
		}
	}

}
