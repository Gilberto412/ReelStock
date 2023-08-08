<?php
namespace Jet_Reviews\Settings;

use Jet_Dashboard\Dashboard as Dashboard;
use Jet_Dashboard\Base\Page_Module as Page_Module_Base;

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

class Advanced extends Page_Module_Base {

	/**
	 * Returns module slug
	 *
	 * @return void
	 */
	public function get_page_slug() {
		return 'jet-reviews-advanced';
	}

	/**
	 * [get_subpage_slug description]
	 * @return [type] [description]
	 */
	public function get_parent_slug() {
		return 'settings-page';
	}

	/**
	 * [get_page_name description]
	 * @return [type] [description]
	 */
	public function get_page_name() {
		return esc_html__( 'Advanced', 'jet-reviews' );
	}

	/**
	 * [get_category description]
	 * @return [type] [description]
	 */
	public function get_category() {
		return 'jet-reviews-settings';
	}

	/**
	 * [get_page_link description]
	 * @return [type] [description]
	 */
	public function get_page_link() {
		return Dashboard::get_instance()->get_dashboard_page_url( $this->get_parent_slug(), $this->get_page_slug() );
	}

	/**
	 * Enqueue module-specific assets
	 *
	 * @return void
	 */
	public function enqueue_module_assets() {

		wp_enqueue_style(
			'jet-reviews-admin-css',
			jet_reviews()->plugin_url( 'assets/css/admin.css' ),
			false,
			jet_reviews()->get_version()
		);

		wp_enqueue_script(
			'jet-reviews-admin-vue-components',
			jet_reviews()->plugin_url( 'assets/js/admin/vue-components.js' ),
			array( 'cx-vue-ui', 'wp-api-fetch' ),
			jet_reviews()->get_version(),
			true
		);

		wp_localize_script(
			'jet-reviews-admin-vue-components',
			'JetReviewsSettingsConfig',
			apply_filters( 'jet-reviews/admin/settings-page/localized-config', jet_reviews()->settings->settings_page_config() )
		);

	}

	/**
	 * License page config
	 *
	 * @param  array  $config  [description]
	 * @param  string $subpage [description]
	 * @return [type]          [description]
	 */
	public function page_config( $config = array(), $page = false, $subpage = false ) {

		$config['pageModule'] = $this->get_parent_slug();
		$config['subPageModule'] = $this->get_page_slug();

		return $config;
	}

	/**
	 * [page_templates description]
	 * @param  array  $templates [description]
	 * @param  string $subpage   [description]
	 * @return [type]            [description]
	 */
	public function page_templates( $templates = array(), $page = false, $subpage = false ) {

		$templates['jet-reviews-advanced'] = jet_reviews()->plugin_path( 'templates/admin-templates/advanced.php' );

		return $templates;
	}
}
