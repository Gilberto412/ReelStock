<?php
namespace Jet_Reviews\Elementor\Dynamic_Tags;

use Elementor\Utils;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class Review_Author_Avatar extends \Elementor\Core\DynamicTags\Data_Tag {

	public function get_name() {
		return 'review-author-avatar';
	}

	public function get_title() {
		return __( 'Review Author Avatar', 'jet-reviews' );
	}

	public function get_group() {
		return 'jet_reviews';
	}

	public function get_categories() {
		return array(
			Module::IMAGE_CATEGORY,
		);
	}

	protected function register_controls() {}

	public function get_value( array $options = [] ) {

		$current_object = jet_engine()->listings->data->get_current_object();

		if ( ! $current_object ) {
			return;
		}

		$settings = $this->get_settings();

		$user_id = $current_object->author;

		if ( ! $user_id ) {
			return [
				'id'  => '',
				'url' => Utils::get_placeholder_image_src(),
			];
		}

		$raw_user_data = jet_reviews()->user_manager->get_raw_user_data( $user_id );

		$url = get_avatar_url( $raw_user_data['mail'], array(
			'size' => 256,
		) );

		return [
			'id'  => $user_id,
			'url' => $url,
		];

	}

}
