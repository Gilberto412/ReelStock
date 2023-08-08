<?php
/**
 * Search Form template
 */

$post_type_string = $this->get_post_types_string();
$link_target_attr = ( isset( $settings['show_result_new_tab'] ) && ( 'yes' === $settings['show_result_new_tab'] || true === $settings['show_result_new_tab'] ) ) ? '_blank' : '';
$is_product_type  = 0;

if ( class_exists( 'WooCommerce' ) ) {
	$is_current_query = $this->get_settings( 'current_query' );
	$display_type     = get_option( 'woocommerce_shop_page_display', '' );

	if ( filter_var( $is_current_query, FILTER_VALIDATE_BOOLEAN ) ) {
		if ( is_shop() || is_product_taxonomy() || is_post_type_archive( 'product' ) ) {
			$is_product_type = 1;
		}
	}

	if ( 'subcategories' === $display_type || 'both' === $display_type ) {
		$post_type_string = '';
	}
}
?>

<form class="jet-ajax-search__form" method="get" action="<?php echo esc_url( home_url( '/' ) ); ?>" role="search" target="<?php echo $link_target_attr; ?>">
	<div class="jet-ajax-search__fields-holder">
		<div class="jet-ajax-search__field-wrapper">
			<?php $this->icon( 'search_field_icon', '<span class="jet-ajax-search__field-icon jet-ajax-search-icon">%s</span>' ); ?>
			<input class="jet-ajax-search__field" type="search" placeholder="<?php echo esc_attr( $settings['search_placeholder_text'] ); ?>" value="" name="s" autocomplete="off" />
			<input type="hidden" value="<?php echo $this->get_query_settings_json(); ?>" name="jet_ajax_search_settings" />

			<?php if ( ! empty( $post_type_string ) ) : ?>
				<input type="hidden" value="<?php echo $post_type_string; ?>" name="post_type" />
			<?php endif; ?>

			<?php if ( 1 === $is_product_type ) : ?>
				<input type="hidden" value="product" name="post_type" />
			<?php endif;?>
		</div>
		<?php echo $this->get_categories_list(); ?>
	</div>
	<?php $this->glob_inc_if( 'submit-button', array( 'show_search_submit' ) ); ?>
</form>
