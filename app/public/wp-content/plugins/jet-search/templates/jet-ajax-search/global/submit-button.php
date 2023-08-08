<?php
/**
 * Submit Button template
 */
?>

<button class="jet-ajax-search__submit" type="submit" aria-label="search submit"><?php
	$this->icon( 'search_submit_icon', '<span class="jet-ajax-search__submit-icon jet-ajax-search-icon">%s</span>' );
	$this->html( 'search_submit_label', '<span class="jet-ajax-search__submit-label">%s</span>' );
?></button>
