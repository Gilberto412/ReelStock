// Elementor popup show event
window.addEventListener('elementor/popup/show', event => {
	const id = event.detail.id;
	const instance = event.detail.instance;

	// removing the "jsf-filter" attributes for filters widgets reinitializing
	instance.$element.find('[jsf-filter]').removeAttr('jsf-filter');
});