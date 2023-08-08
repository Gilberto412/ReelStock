<?php
namespace Jet_Theme_Core\Elementor;

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

class Locations {

	/**
	 * @var array
	 */
	private $_pro_locations = array();

	/**
	 * Return all locations data
	 *
	 * @return array
	 */
	public function get_locations() {
		return $this->_pro_locations;
	}

	/**
	 * @param $id
	 * @param $structure_instance
	 */
	public function define_pro_locations( $id, $structure_instance ) {

		if ( $structure_instance->pro_location_mapping() ) {
			$this->_pro_locations[ $id ] = $structure_instance;
		}
	}

	/**
	 * Register Elementor Pro locations
	 *
	 * @param  [type] $elementor_theme_manager [description]
	 * @return [type]                          [description]
	 */
	public function register_elementor_locations( $elementor_theme_manager ) {

		if ( ! \Jet_Theme_Core\Utils::has_elementor_pro() ) {
			return false;
		}

		foreach ( $this->get_locations() as $jet_location => $structure_instance ) {

			if ( ! $structure_instance->pro_location_mapping() ) {
				continue;
			}

			$elementor_theme_manager->register_location( $structure_instance->location_name(), [
				'label'     => $structure_instance->get_single_label(),
				'is_core'   => true,
				'overwrite' => true,
				'public'    => false,
			] );
		}
	}

	/**
	 * @param int $template_id
	 * @param string $location
	 */
	public function render_elementor_template_content( $render_status, $template_id, $location ) {

		// Define elementor location render instance
		$elementor_location = new \Jet_Theme_Core\Locations\Render\Elementor_Location_Render( [
			'template_id' => $template_id,
			'location'    => $location,
		] );

		$render_status = $elementor_location->render();

		return $render_status;
	}

	/**
	 * Enqueue locations styles
	 *
	 * @return void
	 */
	public function enqueue_locations_styles() {

		$template_ids = [];

		$matched_page_template_layouts = jet_theme_core()->theme_builder->frontend_manager->get_matched_page_template_layouts();

		if ( ! empty( $matched_page_template_layouts )  ) {

			foreach ( $matched_page_template_layouts as $layout => $layout_data  ) {
				if ( $layout_data['id'] ) {
					$template_ids[] = $layout_data['id'];
				}
			}
		}

		$structures = jet_theme_core()->structures->get_structures();

		if ( ! empty( $structures ) ) {
			$current_page_id = get_the_ID();

			foreach ( $structures as $structure => $structure_obj ) {

				$structure_template_ids = jet_theme_core()->template_conditions_manager->find_matched_conditions( $structure_obj->get_id() );

				if ( ! empty( $structure_template_ids ) ) {

					foreach ( $structure_template_ids as $template_id ) {

						if ( $current_page_id !== $template_id ) {
							$template_ids[] = $template_id;
						}
					}
				}
			}
		}

		if ( empty( $template_ids ) ) {
			return;
		}

		$template_ids = array_unique( $template_ids );

		\Elementor\Plugin::instance()->frontend->enqueue_styles();

		foreach ( $template_ids as $template_id ) {
			$css_file = new \Elementor\Core\Files\CSS\Post( $template_id );
			$css_file->enqueue();
		}

	}

	/**
	 * Locations constructor.
	 */
	function __construct() {
		add_action( 'jet-theme-core/locations/register', array( $this, 'define_pro_locations' ), 10, 2 );
		add_action( 'elementor/theme/register_locations', array( $this, 'register_elementor_locations' ) );
		add_filter( 'jet-theme-core/location/render/elementor-location-content', array( $this, 'render_elementor_template_content' ), 10, 3 );
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_locations_styles' ) );
	}

}
