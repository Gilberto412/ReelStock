<?php
/**
 * Review fields template
 */

$data      = $review_data;
$settings  = $this->get_settings();
$layout    = ! empty( $settings['fields_layout'] ) ? esc_attr( $settings['fields_layout'] ) : 'points';
$value_pos = ! empty( $settings['fields_value_position'] ) ? esc_attr( $settings['fields_value_position'] ) : 'above';
$progress  = isset( $settings['fields_progressbar'] ) ? esc_attr( $settings['fields_progressbar'] ) : 'yes';

if ( empty( $data['review_fields'] ) ) {
	return;
}

$this->add_render_attribute( 'fields', 'class', 'jet-review__field' );
$this->add_render_attribute( 'fields', 'class', 'jet-layout-' . $layout );

if ( 'stars' === $layout ) {
	$stars_pos = ! empty( $settings['fields_stars_position'] ) ? $settings['fields_stars_position'] : 'right';
	$this->add_render_attribute( 'fields', 'class', 'jet-stars-position-'. $stars_pos );
}

?>
<div class="jet-review__fields">
<?php
foreach ( $data['review_fields'] as $field ) {

	$label = isset( $field['field_label'] ) ? $field['field_label'] : '';
	$val   = isset( $field['field_value'] ) ? floatval( $field['field_value'] ) : 0;
	$max   = isset( $field['field_max'] ) ? floatval( $field['field_max'] ) : 10;

	if ( ! $max ) {
		continue;
	}

	if ( $val > $max ) {
		$val = $max;
	}

	?>
	<div <?php echo $this->get_render_attribute_string( 'fields' ); ?>>
		<?php include $this->__get_global_template( 'field-layout/' . $layout ); ?>
	</div>
	<?php
}
?>
</div>
