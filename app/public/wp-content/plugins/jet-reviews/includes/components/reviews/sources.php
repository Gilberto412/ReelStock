<?php
namespace Jet_Reviews\Reviews;

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

class Sources {

	/**
	 * A reference to an instance of this class.
	 *
	 * @since 1.0.0
	 * @var   object
	 */
	private static $instance = null;

	/**
	 * [$_endpoints description]
	 * @var null
	 */
	private $registered_sources = array();

	/**
	 * Constructor for the class
	 */
	function __construct() {

		$this->load_files();

		$this->registered_sources = apply_filters( 'jet-reviews/sources/registered_sources', array(
			'post' => array(
				'class'    => '\\Jet_Reviews\\Reviews\\Source\Post',
				'path'     => jet_reviews()->plugin_path( 'includes/components/reviews/sources/post.php' ),
				'instance' => false,
			),
			'user' => array(
				'class'    => '\\Jet_Reviews\\Reviews\\Source\User',
				'path'     => jet_reviews()->plugin_path( 'includes/components/reviews/sources/user.php' ),
				'instance' => false,
			),
		) );

		$this->register_sources();

	}

	/**
	 * [load_files description]
	 * @return [type] [description]
	 */
	public function load_files() {}

	/**
	 * [register_sources description]
	 * @return [type] [description]
	 */
	public function register_sources() {

		// Include base sources file
		require jet_reviews()->plugin_path( 'includes/components/reviews/sources/base.php' );

		$this->registered_sources = array_map( function( $source_data ) {

			if ( ! file_exists( $source_data['path'] ) ) {
				return $source_data;
			}

			require $source_data['path'];

			$class = $source_data['class'];

			if ( ! $source_data['instance'] && class_exists( $class ) ) {
				$source_data['instance'] = new $class();
			}

			return $source_data;
		}, $this->registered_sources );
	}

	/**
	 * [register_source description]
	 * @param  [type] $source_instance [description]
	 * @return [type]                  [description]
	 */
	public function get_registered_sources() {
		return $this->registered_sources;
	}

	/**
	 * [get_register_source description]
	 * @param  [type] $source_slug [description]
	 * @return [type]              [description]
	 */
	public function get_source_data( $source_slug ) {
		return isset( $this->registered_sources[ $source_slug ] ) ? $this->registered_sources[ $source_slug ] : false;
	}

	/**
	 * @param $source_slug
	 *
	 * @return false|mixed
	 */
	public function get_source_instance( $source_slug ) {
		$source_data = $this->get_source_data( $source_slug );

		if ( ! $source_data || ! $source_data['instance'] ) {
			return false;
		}

		return $source_data['instance'];
	}

	/**
	 * @return array
	 */
	public function get_registered_source_list() {

		$sources_options = [];

		foreach ( $this->registered_sources as $source_slug => $source_data ) {
			$source_instance = $source_data['instance'];

			if ( $source_instance ) {
				$sources_options[ $source_instance->get_slug() ] = $source_instance->get_name();
			}
		}

		return $sources_options;
	}

	/**
	 * @return array
	 */
	public function get_registered_source_options() {

		$sources_options = [];

		foreach ( $this->registered_sources as $source_slug => $source_data ) {
			$source_instance = $source_data['instance'];

			if ( $source_instance ) {
				$sources_options[] = [
					'value' => $source_instance->get_slug(),
					'label' => $source_instance->get_name(),
				];
			}
		}

		return $sources_options;
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
