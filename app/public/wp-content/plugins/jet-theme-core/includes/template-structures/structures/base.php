<?php
namespace Jet_Theme_Core\Structures;

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Define Jet_Theme_Core_Structure_Base class
 */
abstract class Base {

	abstract public function get_id();

	abstract public function get_single_label();

	abstract public function get_plural_label();

	abstract public function get_sources();

	abstract public function get_elementor_document_type();

	/**
	 * Is current structure could be outputed as location
	 *
	 * @return boolean
	 */
	public function is_location() {
		return false;
	}

	/**
	 * @return bool
	 */
	public function has_conditions() {
		return true;
	}

	/**
	 * Location name
	 *
	 * @return boolean
	 */
	public function location_name() {
		return '';
	}

	/**
	 * Aproprite location name from Elementor Pro
	 * @return [type] [description]
	 */
	public function pro_location_mapping() {
		return false;
	}

	/**
	 * Library settings for current structure
	 *
	 * @return void
	 */
	public function library_settings() {
		return array(
			'show_title'    => true,
			'show_keywords' => true,
		);
	}

	/**
	 * @return int
	 */
	public function get_admin_bar_priority() {
		return 10;
	}

	public function before_render() {}

	public function after_render() {}

}

