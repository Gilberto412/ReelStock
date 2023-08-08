<?php
namespace Jet_Theme_Core;

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

class Utils {

	/**
	 * @return bool
	 */
	public static function has_elementor() {
		return defined( 'ELEMENTOR_VERSION' );
	}

	/**
	 * @return bool
	 */
	public static function has_elementor_pro() {
		return defined( 'ELEMENTOR_PRO_VERSION' );
	}

	/**
	 * [is_license_exist description]
	 * @return boolean [description]
	 */
	public static function get_theme_core_license() {
		return \Jet_Dashboard\Utils::get_plugin_license_key( 'jet-theme-core/jet-theme-core.php' );
	}

	/**
	 * [active_license_link description]
	 * @return [type] [description]
	 */
	public static function active_license_link() {
		return \Jet_Dashboard\Dashboard::get_instance()->get_dashboard_page_url( 'license-page' );
	}

	/**
	 * @return string
	 */
	public static function is_min_suffix() {
		return defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';
	}

	/**
	 * [search_terms_by_tax description]
	 * @param  [type] $tax   [description]
	 * @param  [type] $query [description]
	 * @return [type]        [description]
	 */
	public static function get_terms_by_tax( $tax, $query = '', $ids = [] ) {

		$terms = get_terms( [
			'taxonomy'   => $tax,
			'hide_empty' => false,
			'name__like' => $query,
			'include'    => $ids,
		] );

		$result = [];

		if ( empty( $ids ) || in_array( 'all', $ids ) ) {
			$result[] = [
				'value' => 'all',
				'label' => __( 'All', 'jet-theme-core' ),
			];
		}

		if ( ! empty( $terms ) ) {
			foreach ( $terms as $term ) {
				$result[] = [
					'value' => $term->term_id,
					'label' => $term->name,
				];
			}
		}

		return $result;

	}

	/**
	 * Get post types options list
	 *
	 * @return array
	 */
	public static function get_post_types() {

		$post_types = get_post_types( [ 'public' => true ], 'objects' );

		$deprecated = apply_filters(
			'jet-theme-core/post-types-list/deprecated',
			[
				'page',
				'jet-woo-builder',
				'e-landing-page',
				'jet-form-builder',
				'jet-engine',
				'jet-menu',
				'attachment',
				'elementor_library',
				'jet-theme-core',
			]
		);

		$result = [];

		if ( empty( $post_types ) ) {
			return $result;
		}

		foreach ( $post_types as $slug => $post_type ) {

			if ( in_array( $slug, $deprecated ) ) {
				continue;
			}

			$result[ $slug ] = $post_type->label;

		}

		return $result;

	}

	/**
	 * Get post types options list
	 *
	 * @return array
	 */
	public static function get_post_types_options() {

		$post_types = self::get_post_types();

		$result = [];

		if ( empty( $post_types ) ) {
			return $result;
		}

		foreach ( $post_types as $slug => $label ) {
			$result[] = [
				'label' => $label,
				'value' => $slug,
			];
		}

		return $result;

	}

	/**
	 * @return array
	 */
	public static function get_custom_post_types_options() {

		$deprecated = apply_filters(
			'jet-theme-core/custom-post-types-list/deprecated',
			[
				'post',
				'jet-menu',
			]
		);

		$post_types = self::get_post_types_options();

		return array_filter( $post_types, function ( $post_type ) use ( $deprecated ) {
			return ! in_array( $post_type['value'], $deprecated );
		} );

	}

	/**
	 * @param false $post_type
	 *
	 * @return array
	 */
	public static function get_taxonomies_by_post_type( $post_type = false ) {
		$taxonomies = get_object_taxonomies( $post_type, 'objects' );

		$post_type_taxonomies = wp_filter_object_list( $taxonomies, [
			'public'            => true,
			'show_in_nav_menus' => true,
		] );

		return $post_type_taxonomies;
	}

	/**
	 * @param $tax
	 * @param string $query
	 * @param array $ids
	 *
	 * @return array
	 */
	public static function get_terms_options_by_taxonomy( $tax, $query = '', $ids = [] ) {

		$terms = get_terms( [
			'taxonomy'   => $tax,
			'hide_empty' => false,
			'name__like' => $query,
			'include'    => $ids,
		] );

		$options = [];

		if ( empty( $ids ) || in_array( 'all', $ids ) ) {
			$options[] = [
				'value' => 'all',
				'label' => __( 'All', 'jet-theme-core' ),
			];
		}

		if ( ! empty( $terms ) ) {
			foreach ( $terms as $term ) {
				$options[] = [
					'label' => $term->name,
					'value' => $term->term_id,
				];
			}
		}

		return $options;

	}

	/**
	 * Returns all custom taxonomies
	 *
	 * @return [type] [description]
	 */
	public static function get_taxonomies() {

		$taxonomies = get_taxonomies( [
			'public'   => true,
			'_builtin' => false
		], 'objects' );

		$deprecated = apply_filters( 'jet-theme-core/taxonomies-list/deprecated', [] );

		$result = [];

		if ( empty( $taxonomies ) ) {
			return $result;
		}

		foreach ( $taxonomies as $slug => $tax ) {

			if ( in_array( $slug, $deprecated ) ) {
				continue;
			}

			$result[] = [
				'value' => $slug,
				'label' => $tax->label,
			];
		}

		return $result;
	}

	/**
	 * [search_posts_by_type description]
	 * @param  [type] $type  [description]
	 * @param  [type] $query [description]
	 * @param  array  $ids   [description]
	 * @return [type]        [description]
	 */
	public static function search_posts_by_type( $type, $query, $ids = array() ) {

		add_filter( 'posts_where', [ __CLASS__, 'force_search_by_title' ], 10, 2 );

		$posts = get_posts( [
			'post_type'           => $type,
			'ignore_sticky_posts' => true,
			'posts_per_page'      => -1,
			'suppress_filters'     => false,
			's_title'             => $query,
			'include'             => $ids,
		] );

		remove_filter( 'posts_where', [ __CLASS__, 'force_search_by_title' ], 10 );

		$result = [];

		if ( ! empty( $posts ) ) {
			foreach ( $posts as $post ) {
				$result[] = [
					'id'   => $post->ID,
					'text' => $post->post_title,
				];
			}
		}

		return $result;
	}

	/**
	 * Force query to look in post title while searching
	 * @return [type] [description]
	 */
	public static function force_search_by_title( $where, $query ) {

		$args = $query->query;

		if ( ! isset( $args['s_title'] ) ) {
			return $where;
		} else {
			global $wpdb;

			$searh = esc_sql( $wpdb->esc_like( $args['s_title'] ) );
			$where .= " AND {$wpdb->posts}.post_title LIKE '%$searh%'";

		}

		return $where;
	}

	/**
	 * [search_terms_by_tax description]
	 * @param  [type] $tax   [description]
	 * @param  [type] $query [description]
	 * @param  array  $ids   [description]
	 * @return [type]        [description]
	 */
	public static function search_terms_by_tax( $tax, $query = '', $ids = [] ) {

		$terms = get_terms( [
			'taxonomy'   => $tax,
			'hide_empty' => false,
			'name__like' => $query,
			'include'    => $ids,
		] );

		$result = [];

		if ( ! empty( $terms ) ) {
			foreach ( $terms as $term ) {
				$result[] = [
					'id'   => $term->term_id,
					'text' => $term->name,
				];
			}
		}

		return $result;

	}

	/**
	 * [search_posts_by_type description]
	 * @param  [type] $type  [description]
	 * @param  [type] $query [description]
	 * @return [type]        [description]
	 */
	public static function get_posts_by_type( $type, $query = '', $ids = [] ) {

		add_filter( 'posts_where', [ __CLASS__, 'force_search_by_title' ], 10, 2 );

		$posts = get_posts( [
			'post_type'           => $type,
			'ignore_sticky_posts' => true,
			'posts_per_page'      => -1,
			'suppress_filters'     => false,
			's_title'             => $query,
			'post_status'         => [ 'publish', 'private' ],
			'include'             => $ids,
		] );

		remove_filter( 'posts_where', array( __CLASS__, 'force_search_by_title' ), 10, 2 );

		$result = [];

		if ( empty( $ids ) || in_array( 'all', $ids ) ) {
			$result[] = [
				'value' => 'all',
				'label' => __( 'All', 'jet-theme-core' ),
			];
		}

		if ( ! empty( $posts ) ) {
			foreach ( $posts as $post ) {
				$result[] = [
					'value' => $post->ID,
					'label' => $post->post_title,
				];
			}
		}

		return $result;
	}

	/**
	 * @param string $post_type
	 * @param string $taxonomy
	 *
	 * @return array
	 */
	public static function get_terms_by_post_type( $post_type = 'post', $taxonomy = 'category' ) {

		$get_all_posts = get_posts( [
			'post_type'     => esc_attr( $post_type ),
			'post_status'   => 'publish',
			'numberposts'   => -1
		] );

		if ( ! empty( $get_all_posts ) ) {

			$post_terms = [];

			foreach( $get_all_posts as $all_posts ){
				$post_terms[] = get_the_terms( $all_posts->ID, esc_attr( $taxonomy ) );
			}

			$post_terms_array = [];

			foreach( $post_terms as $terms ) {
				if ( ! empty( $terms ) ) {
					foreach( $terms as $term ) {
						$post_terms_array[] = [
							'label' => $term->name,
							'value' => $term->term_id,
						];
					}
				}

			}
			$terms = array_unique( $post_terms_array, SORT_REGULAR );

			return $terms;

		}

	}


	/**
	 * Gets a value from a nested array using an address string.
	 *
	 * @param array  $array   An array which contains value located at `$address`.
	 * @param string|array $address The location of the value within `$array` (dot notation).
	 * @param mixed  $default Value to return if not found. Default is an empty string.
	 *
	 * @return mixed The value, if found, otherwise $default.
	 */
	public static function array_get( $array, $address, $default = '' ) {
		$keys   = is_array( $address ) ? $address : explode( '.', $address );
		$value  = $array;

		foreach ( $keys as $key ) {
			if ( ! empty( $key ) && isset( $key[0] ) && '[' === $key[0] ) {
				$index = substr( $key, 1, -1 );

				if ( is_numeric( $index ) ) {
					$key = (int) $index;
				}
			}

			if ( ! isset( $value[ $key ] ) ) {
				return $default;
			}

			$value = $value[ $key ];
		}

		return $value;
	}

	/**
	 * @return string
	 */
	public static function get_theme_bilder_link() {
		return add_query_arg( [
			'page'          => 'jet-theme-builder',
		], admin_url( 'admin.php' ) );
	}

	/**
	 * @param false $icon
	 *
	 * @return false|string
	 */
	public static function get_admin_ui_icon( $icon = false ) {

		if ( ! $icon ) {
			return false;
		}

		$ui_icons = [
			'warning' => '<svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M4.47 21H19.53C21.07 21 22.03 19.33 21.26 18L13.73 4.99C12.96 3.66 11.04 3.66 10.27 4.99L2.74 18C1.97 19.33 2.93 21 4.47 21ZM12 14C11.45 14 11 13.55 11 13V11C11 10.45 11.45 10 12 10C12.55 10 13 10.45 13 11V13C13 13.55 12.55 14 12 14ZM13 18H11V16H13V18Z" fill="#fcb92c"/></svg>',
			'edit'    => '<svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M2.5 14.3751V17.5001H5.625L14.8417 8.28346L11.7167 5.15846L2.5 14.3751ZM17.2583 5.8668C17.5833 5.5418 17.5833 5.0168 17.2583 4.6918L15.3083 2.7418C14.9833 2.4168 14.4583 2.4168 14.1333 2.7418L12.6083 4.2668L15.7333 7.3918L17.2583 5.8668Z" fill="#007CBA"></path></svg>',
			'plus'    => '<svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M15.8332 10.8337H10.8332V15.8337H9.1665V10.8337H4.1665V9.16699H9.1665V4.16699H10.8332V9.16699H15.8332V10.8337Z" fill="#007CBA"></path></svg>',
			'info'    => '<svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M9.99984 1.66675C5.39984 1.66675 1.6665 5.40008 1.6665 10.0001C1.6665 14.6001 5.39984 18.3334 9.99984 18.3334C14.5998 18.3334 18.3332 14.6001 18.3332 10.0001C18.3332 5.40008 14.5998 1.66675 9.99984 1.66675ZM10.8332 14.1667H9.1665V9.16675H10.8332V14.1667ZM10.8332 7.50008H9.1665V5.83341H10.8332V7.50008Z" fill="#2271B1"/></svg>',
		];

		if ( isset( $ui_icons[ $icon ] ) ) {
			return $ui_icons[ $icon ];
		}

		return false;
	}

}
