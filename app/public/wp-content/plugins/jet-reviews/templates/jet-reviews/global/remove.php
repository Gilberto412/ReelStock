<?php
$settings = $this->get_settings();

$content_source = isset( $settings['content_source'] ) ? $settings['content_source'] : 'manually';

$curent_user_id = $this->get_curent_user_id();

if ( ! $curent_user_id || 'post-meta' !== $content_source ) {
	return false;
}

$user_review_id = $review_data['user_id'];

if ( ! current_user_can('administrator') ) {
	return false;
}

?><div class="jet-review__item-remove"><div class="jet-review-remove-spinner"></div><i class="fa fa-trash"></i></div>
