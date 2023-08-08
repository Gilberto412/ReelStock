<?php
/**
 * Review summary template
 */
$data     = $review_data;
$average  = $this->__get_average( $data );
$settings = $this->get_settings();
$layout   = ! empty( $settings['summary_layout'] ) ? esc_attr( $settings['summary_layout'] ) : 'points';
$val_pos  = ! empty( $settings['summary_value_position'] ) ? esc_attr( $settings['summary_value_position'] ) : 'above';
$progress = isset( $settings['summary_progressbar'] ) ? esc_attr( $settings['summary_progressbar'] ) : 'yes';
$result_p = ! empty( $settings['summary_result_position'] ) ? $settings['summary_result_position'] : 'right';
$result_a = ! empty( $settings['summary_average_alignment'] ) ? $settings['summary_average_alignment'] : 'center';

if ( true === $average['valid'] ) {
	$val = $average['val'];
	$max = $average['max'];
} else {
	$val = $average['percent'];
	$max = 100;
}

$this->add_render_attribute( 'summary_result', 'class', 'jet-review__summary' );
$this->add_render_attribute( 'summary_result', 'class', 'jet-review-summary-' . esc_attr( $result_p ) );
$this->add_render_attribute( 'summary_result', 'class', 'jet-review-summary-align-' . esc_attr( $result_a ) );

?>
<div <?php echo $this->get_render_attribute_string( 'summary_result' ); ?>>
	<div class="jet-review__summary-content"><?php
		$this->__html( $data, 'summary_title', '<h5 class="jet-review__summary-title">%s</h5>' );
		$this->__html( $data, 'summary_text', '<div class="jet-review__summary-text">%s</div>' );
	?></div>
	<div class="jet-review__summary-data"><?php
		include $this->__get_global_template( 'summary-layout/' . $layout );
		$this->__html( $data, 'summary_legend', '<div class="jet-review__summary-legend">%s</div>' );
	?></div>
</div>
