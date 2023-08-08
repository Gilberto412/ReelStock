<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class Jet_Account_Page_Document extends Jet_Document_Base {

	public function get_name() {
		return 'jet_account_page';
	}

	public static function get_title() {
		return __( 'Account Page', 'jet-theme-core' );
	}

	public function has_conditions() {
		return false;
	}

}
