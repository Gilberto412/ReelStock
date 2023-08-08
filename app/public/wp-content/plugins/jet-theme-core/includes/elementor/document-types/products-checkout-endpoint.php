<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class Jet_Products_Checkout_Endpoint_Document extends Jet_Document_Base {

	public function get_name() {
		return 'jet_products_checkout_endpoint';
	}

	public static function get_title() {
		return __( 'Products Checkout Endpoint', 'jet-theme-core' );
	}

	public function has_conditions() {
		return false;
	}

}
