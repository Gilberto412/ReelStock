<template >
	<div class="jsf_languages-selector"
		 :class="{ 'jsf_opened': opened }"
		 ref="elLanguagesSelector">
		<!-- Value -->
		<div class="jsf_languages-selector-value"
			 @click="switchState">
			<LanguageItem :data="currentLanguageData" />
		</div>
		<!-- Dropdown -->
		<div class="jsf_languages-selector-dropdown">
			<LanguageItem v-for="languageData of languagesData"
						  :key="languageData.key"
						  :data="languageData"
						  @click="onLanguageClick(languageData.key)" />
		</div>
	</div>
</template>

<script>
import { defineComponent, ref, computed } from "vue";
import useDropDown from "@/modules/JetUI/composables/dropdown.js";
import services from "../services";

const LanguageItem = defineComponent({
	props: {
		data: { type: Object, required: true },
	},
	template: `
		<div class="jsf_languages-selector-item"
			 :class="'jsf_languages-selector-item-' + data.key">
			<img v-if="data.icon"
				 class="jsf_languages-selector-item-flag"
				 :src="data.icon" />
			<div class="jsf_languages-selector-item-label">{{ data.label }}</div>
			<div class="jsf_languages-selector-item-count">({{ data.count }})</div>
		</div>
	`
});

export default defineComponent({
	name: 'LanguagesSelector',

	components: {
		LanguageItem
	},

	setup(props, context) {
		const elLanguagesSelector = ref(null);

		const currentLanguage = services.currentLanguage;
		const languages = services.languages;

		const currentLanguageData = computed(() => services.getLanguageData(currentLanguage.value));
		const languagesData = computed(() => ['all', ...Object.keys(languages)]
			.filter(languageKey => languageKey !== currentLanguage.value)
			.map(languageKey => services.getLanguageData(languageKey))
		);

		// Dropdown
		const dropdown = useDropDown({
			areaElement: elLanguagesSelector
		});

		// Actions
		const onLanguageClick = newLanguage => {
			services.changeLanguage(newLanguage);
			dropdown.close();
		};

		return {
			elLanguagesSelector,

			currentLanguageData,
			languagesData,

			// dropdown
			...dropdown,

			// Actions
			onLanguageClick,
		};
	}
});

</script>