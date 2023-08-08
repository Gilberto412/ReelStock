<?php
namespace Jet_Reviews;

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

abstract class Base_Page {

	/**
	 * [$base_slug description]
	 * @var string
	 */
	public $base_slug = 'jet-reviews';

	/**
	 * [get_slug description]
	 * @return [type] [description]
	 */
	abstract public function get_slug();

	/**
	 * [__construct description]
	 */
	public function __construct() {

		$this->init();

		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_assets' ), 11 );

	}

	/**
	 * Initialize module-specific parts
	 *
	 * @return [type] [description]
	 */
	public function init() {}

	/**
	 * Register module assets
	 *
	 * @return [type] [description]
	 */

	public function enqueue_assets() {

		if ( isset( $_REQUEST['page'] ) && $this->get_slug() === $_REQUEST['page'] ) {
			Admin::get_instance()->get_vue_ui_instance()->enqueue_assets();

			$this->enqueue_module_assets();
		}
	}

	/**
	 * [enqueue_module_assets description]
	 * @return [type] [description]
	 */
	public function enqueue_module_assets() {}

	/**
	 * Returns link to current page
	 *
	 * @return [type] [description]
	 */
	public function get_page_link() {
		return add_query_arg(
			array( 'page' => $this->get_slug() ),
			esc_url( admin_url( 'admin.php' ) )
		);
	}

}
