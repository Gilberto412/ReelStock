<?php
namespace Jet_Reviews\User\Verifications;

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

abstract class Base_Verification {

	/**
	 * Condition slug
	 *
	 * @return string
	 */
	abstract public function get_slug();

	/**
	 * Condition slug
	 *
	 * @return string
	 */
	abstract public function get_name();

	/**
	 * [get_invalid_message description]
	 * @return [type] [description]
	 */
	abstract public function get_icon();

	/**
	 * [get_valid_message description]
	 * @return [type] [description]
	 */
	abstract public function get_message();

	/**
	 * Condition check callback
	 *
	 * @return bool
	 */
	abstract public function check();

}
