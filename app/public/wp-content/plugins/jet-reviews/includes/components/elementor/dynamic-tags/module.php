<?php
namespace Jet_Reviews\Elementor\Dynamic_Tags;

class Module extends \Elementor\Modules\DynamicTags\Module {

	/**
	 * [get_tag_classes_names description]
	 * @return [type] [description]
	 */
	public function get_tag_classes_names() {
		return apply_filters( 'jet-reviews/elementor/registered-dynamic-tags', array(
			'\\Jet_Reviews\\Elementor\\Dynamic_Tags\\Average_Rating' => jet_reviews()->plugin_path( 'includes/components/elementor/dynamic-tags/average-rating.php' ),
			'\\Jet_Reviews\\Elementor\\Dynamic_Tags\\Reviews_Info'   => jet_reviews()->plugin_path( 'includes/components/elementor/dynamic-tags/reviews-info.php' ),
		) );
	}

	/**
	 * [get_groups description]
	 * @return [type] [description]
	 */
	public function get_groups() {
		return array(
			'jet_reviews' => array(
				'title' => __( 'JetReviews', 'jet-reviews' ),
			),
		);
	}

	/**
	 * Register tags.
	 *
	 * Add all the available dynamic tags.
	 *
	 * @since  2.0.0
	 * @access public
	 *
	 * @param Manager $dynamic_tags
	 */
	public function register_tags( $dynamic_tags ) {

		foreach ( $this->get_tag_classes_names() as $tag_class => $tag_filepath ) {

			if ( file_exists( $tag_filepath ) ) {
				require $tag_filepath;
			}

			if ( class_exists( $tag_class ) ) {
				$dynamic_tags->register_tag( $tag_class );
			}
		}

	}

}
