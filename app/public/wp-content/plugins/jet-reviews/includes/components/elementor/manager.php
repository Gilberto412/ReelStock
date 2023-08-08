<?php
namespace Jet_Reviews\Elementor;

class Manager {

	/**
	 * [$dynamic_tags description]
	 * @var [type]
	 */
	public $dynamic_tags;

	/**
	 * Check if processing elementor widget
	 *
	 * @var boolean
	 */
	private $is_elementor_ajax = false;

	/**
	 * Constructor.
	 */
	public function __construct() {

		if ( ! defined( 'ELEMENTOR_VERSION' ) ) {
			//add_action( 'admin_notices', array( $this, 'required_plugins_notice' ) );

			return;
		}

		$this->load_files();

		add_action( 'elementor/init', array( $this, 'init_components' ) );

		add_action( 'elementor/elements/categories_registered', array( $this, 'register_category' ) );

		add_action( 'elementor/widgets/widgets_registered', array( $this, 'register_addons' ), 10 );

		add_action( 'wp_ajax_elementor_render_widget', array( $this, 'set_elementor_ajax' ), 10, -1 );

		add_filter( 'jet-reviews/frontend/deps-scripts', array( $this, 'modify_frontend_deps_scripts' ) );
	}

	/**
	 * @param $deps
	 *
	 * @return mixed
	 */
	public function modify_frontend_deps_scripts( $deps ) {
		$deps[] = 'elementor-frontend';

		return $deps;
	}

	/**
	 * [load_files description]
	 * @return [type] [description]
	 */
	public function load_files() {
		require jet_reviews()->plugin_path( 'includes/components/elementor/dynamic-tags/module.php' );
	}

	/**
	 * Check if we currently in Elementor mode
	 *
	 * @return void
	 */
	public function in_elementor() {

		$result = false;

		if ( wp_doing_ajax() ) {
			$result = $this->is_elementor_ajax;
		} elseif ( \Elementor\Plugin::instance()->editor->is_edit_mode()
			|| \Elementor\Plugin::instance()->preview->is_preview_mode() ) {
			$result = true;
		}

		/**
		 * Allow to filter result before return
		 *
		 * @var bool $result
		 */
		return apply_filters( 'jet-reviews/in-elementor', $result );
	}

	/**
	 * Initialize elementor-related components
	 * @return [type] [description]
	 */
	public function init_components() {
		$this->dynamic_tags = new Dynamic_Tags\Module();
	}

	/**
	 * Register cherry category for elementor if not exists
	 *
	 * @return void
	 */
	public function register_category() {

		$elements_manager = \Elementor\Plugin::instance()->elements_manager;
		$cherry_cat       = 'jet-reviews';

		$elements_manager->add_category(
			$cherry_cat,
			array(
				'title' => esc_html__( 'JetReviews', 'jet-reviews' ),
				'icon'  => 'font',
			),
			1
		);
	}

	/**
	 * Register plugin addons
	 *
	 * @param  object $widgets_manager Elementor widgets manager instance.
	 * @return void
	 */
	public function register_addons( $widgets_manager ) {

		require jet_reviews()->plugin_path( 'includes/components/base/base-elementor-widget.php' );

		foreach ( glob( jet_reviews()->plugin_path( 'includes/components/elementor/widgets/' ) . '*.php' ) as $file ) {
			$slug = basename( $file, '.php' );

			$this->register_addon( $file, $widgets_manager );
		}
	}

	/**
	 * Register addon by file name
	 *
	 * @param  string $file            File name.
	 * @param  object $widgets_manager Widgets manager instance.
	 * @return void
	 */
	public function register_addon( $file, $widgets_manager ) {

		$base  = basename( str_replace( '.php', '', $file ) );
		$class = ucwords( str_replace( '-', ' ', $base ) );
		$class = str_replace( ' ', '_', $class );
		$class = sprintf( '\Elementor\%s', $class );

		require $file;

		if ( class_exists( $class ) ) {
			$widgets_manager->register_widget_type( new $class );
		}
	}

	/**
	 * Set $this->is_elementor_ajax to true on Elementor AJAX processing
	 *
	 * @return  void
	 */
	public function set_elementor_ajax() {
		$this->is_elementor_ajax = true;
	}

	/**
	 * Show recommended plugins notice.
	 *
	 * @return void
	 */
	public function required_plugins_notice() {
		$screen = get_current_screen();

		if ( isset( $screen->parent_file ) && 'plugins.php' === $screen->parent_file && 'update' === $screen->id ) {
			return;
		}

		$plugin = 'elementor/elementor.php';

		$installed_plugins      = get_plugins();
		$is_elementor_installed = isset( $installed_plugins[ $plugin ] );

		if ( $is_elementor_installed ) {
			if ( ! current_user_can( 'activate_plugins' ) ) {
				return;
			}

			$activation_url = wp_nonce_url( 'plugins.php?action=activate&amp;plugin=' . $plugin . '&amp;plugin_status=all&amp;paged=1&amp;s', 'activate-plugin_' . $plugin );

			$message = sprintf( '<p>%s</p>', esc_html__( 'JetReviews requires Elementor to be activated.', 'jet-reviews' ) );
			$message .= sprintf( '<p><a href="%s" class="button-primary">%s</a></p>', $activation_url, esc_html__( 'Activate Elementor Now', 'jet-reviews' ) );
		} else {
			if ( ! current_user_can( 'install_plugins' ) ) {
				return;
			}

			$install_url = wp_nonce_url( self_admin_url( 'update.php?action=install-plugin&plugin=elementor' ), 'install-plugin_elementor' );

			$message = sprintf( '<p>%s</p>', esc_html__( 'JetMenu requires Elementor to be installed.', 'jet-menu' ) );
			$message .= sprintf( '<p><a href="%s" class="button-primary">%s</a></p>', $install_url, esc_html__( 'Install Elementor Now', 'jet-reviews' ) );
		}

		printf( '<div class="notice notice-warning is-dismissible"><p>%s</p></div>', wp_kses_post( $message ) );
	}
}
