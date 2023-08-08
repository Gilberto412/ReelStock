<?php
namespace Jet_Reviews\Comments;

use Jet_Reviews\Admin as Admin;
use Jet_Reviews\Base_Page as Base_Page;

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

class List_Page extends Base_Page {

	/**
	 * Returns module slug
	 *
	 * @return void
	 */
	public function get_slug() {
		return $this->base_slug . '-comments-list-page';
	}

	/**
	 * [get_current_page description]
	 * @return [type] [description]
	 */
	public function get_current_page_param() {

		if ( isset( $_REQUEST['comments-page'] ) ) {
			return $_REQUEST['comments-page'];
		}

		return 1;
	}

	/**
	 * [get_per_page_value description]
	 * @return [type] [description]
	 */
	public function get_page_size_param() {

		if ( isset( $_REQUEST['comments-page-size'] ) ) {
			return $_REQUEST['comments-page-size'];
		}

		return 20;
	}

	/**
	 * @return int|mixed
	 */
	public function get_review_id_param() {

		if ( isset( $_REQUEST['review-id'] ) ) {
			return $_REQUEST['review-id'];
		}

		return '';
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
			esc_html__( 'Comments', 'jet-reviews' ),
			esc_html__( 'Comments', 'jet-reviews' ),
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
		include jet_reviews()->get_template( 'admin/pages/comments/list-page.php' );
	}

	/**
	 * Enqueue module-specific assets
	 *
	 * @return void
	 */
	public function enqueue_module_assets() {

		wp_enqueue_script(
			'jet-reviews-comments-list-page',
			jet_reviews()->plugin_url( 'assets/js/admin/comments-list-page.js' ),
			array( 'cx-vue-ui', 'wp-api-fetch' ),
			jet_reviews()->get_version(),
			true
		);

		wp_localize_script( 'jet-reviews-comments-list-page', 'JetReviewsCommentsListPageConfig', $this->localize_config() );

	}

	/**
	 * License page config
	 *
	 * @param  array  $config  [description]
	 * @param  string $subpage [description]
	 * @return [type]          [description]
	 */
	public function localize_config() {

		$current_page = $this->get_current_page_param();
		$page_size = $this->get_page_size_param();

		$config = array(
			'getCommentsRoute'          => '/jet-reviews-api/v1/get-admin-comments-list',
			'updateCommentRoute'        => '/jet-reviews-api/v1/update-comment',
			'deleteCommentRoute'        => '/jet-reviews-api/v1/delete-comment',
			'toggleCommentApproveRoute' => '/jet-reviews-api/v1/toggle-comment-approve',
			'commentsList'              => array(),
			'currentPage'               => $this->get_current_page_param(),
			'pageSize'                  => $this->get_page_size_param(),
			'reviewId'                  => $this->get_review_id_param(),
			'commentsCount'             => 0,
		);

		return $config;

	}
}
