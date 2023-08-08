<?php
namespace Jet_Reviews\Compatibility\Jet_Engine\Listings;

use Jet_Reviews\Compatibility\Jet_Engine as Module;

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

class Manager {

	/**
	 * @var string
	 */
	public $source = 'jet_reviews';

	/**
	 * @var bool
	 */
	public $current_item = false;

	/**
	 * Class constructor
	 */
	public function __construct() {

		require_once Module::instance()->get_file_path( 'listings/query.php' );
		new Query( $this->source );

		//require_once Module::instance()->get_file_path( 'listings/blocks.php' );
		//new Blocks( $this );

		//require_once Module::instance()->get_file_path( 'listings/context.php' );
		//new Context();

		if ( jet_engine()->has_elementor() ) {
			require_once Module::instance()->get_file_path( 'listings/elementor.php' );
			new Elementor( $this );
		}

		add_filter(
			'jet-engine/templates/listing-sources',
			array( $this, 'register_listing_source' )
		);

		add_filter(
			'jet-engine/templates/admin-columns/type/' . $this->source,
			array( $this, 'type_admin_column_cb' ),
			10, 2
		);

		add_filter(
			'jet-engine/listing/data/object-fields-groups',
			array( $this, 'add_source_fields' )
		);

		add_filter(
			'jet-engine/listings/dynamic-image/custom-image',
			array( $this, 'custom_image_renderer' ),
			10, 2
		);

		add_filter(
			'jet-engine/listings/dynamic-image/custom-url',
			array( $this, 'custom_image_url' ),
			10, 2
		);

		add_filter(
			'jet-engine/listings/dynamic-link/custom-url',
			array( $this, 'custom_link_url' ),
			10, 2
		);

		add_filter(
			'jet-engine/listing/custom-post-id',
			array( $this, 'set_item_id' ),
			10, 2
		);

		add_filter( 'jet-engine/listings/macros/current-id', function( $result, $object ) {

			if ( isset( $object->cct_slug ) ) {
				$result = $object->_ID;
			}

			return $result;

		}, 10, 2 );

		add_filter( 'jet-engine/listing/render/default-settings', function( $settings ) {
			//$settings['jet_cct_query'] = '{}';
			return $settings;
		} );

		add_filter( 'jet-reviews/source/source-post/current-id', array( $this, 'set_object_id' ) );

	}

	/**
	 * Set product ID for WooCoomerce products reviews
	 */
	public function set_object_id( $current_id ) {

		$object_id = jet_engine()->listings->data->get_current_object_id();

		if ( $object_id ) {
			
			$current_id = $object_id;

		}

		return $current_id;
	}

	/**
	 * @param $result
	 * @param $listing_settings
	 *
	 * @return string|void
	 */
	public function type_admin_column_cb( $result, $listing_settings ) {

		$listing_source = isset( $listing_settings['listing_source'] ) ? $listing_settings['listing_source'] : 'posts';

		if ( $listing_source !== $this->source ) {
			return $result;
		}

		return __( 'JetReviews', 'jet-reviews' );

	}

	/**
	 * @param $id
	 * @param $object
	 *
	 * @return mixed
	 */
	public function set_item_id( $id, $object ) {

		if ( isset( $object->jet_reviews ) ) {
			$id = $object->id;
		}

		return $id;

	}

	/**
	 * Register content types object fields
	 *
	 * @param [type] $groups [description]
	 */
	public function add_source_fields( $groups ) {

		$glue = '::';

		$groups[] = array(
			'label'   => __( 'JetReviews', 'jet-reviews' ),
			'options' => array(
				$this->source . $glue . 'id' => __( 'Review ID', 'jet-reviews' ),
				$this->source . $glue . 'post_id' => __( 'Reviewed Object ID', 'jet-reviews' ),
				$this->source . $glue . 'post_type' => __( 'Reviewed Object Type', 'jet-reviews' ),
				$this->source . $glue . 'author' => __( 'Author', 'jet-reviews' ),
				$this->source . $glue . 'author_name' => __( 'Author Name', 'jet-reviews' ),
				$this->source . $glue . 'date' => __( 'Date', 'jet-reviews' ),
				$this->source . $glue . 'title' => __( 'Title', 'jet-reviews' ),
				$this->source . $glue . 'content' => __( 'Content', 'jet-reviews' ),
				//$this->source . $glue . 'type_slug' => __( 'Type Slug', 'jet-reviews' ),
				//$this->source . $glue . 'rating_data' => __( 'Rating Data', 'jet-reviews' ),
				$this->source . $glue . 'rating' => __( 'Rating', 'jet-reviews' ),
				$this->source . $glue . 'likes' => __( 'Likes', 'jet-reviews' ),
				$this->source . $glue . 'dislikes' => __( 'Dislikes', 'jet-reviews' ),
				//$this->source . $glue . 'approved' => __( 'Approved', 'jet-reviews' ),
				//$this->source . $glue . 'pinned' => __( 'Pinned', 'jet-reviews' ),
			),
		);

		return $groups;

	}

	/**
	 * Returns custom value from dynamic prop by setting
	 * @param  [type] $setting  [description]
	 * @param  [type] $settings [description]
	 * @return [type]           [description]
	 */
	public function get_custom_value_by_setting( $setting, $settings ) {

		$current_object = jet_engine()->listings->data->get_current_object();

		if ( ! isset( $current_object->jet_reviews ) ) {
			return false;
		}

		$field  = isset( $settings[ $setting ] ) ? $settings[ $setting ] : '';
		$glue   = '::';
		$prefix = $this->source . $glue;

		if ( '_permalink' === $field ) {
			$post_id = ! empty( $current_object->post_id ) ? $current_object->post_id : get_the_ID();

			if ( $post_id ) {
				return get_permalink( $post_id );
			} else {
				return false;
			}

		}

		if ( false === strpos( $field, $prefix ) ) {
			return false;
		}

		$prop = str_replace( $prefix, '', $field );

		$result = false;

		if ( isset( $current_object->$prop ) ) {
			$result = $current_object->$prop;
		} elseif ( isset( $current_object->$field ) ) { // for Single Post
			$result = $current_object->$field;
		}

		return $result;

	}

	/**
	 * Returns custom URL for the dynamic image
	 *
	 * @param  [type] $result   [description]
	 * @param  [type] $settings [description]
	 * @return [type]           [description]
	 */
	public function custom_image_url( $result, $settings ) {

		$url = $this->get_custom_value_by_setting( 'image_link_source', $settings );

		if ( is_numeric( $url ) ) {
			$url = get_permalink( $url );
		}

		if ( ! $url ) {
			return $result;
		} else {
			return $url;
		}

	}

	/**
	 * Returns custom URL for dynamic link widget
	 *
	 * @param  [type] $result   [description]
	 * @param  [type] $settings [description]
	 * @return [type]           [description]
	 */
	public function custom_link_url( $result, $settings ) {

		$url = $this->get_custom_value_by_setting( 'dynamic_link_source', $settings );

		if ( is_numeric( $url ) ) {
			$url = get_permalink( $url );
		}

		if ( ! $url ) {
			return $result;
		} else {
			return $url;
		}
	}

	/**
	 * Custom image renderer for custom content type
	 *
	 * @return [type] [description]
	 */
	public function custom_image_renderer( $result, $settings = array() ) {

		$image = $this->get_custom_value_by_setting( 'dynamic_image_source', $settings );
		$size  = isset( $settings['dynamic_image_size'] ) ? $settings['dynamic_image_size'] : 'full';

		if ( ! $image ) {
			return $result;
		}

		ob_start();

		if ( filter_var( $image, FILTER_VALIDATE_URL ) ) {
			printf( '<img src="%1$s" alt="%2$s">', $image, '' );
		} else {

			$current_object = jet_engine()->listings->data->get_current_object();

			$alt = apply_filters(
				'jet-engine/cct/image-alt/' . $current_object->cct_slug,
				false,
				$current_object
			);

			echo wp_get_attachment_image( $image, $size, false, array( 'alt' => $alt ) );
		}

		return ob_get_clean();

	}

	/**
	 * Register listing source
	 *
	 * @param  [type] $sources [description]
	 * @return [type]          [description]
	 */
	public function register_listing_source( $sources ) {
		$sources[ $this->source ] = __( 'JetReviews', 'jet-reviews' );
		return $sources;
	}

	/**
	 * Set default blocks source
	 *
	 * @param [type] $object [description]
	 * @param [type] $editor [description]
	 */
	public function set_blocks_source( $object, $editor ) {

		$preview = $this->setup_preview( $object );

		if ( ! empty( $preview ) ) {
			return $preview['id'];
		} else {
			return false;
		}

	}

	/**
	 * Setup preview
	 *
	 * @return [type] [description]
	 */
	public function setup_preview( $document = false ) {

		if ( ! $document ) {
			$document = jet_engine()->listings->data->get_listing();
		}

		$source = $document->get_settings( 'listing_source' );

		if ( $this->source !== $source ) {
			return false;
		}

		$table_name = jet_reviews()->db->tables( 'reviews', 'name' );

		$items = jet_reviews()->db->wpdb()->get_results(
			"SELECT * FROM $table_name WHERE approved=1 ORDER BY date DESC LIMIT 1",
			OBJECT
		);

		if ( ! empty( $items ) ) {
			$item = $items[0];
			$item->jet_reviews = true;
			jet_engine()->listings->data->set_current_object( $item );
			return $item;
		} else {
			return false;
		}

	}

}
