<?php
namespace Jet_Theme_Core\Structures;

class Section extends Base {

	public function get_id() {
		return 'jet_section';
	}

	public function get_single_label() {
		return esc_html__( 'Section', 'jet-theme-core' );
	}

	public function get_plural_label() {
		return esc_html__( 'Sections', 'jet-theme-core' );
	}

	public function get_sources() {
		return array( 'jet-theme', 'jet-api' );
	}

	public function get_elementor_document_type() {
		return array(
			'class' => 'Jet_Section_Document',
			'file'   => jet_theme_core()->plugin_path( 'includes/elementor/document-types/section.php' ),
		);
	}

	/**
	 * @return bool
	 */
	public function has_conditions() {
		return false;
	}

	/**
	 * Library settings for current structure
	 *
	 * @return void
	 */
	public function library_settings() {

		return array(
			'show_title'    => false,
			'show_keywords' => true,
		);

	}

}
