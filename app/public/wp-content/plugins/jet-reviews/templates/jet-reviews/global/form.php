<?php
/**
 * User Review Form
 */

if ( ! $this->is_user_can_add_review() ) {
	return false;
}

$settings = $this->get_settings();

$id_int = substr( $this->get_id_int(), 0, 3 );

$content_source = isset( $settings['content_source'] ) ? $settings['content_source'] : 'manually';

$review_fields = $this->__primary_review_data['review_fields'];

?><form class="jet-review__form">
	<div class="jet-review__form-inner">
		<h4 class="jet-review__form-title"><?php echo __( 'Leave a review', 'jet-reviews' ); ?></h4>
		<div class="jet-review__form-fields"><?php
			if ( ! empty( $review_fields ) ) {

				foreach ( $review_fields as $key => $field_data ) {
					$field_id = sprintf( 'jet-review-%s-fields-%s', $id_int, $key );
					$field_label = $field_data['field_label'];
					$field_max = (int) $field_data['field_max'];
					$field_name = sprintf( 'review_fields[%s][field_value]', $key );
					$field_value = round( $field_max / 2 );

					?><div class="jet-review__form-field type-range">
						<label for="<?php echo $field_id; ?>"><?php echo $field_label; ?></label>
						<input type="range" id="<?php echo $field_id; ?>" name="<?php echo $field_name; ?>" value="<?php echo $field_value; ?>" min="0" max="<?php echo $field_max; ?>">
						<span class="current-value"><?php echo $field_value; ?></span>
					</div><?php
				}
			}

			$field_title_id = sprintf( 'jet-review-%s-title', $id_int );
			$field_legend_id = sprintf( 'jet-review-%s-legend', $id_int );
			$field_text_id = sprintf( 'jet-review-%s-text', $id_int );

			?><div class="jet-review__form-field title-field">
				<label for="<?php echo $field_title_id; ?>"><?php echo __( 'Title', 'jet-reviews' ); ?></label>
				<input id="<?php echo $field_title_id; ?>" type="text" name="summary_title" placeholder="">
			</div><?php
				if ( 'post-meta' === $content_source ) {
				?><div class="jet-review__form-field legend-field">
					<label for="<?php echo $field_legend_id; ?>"><?php echo __( 'Legend', 'jet-reviews' ); ?></label>
					<input id="<?php echo $field_legend_id; ?>" type="text" name="summary_legend" placeholder="">
				</div><?php
			}
			?><div class="jet-review__form-field text-field">
				<label for="<?php echo $field_text_id; ?>"><?php echo __( 'Description', 'jet-reviews' ); ?></label>
				<textarea id="<?php echo $field_text_id; ?>" name="summary_text" placeholder="" rows="3"></textarea>
			</div>
		</div>
		<div class="jet-review__form-submit-container">
			<a href="#" class="elementor-button jet-review__form-submit"><div class="jet-review-spinner"></div><span><?php echo __( 'Submit Review', 'jet-reviews' ); ?></span></a>
		</div>
		<div class="jet-review__form-message"><span></span></div>
	</div>
</form>
