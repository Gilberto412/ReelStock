<?php
/**
 * Cherry addons tools class
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

if ( ! class_exists( 'Jet_Reviews_Tools' ) ) {

	/**
	 * Define Jet_Reviews_Tools class
	 */
	class Jet_Reviews_Tools {

		/**
		 * A reference to an instance of this class.
		 *
		 * @since 1.0.0
		 * @var   object
		 */
		private static $instance = null;

		/**
		 * Get post types options list
		 *
		 * @return array
		 */
		public function get_post_types() {

			$post_types = get_post_types( array( 'public' => true ), 'objects' );

			$deprecated = apply_filters(
				'jet-reviews/post-types-list/deprecated',
				array(
					'attachment',
					'elementor_library',
					'jet-theme-core',
					'jet-menu',
					'jet-engine',
					'jet-engine-booking',
					'jet-popup',
					'jet-smart-filters',
					'jet-woo-builder',
				)
			);

			$result = array();

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
		 * [get_post_types_options description]
		 * @return [type] [description]
		 */
		public function get_post_types_options() {

			$post_types = $this->get_post_types();

			$post_types_options = array(
				array(
					'label' => esc_html__( 'All', 'jet-reviews' ),
					'value' => '',
				)
			);

			foreach ( $post_types as $slug => $name ) {
				$post_types_options[] = array(
					'label' => $name,
					'value' => $slug,
				);
			}

			return $post_types_options;
		}

		/**
		 * Get categories list.
		 *
		 * @return array
		 */
		public function get_categories() {

			$categories = get_categories();

			if ( empty( $categories ) || ! is_array( $categories ) ) {
				return array();
			}

			return wp_list_pluck( $categories, 'name', 'term_id' );

		}

		/**
		 * [search_posts_by_type description]
		 * @param  [type] $type  [description]
		 * @param  [type] $query [description]
		 * @return [type]        [description]
		 */
		public function get_posts_by_type( $type = false, $query = '' ) {

			add_filter( 'posts_where', [ $this, 'force_search_by_title' ], 10, 2 );

			$posts = get_posts( [
				'post_type'           => $type,
				'ignore_sticky_posts' => true,
				'posts_per_page'      => -1,
				'suppress_filters'     => false,
				's_title'             => $query,
				'post_status'         => [ 'publish', 'private' ],
			] );

			remove_filter( 'posts_where', array( $this, 'force_search_by_title' ), 10, 2 );

			$result = [
				[
					'value' => 'all',
					'label' => __( 'All', 'jet-reviews' ),
				]
			];

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
		 * Force query to look in post title while searching
		 * @return [type] [description]
		 */
		public function force_search_by_title( $where, $query ) {

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
		 * [get_content_allowed_html description]
		 * @return [type] [description]
		 */
		public function get_content_allowed_html() {
			return array(
				'a' => array(
					'href'  => true,
					'title' => true,
				),
				'br'     => array(),
				'em'     => array(),
				'strong' => array()
			);
		}

		/**
		 * [get_editable_roles description]
		 * @return [type] [description]
		 */
		public function get_roles_options() {
			global $wp_roles;

			$all_roles = $wp_roles->roles;

			$roles_options = array();

			foreach ( $all_roles as $role_slug => $role_data ) {
				$roles_options[] = array(
					'label' => $role_data['name'],
					'value' => $role_slug,
				);
			}

			$roles_options[] = array(
				'label' => esc_html__( 'Guest', 'jet-reviews' ),
				'value' => 'guest',
			);

			return $roles_options;
		}

		/**
		 * [human_time_diff_by_date description]
		 * @param  boolean $date [description]
		 * @return [type]        [description]
		 */
		public function human_time_diff_by_date( $date = false ) {

			if ( ! $date ) {
				return false;
			}

			$date_time = strtotime( $date );

			$after = esc_html__( ' ago', 'jet-reviews' );

			return human_time_diff( $date_time, current_time( 'timestamp' ) ) . $after;
		}

		/**
		 * [get_post_review_type_data description]
		 * @return [type] [description]
		 */
		public function get_post_review_type_data() {

			$post_type = get_post_type( get_the_ID() );

			$post_type_data = jet_reviews()->settings->get_post_type_data( $post_type );

			$is_exist = jet_reviews()->reviews_manager->data->is_review_type_exist( $post_type_data['review_type'] );

			if ( $is_exist ) {
				$review_type = $post_type_data['review_type'];
			} else {
				$review_type = 'default';
			}

			$review_type_data = \Jet_Reviews\Reviews\Data::get_instance()->get_review_type( $post_type_data['review_type'] );

			if ( ! empty( $review_type_data ) ) {

				$review_type_data = $review_type_data[0];

				$review_type_data['fields'] = maybe_unserialize( $review_type_data['fields'] );

				return $review_type_data;
			}

			return false;
		}

		/**
		 * Returns columns classes string
		 * @param  [type] $columns [description]
		 * @return [type]          [description]
		 */
		public function col_classes( $columns = array() ) {

			$columns = wp_parse_args( $columns, array(
				'desk' => 1,
				'tab'  => 1,
				'mob'  => 1,
			) );

			$classes = array();

			foreach ( $columns as $device => $cols ) {
				if ( ! empty( $cols ) ) {
					$classes[] = sprintf( 'col-%1$s-%2$s', $device, $cols );
				}
			}

			return implode( ' ' , $classes );
		}

		/**
		 * Returns disable columns gap nad rows gap classes string
		 *
		 * @param  string $use_cols_gap [description]
		 * @param  string $use_rows_gap [description]
		 * @return [type]               [description]
		 */
		public function gap_classes( $use_cols_gap = 'yes', $use_rows_gap = 'yes' ) {

			$result = array();

			foreach ( array( 'cols' => $use_cols_gap, 'rows' => $use_rows_gap ) as $element => $value ) {
				if ( 'yes' !== $value ) {
					$result[] = sprintf( 'disable-%s-gap', $element );
				}
			}

			return implode( ' ', $result );

		}

		/**
		 * Returns image size array in slug => name format
		 *
		 * @return  array
		 */
		public function get_image_sizes() {

			global $_wp_additional_image_sizes;

			$sizes  = get_intermediate_image_sizes();
			$result = array();

			foreach ( $sizes as $size ) {
				if ( in_array( $size, array( 'thumbnail', 'medium', 'medium_large', 'large' ) ) ) {
					$result[ $size ] = ucwords( trim( str_replace( array( '-', '_' ), array( ' ', ' ' ), $size ) ) );
				} else {
					$result[ $size ] = sprintf(
						'%1$s (%2$sx%3$s)',
						ucwords( trim( str_replace( array( '-', '_' ), array( ' ', ' ' ), $size ) ) ),
						$_wp_additional_image_sizes[ $size ]['width'],
						$_wp_additional_image_sizes[ $size ]['height']
					);
				}
			}

			return array_merge( array( 'full' => esc_html__( 'Full', 'jet-reviews' ), ), $result );
		}

		/**
		 * Returns icons data list.
		 *
		 * @return array
		 */
		public function get_theme_icons_data() {

			$default = array(
				'icons'  => false,
				'format' => 'fa %s',
				'file'   => false,
			);

			/**
			 * Filter default icon data before useing
			 *
			 * @var array
			 */
			$icon_data = apply_filters( 'jet-reviews/controls/icon/data', $default );
			$icon_data = array_merge( $default, $icon_data );

			return $icon_data;
		}

		/**
		 * Returns allowed order by fields for options
		 *
		 * @return array
		 */
		public function orderby_arr() {
			return array(
				'none'          => esc_html__( 'None', 'jet-reviews' ),
				'ID'            => esc_html__( 'ID', 'jet-reviews' ),
				'author'        => esc_html__( 'Author', 'jet-reviews' ),
				'title'         => esc_html__( 'Title', 'jet-reviews' ),
				'name'          => esc_html__( 'Name (slug)', 'jet-reviews' ),
				'date'          => esc_html__( 'Date', 'jet-reviews' ),
				'modified'       => esc_html__( 'Modified', 'jet-reviews' ),
				'rand'          => esc_html__( 'Rand', 'jet-reviews' ),
				'comment_count' => esc_html__( 'Comment Count', 'jet-reviews' ),
				'menu_order'    => esc_html__( 'Menu Order', 'jet-reviews' ),
			);
		}

		/**
		 * Returns allowed order fields for options
		 *
		 * @return array
		 */
		public function order_arr() {
			return array(
				'desc' => esc_html__( 'Descending', 'jet-reviews' ),
				'asc'  => esc_html__( 'Ascending', 'jet-reviews' ),
			);
		}

		/**
		 * Returns allowed order by fields for options
		 *
		 * @return array
		 */
		public function verrtical_align_attr() {
			return array(
				'baseline'    => esc_html__( 'Baseline', 'jet-reviews' ),
				'top'         => esc_html__( 'Top', 'jet-reviews' ),
				'middle'      => esc_html__( 'Middle', 'jet-reviews' ),
				'bottom'      => esc_html__( 'Bottom', 'jet-reviews' ),
				'sub'         => esc_html__( 'Sub', 'jet-reviews' ),
				'super'       => esc_html__( 'Super', 'jet-reviews' ),
				'text-top'    => esc_html__( 'Text Top', 'jet-reviews' ),
				'text-bottom' => esc_html__( 'Text Bottom', 'jet-reviews' ),
			);
		}

		/**
		 * Returns array with numbers in $index => $name format for numeric selects
		 *
		 * @param  integer $to Max numbers
		 * @return array
		 */
		public function get_select_range( $to = 10 ) {
			$range = range( 1, $to );

			return array_combine( $range, $range );
		}

		/**
		 * Returns badge placeholder URL
		 *
		 * @return void
		 */
		public function get_badge_placeholder() {
			return jet_reviews()->plugin_url( 'assets/images/placeholder-badge.svg' );
		}

		/**
		 * Returns carousel arrow
		 *
		 * @param  array $classes Arrow additional classes list.
		 * @return string
		 */
		public function get_carousel_arrow( $classes ) {

			$format = apply_filters( 'jet_reviews/carousel/arrows_format', '<i class="%s jet-arrow"></i>', $classes );

			return sprintf( $format, implode( ' ', $classes ) );
		}

		/**
		 * Return availbale arrows list
		 * @return [type] [description]
		 */
		public function get_available_prev_arrows_list() {

			return apply_filters(
				'jet_reviews/carousel/available_arrows/prev',
				array(
					'fa fa-angle-left'          => __( 'Angle', 'jet-reviews' ),
					'fa fa-chevron-left'        => __( 'Chevron', 'jet-reviews' ),
					'fa fa-angle-double-left'   => __( 'Angle Double', 'jet-reviews' ),
					'fa fa-arrow-left'          => __( 'Arrow', 'jet-reviews' ),
					'fa fa-caret-left'          => __( 'Caret', 'jet-reviews' ),
					'fa fa-long-arrow-left'     => __( 'Long Arrow', 'jet-reviews' ),
					'fa fa-arrow-circle-left'   => __( 'Arrow Circle', 'jet-reviews' ),
					'fa fa-chevron-circle-left' => __( 'Chevron Circle', 'jet-reviews' ),
					'fa fa-caret-square-o-left' => __( 'Caret Square', 'jet-reviews' ),
				)
			);

		}

		/**
		 * Return availbale arrows list
		 * @return [type] [description]
		 */
		public function get_available_next_arrows_list() {

			return apply_filters(
				'jet_reviews/carousel/available_arrows/next',
				array(
					'fa fa-angle-right'          => __( 'Angle', 'jet-reviews' ),
					'fa fa-chevron-right'        => __( 'Chevron', 'jet-reviews' ),
					'fa fa-angle-double-right'   => __( 'Angle Double', 'jet-reviews' ),
					'fa fa-arrow-right'          => __( 'Arrow', 'jet-reviews' ),
					'fa fa-caret-right'          => __( 'Caret', 'jet-reviews' ),
					'fa fa-long-arrow-right'     => __( 'Long Arrow', 'jet-reviews' ),
					'fa fa-arrow-circle-right'   => __( 'Arrow Circle', 'jet-reviews' ),
					'fa fa-chevron-circle-right' => __( 'Chevron Circle', 'jet-reviews' ),
					'fa fa-caret-square-o-right' => __( 'Caret Square', 'jet-reviews' ),
				)
			);

		}

		/**
		 * Render structured data
		 * @param  [type] $data [description]
		 * @return [type]       [description]
		 */
		public function render_structured_data( $data ) {

			$data = wp_parse_args( $data, array(
				'item_name'     => '',
				'item_image'    => '',
				'item_desc'     => '',
				'item_rating'   => '',
				'review_date'   => '',
				'review_author' => '',
			) );

			$sdata = array(
				'@context' => 'https://schema.org',
				'@type' => 'Review',
				'itemReviewed' => array(
					'@type' => 'Thing',
					'name' => $data['item_name'],
				),
				'reviewRating' => array(
					'@type' => 'Rating',
					'ratingValue' => $data['item_rating'],
				),
				'description' => $data['item_desc'],
				'datePublished' => date( 'c', strtotime( $data['review_date'] ) ),
				'author' => array(
					'@type' => 'Person',
					'name' => $data['review_author']
				)
			);

			if ( ! empty( $data['item_image'] ) ) {
				$sdata['itemReviewed']['image'] = array(
					'@type'  => 'ImageObject',
					'url'    => $data['item_image']['url'],
					'width'  => $data['item_image']['width'],
					'height' => $data['item_image']['height'],
				);
			}

			$json_data = json_encode( $sdata );

			printf( '<script type="application/ld+json">%s</script>', $json_data );

		}


		/**
		 * [get_icon_html description]
		 * @param  boolean $icon_setting [description]
		 * @return [type]                [description]
		 */
		public function get_elementor_icon_html( $icon_setting = false, $attr = array(), $default = false ) {

			if ( ! $icon_setting || empty( $icon_setting[ 'value' ] ) ) {
				return $default;
			}

			ob_start();
			\Elementor\Icons_Manager::render_icon( $icon_setting, $attr );
			return ob_get_clean();
		}

		/**
		 * [is_demo_mode description]
		 * @return boolean [description]
		 */
		public function is_demo_mode() {
			$demo_mode = apply_filters( 'jet-reviews/demo-mode/enabled', false );

			return $demo_mode;
		}

		/**
		 * [get_default_reviews_dataset description]
		 * @return [type] [description]
		 */
		public function get_default_reviews_dataset() {
			return array(
				array(
					'label'   => __( 'Jan', 'jet-reviews' ),
					'month'   => 1,
				),
				array(
					'label'   => __( 'Feb', 'jet-reviews' ),
					'month'   => 2,
				),
				array(
					'label'   => __( 'Mar', 'jet-reviews' ),
					'month'   => 3,
				),
				array(
					'label'   => __( 'Apr', 'jet-reviews' ),
					'month'   => 4,
				),
				array(
					'label'   => __( 'May', 'jet-reviews' ),
					'month'   => 5,
				),
				array(
					'label'   => __( 'Jun', 'jet-reviews' ),
					'month'   => 6,
				),
				array(
					'label'   => __( 'Jul', 'jet-reviews' ),
					'month'   => 7,
				),
				array(
					'label'   => __( 'Aug', 'jet-reviews' ),
					'month'   => 8,
				),
				array(
					'label'   => __( 'Sep', 'jet-reviews' ),
					'month'   => 9,
				),
				array(
					'label'   => __( 'Oct', 'jet-reviews' ),
					'month'   => 10,
				),
				array(
					'label'   => __( 'Nov', 'jet-reviews' ),
					'month'   => 11,
				),
				array(
					'label'   => __( 'Dec', 'jet-reviews' ),
					'month'   => 12,
				),
			);
		}

		/**
		 * @return array[]
		 */
		public function get_structure_data_types() {
			return apply_filters( 'jet-reviews/structure-data/types', [
				[
					'label'   => __( 'Thing', 'jet-reviews' ),
					'value'   => 'Thing',
				],
				[
					'label'   => __( 'Product', 'jet-reviews' ),
					'value'   => 'Product',
				],
				[
					'label'   => __( 'Place', 'jet-reviews' ),
					'value'   => 'Place',
				],
				[
					'label'   => __( 'Service', 'jet-reviews' ),
					'value'   => 'Service',
				],
				[
					'label'   => __( 'Organization', 'jet-reviews' ),
					'value'   => 'Organization',
				],
				[
					'label'   => __( 'Event', 'jet-reviews' ),
					'value'   => 'Event',
				],
				[
					'label'   => __( 'CreativeWork', 'jet-reviews' ),
					'value'   => 'CreativeWork',
				],
			] );
		}

		/**
		 * [rand_hex_color description]
		 * @return [type] [description]
		 */
		function rand_hex_color() {
			return '#' . str_pad( dechex( mt_rand( 0, 0xFFFFFF ) ), 6, '0', STR_PAD_LEFT );
		}

		/**
		 * @param int $length
		 *
		 * @return string
		 */
		public function generate_rand_string( $length = 10 ) {
			return substr(str_shuffle(str_repeat($x='0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ', ceil($length/strlen($x)) )),1,$length);
		}

		/**
		 * @param $string
		 *
		 * @return mixed
		 */
		public function forbidden_text_validation( $strings = [] ) {

			if ( empty( $strings ) ) {
				return false;
			}

			$forbidden_content = jet_reviews()->settings->get( 'forbidden-content', '' );

			if ( empty( $forbidden_content['enable'] ) || empty( $forbidden_content['words'] ) ) {
				return false;
			}

			$forbidden_words = str_replace( ', ', ',', $forbidden_content['words'] );

			$forbidden_words_list = explode( ',', $forbidden_words );

			foreach ( $strings as $string ) {
				$match = preg_match('/\b(?:' . implode('|', $forbidden_words_list ) . ')\b ?/i', $string );

				if ( filter_var( $match, FILTER_VALIDATE_BOOL ) ) {
					return true;
				}
			}

			return false;
		}

		/**
		 * @param string $svg_id
		 * @param bool $wrapper
		 *
		 * @return string
		 */
		public function get_svg_icon_html( $svg_id = '', $default = '', $attr = array(), $wrapper = true ) {

			if ( empty( $svg_id ) ) {
				return $default;
			}

			$url = wp_get_attachment_url( $svg_id );

			if ( ! $url ) {
				return $default;
			}

			return $this->get_image_by_url( $url, $attr, $wrapper );
		}

		/**
		 * Rturns image tag or raw SVG
		 *
		 * @param  string $url  image URL.
		 * @param  array  $attr [description]
		 * @return string
		 */
		public function get_image_by_url( $url = null, $attr = [], $wrapper = true ) {

			$url = esc_url( $url );

			if ( empty( $url ) ) {
				return;
			}

			$ext  = pathinfo( $url, PATHINFO_EXTENSION );
			$attr = array_merge( array( 'alt' => '' ), $attr );

			if ( 'svg' !== $ext ) {
				return sprintf( '<img src="%1$s"%2$s>', $url, $this->get_attr_string( $attr ) );
			}

			$base_url = network_site_url( '/' );
			$svg_path = str_replace( $base_url, ABSPATH, $url );
			$key      = md5( $svg_path );
			$svg      = get_transient( $key );

			if ( ! $svg ) {
				$svg = file_get_contents( $svg_path );
			}

			if ( ! $svg ) {
				return sprintf( '<img src="%1$s"%2$s>', $url, $this->get_attr_string( $attr ) );
			}

			set_transient( $key, $svg, DAY_IN_SECONDS );

			if ( ! $wrapper ) {
				return $svg;
			}

			unset( $attr['alt'] );

			return sprintf( '<div%2$s>%1$s</div>', $svg, $this->get_attr_string( $attr ) );
		}

		/**
		 * Return attributes string from attributes array.
		 *
		 * @param  array  $attr Attributes string.
		 * @return string
		 */
		public function get_attr_string( $attr = array() ) {

			if ( empty( $attr ) || ! is_array( $attr ) ) {
				return;
			}

			$result = '';

			foreach ( $attr as $key => $value ) {
				$result .= sprintf( ' %s="%s"', esc_attr( $key ), esc_attr( $value ) );
			}

			return $result;
		}

		/**
		 * @param string $icon
		 * @param array $classes
		 *
		 * @return array|string|string[]|null
		 */
		public function get_svg_html( $icon = '', $classes = [] ) {
			$icons = apply_filters( 'jet-reviews/svg-icons-list', [
				'emptyStarIcon'           => '<svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M10 1L7 7L1 7.75L5.13 12.37L4 19L10 16L16 19L14.88 12.37L19 7.75L13 7L10 1ZM10 3.24L12.34 7.93L16.99 8.51L13.81 12.07L14.68 17.22L10 14.88L5.32 17.22L6.19 12.07L3.01 8.51L7.66 7.93L10 3.24Z" fill="currentColor"/></svg>',
				'filledStarIcon'           => '<svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M10 1L13 7L19 7.75L14.88 12.37L16 19L10 16L4 19L5.13 12.37L1 7.75L7 7L10 1Z" fill="currentColor"/></svg>',
				'newCommentButtonIcon'    => '<svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M13.89 3.39L16.6 6.11C17.06 6.57 17.02 7.35 16.63 7.75L8.62 15.77L3.06 16.93L4.22 11.35C4.22 11.35 11.82 3.72 12.21 3.32C12.6 2.93 13.43 2.93 13.89 3.39ZM11.16 6.18L5.57 11.79L6.68 12.9L12.22 7.25L11.16 6.18ZM8.19 14.41L13.77 8.81L12.7 7.73L7.11 13.33L8.19 14.41Z" fill="currentColor"/></svg>',
				'newReviewButtonIcon'     => '<svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M13.89 3.39L16.6 6.11C17.06 6.57 17.02 7.35 16.63 7.75L8.62 15.77L3.06 16.93L4.22 11.35C4.22 11.35 11.82 3.72 12.21 3.32C12.6 2.93 13.43 2.93 13.89 3.39ZM11.16 6.18L5.57 11.79L6.68 12.9L12.22 7.25L11.16 6.18ZM8.19 14.41L13.77 8.81L12.7 7.73L7.11 13.33L8.19 14.41Z" fill="currentColor"/></svg>',
				'nextIcon'                => '<svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M6 15L11 10L6 5L7 3L14 10L7 17L6 15Z" fill="currentColor"/></svg>',
				'prevIcon'                => '<svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M14 5L9 10L14 15L13 17L6 10L13 3L14 5Z" fill="currentColor"/></svg>',
				'pinnedIcon'              => '<svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M10.44 3.02001L12.26 1.20001L18.62 7.55001L16.79 9.37001C15.74 8.69001 14.31 8.80001 13.38 9.73001L12.63 10.48C11.71 11.41 11.59 12.83 12.28 13.89L10.45 15.71L8.04 13.3L5.24 16.09C4.82 16.51 1.86 18.8 1.44 18.38C1.02 17.96 3.3 14.99 3.72 14.57L6.51 11.78L4.1 9.36001L5.93 7.54001C6.98 8.23001 8.41 8.11001 9.33 7.18001L10.08 6.43001C11.01 5.51001 11.13 4.08001 10.44 3.02001Z" fill="currentColor"/></svg>',
				'replyButtonIcon'         => '<svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M12 5H7V2L1 6L7 10V7H12C14.2 7 16 8.8 16 11C16 13.2 14.2 15 12 15H7V17H12C15.3 17 18 14.3 18 11C18 7.7 15.3 5 12 5Z" fill="currentColor"/></svg>',
				'reviewEmptyDislikeIcon'  => '<svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M4.86 3.5H13.5V11.5H11.86H11.6529L11.5064 11.6464C11.4261 11.7268 11.3154 11.8678 11.2004 12.0206C11.0776 12.1838 10.9269 12.3919 10.7592 12.6284C10.4233 13.102 10.0126 13.6988 9.60703 14.2987C9.20133 14.8988 8.7992 15.5043 8.48036 15.9959C8.17205 16.4712 7.92094 16.8711 7.83412 17.0437C7.69493 17.3141 7.44391 17.4756 7.21798 17.5039L7.28 18L7.21392 17.5044C7.20373 17.5057 7.19661 17.5059 7.17848 17.5021C7.1665 17.4995 7.14997 17.4952 7.12438 17.4873C7.10225 17.4804 7.07983 17.4729 7.05002 17.463C7.04361 17.4608 7.03686 17.4586 7.02969 17.4562C6.75161 17.3613 6.58923 17.0539 6.68812 16.7263C6.71252 16.6474 6.7539 16.5182 6.80611 16.3551C6.92342 15.9887 7.09543 15.4515 7.25247 14.9306C7.36767 14.5485 7.4792 14.1612 7.56238 13.8325C7.63965 13.5272 7.71 13.2084 7.71 13C7.71 12.4971 7.36696 12.1158 7.04673 11.8916C6.71257 11.6577 6.27673 11.5 5.86 11.5H2.86C2.67362 11.5 2.55716 11.4401 2.48855 11.3714C2.41994 11.3028 2.36 11.1864 2.36 11C2.36 11.0021 2.36 11.0032 2.36006 11.0032C2.36038 11.0032 2.36233 10.9777 2.37425 10.9127C2.38681 10.8443 2.40626 10.7536 2.43265 10.6415C2.4853 10.4177 2.56161 10.1251 2.65473 9.78524C2.84073 9.10636 3.08945 8.25437 3.33911 7.41802C3.58857 6.58232 3.83813 5.765 4.02539 5.15642C4.119 4.85218 4.197 4.60024 4.25158 4.4244L4.31491 4.22074L4.32878 4.17624C4.40112 4.01553 4.51019 3.82432 4.63463 3.67915C4.77684 3.51323 4.85658 3.50055 4.8599 3.50002C4.85998 3.50001 4.86002 3.5 4.86 3.5ZM17.5 11.5H16.5V3.5H17.5V11.5Z" stroke="currentColor"/></svg>',
				'reviewFilledDislikeIcon' => '<svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M7.28 18C7.13 18.02 7.02 17.98 6.87 17.93C6.31 17.74 6.04 17.14 6.21 16.58C6.38 16.03 7.21 13.54 7.21 13C7.21 12.47 6.46 12 5.86 12H2.86C2.26 12 1.86 11.6 1.86 11C1.86 10.4 3.86 4 3.86 4C4.03 3.61 4.41 3 4.86 3H14V12H11.86C11.45 12.41 8.56 16.71 8.28 17.27C8.07 17.68 7.68 17.95 7.28 18ZM18 12H16V3H18V12Z" fill="currentColor"/></svg>',
				'reviewEmptyLikeIcon'     => '<svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M15.6851 15.7793L15.6712 15.8238C15.5989 15.9845 15.4898 16.1757 15.3654 16.3209C15.2232 16.4868 15.1434 16.4995 15.1401 16.5C15.14 16.5 15.14 16.5 15.14 16.5H6.5V8.50001H8.14H8.34711L8.49355 8.35356C8.57391 8.27321 8.68464 8.13223 8.7996 7.97943C8.92242 7.81619 9.07306 7.60815 9.24082 7.37158C9.57668 6.89798 9.98737 6.30124 10.393 5.7013C10.7987 5.10121 11.2008 4.49568 11.5196 4.00413C11.8279 3.52887 12.079 3.12896 12.1659 2.95633C12.305 2.68591 12.5561 2.52439 12.782 2.49615L12.72 2.00001L12.7861 2.49562C12.7963 2.49427 12.8034 2.4941 12.8215 2.49794C12.8335 2.50047 12.85 2.50478 12.8756 2.51272C12.8978 2.51959 12.9202 2.52708 12.95 2.53705C12.9564 2.5392 12.9632 2.54146 12.9704 2.54385C13.2484 2.63881 13.4108 2.94616 13.3119 3.27372C13.2875 3.35262 13.2461 3.48184 13.1939 3.64489C13.0766 4.0113 12.9046 4.54854 12.7475 5.06944C12.6323 5.45154 12.5208 5.83885 12.4376 6.1675C12.3604 6.47285 12.29 6.7916 12.29 7.00001C12.29 7.5029 12.633 7.88422 12.9533 8.10838C13.2874 8.34229 13.7233 8.50001 14.14 8.50001H17.14C17.3264 8.50001 17.4428 8.55995 17.5114 8.62856C17.5801 8.69717 17.64 8.81363 17.64 9.00001C17.64 8.99794 17.64 8.99683 17.6399 8.99683C17.6396 8.99681 17.6377 9.02231 17.6257 9.08726C17.6132 9.15573 17.5937 9.24637 17.5674 9.35854C17.5147 9.58233 17.4384 9.87486 17.3453 10.2148C17.1593 10.8937 16.9105 11.7456 16.6609 12.582C16.4114 13.4177 16.1619 14.235 15.9746 14.8436C15.881 15.1478 15.803 15.3998 15.7484 15.5756L15.6851 15.7793ZM2.5 8.50001H3.5V16.5H2.5V8.50001Z" stroke="currentColor"/></svg>',
				'reviewFilledLikeIcon'    => '<svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M12.72 2.00001C12.87 1.98001 12.98 2.02001 13.13 2.07001C13.69 2.26001 13.96 2.86001 13.79 3.42001C13.62 3.97001 12.79 6.46001 12.79 7.00001C12.79 7.53001 13.54 8.00001 14.14 8.00001H17.14C17.74 8.00001 18.14 8.40001 18.14 9.00001C18.14 9.60001 16.14 16 16.14 16C15.97 16.39 15.59 17 15.14 17H6V8.00001H8.14C8.55 7.59001 11.44 3.29001 11.72 2.73001C11.93 2.32001 12.32 2.05001 12.72 2.00001ZM2 8.00001H4V17H2V8.00001Z" fill="currentColor"/></svg>',
				'showCommentsButtonIcon'  => '<svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M5 2H14C15.1 2 16 2.9 16 4V11C16 12.1 15.1 13 14 13H12L7 18V13H5C3.9 13 3 12.1 3 11V4C3 2.9 3.9 2 5 2Z" fill="currentColor"/></svg>',
			] );

			$classes   = (array) $classes;
			$classes[] = 'svg-icon';

			if ( array_key_exists( $icon, $icons ) ) {
				$repl = sprintf( '<svg class="%s" aria-hidden="true" role="img" focusable="false" ', join( ' ', $classes ) );
				$svg  = preg_replace( '/^<svg /', $repl, trim( $icons[ $icon ] ) ); // Add extra attributes to SVG code.
				$svg  = preg_replace( "/([\n\t]+)/", ' ', $svg ); // Remove newlines & tabs.
				$svg  = preg_replace( '/>\s*</', '><', $svg ); // Remove white space between SVG tags.

				return $svg;
			}

			return false;
		}

		/**
		 * @param $review_data
		 *
		 * @return bool
		 */
		public function submit_review_notify_moderator( $review_data = false ) {

			if ( ! $review_data ) {
				return true;
			}

			$submit_review_notify = jet_reviews()->settings->get( 'submit-review-notify', array(
				'enable'       => false,
				'approval'     => false,
				'authorNotify' => true,
			) );

			$need_approve = ! filter_var( $review_data['approved'],FILTER_VALIDATE_BOOLEAN ) ? true : false;

			if ( ! $submit_review_notify['enable'] || ( $submit_review_notify['approval'] && ! $need_approve ) ) {
				return true;
			}

			$switched_locale = switch_to_locale( get_locale() );

			$emails_data     = [];
			$source_id       = $review_data[ 'post_id' ];
			$post            = get_post( $source_id );
			$current_user    = jet_reviews()->user_manager->get_raw_user_data();
			$blogname        = wp_specialchars_decode( get_option( 'blogname' ), ENT_QUOTES );
			$review_id       = $review_data[ 'id' ];
			$review_content  = wp_specialchars_decode( $review_data[ 'content' ] );
			$rating_data     = maybe_unserialize( $review_data[ 'rating_data' ] );
			$author_id       = $review_data[ 'author' ];
			$author_data     = jet_reviews()->user_manager->get_raw_user_data( $author_id );
			$approve_link    = sprintf( __( 'Approve it: %s', 'jet-reviews' ), admin_url( "admin.php?page=jet-reviews-list-page&action=approve&review={$review_id}" ) ) . "\r\n\r\n";
			$delete_link     = sprintf( __( 'Delete it: %s', 'jet-reviews' ), admin_url( "admin.php?page=jet-reviews-list-page&action=delete&review={$review_id}" ) ) . "\r\n";

			$emails_data[ get_option( 'admin_email' ) ] = [
				'approve_link' => $need_approve && in_array( 'administrator', $current_user[ 'roles' ] ) ? $approve_link : '',
				'delete_link'  => in_array( 'administrator', $current_user[ 'roles' ] ) ? $delete_link : '',
			];

			$message_headers = '';
			$notify_message = '';

			$subject = '';

			if ( isset( $submit_review_notify['author_notify'] ) && $submit_review_notify['author_notify'] ) {
				$post_author = $post->post_author;
				$post_author_data = jet_reviews()->user_manager->get_raw_user_data( $post_author );

				$emails_data[ $post_author_data['mail'] ] = [
					'approve_link' => $need_approve && in_array( 'administrator', $post_author_data['roles'] ) ? $approve_link : '',
					'delete_link'  => user_can( $post_author_data['id'], 'delete_posts', $post->ID ) ? $delete_link : '',
				];
			}

			switch ( $review_data['source'] ) {
				case 'post':
					$notify_message .= sprintf( __( 'A new review for "%s" has been submitted', 'jet-reviews' ), $post->post_title ) . "\r\n";
					$notify_message .= get_permalink( $source_id ) . "#jet-reviews-item-{$review_id}" . "\r\n\r\n";
					$subject         = sprintf( __( '[%1$s] Please check a new review: "%2$s"', 'jet-reviews' ), $blogname, $post->post_title );

					break;

				case 'user':
					$user_data = jet_reviews()->user_manager->get_raw_user_data( $source_id );
					$notify_message .= sprintf( __( 'A new review for "%s" has been submitted', 'jet-reviews' ), $user_data['name'] ) . "\r\n";
					$subject         = sprintf( __( '[%1$s] Please check a new review: "%2$s"', 'jet-reviews' ), $blogname, $user_data['name'] );

					break;
			}

			$rating = '';
			$fields_rating = [];

			foreach ( $rating_data as $key => $field_data ) {
				$label = $field_data[ 'field_label' ];
				$value = (int) $field_data[ 'field_value' ];
				$max   = (int) $field_data[ 'field_max' ];

				$rating .= sprintf( '%1$s %2$s / %3$s', $label, $value, $max ) . "\r\n";
				$fields_rating[] = round( ( 100 * $value ) / $max, 2 );
			}

			$rating .= sprintf( __( 'Total satisfy percent: %1$s', 'jet-reviews' ), round( array_sum( $fields_rating ) / count( $fields_rating ) ) );

			$notify_message .= sprintf( __( 'Author: %1$s', 'jet-reviews' ), $author_data['name'] ) . "\r\n";
			$notify_message .= sprintf( __( 'Email: %1$s', 'jet-reviews' ), $author_data['mail'] ) . "\r\n";
			$notify_message .= sprintf( __( 'Date: %1$s', 'jet-reviews' ), $review_data[ 'date' ] ) . "\r\n\r\n";
			$notify_message .= sprintf( __( 'Comment: %s', 'jet-reviews' ), "\r\n" . $review_content ) . "\r\n\r\n";
			$notify_message .= $rating . "\r\n\r\n";

			$notify_message = apply_filters( 'jet-reviews/notify/review/message', $notify_message, $review_data );
			$subject = apply_filters( 'jet-reviews/notify/review/subject', $subject, $review_data );
			$message_headers = apply_filters( 'jet-reviews/notify/review/headers', $message_headers, $review_data );

			foreach ( $emails_data as $email => $email_data ) {
				wp_mail( $email, wp_specialchars_decode( $subject ), $notify_message . $email_data['approve_link'] . $email_data['delete_link'], $message_headers );
			}

			if ( $switched_locale ) {
				restore_previous_locale();
			}

			return true;
		}

		/**
		 * @param $comment_data
		 *
		 * @return bool
		 */
		public function submit_comment_notify_moderator( $comment_data = false ) {

			if ( ! $comment_data ) {
				return true;
			}

			$submit_comment_notify = jet_reviews()->settings->get( 'submit-comment-notify', array(
				'enable'   => false,
				'approval' => false,
			) );

			$need_approve = ! filter_var( $comment_data['approved'],FILTER_VALIDATE_BOOLEAN ) ? true : false;

			if ( ! $submit_comment_notify['enable'] || ( $submit_comment_notify['approval'] && ! $need_approve ) ) {
				return true;
			}

			$emails = [ get_option( 'admin_email' ) ];

			$source_id      = $comment_data['post_id'];
			$blogname       = wp_specialchars_decode( get_option( 'blogname' ), ENT_QUOTES );
			$comment_id     = $comment_data['id'];
			$comment_content = wp_specialchars_decode( $comment_data['content'] );
			$author_id      = $comment_data['author'];
			$author_data    = jet_reviews()->user_manager->get_raw_user_data( $author_id );

			$subject = '';
			$notify_message = '';
			$message_headers = '';

			switch ( $comment_data['source'] ) {
				case 'post':
					$post            = get_post( $source_id );
					$notify_message .= sprintf( __( 'A new comment for "%s" has been submitted', 'jet-reviews' ), $post->post_title ) . "\r\n";
					$notify_message .= get_permalink( $source_id ) . "#jet-reviews-item-{$comment_data['review_id']}" . "\r\n\r\n";

					$subject         = sprintf( __( '[%1$s] Please check a new review: "%2$s"', 'jet-reviews' ), $blogname, $post->post_title );
					break;

				case 'user':
					$user_data = jet_reviews()->user_manager->get_raw_user_data( $source_id );
					$notify_message .= sprintf( __( 'A new review for "%s" has been submitted', 'jet-reviews' ), $user_data['name'] ) . "\r\n";
					$subject         = sprintf( __( '[%1$s] Please check a new review: "%2$s"', 'jet-reviews' ), $blogname, $user_data['name'] );

					break;

			}

			$notify_message .= sprintf( __( 'Author: %1$s', 'jet-reviews' ), $author_data['name'] ) . "\r\n";
			$notify_message .= sprintf( __( 'Email: %1$s', 'jet-reviews' ), $author_data['mail'] ) . "\r\n";
			$notify_message .= sprintf( __( 'Date: %1$s', 'jet-reviews' ), $comment_data[ 'date' ] ) . "\r\n\r\n";
			$notify_message .= sprintf( __( 'Comment: %s', 'jet-reviews' ), "\r\n" . $comment_content ) . "\r\n\r\n";

			if ( $need_approve ) {
				$notify_message .= sprintf( __( 'Approve it: %s', 'jet-reviews' ), admin_url( "admin.php?page=jet-reviews-comments-list-page&action=approve&comment={$comment_id}" ) ) . "\r\n\r\n";
			}

			$notify_message .= sprintf( __( 'Delete it: %s', 'jet-reviews' ), admin_url( "admin.php?page=jet-reviews-comments-list-page&action=delete&comment={$comment_id}" ) ) . "\r\n";

			$notify_message = apply_filters( 'jet-reviews/notify/comment/message', $notify_message, $comment_data );

			$subject = apply_filters( 'jet-reviews/notify/comment/subject', $subject, $comment_data );

			$message_headers = apply_filters( 'jet-reviews/notify/comment/headers', $message_headers, $comment_data );

			foreach ( $emails as $email ) {
				wp_mail( $email, wp_specialchars_decode( $subject ), $notify_message, $message_headers );
			}

			$switched_locale = switch_to_locale( get_locale() );

			if ( $switched_locale ) {
				restore_previous_locale();
			}

			return true;
		}

		/**
		 * @return bool
		 */
		public function has_elementor() {
			return defined( 'ELEMENTOR_VERSION' );
		}

		/**
		 * Returns the instance.
		 *
		 * @since  1.0.0
		 * @return object
		 */
		public static function get_instance( $shortcodes = array() ) {

			// If the single instance hasn't been set, set it now.
			if ( null == self::$instance ) {
				self::$instance = new self( $shortcodes );
			}
			return self::$instance;
		}
	}

}

/**
 * Returns instance of Jet_Reviews_Tools
 *
 * @return object
 */
function jet_reviews_tools() {
	return Jet_Reviews_Tools::get_instance();
}
