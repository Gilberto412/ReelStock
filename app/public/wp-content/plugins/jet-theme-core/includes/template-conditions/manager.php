<?php
namespace Jet_Theme_Core\Template_Conditions;

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

class Manager {

	/**
	 * A reference to an instance of this class.
	 *
	 * @since 1.0.0
	 * @var   object
	 */
	private static $instance = null;

	/**
	 * @var array
	 */
	private $_conditions = [];

	/**
	 * @var array
	 */
	private $_condition_sub_groups = [];

	/**
	 * @var string
	 */
	public $conditions_key = 'jet_site_conditions';

	/**
	 * Returns the instance.
	 *
	 * @since  1.0.0
	 * @return object
	 */
	public static function instance() {

		// If the single instance hasn't been set, set it now.
		if ( null == self::$instance ) {
			self::$instance = new self;
		}

		return self::$instance;
	}

	/**
	 * [load_files description]
	 * @return [type] [description]
	 */
	public function load_files() {}

	/**
	 * [register_conditions description]
	 * @return [type] [description]
	 */
	public function register_conditions() {

		$this->_condition_sub_groups = apply_filters( 'jet-theme-core/template-conditions/condition-sub-groups', [
			'page-singular'  => [
				'label'  => __( 'Page', 'jet-theme-core' ),
				'options' => [],
			],
			'post-archive'  => [
				'label'  => __( 'Post', 'jet-theme-core' ),
				'options' => [],
			],
			'post-singular' => [
				'label'  => __( 'Post', 'jet-theme-core' ),
				'options' => [],
			],
		] );

		$base_path = jet_theme_core()->plugin_path( 'includes/template-conditions/conditions/' );

		require $base_path . 'base.php';

		$default_conditions = apply_filters( 'jet-theme-core/template-conditions/conditions-list', [
			'\Jet_Theme_Core\Template_Conditions\Entire'                 => $base_path . 'entire.php',

			// Singular conditions
			'\Jet_Theme_Core\Template_Conditions\Front_Page'             => $base_path . 'singular-front-page.php',
			'\Jet_Theme_Core\Template_Conditions\Page'                   => $base_path . 'singular-page.php',
			'\Jet_Theme_Core\Template_Conditions\Page_Child'             => $base_path . 'singular-page-child.php',
			'\Jet_Theme_Core\Template_Conditions\Page_Template'          => $base_path . 'singular-page-template.php',
			'\Jet_Theme_Core\Template_Conditions\Page_404'               => $base_path . 'singular-404.php',
			'\Jet_Theme_Core\Template_Conditions\Post'                   => $base_path . 'singular-post.php',
			'\Jet_Theme_Core\Template_Conditions\Post_From_Category'     => $base_path . 'singular-post-from-cat.php',
			'\Jet_Theme_Core\Template_Conditions\Post_From_Tag'          => $base_path . 'singular-post-from-tag.php',

			// Archive conditions
			'\Jet_Theme_Core\Template_Conditions\Archive_All'            => $base_path . 'archive-all.php',
			'\Jet_Theme_Core\Template_Conditions\Archive_Category'       => $base_path . 'archive-category.php',
			'\Jet_Theme_Core\Template_Conditions\Archive_Tag'            => $base_path . 'archive-tag.php',
			'\Jet_Theme_Core\Template_Conditions\Archive_Search'         => $base_path . 'archive-search-results.php',

			// Custom Post Type
			'\Jet_Theme_Core\Template_Conditions\CPT_Singular_Post_Type' => $base_path . 'cpt-singular/cpt-singular-post-type.php',
			'\Jet_Theme_Core\Template_Conditions\CPT_Archive_Post_Type'  => $base_path . 'cpt-archive/cpt-archive-post-type.php',
			'\Jet_Theme_Core\Template_Conditions\CPT_Archive_Taxonomy'   => $base_path . 'cpt-archive/cpt-archive-taxonomy.php',

			// Advanced
			'\Jet_Theme_Core\Template_Conditions\Url_Param'              => $base_path . 'advanced-url-param.php',
			'\Jet_Theme_Core\Template_Conditions\Device'                 => $base_path . 'advanced-device.php',
			'\Jet_Theme_Core\Template_Conditions\Roles'                  => $base_path . 'advanced-roles.php',
			'\Jet_Theme_Core\Template_Conditions\Mobile_OS'              => $base_path . 'advanced-mobile-os.php',
			'\Jet_Theme_Core\Template_Conditions\Mobile_Browsers'        => $base_path . 'advanced-mobile-browsers.php',
		] );

		foreach ( $default_conditions as $class => $file ) {
			require $file;

			$instance = new $class;
			$id = $instance->get_id();
			$label = $instance->get_label();
			$sub_group = $instance->get_sub_group();

			$this->_conditions[ $id ] = $instance;

			$this->add_condition_sub_group_option( $sub_group, $id, $label );
		}

		$this->register_cpt_conditions();

		do_action( 'jet-theme-core/template-conditions/register', $this );

	}

	/**
	 *
	 */
	public function register_cpt_conditions() {
		$base_path = jet_theme_core()->plugin_path( 'includes/template-conditions/conditions/' );

		require $base_path . 'cpt-archive/cpt-archive.php';
		require $base_path . 'cpt-archive/cpt-taxonomy.php';
		require $base_path . 'cpt-singular/cpt-single-post.php';
		require $base_path . 'cpt-singular/cpt-single-post-term.php';

		$post_types = \Jet_Theme_Core\Utils::get_custom_post_types_options();

		foreach ( $post_types as $type ) {
			$post_type_slug = $type[ 'value' ];
			$post_type_label = $type[ 'label' ];
			$post_type_obj = get_post_type_object( $post_type_slug );
			$post_type_taxonomies = \Jet_Theme_Core\Utils::get_taxonomies_by_post_type( $post_type_slug );

			$archive_sub_group = $post_type_slug . '-archive';
			$this->register_condition_sub_group( $archive_sub_group, $post_type_label );

			$single_sub_group = $post_type_slug . '-single-post';
			$this->register_condition_sub_group( $single_sub_group, $post_type_label );

			$instance = new CPT_Single_Post( [
				'id'             => 'cpt-single-' . $post_type_slug,
				'label'          => sprintf( __( '%s Single', 'jet-theme-core' ), $post_type_obj->labels->singular_name ),
				'group'          => 'singular',
				'sub_group'      => $single_sub_group,
				'priority'       => 28,
				'body_structure' => 'jet_single',
				'value_control'  => [
					'type'        => 'f-search-select',
					'placeholder' => __( 'Select', 'jet-theme-core' ),
				],
				'value_options'  => false,
				'ajax_action'    =>  [
					'action' => 'get-posts',
					'params' => [
						'post_type' => $post_type_slug,
						'query'     => '',
					],
				],
			] );

			$this->_conditions[ $instance->get_id() ] = $instance;
			$this->add_condition_sub_group_option( $single_sub_group, 'cpt-single-' . $post_type_slug, $post_type_label );

			$instance = new CPT_Archive( [
				'id'             => 'cpt-archive-' . $post_type_slug,
				'label'          =>  sprintf( __( 'All %s Archives', 'jet-theme-core' ), $post_type_label ),
				'group'          => 'archive',
				'sub_group'      => $archive_sub_group,
				'priority'       => 9,
				'body_structure' => 'jet_archive',
			] );

			$this->_conditions[ $instance->get_id() ] = $instance;
			$this->add_condition_sub_group_option( $archive_sub_group, 'cpt-archive-' . $post_type_slug, sprintf( __( 'All %s Archives', 'jet-theme-core' ), $post_type_label ) );

			foreach ( $post_type_taxonomies as $taxonomy => $taxonomy_obj ) {

				$instance = new CPT_Taxonomy( [
					'id'             => 'cpt-taxonomy-' . $taxonomy_obj->name,
					'label'          => $taxonomy_obj->label,
					'group'          => 'archive',
					'sub_group'      => $archive_sub_group,
					'priority'       => 45,
					'body_structure' => 'jet_archive',
					'value_control'  => [
						'type'        => 'f-search-select',
						'placeholder' => __( 'Select taxonomy', 'jet-theme-core' ),
					],
					'value_options'  => false,
					'ajax_action'    =>  [
						'action' => 'get-tax-terms',
						'params' => [
							'tax_name' => $taxonomy_obj->name,
						],
					],
				] );

				$this->_conditions[ $instance->get_id() ] = $instance;
				$this->add_condition_sub_group_option( $archive_sub_group, 'cpt-taxonomy-' . $taxonomy_obj->name, $taxonomy_obj->label );

				$instance = new CPT_Single_Post_Term( [
					'id'             => 'cpt-post-term-' . $taxonomy_obj->name,
					'label'          => $taxonomy_obj->label,
					'group'          => 'singular',
					'sub_group'      => $single_sub_group,
					'priority'       => 27,
					'body_structure' => 'jet_single',
					'value_control'  => [
						'type'        => 'f-search-select',
						'placeholder' => __( 'Select taxonomy', 'jet-theme-core' ),
					],
					'value_options'  => false,
					'ajax_action'    =>  [
						'action' => 'get-tax-terms',
						'params' => [
							'tax_name' => $taxonomy_obj->name,
						],
					],
				] );

				$this->_conditions[ $instance->get_id() ] = $instance;
				$this->add_condition_sub_group_option( $single_sub_group, 'cpt-post-term-' . $taxonomy_obj->name, sprintf( 'In %s', $taxonomy_obj->label) );
			}
		}

	}

	/**
	 * @param false $id
	 * @param string $label
	 *
	 * @return false
	 */
	public function register_condition_sub_group( $id = false, $label = '' ) {

		if ( ! $id ) {
			return false;
		}

		if ( array_key_exists( $id, $this->_condition_sub_groups ) ) {
			return false;
		}

		$this->_condition_sub_groups[ $id ] = [
			'label'   => $label,
			'options' => [],
		];

	}

	/**
	 * @return array
	 */
	public function get_condition_sub_groups() {
		return $this->_condition_sub_groups;
	}

	/**
	 * @param false $sub_group
	 * @param false $id
	 * @param string $label
	 */
	public function add_condition_sub_group_option( $sub_group = false, $id = false, $label = '' ) {

		if ( ! $sub_group ) {
			return false;
		}

		if ( ! array_key_exists( $sub_group, $this->_condition_sub_groups ) ) {
			return false;
		}

		$this->_condition_sub_groups[ $sub_group ]['options'][] = [
			'label' => $label,
			'value' => $id,
		];
	}

	/**
	 * @return false
	 */
	public function maybe_update_backward_conditions() {

		if ( version_compare( JET_THEME_CORE_VERSION, '2.0.0', '<' ) ) {
			return false;
		}

		$site_conditions = get_option( $this->conditions_key, [] );

		if ( empty( $site_conditions ) ) {
			return false;
		}

		foreach ( $site_conditions as $type => $type_templates ) {

			if ( ! empty( $type_templates ) ) {

				foreach ( $type_templates as $template_id => $template_conditions ) {

					if ( ! array_key_exists( 'main', $template_conditions ) ) {
						continue;
					}

					$elementor_template_type = get_post_meta( $template_id, '_elementor_template_type', true );
					update_post_meta( $template_id, '_jet_template_type', $elementor_template_type );
					$is_elementor_content_type = get_post_meta( $template_id, '_elementor_edit_mode', true );

					if ( 'builder' === $is_elementor_content_type ) {
						update_post_meta( $template_id, '_jet_template_content_type', 'elementor' );
					}
					
					$converted_template_conditions = $this->maybe_convert_conditions( $template_conditions );

					if ( $converted_template_conditions ) {
						$this->update_template_conditions( $template_id, $converted_template_conditions );
					}
				}
			}
		}

		return false;
	}

	/**
	 * [get_condition description]
	 * @param  [type] $condition_id [description]
	 * @return [type]               [description]
	 */
	public function get_condition( $condition_id ) {
		return isset( $this->_conditions[ $condition_id ] ) ? $this->_conditions[ $condition_id ] : false;
	}

	/**
	 * [get_template_id description]
	 * @return [type] [description]
	 */
	public function get_template_id() {
		return get_the_ID();
	}

	/**
	 * [update_template_conditions description]
	 * @param  boolean $post_id    [description]
	 * @param  array   $conditions [description]
	 * @return [type]              [description]
	 */
	public function update_template_conditions( $template_id = false, $conditions = [] ) {
		update_post_meta( $template_id, '_jet_template_conditions', $conditions );

		$siteTemplateConditions = get_option( $this->conditions_key, [] );
		$type = get_post_meta( $template_id, '_jet_template_type', true );
		$type = isset( $type ) ? $type : 'other';

		if ( ! isset( $siteTemplateConditions[ $type ] ) ) {
			$siteTemplateConditions[ $type ] = [];
		}

		if ( isset( $siteTemplateConditions[ $type ][ $template_id ] ) ) {
			unset( $siteTemplateConditions[ $type ][ $template_id ] );
			$new_condition[ $template_id ] = $conditions;
			$siteTemplateConditions[ $type ] = $new_condition + $siteTemplateConditions[ $type ];
		} else {
			$siteTemplateConditions[ $type ][ $template_id ] = $conditions;
			$siteTemplateConditions[ $type ] = array_reverse( $siteTemplateConditions[ $type ], true );
		}

		update_option( $this->conditions_key, $siteTemplateConditions, true );
	}

	/**
	 * [get_template_conditions description]
	 * @param  boolean $post_id [descriptionupdate_template_conditions
	 * @return [type]           [description]
	 */
	public function get_template_conditions( $template_id = false ) {
		$template_conditions = get_post_meta( $template_id, '_jet_template_conditions', true );

		return ! empty( $template_conditions ) ? $template_conditions : [];
	}

	/**
	 * [remove_post_from_site_conditions description]
	 * @param  integer $post_id [description]
	 * @return [type]           [description]
	 */
	public function remove_post_from_site_conditions( $post_id = 0 ) {
		$conditions = get_option( $this->conditions_key, [] );
		$conditions = $this->remove_post_from_conditions_array( $post_id, $conditions );

		update_option( $this->conditions_key, $conditions, true );
	}

	/**
	 * Check if post currently presented in conditions array and remove it if yes.
	 *
	 * @param  integer $post_id    [description]
	 * @param  array   $conditions [description]
	 * @return [type]              [description]
	 */
	public function remove_post_from_conditions_array( $post_id = 0, $conditions = array() ) {

		foreach ( $conditions as $type => $type_conditions ) {
			if ( array_key_exists( $post_id, $type_conditions ) ) {
				unset( $conditions[ $type ][ $post_id ] );
			}
		}

		return $conditions;
	}

	/**
	 * Run condtions check for passed type. Return {template_id} on firs condition match.
	 * If not matched - return false
	 *
	 * @return int|bool
	 */
	public function find_matched_conditions( $type, $single = false ) {

		$conditions = get_option( $this->conditions_key, [] );

		if ( empty( $conditions[ $type ] ) ) {
			return false;
		}

		$template_id_list = [];

		foreach ( $conditions[ $type ] as $template_id => $template_conditions ) {

			if ( empty( $template_conditions ) || array_key_exists( 'main', $template_conditions ) ) {
				continue;
			}

			$check_list = [];

			$template_conditions = array_map( function( $condition ) use ( $template_id ) {

				$include = filter_var( $condition['include'], FILTER_VALIDATE_BOOLEAN );

				if ( 'entire' === $condition['group'] ) {
					$match = 'entire' === $condition['group'] ? true : false;
					$condition['match'] = $match;

					return $condition;
				} else {
					$sub_group = $condition['subGroup'];

					$instance = $this->get_condition( $sub_group );

					if ( ! $instance ) {
						$condition['match'] = true;

						return $condition;
					}

					$sub_group_value = isset( $condition['subGroupValue'] ) ? $condition['subGroupValue'] : '';

					$instance_check = call_user_func( array( $instance, 'check' ), $sub_group_value, $sub_group );

					$condition['match'] = $instance_check;

					return $condition;

				}

				return $condition;

			}, $template_conditions );

			$includes_matchs = [];
			$excludes_matchs = [];

			foreach ( $template_conditions as $key => $condition ) {
				$include = filter_var( $condition['include'], FILTER_VALIDATE_BOOLEAN );

				if ( $include ) {
					$includes_matchs[] = $condition['match'];
				} else {
					$excludes_matchs[] = $condition['match'];
				}
			}

			if ( in_array( true, $includes_matchs ) && ! in_array( true, $excludes_matchs ) ) {
				$template_id_list[] = $template_id;
			}
		}

		if ( ! empty( $template_id_list ) ) {

			if ( $single ) {
				return $template_id_list[0];
			}

			return $template_id_list;
		}

		return false;
	}

	/**
	 * @param array $condition
	 *
	 * @return array|mixed
	 */
	public function maybe_convert_conditions( $condition = [] ) {

		if ( ! array_key_exists( 'main', $condition ) ) {
			return false;
		}

		$new_condition        = [];
		$condition_array_keys = array_keys( $condition );
		$sub_group            = isset( $condition_array_keys[1] ) ? $condition_array_keys[1] : false;
		$sub_group_value      = '';

		if ( $sub_group && isset( $sub_group ) ) {
			$sub_group_key   = $condition[ $sub_group ];
			$key_value       = ! empty( array_keys( $sub_group_key ) ) ? array_keys( $sub_group_key )[ 0 ] : false;
			$sub_group_value = $key_value ? $sub_group_key[ $key_value ] : '';
		}

		if ( ! empty( $sub_group_value ) && is_array( $sub_group_value ) ) {

			foreach ( $sub_group_value as $key => $value ) {
				$new_condition[] = [
					'id'            => uniqid( '_' ),
					'include'       => 'true',
					'group'         => $condition['main'],
					'subGroup'      => $sub_group ? $sub_group : 'entire',
					'subGroupValue' => $value,
				];
			}

			return $new_condition;
		} else {
			$sub_group_value = ! is_array( $sub_group_value ) ? $sub_group_value : '';

			$new_condition[] = [
				'id'            => uniqid( '_' ),
				'include'       => 'true',
				'group'         => $condition['main'],
				'subGroup'      => $sub_group ? $sub_group : 'entire',
				'subGroupValue' => $sub_group_value,
			];

			return $new_condition;
		}

		return false;
	}

	/**
	 * Get active conditions for passed post
	 *
	 * @param  [type] $post_id [description]
	 * @return [type]          [description]
	 */
	public function post_conditions_verbose( $post_id = null ) {
		$structure = jet_theme_core()->structures->get_post_structure( $post_id );

		$warning_icon = \Jet_Theme_Core\Utils::get_admin_ui_icon( 'warning' );

		if ( ! $structure->has_conditions() ) {

			return sprintf(
				'<div class="jet-template-library__template-conditions-item undefined-conditions">%1$s<span>%2$s</span></div>',
				$warning_icon,
				__( 'Not available for this type', 'jet-theme-core' )
			);
		}

		$conditions = $this->get_template_conditions( $post_id );

		if ( empty( $conditions ) ) {

			return sprintf(
				'<div class="jet-template-library__template-conditions-item undefined-conditions"><span>%1$s</span></div>',
				__( 'Conditions aren\'t selected', 'jet-theme-core' )
			);
		}

		$verbose = '';

		if ( $this->isActiveTemplateStructure( $post_id ) ) {
			$verbose .= sprintf(
				'<div class="jet-template-library__template-conditions-item active-structure"><span class="dashicons dashicons-yes-alt"></span>%1$s</div>',
				__( 'Active', 'jet-theme-core' )
			);
		}

		foreach ( $conditions as $key => $condition ) {
			$include         = filter_var( $condition['include'], FILTER_VALIDATE_BOOLEAN );
			$group           = $condition['group'];
			$sub_group       = $condition['subGroup'];
			$sub_group_value = $condition['subGroupValue'];

			if ( $include ) {
				$item_icon = '<span class="dashicons dashicons-plus-alt2"></span>';
			} else {
				$item_icon = '<span class="dashicons dashicons-minus"></span>';
			}

			if ( 'entire' === $group ) {
				$verbose .= sprintf( '<div class="jet-template-library__template-conditions-item match-condition">%2$s<span>%1$s</span></div>', __( 'Entire Site', 'jet-theme-core' ), $item_icon );

				continue;
			}

			$instance = $this->get_condition( $sub_group );

			if ( ! $instance ) {
				continue;
			}

			$item_class = 'jet-template-library__template-conditions-item match-condition';

			if ( ! $include ) {
				$item_class .= ' exclude';
				$item_icon = '<span class="dashicons dashicons-minus"></span>';
			}

			if ( ! empty( $sub_group_value ) ) {
				$label = $instance->get_label_by_value( $sub_group_value );
				$verbose .= sprintf( '<div class="%1$s">%4$s<span>%2$s: </span><i>%3$s</i></div>', $item_class, $instance->get_label(), $label, $item_icon );
			} else {
				$verbose .= sprintf( '<div class="%1$s">%3$s<span>%2$s</span></div>', $item_class, $instance->get_label(), $item_icon );
			}
		}

		return sprintf(
			'<div class="jet-template-library__template-conditions-list">%1$s</div>',
			$verbose
		) ;
	}

	/**
	 * @param $template_id
	 *
	 * @return bool
	 */
	public function isActiveTemplateStructure( $template_id ) {
		$structure = jet_theme_core()->structures->get_post_structure( $template_id );

		if ( ! $structure ) {
			return false;
		}

		$structure_id   = $structure->get_id();
		$siteConditions = get_option( 'jet_site_conditions', [] );

		if ( empty( $siteConditions ) || ! isset( $siteConditions[ $structure_id ] ) ) {
			return false;
		}

		$structure_templates = array_keys( $siteConditions[ $structure_id ] );

		if ( in_array( $template_id, $structure_templates ) ) {
			return true;
		}

		return false;
	}

	/**
	 * [prepare_data_for_localize description]
	 * @return [type] [description]
	 */
	public function get_conditions_raw_data() {

		$sorted_conditions = apply_filters( 'jet-theme-core/template-conditions/conditions-group-list', [
			'entire'       => [
				'label'      => __( 'Entire', 'jet-theme-core' ),
				'sub-groups' => [],
			],
			'singular'     => [
				'label'      => __( 'Singular', 'jet-theme-core' ),
				'sub-groups' => [],
			],
			'archive'      => [
				'label'      => __( 'Archive', 'jet-theme-core' ),
				'sub-groups' => [],
			],
			'advanced'     => [
				'label'      => __( 'Advanced', 'jet-theme-core' ),
				'sub-groups' => [],
			],
		] );

		foreach ( $this->_conditions as $cid => $instance ) {
			$group = $instance->get_group();

			$current = [
				'label'         => $instance->get_label(),
				'priority'      => $instance->get_priority(),
				'action'        => $instance->ajax_action(),
				'options'       => $instance->get_avaliable_options(),
				'control'       => $instance->get_control(),
				'bodyStructure' => $instance->get_body_structure(),
			];

			$sorted_conditions[ $group ]['sub-groups'][ $cid ] = $current;
		}

		foreach ( $sorted_conditions as $group => $group_conditions ) {

			if ( isset( $group_conditions['sub-groups'] ) ) {
				$group_options = $this->get_condition_group_options( $group_conditions['sub-groups'] );
			} else {
				$group_options = [];
			}

			$sorted_conditions[ $group ]['options'] = $group_options;
		}

		return $sorted_conditions;
	}

	/**
	 * @param array $group_conditions
	 *
	 * @return false
	 */
	public function get_condition_group_options( $group_conditions = [] ) {

		if ( empty( $group_conditions ) ) {
			return [];
		}

		$options = [];
		$condition_sub_groups = $this->get_condition_sub_groups();

		foreach ( $group_conditions as $condition_id => $condition_data ) {
			$instance = $this->get_condition( $condition_id );
			$sub_group = $instance->get_sub_group();

			if ( ! $sub_group ) {
				$options[ $condition_id ] = [
					'label' => $condition_data['label'],
					'value' => $condition_id,
				];
			} else {
				if ( array_key_exists( $sub_group, $condition_sub_groups ) ) {

					if ( ! array_key_exists( $sub_group, $options ) ) {
						$options[ $sub_group ] = $condition_sub_groups[ $sub_group ];
					}
				}
			}
		}

		return array_values( $options );
	}

	/**
	 * [__construct description]
	 */
	public function __construct() {
		$this->load_files();
		$this->maybe_update_backward_conditions();

		add_action( 'init', [ $this, 'register_conditions' ], 999  );
		add_action( 'wp_trash_post', array( $this, 'remove_post_from_site_conditions' ) );
	}

}
