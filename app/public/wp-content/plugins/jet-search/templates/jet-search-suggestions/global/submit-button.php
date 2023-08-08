<?php
/**
 * Submit Button template
 */
?>

<button class="jet-search-suggestions__submit" type="submit" aria-label="search submit"><?php
	$this->icon( 'search_submit_icon', '<span class="jet-search-suggestions__submit-icon jet-search-suggestions-icon">%s</span>' );
	$this->html( 'search_submit_label', '<span class="jet-search-suggestions__submit-label">%s</span>' );
?></button>
