<?php
namespace Jet_Theme_Core\Template_Conditions;

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

class Mobile_Browsers extends Base {

	/**
	 * Condition slug
	 *
	 * @return string
	 */
	public function get_id() {
		return 'mobile-browsers';
	}

	/**
	 * Condition label
	 *
	 * @return string
	 */
	public function get_label() {
		return __( 'Mobile Browsers', 'jet-theme-core' );
	}

	/**
	 * Condition group
	 *
	 * @return string
	 */
	public function get_group() {
		return 'advanced';
	}

	/**
	 * @return int
	 */
	public function get_priority() {
		return 100;
	}

	/**
	 * @return string
	 */
	public function get_body_structure() {
		return 'jet_page';
	}

	/**
	 * [get_control description]
	 * @return [type] [description]
	 */
	public function get_control() {
		return [
			'type'        => 'f-select',
			'placeholder' => __( 'Select browsers', 'jet-theme-core' ),
		];
	}

	/**
	 * [get_avaliable_options description]
	 * @return [type] [description]
	 */
	public function get_avaliable_options() {
		$browsers = \Detection\MobileDetect::getBrowsers();

		$browsers_options = array_map( function  ( $item ) {
			return [
				'label' => $item,
				'value' => $item,
			];
		}, array_keys( $browsers ) );

		return $browsers_options;
	}


	/**
	 * [get_label_by_value description]
	 * @param  string $value [description]
	 * @return [type]        [description]
	 */
	public function get_label_by_value( $value = '' ) {

		$browser_string = '';

		if ( ! empty( $value ) && is_array( $value ) ) {
			$browser_string = implode( ', ', $value );
		}

		return $browser_string;
	}

	/**
	 * Condition check callback
	 *
	 * @return bools
	 */
	public function check( $args = '' ) {

		if ( empty( $args ) ) {
			return false;
		}

		$mobile_detect = new \Detection\MobileDetect;

		if ( ! $mobile_detect->isMobile() ) {
			return false;
		}

		foreach ( $args as $browser ) {

			if ( $mobile_detect->is( $browser ) ) {
				return true;
			}
		}

		return false;
	}

}
