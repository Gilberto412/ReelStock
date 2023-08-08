<?php
namespace Jet_Reviews\Reviews;

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

class Data {

	/**
	 * A reference to an instance of this class.
	 *
	 * @since 1.0.0
	 * @var   object
	 */
	private static $instance = null;

	/**
	 * [$reviews_meta description]
	 * @var boolean
	 */
	private $reviews_cache = array();

	/**
	 * Constructor for the class
	 */
	function __construct() {}

	/**
	 * [insert_review description]
	 * @param  array  $args [description]
	 * @return [type]       [description]
	 */
	public function get_admin_reviews_list_by_page( $id = false, $page = 0, $per_page = 20, $search_title = '', $post_type = '' ) {

		$table_name = jet_reviews()->db->tables( 'reviews', 'name' );
		$offset = $page * $per_page;

		if ( empty( $id ) ) {
			$count_query = "SELECT COUNT(*) FROM $table_name ORDER BY id DESC";
			$page_query = "SELECT * FROM $table_name ORDER BY id DESC LIMIT $offset, $per_page";
		} else {
			$count_query = "SELECT COUNT(*) FROM $table_name WHERE id=$id ORDER BY id DESC";
			$page_query = "SELECT * FROM $table_name WHERE id=$id ORDER BY id DESC LIMIT $offset, $per_page";
		}

		if ( ! empty( $search_title ) ) {
			$title_like = jet_reviews()->db->wpdb()->esc_like( $search_title );

			$count_query = jet_reviews()->db->wpdb()->prepare(
				"SELECT COUNT(*) FROM $table_name WHERE content LIKE '%s'",
				'%' . $title_like .'%'
			);

			$page_query = jet_reviews()->db->wpdb()->prepare(
				"SELECT * FROM $table_name WHERE content LIKE '%s' ORDER BY id DESC LIMIT $offset, $per_page",
				'%' . $title_like .'%'
			);
		}

		/*if ( ! empty( $post_type ) ) {
			$query = jet_reviews()->db->wpdb()->prepare(
				"SELECT * FROM $table_name WHERE post_type = '%s' ORDER BY id DESC LIMIT $offset, $per_page",
				esc_sql( $post_type )
			);
		}*/

		$raw_result = jet_reviews()->db->wpdb()->get_results( $page_query, ARRAY_A );

		$result_count = 0;

		$prepare_data = array();

		if ( ! empty( $raw_result ) ) {

			$all_comments = jet_reviews()->comments_manager->data->get_comments_count_by_reviews();

			foreach ( $raw_result as $key => $review_data ) {

				$user_data = jet_reviews()->user_manager->get_raw_user_data( $review_data['author'] );

				$comments_count = isset( $all_comments[ $review_data['id'] ] ) ? $all_comments[ $review_data['id'] ]->comments : 0;

				$prepare_data[] = array(
					'id'          => $review_data['id'],
					'source'      => $review_data['source'],
					'source_type' => $review_data['post_type'],
					'post'   => array(
						'id'    => $review_data['post_id'],
						'title' => wp_trim_words( get_the_title( $review_data['post_id'] ), 6, ' ...' ),
						'link'  => get_permalink( $review_data['post_id'] ),
					),
					'author'    => array(
						'id'     => $user_data['id'],
						'name'   => $user_data['name'],
						'mail'   => $user_data['mail'],
						'avatar' => $user_data['avatar'],
						'roles'  => $user_data['roles'],
						'url'    => add_query_arg( array( 'user_id' => $user_data['id'] ), esc_url( admin_url( 'user-edit.php' ) ) ),
					),
					'date'           => $review_data['date'],
					'title'          => $review_data['title'],
					'content'        => $review_data['content'],
					'type_slug'      => $review_data['type_slug'],
					'rating_data'    => maybe_unserialize( $review_data['rating_data'] ),
					'rating'         => $review_data['rating'],
					'comments_count' => $comments_count,
					'approved'       => filter_var( $review_data['approved'], FILTER_VALIDATE_BOOLEAN ),
					'check'          => false,
				);
			}

			$result_count = jet_reviews()->db->wpdb()->get_var( $count_query );

		}

		return array(
			'page_list'   => $prepare_data,
			'total_count' => $result_count,
		);

	}

	/**
	 * [get_public_reviews_list_by_page description]
	 * @param  integer $page     [description]
	 * @param  integer $per_page [description]
	 * @return [type]            [description]
	 */
	public function get_public_reviews_list( $source = 'post', $source_id = false, $page = 0, $per_page = 10 ) {

		if ( ! $source_id ) {
			return false;
		}

		$table_name = jet_reviews()->db->tables( 'reviews', 'name' );

		$offset = $page * $per_page;

		$review_count_query = jet_reviews()->db->wpdb()->prepare(
			"SELECT COUNT(*) FROM $table_name WHERE source = %s AND post_id = %d AND approved=1",
			$source,
			$source_id
		);

		$average_rating_query = jet_reviews()->db->wpdb()->prepare(
			"SELECT AVG(rating) FROM $table_name WHERE source = %s AND post_id = %d AND approved=1",
			$source,
			$source_id
		);

		$review_query = "SELECT * FROM $table_name WHERE source = %s AND post_id = %d AND approved=1 ORDER BY date DESC";

		if ( $per_page ) {
			$review_query = $review_query . " LIMIT $offset, $per_page";
		}

		$review_page_query = jet_reviews()->db->wpdb()->prepare(
			$review_query,
			$source,
			$source_id
		);

		$raw_page_result = jet_reviews()->db->wpdb()->get_results( $review_page_query, ARRAY_A );

		$all_comments = \Jet_Reviews\Comments\Data::get_instance()->get_review_comments_by_post_id( $source, $source_id );

		$review_list = array();

		if ( ! empty( $raw_page_result ) ) {

			$source_instance = jet_reviews()->reviews_manager->sources->get_source_instance( $source );

			$source_type = $source_instance->get_type( [
				'source_id' => $source_id,
			] );

			$source_settings = jet_reviews()->settings->get_source_settings_data( $source_instance->get_slug(), $source_type );

			$verifications = $source_settings['verifications'];

			foreach ( $raw_page_result as $key => $review_data ) {

				$user_data = jet_reviews()->user_manager->get_raw_user_data( $review_data['author'] );

				$review_comments = $this->find_comments_by_review_id( $review_data['id'], $all_comments );

				$review_verification_data = jet_reviews()->user_manager->get_verification_data(
					$verifications,
					array(
						'user_id' => $user_data['id'],
						'post_id' => $source_id,
					)
				);

				$review_list[] = array(
					'id'          => $review_data['id'],
					'source'      => $source,
					'source_type' => $source_type,
					'author'    => array(
						'id'     => $user_data['id'],
						'name'   => $user_data['name'],
						'mail'   => $user_data['mail'],
						'avatar' => $user_data['avatar'],
					),
					'date'        => array(
						'raw'        => $review_data['date'],
						'human_diff' => jet_reviews_tools()->human_time_diff_by_date( $review_data['date'] ),
					),
					'title'         => $review_data['title'],
					'content'       => $review_data['content'],
					'type_slug'     => $review_data['type_slug'],
					'rating_data'   => maybe_unserialize( $review_data['rating_data'] ),
					'rating'        => $review_data['rating'],
					'comments'      => $review_comments,
					'approved'      => filter_var( $review_data['approved'], FILTER_VALIDATE_BOOLEAN ),
					'like'          => $review_data['likes'],
					'dislike'       => $review_data['dislikes'],
					'approval'      => jet_reviews()->user_manager->get_review_approval_data( $review_data['id'] ),
					'pinned'        => filter_var( $review_data['pinned'], FILTER_VALIDATE_BOOLEAN ),
					'verifications' => $review_verification_data,
				);
			}
		}

		return array(
			'list'   => $review_list,
			'total'  => jet_reviews()->db->wpdb()->get_var( $review_count_query ),
			'rating' => (float) jet_reviews()->db->wpdb()->get_var( $average_rating_query ),
		);

	}

	/**
	 * [find_comments_by_review_id description]
	 * @param  boolean $review_id    [description]
	 * @param  array   $all_comments [description]
	 * @return [type]                [description]
	 */
	public function find_comments_by_review_id( $review_id = false, $all_comments = array() ) {

		if ( empty( $all_comments ) || ! $review_id ) {
			return [];
		}

		$comments = array();

		foreach ( $all_comments as $key => $comment_data ) {

			if ( $review_id === $comment_data['review_id'] ) {
				$comments[] = $comment_data;
			}
		}

		if ( ! empty( $comments ) ) {
			$comments = $this->buildReviewCommentsTree( $comments, '0' );
		}

		return $comments;
	}

	/**
	 * [buildReviewCommentsTree description]
	 * @param  array  &$items   [description]
	 * @param  string $parentId [description]
	 * @return [type]           [description]
	 */
	public function buildReviewCommentsTree( array &$items, $parentId = '0' ) {

		$branch = [];

		foreach ( $items as &$item ) {

			if ( $item['parent_id'] === $parentId ) {
				$children = $this->buildReviewCommentsTree( $items, $item['id'] );

				if ( $children ) {
					$item['children'] = $children;
				} else {
					$item['children'] = array();
				}

				$branch[] = $item;

				unset( $item );
			}
		}

		return $branch;

	}

	/**
	 * [insert_review description]
	 * @param  array  $args [description]
	 * @return [type]       [description]
	 */
	public function get_reviews_by_post_id( $post_id = 0 ) {

		$table_name = jet_reviews()->db->tables( 'reviews', 'name' );

		$query = jet_reviews()->db->wpdb()->prepare(
			"SELECT * FROM $table_name WHERE post_id = %d AND approved=1 ORDER BY date DESC",
			$post_id
		);

		$raw_result = jet_reviews()->db->wpdb()->get_results( $query, ARRAY_A );

		$prepare_data = array();

		if ( ! empty( $raw_result ) ) {
			foreach ( $raw_result as $key => $review_data ) {
				$prepare_data[ $review_data['id'] ] = $review_data;
			}
		}

		return $prepare_data;
	}

	/**
	 * [delete_review_by_id description]
	 * @param  integer $id [description]
	 * @return [type]      [description]
	 */
	public function add_new_review( $data = array() ) {

		if ( empty( $data ) ) {
			return false;
		}

		$table_name = jet_reviews()->db->tables( 'reviews', 'name' );

		$prepare_data = array(
			'source'       => $data['source'],
			'post_id'      => $data['post_id'],
			'post_type'    => $data['post_type'],
			'author'       => $data['author'],
			'date'         => $data['date'],
			'title'        => $data['title'],
			'content'      => $data['content'],
			'type_slug'    => $data['type_slug'],
			'rating_data'  => $data['rating_data'],
			'rating'       => $data['rating'],
			'approved'     => $data['approved'],
		);

		$query = jet_reviews()->db->wpdb()->insert( $table_name, $prepare_data );

		if ( ! $query ) {
			return false;
		}

		$insert_id = jet_reviews()->db->wpdb()->insert_id;

		$prepare_data = wp_parse_args( $prepare_data, [ 'id' => $insert_id ] );

		// maybe notify moderator
		jet_reviews_tools()->submit_review_notify_moderator( $prepare_data );

		$post_id = $prepare_data['post_id'];

		$average_rating_query = jet_reviews()->db->wpdb()->prepare(
			"SELECT AVG(rating) FROM $table_name WHERE post_id = %d AND approved=1",
			$post_id
		);

		$rating = (float) jet_reviews()->db->wpdb()->get_var( $average_rating_query );

		return array(
			'insert_id' => jet_reviews()->db->wpdb()->insert_id,
			'rating'    => $rating,
		);
	}

	/**
	 * [update_review_type description]
	 * @param  array  $data [description]
	 * @return [type]       [description]
	 */
	public function update_review( $data = array() ) {

		if ( empty( $data ) ) {
			return false;
		}

		$table_name = jet_reviews()->db->tables( 'reviews', 'name' );

		$prepared_data = array(
			'post_id'     => $data['post']['id'],
			'post_type'   => $data['post_type'],
			'author'      => $data['author']['id'],
			'date'        => $data['date'],
			'title'       => $data['title'],
			'content'     => $data['content'],
			'type_slug'   => $data['type_slug'],
			'rating_data' => maybe_serialize( $data['rating_data'] ),
			'approved'    => filter_var( $data['approved'], FILTER_VALIDATE_BOOLEAN ) ? 1 : 0,
		);

		$query = jet_reviews()->db->wpdb()->update(
			$table_name,
			$prepared_data,
			array(
				'id' => $data['id'],
			)
		);

		return $query;
	}

	/**
	 * [update_review_approval description]
	 * @return [type] [description]
	 */
	public function update_review_approval( $review_id = false, $type = 'like', $inc = true, $current_state = false ) {

		if ( ! $current_state ) {
			$current_state = array(
				'like'    => false,
				'dislike' => false,
			);
		}

		$table_name = jet_reviews()->db->tables( 'reviews', 'name' );

		$likes = (int)jet_reviews()->db->wpdb()->get_var(
			jet_reviews()->db->wpdb()->prepare(
				"SELECT likes FROM $table_name WHERE id=%d",
				$review_id
			)
		);

		$dislikes = (int)jet_reviews()->db->wpdb()->get_var(
			jet_reviews()->db->wpdb()->prepare(
				"SELECT dislikes FROM $table_name WHERE id=%d",
				$review_id
			)
		);

		$alt_type = 'like' !== $type ? 'like' : 'dislike';

		if ( ! $current_state[ $type ] && ! $current_state[ $alt_type ] ) {
			$current_state[ $type ] = ! $current_state[ $type ];

			if ( 'like' === $type ) {
				$likes++;
			}

			if ( 'dislike' === $type ) {
				$dislikes++;
			}

		} else {
			if ( $inc ) {
				$current_state[ $type ] = ! $current_state[ $type ];
				$current_state[ $alt_type ] = ! $current_state[ $alt_type ];

				if ( $current_state['like'] ) {
					$likes++;
				} else {
					$likes--;
				}

				if ( $current_state['dislike'] ) {
					$dislikes++;
				} else {
					$dislikes--;
				}

			} else {
				$current_state[ $type ] = ! $current_state[ $type ];

				if ( 'like' === $type ) {
					$likes--;
				}

				if ( 'dislike' === $type ) {
					$dislikes--;
				}
			}

		}

		$likes = 0 < $likes ? $likes : 0;
		$dislikes = 0 < $dislikes ? $dislikes : 0;

		$query = jet_reviews()->db->wpdb()->update(
			$table_name,
			array(
				'likes'    => $likes,
				'dislikes' => $dislikes,
			),
			array(
				'id' => $review_id,
			)
		);

		jet_reviews()->user_manager->update_user_approval_review( $review_id, $current_state );

		return array(
			'like'     => $likes,
			'dislike'  => $dislikes,
			'approval' => $current_state,
		);
	}

	/**
	 * [get_reviews_cache description]
	 * @return [type] [description]
	 */
	public function get_reviews_cache() {
		return $this->reviews_cache;
	}

	/**
	 * [get_review_meta description]
	 * @param  [type]  $review_id [description]
	 * @param  boolean $key       [description]
	 * @return [type]             [description]
	 */
	public function get_review_meta( $review_id = false, $key = false, $default = false ) {

		if ( ! $review_id || ! $key ) {
			return false;
		}

		$reviews_cache = $this->get_reviews_cache();

		if ( ! isset( $reviews_cache[ $review_id ] ) ) {
			$reviews_cache[ $review_id ] = $this->get_review_meta_query( $review_id );
		}

		$all_review_meta = $reviews_cache[ $review_id ];

		if ( ! $key ) {
			return $all_review_meta;
		}

		if ( ! isset( $all_review_meta[ $key ] ) ) {
			return $default;
		}

		$key_meta = $all_review_meta[ $key ];

		return $key_meta;
	}

	/**
	 * [get_reviews_meta description]
	 * @return [type] [description]
	 */
	public function get_review_meta_query( $review_id = false ) {
		$table_name = jet_reviews()->db->tables( 'review_meta', 'name' );

		$query = jet_reviews()->db->wpdb()->prepare(
			"SELECT * FROM $table_name WHERE review_id = %d",
			$review_id
		);

		$raw_result = jet_reviews()->db->wpdb()->get_results( $query, ARRAY_A );

		$prepared_data = array();

		foreach ( $raw_result as $key => $field ) {
			$prepared_data[ $field['meta_key'] ] = maybe_unserialize( $field['meta_value'] );
		}

		return $prepared_data;
	}

	/**
	 * [update_review_meta description]
	 * @param  boolean $review_id [description]
	 * @param  boolean $key       [description]
	 * @param  string  $value     [description]
	 * @return [type]             [description]
	 */
	public function update_review_meta( $review_id = false, $key = false, $value = '' ) {

		if ( ! $review_id || ! $key ) {
			return false;
		}

		$reviews_cache = $this->get_reviews_cache();

		if ( isset( $reviews_cache[ $review_id ] ) && $reviews_cache[ $review_id ][ $key ] ) {
			$this->reviews_cache[ $review_id ][ $key ] = $value;
		}

		$table_name = jet_reviews()->db->tables( 'review_meta', 'name' );

		$review_field_exist = jet_reviews()->db->wpdb()->get_var(
			jet_reviews()->db->wpdb()->prepare(
				"SELECT id FROM $table_name WHERE review_id = %d AND meta_key = %s LIMIT 1",
				$review_id,
				$key
			)
		);

		if ( $review_field_exist ) {
			$query = jet_reviews()->db->wpdb()->update(
				$table_name,
				array(
					'meta_value' => maybe_serialize( $value ),
				),
				array(
					'review_id' => $review_id,
					'meta_key'  => $key
				)
			);
		} else {
			$query = jet_reviews()->db->wpdb()->insert( $table_name, array(
				'review_id'  => $review_id,
				'meta_key'   => $key,
				'meta_value' => maybe_serialize( $value ),
			) );
		}

		if ( $query ) {
			return true;
		}

		return false;
	}

	/**
	 * [insert_review description]
	 * @param  array  $args [description]
	 * @return [type]       [description]
	 */
	public function get_review_types_list() {

		$table_name = jet_reviews()->db->tables( 'review_types', 'name' );

		$query = "SELECT * FROM $table_name ORDER BY id DESC";

		$raw_result = jet_reviews()->db->wpdb()->get_results( $query, ARRAY_A );

		$prepare_data = array();

		if ( ! empty( $raw_result ) ) {

			foreach ( $raw_result as $key => $review_data ) {

				$review_data['fields'] = maybe_unserialize( $review_data['fields'] );

				$prepare_data[ $review_data['id'] ] = $review_data;
			}

		}

		return $prepare_data;
	}

	/**
	 * [delete_review_by_id description]
	 * @param  integer $id [description]
	 * @return [type]      [description]
	 */
	public function delete_review_by_id( $id = 0 ) {

		$reviews_table = jet_reviews()->db->tables( 'reviews', 'name' );
		$comments_table = jet_reviews()->db->tables( 'review_comments', 'name' );

		$deleted_reviews = jet_reviews()->db->wpdb()->delete( $reviews_table, array( 'id' => $id ) );
		$deleted_comments = jet_reviews()->db->wpdb()->delete( $comments_table, array( 'review_id' => $id ) );

		return $deleted_reviews;
	}

	/**
	 * [delete_review_type_by_id description]
	 * @param  integer $id [description]
	 * @return [type]      [description]
	 */
	public function delete_review_type_by_id( $id = 0 ) {
		$table_name = jet_reviews()->db->tables( 'review_types', 'name' );

		return jet_reviews()->db->wpdb()->delete( $table_name, array( 'id' => $id ) );
	}

	/**
	 * [is_review_type_exist description]
	 * @param  string  $slug [description]
	 * @return boolean       [description]
	 */
	public function is_review_type_exist( $slug = 'default' ) {

		$table_name = jet_reviews()->db->tables( 'review_types', 'name' );

		$count_query = jet_reviews()->db->wpdb()->prepare(
			"SELECT COUNT(*) FROM $table_name WHERE slug = %s",
			$slug
		);

		$result_count = intval( jet_reviews()->db->wpdb()->get_var( $count_query ) );

		if ( 0 === $result_count ) {
			return false;
		}

		return true;
	}

	/**
	 * [add_new_review_type description]
	 * @param array $data [description]
	 */
	public function add_new_review_type( $data = array() ) {

		if ( empty( $data ) ) {
			return false;
		}

		$table_name = jet_reviews()->db->tables( 'review_types', 'name' );

		$query = jet_reviews()->db->wpdb()->insert( $table_name, $data );

		if ( ! $query ) {
			return false;
		}

		return jet_reviews()->db->wpdb()->insert_id;
	}

	/**
	 * [update_review_type description]
	 * @param  array  $data [description]
	 * @return [type]       [description]
	 */
	public function update_review_type( $data = array() ) {

		if ( empty( $data ) ) {
			return false;
		}

		$table_name = jet_reviews()->db->tables( 'review_types', 'name' );

		return jet_reviews()->db->wpdb()->update(
			$table_name,
			$data,
			array(
				'slug' => $data['slug'],
			)
		);
	}

	/**
	 * [get_review_type description]
	 * @param  boolean $slug [description]
	 * @return [type]        [description]
	 */
	public function get_review_type( $slug = false ) {

		if ( ! $slug ) {
			return false;
		}

		$table_name = jet_reviews()->db->tables( 'review_types', 'name' );

		$query = jet_reviews()->db->wpdb()->prepare(
			"SELECT * FROM $table_name WHERE slug = %s ORDER BY id DESC",
			$slug
		);

		$raw_result = jet_reviews()->db->wpdb()->get_results( $query, ARRAY_A );

		return $raw_result;
	}

	/**
	 * [get_review_count description]
	 * @return [type] [description]
	 */
	public function get_review_count( $post_type = false, $rating = false ) {
		$table_name = jet_reviews()->db->tables( 'reviews', 'name' );

		$current_year = date( 'Y' );

		$rating_condition = '';

		switch ( $rating ) {
			case 'low':
				$rating_condition = 'rating BETWEEN 0 AND 30';
			break;
			case 'medium':
				$rating_condition = 'rating BETWEEN 31 AND 70';
			break;
			case 'high':
				$rating_condition = 'rating BETWEEN 71 AND 100';
			break;
			default:
				$rating_condition = 'rating BETWEEN 0 AND 100';
			break;
		}

		if ( $post_type ) {
			$count_query = jet_reviews()->db->wpdb()->prepare(
				//"SELECT COUNT(*) FROM $table_name WHERE YEAR(date) = $current_year AND post_type = %s $rating_condition",
				"SELECT COUNT(*) FROM $table_name WHERE $rating_condition AND post_type = %s",
				$post_type
			);
		} else {
			//$count_query = "SELECT COUNT(*) FROM $table_name WHERE YEAR(date) = $current_year $rating_condition";
			$count_query = "SELECT COUNT(*) FROM $table_name WHERE $rating_condition";
		}

		$result_count = jet_reviews()->db->wpdb()->get_var( $count_query );

		return $result_count;
	}

	/**
	 * [get_approved_review_count description]
	 * @return [type] [description]
	 */
	public function get_approved_review_count( $post_type = false ) {
		$table_name = jet_reviews()->db->tables( 'reviews', 'name' );

		if ( $post_type ) {
			$count_query = jet_reviews()->db->wpdb()->prepare(
				"SELECT COUNT(*) FROM $table_name WHERE post_type = %s AND approved = 1",
				$post_type
			);
		} else {
			$count_query = "SELECT COUNT(*) FROM $table_name WHERE approved = 1";
		}

		$result_count = jet_reviews()->db->wpdb()->get_var( $count_query );

		return $result_count;
	}

	/**
	 * [get_review_count_by_month description]
	 * @param  boolean $post_type [description]
	 * @return [type]             [description]
	 */
	public function get_review_dataset_by_post( $post_type = false, $rating = false, $approved = false ) {

		$table_name = jet_reviews()->db->tables( 'reviews', 'name' );

		$current_year = date('Y');

		$rating_condition = '';

		if ( $rating ) {
			switch ( $rating ) {
				case 'low':
					$rating_condition = 'AND rating BETWEEN 0 AND 30';
				break;
				case 'medium':
					$rating_condition = 'AND rating BETWEEN 31 AND 70';
				break;
				case 'high':
					$rating_condition = 'AND rating BETWEEN 71 AND 100';
				break;
			}
		}

		$approved_condition = '';

		if ( $approved ) {
			$approved_condition = 'AND approved = 1';
		}

		if ( $post_type ) {
			$count_query = jet_reviews()->db->wpdb()->prepare(
				"SELECT MONTH(date) AS month, COUNT(*) AS count FROM $table_name WHERE post_type = %s AND YEAR(date) = $current_year $rating_condition $approved_condition GROUP BY MONTH(date)",
				$post_type
			);
		} else {
			$count_query = "SELECT MONTH(date) AS month, COUNT(*) AS count FROM $table_name WHERE YEAR(date) = $current_year $rating_condition $approved_condition GROUP BY MONTH(date)";
		}

		$result_count = jet_reviews()->db->wpdb()->get_results( $count_query, ARRAY_A );

		$prepared_data = array();

		foreach ( range( 1, 12 ) as $month ) {
			$count = 0;

			foreach ( $result_count as $key => $month_data ) {

				if ( $month === intval( $month_data['month'] ) ) {
					$count = intval( $month_data['count'] );

					break;
				}
			}

			$prepared_data[] = $count;
		}

		return $prepared_data;
	}

	/**
	 * [sync_rating_post_meta description]
	 * @param  boolean $post_type [description]
	 * @return [type]             [description]
	 */
	public function sync_rating_post_meta( $post_type = false ) {

		if ( empty( $post_type ) ) {
			return [
				'success' => false,
				'data'    => [],
			];
		}

		$post_type_data = jet_reviews()->settings->get_post_type_data( $post_type );

		$posts = get_posts( array(
			'numberposts' => -1,
			'post_type'   => $post_type,
		) );

		foreach( $posts as $post ) {
			$post_id = $post->ID;

			$table_name = jet_reviews()->db->tables( 'reviews', 'name' );

			$query = jet_reviews()->db->wpdb()->prepare(
				"SELECT AVG(rating) FROM $table_name WHERE post_id = %d AND approved=1",
				$post_id
			);

			$average_rating_percent = (float) jet_reviews()->db->wpdb()->get_var( $query );

			/**
			 * Maybe update average rating post meta field
			 */
			if ( filter_var( $post_type_data['metadata'], FILTER_VALIDATE_BOOLEAN ) && ! empty( $average_rating_percent ) ) {
				 update_post_meta( $post_id, $post_type_data['metadata_rating_key'], ( $average_rating_percent / 100 ) * $post_type_data['metadata_ratio_bound'] );
			}
		}

		return [
			'success' => true,
			'data'    => [
				'post_count' => count( $posts ),
			]
		];
	}

	/**
	 * Returns the instance.
	 *
	 * @since  1.0.0
	 * @return object
	 */
	public static function get_instance() {

		// If the single instance hasn't been set, set it now.
		if ( null == self::$instance ) {
			self::$instance = new self;
		}
		return self::$instance;
	}
}
