<?php
namespace Jet_Theme_Core\Compatibility;

// If this file is called directly, abort.

if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Compatibility Manager
 */
class Jet_Woo_Builder {

	/**
	 * @param $raw_templates_list
	 *
	 * @return mixed
	 */
	public function modify_raw_templates_list( $templates_list ) {
		$jet_woo_templates = jet_woo_builder_post_type()->get_templates_list();
		$doc_types    = jet_woo_builder()->documents->get_document_types();

		$templates = array_map( function( $template_obj ) {
			$template_id  = $template_obj->ID;
			$type         = 'jet_page';
			$content_type = 'elementor';
			$edit_link    = jet_theme_core()->templates->get_template_edit_link( $template_id, 'elementor' );
			$template_id  = $template_obj->ID;
			$author_id    = $template_obj->post_author;
			$author_data  = get_userdata( $author_id );
			$details      = [ 'jet-woo-builder' ];

			$elementor_template_type = get_post_meta( $template_id, '_elementor_template_type', true );

			switch ( $elementor_template_type ) {
				case 'jet-woo-builder':
					$type = 'jet_single_product';
					break;

				case 'jet-woo-builder-archive':
				case 'jet-woo-builder-category':
				case 'jet-woo-builder-shop':
					$type = 'jet_products_archive';
					break;

				case 'jet-woo-builder-cart':
					$type = 'jet_products_card';
					break;

				case 'jet-woo-builder-cart':
					$type = 'jet_products_card';
					break;

				case 'jet-woo-builder-checkout':
				case 'jet-woo-builder-thankyou':
					$type = 'jet_products_checkout';
					break;

				case 'jet-woo-builder-myaccount':
					$type = 'jet_account_page';
					break;

				default:
					$type = 'jet_products_archive';
					break;
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
					'lastModified' => $template_obj->post_modified,
				],
				'author'      => [
					'id'   => $author_id,
					'name' => $author_data->user_login,
				],
			];
		}, $jet_woo_templates );

		return wp_parse_args( $templates, $templates_list );
	}

	/**
	 * @param $icons
	 *
	 * @return mixed
	 */
	public function modify_source_type_icons( $icons ) {
		$icons['jet-woo-builder'] = '<svg width="32" height="32" viewBox="0 0 32 32" fill="none" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" clip-rule="evenodd" d="M27.6759 8.66748C28.0509 8.64436 28.2649 9.132 28.0157 9.44197L25.7979 12.2011C25.5382 12.5243 25.0541 12.3177 25.0592 11.8859L25.0724 10.7664C25.0741 10.6197 25.0132 10.4807 24.9078 10.3912L24.1037 9.70785C23.7936 9.44429 23.9472 8.89747 24.338 8.87336L27.6759 8.66748ZM10.0288 15.9132C10.0288 18.5563 7.86821 20.6982 5.20575 20.6982C4.53861 20.6982 4 20.1605 4 19.5012C4 18.8419 4.53861 18.3072 5.20575 18.3072C6.53698 18.3072 7.61726 17.2348 7.61726 15.9132V12.3252C7.61726 11.6629 8.15588 11.1282 8.82302 11.1282C9.49016 11.1282 10.0288 11.6629 10.0288 12.3252V15.9132ZM23.0289 15.9132C23.0289 17.2348 24.1092 18.3072 25.4404 18.3072C26.1075 18.3072 26.6462 18.8389 26.6462 19.5012C26.6462 20.1635 26.1075 20.6982 25.4404 20.6982C22.7779 20.6982 20.6174 18.5563 20.6174 15.9132V12.3252C20.6174 11.6629 21.156 11.1282 21.8231 11.1282C22.4903 11.1282 23.0289 11.666 23.0289 12.3252V13.3977H23.9868C24.6539 13.3977 25.1956 13.9354 25.1956 14.5977C25.1956 15.26 24.6539 15.7978 23.9868 15.7978H23.0289V15.9132ZM19.9258 14.2362C19.9288 14.2332 19.9319 14.2332 19.9319 14.2332C19.5524 13.2428 18.8424 12.3739 17.8509 11.8027C15.5496 10.4781 12.6117 11.2619 11.2866 13.5526C9.95842 15.8403 10.748 18.766 13.0463 20.0876C14.7386 21.0597 16.7767 20.8896 18.264 19.8202L18.2549 19.8081C18.5976 19.5984 18.8241 19.2217 18.8241 18.7933C18.8241 18.1341 18.2855 17.5994 17.6214 17.5994C17.3001 17.5994 17.0063 17.7239 16.792 17.9305C16.0698 18.4136 15.1089 18.4895 14.2979 18.046L19.1546 15.8069C19.4361 15.7218 19.6871 15.5335 19.8432 15.26C20.0329 14.935 20.0513 14.5613 19.9258 14.2362ZM16.6482 13.8716C16.792 13.9537 16.9206 14.0478 17.0399 14.1511L13.0432 15.9892C13.034 15.5669 13.1381 15.1385 13.3645 14.7466C14.0286 13.6043 15.4975 13.2124 16.6482 13.8716Z" fill="#D946EF"/><rect x="1" y="1" width="30" height="30" rx="5" stroke="#D946EF" stroke-width="2"/></svg>';

		return $icons;
	}

	/**
	 * @param $details
	 *
	 * @return mixed
	 */
	public function modify_template_details( $details ) {
		$additional = [
			'jet-woo-builder' => [
				'icon'  => '<svg width="32" height="32" viewBox="0 0 32 32" fill="none" xmlns="http://www.w3.org/2000/svg"><rect width="32" height="32" rx="2" fill="#D946EF"/><path fill-rule="evenodd" clip-rule="evenodd" d="M27.6759 8.66748C28.0509 8.64436 28.2649 9.132 28.0157 9.44197L25.7979 12.2011C25.5382 12.5243 25.0541 12.3177 25.0592 11.8859L25.0724 10.7664C25.0741 10.6197 25.0132 10.4807 24.9078 10.3912L24.1037 9.70785C23.7936 9.44429 23.9472 8.89747 24.338 8.87336L27.6759 8.66748ZM10.0288 15.9132C10.0288 18.5563 7.86821 20.6982 5.20575 20.6982C4.53861 20.6982 4 20.1605 4 19.5012C4 18.8419 4.53861 18.3072 5.20575 18.3072C6.53698 18.3072 7.61726 17.2348 7.61726 15.9132V12.3252C7.61726 11.6629 8.15588 11.1282 8.82302 11.1282C9.49016 11.1282 10.0288 11.6629 10.0288 12.3252V15.9132ZM23.0289 15.9132C23.0289 17.2348 24.1092 18.3072 25.4404 18.3072C26.1075 18.3072 26.6462 18.8389 26.6462 19.5012C26.6462 20.1635 26.1075 20.6982 25.4404 20.6982C22.7779 20.6982 20.6174 18.5563 20.6174 15.9132V12.3252C20.6174 11.6629 21.156 11.1282 21.8231 11.1282C22.4903 11.1282 23.0289 11.666 23.0289 12.3252V13.3977H23.9868C24.6539 13.3977 25.1956 13.9354 25.1956 14.5977C25.1956 15.26 24.6539 15.7978 23.9868 15.7978H23.0289V15.9132ZM19.9258 14.2362C19.9288 14.2332 19.9319 14.2332 19.9319 14.2332C19.5524 13.2428 18.8424 12.3739 17.8509 11.8027C15.5496 10.4781 12.6117 11.2619 11.2866 13.5526C9.95842 15.8403 10.748 18.766 13.0463 20.0876C14.7386 21.0597 16.7767 20.8896 18.264 19.8202L18.2549 19.8081C18.5976 19.5984 18.8241 19.2217 18.8241 18.7933C18.8241 18.1341 18.2855 17.5994 17.6214 17.5994C17.3001 17.5994 17.0063 17.7239 16.792 17.9305C16.0698 18.4136 15.1089 18.4895 14.2979 18.046L19.1546 15.8069C19.4361 15.7218 19.6871 15.5335 19.8432 15.26C20.0329 14.935 20.0513 14.5613 19.9258 14.2362ZM16.6482 13.8716C16.792 13.9537 16.9206 14.0478 17.0399 14.1511L13.0432 15.9892C13.034 15.5669 13.1381 15.1385 13.3645 14.7466C14.0286 13.6043 15.4975 13.2124 16.6482 13.8716Z" fill="white"/></svg>',
				'label' => __( 'Template builded by JetWooBuilder', 'jet-theme-core' ),
			]
		];

		return wp_parse_args( $additional, $details );
	}

	/**
	 * @param $template_type
	 * @param $template_id
	 *
	 * @return mixed
	 */
	public function maybe_modify_location_template_type( $template_type, $template_id ) {

		$post_type = get_post_type( $template_id );

		if ( 'jet-woo-builder' === $post_type ) {
			$type = get_post_meta( $template_id, '_elementor_template_type', true );
			$type_map = $this->get_templates_compatibility_map();

			if ( array_key_exists( $type, $type_map ) ) {
				return $type_map[ $type ];
			}
		}

		return $template_type;
	}

	/**
	 * @param $content_type
	 * @param $template_id
	 * @param $template_type
	 *
	 * @return mixed|string
	 */
	public function maybe_modify_location_template_content_type( $content_type, $template_id, $template_type ) {

		$post_type = get_post_type( $template_id );

		if ( 'jet-woo-builder' === $post_type ) {
			return 'elementor';
		}

		return $content_type;
	}

	/**
	 * @return string[]
	 */
	public function get_templates_compatibility_map() {
		return [
			'jet-woo-builder'           => 'jet_single_product',
			'jet-woo-builder-archive'   => 'jet_products_archive',
			'jet-woo-builder-category'  => 'jet_products_archive',
			'jet-woo-builder-shop'      => 'jet_products_archive',
			'jet-woo-builder-cart'      => 'jet_products_card',
			'jet-woo-builder-checkout'  => 'jet_products_checkout',
			'jet-woo-builder-thankyou'  => 'jet_products_checkout',
			'jet-woo-builder-myaccount' => 'jet_account_page',
		];
	}

	/**
	 * @param $check
	 * @param $type
	 *
	 * @return mixed
	 */
	public function is_register_widgets( $check, $type ) {
		$types_map = [ 'single', 'shop', 'cart', 'checkout', 'thankyou', 'myaccount' ];

		if ( in_array( $type, $types_map )  ) {
			return true;
		}

		return $check;
	}

	/**
	 * @param $check
	 * @param $type
	 *
	 * @return mixed
	 */
	public function is_document_type_for_widget( $check, $type ) {
		$meta_doc_type = get_post_meta( get_the_ID(), \Elementor\Core\Base\Document::TYPE_META_KEY, true );

		$doc_type_map = [
			'jet_single_product' => [ 'single' ],
			'jet_products_archive' => [ 'archive', 'category', 'shop' ],
			'jet_products_card' => [ 'cart' ],
			'jet_products_checkout' => [ 'checkout' ],
			'jet_products_checkout_endpoint' => [ 'checkout', 'thankyou' ],
			'jet_account_page' => [ 'myaccount' ],
		];

		if ( isset( $doc_type_map[ $meta_doc_type ] ) && in_array( $type, $doc_type_map[ $meta_doc_type ] ) ) {
			return true;
		}

		return $check;
	}

	/**
	 * [__construct description]
	 */
	public function __construct() {

		if ( ! class_exists( 'WooCommerce' ) || ! function_exists( 'jet_woo_builder' ) ) {
			return false;
		}

		add_filter( 'jet-theme-core/templates/raw-templates-list', [ $this, 'modify_raw_templates_list' ] );
		add_filter( 'jet-theme-core/templates/template-details', [ $this, 'modify_template_details' ], 10, 2 );
		add_filter( 'jet-theme-core/theme-builder/frontend/render-location/template-type', [ $this, 'maybe_modify_location_template_type' ], 10, 3 );
		add_filter( 'jet-theme-core/theme-builder/frontend/render-location/template-content-type', [ $this, 'maybe_modify_location_template_content_type' ], 10, 4 );
		add_filter( 'jet-woo-builder/integration/register-widgets', [ $this, 'is_register_widgets' ], 10, 3 );
		add_filter( 'jet-woo-builder/documents/is-document-type', [ $this, 'is_document_type_for_widget' ], 10, 3 );

	}

}
