<?php
/**
 * Review user data
 */

if ( ! isset( $review_data['user_id'] ) ) {
	return false;
}

if ( ! filter_var( $this->get_settings( 'show_review_author' ), FILTER_VALIDATE_BOOLEAN ) ) {
	return false;
}

$user_id = $review_data['user_id'];

$user_data = $this->get_user_data_by_id( $user_id );

?><div class="jet-review__user">
	<div class="jet-review__user-inner">
		<div class="jet-review__user-avatar"><?php
			echo $this->get_user_avatar( $user_data->user_email );
		?></div>
		<div class="jet-review__user-info">
			<div class="jet-review__user-name"><?php
				echo sprintf( '<span>%s</span>', $user_data->user_nicename );
			?></div>
			<div class="jet-review__user-mail"><?php
				echo sprintf( '<span>%s</span>', $user_data->user_email );
			?></div><?php
				$this->render_review_date( $review_data );
		?></div>
	</div>
</div>
