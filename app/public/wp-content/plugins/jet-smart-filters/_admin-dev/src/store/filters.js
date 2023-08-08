// State
const state = {
	columns: { title: 'Title', type: 'Type', source: 'Data Source', date: 'Date' },
	filteredColumns: {
		date: { type: 'date' },
		type: {
			type: 'checkboxes',
			options: [
				{ key: 'checkboxes', value: 'Checkboxes', checked: false },
				{ key: 'select', value: 'Select', checked: false },
				{ key: 'range', value: 'Range', checked: false },
				{ key: 'rating', value: 'Rating', checked: false }
			]
		}
	},
	filtersList: [],
	filtersListArgs: {
		pagination: {
			page: 1,
			totalPages: 1,
			count: 0,
			perPage: 20
		},
		search: '',
		type: '',
		source: '',
		sort: false
	},
	filterTypes: { ...window.JetSmartFiltersAdminData.filter_types },
	filterSources: { ...window.JetSmartFiltersAdminData.filter_sources },
	sortByList: { ...window.JetSmartFiltersAdminData.sort_by_list },
	submenuList: [
		{
			type: 'filters',
			label: 'All filters',
			count: 0,
		},
		{
			type: 'trash',
			label: 'Trash',
			count: 0,
		}
	],
	isFiltersListLoading: true,
};

// Getters
const getters = {
	columns: state => { return state.columns; },
	filteredColumns: state => { return state.filteredColumns; },
	filtersList: state => { return state.filtersList; },
	filtersListArgs: state => { return state.filtersListArgs; },
	filterTypes: state => { return state.filterTypes; },
	filterSources: state => { return state.filterSources; },
	sortByList: state => { return state.sortByList; },
	submenuList: state => { return state.submenuList; },
	isFiltersListLoading: state => { return state.isFiltersListLoading; },
};

// Actions
const actions = {
	updateColumns: ({ commit }, columns) => {
		commit('updateState', {
			name: 'columns',
			data: columns
		});
	},

	updateFiltersList: ({ commit }, filtersList) => {
		commit('updateState', {
			name: 'filtersList',
			data: filtersList
		});
	},

	updateFiltersListArgs: ({ commit }, filtersListArgs) => {
		commit('updateState', {
			name: 'filtersListArgs',
			data: filtersListArgs
		});
	},

	updateSubmenuList: ({ commit }, submenuList) => {
		commit('updateState', {
			name: 'submenuList',
			data: submenuList
		});
	},

	updateIsFiltersListLoading: ({ commit }, value) => {
		commit('updateState', {
			name: 'isFiltersListLoading',
			data: value
		});
	},
};

export default {
	state,
	getters,
	actions
};