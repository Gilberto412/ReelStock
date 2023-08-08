import mitt from "mitt";

const emitter = mitt();

// Filters list after update
const emitFiltersListUpdated = (response) => {
	emitter.emit('filters-list-updated', response);
};
const onFiltersListUpdated = (callback) => {
	emitter.on('filters-list-updated', response => {
		callback(response);
	});
};

export default {
	emitter,
	emit: {
		filtersListUpdated: emitFiltersListUpdated,
	},
	on: {
		filtersListUpdated: onFiltersListUpdated,
	}
};