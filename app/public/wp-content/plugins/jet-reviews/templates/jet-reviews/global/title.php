<?php
/**
 * Review Header Title
 */

$title = $this->get_review_title();

if ( empty( $title ) ) {
	return false;
}

?><h4 class="jet-review__title"><?php echo $title; ?></h4>
