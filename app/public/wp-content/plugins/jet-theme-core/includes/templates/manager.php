<?php
namespace Jet_Theme_Core;
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

class Templates {

	/**
	 * Post type slug.
	 *
	 * @var string
	 */
	public $post_type = 'jet-theme-core';

	/**
	 * Template meta cache key
	 *
	 * @var string
	 */
	public $cache_key = '_jet_template_cache';

	/**
	 * Template type arg for URL
	 * @var string
	 */
	public $type_tax = 'jet_library_type';

	/**
	 * Site conditions
	 * @var array
	 */
	private $conditions = array();

	/**
	 * @var bool
	 */
	public $export_import_manager = false;

	/**
	 * Templates post type slug
	 *
	 * @return string
	 */
	public function slug() {
		return $this->post_type;
	}

	/**
	 * Init comnonents
	 */
	public function init_components() {
		$this->export_import_manager = new Templates_Export_Import();
	}

	/**
	 * Register templates post type
	 *
	 * @return void
	 */
	public function register_post_types() {

		$args = array(
			'labels' => array(
				'name'               => esc_html__( 'Theme Parts', 'jet-theme-core' ),
				'singular_name'      => esc_html__( 'Template', 'jet-theme-core' ),
				'add_new'            => esc_html__( 'Add New', 'jet-theme-core' ),
				'add_new_item'       => esc_html__( 'Add New Template', 'jet-theme-core' ),
				'edit_item'          => esc_html__( 'Edit Template', 'jet-theme-core' ),
				'new_item'           => esc_html__( 'Add New Template', 'jet-theme-core' ),
				'view_item'          => esc_html__( 'View Template', 'jet-theme-core' ),
				'search_items'       => esc_html__( 'Search Template', 'jet-theme-core' ),
				'not_found'          => esc_html__( 'No Templates Found', 'jet-theme-core' ),
				'not_found_in_trash' => esc_html__( 'No Templates Found In Trash', 'jet-theme-core' ),
				'menu_name'          => esc_html__( 'My Library', 'jet-theme-core' ),
			),
			'public'              => true,
			'hierarchical'        => false,
			'show_ui'             => true,
			'show_in_menu'        => false,
			'show_in_rest'        => true,
			'show_in_nav_menus'   => false,
			'can_export'          => true,
			'exclude_from_search' => true,
			'capability_type'     => 'post',
			'rewrite'             => false,
			'supports'            => array( 'title', 'editor', 'thumbnail', 'author', 'elementor', 'custom-fields' ),
		);

		register_post_type(
			$this->slug(),
			apply_filters( 'jet-theme-core/templates/post-type/args', $args )
		);

		$tax_args = array(
			'hierarchical'      => false,
			'show_ui'           => true,
			'show_in_nav_menus' => false,
			'show_admin_column' => true,
			'query_var'         => is_admin(),
			'rewrite'           => false,
			'public'            => false,
			'label'             => __( 'Type', 'jet-theme-core' ),
		);

		register_taxonomy(
			$this->type_tax,
			$this->slug(),
			apply_filters( 'jet-theme-core/templates/type-tax/args', $tax_args )
		);
	}

	/**
	 * Set required post columns
	 *
	 * @param  array $columns
	 * @return array
	 */
	public function set_post_columns( $columns ) {

		unset( $columns['taxonomy-' . $this->type_tax ] );
		unset( $columns['date'] );

		$columns['type'] = __( 'Type', 'jet-theme-core' );

		$columns['page-template'] = __( 'Page Templates', 'jet-theme-core' );

		$exclude_structures = apply_filters( 'jet-theme-core/admin/template-library/column-exclude-structures', [ 'jet_page', 'jet_section' ] );

		if ( ! isset( $_GET[ $this->type_tax ] ) || ! in_array( $_GET[ $this->type_tax ], $exclude_structures ) ) {
			$icon = \Jet_Theme_Core\Utils::get_admin_ui_icon( 'info' );
			$columns['conditions'] = sprintf('<div class="jet-template-library__theme-location-info-label"><span>%2$s</span>%1$s</div>', $icon, __( 'Theme Location', 'jet-theme-core' ) );
		}

		$columns['date'] = __( 'Date', 'jet-theme-core' );

		return $columns;

	}

	/**
	 * Manage post columns content
	 *
	 * @return [type] [description]
	 */
	public function post_columns( $column, $template_id ) {

		$structure = jet_theme_core()->structures->get_post_structure( $template_id );

		if ( ! $structure ) {
			return false;
		}

		switch ( $column ) {

			case 'type':

				if ( $structure ) {

					$link = add_query_arg( array(
						$this->type_tax => $structure->get_id(),
					) );

					$template_content_type = $this->get_template_content_type( $template_id );
					$template_content_type_icons = $this->get_template_content_type_icons();
					$icon = isset( $template_content_type_icons[ $template_content_type ] ) ? $template_content_type_icons[ $template_content_type ] : '';

					printf( '<a href="%1$s" class="jet-template-library-column__template-type"><span class="type-icon">%3$s</span><span>%2$s</span></a>', $link, $structure->get_single_label(), $icon );

					do_action( "jet-theme-core/admin/template-library/column-type/{$template_content_type}-type/after", $template_id );
				}

				break;

			case 'page-template':
				$page_templates_verbose = jet_theme_core()->theme_builder->page_templates_manager->get_used_verbose_page_templates( $template_id, $structure->get_id() );

				echo $page_templates_verbose;
				break;

			case 'conditions':

				$template_verbose_conditions = jet_theme_core()->template_conditions_manager->post_conditions_verbose( $template_id );

				echo sprintf( '<div class="jet-template-library__template-conditions" data-template-id="%1$s" data-structure-type="%2$s">%3$s</div>',
					$template_id,
					$structure->get_id(),
					$template_verbose_conditions
				);

				if ( $structure->has_conditions() ) {
					printf(
						'<a class="jet-template-library__template-edit-conditions" href="#" data-template-id="%1$s" data-structure-type="%2$s">%3$s<span>%4$s</span></a>',
						$template_id,
						$structure->get_id(),
						\Jet_Theme_Core\Utils::get_admin_ui_icon( 'edit' ),
						__( 'Edit Conditions', 'jet-theme-core' )
					);
				}

				break;
		}
	}

	/**
	 * @param false $template_type
	 * @param string $content_type
	 * @param string $template_name
	 *
	 * @return array
	 */
	public function create_template( $template_type = false, $template_content_type = 'default', $template_name = '', $template_conditions = [] ) {

		if ( ! current_user_can( 'edit_posts' ) ) {
			return [
				'type'          => 'error',
				'message'       => __( 'You don\'t have permissions to do this', 'jet-theme-core' ),
				'redirect'      => false,
				'newTemplateId' => false,
			];
		}

		$template_types = jet_theme_core()->structures->get_structures_for_post_type();

		if ( ! $template_type || ! array_key_exists( $template_type, $template_types ) ) {
			return [
				'type'          => 'error',
				'message'       => __( 'Incorrect template type. Please try again.', 'jet-theme-core' ),
				'redirect'      => false,
				'newTemplateId' => false,
			];
		}

		switch ( $template_content_type ) {
			case 'default':
				$meta_input = [
					'_jet_template_conditions'   => $template_conditions,
					'_jet_template_content_type' => $template_content_type,
					'_jet_template_type'         => $template_type,
				];
				break;
			case 'elementor':
				$documents = \Elementor\Plugin::instance()->documents;
				$doc_type  = $documents->get_document_type( $template_type );

				if ( ! $doc_type ) {
					return [
						'type'          => 'error',
						'message'       => __( 'Incorrect template type.', 'jet-theme-core' ),
						'redirect'      => false,
						'newTemplateId' => false,
					];
				}

				$meta_input = [
					'_elementor_edit_mode'       => 'builder',
					$doc_type::TYPE_META_KEY     => $template_type,
					'_jet_template_conditions'   => [],
					'_jet_template_content_type' => $template_content_type,
					'_jet_template_type'         => $template_type,
				];

				break;
		}

		$post_title = $template_name;

		if ( empty( $template_name ) ) {
			$post_title = ucwords( str_replace( '_', ' ', $template_type ) );
		}

		$post_data = array(
			'post_status' => 'publish',
			'post_title'  => $post_title,
			'post_type'   => $this->slug(),
			'tax_input'   => array(
				$this->type_tax => $template_type,
			),
			'meta_input' => $meta_input,
		);

		$template_id = wp_insert_post( $post_data, true );

		if ( empty( $template_name ) ) {
			$post_title = $post_title . ' #' . $template_id;

			wp_update_post( [
				'ID'         => $template_id,
				'post_title' => $post_title,
			] );
		}

		if ( $template_id ) {

			switch ( $template_content_type ) {
				case 'default':
					$redirect = get_edit_post_link( $template_id, '' );
					break;
				case 'elementor':
					$redirect = \Elementor\Plugin::$instance->documents->get( $template_id )->get_edit_url();
					break;
			}

			return [
				'type'          => 'success',
				'message'       => __( 'Template has been created', 'jet-theme-core' ),
				'redirect'      => $redirect,
				'newTemplateId' => $template_id,
			];
		} else {
			return [
				'type'          => 'error',
				'message'       => __( 'Server Error. Please try again later.', 'jet-theme-core' ),
				'redirect'      => false,
				'newTemplateId' => false,
			];
		}
	}

	/**
	 * @param $template_id
	 *
	 * @return mixed|string
	 */
	public function get_template_type( $template_id ) {
		$type = get_post_meta( $template_id, '_jet_template_type', true );

		return ! empty( $type ) ? $type : false;
	}

	/**
	 * @param $template_id
	 *
	 * @return string
	 */
	public function get_template_content_type( $template_id ) {
		$content_type = get_post_meta( $template_id, '_jet_template_content_type', true );

		return ! empty( $content_type ) ? $content_type : 'default';
	}

	/**
	 * @param $template_id
	 * @return false
	 */
	public function is_template_exist( $template_id ) {
		 var_dump( get_post_status( $template_id ) );
		return false;
	}

	/**
	 * @return array|int[]|\WP_Post[]
	 */
	public function get_raw_template_list() {
		$raw_templates = get_posts( [
			'post_type'           => $this->post_type,
			'ignore_sticky_posts' => true,
			'posts_per_page'      => -1,
			'suppress_filters'     => false,
			'meta_query'          => apply_filters( 'jet-theme-core/templates/meta-query-params', [] ),
		] );

		if ( empty( $raw_templates ) ) {
			return [];
		}

		return $raw_templates;
	}

	/**
	 * @return array|array[]
	 */
	public function get_template_list() {

		$raw_templates = $this->get_raw_template_list();

		if ( empty( $raw_templates ) ) {
			return [];
		}

		$templates = array_map( function( $template_obj ) {
			$template_id  = $template_obj->ID;
			$type         = $this->get_template_type( $template_id );
			$content_type = $this->get_template_content_type( $template_id );
			$edit_link    = $this->get_template_edit_link( $template_id, $content_type );
			$author_id    = $template_obj->post_author;
			$author_data  = get_userdata( $author_id );
			$details      = [ 'jet-theme-core' ];

			if ( 'default' === $content_type ) {
				$details[] = 'block-editor';
			}

			return [
				'id'          => $template_id,
				'title'       => $template_obj->post_title,
				'type'        => $type,
				'contentType' => $content_type,
				'editLink'    => $edit_link,
				'details'     => apply_filters( 'jet-theme-core/templates/structure-template-details', $details, $template_id, $type, $content_type ),
				'date'        => [
					'raw'          => $template_obj->post_date,
					'format'       => get_the_date( '', $template_id ),
					'lastModified'  => $template_obj->post_modified,
				],
				'author'      => [
					'id'   => $author_id,
					'name' => $author_data->user_login,
				],
			];
		}, $raw_templates );

		$templates = apply_filters( 'jet-theme-core/templates/raw-templates-list', $templates );

		return $templates;
	}

	/**
	 * @param $type
	 * @param $content_type
	 *
	 * @return array|array[]
	 */
	public function get_template_options_list( $type = false, $content_type = false ) {
		$raw_templates = $this->get_raw_template_list();

		if ( empty( $raw_templates ) ) {
			return [];
		}

		if ( $type || $content_type ) {
			$raw_templates = array_filter( $raw_templates, function( $template_obj ) use ( $type, $content_type ) {
				$template_id           = $template_obj->ID;
				$template_type         = $this->get_template_type( $template_id );
				$template_content_type = $this->get_template_content_type( $template_id );

				$type_match = ! $type || $type === $template_type ? true : false;
				$content_type_match = ! $content_type || $content_type === $template_content_type ? true : false;

				return ( $type_match && $content_type_match ) ? true : false;
			} );
		}

		$options = array_map( function( $template_obj ) {
			return [
				'label' => $template_obj->post_title,
				'value' => $template_obj->ID,
			];
		}, $raw_templates );

		return $options;
	}

	/**
	 * @param false $template_id
	 * @param false $content_type
	 *
	 * @return false|mixed|string|null
	 */
	public function get_template_edit_link( $template_id = false, $content_type = false ) {

		if ( ! $template_id || ! $content_type ) {
			return false;
		}

		switch ( $content_type ) {
			case 'default':
				$edit_link = get_edit_post_link( $template_id, '' );
				break;
			case 'elementor':

				if ( \Jet_Theme_Core\Utils::has_elementor() ) {
					$edit_link = \Elementor\Plugin::$instance->documents->get( $template_id )->get_edit_url();
				} else {
					$edit_link = false;
				}

				break;
		}

		return $edit_link;
	}

	/**
	 * Disable metaboxes from Jet Templates
	 *
	 * @return void
	 */
	public function disable_metaboxes() {
		global $wp_meta_boxes;
		unset( $wp_meta_boxes[ $this->slug() ]['side']['core']['pageparentdiv'] );
	}

	/**
	 * Menu page
	 */
	public function add_templates_page() {
		add_submenu_page(
			'jet-dashboard',
			esc_html__( 'Theme Templates', 'jet-theme-core' ),
			esc_html__( 'Theme Templates', 'jet-theme-core' ),
			'edit_pages',
			'edit.php?post_type=' . $this->slug()
		);
	}

	/**
	 * Print library types tabs
	 *
	 * @return [type] [description]
	 */
	public function print_type_tabs( $edit_links ) {

		$tabs = jet_theme_core()->templates_api->get_library_types();
		$tabs = array_merge(
			array(
				'all' => esc_html__( 'All', 'jet-theme-core' ),
			),
			$tabs
		);

		$active_tab = isset( $_GET[ $this->type_tax ] ) ? $_GET[ $this->type_tax ] : 'all';
		$page_link  = admin_url( 'edit.php?post_type=' . $this->slug() );

		if ( ! array_key_exists( $active_tab, $tabs ) ) {
			$active_tab = 'all';
		}

		include jet_theme_core()->get_template( 'template-types-tabs.php' );

		return $edit_links;
	}

	/**
	 * Add an export link to the template library action links table list.
	 *
	 * @param array $actions
	 * @param object $post
	 *
	 * @return array
	 */
	public function post_row_actions( $actions, $post ) {

		if ( $this->post_type === $post->post_type ) {
			$actions['export-template'] = sprintf(
				'<a href="%1$s">%2$s</a>',
				$this->get_export_link( $post->ID ),
				esc_html__( 'Export Template', 'jet-theme-core' )
			);
		}

		return $actions;
	}

	/**
	 * Get template export link.
	 *
	 * @param int $template_id The template ID.
	 *
	 * @return string
	 */
	private function get_export_link( $template_id ) {
		return add_query_arg(
			array(
				'action'         => 'jet_theme_core_export_template',
				'template_id'    => $template_id,
			),
			esc_url( admin_url( 'admin-ajax.php' ) )
		);
	}

	/**
	 * Template type popup assets
	 *
	 * @return void
	 */
	public function template_type_form_assets() {

		$screen = get_current_screen();

		if ( $screen->id !== 'edit-' . $this->slug() ) {
			return;
		}

		$module_data = jet_theme_core()->module_loader->get_included_module_data( 'cherry-x-vue-ui.php' );
		$ui          = new \CX_Vue_UI( $module_data );
		$ui->enqueue_assets();

		wp_enqueue_style(
			'jet-theme-core-templates-library',
			jet_theme_core()->plugin_url( 'assets/css/templates-library.css' ),
			array(),
			jet_theme_core()->get_version()
		);

		wp_enqueue_script(
			'jet-theme-core-templates-library',
			jet_theme_core()->plugin_url( 'assets/js/templates-library.js' ), array(
				'jquery',
				'cx-vue-ui',
				'wp-api-fetch',
			),
			jet_theme_core()->get_version(),
			true
		);

		wp_localize_script(
			'jet-theme-core-templates-library',
			'JetThemeCoreTemplatesLibrary',
			array(
				'templateTypeOptions'          => jet_theme_core()->structures->get_template_type_options(),
				'templateContentTypeOptions'   => $this->get_template_content_type_options(),
				'createTemplatePath'           => 'jet-theme-core-api/v2/create-template',
				'getTemplateConditionsPath'    => 'jet-theme-core-api/v2/get-template-conditions',
				'updateTemplateConditionsPath' => 'jet-theme-core-api/v2/update-template-conditions',
				'rawConditionsData'            => jet_theme_core()->template_conditions_manager->get_conditions_raw_data(),
			)
		);

	}

	/**
	 * @return mixed|void
	 */
	public function get_template_content_type_options() {
		return apply_filters( 'jet-theme-core/templates/content-type-options', [
			[
				'label' => __( 'Block Editor', 'jet-theme-core' ),
				'value' => 'default',
			],
		] );
	}

	/**
	 * @return mixed|void
	 */
	public function get_template_content_type_icons() {
		return apply_filters( 'jet-theme-core/templates/content-type-icons', [
			'default' => '<svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" clip-rule="evenodd" d="M24 12C24 18.6274 18.6274 24 12 24C5.37258 24 0 18.6274 0 12C0 5.37258 5.37258 0 12 0C18.6274 0 24 5.37258 24 12ZM13.1079 0.927195L23.0728 10.8921C22.5528 5.63123 18.3688 1.44724 13.1079 0.927195ZM23.0728 13.1079C22.5528 18.3688 18.3688 22.5528 13.1079 23.0728L23.0728 13.1079ZM11.1313 23.0939L0.906137 12.8687C1.3272 18.3215 5.67854 22.6728 11.1313 23.0939ZM0.906137 11.1313C1.3272 5.67854 5.67854 1.3272 11.1313 0.906137L0.906137 11.1313ZM17.0621 10.5758C16.8846 10.4529 16.6433 10.5036 16.5226 10.6843C15.8199 11.7614 14.3294 11.8192 14.2513 11.8192H14.2158C12.3775 11.8192 11.6748 13.4168 11.6464 13.4819C11.5612 13.6843 11.6535 13.9156 11.8451 14.0023C11.8948 14.024 11.9516 14.0385 12.0013 14.0385C12.1503 14.0385 12.2923 13.9517 12.3562 13.7999L12.3571 13.798C12.3817 13.7446 12.8692 12.6853 14.088 12.6144V14.6602C14.0384 15.1011 13.8325 15.4481 13.4705 15.7084C13.0943 15.9758 12.5904 16.1132 11.9729 16.1132C11.2347 16.1132 10.6314 15.8529 10.1842 15.3397C9.72993 14.8264 9.5028 14.0963 9.5028 13.1566L9.5099 10.9011C9.54539 10.0698 9.76542 9.41198 10.1842 8.9421C10.6385 8.42885 11.2347 8.16861 11.9729 8.16861C12.5904 8.16861 13.0943 8.30596 13.4705 8.57343C13.8467 8.8409 14.0597 9.20957 14.0951 9.68668V9.73729C14.0951 10.012 14.3152 10.2361 14.5849 10.2361C14.8546 10.2361 15.0747 10.012 15.0747 9.73729V9.68668C15.0037 8.97102 14.6843 8.40717 14.1093 7.98066C13.5344 7.55415 12.8175 7.34451 11.9516 7.34451C10.9224 7.34451 10.0919 7.6915 9.46021 8.37825C8.86399 9.02162 8.55168 9.86741 8.51619 10.9084C8.51619 10.9445 8.51442 10.9806 8.51264 11.0168L8.51264 11.0168C8.51087 11.0529 8.50909 11.0891 8.50909 11.1252L8.51619 13.1566H8.50909C8.50909 14.306 8.8285 15.224 9.46021 15.9108C10.0919 16.5975 10.9224 16.9445 11.9516 16.9445C12.8175 16.9445 13.5344 16.7349 14.1093 16.3084C14.6346 15.918 14.9469 15.4048 15.0534 14.7686L15.0747 12.4987C15.7206 12.3397 16.6007 11.9783 17.1543 11.1252C17.2963 10.9445 17.2466 10.6987 17.0621 10.5758ZM12.1091 22.8374L1.27166 12L12.1091 1.16257L22.9465 12L12.1091 22.8374Z" fill="#23282D"/></svg>',
		] );
	}

	/**
	 * @return mixed|void
	 */
	public function get_template_source_type_details() {
		return apply_filters( 'jet-theme-core/templates/template-details', [
			'jet-theme-core' => [
				'icon'  => '<svg width="32" height="32" viewBox="0 0 32 32" fill="none" xmlns="http://www.w3.org/2000/svg"><rect width="32" height="32" rx="2" fill="#FACC15"/><path fill-rule="evenodd" clip-rule="evenodd" d="M27.6759 8.66748C28.0509 8.64436 28.2649 9.132 28.0157 9.44197L25.7979 12.2011C25.5382 12.5243 25.0541 12.3177 25.0592 11.8859L25.0724 10.7664C25.0741 10.6197 25.0132 10.4807 24.9078 10.3912L24.1037 9.70785C23.7936 9.44429 23.9472 8.89747 24.338 8.87336L27.6759 8.66748ZM10.0288 15.9132C10.0288 18.5563 7.86821 20.6982 5.20575 20.6982C4.53861 20.6982 4 20.1604 4 19.5012C4 18.8419 4.53861 18.3072 5.20575 18.3072C6.53698 18.3072 7.61726 17.2348 7.61726 15.9132V12.3252C7.61726 11.6629 8.15588 11.1282 8.82302 11.1282C9.49016 11.1282 10.0288 11.6629 10.0288 12.3252V15.9132ZM23.0289 15.9132C23.0289 17.2348 24.1092 18.3072 25.4404 18.3072C26.1075 18.3072 26.6462 18.8389 26.6462 19.5012C26.6462 20.1635 26.1075 20.6982 25.4404 20.6982C22.7779 20.6982 20.6174 18.5563 20.6174 15.9132V12.3252C20.6174 11.6629 21.156 11.1282 21.8231 11.1282C22.4903 11.1282 23.0289 11.666 23.0289 12.3252V13.3977H23.9868C24.6539 13.3977 25.1956 13.9354 25.1956 14.5977C25.1956 15.26 24.6539 15.7978 23.9868 15.7978H23.0289V15.9132ZM19.9258 14.2362C19.9288 14.2332 19.9319 14.2332 19.9319 14.2332C19.5524 13.2428 18.8424 12.3739 17.8509 11.8027C15.5496 10.4781 12.6117 11.2619 11.2866 13.5526C9.95842 15.8403 10.748 18.766 13.0463 20.0876C14.7386 21.0597 16.7767 20.8896 18.264 19.8202L18.2549 19.808C18.5976 19.5984 18.8241 19.2217 18.8241 18.7933C18.8241 18.1341 18.2855 17.5994 17.6214 17.5994C17.3001 17.5994 17.0063 17.7239 16.792 17.9305C16.0698 18.4136 15.1089 18.4895 14.2979 18.046L19.1546 15.8069C19.4361 15.7218 19.6871 15.5335 19.8432 15.26C20.0329 14.935 20.0513 14.5613 19.9258 14.2362ZM16.6482 13.8716C16.792 13.9537 16.9206 14.0478 17.0399 14.1511L13.0432 15.9892C13.034 15.5669 13.1381 15.1385 13.3645 14.7466C14.0286 13.6043 15.4975 13.2124 16.6482 13.8716Z" fill="white"/></svg>',
				'label' => __( 'Template builded by JetThemeCore', 'jet-theme-core' ),
			],
			'elementor-editor' => [
				'icon'  => '<svg width="32" height="32" viewBox="0 0 32 32" fill="none" xmlns="http://www.w3.org/2000/svg"><rect width="32" height="32" rx="2" fill="#92003B"/><path fill-rule="evenodd" clip-rule="evenodd" d="M9.28 4.8H4.8V27.2H9.28V4.8ZM27.2 18.2355V13.7555L13.76 13.7555V18.2355H27.2ZM27.2 4.8V9.28L13.76 9.28V4.8H27.2ZM27.2 27.1902V22.7102H13.76V27.1902H27.2Z" fill="white"/></svg>',
				'label' => __( 'Template builded by Elementor', 'jet-theme-core' ),
			],
			'block-editor' => [
				'icon'  => '<svg width="32" height="32" viewBox="0 0 32 32" fill="none" xmlns="http://www.w3.org/2000/svg"><rect width="32" height="32" rx="2" fill="black"/><path fill-rule="evenodd" clip-rule="evenodd" d="M28.8 16C28.8 23.0692 23.0692 28.8 16 28.8C8.93076 28.8 3.2 23.0692 3.2 16C3.2 8.93076 8.93076 3.2 16 3.2C23.0692 3.2 28.8 8.93076 28.8 16ZM17.1818 4.18901L27.811 14.8182C27.2563 9.20664 22.7934 4.74372 17.1818 4.18901ZM27.811 17.1818C27.2563 22.7934 22.7934 27.2563 17.1818 27.811L27.811 17.1818ZM15.0734 27.8335L4.16655 16.9266C4.61568 22.7429 9.25711 27.3843 15.0734 27.8335ZM4.16655 15.0734C4.61568 9.25711 9.25711 4.61568 15.0734 4.16655L4.16655 15.0734ZM21.3995 14.4809C21.2103 14.3498 20.9528 14.4038 20.8241 14.5966C20.0746 15.7455 18.4847 15.8072 18.4014 15.8072H18.3635C16.4026 15.8072 15.6531 17.5113 15.6228 17.5807C15.5319 17.7966 15.6304 18.0433 15.8348 18.1358C15.8878 18.159 15.9484 18.1744 16.0013 18.1744C16.1603 18.1744 16.3118 18.0819 16.3799 17.9199L16.3809 17.9179C16.4071 17.8609 16.9272 16.731 18.2273 16.6554V18.8375C18.1743 19.3079 17.9547 19.678 17.5686 19.9556C17.1673 20.2409 16.6298 20.3874 15.9711 20.3874C15.1837 20.3874 14.5401 20.1098 14.0631 19.5623C13.5786 19.0149 13.3363 18.2361 13.3363 17.2337L13.3439 14.8279C13.3817 13.9411 13.6165 13.2395 14.0631 12.7382C14.5477 12.1908 15.1837 11.9132 15.9711 11.9132C16.6298 11.9132 17.1673 12.0597 17.5686 12.345C17.9698 12.6303 18.197 13.0235 18.2348 13.5325V13.5864C18.2348 13.8794 18.4695 14.1185 18.7572 14.1185C19.0449 14.1185 19.2796 13.8794 19.2796 13.5864V13.5325C19.2039 12.7691 18.8632 12.1676 18.25 11.7127C17.6367 11.2578 16.872 11.0341 15.9483 11.0341C14.8505 11.0341 13.9647 11.4043 13.2909 12.1368C12.6549 12.8231 12.3218 13.7252 12.2839 14.8356C12.2839 14.8741 12.282 14.9127 12.2802 14.9512L12.2801 14.9513C12.2783 14.9898 12.2764 15.0284 12.2764 15.0669L12.2839 17.2337H12.2764C12.2764 18.4597 12.6171 19.439 13.2909 20.1715C13.9647 20.904 14.8505 21.2741 15.9483 21.2741C16.872 21.2741 17.6367 21.0505 18.25 20.5956C18.8102 20.1792 19.1434 19.6317 19.2569 18.9532L19.2796 16.532C19.9686 16.3623 20.9074 15.9768 21.498 15.0669C21.6494 14.8741 21.5964 14.612 21.3995 14.4809ZM16.1164 27.5599L4.55644 16L16.1164 4.44007L27.6763 16L16.1164 27.5599Z" fill="white"/></svg>',
				'label' => __( 'Template builded by Gutenberg', 'jet-theme-core' ),
			],
		] );
	}

	/**
	 * [print_vue_templates description]
	 * @return [type] [description]
	 */
	public function print_vue_templates() {

		$map = [
			'template-conditions-item',
			'template-conditions-manager',
		];

		foreach ( glob( jet_theme_core()->plugin_path( 'templates/admin/templates-library/' )  . '*.php' ) as $file ) {
			$name = basename( $file, '.php' );

			if ( ! in_array( $name,  $map )) {
				continue;
			}

			ob_start();
			include $file;
			printf( '<script type="x-template" id="tmpl-jet-theme-core-%1$s">%2$s</script>', $name, ob_get_clean() );
		}

	}

	/**
	 * Print template type form HTML
	 *
	 * @return void
	 */
	public function print_template_library() {
		$screen = get_current_screen();

		if ( $screen->id !== 'edit-' . $this->slug() ) {
			return;
		}

		include jet_theme_core()->get_template( 'admin/templates-library/templates-library.php' );
	}

	/**
	 * Constructor for the class
	 */
	public function __construct() {

		add_action( 'init', array( $this, 'register_post_types' ) );
		add_action( 'jet-theme-core/init', array( $this, 'init_components' ) );

		if ( is_admin() ) {
			add_action( 'admin_menu', array( $this, 'add_templates_page' ), 22 );
			add_filter( 'views_edit-' . $this->post_type, array( $this, 'print_type_tabs' ) );
			add_filter( 'manage_' . $this->slug() . '_posts_columns', array( $this, 'set_post_columns' ) );
			add_action( 'manage_' . $this->slug() . '_posts_custom_column', array( $this, 'post_columns' ), 10, 2 );
			add_action( 'add_meta_boxes_' . $this->slug(), array( $this, 'disable_metaboxes' ), 9999 );
			add_filter( 'post_row_actions', array( $this, 'post_row_actions' ), 10, 2 );

			add_action( 'admin_enqueue_scripts', array( $this, 'template_type_form_assets' ) );
			add_action( 'admin_footer', array( $this, 'print_vue_templates' ), 998 );
			add_action( 'admin_footer', array( $this, 'print_template_library' ), 999 );
		}

	}

}