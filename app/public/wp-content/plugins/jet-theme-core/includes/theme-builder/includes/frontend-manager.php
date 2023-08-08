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

class Frontend_Manager {

	/**
	 * A reference to an instance of this class.
	 *
	 * @since  1.0.0
	 * @access private
	 * @var    Jet_Theme_Core
	 */
	private static $instance = null;

	/**
	 * @var array
	 */
	public $all_site_conditions = [];

	/**
	 * @var array
	 */
	public $matched_page_template_layout = [];

	/**
	 * @var bool
	 */
	public $is_theme_builder_render = false;

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
	 * @return bool
	 */
	public function is_theme_builder_enabled() {
		return true;
	}

	/**
	 * @return array|false
	 */
	public function add_body_classes( $classes ) {

		$layout = $this->get_matched_page_template_layouts();

		if ( ! $layout ) {
			return $classes;
		}

		$classes[] = 'jet-theme-core';

		if ( ! empty( $layout ) ) {
			$classes[] = 'jet-theme-core--has-template';

			if ( $layout['header']['override'] ) {
				$classes[] = 'jet-theme-core--has-header';
			}

			if ( $layout['body']['override'] ) {
				$classes[] = 'jet-theme-core--has-body';
			}

			if ( $layout['footer']['override'] ) {
				$classes[] = 'jet-theme-core--has-footer';
			}
		}

		return $classes;
	}

	/**
	 * @param $template
	 *
	 * @return mixed
	 */
	public function frontend_override_template( $template ) {
		$layout = $this->get_matched_page_template_layouts();

		if ( empty( $layout ) ) {
			return $template;
		}

		$override_header   = $layout['header']['override'] && $layout['header']['id'] ? true : false;
		$override_body     = $layout['body']['override'] && $layout['body']['id'] ? true : false;
		$override_footer   = $layout['footer']['override'] && $layout['footer']['id'] ? true : false;
		$page_template     = locate_template( 'page.php' );

		if ( $override_header ) {
			// wp-version >= 5.2
			remove_action( 'wp_body_open', 'wp_admin_bar_render', 0 );

			add_action( 'get_header', [ $this, 'get_override_header' ] );
		}

		if ( $override_footer ) {
			add_action( 'get_footer', [ $this, 'get_override_footer' ] );
		}

		if ( $override_body ) {
			return jet_theme_core()->plugin_path( 'includes/theme-builder/templates/frontend-body-template.php' );
		}

		if ( $override_body && !is_home() ) {
			return $page_template;
		}

		return $template;
	}

	/**
	 * @param $name
	 */
	function get_override_header( $name ) {
		$this->get_override_partial( 'header', $name, 'wp_head' );
	}

	/**
	 * @param $name
	 */
	function get_override_footer( $name ) {
		$this->get_override_partial( 'footer', $name, 'wp_footer' );
	}

	/**
	 * @param $partial
	 * @param $name
	 * @param string $action
	 */
	function get_override_partial( $partial, $name, $action = '' ) {
		global $wp_filter;

		$jet_theme_core_theme_head = '';

		/**
		 * Slightly adjusted version of WordPress core code in order to mimic behavior.
		 *
		 * @link https://core.trac.wordpress.org/browser/tags/5.0.3/src/wp-includes/general-template.php#L33
		 */
		$templates = array();
		$name      = (string) $name;

		if ( '' !== $name ) {
			$templates[] = "{$partial}-{$name}.php";
		}
		$templates[] = "{$partial}.php";

		// Buffer and discard the original partial forcing a require_once so it doesn't load again later.
		$buffered = ob_start();

		if ( $buffered ) {
			$actions = [];

			if ( ! empty( $action ) ) {
				// Skip any partial-specific actions so they don't run twice.
				$actions = \Jet_Theme_Core\Utils::array_get( $wp_filter, $action, [] );

				unset( $wp_filter[ $action ] );
			}

			locate_template( $templates, true, true );
			$html = ob_get_clean();

			if ( 'wp_head' === $action ) {
				$jet_theme_core_theme_head = $this->extract_head( $html );
			}

			if ( ! empty( $action ) ) {
				// Restore skipped actions.
				$wp_filter[ $action ] = $actions;
			}
		}

		require_once jet_theme_core()->plugin_path( "includes/theme-builder/templates/frontend-{$partial}-template.php" );
	}

	/**
	 * Extract <head> tag contents.
	 *
	 * @since 4.0.8
	 *
	 * @param string $html
	 *
	 * @return string
	 */
	function extract_head( $html ) {
		// We could use DOMDocument here to guarantee proper parsing but we need
		// the most performant solution since we cannot reliably cache the result.
		$head = array();
		preg_match( '/^[\s\S]*?<head[\s\S]*?>([\s\S]*?)<\/head>[\s\S]*$/i', $html, $head );

		return ! empty( $head[1] ) ? trim( $head[1] ) : '';
	}

	/**
	 * @param false $template_id
	 *
	 * @return false
	 */
	public function render_location( $template_id = false ) {

		if ( ! $template_id ) {
			return false;
		}

		$template_type = apply_filters( 'jet-theme-core/theme-builder/frontend/render-location/template-type',
			jet_theme_core()->templates->get_template_type( $template_id ),
			$template_id
		);

		$content_type = apply_filters( 'jet-theme-core/theme-builder/frontend/render-location/template-content-type',
			jet_theme_core()->templates->get_template_content_type( $template_id ),
			$template_id,
			$template_type
		);

		$structure = jet_theme_core()->structures->get_structure( $template_type );

		if ( ! $structure ) {
			return false;
		}

		$location = $structure->location_name();

		do_action( 'jet-theme-core/theme-builder/render/location/before', $location, $template_id, $content_type );

		switch ( $content_type ) {
			case 'elementor':
				$location_render = new \Jet_Theme_Core\Locations\Render\Elementor_Location_Render( [
					'template_id' => $template_id,
					'location'    => $location,
				] );
			break;
			default:
				$location_render = new \Jet_Theme_Core\Locations\Render\Block_Editor_Render( [
					'template_id' => $template_id,
					'location'    => $location,
				] );
			break;
		}

		do_action( 'jet-theme-core/theme-builder/render/location/after', $location, $template_id, $content_type );

		$buffered = ob_start();

		$structure->before_render();

		$location_render->render();

		$structure->after_render();

		$location_html = ob_get_clean();

		do_action( "jet-theme-core/theme-builder/render/{$location}-location/before", $template_id, $content_type, $location_html );

		switch ( $location ) {
			case 'header':
				$this->render_location_html( $location, 'header', $location_html );
				break;
			case 'footer':
				$this->render_location_html( $location, 'footer', $location_html );
				break;
			case 'page':
			case 'single':
				$this->render_location_html( $location, 'main', $location_html );
				break;
			case 'archive':
			case 'products-archive':
			case 'products-card':
			case 'account-page':
			case 'products-checkout-endpoint':
				$this->render_location_html( $location, 'main', $location_html );
				break;
			case 'products-checkout':
				$this->render_products_checkout_location_html( $location, 'main', $location_html );
				break;
			case 'single-product':
				$this->render_single_product_location_html( $location, 'main', $location_html );
				break;
		}

		do_action( "jet-theme-core/theme-builder/render/{$location}-location/after", $template_id, $content_type );
	}

	/**
	 * @param false $location_html
	 *
	 * @return false
	 */
	public function render_location_html( $location = false, $container_tag = 'div', $location_html = false ) {

		if ( ! $location || ! $location_html ) {
			return false;
		}

		echo sprintf( '<%1$s id="jet-theme-core-%2$s" class="jet-theme-core-location jet-theme-core-location--%2$s-location"><div class="jet-theme-core-location__inner">%3$s</div></%1$s>',
			$container_tag,
			$location,
			$location_html
		);
	}

	/**
	 * @param false $location_html
	 *
	 * @return false
	 */
	public function render_products_checkout_location_html( $location = false, $container_tag = 'div', $location_html = false ) {

		if ( ! $location || ! $location_html ) {
			return false;
		}

		$location_html = sprintf( '<form name="checkout" method="post" class="checkout woocommerce-checkout" action="%1$s" enctype="multipart/form-data">%2$s</form>', esc_url( wc_get_checkout_url() ), $location_html );

		echo sprintf( '<%1$s id="jet-theme-core-%2$s" class="jet-theme-core-location jet-theme-core-location--%2$s-location"><div class="jet-theme-core-location__inner">%3$s</div></%1$s>', $container_tag, $location, $location_html );
	}

	/**
	 * @param false $location_html
	 *
	 * @return false
	 */
	public function render_single_product_location_html( $location = false, $container_tag = 'div', $location_html = false ) {

		global $product;

		if ( ! $location || ! $location_html || empty( $product ) ) {
			return false;
		}

		ob_start();
		wc_product_class( '', $product );
		$wc_product_class = ob_get_clean();

		$location_html = sprintf( '<div id="product-%1$s" %2$s>%3$s</div>', $product->get_id(), $wc_product_class , $location_html );

		echo sprintf( '<%1$s id="jet-theme-core-%2$s" class="jet-theme-core-location jet-theme-core-location--%2$s-location"><div class="jet-theme-core-location__inner">%3$s</div></%1$s>', $container_tag, $location, $location_html );
	}


	/**
	 * @param $template_id
	 * @param $content_type
	 */
	public function render_document_open_wrapper( $template_id, $content_type ) {

		$layout = $this->get_matched_page_template_layouts();

		if ( ! $layout ) {
			return false;
		}

		$override_header = $this->is_layout_structure_override( 'header' );
		$override_footer = $this->is_layout_structure_override( 'footer' );

		if ( ! $override_header || ! $override_footer ) {
			return false;
		}

		do_action( 'jet-theme-core/theme-builder/render/open-document-wrapper/before', $template_id, $content_type );

		echo sprintf( '<div id="jet-theme-core-document" class="jet-theme-core-document jet-theme-core-document--%1$s-content-type"><div class="jet-theme-core-document__inner">',
			$content_type
		);

		do_action( 'jet-theme-core/theme-builder/render/open-document-wrapper/after', $template_id, $content_type );
	}

	/**
	 * @param $template_id
	 * @param $content_type
	 */
	public function render_document_close_wrapper( $template_id, $content_type ) {

		$layout = $this->get_matched_page_template_layouts();

		if ( ! $layout ) {
			return false;
		}

		$override_header = $this->is_layout_structure_override( 'header' );
		$override_footer = $this->is_layout_structure_override( 'footer' );

		if ( ! $override_header || ! $override_footer ) {
			return false;
		}

		do_action( 'jet-theme-core/theme-builder/render/close-document-wrapper/before', $template_id, $content_type );

		echo '</div></div>';

		do_action( 'jet-theme-core/theme-builder/render/close-document-wrapper/before', $template_id, $content_type );
	}

	/**
	 * @param false $single
	 *
	 * @return false
	 */
	public function get_matched_page_template_layouts( $single = false ) {

		if ( ! $this->is_theme_builder_enabled() ) {
			return false;
		}

		if ( ! empty( $this->matched_page_template_layout ) ) {
			return $this->matched_page_template_layout;
		}

		$matched_page_template_conditions = $this->get_matched_page_template_conditions();

		if ( ! $matched_page_template_conditions ) {
			return false;
		}

		uasort($matched_page_template_conditions, function ( $templateA, $templateB ) {
			$priorityA = $templateA[0]['priority'];
			$priorityB = $templateB[0]['priority'];
			$includeB = $templateB[0]['include'];

			if ( ! filter_var( $includeB, FILTER_VALIDATE_BOOLEAN ) ) {
				return -1;
			}

			if ( $priorityA == $priorityB ) {
				return 0;
			}

			return ( $priorityA < $priorityB ) ? -1 : 1;
		} );

		if ( ! $matched_page_template_conditions || empty( $matched_page_template_conditions ) ) {
			return false;
		}

		$page_template_id = array_key_first( $matched_page_template_conditions );

		$page_template_layout = get_post_meta( $page_template_id, '_layout', true );

		$this->matched_page_template_layout = apply_filters( 'jet-theme-core/theme-builder/matched-page-template-layout', $page_template_layout );

		return $this->matched_page_template_layout;

	}

	/**
	 * @param false $structure
	 *
	 * @return bool
	 */
	public function is_layout_structure_override( $structure = false ) {
		$layout = $this->get_matched_page_template_layouts();

		if ( ! $layout || ! isset( $layout[ $structure ] ) ) {
			return false;
		}

		return $layout[ $structure ]['override'] && $layout[ $structure ]['id'] ? true : false;
	}

	/**
	 * @param false $single
	 *
	 * @return array|false|mixed
	 */
	public function get_matched_page_template_conditions( $single = false ) {

		$all_site_conditions = jet_theme_core()->theme_builder->page_templates_manager->get_site_page_template_conditions();

		$page_template_id_list = [];

		foreach ( $all_site_conditions as $page_template_id => $page_template_condition_data ) {

			$page_template_conditions = $page_template_condition_data['conditions'];
			$page_template_relation_type = $page_template_condition_data['relation_type'];

			if ( empty( $page_template_conditions ) ) {
				continue;
			}

			$check_list = [];

			// for multi-language plugins
			$page_template_id = apply_filters( 'jet-theme-core/page-template-conditions/page-template-id', $page_template_id );

			$template_conditions = array_map( function( $condition ) use ( $page_template_id ) {

				$include = filter_var( $condition['include'], FILTER_VALIDATE_BOOLEAN );

				if ( 'entire' === $condition['group'] ) {
					$match = 'entire' === $condition['group'] ? true : false;
					$condition['match'] = $match;

					return $condition;
				} else {
					$sub_group = $condition['subGroup'];

					$instance = jet_theme_core()->template_conditions_manager->get_condition( $sub_group );

					if ( ! $instance ) {
						$condition['match'] = false;

						return $condition;
					}

					$sub_group_value = isset( $condition['subGroupValue'] ) ? $condition['subGroupValue'] : '';

					$instance_check = call_user_func( array( $instance, 'check' ), $sub_group_value, $sub_group );

					$condition['match'] = $instance_check;

					return $condition;

				}

				return $condition;

			}, $page_template_conditions );

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

			if ( 'and' === $page_template_relation_type ) {
				// 'and' check
				// include only we have at least 1 include condition and if all included conditions are met (no failed conditions)
				$is_included = ( ! empty( $includes_matchs ) && ! in_array( false, $includes_matchs ) ) ? true : false;
				// exclude if we have at least 1 exclude condition and all exclude condition are met
				$is_excluded = ( ! empty( $excludes_matchs ) && ! in_array( false, $excludes_matchs ) ) ? true : false;
			} else {
				// 'or' check
				// include if we have at least 1 include condition and if at least 1 include condition are met
				$is_included = ( ! empty( $includes_matchs ) && in_array( true, $includes_matchs ) ) ? true : false;
				// exclude if we have at least 1 exclude condition and if at least 1 exclude condition are met
				$is_excluded = ( ! empty( $excludes_matchs ) && in_array( true, $excludes_matchs ) ) ? true : false;
			}

			// final check - this template are valid only if its included and not excluded at the same time.
			// this relation potentially also could be controlled by option
			if ( $is_included && ! $is_excluded ) {
				$page_template_id_list[ $page_template_id ] = $page_template_conditions;
			}
		}

		if ( ! empty( $page_template_id_list ) ) {

			if ( $single ) {
				$first_key = array_key_first( $page_template_id_list );

				return $page_template_id_list[ $first_key ];
			}

			return $page_template_id_list;
		}

		return false;
	}

	/**
	 * Load admin assets
	 *
	 * @param  string $hook Current page hook.
	 * @return void
	 */
	public function register_frontend_styles() {
		wp_register_style(
			'jet-theme-core-frontend-styles',
			jet_theme_core()->plugin_url( 'assets/css/frontend.css' ),
			apply_filters( 'jet-theme-core/assets/public-styles-dependencies', [] ),
			JET_THEME_CORE_VERSION
		);
	}

	/**
	 * @return void
	 */
	public function enqueue_frontend_assets() {
		wp_enqueue_style( 'jet-theme-core-frontend-styles' );
	}

	/**
	 * Constructor for the class
	 */
	public function __construct() {
		add_filter( 'body_class', [ $this, 'add_body_classes' ], 9 );

		// Priority of 98 so it can be overridden by BFB.
		add_filter( 'template_include', [ $this, 'frontend_override_template' ], 98 );

		add_action( 'jet-theme-core/theme-builder/render/header-location/before', [ $this, 'render_document_open_wrapper' ], 10, 3 );

		add_action( 'jet-theme-core/theme-builder/render/footer-location/after', [ $this, 'render_document_close_wrapper' ], 10, 3 );

		add_action( 'wp_enqueue_scripts', [ $this, 'register_frontend_styles' ], 9 );

		add_action( 'wp_enqueue_scripts', [ $this, 'enqueue_frontend_assets' ], 10 );

	}
}
