<?php
namespace Jet_Reviews\Endpoints;

use Jet_Reviews\Reviews\Data as Reviews_Data;
use Jet_Reviews\Comments\Data as Comments_Data;

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}
/**
 * Define Posts class
 */
class Pull_Product_Reviews extends Base {

	/**
	 * @var array
	 */
	public $products_wp_comments = [];

	/**
	 * @var array
	 */
	public $product_reviews = [];

	/**
	 * @var array
	 */
	public $product_comments = [];

	/**
	 * [get_method description]
	 * @return [type] [description]
	 */
	public function get_method() {
		return 'POST';
	}

	/**
	 * Returns route name
	 *
	 * @return string
	 */
	public function get_name() {
		return 'pull-product-reviews';
	}

	/**
	 * Returns arguments config
	 *
	 * @return [type] [description]
	 */
	public function get_args() {

		return array(
			'products' => array(
				'default'  => '',
				'required' => false,
			),
		);
	}

	/**
	 * [callback description]
	 * @param  [type]   $request [description]
	 * @return function          [description]
	 */
	public function callback( $request ) {
		$data = $request->get_params();
		$products = $data['products'];

		if ( empty( $products ) ) {
			return rest_ensure_response( [
				'success' => false,
				'message' => __( 'Select products', 'jet-reviews' ),
				'data'    => [],
			] );
		}

		$products_wp_comments = [];
		$products_review_ids = [];
		$products_comment_ids = [];

		if ( in_array( 'all', $products ) ) {
			$this->products_wp_comments = get_comments( [
				'post_type'   => 'product',
				'order'       => 'ASC',
			] );

		} else {
			foreach ( $products as $product_id ) {
				$product_wp_comments = get_comments( [
					'post_id' => $product_id,
					'order'   => 'ASC'
				] );
				$this->products_wp_comments = array_merge( $products_wp_comments, $product_wp_comments );
			}
		}

		$this->product_reviews = array_filter( $this->products_wp_comments, function ( $comment ) {
			return 'review' === $comment->comment_type;
		} );

		$this->product_comments = array_filter( $this->products_wp_comments, function ( $comment ) {
			return 'comment' === $comment->comment_type;
		} );

		// Pull Reviews
		foreach ( $this->product_reviews as $comment ) {
			//delete_comment_meta( $comment->comment_ID, '_jet_reviews_woo_pulled' );
			$is_pulled = get_comment_meta( $comment->comment_ID, '_jet_reviews_woo_pulled', true );

			if ( filter_var( $is_pulled, FILTER_VALIDATE_BOOLEAN ) ) {
				continue;
			}

			$review_rating = intval( get_comment_meta( $comment->comment_ID, 'rating', true ) );
			$user_obj = get_user_by( 'email', $comment->comment_author_email );

			$prepared_data = [
				'source'      => 'post',
				'post_id'     => $comment->comment_post_ID,
				'post_type'   => 'product',
				'author'      => $user_obj->ID,
				'date'        => $comment->comment_date,
				'title'       => '',
				'content'     => $comment->comment_content,
				'type_slug'   => 'default',
				'rating_data' => maybe_serialize( [
					'field_label' => 'Rating',
					'field_value' => $review_rating,
					'field_step'  => 1,
					'field_max'   => 5,
				] ),
				'rating'      => round( ( 100 * $review_rating ) / 5, 2 ),
				'approved'    => $comment->comment_approved,
				'pinned'      => 0,
			];

			$insert_data = Reviews_Data::get_instance()->add_new_review( $prepared_data );

			if ( ! $insert_data ) {
				continue;
			}

			$products_review_ids[ $comment->comment_ID ] = $insert_data['insert_id'];
			update_comment_meta( $comment->comment_ID, '_jet_reviews_woo_pulled', 'true' );
		}

		// Pull Comments
		foreach ( $this->product_comments as $comment ) {
			//delete_comment_meta( $comment->comment_ID, '_jet_reviews_woo_pulled' );
			$is_pulled = get_comment_meta( $comment->comment_ID, '_jet_reviews_woo_pulled', true );

			if ( filter_var( $is_pulled, FILTER_VALIDATE_BOOLEAN ) ) {
				continue;
			}

			$review_id = 0;
			$parent_id = 0;
			$comment_parent = $comment->comment_parent;

			$top_comment = $this->get_top_comment( $comment->comment_ID );

			if ( array_key_exists( $top_comment, $products_review_ids ) ) {
				$review_id = $products_review_ids[ $top_comment ];
			}

			if ( array_key_exists( $comment_parent, $products_comment_ids ) ) {
				$parent_id = $products_comment_ids[ $comment_parent ];
			}

			$prepared_data = [
				'source'    => 'post',
				'post_id'   => $comment->comment_post_ID,
				'parent_id' => $parent_id,
				'review_id' => $review_id,
				'author'    => $user_obj->ID,
				'content'   => $comment->comment_content,
				'date'      => $comment->comment_date,
				'approved'  => $comment->comment_approved,
			];

			$insert_data = Comments_Data::get_instance()->submit_review_comment( $prepared_data );

			if ( ! $insert_data ) {
				continue;
			}

			$products_comment_ids[ $comment->comment_ID ] = $insert_data;
			update_comment_meta( $comment->comment_ID, '_jet_reviews_woo_pulled', 'true' );
		}

		$products_review_ids_count = count( $products_review_ids );
		$products_review_comment_ids_count = count( $products_comment_ids );

		return rest_ensure_response( [
			'success' => true,
			'message' => sprintf( '%s review(s) and %s comment(s) imported', $products_review_ids_count, $products_review_comment_ids_count ),
			'data'    => [],
		] );
	}

	/**
	 * @param $all_comments
	 * @param $comment_id
	 *
	 * @return void
	 */
	public function get_top_comment( $comment_id = false ) {

		foreach ( $this->products_wp_comments as $comment ) {
			$parent_id = $comment->comment_parent;

			if ( '0' !== $parent_id ) {
				$this->get_top_comment( $parent_id );
			} else {
				return $comment->comment_ID;
			}
		}

		return false;
	}

}
