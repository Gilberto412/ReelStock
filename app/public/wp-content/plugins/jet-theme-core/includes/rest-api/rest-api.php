<?php
namespace Jet_Theme_Core;

/**
 * API controller class
 */
// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}
/**
 * Define Controller class
 */
class Rest_Api {

	/**
	 * A reference to an instance of this class.
	 *
	 * @since 1.0.0
	 * @var   object
	 */
	private static $instance = null;

	/**
	 * [$api_namespace description]
	 * @var string
	 */
	public $api_namespace = 'jet-theme-core-api/v2';

	/**
	 * [$_endpoints description]
	 * @var null
	 */
	private $_endpoints = null;

	/**
	 * Returns the instance.
	 *
	 * @since  1.0.0
	 * @return object
	 */
	public static function get_instance() {

		// If the single instance hasn't been set, set it now.
		if ( null == self::$instance ) {
			self::$instance = new self;
		}
		return self::$instance;
	}

	// Here initialize our namespace and resource name.
	public function __construct() {
		$this->load_files();

		add_action( 'rest_api_init', array( $this, 'register_routes' ) );
	}

	/**
	 * [load_files description]
	 * @return [type] [description]
	 */
	public function load_files() {}

	/**
	 * Initialize all JetEngine related Rest API endpoints
	 *
	 * @return [type] [description]
	 */
	public function init_endpoints() {

		$base_path = jet_theme_core()->plugin_path( 'includes/rest-api/endpoints/' );

		require $base_path . 'base.php';

		$default_endpoints = apply_filters( 'jet-theme-core/rest-api/endpoint-list', [
			'\Jet_Theme_Core\Endpoints\Plugin_Settings'            => $base_path . 'plugin-settings.php',
			'\Jet_Theme_Core\Endpoints\Sync_Templates'             => $base_path . 'sync-templates.php',
			'\Jet_Theme_Core\Endpoints\Get_Page_Templates'         => $base_path . 'get-page-templates.php',
			'\Jet_Theme_Core\Endpoints\Get_Post_Categories'        => $base_path . 'get-post-categories.php',
			'\Jet_Theme_Core\Endpoints\Get_Posts'                  => $base_path . 'get-posts.php',
			'\Jet_Theme_Core\Endpoints\Get_Post_Tags'              => $base_path . 'get-post-tags.php',
			'\Jet_Theme_Core\Endpoints\Get_Post_Types'             => $base_path . 'get-post-types.php',
			'\Jet_Theme_Core\Endpoints\Get_Static_Pages'           => $base_path . 'get-static-pages.php',
			'\Jet_Theme_Core\Endpoints\Get_Template_Conditions'    => $base_path . 'get-template-conditions.php',
			'\Jet_Theme_Core\Endpoints\Update_Template_Conditions' => $base_path . 'update-template-conditions.php',
			'\Jet_Theme_Core\Endpoints\Create_Template'            => $base_path . 'create-template.php',
			'\Jet_Theme_Core\Endpoints\Update_Template_Data'       => $base_path . 'update-template-data.php',
			'\Jet_Theme_Core\Endpoints\Get_Tax_Terms'              => $base_path . 'get-tax-terms.php',

			'\Jet_Theme_Core\Endpoints\Get_Page_Template_List'       => $base_path . 'theme-builder/get-page-template-list.php',
			'\Jet_Theme_Core\Endpoints\Create_Page_Template'         => $base_path . 'theme-builder/create-page-template.php',
			'\Jet_Theme_Core\Endpoints\Copy_Page_Template'           => $base_path . 'theme-builder/copy-page-template.php',
			'\Jet_Theme_Core\Endpoints\Delete_Page_Template'         => $base_path . 'theme-builder/delete-page-template.php',
			'\Jet_Theme_Core\Endpoints\Get_Page_Template_Conditions' => $base_path . 'theme-builder/get-page-template-conditions.php',
			'\Jet_Theme_Core\Endpoints\Update_Page_Template_Data'    => $base_path . 'theme-builder/update-page-template-data.php',
			'\Jet_Theme_Core\Endpoints\Get_Structure_Template_List'  => $base_path . 'theme-builder/get-structure-template-list.php',

		] );

		foreach ( $default_endpoints as $class => $file ) {
			require $file;
			$instance = new $class;
			$this->register_endpoint( $instance );
		}

		do_action( 'jet-theme-core/rest-api/init-endpoints', $this );

	}

	/**
	 * Register new endpoint
	 *
	 * @param  object $endpoint_instance Endpoint instance
	 * @return void
	 */
	public function register_endpoint( $endpoint_instance = null ) {

		if ( $endpoint_instance ) {
			$this->_endpoints[ $endpoint_instance->get_name() ] = $endpoint_instance;
		}

	}

	/**
	 * Returns all registererd API endpoints
	 *
	 * @return [type] [description]
	 */
	public function get_endpoints() {

		if ( null === $this->_endpoints ) {
			$this->init_endpoints();
		}

		return $this->_endpoints;

	}

	/**
	 * Returns endpoints URLs
	 */
	public function get_endpoints_urls() {

		$result    = [];
		$endpoints = $this->get_endpoints();

		foreach ( $endpoints as $endpoint ) {
			$key = str_replace( '-', '', ucwords( $endpoint->get_name(), '-' ) );
			$result[ $key ] = get_rest_url( null, $this->api_namespace . '/' . $endpoint->get_name() . '/' . $endpoint->get_query_params() , 'rest' );
		}

		return $result;

	}

	/**
	 * Returns route to passed endpoint
	 *
	 * @return [type] [description]
	 */
	public function get_route( $endpoint = '', $full = false ) {

		$path = $this->api_namespace . '/' . $endpoint . '/';

		if ( ! $full ) {
			return $path;
		} else {
			return get_rest_url( null, $path );
		}

	}

	// Register our routes.
	public function register_routes() {

		$endpoints = $this->get_endpoints();

		foreach ( $endpoints as $endpoint ) {

			$args = [
				'methods'             => $endpoint->get_method(),
				'callback'            => array( $endpoint, 'callback' ),
				'permission_callback' => array( $endpoint, 'permission_callback' ),
			];

			if ( ! empty( $endpoint->get_args() ) ) {
				$args['args'] = $endpoint->get_args();
			}

			$route = '/' . $endpoint->get_name() . '/' . $endpoint->get_query_params();

			register_rest_route( $this->api_namespace, $route, $args );
		}
	}

}

