<?php
namespace Jet_Reviews\User\Conditions;

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

abstract class Base_Condition {

	/**
	 * @return mixed
	 */
	abstract public function get_type();

	/**
	 * Condition slug
	 *
	 * @return string
	 */
	abstract public function get_slug();

	/**
	 * [get_invalid_message description]
	 * @return [type] [description]
	 */
	abstract public function get_invalid_message();

	/**
	 * Condition check callback
	 *
	 * @return bool
	 */
	abstract public function check();

}
