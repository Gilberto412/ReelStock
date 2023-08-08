<?php
namespace Jet_Reviews\Elementor\Dynamic_Tags;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class Reviews_Info extends \Elementor\Core\DynamicTags\Tag {

	public function get_name() {
		return 'reviews-info';
	}

	public function get_title() {
		return __( 'Reviews Info', 'jet-reviews' );
	}

	public function get_group() {
		return 'jet_reviews';
	}

	public function get_categories() {
		return array(
			Module::TEXT_CATEGORY,
		);
	}

	protected function register_controls() {

		$this->add_control(
			'info_type',
			array(
				'label'   => __( 'Type', 'jet-reviews' ),
				'type'    => \Elementor\Controls_Manager::SELECT,
				'default' => 'reviews_count',
				'options' => array(
					'reviews_count'       => esc_html__( 'Reviews Count', 'jet-reviews' ),
					'reviews_count_label' => esc_html__( 'Reviews Count Label', 'jet-reviews' ),
				),
			)
		);

	}

	public function render() {

		$settings = $this->get_settings();

		$info_type = $settings['info_type'];

		$post_id = get_the_ID();

		$table_name = jet_reviews()->db->tables( 'reviews', 'name' );

		$query = jet_reviews()->db->wpdb()->prepare(
			"SELECT COUNT(*) FROM $table_name WHERE post_id = %d AND approved=1",
			$post_id
		);

		$reviews_count = (float) jet_reviews()->db->wpdb()->get_var( $query );

		switch ( $info_type ) {
			case 'reviews_count':
				$return_value = $reviews_count;

				break;

			case 'reviews_count_label':
				$return_value = sprintf( _n( '1 Review', '%s Reviews', $reviews_count, 'jet-reviews' ), $reviews_count );

				break;
		}

		echo $return_value;

	}

}
