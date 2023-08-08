<?php
namespace Jet_Reviews\Settings;

use Jet_Reviews\Admin as Admin;
use Jet_Reviews\Endpoints as Endpoints;
use Jet_Reviews\Reviews\Data as Reviews_Data;

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
	private $slug = 'settings-manager';

	/**
	 * [$key description]
	 * @var string
	 */
	public $key = 'jet-reviews-settings';

	/**
	 * [$settings description]
	 * @var null
	 */
	public $settings_page_config = null;

	/**
	 * [$page description]
	 * @var null
	 */
	public $page = null;

	/**
	 * [$subpage_modules description]
	 * @var array
	 */
	public $subpage_modules = array();

	/**
	 * Constructor for the class
	 */
	function __construct() {

		$this->load_files();

		add_action( 'jet-reviews/rest/init-endpoints', array( $this, 'init_endpoints' ), 10, 1 );

		$this->subpage_modules = apply_filters( 'jet-reviews/settings/registered-subpage-modules', array(
			'jet-reviews-post-types' => array(
				'class' => '\\Jet_Reviews\\Settings\\Post_Types',
				'args'  => array(),
				'path'  => jet_reviews()->plugin_path( 'includes/components/settings/admin-pages/post-types-submodule.php' ),
			),
			'jet-reviews-user-source' => array(
				'class' => '\\Jet_Reviews\\Settings\\User_Source',
				'args'  => array(),
				'path'  => jet_reviews()->plugin_path( 'includes/components/settings/admin-pages/user-source-submodule.php' ),
			),
			'jet-reviews-integrations' => array(
				'class' => '\\Jet_Reviews\\Settings\\Integrations',
				'args'  => array(),
				'path'  => jet_reviews()->plugin_path( 'includes/components/settings/admin-pages/integrations-submodule.php' ),
			),
			'jet-reviews-advanced' => array(
				'class' => '\\Jet_Reviews\\Settings\\Advanced',
				'args'  => array(),
				'path'  => jet_reviews()->plugin_path( 'includes/components/settings/admin-pages/advanced-submodule.php' ),
			),
		) );

		if ( is_admin() ) {
			add_action( 'init', array( $this, 'register_settings_category' ), 10 );
			add_action( 'init', array( $this, 'init_plugin_subpage_modules' ), 10 );
		}

		add_action( 'admin_menu', array(  $this, 'register_page' ), 12 );
	}

	/**
	 * [load_files description]
	 * @return [type] [description]
	 */
	public function load_files() {
		require jet_reviews()->plugin_path( 'includes/components/settings/rest-api/save-settings.php' );
		require jet_reviews()->plugin_path( 'includes/components/settings/rest-api/sync-rating-data.php' );;
	}

	/**
	 * Register add/edit page
	 *
	 * @return void
	 */
	public function register_page() {
		add_submenu_page(
			Admin::get_instance()->admin_page_slug,
			__( 'Settings', 'jet-reviews' ),
			__( 'Settings', 'jet-reviews' ),
			'manage_options',
			add_query_arg(
				array(
					'page'    => 'jet-dashboard-settings-page',
					'subpage' => 'jet-reviews-post-types'
				),
				admin_url( 'admin.php' )
			)
		);
	}

	/**
	 * [get_slug description]
	 * @return [type] [description]
	 */
	public function get_slug() {
		return $this->slug;
	}

	/**
	 * [init description]
	 * @return [type] [description]
	 */
	public function register_settings_category() {

		\Jet_Dashboard\Dashboard::get_instance()->module_manager->register_module_category( array(
			'name'     => esc_html__( 'JetReviews', 'jet-reviews' ),
			'slug'     => 'jet-reviews-settings',
			'priority' => 1
		) );
	}

	/**
	 * [init_plugin_subpage_modules description]
	 * @return [type] [description]
	 */
	public function init_plugin_subpage_modules() {

		foreach ( $this->subpage_modules as $subpage => $subpage_data ) {

			if ( file_exists( $subpage_data['path'] ) ) {
				require $subpage_data['path'];
			}

			\Jet_Dashboard\Dashboard::get_instance()->module_manager->register_subpage_module( $subpage, $subpage_data );
		}
	}

	/**
	 * [init_endpoints description]
	 * @return [type] [description]
	 */
	public function init_endpoints( $rest_api_manager ) {
		$rest_api_manager->register_endpoint( new Endpoints\Save_Settings() );
		$rest_api_manager->register_endpoint( new Endpoints\Sync_Rating_Data() );
	}

	/**
	 * [get description]
	 * @param  [type]  $setting [description]
	 * @param  boolean $default [description]
	 * @return [type]           [description]
	 */
	public function get( $setting, $default = false ) {

		if ( null === $this->settings_page_config ) {
			$this->settings_page_config = get_option( $this->key, array() );
		}

		return isset( $this->settings_page_config[ $setting ] ) ? $this->settings_page_config[ $setting ] : $default;
	}

	/**
	 * @param string $source
	 * @param string $source_type
	 *
	 * @return array|false|object|string
	 */
	public function get_source_settings_data( $source = 'post', $source_type = '' ) {

		if ( 'post' === $source ) {
			return $this->get_post_type_data( $source_type );
		}

		$source_instance = jet_reviews()->reviews_manager->sources->get_source_instance( $source );

		if ( ! $source_instance ) {
			return false;
		}

		$source_slug = ! empty( $source_type ) ? $source . '-' . $source_type : $source;

		$option_name = $source_slug . '-source-settings';

		$default_data = [
			'allowed'               => true,
			'name'                  => $source_instance->get_name(),
			'slug'                  => $source_slug,
			'review_type'           => 'default',
			'allowed_roles'         => [
				'administrator',
				'editor',
				'author',
				'contributor',
				'subscriber',
			],
			'verifications'          => [],
			'comment_verifications'  => [],
			'need_approve'          => false,
			'comments_allowed'      => true,
			'comments_need_approve' => false,
			'approval_allowed'      => true,
			'structuredata'         => false,
			'structuredata_type'    => 'Thing',
		];

		$saved_data = $this->get( $option_name, [] );

		return wp_parse_args( $saved_data, $default_data );
	}

	/**
	 * [get_post_type_data description]
	 * @param  [type] $post_type [description]
	 * @return [type]            [description]
	 */
	public function get_post_type_data( $post_type ) {

		$post_types = jet_reviews_tools()->get_post_types();

		$option_name = $post_type . '-type-settings';

		$allowed_post_types = $this->get( 'allowed-post-types', [ 'post' => 'true' ] );

		$default_allowed = isset( $allowed_post_types[ $post_type ] ) ? filter_var( $allowed_post_types[ $post_type ], FILTER_VALIDATE_BOOLEAN ) : false;

		$default_data = [
			'allowed'               => $default_allowed,
			'name'                  => isset( $post_types[ $post_type ] ) ? $post_types[ $post_type ] : $post_type,
			'slug'                  => $post_type,
			'review_type'           => 'default',
			'allowed_roles'         => [
				'administrator',
				'editor',
				'author',
				'contributor',
				'subscriber',
			],
			'verifications'         => [],
			'comment_verifications' => [],
			'need_approve'          => false,
			'comments_allowed'      => true,
			'comments_need_approve' => false,
			'approval_allowed'      => true,
			'metadata'              => false,
			'metadata_rating_key'   => '_jet_reviews_average_rating',
			'metadata_ratio_bound'  => 5,
			'structuredata'         => false,
			'structuredata_type'    => 'Thing',
		];

		$saved_data = $this->get( $option_name, [] );

		return wp_parse_args( $saved_data, $default_data );

	}

	/**
	 * [get_the_post_type_data description]
	 * @return [type] [description]
	 */
	public function get_the_post_type_data() {
		$post_id = get_the_ID();
		$post_type = get_post_type( $post_id );

		return $this->get_post_type_data( $post_type );
	}

	/**
	 * License page config
	 *
	 * @param  array  $config  [description]
	 * @param  string $subpage [description]
	 * @return [type]          [description]
	 */
	public function settings_page_config() {
		$post_types = jet_reviews_tools()->get_post_types();

		$plugin_settings_data = array(
			'captcha' => $this->get( 'captcha', array(
				'enable'     => false,
				'site_key'   => '',
				'secret_key' => '',
			) ),
			'forbidden-content' => $this->get( 'forbidden-content', array(
				'enable'  => false,
				'words'   => '',
			) ),
			'submit-review-notify' => $this->get( 'submit-review-notify', array(
				'enable'        => false,
				'approval'      => false,
				'author_notify' => false,
			) ),
			'submit-comment-notify' => $this->get( 'submit-comment-notify', array(
				'enable'   => false,
				'approval' => false,
			) ),
		);

		$avaliable_post_types_data    = array();
		$avaliable_post_types_options = array();
		$avaliable_review_types       = Reviews_Data::get_instance()->get_review_types_list();

		foreach ( $post_types as $slug => $name ) {
			$review_post_type_option = $slug . '-type-settings';

			$plugin_settings_data[ $review_post_type_option ] = jet_reviews()->settings->get_post_type_data( $slug );

			$avaliable_post_types_options[] = array(
				'label' => $name,
				'value' => $slug,
			);
		}

		$plugin_settings_data['user-wp-user-source-settings'] = $this->get_source_settings_data( 'user', 'wp-user' );

		$config = array(
			'saveSettingsRoute'         => '/jet-reviews-api/v1/save-settings',
			'syncRatingDataRoute'       => '/jet-reviews-api/v1/sync-rating-data',
			'avaliablePostTypes'        => $avaliable_post_types_options,
			'avaliableReviewTypes'      => $avaliable_review_types,
			'allRolesOptions'           => jet_reviews_tools()->get_roles_options(),
			'verificationOptions'        => jet_reviews()->user_manager->get_verification_options(),
			'settingsData'              => $plugin_settings_data,
			'structureDataTypesOptions' => jet_reviews_tools()->get_structure_data_types(),
		);

		return apply_filters( 'jet-reviews/admin/settings-page/localized-config', $config );
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
