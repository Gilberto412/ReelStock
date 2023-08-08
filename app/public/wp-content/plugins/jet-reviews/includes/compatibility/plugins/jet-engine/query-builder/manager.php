<?php
namespace Jet_Reviews\Compatibility\Jet_Engine\Query_Builder;

use Jet_Reviews\Compatibility\Jet_Engine as Module;

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

class Manager {

	/**
	 * Instance.
	 *
	 * Holds query builder instance.
	 *
	 * @access public
	 * @static
	 *
	 * @var Plugin
	 */
	public static $instance = null;

	/**
	 * @var string
	 */
	public $slug = 'jet-reviews';

	/**
	 * Instance.
	 *
	 * Ensures only one instance of the plugin class is loaded or can be loaded.
	 *
	 * @access public
	 * @static
	 *
	 * @return Plugin An instance of the class.
	 */
	public static function instance() {

		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	/**
	 * Class constructor
	 */
	public function __construct() {
		add_action( 'jet-engine/query-builder/query-editor/register', array( $this, 'register_editor_component' ) );

		add_action( 'jet-engine/query-builder/queries/register', array( $this, 'register_query' ) );

		/*add_filter( 'jet-engine/listings/macros-list', function( $macros_list ) {

			$macros_list['current_review_source_id'] = array(
				'label' => esc_html__( 'Current Review Source ID', 'jet-reviews' ),
				'cb'    => array( $this, 'get_current_review_source_id' ),
				'args'  => array(
					'source' => array(
						'label'  => __( 'Source', 'jet-reviews' ),
						'type'   => 'select',
						'options' => jet_reviews()->reviews_manager->sources->get_registered_source_list(),
					),
				),
			);

			return $macros_list;
		} );*/
	}

	/**
	 * Register editor componenet for the query builder
	 *
	 * @param  [type] $manager [description]
	 * @return [type]          [description]
	 */
	public function register_editor_component( $manager ) {
		require_once Module::instance()->get_file_path( 'query-builder/editor.php' );
		$manager->register_type( new Query_Editor() );
	}

	/**
	 * Regsiter query class
	 *
	 * @param  [type] $manager [description]
	 * @return [type]          [description]
	 */
	public function register_query( $manager ) {

		require_once Module::instance()->get_file_path( 'query-builder/query.php' );
		$type  = $this->slug;
		$class = __NAMESPACE__ . '\Query';

		$manager::register_query( $type, $class );

	}

	/**
	 * @param null $field_value
	 * @param string $source
	 *
	 * @return array
	 */
	public function get_current_review_source_id( $field_value = null, $source = 'post' ) {

		$source_instance = jet_reviews()->reviews_manager->sources->get_source_instance( $source );

		if ( ! $source_instance ) {
			return [];
		}

		$source      = $source_instance->get_slug();
		$source_id   = $source_instance->get_current_id();

		return $source_id;

	}

}
