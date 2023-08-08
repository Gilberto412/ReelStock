<?php
/**
 * Search Form template
 */
$settings      = $this->get_settings_for_display();
$form_settings = $this->get_suggestions_settings_json();
$has_settings  = true;

if ( '[]' === $form_settings ){
	$has_settings = false;
}
?>

<form class="jet-search-suggestions__form" method="get" action="<?php echo esc_url( home_url( '/' ) ); ?>" role="search">
	<?php do_action( 'jet-search/search-suggestions/start-form', $this ); ?>
	<div class="jet-search-suggestions__fields-holder">
		<div class="jet-search-suggestions__field-wrapper">
			<input class="jet-search-suggestions__field" type="search" placeholder="<?php echo esc_attr( $settings['search_placeholder_text'] ); ?>" value="" name="s" autocomplete="off" />
			<?php if ( true === $has_settings ): ?>
			<input type="hidden" value="<?php echo $this->get_suggestions_settings_json(); ?>" name="jet_search_suggestions_settings" />
			<?php endif; ?>
			<?php if ( function_exists( 'WC' ) && isset( $settings['is_product_search'] ) && ( 'true' === $settings['is_product_search'] || true === $settings['is_product_search'] ) ) : ?>
				<input type="hidden" value="product" name="post_type" />
			<?php endif; ?>
		</div>
		<?php echo $this->get_categories_list(); ?>
	</div>
	<?php $this->glob_inc_if( 'submit-button', array( 'show_search_submit' ) ); ?>
	<?php do_action( 'jet-search/search-suggestions/end-form', $this ); ?>
</form>
