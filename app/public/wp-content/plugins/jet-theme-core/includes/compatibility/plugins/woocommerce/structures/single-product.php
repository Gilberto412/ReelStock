<?php
namespace Jet_Theme_Core\Structures;

class Single_Product extends Base {

	public function get_id() {
		return 'jet_single_product';
	}

	public function get_single_label() {
		return esc_html__( 'Single Product', 'jet-theme-core' );
	}

	public function get_plural_label() {
		return esc_html__( 'Single Product', 'jet-theme-core' );
	}

	public function get_elementor_document_type() {
		return array(
			'class' => 'Jet_Single_Product_Document',
			'file'   => jet_theme_core()->plugin_path( 'includes/elementor/document-types/single-product.php' ),
		);
	}

	/**
	 * Location name
	 *
	 * @return boolean
	 */
	public function location_name() {
		return 'single-product';
	}

	/**
	 * Is current structure could be outputed as location
	 *
	 * @return boolean
	 */
	public function is_location() {
		return true;
	}

	/**
	 * Aproprite location name from Elementor Pro
	 * @return [type] [description]
	 */
	/*public function pro_location_mapping() {
		return 'single';
	}*/

	/**
	 * @return false
	 */
	public function has_conditions() {
		return false;
	}

	public function get_sources() {
		return [];
	}

}
