<?php
namespace Jet_Reviews\Reviews;

use Jet_Reviews\Admin as Admin;
use Jet_Reviews\Base_Page as Base_Page;
use Jet_Reviews\Reviews\Data as Reviews_Data;

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

class Types_Page extends Base_Page {

	/**
	 * Returns module slug
	 *
	 * @return void
	 */
	public function get_slug() {
		return $this->base_slug . '-type-page';
	}

	/**
	 * [init description]
	 * @return [type] [description]
	 */
	public function init() {
		add_action( 'admin_menu', array( $this, 'register_page' ), 11 );
	}

	/**
	 * [register_page description]
	 * @return [type] [description]
	 */
	public function register_page() {

		add_submenu_page(
			Admin::get_instance()->admin_page_slug,
			esc_html__( 'Review Types', 'jet-reviews' ),
			esc_html__( 'Review Types', 'jet-reviews' ),
			'manage_options',
			$this->get_slug(),
			array( $this, 'render_page' )
		);

	}

	/**
	 * [render_page description]
	 * @return [type] [description]
	 */
	public function render_page() {
		include jet_reviews()->get_template( 'admin/pages/reviews/types-page.php' );
	}

	/**
	 * Enqueue module-specific assets
	 *
	 * @return void
	 */
	public function enqueue_module_assets() {

		wp_enqueue_script(
			'jet-reviews-types-page',
			jet_reviews()->plugin_url( 'assets/js/admin/review-types-page.js' ),
			array( 'cx-vue-ui', 'wp-api-fetch' ),
			jet_reviews()->get_version(),
			true
		);

		wp_localize_script( 'jet-reviews-types-page', 'JetReviewsTypesPageConfig', $this->localize_config() );

	}

	/**
	 * License page config
	 *
	 * @param  array  $config  [description]
	 * @param  string $subpage [description]
	 * @return [type]          [description]
	 */
	public function localize_config() {

		$config = array(
			'messages' => array(
				'emptyName'   => esc_html__( 'Type name cannot be empty', 'jet-reviews' ),
				'emptyFields' => esc_html__( 'List of fields cannot be empty', 'jet-reviews' ),
			),
			'addReviewType'    => '/jet-reviews-api/v1/add-review-type',
			'updateReviewType' => '/jet-reviews-api/v1/update-review-type',
			'deleteReviewType' => '/jet-reviews-api/v1/delete-review-type',
			'typesList'        => Reviews_Data::get_instance()->get_review_types_list(),
		);

		return $config;

	}
}
