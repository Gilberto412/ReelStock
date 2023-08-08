<?php
namespace Jet_Theme_Core\Structures;

class Products_Card extends Base {

	public function get_id() {
		return 'jet_products_card';
	}

	public function get_single_label() {
		return esc_html__( 'Products Cart', 'jet-theme-core' );
	}

	public function get_plural_label() {
		return esc_html__( 'Products Cart', 'jet-theme-core' );
	}

	public function get_elementor_document_type() {
		return array(
			'class' => 'Jet_Products_Card_Document',
			'file'   => jet_theme_core()->plugin_path( 'includes/elementor/document-types/products-card.php' ),
		);
	}

	/**
	 * Location name
	 *
	 * @return boolean
	 */
	public function location_name() {
		return 'products-card';
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

	public function get_sources() {
		return [];
	}
}
