<?php
namespace Jet_Theme_Core\Structures;

class Products_Archive extends Base {

	public function get_id() {
		return 'jet_products_archive';
	}

	public function get_single_label() {
		return esc_html__( 'Products Archive', 'jet-theme-core' );
	}

	public function get_plural_label() {
		return esc_html__( 'Products Archives', 'jet-theme-core' );
	}

	public function get_elementor_document_type() {
		return array(
			'class' => 'Jet_Products_Archive_Document',
			'file'   => jet_theme_core()->plugin_path( 'includes/elementor/document-types/products-archive.php' ),
		);
	}

	/**
	 * Location name
	 *
	 * @return boolean
	 */
	public function location_name() {
		return 'products-archive';
	}

	/**
	 * @return false
	 */
	public function has_conditions() {
		return false;
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
		return 'archive';
	}*/

	public function get_sources() {
		return [];
	}
}
