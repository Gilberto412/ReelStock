<?php
namespace Jet_Reviews;
/**
 * Class description
 *
 * @package   package_name
 * @author    Cherry Team
 * @license   GPL-2.0+
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Define Manager class
 */
class Admin {

	/**
	 * A reference to an instance of this class.
	 *
	 * @since 1.0.0
	 * @var   object
	 */
	private static $instance = null;

	/**
	 * Jet engine menu page slug
	 *
	 * @var string
	 */
	public $admin_page_slug = 'jet-reviews';

	/**
	 * [$registered_pages description]
	 * @var array
	 */
	public $components = array();

	/**
	 * [$inited_components description]
	 * @var array
	 */
	public $inited_components = array();

	/**
	 * [$cx_vue_ui description]
	 * @var null
	 */
	public $cx_vue_ui = null;

	/**
	 * Constructor for the class
	 */
	function __construct() {

		add_action( 'admin_menu', array( $this, 'register_main_menu_page' ), 11 );

		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_assets' ) );

		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_main_page_assets' ) );

	}

	/**
	 * [get_vue_ui_instance description]
	 * @return [type] [description]
	 */
	public function get_vue_ui_instance() {

		if ( null === $this->cx_vue_ui ) {
			$module_data = jet_reviews()->module_loader->get_included_module_data( 'cherry-x-vue-ui.php' );

			$this->cx_vue_ui = new \CX_Vue_UI( $module_data );
		}

		return $this->cx_vue_ui;
	}

	/**
	 * Register menu page
	 *
	 * @return void
	 */
	public function register_main_menu_page() {

		add_menu_page(
			__( 'JetReviews', 'jet-reviews' ),
			__( 'JetReviews', 'jet-reviews' ),
			'manage_options',
			$this->admin_page_slug,
			array( $this, 'render_page' ),
			'data:image/svg+xml;base64,' . base64_encode('<svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" clip-rule="evenodd" d="M20 1H4C2.34315 1 1 2.34315 1 4V20C1 21.6569 2.34315 23 4 23H20C21.6569 23 23 21.6569 23 20V4C23 2.34315 21.6569 1 20 1ZM4 0C1.79086 0 0 1.79086 0 4V20C0 22.2091 1.79086 24 4 24H20C22.2091 24 24 22.2091 24 20V4C24 1.79086 22.2091 0 20 0H4Z" fill="#a0a5aa"/><path fill-rule="evenodd" clip-rule="evenodd" d="M21.6293 6.00066C21.9402 5.98148 22.1176 6.38578 21.911 6.64277L20.0722 8.93035C19.8569 9.19824 19.4556 9.02698 19.4598 8.669L19.4708 7.74084C19.4722 7.61923 19.4216 7.50398 19.3343 7.42975L18.6676 6.86321C18.4105 6.6447 18.5378 6.19134 18.8619 6.17135L21.6293 6.00066ZM6.99835 12.008C6.99835 14.1993 5.20706 15.9751 2.99967 15.9751C2.44655 15.9751 2 15.5293 2 14.9827C2 14.4361 2.44655 13.9928 2.99967 13.9928C4.10336 13.9928 4.99901 13.1036 4.99901 12.008V9.03323C4.99901 8.48413 5.44556 8.04082 5.99868 8.04082C6.55179 8.04082 6.99835 8.48413 6.99835 9.03323V12.008ZM17.7765 12.008C17.7765 13.1036 18.6721 13.9928 19.7758 13.9928C20.329 13.9928 20.7755 14.4336 20.7755 14.9827C20.7755 15.5318 20.329 15.9751 19.7758 15.9751C17.5684 15.9751 15.7772 14.1993 15.7772 12.008V9.03323C15.7772 8.48413 16.2237 8.04082 16.7768 8.04082C17.33 8.04082 17.7765 8.48665 17.7765 9.03323V9.92237H18.5707C19.1238 9.92237 19.5729 10.3682 19.5729 10.9173C19.5729 11.4664 19.1238 11.9122 18.5707 11.9122H17.7765V12.008ZM15.2038 10.6176C15.2063 10.6151 15.2088 10.6151 15.2088 10.6151C14.8942 9.79393 14.3056 9.07355 13.4835 8.60001C11.5755 7.50181 9.13979 8.15166 8.04117 10.0508C6.94001 11.9475 7.59462 14.3731 9.50008 15.4688C10.9032 16.2749 12.593 16.1338 13.8261 15.2472L13.8184 15.2371C14.1026 15.0633 14.2904 14.751 14.2904 14.3958C14.2904 13.8492 13.8438 13.4059 13.2932 13.4059C13.0268 13.4059 12.7833 13.5092 12.6057 13.6805C12.0069 14.081 11.2102 14.1439 10.5378 13.7762L14.5644 11.9198C14.7978 11.8493 15.0059 11.6931 15.1353 11.4664C15.2926 11.1969 15.3078 10.8871 15.2038 10.6176ZM12.4864 10.3153C12.6057 10.3833 12.7122 10.4614 12.8112 10.5471L9.49754 12.0709C9.48993 11.7208 9.5762 11.3657 9.76395 11.0407C10.3145 10.0937 11.5324 9.76874 12.4864 10.3153Z" fill="#a0a5aa"/></svg>')
		);

	}

	/**
	 * Initialize interface builder
	 *
	 * @return [type] [description]
	 */
	public function enqueue_assets() {

		do_action( 'jet-reviews/dashboard/before-assets' );

		wp_enqueue_style(
			'jet-reviews-admin-css',
			jet_reviews()->plugin_url( 'assets/css/admin.css' ),
			array(),
			jet_reviews()->get_version()
		);

		do_action( 'jet-reviews/dashboard/after-assets' );

	}

	/**
	 * [enqueue_main_page_assets description]
	 * @return [type] [description]
	 */
	public function enqueue_main_page_assets() {

		if ( isset( $_REQUEST['page'] ) && $this->admin_page_slug === $_REQUEST['page'] ) {

			$cx_vue_ui = $this->get_vue_ui_instance();

			$cx_vue_ui->enqueue_assets();

			wp_enqueue_script(
				'chartjs.js',
				jet_reviews()->plugin_url( 'assets/js/lib/chartjs.min.js' ),
				array(),
				'2.7.1',
				true
			);

			wp_enqueue_script(
				'vuechartjs.js',
				jet_reviews()->plugin_url( 'assets/js/lib/vue-chartjs.min.js' ),
				array(),
				'3.5.0',
				true
			);

			wp_enqueue_script(
				'jet-reviews-main-page-js',
				jet_reviews()->plugin_url( 'assets/js/admin/main-page.js' ),
				array( 'cx-vue-ui', 'wp-api-fetch' ),
				jet_reviews()->get_version(),
				true
			);

			wp_localize_script( 'jet-reviews-main-page-js', 'JetReviewsMainPageConfig', $this->localize_main_page_config() );
		}
	}

	/**
	 * License page config
	 *
	 * @param  array  $config  [description]
	 * @param  string $subpage [description]
	 * @return [type]          [description]
	 */
	public function localize_main_page_config() {

		$post_types    = jet_reviews_tools()->get_post_types();
		$postTypesData = array();

		$month_list = array_map( function( $month_data ) {
			return $month_data['label'];
		}, jet_reviews_tools()->get_default_reviews_dataset() );

		$all_review_dataset = jet_reviews()->reviews_manager->data->get_review_dataset_by_post();
		$approved_review_dataset = jet_reviews()->reviews_manager->data->get_review_dataset_by_post( false, false, true );

		foreach ( $all_review_dataset as $key => $value ) {
			$not_approved_review_dataset[] = intval( $value ) - intval( $approved_review_dataset[ $key ] );
		}

		$generalDataSet = array(
			array(
				'label'            => __( 'All', 'jet-reviews' ),
				'data'             => $all_review_dataset,
				'backgroundColor'  => 'rgba(35, 156, 255, 0.05)',
				'borderColor'      => 'rgb(35, 156, 255)',
				'borderWidth'      => 2,
				'pointBorderWidth' => 2,
				'pointRadius'      => 3,
				'order'            => 0,
			),
			array(
				'label'            => __( 'Approved', 'jet-reviews' ),
				'data'             => $approved_review_dataset,
				'backgroundColor'  => 'rgba(35, 156, 255, 0.05)',
				'borderColor'      => '#46B450',
				'borderWidth'      => 2,
				'pointBorderWidth' => 2,
				'pointRadius'      => 3,
				'order'            => 1,
				'fill'             => false,
			),
			array(
				'label'            => __( 'Not Approved', 'jet-reviews' ),
				'data'             => $not_approved_review_dataset,
				'backgroundColor'  => 'rgba(35, 156, 255, 0.05)',
				'borderColor'      => '#C92C2C',
				'borderWidth'      => 2,
				'pointBorderWidth' => 2,
				'pointRadius'      => 3,
				'order'            => 1,
				'fill'             => false,
			),

		);

		foreach ( $post_types as $slug => $name ) {

			$post_type_settings = jet_reviews()->settings->get_post_type_data( $slug );

			$postTypesData[] = array(
				'label'               => $name,
				'slug'                => $slug,
				'reviewCount' => array(
					'all'    => jet_reviews()->reviews_manager->data->get_review_count( $slug ),
					'low'    => jet_reviews()->reviews_manager->data->get_review_count( $slug, 'low' ),
					'medium' => jet_reviews()->reviews_manager->data->get_review_count( $slug, 'medium' ),
					'high'   => jet_reviews()->reviews_manager->data->get_review_count( $slug, 'high' ),
				),
				'approvedReviews'     => jet_reviews()->reviews_manager->data->get_approved_review_count( $slug ),
				'allowed'             => $post_type_settings['allowed'],
				'needApprove'         => $post_type_settings['need_approve'],
				'commentsAllowed'     => $post_type_settings['comments_allowed'],
				'commentsNeedApprove' => $post_type_settings['comments_need_approve'],
				'approvalAllowed'     => $post_type_settings['approval_allowed'],
				'reviewType'          => $post_type_settings['review_type'],
				'dataSet' => array(
					array(
						'label'            => $name,
						'data'             => jet_reviews()->reviews_manager->data->get_review_dataset_by_post( $slug ),
						'backgroundColor'  => 'rgba(35, 156, 255, 0.05)',
						'borderColor'      => 'rgb(35, 156, 255)',
						'borderWidth'      => 2,
						'pointBorderWidth' => 2,
						'pointRadius'      => 3,
						'fill'             => true,
					)
				),
			);

			/*$generalDataSet[] = array(
				'label'       => $name,
				'data'        => jet_reviews()->reviews_manager->data->get_review_dataset_by_post( $slug ),
				'borderColor' => jet_reviews_tools()->rand_hex_color(),
				'borderWidth' => 1,
				'order'       => 1,
				'fill'        => false,
			);*/
		}

		$config = array(
			'reviewCount' => array(
				'all'    => jet_reviews()->reviews_manager->data->get_review_count(),
				'low'    => jet_reviews()->reviews_manager->data->get_review_count( false, 'low' ),
				'medium' => jet_reviews()->reviews_manager->data->get_review_count( false, 'medium' ),
				'high'   => jet_reviews()->reviews_manager->data->get_review_count( false, 'high' ),
			),
			'approvedReviewCount'   => jet_reviews()->reviews_manager->data->get_approved_review_count(),
			'commentsCount'         => jet_reviews()->comments_manager->data->get_comment_count(),
			'approvedCommentsCount' => jet_reviews()->comments_manager->data->get_approved_comment_count(),
			'generalDataSets'       => $generalDataSet,
			'postTypes'             => $postTypesData,
			'monthList'             => $month_list,
		);

		return $config;
	}


	/**
	 * Returns dashboard page URL
	 * @return [type] [description]
	 */
	public function dashboard_url() {
		return add_query_arg(
			array( 'page' => $this->admin_page_slug ),
			esc_url( admin_url( 'admin.php' ) )
		);
	}

	/**
	 * Render main admin page
	 *
	 * @return void
	 */
	public function render_page() {
		include jet_reviews()->get_template( 'admin/pages/dashboard/main.php' );
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
