<?php
namespace Jet_Reviews\Reviews\Source;

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

abstract class Base {

	/**
	 * [get_slug description]
	 * @return [type] [description]
	 */
	abstract public function get_slug();

	/**
	 * [get_name description]
	 * @return [type] [description]
	 */
	abstract public function get_name();

	/**
	 * [get_source_id description]
	 * @return [type] [description]
	 */
	abstract public function get_current_id();

	/**
	 * @return mixed
	 */
	abstract public function get_type();

	/**
	 * @return mixed
	 */
	abstract public function get_item_label();

	/**
	 * @return mixed
	 */
	abstract public function get_item_thumb_url();

	/**
	 * @return mixed
	 */
	abstract public function get_item_decsription();

	/**
	 * [get_source_settings description]
	 * @return [type] [description]
	 */
	abstract public function get_settings();

}
