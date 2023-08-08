<?php
namespace Jet_Reviews\Reviews\Source;

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

class Post extends Base {

	/**
	 * [get_slug description]
	 * @return [type] [description]
	 */
	public function get_slug() {
		return 'post';
	}

	/**
	 * [get_slug description]
	 * @return [type] [description]
	 */
	public function get_name() {
		return __( 'Post', 'jet-reviews' );
	}

	/**
	 * [get_source_id description]
	 * @return [type] [description]
	 */
	public function get_current_id() {
		$current_id = get_the_ID();

		return apply_filters( "jet-reviews/source/source-{$this->get_slug()}/current-id", $current_id, $this );
	}

	/**
	 * @return false|mixed|string
	 */
	public function get_type( $args = [] ) {
		$post_id = $this->get_current_id();

		if ( ! empty( $args['source_id'] ) ) {
			$post_id = $args['source_id'];
		}

		return get_post_type( $post_id );
	}

	/**
	 * @param array $args
	 *
	 * @return mixed|string
	 */
	public function get_item_label( $args = [] ) {
		$post_id = $this->get_current_id();

		if ( ! empty( $args['source_id'] ) ) {
			$post_id = $args['source_id'];
		}

		return get_the_title( $post_id );
	}

	/**
	 * @param array $args
	 *
	 * @return mixed|string
	 */
	public function get_item_decsription( $args = [] ) {
		$post_id = $this->get_current_id();

		if ( ! empty( $args['source_id'] ) ) {
			$post_id = $args['source_id'];
		}

		return get_the_excerpt( $post_id );
	}

	/**
	 * @param array $args
	 *
	 * @return false|mixed|string
	 */
	public function get_item_thumb_url( $args = [] ) {
		$post_id = $this->get_current_id();

		if ( ! empty( $args['source_id'] ) ) {
			$post_id = $args['source_id'];
		}

		return get_the_post_thumbnail_url( $post_id );
	}

	/**
	 * [get_source_settings description]
	 * @return [type] [description]
	 */
	public function get_settings() {
		return array();
	}

}
