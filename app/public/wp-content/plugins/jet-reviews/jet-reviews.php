<?php
/**
 * Plugin Name: JetReviews For Elementor
 * Plugin URI:  https://crocoblock.com/plugins/jetreviews/
 * Description: JetReviews - Reviews Widget for Elementor Page Builder
 * Version:     2.3.1
 * Author:      Crocoblock
 * Author URI:  https://crocoblock.com/
 * Text Domain: jet-reviews
 * License:     GPL-2.0+
 * License URI: http://www.gnu.org/licenses/gpl-2.0.txt
 * Domain Path: /languages
 *
 * Elementor tested up to: 3.2
 * Elementor Pro tested up to: 3.3
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die();
}

// If class `Jet_Reviews` doesn't exists yet.
if ( ! class_exists( 'Jet_Reviews' ) ) {

	/**
	 * Sets up and initializes the plugin.
	 */
	class Jet_Reviews {

		/**
		 * A reference to an instance of this class.
		 *
		 * @since  1.0.0
		 * @access private
		 * @var    object
		 */
		private static $instance = null;

		/**
		 * Plugin version
		 *
		 * @var string
		 */
		private $version = '2.3.1';

		/**
		 * Holder for base plugin URL
		 *
		 * @since  1.0.0
		 * @access private
		 * @var    string
		 */
		private $plugin_url = null;

		/**
		 * Holder for base plugin path
		 *
		 * @since  1.0.0
		 * @access private
		 * @var    string
		 */
		private $plugin_path = null;

		/**
		 * [$module_loader description]
		 * @var [type]
		 */
		public $module_loader;

		/**
		 * [$db description]
		 * @var null
		 */
		public $db = null;

		/**
		 * [$admin description]
		 * @var null
		 */
		public $admin = null;

		/**
		 * [$settings description]
		 * @var null
		 */
		public $settings = null;

		/**
		 * [$user_manager description]
		 * @var null
		 */
		public $user_manager = null;

		/**
		 * [$reviews_manager description]
		 * @var null
		 */
		public $reviews_manager = null;

		/**
		 * [$review_comments_manager description]
		 * @var null
		 */
		public $comments_manager = null;

		/**
		 * [$elementor_manager description]
		 * @var null
		 */
		public $elementor_manager = null;

		/**
		 * [$compatibility_manager description]
		 * @var null
		 */
		public $compatibility_manager = null;

		/**
		 * [$integration_manager description]
		 * @var null
		 */
		public $integration_manager = null;

		/**
		 *
		 */
		public $blocks_manager = null;

		/**
		 * Sets up needed actions/filters for the plugin to initialize.
		 *
		 * @since 1.0.0
		 * @access public
		 * @return void
		 */
		public function __construct() {

			// Load the CX Loader.
			add_action( 'after_setup_theme', array( $this, 'module_loader' ), -20 );

			// Internationalize the text strings used.
			add_action( 'init', array( $this, 'lang' ), -999 );

			// Load files.
			add_action( 'init', array( $this, 'init' ), -999 );

			// Jet Dashboard Init
			add_action( 'init', array( $this, 'jet_dashboard_init' ), -999 );

			// Register activation and deactivation hook.
			register_activation_hook( __FILE__, array( $this, 'activation' ) );

			register_deactivation_hook( __FILE__, array( $this, 'deactivation' ) );
		}

		/**
		 * Load the theme modules.
		 *
		 * @since  1.0.0
		 */
		public function module_loader() {
			require $this->plugin_path( 'includes/modules/loader.php' );

			$this->module_loader = new Jet_Reviews_CX_Loader(
				array(
					$this->plugin_path( 'includes/modules/interface-builder/cherry-x-interface-builder.php' ),
					$this->plugin_path( 'includes/modules/post-meta/cherry-x-post-meta.php' ),
					$this->plugin_path( 'includes/modules/vue-ui/cherry-x-vue-ui.php' ),
					$this->plugin_path( 'includes/modules/jet-dashboard/jet-dashboard.php' ),
				)
			);
		}

		/**
		 * [jet_dashboard_init description]
		 * @return [type] [description]
		 */
		public function jet_dashboard_init() {

			if ( is_admin() ) {

				$cx_ui_module_data         = $this->module_loader->get_included_module_data( 'cherry-x-vue-ui.php' );
				$jet_dashboard_module_data = $this->module_loader->get_included_module_data( 'jet-dashboard.php' );

				$jet_dashboard = \Jet_Dashboard\Dashboard::get_instance();

				$jet_dashboard->init( array(
					'path'           => $jet_dashboard_module_data['path'],
					'url'            => $jet_dashboard_module_data['url'],
					'cx_ui_instance' => array( $this, 'jet_dashboard_ui_instance_init' ),
					'plugin_data'    => array(
						'slug'    => 'jet-reviews',
						'file'    => 'jet-reviews/jet-reviews.php',
						'version' => $this->get_version(),
						'plugin_links' => array(
							array(
								'label'  => esc_html__( 'Review Stats', 'jet-tricks' ),
								'url'    => add_query_arg( array( 'page' => 'jet-reviews' ), admin_url( 'admin.php' ) ),
								'target' => '_self',
							),
							array(
								'label'  => esc_html__( 'Reviews', 'jet-tricks' ),
								'url'    => add_query_arg( array( 'page' => 'jet-reviews-list-page' ), admin_url( 'admin.php' ) ),
								'target' => '_self',
							),
							array(
								'label'  => esc_html__( 'Comments', 'jet-tricks' ),
								'url'    => add_query_arg( array( 'page' => 'jet-reviews-comments-list-page' ), admin_url( 'admin.php' ) ),
								'target' => '_self',
							),
							array(
								'label'  => esc_html__( 'Review Types', 'jet-tricks' ),
								'url'    => add_query_arg( array( 'page' => 'jet-reviews-type-page' ), admin_url( 'admin.php' ) ),
								'target' => '_self',
							),
							array(
								'label'  => esc_html__( 'Settings', 'jet-tricks' ),
								'url'    => add_query_arg( array( 'page' => 'jet-dashboard-settings-page', 'subpage' => 'jet-reviews-post-types' ), admin_url( 'admin.php' ) ),
								'target' => '_self',
							),
						),
					),
				) );
			}
		}

		/**
		 * [jet_dashboard_ui_instance_init description]
		 * @return [type] [description]
		 */
		public function jet_dashboard_ui_instance_init() {
			$cx_ui_module_data = $this->module_loader->get_included_module_data( 'cherry-x-vue-ui.php' );

			return new CX_Vue_UI( $cx_ui_module_data );
		}

		/**
		 * Returns plugin version
		 *
		 * @return string
		 */
		public function get_version() {
			return $this->version;
		}

		/**
		 * Manually init required modules.
		 *
		 * @return void
		 */
		public function init() {

			$this->load_files();

			$this->db = new Jet_Reviews\DB\Manager;

			$this->admin = new Jet_Reviews\Admin;

			$this->compatibility_manager = new Jet_Reviews\Compatibility\Manager;

			$this->settings = new Jet_Reviews\Settings\Manager;

			$this->user_manager = new Jet_Reviews\User\Manager;

			$this->reviews_manager = new Jet_Reviews\Reviews\Manager;

			$this->comments_manager = new Jet_Reviews\Comments\Manager;

			$this->elementor_manager = new Jet_Reviews\Elementor\Manager;

			$this->integration_manager = new Jet_Reviews\Integrations\Manager;

			jet_reviews_assets()->init();

			jet_reviews_meta()->init();

			jet_reviews_ajax_handlers()->init();

			$this->blocks_manager = new Jet_Reviews\Blocks\Manager();

			//Init Rest Api
			new \Jet_Reviews\Rest_Api();

			do_action( 'jet-reviews/init', $this );
		}

		/**
		 * Check if theme has elementor
		 *
		 * @return boolean
		 */
		public function has_elementor() {
			return defined( 'ELEMENTOR_VERSION' );
		}

		/**
		 * Load required files.
		 *
		 * @return void
		 */
		public function load_files() {
			require $this->plugin_path( 'includes/db/manager.php' );
			require $this->plugin_path( 'includes/components/user/manager.php' );
			require $this->plugin_path( 'includes/components/settings/manager.php' );

			require $this->plugin_path( 'includes/admin.php' );
			require $this->plugin_path( 'includes/components/base/base-page.php' );
			require $this->plugin_path( 'includes/components/base/base-render.php' );
			require $this->plugin_path( 'includes/components/reviews/manager.php' );
			require $this->plugin_path( 'includes/components/comments/manager.php' );
			require $this->plugin_path( 'includes/components/elementor/manager.php' );
			require $this->plugin_path( 'includes/components/integrations/manager.php' );
			require $this->plugin_path( 'includes/components/blocks/manager.php' );

			require $this->plugin_path( 'includes/compatibility/manager.php' );

			require $this->plugin_path( 'includes/meta.php' );
			require $this->plugin_path( 'includes/assets.php' );
			require $this->plugin_path( 'includes/tools.php' );
			require $this->plugin_path( 'includes/ajax-handlers.php' );

			require $this->plugin_path( 'includes/rest-api/rest-api.php' );
			require $this->plugin_path( 'includes/rest-api/endpoints/base.php' );
			require $this->plugin_path( 'includes/rest-api/endpoints/elementor-template.php' );

		}

		/**
		 * Returns path to file or dir inside plugin folder
		 *
		 * @param  string $path Path inside plugin dir.
		 * @return string
		 */
		public function plugin_path( $path = null ) {

			if ( ! $this->plugin_path ) {
				$this->plugin_path = trailingslashit( plugin_dir_path( __FILE__ ) );
			}

			return $this->plugin_path . $path;
		}
		/**
		 * Returns url to file or dir inside plugin folder
		 *
		 * @param  string $path Path inside plugin dir.
		 * @return string
		 */
		public function plugin_url( $path = null ) {

			if ( ! $this->plugin_url ) {
				$this->plugin_url = trailingslashit( plugin_dir_url( __FILE__ ) );
			}

			return $this->plugin_url . $path;
		}

		/**
		 * Loads the translation files.
		 *
		 * @since 1.0.0
		 * @access public
		 * @return void
		 */
		public function lang() {
			load_plugin_textdomain( 'jet-reviews', false, dirname( plugin_basename( __FILE__ ) ) . '/languages' );
		}

		/**
		 * Get the template path.
		 *
		 * @return string
		 */
		public function template_path() {
			return apply_filters( 'jet-reviews/template-path', 'jet-reviews/' );
		}

		/**
		 * Returns path to template file.
		 *
		 * @return string|bool
		 */
		public function get_template( $name = null ) {

			$template = locate_template( $this->template_path() . $name );

			if ( ! $template ) {
				$template = $this->plugin_path( 'templates/' . $name );
			}

			if ( file_exists( $template ) ) {
				return $template;
			} else {
				return false;
			}
		}

		/**
		 * Do some stuff on plugin activation
		 *
		 * @since  1.0.0
		 * @return void
		 */
		public function activation() {
			require $this->plugin_path( 'includes/db/manager.php' );
			\Jet_Reviews\DB\Manager::init_db_required();
		}

		/**
		 * Do some stuff on plugin activation
		 *
		 * @since  1.0.0
		 * @return void
		 */
		public function deactivation() {}

		/**
		 * Returns the instance.
		 *
		 * @since  1.0.0
		 * @access public
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
}

if ( ! function_exists( 'jet_reviews' ) ) {

	/**
	 * Returns instanse of the plugin class.
	 *
	 * @since  1.0.0
	 * @return object
	 */
	function jet_reviews() {
		return Jet_Reviews::get_instance();
	}
}

jet_reviews();
