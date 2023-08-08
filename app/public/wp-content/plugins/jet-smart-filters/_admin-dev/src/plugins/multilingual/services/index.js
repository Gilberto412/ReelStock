import { ref } from "vue";
import { useGetters, useActions } from "@/store/helper.js";
import events from "@/store/events.js";
import request from "@/services/request.js";
import filters from "@/services/filters.js";
import filter from "@/services/filter.js";
import popup from "@/services/popups.js";
import { getNesting, stringToBoolean } from "@/modules/helpers/utils.js";
import _array from "@/modules/helpers/array.js";

const {
	currentPage,
	columns,
	filtersListArgs,
	filterSavedData,
	filterDate
} = useGetters(['currentPage', 'columns', 'filtersListArgs', 'filterSavedData', 'filterDate']);

const {
	updateColumns,
	updateFiltersListArgs
} = useActions(['updateColumns', 'updateFiltersListArgs']);

export const defaultLanguage = getNesting(window.JetSmartFiltersAdminData, 'multilingual', 'default_language');
export const currentLanguage = ref(getNesting(window.JetSmartFiltersAdminData, 'multilingual', 'current_language'));
export const languages = getNesting(window.JetSmartFiltersAdminData, 'multilingual', 'languages');
export const quantity = ref({});

export function init() {
	// Add language to filters list args
	updateCurrentLanguage(currentLanguage.value);

	// Add multilingual column to filters list table
	const newColumns = { ...columns.value, };

	newColumns.multilingual = '';
	updateColumns(newColumns);

	// Events
	events.on.filtersListUpdated(response => {
		quantity.value = response.multilingual_quantity || {};
	});
}

export function changeLanguage(newLanguage) {
	// update current language
	updateCurrentLanguage(newLanguage, true);
}

export function getLanguageData(languageKey) {
	switch (languageKey) {
		case 'all':
			return {
				key: 'all',
				label: 'All languages',
				count: quantity.value.all || 0
			};

		default:
			return {
				key: languageKey,
				label: languages[languageKey].english_name,
				icon: languages[languageKey].flag_url,
				count: quantity.value[languageKey] || 0
			};
	}
}

function updateCurrentLanguage(newLanguage, updateList = false) {
	if (window.JetSmartFiltersAdminData.multilingual.current_language !== newLanguage)
		window.JetSmartFiltersAdminData.multilingual.current_language = newLanguage;

	if (currentLanguage.value !== newLanguage)
		currentLanguage.value = newLanguage;

	if (filtersListArgs.value.language !== newLanguage)
		if (updateList) {
			filters.updateListArg('language', newLanguage, true);
		} else {
			updateFiltersListArgs(
				Object.assign(
					filtersListArgs.value,
					{ language: newLanguage }
				)
			);
		}
}

// Filter
export function getFiterData(filterID = false) {
	const mlData = {
		language: currentLanguage.value,
		translations: [],
		availableLanguages: [],
		posted: stringToBoolean(filterDate.value),
	};

	let fiterData = {};
	let fiterTranslations = {};

	switch (currentPage.value) {
		case 'filters':
			if (filterID)
				fiterData = filters.getById(filterID) || {};

			break;

		case 'filter':
			fiterData = filterSavedData.value;

			break;
	}

	if (fiterData.language)
		mlData.language = fiterData.language;

	if (fiterData.translations)
		fiterTranslations = fiterData.translations;

	mlData.languageLabel = getNesting(languages, mlData.language, 'english_name');
	mlData.languageFlagURL = getNesting(languages, mlData.language, 'flag_url');

	Object.keys(languages).forEach(languageKey => {
		if (mlData.language && mlData.language !== languageKey) {
			mlData.translations.push({
				key: languageKey,
				label: languages[languageKey].english_name,
				flagURL: languages[languageKey].flag_url,
				translatedfilterID: fiterTranslations.hasOwnProperty(languageKey)
					? fiterTranslations[languageKey]
					: null
			});

			if (!fiterTranslations.hasOwnProperty(languageKey))
				mlData.availableLanguages.push({
					value: languageKey,
					label: languages[languageKey].english_name
				});
		}
	});

	return mlData;
};

export function сhangeFilterLanguageSetting(languageKey) {
	filter.сhangeSetting('_language_code', languageKey);
}

export function translatePost(postID, languageKey) {
	request.fetchJson(request.endpoints.FilterAddTranslation, {
		id: postID,
		language: languageKey
	}).then(response => {
		goToTranslation(response);
	});
}

export function goToTranslation(translatedfilterID) {
	switch (currentPage.value) {
		case 'filters':
			filters.toFilter(translatedfilterID);

			break;

		case 'filter':
			filter.toFilter(translatedfilterID);

			break;
	}
}

// Popup
export function popupEdit(filterID) {
	popup.open('multilingualEdit', { data: filterID });
}

export default {
	defaultLanguage,
	currentLanguage,
	languages,
	quantity,
	init,
	changeLanguage,
	getLanguageData,
	getFiterData,
	сhangeFilterLanguageSetting,
	translatePost,
	goToTranslation,
	popupEdit,
};