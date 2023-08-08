<?php
namespace Jet_Theme_Core\Theme_Builder;
/**
 * Class description
 *
 * @package   package_name
 * @author    Cherry Team
 * @license   GPL-2.0+
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

class Page_Templates_Manager {

	/**
	 * A reference to an instance of this class.
	 *
	 * @since  1.0.0
	 * @access private
	 * @var    Jet_Theme_Core
	 */
	private static $instance = null;

	/**
	 * @var string
	 */
	public $page_template_conditions_option_key = 'jet_page_template_conditions';

	/**
	 * @var array
	 */
	public $page_template_list = [];

	/**
	 * Returns the instance.
	 *
	 * @since  1.0.0
	 * @access public
	 * @return Jet_Theme_Core
	 */
	public static function get_instance() {

		// If the single instance hasn't been set, set it now.
		if ( null == self::$instance ) {
			self::$instance = new self;
		}
		return self::$instance;
	}

	/**
	 * Templates post type slug
	 *
	 * @return string
	 */
	public function get_slug() {
		return jet_theme_core()->theme_builder->post_type;
	}

	/**
	 * @return array
	 */
	public function get_site_page_template_conditions() {
		$page_template_conditions = get_option( $this->page_template_conditions_option_key, [] );

		if ( empty( $page_template_conditions ) ) {
			return [];
		}

		return array_map( function( $page_template_data ) {

			if ( ! isset( $page_template_data['conditions'] ) ) {
				return $page_template_data = [
					'conditions'    => $page_template_data,
					'relation_type' => 'or',
				];
			}

			return $page_template_data;
		}, $page_template_conditions );

	}

	/**
	 * @param false $template_type
	 * @param string $content_type
	 * @param string $template_name
	 *
	 * @return array
	 */
	public function create_page_template( $template_name = '', $template_conditions = [], $template_layout = [], $template_type = 'unassigned', $relation_type = 'or' ) {

		if ( ! current_user_can( 'edit_posts' ) ) {
			return [
				'type'     => 'error',
				'message'  => __( 'You don\'t have permissions to do this', 'jet-theme-core' ),
			];
		}

		$default_layout = [
			'header' => [
				'id'       => false,
				'enabled'  => true,
				'override' => true,
			],
			'body' => [
				'id'       => false,
				'enabled'  => true,
				'override' => true,
			],
			'footer' => [
				'id'       => false,
				'enabled'  => true,
				'override' => true,
			],
		];

		$template_layout = wp_parse_args( $template_layout, $default_layout );

		$meta_input = [
			'_conditions'    => $template_conditions,
			'_relation_type' => $relation_type,
			'_layout'        => $template_layout,
			'_type'          => $template_type,
		];

		$post_title = $template_name;

		$post_data = array(
			'post_status' => 'publish',
			'post_title'  => $post_title,
			'post_type'   => $this->get_slug(),
			'meta_input'  => $meta_input,
		);

		$template_id = wp_insert_post( $post_data, true );

		if ( empty( $template_name ) ) {
			$post_title = $post_title . 'Page Template #' . $template_id;

			wp_update_post( [
				'ID'         => $template_id,
				'post_title' => $post_title,
			] );
		}

		if ( $template_id ) {

			// Update all site page template conditions.
			$page_template_conditions = $this->get_site_page_template_conditions();

			if ( ! isset( $page_template_conditions[ $template_id ] ) ) {
				$page_template_conditions[ $template_id ] = [
					'conditions'    => [],
					'relation_type' => $relation_type,
				];
				update_option( $this->page_template_conditions_option_key, $page_template_conditions, true );
			}

			$page_template_list = $this->get_page_template_list();

			return [
				'type'     => 'success',
				'message'  => __( 'Page template has been created', 'jet-theme-core' ),
				'data' => [
					'newTemplateId' => $template_id,
					'list'          => $page_template_list,
				],
			];
		} else {
			return [
				'type'     => 'error',
				'message'  => __( 'Server Error. Please try again later.', 'jet-theme-core' ),
				'data' => [],
			];
		}
	}

	/**
	 * @param $template_id
	 *
	 * @return array
	 */
	public function delete_page_template( $template_id ) {

		if ( ! current_user_can( 'edit_posts' ) ) {
			return [
				'type'     => 'error',
				'message'  => __( 'You don\'t have permissions to do this', 'jet-theme-core' ),
			];
		}

		if ( ! $template_id ) {
			return [
				'type'     => 'error',
				'message'  => __( 'Invalid template id', 'jet-theme-core' ),
			];
		}

		$delete = wp_delete_post( $template_id, true );
		$page_template_list = $this->get_page_template_list();


		if ( $delete ) {

			// Update all site page template conditions.
			$page_template_conditions = $this->get_site_page_template_conditions();

			if ( isset( $page_template_conditions[ $template_id ] ) ) {
				unset( $page_template_conditions[ $template_id ] );
				update_option( $this->page_template_conditions_option_key, $page_template_conditions, true );
			}

			return [
				'type'     => 'success',
				'message'  => __( 'Page template has been deleted', 'jet-theme-core' ),
				'data'     => [
					'list' => $page_template_list,
				],
			];
		} else {
			return [
				'type'     => 'error',
				'message'  => __( 'Server Error. Please try again later.', 'jet-theme-core' ),
				'data'     => [
					'list' => $page_template_list,
				],
			];
		}
	}

	/**
	 * @param $template_id
	 *
	 * @return array
	 */
	public function copy_page_template( $template_id ) {

		if ( ! current_user_can( 'edit_posts' ) ) {
			return [
				'type'     => 'error',
				'message'  => __( 'You don\'t have permissions to do this', 'jet-theme-core' ),
			];
		}

		if ( ! $template_id ) {
			return [
				'type'     => 'error',
				'message'  => __( 'Invalid template id', 'jet-theme-core' ),
			];
		}

		$new_template_name   = get_the_title( $template_id ) . ' copy';

		$template_data = get_post( $template_id );

		$new_template    = array(
			'post_title'  => $new_template_name,
			'post_status' => 'publish',
			'post_type'   => $template_data->post_type,
			'post_author' => get_current_user_id(),
		);

		// Create new page template
		$new_template_id = wp_insert_post( $new_template );

		// Copy page template metadata
		$data = get_post_custom( $template_id );

		foreach ( $data as $key => $values ) {
			foreach ( $values as $value) {
				add_post_meta( $new_template_id, $key, maybe_unserialize( $value ) );
			}
		}

		if ( $new_template_id ) {

			// Update all site page template conditions.
			$page_template_conditions = $this->get_site_page_template_conditions();

			if ( isset( $page_template_conditions[ $template_id ] ) ) {
				$page_template_conditions[ $new_template_id ] = [
					'conditions'    => $this->get_page_template_conditions( $template_id ),
					'relation_type' => $this->get_page_template_relation_type( $template_id ),
				];

				update_option( $this->page_template_conditions_option_key, $page_template_conditions, true );
			}

			$page_template_list = $this->get_page_template_list();

			return [
				'type'     => 'success',
				'message'  => __( 'Page template has been deleted', 'jet-theme-core' ),
				'data' => [
					'list' => $page_template_list,
				],
			];
		} else {
			return [
				'type'     => 'error',
				'message'  => __( 'Server Error. Please try again later.', 'jet-theme-core' ),
				'data' => [],
			];
		}
	}

	/**
	 * @return array
	 */
	public function get_page_template_list( $template_name = false, $order_by = false ) {

		if ( ! empty( $this->page_template_list ) ) {
			return $this->page_template_list;
		}

		$params = [
			'post_type'           => $this->get_slug(),
			'ignore_sticky_posts' => true,
			'posts_per_page'      => -1,
			'suppress_filters'     => false,
		];

		if ( $template_name ) {
			$params['s'] = $template_name;
		}

		$structure_types = jet_theme_core()->structures->get_structure_types();

		$page_templates_data = get_posts( $params );

		if ( ! empty( $page_templates_data ) ) {
			foreach ( $page_templates_data as $template ) {
				$template_id = $template->ID;
				$author_id = $template->post_author;
				$author_data = get_userdata( $author_id );
				$type = get_post_meta( $template_id, '_type', true );

				if ( ! in_array( $type, $structure_types ) ) {
					continue;
				}

				$this->page_template_list [] = [
					'id'           => $template_id,
					'templateName' => $template->post_title,
					'date'         => [
						'raw'          => $template->post_date,
						'format'       => get_the_date( '', $template_id ),
						'lastModified' => $template->post_modified,
					],
					'author'       => [
						'id'   => $author_id,
						'name' => $author_data->user_login,
					],
					'type'         => $type,
					'conditions'   => get_post_meta( $template_id, '_conditions', true ),
					'relationType' => $this->get_page_template_relation_type( $template_id ),
					'layout'       => $this->get_page_template_layout( $template_id ),
					'exportLink'   => \Jet_Theme_Core\Theme_Builder\Page_Templates_Export_Import::get_instance()->get_page_template_export_link( $template_id )
				];
			}
		}

		return $this->page_template_list;
	}

	/**
	 * @param false $page_template_id
	 * @param false $structure
	 * @param array $structure_data
	 *
	 * @return array
	 */
	public function update_page_template_data( $id = false, $data = false ) {

		if ( ! $id || empty( $data ) ) {
			return [
				'type'     => 'error',
				'message'  => __( 'Server Error', 'jet-theme-core' ),
				'data' => [],
			];
		}

		if ( isset( $data['conditions'] ) ) {
			$this->update_page_template_conditions( $id, $data['conditions'] );
		}

		if ( isset( $data['relationType'] ) ) {
			$this->update_page_template_relation_type( $id, $data['relationType'] );
		}

		if ( isset( $data['layout'] ) ) {
			$this->update_page_template_layout( $id, $data['layout'] );
		}

		if ( isset( $data['type'] ) ) {
			$this->update_page_template_type( $id, $data['type'] );
		}

		if ( isset( $data['templateName'] ) ) {
			wp_update_post( [
				'ID'         => $id,
				'post_title' => $data['templateName'],
			] );
		}

		return [
			'type'     => 'success',
			'message'  => __( 'Page template layout updated', 'jet-theme-core' ),
			'data' => [],
		];
	}

	/**
	 * @param false $page_template_id
	 * @param array $conditions
	 */
	public function update_page_template_conditions( $page_template_id = false, $conditions = [] ) {

		update_post_meta( $page_template_id, '_conditions', $conditions );

		// Update all site page template conditions.
		$page_template_conditions = $this->get_site_page_template_conditions();

		if ( isset( $page_template_conditions[ $page_template_id ] ) ) {
			$page_template_conditions[ $page_template_id ]['conditions'] = $conditions;
		}

		update_option( $this->page_template_conditions_option_key, $page_template_conditions, true );
	}

	/**
	 * @param $page_template_id
	 * @param $relation_type
	 *
	 * @return void
	 */
	public function update_page_template_relation_type( $page_template_id = false, $relation_type = 'of' ) {

		update_post_meta( $page_template_id, '_relation_type', $relation_type );

		// Update all site page template conditions.
		$page_template_conditions = $this->get_site_page_template_conditions();

		if ( isset( $page_template_conditions[ $page_template_id ] ) ) {
			$page_template_conditions[ $page_template_id ]['relation_type'] = $relation_type;
		}

		update_option( $this->page_template_conditions_option_key, $page_template_conditions, true );
	}


	/**
	 * @param false $id
	 * @param array $layout
	 */
	public function update_page_template_layout( $id = false, $layout = [] ) {

		if ( ! $id || empty( $layout ) ) {
			return false;
		}

		return update_post_meta( $id, '_layout', $layout );
	}

	/**
	 * @param $template_id
	 *
	 * @return mixed
	 */
	public function get_page_template_layout( $template_id ) {
		$layout = get_post_meta( $template_id, '_layout', true );

		if ( ! empty( $layout ) ) {
			$is_modify = false;

			foreach ( $layout as $structure => $structure_data ) {

				if ( ! empty( $structure_data['id'] ) && 'publish' !== get_post_status( $structure_data['id'] ) ) {
					$layout[ $structure ]['id'] = false;
					$is_modify = true;
				}
			}

			if ( $is_modify ) {
				$this->update_page_template_layout( $template_id, $layout );
			}
		}

		return $layout;
	}

	/**
	 * @param false $id
	 * @param array $layout
	 */
	public function update_page_template_type( $id = false, $type = false ) {

		if ( ! $id || ! $type ) {
			return false;
		}

		return update_post_meta( $id, '_type', $type );
	}

	/**
	 * @param false $id
	 * @param false $data
	 *
	 * @return array
	 */
	public function update_template_data( $id = false,  $data = false ) {

		if ( ! $id || empty( $data ) ) {
			return [
				'type'     => 'error',
				'message'  => __( 'Server Error', 'jet-theme-core' ),
				'data' => [],
			];
		}

		if ( isset( $data['title'] ) ) {

			wp_update_post( [
				'ID'         => $id,
				'post_title' => $data['title'],
			] );
		}

		return [
			'type'     => 'success',
			'message'  => __( 'Template layout updated', 'jet-theme-core' ),
			'data' => [],
		];
	}

	/**
	 * @param false $page_template_id
	 *
	 * @return array|mixed
	 */
	public function get_page_template_conditions( $page_template_id = false ) {

		if ( ! $page_template_id ) {
			return [];
		}

		$page_template_conditions = get_post_meta( $page_template_id, '_conditions', true );

		return ! empty( $page_template_conditions ) ? $page_template_conditions : [];
	}

	/**
	 * @param false $page_template_id
	 *
	 * @return array|mixed
	 */
	public function get_page_template_relation_type( $page_template_id = false ) {

		if ( ! $page_template_id ) {
			return [];
		}

		$relation_type = get_post_meta( $page_template_id, '_relation_type', true );

		return ! empty( $relation_type ) ? $relation_type : 'or';
	}

	/**
	 * @param $page_template_id
	 *
	 * @return string
	 */
	public function get_page_template_export_link( $page_template_id ) {
		return add_query_arg(
			[
				'action'           => 'jet_theme_core_export_page_template',
				'page_template_id' => $page_template_id,
			],
			admin_url( 'admin-ajax.php' )
		);
	}

	/**
	 * @param false $template_id
	 *
	 * @return array|false
	 */
	public function get_used_page_templates_for_template( $template_id = false ) {

		if ( ! $template_id ) {
			return false;
		}

		$raw_page_template_list = $this->get_page_template_list();
		$page_template_list = [];

		if ( ! empty( $raw_page_template_list ) ) {

			foreach ( $raw_page_template_list as $key => $page_template_data ) {
				$page_template_layout = $page_template_data['layout'];

				foreach ( $page_template_layout as $layout => $layout_data ) {

					if ( $template_id === $layout_data['id'] ) {

						$page_template_url = add_query_arg( [
							'page'          => 'jet-theme-builder',
							'page_template' => $page_template_data['id'],
						], admin_url( 'admin.php' ) );

						$page_template_list[] = [
							'id'                 => $page_template_data['id'],
							'name'               => $page_template_data[ 'templateName' ],
							'date'               => $page_template_data[ 'date' ][ 'format' ],
							'author'             => $page_template_data[ 'author' ][ 'name' ],
							'type'               => $page_template_data[ 'type' ],
							'theme_builder_link' => $page_template_url,
						];
					}
				}
			}
		}

		if ( empty( $page_template_list ) ) {
			return false;
		}

		return $page_template_list;
	}

	/**
	 * @param false $template_id
	 */
	public function get_used_verbose_page_templates( $template_id = false, $structure_id = false ) {

		$warning_icon = \Jet_Theme_Core\Utils::get_admin_ui_icon( 'warning' );

		$exclude_structures = [ 'jet_section' ];

		if ( in_array( $structure_id, $exclude_structures ) ) {
			return sprintf(
				'<div class="jet-template-library__page-templates-alert warning"><div class="jet-template-library__message"><span>%1$s</span></div></div>',
				__( 'Template cannot be used for JetThemeBuilder', 'jet-theme-core' )
			);
		}

		$used_page_templates = $this->get_used_page_templates_for_template( $template_id );

		if ( empty( $used_page_templates ) ) {
			$warning_icon = \Jet_Theme_Core\Utils::get_admin_ui_icon( 'warning' );

			return sprintf(
				'<div class="jet-template-library__page-templates-alert warning"><div class="jet-template-library__message"><span>%1$s</span></div><a class="jet-template-library__action" href="%2$s" target="_blank">%3$s<span>%4$s</span></a></div>',
				__( 'Template isn\'t used yet', 'jet-theme-core' ),
				\Jet_Theme_Core\Utils::get_theme_bilder_link(),
				\Jet_Theme_Core\Utils::get_admin_ui_icon( 'plus' ),
				__( 'Use template', 'jet-theme-core' )
			);
		}

		$verbose = '';

		foreach ( $used_page_templates as $key => $page_template_data ) {
			$verbose .= sprintf( '<div class="jet-template-library__page-templates-item"><a class="page-template-name" href="%4$s" target="_blank">%1$s</a><i class="page-template-date">%2$s</i><i class="page-template-author">%3$s</i></div>',
				$page_template_data['name'],
				$page_template_data['date'],
				$page_template_data['author'],
				$page_template_data['theme_builder_link']
			);
		}

		$verbose = sprintf(
			'<div class="jet-template-library__page-templates-list">%1$s</div>',
			$verbose
		);

		return sprintf(
			'<div class="jet-template-library__page-templates">%1$s</div>',
			$verbose
		);
	}

	/**
	 * Constructor for the class
	 */
	public function __construct() {
		//$this->delete_page_template( 3789 );
	}

}
