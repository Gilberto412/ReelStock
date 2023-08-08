<?php
namespace Jet_Theme_Core\Compatibility;

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Compatibility Manager
 */
class Woocommerce {

	/**
	 * Include files
	 */
	public function load_files() {}

	/**
	 * @return string[]
	 */
	public function get_structure_list() {
		return [
			'jet_single_product',
			'jet_products_archive',
			'jet_products_card',
			'jet_products_checkout',
			'jet_account_page'
		];
	}

	/**
	 * @param $conditions_list
	 *
	 * @return mixed
	 */
	public function modify_template_conditions_group_list( $conditions_group_list ) {

		$woo_groups_list = [
			'woocommerce' => [
				'label'      => __( 'WooCommerce', 'jet-theme-core' ),
				'sub-groups' => [],
			],
		];

		return wp_parse_args( $woo_groups_list, $conditions_group_list );
	}

	/**
	 * @param $conditions_sub_group_list
	 *
	 * @return array|object
	 */
	public function modify_template_conditions_sub_group_list( $conditions_sub_group_list ) {

		$woo_groups_list = [
			'woocommerce-archive' => [
				'label'   => __( 'Products Archive', 'jet-theme-core' ),
				'options' => [],
			],
			'woocommerce-single' => [
				'label'   => __( 'Single Product', 'jet-theme-core' ),
				'options' => [],
			],
			'woocommerce-page' => [
				'label'   => __( 'Pages', 'jet-theme-core' ),
				'options' => [],
			],
			'woocommerce-сheckout' => [
				'label'   => __( 'Checkout', 'jet-theme-core' ),
				'options' => [],
			],
			'woocommerce-my-account' => [
				'label'   => __( 'My Account', 'jet-theme-core' ),
				'options' => [],
			],
		];

		return wp_parse_args( $woo_groups_list, $conditions_sub_group_list );
	}

	/**
	 * @param $conditions_list
	 *
	 * @return mixed
	 */
	public function modify_template_conditions_list( $conditions_list ) {

		$base_path = jet_theme_core()->plugin_path( 'includes/compatibility/plugins/woocommerce/template-conditions/' );

		$woo_conditions_list = [
			'\Jet_Theme_Core\Template_Conditions\Woo_All_Product_Archives'            => $base_path . 'all-products-archive.php',
			'\Jet_Theme_Core\Template_Conditions\Woo_Product_Categories'              => $base_path . 'product-categories.php',
			'\Jet_Theme_Core\Template_Conditions\Woo_Product_Tags'                    => $base_path . 'product-tags.php',
			'\Jet_Theme_Core\Template_Conditions\Woo_Singular_Product'                => $base_path . 'singular-product.php',
			'\Jet_Theme_Core\Template_Conditions\Woo_Singular_Product_Categories'     => $base_path . 'singular-product-categories.php',
			'\Jet_Theme_Core\Template_Conditions\Woo_Singular_Product_Category_Child' => $base_path . 'singular-product-category-child.php',
			'\Jet_Theme_Core\Template_Conditions\Woo_Singular_Product_Tags'           => $base_path . 'singular-product-tags.php',
			'\Jet_Theme_Core\Template_Conditions\Woo_Shop_Page'                       => $base_path . 'shop-page.php',
			'\Jet_Theme_Core\Template_Conditions\Woo_Search_Results'                  => $base_path . 'search-results.php',
			'\Jet_Theme_Core\Template_Conditions\Woo_Product_Card'                    => $base_path . 'product-card.php',
			'\Jet_Theme_Core\Template_Conditions\Woo_Product_Empty_Card'              => $base_path . 'product-empty-card.php',
			'\Jet_Theme_Core\Template_Conditions\Woo_Product_Checkout'                => $base_path . 'product-checkout.php',
			'\Jet_Theme_Core\Template_Conditions\Woo_Product_Checkout_Endpoints'      => $base_path . 'product-checkout-endpoints.php',
			'\Jet_Theme_Core\Template_Conditions\Woo_Thanks_Page'                     => $base_path . 'thanks-page.php',
			'\Jet_Theme_Core\Template_Conditions\Woo_Account_Page'                    => $base_path . 'account-page.php',
			'\Jet_Theme_Core\Template_Conditions\Woo_Account_Login_Page'              => $base_path . 'account-login-page.php',
			'\Jet_Theme_Core\Template_Conditions\Woo_Account_Endpoints'               => $base_path . 'account-endpoints.php',
		];

		return wp_parse_args( $woo_conditions_list, $conditions_list );
	}

	/**
	 * @param $endpoints_list
	 *
	 * @return mixed
	 */
	public function modify_template_endpoint_list( $endpoints_list ) {

		$base_path = jet_theme_core()->plugin_path( 'includes/compatibility/plugins/woocommerce/rest-api/' );

		$endpoints_list[ '\Jet_Theme_Core\Endpoints\Get_Product_Categories' ] = $base_path . 'get-product-categories.php';
		$endpoints_list[ '\Jet_Theme_Core\Endpoints\Get_Product_Tags' ]       = $base_path . 'get-product-tags.php';
		$endpoints_list[ '\Jet_Theme_Core\Endpoints\Get_Products' ]           = $base_path . 'get-products.php';

		return $endpoints_list;
	}

	/**
	 * @param $structures_list
	 *
	 * @return mixed
	 */
	public function modify_template_structures_list( $structures_list ) {

		$base_path = jet_theme_core()->plugin_path( 'includes/compatibility/plugins/woocommerce/structures/' );

		$woo_structures_list = [
			'\Jet_Theme_Core\Structures\Products_Archive' => $base_path . 'products-archive.php',
			'\Jet_Theme_Core\Structures\Single_Product' => $base_path . 'single-product.php',
			'\Jet_Theme_Core\Structures\Products_Card' => $base_path . 'products-card.php',
			'\Jet_Theme_Core\Structures\Products_Checkout' => $base_path . 'products-checkout.php',
			'\Jet_Theme_Core\Structures\Products_Checkout_Endpoint' => $base_path . 'products-checkout-endpoint.php',
			'\Jet_Theme_Core\Structures\Account_Page' => $base_path . 'account-page.php',
		];

		return wp_parse_args( $woo_structures_list, $structures_list );
	}

	/**
	 * @param $body_type_map
	 *
	 * @return array
	 */
	public function modify_template_import_body_type_map( $body_type_map ) {
		return wp_parse_args( $this->get_structure_list(), $body_type_map );
	}

	/**
	 * @param $structures_list
	 *
	 * @return array
	 */
	public function modify_template_column_exclude_structures( $structures_list ) {
		return array_merge( $structures_list, $this->get_structure_list() );
	}

	/**
	 * @param $details
	 *
	 * @return mixed
	 */
	public function modify_template_details( $details ) {
		$additional = [
			'woocommerce' => [
				'icon'  => '<svg width="32" height="32" viewBox="0 0 32 32" fill="none" xmlns="http://www.w3.org/2000/svg"><rect width="32" height="32" rx="2" fill="#7956AD"/><path d="M3.2 19.2168C3.2 16.5395 3.2 13.8594 3.2 11.1821C3.20554 11.1514 3.2194 11.1234 3.2194 11.0927C3.22217 10.9587 3.2388 10.8303 3.26375 10.6991C3.3663 10.2133 3.59636 9.80015 3.95114 9.45955C4.24218 9.17758 4.58588 8.98495 4.9767 8.87887C5.12083 8.83978 5.26773 8.81745 5.41464 8.80349C5.49502 8.79791 5.57817 8.8007 5.65855 8.8007C12.5076 8.8007 19.3594 8.8007 26.2084 8.8007C26.2721 8.8007 26.3387 8.8007 26.4024 8.8007C26.5937 8.79791 26.7822 8.81745 26.9706 8.86212C27.4557 8.97937 27.8659 9.22504 28.1985 9.59914C28.4258 9.85319 28.5893 10.1435 28.6919 10.4702C28.7584 10.6851 28.8 10.9057 28.8 11.1346C28.8 13.8315 28.8 16.5255 28.8 19.2223C28.8 19.2866 28.7972 19.348 28.7917 19.4122C28.7806 19.5992 28.7418 19.7807 28.6836 19.9566C28.5838 20.2609 28.4314 20.5345 28.2207 20.7746C28.0239 21.0035 27.7938 21.1877 27.5278 21.3301C27.1702 21.52 26.7877 21.6065 26.3858 21.6065C23.9356 21.6065 21.4825 21.6065 19.0323 21.6065C19.0157 21.6065 18.999 21.6065 18.9824 21.6065C18.9713 21.6065 18.9602 21.6093 18.9436 21.6121C19.0018 21.7545 19.0573 21.8941 19.1127 22.0309C19.1681 22.1704 19.2263 22.3072 19.2818 22.4468C19.3372 22.5864 19.3926 22.7232 19.4508 22.8628C19.5063 23.0024 19.5617 23.1392 19.6199 23.2788C19.6754 23.4184 19.7336 23.5552 19.789 23.6947C19.8444 23.8315 19.9026 23.9683 19.9498 24.1107C19.8999 24.0884 19.8555 24.066 19.8112 24.0409C19.3178 23.7645 18.8244 23.4882 18.3311 23.2118C17.8931 22.9661 17.4552 22.7204 17.0172 22.4747C16.5239 22.1984 16.0305 21.922 15.5371 21.6428C15.4928 21.6177 15.4512 21.6093 15.4013 21.6093C12.1639 21.6093 8.92923 21.6093 5.69181 21.6093C5.54491 21.6093 5.398 21.6009 5.25387 21.5814C5.07925 21.5563 4.91017 21.5116 4.74664 21.4474C4.3974 21.3134 4.09805 21.1068 3.84582 20.8276C3.59636 20.554 3.42174 20.2413 3.31641 19.884C3.26098 19.6914 3.22771 19.4931 3.22217 19.2893C3.2194 19.2642 3.22217 19.2363 3.2 19.2168Z" fill="white"/><path fill-rule="evenodd" clip-rule="evenodd" d="M7.77815 16.4112L7.7782 16.4111L7.7784 16.4107C7.80796 16.3524 7.83699 16.2952 7.86548 16.2386C8.1194 15.7366 8.37332 15.2346 8.62989 14.7325C8.7481 14.5016 8.86484 14.2692 8.98121 14.0375C9.02107 13.9582 9.06089 13.8789 9.10071 13.7998C9.35198 13.3033 9.60591 12.804 9.85983 12.3075C9.93389 12.1621 10.0291 12.0304 10.1508 11.9234C10.2592 11.8274 10.3835 11.7616 10.5264 11.7424C10.7195 11.7177 10.9046 11.7424 11.0607 11.8768C11.1665 11.9646 11.2379 12.0798 11.2908 12.206C11.3516 12.3514 11.3887 12.5023 11.4151 12.6586C11.4812 13.0207 11.5553 13.3828 11.6373 13.7422C11.7722 14.332 11.9309 14.9136 12.1213 15.4869C12.1875 15.6817 12.2562 15.8737 12.325 16.0658C12.3258 16.0674 12.3266 16.0688 12.3275 16.0704C12.3295 16.0739 12.3319 16.0781 12.3356 16.0877C12.338 16.0662 12.3404 16.0457 12.3427 16.0258V16.0258C12.3481 15.9797 12.353 15.937 12.3567 15.8929C12.3647 15.823 12.3726 15.7523 12.3805 15.6817C12.3885 15.6111 12.3964 15.5404 12.4043 15.4705L12.4089 15.4305L12.4089 15.4303C12.4233 15.3033 12.4379 15.1745 12.4546 15.048L12.4615 14.9964L12.4615 14.9963C12.4804 14.8539 12.4995 14.7098 12.5207 14.5679C12.5295 14.5103 12.5381 14.4527 12.5466 14.3951L12.5466 14.395C12.5636 14.2798 12.5807 14.1646 12.6001 14.0495C12.6096 13.9957 12.619 13.9419 12.6284 13.8881C12.6661 13.6731 12.7038 13.458 12.7482 13.2429C12.8011 12.9796 12.8619 12.719 12.9254 12.4584C13.0418 11.981 13.1873 11.5147 13.3671 11.0593C13.4518 10.8508 13.5444 10.6451 13.6502 10.4475C13.7136 10.3296 13.793 10.2253 13.8988 10.1458C14.0337 10.047 14.1871 10.0059 14.3537 10.0004C14.6341 9.99217 14.8616 10.1129 15.0494 10.3214C15.1816 10.4695 15.2425 10.6478 15.2477 10.8508C15.253 11.0291 15.2054 11.191 15.1261 11.3446C15.0335 11.5202 14.9594 11.704 14.8933 11.8905C14.7187 12.3651 14.5944 12.8561 14.486 13.3527C14.404 13.7285 14.3326 14.1071 14.2691 14.4884C14.2294 14.7325 14.1897 14.9767 14.1554 15.2236C14.1263 15.4293 14.0998 15.6378 14.0734 15.8463C14.0469 16.0575 14.0205 16.2715 13.9993 16.4827C13.9834 16.6391 13.9702 16.7955 13.957 16.9518C13.9438 17.1055 13.9305 17.2618 13.9199 17.4155C13.9173 17.4607 13.9153 17.5053 13.9133 17.5498C13.9114 17.5944 13.9094 17.639 13.9067 17.6843C13.8935 17.9614 13.8856 18.2384 13.8909 18.5155C13.8926 18.5953 13.8967 18.6738 13.9007 18.7527L13.9007 18.7528C13.9028 18.7933 13.9049 18.8339 13.9067 18.8749C13.9173 19.0669 13.9199 19.2589 13.8618 19.4455C13.8115 19.6073 13.7348 19.7527 13.6052 19.8597C13.5126 19.9365 13.4068 19.9804 13.2904 19.9941C13.0973 20.0188 12.9228 19.9639 12.7614 19.8624C12.6583 19.7994 12.5657 19.7171 12.4811 19.6265C12.116 19.2397 11.8039 18.809 11.5341 18.3482C11.304 17.9559 11.103 17.5471 10.9258 17.1247C10.7486 16.7049 10.5925 16.2743 10.455 15.8381C10.4147 15.7088 10.3758 15.5766 10.3373 15.4457C10.3227 15.3961 10.3082 15.3467 10.2936 15.2976C10.2921 15.2899 10.2898 15.2831 10.2871 15.2752C10.285 15.269 10.2827 15.2622 10.2804 15.2538C10.2698 15.2702 10.2645 15.2812 10.2592 15.2922C9.96298 15.9012 9.66674 16.5102 9.37314 17.1192C9.30339 17.2639 9.23498 17.409 9.16662 17.5541L9.1666 17.5541C9.06908 17.7611 8.97166 17.9678 8.87059 18.1726C8.66957 18.5813 8.45003 18.9764 8.18288 19.344C8.05063 19.525 7.9078 19.6951 7.72794 19.8295C7.64065 19.8954 7.55072 19.9475 7.44492 19.9749C7.2968 20.0133 7.1619 19.9804 7.04023 19.8844C6.93443 19.7994 6.85772 19.6869 6.7916 19.5689C6.70696 19.4208 6.64612 19.2644 6.59057 19.1026C6.42129 18.6197 6.2864 18.126 6.16208 17.6294C6.02983 17.0972 5.9108 16.5596 5.80236 16.0219C5.72036 15.6104 5.64101 15.1961 5.5643 14.7819C5.51853 14.5374 5.47473 14.2909 5.43121 14.0458L5.43119 14.0457L5.41089 13.9315C5.3745 13.7239 5.34061 13.5176 5.30664 13.3108L5.30663 13.3108L5.30662 13.3107C5.29122 13.2169 5.2758 13.1231 5.26013 13.029C5.24715 12.9482 5.23385 12.8678 5.22056 12.7875L5.22056 12.7875C5.19593 12.6386 5.17137 12.4901 5.14903 12.3404C5.13184 12.2293 5.11465 12.1175 5.09746 12.0057C5.08026 11.8939 5.06307 11.7821 5.04588 11.671C5.04147 11.64 5.03677 11.6089 5.03207 11.5778C5.02266 11.5156 5.01326 11.4534 5.0062 11.3912C4.95859 10.9962 5.18606 10.5408 5.65423 10.4558C5.805 10.4283 5.95841 10.4174 6.10918 10.4503C6.33401 10.4969 6.49535 10.6286 6.59057 10.8453C6.63818 10.9523 6.66199 11.0648 6.68051 11.18C6.72018 11.4571 6.7625 11.7341 6.80482 12.0112L6.80482 12.0112L6.80482 12.0112L6.80482 12.0112C6.84714 12.291 6.88946 12.5708 6.93707 12.8507C6.98204 13.1277 7.02965 13.402 7.07726 13.6764C7.13809 14.0193 7.20158 14.3622 7.2677 14.7051C7.32589 15.0123 7.38937 15.3196 7.4555 15.6268C7.49978 15.8318 7.54626 16.0355 7.59282 16.2397L7.59282 16.2397C7.61856 16.3525 7.64432 16.4655 7.66975 16.5788C7.65808 16.5908 7.66082 16.6029 7.66526 16.6226C7.66585 16.6252 7.66648 16.628 7.6671 16.6309C7.70492 16.5555 7.74193 16.4825 7.77815 16.4112ZM26.9996 14.3924C26.9978 14.4432 26.9963 14.4942 26.9949 14.5454C26.9919 14.6463 26.989 14.7477 26.9837 14.8478C26.9705 15.059 26.9441 15.2702 26.907 15.4787C26.8568 15.7585 26.788 16.0329 26.6954 16.299C26.4997 16.8613 26.2325 17.3853 25.8702 17.8544C25.6665 18.115 25.4285 18.3427 25.1507 18.5183C24.9074 18.6719 24.6482 18.7761 24.3678 18.831C24.2567 18.8529 24.143 18.8694 24.0292 18.8721C24.0023 18.8721 23.9748 18.8743 23.9472 18.8765L23.9472 18.8765H23.9472C23.9126 18.8792 23.8779 18.8819 23.8441 18.8804C23.487 18.8639 23.1432 18.7926 22.8125 18.639C22.384 18.4414 22.0561 18.126 21.8074 17.7172C21.5667 17.3167 21.416 16.8805 21.3393 16.4169C21.3102 16.2331 21.2864 16.0466 21.2811 15.86C21.2803 15.8138 21.2791 15.7675 21.2779 15.7213C21.275 15.6057 21.272 15.4901 21.2758 15.3745C21.2837 15.1413 21.3049 14.9109 21.3393 14.6804C21.4001 14.2772 21.5006 13.8849 21.6461 13.5063C21.8339 13.018 22.0799 12.5626 22.4026 12.1539C22.6485 11.8411 22.9368 11.5805 23.2833 11.394C23.487 11.2843 23.7039 11.2074 23.9314 11.1636C24.1271 11.1251 24.3228 11.1142 24.5186 11.1251C24.7037 11.1361 24.8862 11.1663 25.0661 11.2184C25.2936 11.2815 25.5105 11.372 25.7115 11.4982C25.9575 11.6519 26.1638 11.8494 26.3357 12.088C26.5579 12.4008 26.7245 12.7437 26.833 13.114C26.8938 13.3252 26.9388 13.5392 26.9652 13.7559C26.9917 13.9644 27.0023 14.1784 26.9996 14.3924ZM25.32 14.535C25.3227 14.3869 25.3174 14.2442 25.2936 14.1043C25.2618 13.9096 25.2116 13.723 25.1454 13.5392C25.0846 13.3774 25.0105 13.221 24.8995 13.0866C24.7143 12.8671 24.4763 12.8123 24.2197 12.8946C24.0372 12.9522 23.8917 13.0729 23.7647 13.2155C23.5981 13.4048 23.4606 13.616 23.3415 13.841C23.085 14.3238 22.9527 14.8395 22.9501 15.3909C22.9501 15.5363 22.9527 15.6817 22.9739 15.8244C23.003 16.0191 23.0506 16.2084 23.1194 16.3922C23.1749 16.5458 23.241 16.694 23.3415 16.8229C23.4182 16.9244 23.5108 17.0122 23.6325 17.0561C23.7806 17.111 23.9314 17.0972 24.0769 17.0424C24.2461 16.9765 24.3916 16.8641 24.5239 16.7379C24.7725 16.502 24.9471 16.2112 25.0687 15.8902C25.2327 15.454 25.3253 15.0014 25.32 14.535ZM20.7777 14.5808C20.7797 14.5316 20.7815 14.484 20.7838 14.439C20.7826 14.4063 20.7815 14.3753 20.7804 14.3455C20.777 14.2477 20.774 14.1631 20.7679 14.0769C20.7573 13.8492 20.7282 13.6215 20.6806 13.3966C20.5907 12.9604 20.4294 12.5544 20.186 12.184C20.0273 11.9454 19.8369 11.7369 19.6068 11.5723C19.2153 11.2897 18.7762 11.1526 18.3054 11.1169C18.1017 11.1032 17.8954 11.1169 17.6944 11.1581C17.332 11.2294 17.004 11.383 16.7104 11.6134C16.4486 11.8192 16.229 12.0633 16.0386 12.3377C15.8244 12.6449 15.6471 12.9714 15.499 13.317C15.4144 13.5145 15.343 13.7203 15.2821 13.9288C15.2081 14.1921 15.1499 14.4609 15.1128 14.7325C15.0811 14.9492 15.0626 15.166 15.0573 15.3827C15.052 15.5994 15.052 15.8189 15.0732 16.0356C15.0917 16.2221 15.1155 16.4059 15.1552 16.5897C15.2451 17.0259 15.4064 17.4346 15.6498 17.8077C15.8085 18.0491 15.9989 18.2576 16.2317 18.4222C16.6523 18.724 17.1257 18.8529 17.6283 18.8749C17.6891 18.8776 17.7526 18.8721 17.8134 18.8666C17.831 18.8652 17.8486 18.8638 17.8663 18.8625C17.9499 18.8561 18.0346 18.8496 18.1176 18.8337C18.4562 18.7734 18.7683 18.6389 19.0513 18.4332C19.3661 18.2055 19.6226 17.9175 19.8395 17.591C20.0829 17.2262 20.2786 16.8339 20.432 16.4196C20.6039 15.9505 20.7097 15.465 20.7547 14.9657C20.7679 14.8348 20.773 14.7026 20.7777 14.5808ZM19.0619 14.0769C19.0883 14.2333 19.0963 14.3924 19.0936 14.546C19.1016 14.8039 19.0672 15.0535 19.0116 15.2949C18.9428 15.5967 18.8529 15.8902 18.7101 16.1645C18.554 16.4663 18.3451 16.7241 18.07 16.9162C17.9563 16.9957 17.8372 17.0588 17.6997 17.0808C17.5225 17.1082 17.3638 17.0725 17.2262 16.9491C17.0993 16.8366 17.0146 16.6967 16.9485 16.5431C16.848 16.3154 16.7792 16.0795 16.7448 15.8326C16.7316 15.7421 16.7237 15.6488 16.721 15.5555C16.7131 15.369 16.721 15.1824 16.7422 14.9986C16.7792 14.6886 16.8612 14.3896 16.9829 14.1043C17.0993 13.8327 17.25 13.5804 17.4272 13.3444C17.533 13.2045 17.6547 13.0756 17.8055 12.9823C17.9298 12.9028 18.0647 12.8561 18.2128 12.8589C18.3636 12.8616 18.4958 12.9165 18.6069 13.0207C18.6995 13.1085 18.7683 13.2128 18.8238 13.3252C18.9428 13.5612 19.0169 13.8135 19.0619 14.0769Z" fill="#7956AD"/></svg>',
				'label' => __( 'Template for WooCommerce page', 'jet-theme-core' ),
			]
		];

		return wp_parse_args( $additional, $details );
	}

	/**
	 * @param $template_details
	 * @param $template_id
	 * @param $type
	 *
	 * @return array|object
	 */
	public function structure_template_details( $template_details, $template_id, $type ) {
		$additional = [];
		$woo_structure_list = $this->get_structure_list();

		if ( in_array( $type, $woo_structure_list ) ) {
			$additional[] = 'woocommerce';
		}

		return wp_parse_args( $additional, $template_details );
	}

	/**
	 * @param $location
	 * @param $template_id
	 * @param $content_type
	 */
	public function before_location_render( $location, $template_id, $content_type ) {
		global $product;

		$location_map = [
			'single-product',
			'products-archive',
			'products-card',
		];

		if ( ! in_array( $location, $location_map ) ) {
			return false;
		}

		global $product, $post;

		if ( $product && ! is_a( $product, 'WC_Product' ) ) {
			$product = wc_get_product( $post );
		}

	}

	/**
	 * [__construct description]
	 */
	public function __construct() {

		if ( ! class_exists( 'WooCommerce' ) ) {
			return false;
		}

		//var_dump(is_wc_endpoint_url('orders'));

		$this->load_files();

		add_filter( 'jet-theme-core/template-conditions/conditions-group-list', [ $this, 'modify_template_conditions_group_list' ], 10, 2 );
		add_filter( 'jet-theme-core/template-conditions/condition-sub-groups', [ $this, 'modify_template_conditions_sub_group_list' ], 10, 2 );
		add_filter( 'jet-theme-core/template-conditions/conditions-list', [ $this, 'modify_template_conditions_list' ], 10, 2 );
		add_filter( 'jet-theme-core/template-structures/structures-list', [ $this, 'modify_template_structures_list' ], 10, 2 );
		add_filter( 'jet-theme-core/rest-api/endpoint-list', [ $this, 'modify_template_endpoint_list' ], 10, 2 );
		add_filter( 'jet-theme-core/admin/template-library/column-exclude-structures', [ $this, 'modify_template_column_exclude_structures' ], 10, 2 );
		add_filter( 'jet-theme-core/templates/template-details', [ $this, 'modify_template_details' ], 10, 2 );
		add_filter( 'jet-theme-core/templates-import/body-type-map', [ $this, 'modify_template_import_body_type_map' ], 10, 2 );
		add_filter( 'jet-theme-core/templates/structure-template-details', [ $this, 'structure_template_details' ], 10, 5 );
		add_action( 'jet-theme-core/theme-builder/render/location/before', [ $this, 'before_location_render' ], 10, 5 );
	}

}
