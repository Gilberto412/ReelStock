<?php
/**
 * Points layout template
 */

if ( 'yes' !== $progress || 'inside' !== $val_pos ) {
	?><div class="jet-review__total-average-val"><?php echo $total_average['percent'] . '%'; ?></div><?php
}

if ( 'yes' === $progress ) {
	echo $this->__get_progressbar( $val, $max, $val_pos, 'percentage' );
}
