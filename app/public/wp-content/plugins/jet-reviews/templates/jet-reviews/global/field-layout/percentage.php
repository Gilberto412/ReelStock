<?php
/**
 * Points layout template
 */
?>
<div class="jet-review__field-heading">
	<div class="jet-review__field-label"><?php
		echo $label;
		$this->__html( $settings, 'fields_label_suffix', '<span class="jet-review__field-label-suffix">%s</span>' );
	?></div>
	<?php if ( 'above' === $value_pos ) : ?>
	<div class="jet-review__field-val"><?php echo round( ( 100 * $val ) / $max, 0 ) . '%'; ?></div>
	<?php endif; ?>
</div>
<?php
	if ( 'yes' === $progress ) {
		echo $this->__get_progressbar( $val, $max, $value_pos, 'percentage' );
	}
?>