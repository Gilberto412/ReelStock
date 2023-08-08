<?php

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

if ( ! class_exists( 'Jet_Reviews_Meta' ) ) {

	/**
	 * Define Jet_Reviews_Meta class
	 */
	class Jet_Reviews_Meta {

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
			add_action( 'current_screen', array( $this, 'init_meta' ) );
		}

		/**
		 * Initialize reviews metabox for allowed post types
		 *
		 * @return void
		 */
		public function init_meta() {

			if ( ! is_admin() ) {
				return;
			}

			$screen = get_current_screen();

			$post_types = jet_reviews_tools()->get_post_types();

			if ( empty( $screen->post_type ) || 'post' !== $screen->base || ! array_key_exists( $screen->post_type, $post_types ) ) {
				return;
			}

			$post_type_settings = jet_reviews()->settings->get_post_type_data( $screen->post_type );


			if ( ! isset( $post_type_settings[ 'allowed' ] ) || ! filter_var( $post_type_settings[ 'allowed' ], FILTER_VALIDATE_BOOLEAN ) ) {
				return;
			}

			new Cherry_X_Post_Meta( array(
				'id'            => 'jet-reviews',
				'title'         => esc_html__( 'JetReviews', 'jet-reviews' ),
				'page'          => $post_type_settings['slug'],
				'context'       => 'normal',
				'priority'      => 'high',
				'callback_args' => false,
				'builder_cb'    => array( $this, 'get_builder' ),
				'fields'        => array(
					'jet-review-title' => array(
						'type'        => 'text',
						'placeholder' => esc_html__( 'Review Box Title', 'jet-reviews' ),
						'label'       => esc_html__( 'Title', 'jet-reviews' ),
					),
					'jet-review-items' => array(
						'type'        => 'repeater',
						'label'       => esc_html__( 'Review Fields', 'jet-reviews' ),
						'add_label'   => esc_html__( 'Add New Field', 'jet-reviews' ),
						'title_field' => 'field_label',
						'fields'      => array(
							'field_label' => array(
								'type'        => 'text',
								'id'          => 'field_label',
								'name'        => 'field_label',
								'placeholder' => esc_html__( 'Review Field Label', 'jet-reviews' ),
								'label'       => esc_html__( 'Label', 'jet-reviews'  ),
							),
							'field_value' => array(
								'type'        => 'text',
								'id'          => 'field_value',
								'name'        => 'field_value',
								'placeholder' => esc_html__( 'Field Value', 'jet-reviews' ),
								'label'       => esc_html__( 'Value', 'jet-reviews'  ),
							),
							'field_max' => array(
								'type'        => 'text',
								'id'          => 'field_max',
								'name'        => 'field_max',
								'placeholder' => esc_html__( 'Field Max Value', 'jet-reviews' ),
								'label'       => esc_html__( 'Max Value', 'jet-reviews'  ),
							),
						),
					),
					'jet-review-summary-title' => array(
						'type'        => 'text',
						'placeholder' => esc_html__( 'Summary Block Title', 'jet-reviews' ),
						'label'       => esc_html__( 'Summary Title', 'jet-reviews' ),
					),
					'jet-review-summary-text' => array(
						'type'        => 'textarea',
						'label'       => esc_html__( 'Summary Text', 'jet-reviews' ),
					),
					'jet-review-summary-legend' => array(
						'type'        => 'textarea',
						'placeholder' => esc_html__( 'Text Above Summary Value', 'jet-reviews' ),
						'label'       => esc_html__( 'Summary Legend', 'jet-reviews' ),
					),
				),
			) );

			new Cherry_X_Post_Meta( array(
				'id'            => 'jet-reviews-markup',
				'title'         => esc_html__( 'JetReviews Structured Data', 'jet-reviews' ),
				'page'          => $post_type_settings['slug'],
				'context'       => 'normal',
				'priority'      => 'high',
				'callback_args' => false,
				'builder_cb'    => array( $this, 'get_builder' ),
				'fields'        => array(
					'jet-review-data-name' => array(
						'type'        => 'text',
						'placeholder' => esc_html__( 'What was reviewed?', 'jet-reviews' ),
						'label'       => esc_html__( 'Reviewed Item Name', 'jet-reviews' ),
					),
					'jet-review-data-image' => array(
						'type'     => 'media',
						'multiple' => false,
						'label'    => esc_html__( 'Reviewed Item Image', 'jet-reviews' ),
					),
					'jet-review-data-desc' => array(
						'type'        => 'textarea',
						'placeholder' => esc_html__( 'Few words about reviewd item', 'jet-reviews' ),
						'label'       => esc_html__( 'Reviewd Item Description', 'jet-reviews' ),
					),
					'jet-review-data-author-name' => array(
						'type'        => 'text',
						'placeholder' => esc_html__( 'Review Author Name', 'jet-reviews' ),
						'label'       => esc_html__( 'Review Author Name', 'jet-reviews' ),
					),
				),
			) );

		}

		/**
		 * Return UI builder instance
		 *
		 * @since  1.2.1
		 * @return object
		 */
		public function get_builder() {

			$data = jet_reviews()->module_loader->get_included_module_data( 'cherry-x-interface-builder.php' );

			return new CX_Interface_Builder(
				array(
					'path' => $data['path'],
					'url'  => $data['url'],
				)
			);

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
 * Returns instance of Jet_Reviews_Meta
 *
 * @return object
 */
function jet_reviews_meta() {
	return Jet_Reviews_Meta::get_instance();
}
