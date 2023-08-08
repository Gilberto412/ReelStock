<?php
namespace Jet_Reviews\Reviews;

use Jet_Reviews\Endpoints as Endpoints;

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

class Manager {

	/**
	 * A reference to an instance of this class.
	 *
	 * @since 1.0.0
	 * @var   object
	 */
	private static $instance = null;

	/**
	 * [$key description]
	 * @var string
	 */
	private $slug = 'reviews-manager';

	/**
	 * @var null
	 */
	public $sources = null;

	/**
	 * @var null
	 */
	public $data = null;

	/**
	 * [$page description]
	 * @var null
	 */
	public $list_page = null;

	/**
	 * [$types_page description]
	 * @var null
	 */
	public $types_page = null;

	/**
	 * Constructor for the class
	 */
	function __construct() {

		$this->load_files();

		add_action( 'jet-reviews/init', array( $this, 'init' ) );

		add_action( 'jet-reviews/rest/init-endpoints', array( $this, 'init_endpoints' ), 10, 1 );

	}

	/**
	 * [get_slug description]
	 * @return [type] [description]
	 */
	public function get_slug() {
		return $this->slug;
	}

	/**
	 * [load_files description]
	 * @return [type] [description]
	 */
	public function load_files() {

		// admin endpoints
		require jet_reviews()->plugin_path( 'includes/components/reviews/rest-api/get-admin-reviews-list.php' );
		require jet_reviews()->plugin_path( 'includes/components/reviews/rest-api/update-review.php' );
		require jet_reviews()->plugin_path( 'includes/components/reviews/rest-api/delete-review.php' );
		require jet_reviews()->plugin_path( 'includes/components/reviews/rest-api/add-review-type.php' );
		require jet_reviews()->plugin_path( 'includes/components/reviews/rest-api/update-review-type.php' );
		require jet_reviews()->plugin_path( 'includes/components/reviews/rest-api/delete-review-type.php' );
		require jet_reviews()->plugin_path( 'includes/components/reviews/rest-api/toggle-review-approve.php' );

		// public endpoint
		require jet_reviews()->plugin_path( 'includes/components/reviews/rest-api/get-public-reviews-list.php' );
		require jet_reviews()->plugin_path( 'includes/components/reviews/rest-api/submit-review.php' );
		require jet_reviews()->plugin_path( 'includes/components/reviews/rest-api/update-review-approval.php' );

		// general
		require jet_reviews()->plugin_path( 'includes/components/reviews/sources.php' );
		require jet_reviews()->plugin_path( 'includes/components/reviews/data.php' );
		require jet_reviews()->plugin_path( 'includes/components/reviews/render/review-listing-render.php' );

		if ( is_admin() ) {
			require jet_reviews()->plugin_path( 'includes/components/reviews/admin-pages/list-page.php' );
			require jet_reviews()->plugin_path( 'includes/components/reviews/admin-pages/types-page.php' );
		}

	}

	/**
	 * [init description]
	 * @return [type] [description]
	 */
	public function init() {

		$this->sources = new Sources();
		$this->data = new Data();

		if ( is_admin() ) {
			$this->list_page = new List_Page();
			$this->types_page = new Types_Page();
		}

		do_action( 'jet-reviews/reviews-module/init', $this );
	}

	/**
	 * [init_endpoints description]
	 * @return [type] [description]
	 */
	public function init_endpoints( $rest_api_manager ) {
		// admin endpoints
		$rest_api_manager->register_endpoint( new Endpoints\Get_Admin_Reviews_List() );
		$rest_api_manager->register_endpoint( new Endpoints\Update_Review() );
		$rest_api_manager->register_endpoint( new Endpoints\Delete_Review() );
		$rest_api_manager->register_endpoint( new Endpoints\Add_Review_Type() );
		$rest_api_manager->register_endpoint( new Endpoints\Update_Review_Type() );
		$rest_api_manager->register_endpoint( new Endpoints\Delete_Review_Type() );
		$rest_api_manager->register_endpoint( new Endpoints\Toggle_Review_Approve() );

		// public endpoint
		$rest_api_manager->register_endpoint( new Endpoints\Get_Public_Reviews_List() );
		$rest_api_manager->register_endpoint( new Endpoints\Submit_Review() );
		$rest_api_manager->register_endpoint( new Endpoints\Update_Review_Type() );
		$rest_api_manager->register_endpoint( new Endpoints\Update_Review_Approval() );
	}

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
}
