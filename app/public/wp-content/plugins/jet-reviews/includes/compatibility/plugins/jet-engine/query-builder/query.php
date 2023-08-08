<?php
namespace Jet_Reviews\Compatibility\Jet_Engine\Query_Builder;

use Jet_Reviews\Compatibility\Jet_Engine as Module;

class Query extends \Jet_Engine\Query_Builder\Queries\Base_Query {

	/**
	 * @var null
	 */
	public $current_query = null;

	/**
	 * Returns queries items
	 *
	 * @return [type] [description]
	 */
	public function _get_items() {

		$result = [];

		$source      = ! empty( $this->final_query[ 'source' ] ) ? $this->final_query[ 'source' ] : false;
		$source_type = ! empty( $this->final_query[ 'source_type' ] ) ? $this->final_query[ 'source_type' ] : false;
		$source_id   = ! empty( $this->final_query[ 'source_id' ] ) ? $this->final_query[ 'source_id' ] : false;
		$order       = ! empty( $this->final_query[ 'order' ] ) ? $this->final_query[ 'order' ] : [];
		$limit       = ! empty( $this->final_query[ 'number' ] ) ? absint( $this->final_query[ 'number' ] ) : 0;
		$offset      = ! empty( $this->final_query[ 'offset' ] ) ? absint( $this->final_query[ 'offset' ] ) : 0;

		$table_name = jet_reviews()->db->tables( 'reviews', 'name' );
		$where_definition = "WHERE approved=1";
		$limit_definition = "";

		if ( intval( $limit ) > 0 ) {
			$limit  = absint( $limit );
			$offset = absint( $offset );
			$limit_definition = " LIMIT $offset, $limit";
		}

		if ( $source ) {
			$where_definition .= jet_reviews()->db->wpdb()->prepare(
				" AND source=%s",
				$source
			);
		}

		if ( $source_type ) {
			$where_definition .= jet_reviews()->db->wpdb()->prepare(
				" AND post_type=%s",
				$source_type
			);
		}

		if ( $source_id ) {
			$where_definition .= jet_reviews()->db->wpdb()->prepare(
				" AND post_id=%d",
				$source_id
			);
		}

		$review_query = "SELECT * FROM $table_name $where_definition ORDER BY date DESC $limit_definition";

		$result = jet_reviews()->db->wpdb()->get_results( $review_query );

		return array_map( function( $item ) {
			$item->jet_reviews = true;
			return $item;
		}, $result );

	}

	/**
	 * @return float|int
	 */
	public function get_current_items_page() {

		$offset = ! empty( $this->final_query['offset'] ) ? absint( $this->final_query['offset'] ) : 0;
		$per_page = $this->get_items_per_page();

		if ( ! $offset || ! $per_page ) {
			return 1;
		} else {
			return ceil( $offset / $per_page ) + 1;
		}

	}

	/**
	 * Returns total found items count
	 *
	 * @return [type] [description]
	 */
	public function get_items_total_count() {

		$cached = $this->get_cached_data( 'count' );

		if ( false !== $cached ) {
			return $cached;
		}

		$result = 0;

		$source      = ! empty( $this->final_query[ 'source' ] ) ? $this->final_query[ 'source' ] : false;
		$source_type = ! empty( $this->final_query[ 'source_type' ] ) ? $this->final_query[ 'source_type' ] : false;
		$source_id   = ! empty( $this->final_query[ 'source_id' ] ) ? $this->final_query[ 'source_id' ] : false;

		$table_name = jet_reviews()->db->tables( 'reviews', 'name' );

		$where_definition = "WHERE approved=1";

		if ( $source ) {
			$where_definition .= jet_reviews()->db->wpdb()->prepare(
				" AND source=%s",
				$source
			);
		}

		if ( $source_type ) {
			$where_definition .= jet_reviews()->db->wpdb()->prepare(
				" AND post_type=%s",
				$source_type
			);
		}

		if ( $source_id ) {
			$where_definition .= jet_reviews()->db->wpdb()->prepare(
				" AND post_id=%d",
				$source_id
			);
		}

		$review_count_query = "SELECT COUNT(*) FROM $table_name $where_definition";

		$result = jet_reviews()->db->wpdb()->get_var( $review_count_query );

		$this->update_query_cache( $result, 'count' );

		return $result;

	}

	/**
	 * Returns count of the items visible per single listing grid loop/page
	 * @return [type] [description]
	 */
	public function get_items_per_page() {

		$this->setup_query();

		$limit = 0;

		if ( ! empty( $this->final_query['number'] ) ) {
			$limit = absint( $this->final_query['number'] );
		}

		return $limit;

	}

	/**
	 * Returns queried items count per page
	 *
	 * @return [type] [description]
	 */
	public function get_items_page_count() {
		return $this->get_items_total_count();
	}

	/**
	 * Returns queried items pages count
	 *
	 * @return [type] [description]
	 */
	public function get_items_pages_count() {

		$per_page = $this->get_items_per_page();
		$total    = $this->get_items_total_count();

		if ( ! $per_page || ! $total ) {
			return 1;
		} else {
			return ceil( $total / $per_page );
		}

	}

	/**
	 * @param string $prop
	 * @param null $value
	 */
	public function set_filtered_prop( $prop = '', $value = null ) {

		switch ( $prop ) {

			case '_page':

				$page = absint( $value );

				if ( 0 < $page ) {
					$offset = ( $page - 1 ) * $this->get_items_per_page();
					$this->final_query['offset'] = $offset;
				}

				break;

			case 'orderby':
			case 'order':
			case 'meta_key':

				$key = $prop;

				if ( 'orderby' === $prop ) {
					$key = 'type';
					$value = ( 'meta_key' === $value ) ? 'CHAR' : 'DECIMAL';
				} elseif ( 'meta_key' === $prop ) {
					$key = 'orderby';
				}

				$this->set_filtered_order( $key, $value );
				break;

			case 'meta_query':

				foreach ( $value as $row ) {

					$prepared_row = array(
						'field'    => ! empty( $row['key'] ) ? $row['key'] : false,
						'operator' => ! empty( $row['compare'] ) ? $row['compare'] : '=',
						'value'    => ! empty( $row['value'] ) ? $row['value'] : '',
						'type'     => ! empty( $row['type'] ) ? $row['type'] : false,
					);

					$this->update_args_row( $prepared_row );

				}
				break;
		}

	}

	/**
	 * Set new order from filters query
	 *
	 * @param [type] $key   [description]
	 * @param [type] $value [description]
	 */
	public function set_filtered_order( $key, $value ) {

		if ( empty( $this->final_query['order'] ) ) {
			$this->final_query['order'] = array();
		}

		if ( ! isset( $this->final_query['order']['custom'] ) ) {
			$this->final_query['order'] = array_merge( array( 'custom' => array() ), $this->final_query['order'] );
		}

		$this->final_query['order']['custom'][ $key ] = $value;

	}

	/**
	 * Update argumnts row in the arguments list of the final query
	 *
	 * @param  [type] $row [description]
	 * @return [type]      [description]
	 */
	public function update_args_row( $row ) {

		if ( empty( $this->final_query['args'] ) ) {
			$this->final_query['args'] = array();
		}

		foreach ( $this->final_query['args'] as $index => $existing_row ) {
			if ( $existing_row['field'] === $row['field'] && $existing_row['operator'] === $row['operator'] ) {
				$this->final_query['args'][ $index ] = $row;
				return;
			}
		}

		$this->final_query['args'][] = $row;
	}

}
