<?php
namespace Jet_Theme_Core\Template_Conditions;

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

class Mobile_OS extends Base {

	/**
	 * Condition slug
	 *
	 * @return string
	 */
	public function get_id() {
		return 'mobile-os';
	}

	/**
	 * Condition label
	 *
	 * @return string
	 */
	public function get_label() {
		return __( 'Mobile OS', 'jet-theme-core' );
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
			'placeholder' => __( 'Select device', 'jet-theme-core' ),
		];
	}

	/**
	 * [get_avaliable_options description]
	 * @return [type] [description]
	 */
	public function get_avaliable_options() {

		$operating_systems = \Detection\MobileDetect::getOperatingSystems();

		$operating_systems_options = array_map( function  ( $item ) {
			return [
				'label' => $item,
				'value' => $item,
			];
		}, array_keys( $operating_systems ) );

		return $operating_systems_options;
	}


	/**
	 * [get_label_by_value description]
	 * @param  string $value [description]
	 * @return [type]        [description]
	 */
	public function get_label_by_value( $value = '' ) {

		$device_string = '';

		if ( ! empty( $value ) && is_array( $value ) ) {
			$device_string = implode( ', ', $value );
		}

		return $device_string;
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

		foreach ( $args as $os_type ) {
			if ( $mobile_detect->is( $os_type ) ) {
				return true;
			}
		}

		return false;
	}

}
