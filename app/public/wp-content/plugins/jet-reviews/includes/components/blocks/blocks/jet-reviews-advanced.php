<?php
namespace Jet_Reviews\Blocks;

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

class Jet_Reviews_Advanced extends Base {

	/**
	 * Returns block name
	 *
	 * @return string
	 */
	public function get_name() {
		return 'jet-reviews-advanced';
	}

	/**
	 * Return attributes array
	 *
	 * @return array
	 */
	public function get_attributes() {

		return [
			'__internalWidgetId'         => [
				'type'    => 'string',
				'default' => '',
			],
			'source'                     => [
				'type'    => 'string',
				'default' => 'post',
			],
			'ratingLayout'               => [
				'type'    => 'string',
				'default' => 'stars-field',
			],
			'ratingInputType'            => [
				'type'    => 'string',
				'default' => 'stars-input',
			],
			'reviewRatingType'           => [
				'type'    => 'string',
				'default' => 'average',
			],
			'reviewsPerPage'             => [
				'type'    => 'number',
				'default' => 10,
			],
			'reviewAuthorAvatarVisible'  => [
				'type'    => 'boolean',
				'default' => true,
			],
			'reviewTitleVisible'         => [
				'type'    => 'boolean',
				'default' => true,
			],
			'commentAuthorAvatarVisible' => [
				'type'    => 'boolean',
				'default' => true,
			],
			'pinnedIconId'               => [
				'type'    => 'number',
				'default' => 0,
			],
			'pinnedIconUrl'              => [
				'type'    => 'string',
				'default' => '',
			],
			'emptyStarIconId'            => [
				'type'    => 'number',
				'default' => 0,
			],
			'emptyStarIconUrl'           => [
				'type'    => 'string',
				'default' => '',
			],
			'filledStarIconId'           => [
				'type'    => 'number',
				'default' => 0,
			],
			'filledStarIconUrl'          => [
				'type'    => 'string',
				'default' => '',
			],
			'newReviewButtonIconId'      => [
				'type'    => 'number',
				'default' => 0,
			],
			'newReviewButtonIconUrl'     => [
				'type'    => 'string',
				'default' => '',
			],
			'showCommentsButtonIconId'   => [
				'type'    => 'number',
				'default' => 0,
			],
			'showCommentsButtonIconUrl'  => [
				'type'    => 'string',
				'default' => '',
			],
			'newCommentButtonIconId'     => [
				'type'    => 'number',
				'default' => 0,
			],
			'newCommentButtonIconUrl'    => [
				'type'    => 'string',
				'default' => '',
			],
			'reviewEmptyLikeIconId'      => [
				'type'    => 'number',
				'default' => 0,
			],
			'reviewEmptyLikeIconUrl'     => [
				'type'    => 'string',
				'default' => '',
			],
			'reviewFilledLikeIconId'     => [
				'type'    => 'number',
				'default' => 0,
			],
			'reviewFilledLikeIconUrl'    => [
				'type'    => 'string',
				'default' => '',
			],
			'reviewEmptyDislikeIconId'   => [
				'type'    => 'number',
				'default' => 0,
			],
			'reviewEmptyDislikeIconUrl'  => [
				'type'    => 'string',
				'default' => '',
			],
			'reviewFilledDislikeIconId'  => [
				'type'    => 'number',
				'default' => 0,
			],
			'reviewFilledDislikeIconUrl' => [
				'type'    => 'string',
				'default' => '',
			],
			'replyButtonIconId'          => [
				'type'    => 'number',
				'default' => 0,
			],
			'replyButtonIconUrl'         => [
				'type'    => 'string',
				'default' => '',
			],
			'prevIconId'          => [
				'type'    => 'number',
				'default' => 0,
			],
			'prevIconUrl'         => [
				'type'    => 'string',
				'default' => '',
			],
			'nextIconId'          => [
				'type'    => 'number',
				'default' => 0,
			],
			'nextIconUrl'         => [
				'type'    => 'string',
				'default' => '',
			],

			'noReviewsLabel'    => [
				'type'    => 'string',
				'default' => '',
			],
			'singularReviewCountLabel'    => [
				'type'    => 'string',
				'default' => '',
			],
			'pluralReviewCountLabel'    => [
				'type'    => 'string',
				'default' => '',
			],
			'cancelButtonLabel'    => [
				'type'    => 'string',
				'default' => '',
			],
			'newReviewButton'    => [
				'type'    => 'string',
				'default' => '',
			],
			'authorNamePlaceholder'    => [
				'type'    => 'string',
				'default' => '',
			],
			'authorMailPlaceholder'    => [
				'type'    => 'string',
				'default' => '',
			],
			'reviewContentPlaceholder'    => [
				'type'    => 'string',
				'default' => '',
			],
			'reviewTitlePlaceholder'    => [
				'type'    => 'string',
				'default' => '',
			],
			'submitReviewButton'    => [
				'type'    => 'string',
				'default' => '',
			],
			'newCommentButton'    => [
				'type'    => 'string',
				'default' => '',
			],
			'showCommentsButton'    => [
				'type'    => 'string',
				'default' => '',
			],
			'hideCommentsButton'    => [
				'type'    => 'string',
				'default' => '',
			],
			'сommentsTitle'    => [
				'type'    => 'string',
				'default' => '',
			],
			'commentPlaceholder'    => [
				'type'    => 'string',
				'default' => '',
			],
			'submitCommentButton'    => [
				'type'    => 'string',
				'default' => '',
			],
			'replyButton'    => [
				'type'    => 'string',
				'default' => '',
			],
			'replyPlaceholder'    => [
				'type'    => 'string',
				'default' => '',
			],
			'submitReplyButton'    => [
				'type'    => 'string',
				'default' => '',
			],
			'alreadyReviewedMessage'    => [
				'type'    => 'string',
				'default' => '',
			],
			'moderatorCheckMessage'    => [
				'type'    => 'string',
				'default' => '',
			],
			'notValidFieldMessage'    => [
				'type'    => 'string',
				'default' => '',
			],
		];
	}

	/**
	 * Return callback
	 *
	 * @return html
	 */
	public function render_callback( $settings = [] ) {

		$prev_icon = jet_reviews_tools()->get_svg_icon_html( $settings[ 'prevIconId' ], jet_reviews_tools()->get_svg_html( 'prevIcon' ), [],  false );
		$next_icon = jet_reviews_tools()->get_svg_icon_html( $settings[ 'nextIconId' ], jet_reviews_tools()->get_svg_html( 'nextIcon' ), [],  false );

		$instance_settings = array(
			'source'                     => isset( $settings[ 'source' ] ) ? $settings[ 'source' ] : 'post',
			'ratingLayout'               => $settings[ 'ratingLayout' ],
			'ratingInputType'            => $settings[ 'ratingInputType' ],
			'reviewRatingType'           => $settings[ 'reviewRatingType' ],
			'reviewsPerPage'             => $settings[ 'reviewsPerPage' ],
			'reviewAuthorAvatarVisible'  => filter_var( $settings[ 'reviewAuthorAvatarVisible' ], FILTER_VALIDATE_BOOLEAN ),
			'reviewTitleVisible'         => filter_var( $settings[ 'reviewTitleVisible' ], FILTER_VALIDATE_BOOLEAN ),
			'commentAuthorAvatarVisible' => filter_var( $settings[ 'commentAuthorAvatarVisible' ], FILTER_VALIDATE_BOOLEAN ),
			'icons' => [
				'emptyStarIcon'           => jet_reviews_tools()->get_svg_icon_html( $settings[ 'emptyStarIconId' ], jet_reviews_tools()->get_svg_html( 'emptyStarIcon' ), [],  false ),
				'filledStarIcon'           => jet_reviews_tools()->get_svg_icon_html( $settings[ 'filledStarIconId' ], jet_reviews_tools()->get_svg_html( 'filledStarIcon' ), [],  false ),
				'newReviewButtonIcon'     => jet_reviews_tools()->get_svg_icon_html( $settings[ 'newReviewButtonIconId' ], jet_reviews_tools()->get_svg_html( 'newReviewButtonIcon' ), [],  false ),
				'showCommentsButtonIcon'  => jet_reviews_tools()->get_svg_icon_html( $settings[ 'showCommentsButtonIconId' ], jet_reviews_tools()->get_svg_html( 'showCommentsButtonIcon' ), [],  false ),
				'newCommentButtonIcon'    => jet_reviews_tools()->get_svg_icon_html( $settings[ 'newCommentButtonIconId' ], jet_reviews_tools()->get_svg_html( 'newCommentButtonIcon' ), [],  false ),
				'pinnedIcon'              => jet_reviews_tools()->get_svg_icon_html( $settings[ 'pinnedIconId' ], jet_reviews_tools()->get_svg_html( 'pinnedIcon' ), [],  false ),
				'reviewEmptyLikeIcon'     => jet_reviews_tools()->get_svg_icon_html( $settings[ 'reviewEmptyLikeIconId' ], jet_reviews_tools()->get_svg_html( 'reviewEmptyLikeIcon' ), [],  false ),
				'reviewFilledLikeIcon'    => jet_reviews_tools()->get_svg_icon_html( $settings[ 'reviewFilledLikeIconId' ], jet_reviews_tools()->get_svg_html( 'reviewFilledLikeIcon' ), [],  false ),
				'reviewEmptyDislikeIcon'  => jet_reviews_tools()->get_svg_icon_html( $settings[ 'reviewEmptyDislikeIconId' ], jet_reviews_tools()->get_svg_html( 'reviewEmptyDislikeIcon' ), [],  false ),
				'reviewFilledDislikeIcon' => jet_reviews_tools()->get_svg_icon_html( $settings[ 'reviewFilledDislikeIconId' ], jet_reviews_tools()->get_svg_html( 'reviewFilledDislikeIcon' ), [],  false ),
				'replyButtonIcon'         => jet_reviews_tools()->get_svg_icon_html( $settings[ 'replyButtonIconId' ], jet_reviews_tools()->get_svg_html( 'replyButtonIcon' ), [],  false ),
				'prevIcon'                => ! is_rtl() ? $prev_icon : $next_icon,
				'nextIcon'                => ! is_rtl() ? $next_icon : $prev_icon,
			],
			'labels' => [
				'noReviewsLabel'           => esc_attr( $settings[ 'noReviewsLabel' ] ),
				'singularReviewCountLabel' => esc_attr( $settings[ 'singularReviewCountLabel' ] ),
				'pluralReviewCountLabel'   => esc_attr( $settings[ 'pluralReviewCountLabel' ] ),
				'cancelButtonLabel'        => esc_attr( $settings[ 'cancelButtonLabel' ] ),
				'newReviewButton'          => esc_attr( $settings[ 'newReviewButton' ] ),
				'authorNamePlaceholder'    => esc_attr( $settings[ 'authorNamePlaceholder' ] ),
				'authorMailPlaceholder'    => esc_attr( $settings[ 'authorMailPlaceholder' ] ),
				'reviewContentPlaceholder' => esc_attr( $settings[ 'reviewContentPlaceholder' ] ),
				'reviewTitlePlaceholder'   => esc_attr( $settings[ 'reviewTitlePlaceholder' ] ),
				'submitReviewButton'       => esc_attr( $settings[ 'submitReviewButton' ] ),
				'newCommentButton'         => esc_attr( $settings[ 'newCommentButton' ] ),
				'showCommentsButton'       => esc_attr( $settings[ 'showCommentsButton' ] ),
				'hideCommentsButton'       => esc_attr( $settings[ 'hideCommentsButton' ] ),
				'сommentsTitle'            => esc_attr( $settings[ 'сommentsTitle' ] ),
				'commentPlaceholder'       => esc_attr( $settings[ 'commentPlaceholder' ] ),
				'submitCommentButton'      => esc_attr( $settings[ 'submitCommentButton' ] ),
				'replyButton'              => esc_attr( $settings[ 'replyButton' ] ),
				'replyPlaceholder'         => esc_attr( $settings[ 'replyPlaceholder' ] ),
				'submitReplyButton'        => esc_attr( $settings[ 'submitReplyButton' ] ),
				'alreadyReviewedMessage'   => esc_attr( $settings[ 'alreadyReviewedMessage' ] ),
				'moderatorCheckMessage'    => esc_attr( $settings[ 'moderatorCheckMessage' ] ),
				'notValidFieldMessage'     => esc_attr( $settings[ 'notValidFieldMessage' ] ),
			],
		);

		$render_widget_instance = new \Jet_Reviews\Reviews\Review_Listing_Render( $instance_settings );

		ob_start();

		$render_widget_instance->render();

		return ob_get_clean();
	}

	/**
	 * @return void
	 */
	public function add_style_manager_options() {}

}
