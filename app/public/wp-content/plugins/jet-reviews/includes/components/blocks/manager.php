<?php
namespace Jet_Reviews\Blocks;

class Manager {

	/**
	 * A reference to an instance of this class.
	 *
	 * @since 1.0.0
	 * @var   object
	 */
	private static $instance = null;

	/**
	 * @var array
	 */
	private $registered_blocks = [];
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

	/**
	 * Load files
	 */
	public function load_files() {}

	/**
	 * Add new category for filters
	 *
	 * @param $categories
	 * @return false
	 */
	function add_filters_category( $categories ) {

		return array_merge( $categories,
			[
				[
					'slug'  => 'jet-reviews',
					'title' => __( 'JetReviews', 'jet-reviews' ),
					'icon'  => 'filter',
				],
			]
		);

	}

	/**
	 * @return array
	 */
	public function get_registered_blocks() {
		return $this->registered_blocks;
	}

	/**
	 * @return array
	 */
	public function get_registered_block_attrs() {
		$registered_blocks = $this->get_registered_blocks();

		$block_attrs = [];

		foreach ( $registered_blocks as $block_slug => $block_instance ) {
			$block_attrs[ $block_slug ] = $block_instance->get_attributes();
		}

		return $block_attrs;
	}

	/**
	 * Register blocks assets
	 *
	 * @return false
	 */
	public function blocks_assets() {
		jet_reviews_assets()->enqueue_styles();
		jet_reviews_assets()->register_scripts();

		wp_enqueue_style(
			'jet-reviews-block-editor-styles',
			jet_reviews()->plugin_url( 'assets/css/jet-reviews-admin.css' ),
			[ 'jet-reviews-frontend' ],
			jet_reviews()->get_version()
		);

		wp_enqueue_script(
			'jet-reviews-frontend',
			jet_reviews()->plugin_url( 'assets/js/jet-reviews-frontend.js' ),
			apply_filters( 'jet-reviews/admin/frontend-scripts/deps-scripts',
				array( 'jquery', 'wp-api-fetch', 'jet-vue' )
			),
			jet_reviews()->get_version(),
			true
		);

		wp_localize_script(
			'jet-reviews-frontend',
			'jetReviewPublicConfig',
			jet_reviews_assets()->get_front_localize_data()
		);

		wp_enqueue_script(
			'jet-reviews-blocks',
			jet_reviews()->plugin_url( 'assets/js/admin/blocks.js' ),
			[ 'wp-blocks', 'wp-editor', 'wp-components', 'wp-element', 'wp-i18n', 'jet-reviews-frontend' ],
			jet_reviews()->get_version(),
			true
		);

		wp_localize_script(
			'jet-reviews-blocks',
			'JetReviewsBlocksData',
			apply_filters( 'jet-reviews/assets/admin/blocks/localized-data',
				[
					'version'              => jet_reviews()->get_version(),
					'postId'               => get_the_ID(),
					'registeredBlockAttrs' => $this->get_registered_block_attrs(),
					'registeredSourceList' => jet_reviews()->reviews_manager->sources->get_registered_source_options(),
				]
			)
		);
	}

	/**
	 * Register block types
	 *
	 * @return false
	 */
	public function register_block_types() {

		$base_path = jet_reviews()->plugin_path( 'includes/components/blocks/blocks/' );

		require $base_path . 'base.php';

		$default_blocks = apply_filters( 'jet-reviews/block-manager/blocks-list', [
			'\Jet_Reviews\Blocks\Jet_Reviews_Advanced' => $base_path . 'jet-reviews-advanced.php',
		] );

		foreach ( $default_blocks as $class => $file ) {
			require $file;

			$instance = new $class;
			$id = $instance->get_name();

			$this->registered_blocks[ $id ] = $instance;
		}

	}

	/**
	 * Is editor context
	 *
	 * @return boolean
	 */
	public function is_editor() {
		return isset( $_REQUEST['context'] ) && $_REQUEST['context'] === 'edit' ? true : false;
	}

	/**
	 * @param $current_id
	 *
	 * @return mixed
	 */
	public function maybe_modify_post_source_id( $current_id ) {

		if ( $this->is_editor() && isset( $_REQUEST['postId'] ) ) {
			return $_REQUEST['postId'];
		}

		return $current_id;
	}

	/**
	 * Constructor for the class
	 */
	public function __construct() {

		$this->load_files();

		//add_filter( 'block_categories_all', [ $this, 'add_filters_category' ] );
		add_action( 'enqueue_block_editor_assets', [ $this, 'blocks_assets' ] );

		add_action( 'admin_footer', [ jet_reviews_assets(), 'render_vue_template' ] );

		add_filter( 'jet-reviews/source/source-post/current-id', [ $this, 'maybe_modify_post_source_id' ] );

		$this->register_block_types();
	}
}
