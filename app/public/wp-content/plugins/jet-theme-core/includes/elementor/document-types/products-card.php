<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class Jet_Products_Card_Document extends Jet_Document_Base {

	public function get_name() {
		return 'jet_products_card';
	}

	public static function get_title() {
		return __( 'Products Cart', 'jet-theme-core' );
	}

	public function has_conditions() {
		return false;
	}

}
