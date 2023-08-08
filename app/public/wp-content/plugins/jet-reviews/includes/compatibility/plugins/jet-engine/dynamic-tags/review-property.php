<?php
namespace Jet_Reviews\Elementor\Dynamic_Tags;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class Review_Property extends \Elementor\Core\DynamicTags\Tag {

	public function get_name() {
		return 'review-property';
	}

	public function get_title() {
		return __( 'Review Property', 'jet-reviews' );
	}

	public function get_group() {
		return 'jet_reviews';
	}

	public function get_categories() {
		return array(
			Module::TEXT_CATEGORY,
			Module::NUMBER_CATEGORY,
			Module::URL_CATEGORY,
			Module::IMAGE_CATEGORY,
		);
	}

	protected function register_controls() {

		$this->add_control(
			'prop',
			array(
				'label'   => __( 'Type', 'jet-reviews' ),
				'type'    => \Elementor\Controls_Manager::SELECT,
				'default' => 'rating',
				'options' => array(
					'id'            => __( 'Review ID', 'jet-reviews' ),
					'post_id'       => __( 'Reviewed Object ID', 'jet-reviews' ),
					'post_type'     => __( 'Reviewed Object Type', 'jet-reviews' ),
					'author'        => __( 'Author', 'jet-reviews' ),
					'date'          => __( 'Date', 'jet-reviews' ),
					'title'         => __( 'Title', 'jet-reviews' ),
					'content'       => __( 'Content', 'jet-reviews' ),
					//'type_slug'   => __( 'Type Slug', 'jet-reviews' ),
					//'rating_data' => __( 'Rating Data', 'jet-reviews' ),
					'rating'        => __( 'Rating', 'jet-reviews' ),
					'likes'         => __( 'Likes', 'jet-reviews' ),
					'dislikes'      => __( 'Dislikes', 'jet-reviews' ),
					//'approved'    => __( 'Approved', 'jet-reviews' ),
					//'pinned'      => __( 'Pinned', 'jet-reviews' ),
				),
			)
		);

	}

	public function render() {

		$settings = $this->get_settings();
		$prop     = ! empty( $settings['prop'] ) ? $settings['prop'] : false;

		if ( ! $prop ) {
			return;
		}

		$prefixed_prop  = 'jet_reviews::' . $prop;
		$current_object = jet_engine()->listings->data->get_current_object();

		if ( ! $current_object ) {
			return;
		}

		if ( isset( $current_object->$prefixed_prop ) ) {
			$value = $current_object->$prefixed_prop;
		} elseif ( isset( $current_object->$prop ) ) {
			$value = $current_object->$prop;
		} else {
			return;
		}

		echo $value;

	}

}
