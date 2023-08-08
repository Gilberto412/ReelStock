<template >
	<div class="jet-multilingual"
		 :class="{ 'jet-multilingual--loading': isLoading }">
		<template v-if="!data.posted">
			<h3 class="jet-multilingual-title">{{ texts.title }}</h3>
			<div class="jet-multilingual-current">
				<div class="jet-multilingual-current-label">{{ texts.currentLanguageLabel }}</div>
			</div>
			<div class="jet-multilingual-languages">
				<div v-for="(languageItem, languageKey) in languages"
					 class="jet-multilingual-language"
					 @click="onLanguageClick(languageKey)">
					<Switcher class="jet-multilingual-language-switcher"
							  :modelValue="languageSwitcher === languageKey" />
					<div class="jet-multilingual-language-label">
						<img class="jet-multilingual-language-label-flag"
							 :src="languageItem.flag_url" />
						{{ languageItem.english_name }}
					</div>
				</div>
			</div>
		</template>
		<template v-else>
			<Preloader :show="isLoading" />
			<h3 class="jet-multilingual-title">{{ texts.title }}</h3>
			<div class="jet-multilingual-current">
				<div class="jet-multilingual-current-label">{{ texts.currentLanguageLabel }}</div>
				<div class="jet-multilingual-current-value">
					<img class="jet-multilingual-current-value-flag"
						 :src="languageFlagURL" />
					{{ languageLabel }}
				</div>
			</div>
			<div class="jet-multilingual-translations">
				<div class="jet-multilingual-translations-label">{{ texts.translationsLabel }}</div>
				<div v-for="translation in translations"
					 class="jet-multilingual-translation">
					<div class="jet-multilingual-translation-label">
						<img class="jet-multilingual-translation-label-flag"
							 :src="translation.flagURL" />
						{{ translation.label }}
					</div>
					<div class="jet-multilingual-translation-action">
						<div v-if="translation.translatedfilterID"
							 class="jet-multilingual-translation-action-edit">
							<button class="jet-multilingual-translation-edit-link"
									@click="onEditTranslationClick(translation.translatedfilterID)">
								<svg viewBox="0 0 32 32">
									<path class="cls-1"
										  d="M2,31a1,1,0,0,1-1-1.11l.9-8.17a1,1,0,0,1,.29-.6L21.27,2.05a3.56,3.56,0,0,1,5.05,0L30,5.68a3.56,3.56,0,0,1,0,5.05L10.88,29.8a1,1,0,0,1-.6.29L2.11,31Zm8.17-1.91h0ZM3.86,22.28l-.73,6.59,6.59-.73L28.54,9.31a1.58,1.58,0,0,0,0-2.22L24.91,3.46a1.58,1.58,0,0,0-2.22,0Z" />
									<path class="cls-1"
										  d="M26.52,13.74a1,1,0,0,1-.7-.29L18.55,6.18A1,1,0,0,1,20,4.77L27.23,12a1,1,0,0,1,0,1.41A1,1,0,0,1,26.52,13.74Z" />
									<rect class="cls-1"
										  height="2"
										  transform="translate(-7.91 15.47) rotate(-45)"
										  width="12.84"
										  x="8.29"
										  y="16.28" />
								</svg>
							</button>
						</div>
						<div v-else
							 class="jet-multilingual-translation-action-add-new">
							<button class="jet-multilingual-translation-add-new-link"
									@click="onTranslateClick(translation.key)">
								<svg viewBox="0 0 32 32">
									<path
										  d="M28,14H18V4c0-1.104-0.896-2-2-2s-2,0.896-2,2v10H4c-1.104,0-2,0.896-2,2s0.896,2,2,2h10v10c0,1.104,0.896,2,2,2  s2-0.896,2-2V18h10c1.104,0,2-0.896,2-2S29.104,14,28,14z" />
								</svg>
							</button>
						</div>
					</div>
				</div>
			</div>
		</template>
	</div>
</template>

<script>
import { defineComponent, ref, computed, watchEffect } from "vue";
import Preloader from "@/components/Preloader.vue";
import controls from "@/modules/JetUI/controls";
import _object from "@/modules/helpers/object.js";
import mlService from "../services";

export default defineComponent({
	name: 'MultilingualEdit',

	components: {
		Preloader,
		Select: controls.Select,
		Switcher: controls.Switcher,
		ApproveDisapprove: controls.ApproveDisapprove,
	},

	props: {
		filterID: { type: [Boolean, Number, String], required: true }
	},

	setup(props, context) {
		// Data
		const texts = {
			title: 'Language',
			currentLanguageLabel: 'Language of this filter:',
			translationsLabel: 'Filter translations'
		};

		const languages = mlService.languages;
		const languageSwitcher = ref(mlService.defaultLanguage);
		const languageLabel = ref('');
		const languageFlagURL = ref('');
		const translations = ref('');
		const isLoading = ref(false);

		// Computed
		const data = computed(() => mlService.getFiterData(props.filterID));

		// Watchers
		watchEffect(() => {
			languageLabel.value = data.value.languageLabel;
			languageFlagURL.value = data.value.languageFlagURL;
			translations.value = data.value.translations;
		});

		// Actions
		const onLanguageClick = languageKey => {
			languageSwitcher.value = languageKey;
			mlService.сhangeFilterLanguageSetting(languageKey);
		};

		const onTranslateClick = languageKey => {
			isLoading.value = true;
			mlService.translatePost(props.filterID, languageKey);
		};

		const onEditTranslationClick = translatedfilterID => {
			mlService.goToTranslation(translatedfilterID);
		};

		return {
			texts,
			languages,
			languageSwitcher,
			languageLabel,
			languageFlagURL,
			translations,
			isLoading,

			data,

			onLanguageClick,
			onTranslateClick,
			onEditTranslationClick,
		};
	}
});
</script>