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
	<?php echo $this->__get_stars( $val, $max ); ?>
</div>