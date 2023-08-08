<?php

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

if ( ! class_exists( 'Jet_Reviews_Assets' ) ) {

	/**
	 * Define Jet_Reviews_Assets class
	 */
	class Jet_Reviews_Assets {

		/**
		 * A reference to an instance of this class.
		 *
		 * @since 1.0.0
		 * @var   object
		 */
		private static $instance = null;

		/**
		 * Constructor for the class
		 */
		public function init() {

			add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_styles' ) );

			add_action( 'elementor/editor/after_enqueue_styles',   array( $this, 'editor_styles' ) );

			add_action( 'wp_enqueue_scripts', array( $this, 'register_scripts' ), 5 );

            if ( ! jet_reviews_tools()->has_elementor() ) {
	            add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
            } else {
	            add_action( 'elementor/frontend/before_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
            }

			add_action( 'wp_footer', array( $this, 'render_vue_template' ) );
		}

		/**
		 * Enqueue public-facing stylesheets.
		 *
		 * @since 1.0.0
		 * @access public
		 * @return void
		 */
		public function enqueue_styles() {
			wp_enqueue_style(
				'jet-reviews-frontend',
				jet_reviews()->plugin_url( 'assets/css/jet-reviews.css' ),
				array(),
				jet_reviews()->get_version()
			);
		}

			/**
		 * Enqueue editor styles
		 *
		 * @return void
		 */
		public function editor_styles() {
			wp_enqueue_style(
				'jet-reviews-editor',
				jet_reviews()->plugin_url( 'assets/css/jet-reviews-editor.css' ),
				array(),
				jet_reviews()->get_version()
			);
		}

		/**
		 * [register_scripts description]
		 * @return [type] [description]
		 */
		public function register_scripts() {

			do_action( 'jet-reviews/frontend/before_register_scripts' );

			$suffix = ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) ? '' : '.min';

			wp_register_script(
				'jet-vue',
				jet_reviews()->plugin_url( 'assets/js/lib/vue' . $suffix . '.js' ),
				array(),
				'2.6.11',
				true
			);

			do_action( 'jet-reviews/frontend/after_register_scripts' );

		}

		/**
		 * Enqueue plugin scripts only with elementor scripts
		 *
		 * @return void
		 */
		public function enqueue_scripts() {

			$frontend_deps_scripts = apply_filters( 'jet-reviews/frontend/deps-scripts',
				array( 'jquery', 'wp-api-fetch', 'jet-vue' )
			);

			wp_enqueue_script(
				'jet-reviews-frontend',
				jet_reviews()->plugin_url( 'assets/js/jet-reviews-frontend.js' ),
				$frontend_deps_scripts,
				jet_reviews()->get_version(),
				true
			);

			wp_localize_script(
				'jet-reviews-frontend',
				'jetReviewPublicConfig',
				$this->get_front_localize_data()
			);

		}

		/**
		 * @return mixed|void
		 */
        public function get_front_localize_data() {
	        global $wp;

            return apply_filters( 'jet-reviews/public/localized-data', array(
	            'version'                  => jet_reviews()->get_version(),
	            'ajax_url'                 => esc_url( admin_url( 'admin-ajax.php' ) ),
	            'current_url'              => esc_url( home_url( add_query_arg( [], $wp->request ) ) ),
	            'getPublicReviewsRoute'    => '/jet-reviews-api/v1/get-public-reviews-list',
	            'submitReviewCommentRoute' => '/jet-reviews-api/v1/submit-review-comment',
	            'submitReviewRoute'        => '/jet-reviews-api/v1/submit-review',
	            'likeReviewRoute'          => '/jet-reviews-api/v1/update-review-approval',
	            'reviewTypeData'           => jet_reviews_tools()->get_post_review_type_data(),
	            'labels'                   => array(
		            'alreadyReviewed'           => __( '*Already reviewed', 'jet-reviews' ),
		            'notApprove'                => __( '*Your review must be approved by the moderator', 'jet-reviews' ),
		            'notValidField'             => __( '*This field is required or not valid', 'jet-reviews' ),
		            'captchaValidationFailed'   => __( '*Captcha validation failed', 'jet-reviews' ),
	            )
            ) );
        }

		/**
		 * [render_vue_template description]
		 * @return [type] [description]
		 */
		public function render_vue_template() {

			$vue_templates = array(
				'jet-advanced-reviews-item',
				'jet-advanced-reviews-comment',
				'jet-advanced-reviews-point-field',
				'jet-advanced-reviews-star-field',
				'jet-advanced-reviews-form',
				'jet-advanced-reviews-slider-input',
				'jet-advanced-reviews-stars-input',
				'jet-reviews-widget-pagination',
			);

			foreach ( glob( jet_reviews()->plugin_path() . 'templates/public/vue-templates/*.php' ) as $file ) {
				$path_info = pathinfo( $file );
				$template_name = $path_info['filename'];

				if ( in_array( $template_name, $vue_templates ) ) {?>
					<script type="text/x-template" id="<?php echo $template_name; ?>-template"><?php
						require $file; ?>
					</script><?php
				}
			}
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

}

/**
 * Returns instance of Jet_Reviews_Assets
 *
 * @return object
 */
function jet_reviews_assets() {
	return Jet_Reviews_Assets::get_instance();
}
