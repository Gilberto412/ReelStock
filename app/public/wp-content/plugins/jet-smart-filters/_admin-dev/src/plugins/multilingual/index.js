import TableCell from "./components/TableCell.vue";
import MultilingualBlock from "./components/MultilingualBlock.vue";
import PopupMultilingualBlock from "./components/PopupMultilingualBlock.vue";
import LanguagesSelector from "./components/LanguagesSelector.vue";
import mlService from "./services";
import { isNestingExist } from "@/modules/helpers/utils.js";

export default {
	install: (app, options) => {
		const isEnabled = isNestingExist(window.JetSmartFiltersAdminData, 'multilingual');

		// if WPML Multilingual plugin activate
		if (isEnabled) {
			// Init
			mlService.init();
		}

		// Providers
		app.provide('multilingual', isEnabled
			? {
				currentLanguage: mlService.currentLanguage,
				languages: mlService.languages
			}
			: false
		);

		// Components
		app.component('ML_TableCell', TableCell);
		app.component('ML_Block', MultilingualBlock);
		app.component('ML_Popup', PopupMultilingualBlock);
		app.component('ML_LanguagesSelector', LanguagesSelector);
	}
};