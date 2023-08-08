<?php
namespace Jet_Theme_Core\Structures;

class Account_Page extends Base {

	public function get_id() {
		return 'jet_account_page';
	}

	public function get_single_label() {
		return esc_html__( 'Account Page', 'jet-theme-core' );
	}

	public function get_plural_label() {
		return esc_html__( 'Account Page', 'jet-theme-core' );
	}

	public function get_elementor_document_type() {
		return array(
			'class' => 'Jet_Account_Page_Document',
			'file'   => jet_theme_core()->plugin_path( 'includes/elementor/document-types/account-page.php' ),
		);
	}

	/**
	 * Location name
	 *
	 * @return boolean
	 */
	public function location_name() {
		return 'account-page';
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
