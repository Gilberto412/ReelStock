<?php
namespace Jet_Reviews\Elementor\Dynamic_Tags;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class Review_Average_Rating extends \Elementor\Core\DynamicTags\Tag {

	public function get_name() {
		return 'review-average-rating';
	}

	public function get_title() {
		return __( 'Review Average Rating', 'jet-reviews' );
	}

	public function get_group() {
		return 'jet_reviews';
	}

	public function get_categories() {
		return array(
			Module::TEXT_CATEGORY,
			Module::NUMBER_CATEGORY,
		);
	}

	protected function register_controls() {

		$this->add_control(
			'average_type',
			array(
				'label'   => __( 'Type', 'jet-reviews' ),
				'type'    => \Elementor\Controls_Manager::SELECT,
				'default' => 'percent',
				'options' => array(
					'percent' => esc_html__( 'Percent', 'jet-reviews' ),
					'ratio'   => esc_html__( 'Ratio', 'jet-reviews' ),
				),
			)
		);

		$this->add_control(
			'ratio_bound',
			array(
				'label'   => __( 'Ratio Bound', 'elementor' ),
				'type'    => \Elementor\Controls_Manager::NUMBER,
				'min'     => 1,
				'max'     => 10000,
				'step'    => 1,
				'default' => 5,
				'condition' => array(
					'average_type' => 'ratio',
				),
			)
		);

		$this->add_control(
			'decimal_count',
			array(
				'label'   => __( 'Decimals Count', 'elementor' ),
				'type'    => \Elementor\Controls_Manager::NUMBER,
				'min'     => 1,
				'max'     => 10,
				'step'    => 1,
				'default' => 1,
			)
		);

	}

	public function render() {

		$current_object = jet_engine()->listings->data->get_current_object();

		if ( ! $current_object ) {
			return;
		}

		$settings = $this->get_settings();

		$average_type = $settings['average_type'];

		$average_rating_percent = (float) $current_object->rating;

		switch ( $average_type ) {
			case 'percent':
				$return_value = $average_rating_percent;

				break;

			case 'ratio':
				$ratio_bound = $settings['ratio_bound'];
				$return_value = ( $average_rating_percent / 100 ) * $ratio_bound;

				break;
		}

		$return_value = number_format( $return_value, $settings['decimal_count'] );

		echo $return_value;

	}

}
