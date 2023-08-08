<?php
/**
 * Review Header Average
 */

$settings      = $this->get_settings();
$total_average = $this->__get_total_average();


if ( ! $total_average ) {
	return false;
}

$review_data_length = count( $review_data );

if ( 0 == $total_average['percent'] ) {
	return false;
}

$layout        = ! empty( $settings['total_average_layout'] ) ? esc_attr( $settings['total_average_layout'] ) : 'points';
$val_pos       = ! empty( $settings['total_average_value_position'] ) ? esc_attr( $settings['total_average_value_position'] ) : 'above';
$progress      = isset( $settings['total_average_progressbar'] ) ? esc_attr( $settings['total_average_progressbar'] ) : 'yes';

if ( true === $total_average['valid'] ) {
	$val = $total_average['val'];
	$max = $total_average['max'];
} else {
	$val = $total_average['percent'];
	$max = 100;
}

?><div class="jet-review__total-average"><?php
	include $this->__get_global_template( 'total-average-layout/' . $layout );
?></div>
