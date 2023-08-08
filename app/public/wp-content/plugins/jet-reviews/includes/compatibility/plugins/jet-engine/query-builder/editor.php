<?php
namespace Jet_Reviews\Compatibility\Jet_Engine\Query_Builder;

use Jet_Reviews\Compatibility\Jet_Engine as Module;

class Query_Editor extends \Jet_Engine\Query_Builder\Query_Editor\Base_Query {

	/**
	 * Qery type ID
	 */
	public function get_id() {
		return Manager::instance()->slug;
	}

	/**
	 * Qery type name
	 */
	public function get_name() {
		return __( 'Jet Reviews Query', 'jet-engine' );
	}

	/**
	 * Returns Vue component name for the Query editor for the current type.
	 * I
	 * @return [type] [description]
	 */
	public function editor_component_name() {
		return 'jet-reviews-query';
	}

	/**
	 * Returns Vue component template for the Query editor for the current type.
	 *
	 * @return [type] [description]
	 */
	public function editor_component_data() {

		return [
			'sourceOptions' => jet_reviews()->reviews_manager->sources->get_registered_source_options(),
		];
	}

	/**
	 * Returns Vue component template for the Query editor for the current type.
	 * I
	 * @return [type] [description]
	 */
	public function editor_component_template() {
		ob_start();
		include Module::instance()->get_file_path( 'templates/admin/query-editor.php' );
		return ob_get_clean();
	}

	/**
	 * Returns Vue component template for the Query editor for the current type.
	 * I
	 * @return [type] [description]
	 */
	public function editor_component_file() {
		return Module::instance()->get_file_url( 'assets/js/admin/query-editor.js' );
	}

}
