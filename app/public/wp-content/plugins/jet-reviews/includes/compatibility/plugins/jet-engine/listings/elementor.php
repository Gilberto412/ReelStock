<?php
namespace Jet_Reviews\Compatibility\Jet_Engine\Listings;

use Jet_Reviews\Compatibility\Jet_Engine as Module;

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

class Elementor {

	public $manager;

	public function __construct( $manager ) {

		$this->manager = $manager;

		/*add_filter(
			'jet-engine/listings/dynamic-image/fields',
			array( $this, 'add_image_source_fields' ), 10, 2
		);

		add_filter(
			'jet-engine/listings/dynamic-link/fields',
			array( $this->manager, 'add_source_fields' ),
			10, 2
		);*/

		add_action(
			'jet-engine/listings/document/get-preview/' . $this->manager->source,
			array( $this->manager, 'setup_preview' )
		);

	}

	/**
	 * Register content types media fields
	 *
	 * @param [type] $groups [description]
	 */
	/*public function add_image_source_fields( $groups, $for ) {

		foreach ( Module::instance()->manager->get_content_types() as $type => $instance ) {

			$fields = $instance->get_fields_list( $for );
			$prefixed_fields = array();

			if ( empty( $fields ) ) {
				continue;
			}

			foreach ( $fields as $key => $label ) {
				$prefixed_fields[ $type . '::' . $key ] = $label;
			}

			$groups[] = array(
				'label'   => __( 'Content Type:', 'jet-engine' ) . ' ' . $instance->get_arg( 'name' ),
				'options' => $prefixed_fields,
			);
		}

		return $groups;

	}*/

}
