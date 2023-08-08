<?php
namespace Jet_Reviews\Compatibility\Jet_Engine\Listings;

use Jet_Reviews\Compatibility\Jet_Engine as Module;

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

class Query {

	/**
	 * @var
	 */
	public $source;

	/**
	 * @var array
	 */
	public $items = array();

	/**
	 * Constructor for the class
	 */
	public function __construct( $source ) {

		$this->source = $source;

		add_filter(
			'jet-engine/listing/grid/query/' . $this->source,
			array( $this, 'query_items' ), 10, 3
		);

		add_filter(
			'jet-engine/listings/data/object-vars',
			array( $this, 'prepare_object_vars' ), 10
		);

		add_action( 'jet-engine/listings/frontend/reset-data', function( $data ) {

			if ( $this->source === $data->get_listing_source() ) {
				wp_reset_postdata();
			}
		} );

	}

	/**
	 * Prepare item variables
	 */
	public function prepare_object_vars( $vars ) {

		if ( empty( $vars['jet_reviews'] ) ) {
			return $vars;
		}

		$new_vars = array();

		foreach ( $vars as $key => $value ) {
			$new_vars[ $this->source . '::' . $key ] = $value;
		}

		if ( isset( $new_vars[ $this->source . '::author' ] ) ) {
			$raw_user_data = jet_reviews()->user_manager->get_raw_user_data( $new_vars[ $this->source . '::author' ] );

			$new_vars[ $this->source . '::author_name' ] = $raw_user_data['name'];
		}

		$vars = array_merge( $vars, $new_vars );

		return $vars;

	}

	/**
	 * Query items list
	 *
	 * @param  [type] $query    [description]
	 * @param  [type] $settings [description]
	 * @param  [type] $widget   [description]
	 * @return [type]           [description]
	 */
	public function query_items( $query, $settings, $widget ) {

		$widget->query_vars['page']    = 1;
		$widget->query_vars['pages']   = 1;
		$widget->query_vars['request'] = false;

		$table_name = jet_reviews()->db->tables( 'reviews', 'name' );
		$offset     = 0;
		$per_page   = $widget->get_posts_num( $settings );

		$result = jet_reviews()->db->wpdb()->get_results(
			"SELECT * FROM $table_name WHERE approved=1 ORDER BY date DESC LIMIT $offset, $per_page",
			OBJECT
		);

		if ( empty( $result ) ) {
			$result = array();
		}

		return array_map( function( $item ) {
			$item->jet_reviews = true;
			return $item;
		}, $result );

	}

}
