<template>
	<div
		class="jet-theme-builder-form jet-theme-builder-form--template-library-form"
		:class="itemClasses"
	>
		<div class="jet-theme-builder-form__header">
			<div class="jet-theme-builder-form__header-title"><span>Template Library</span><spinnerLoader v-if="progressState"/></div>
			<p class="jet-theme-builder-form__header-sub-title" v-if="isStructureTemplatesEmpty"><span class="capitalize-format">{{ $store.state.layoutStructureType}}</span> templates for not found. Create a template to use for further pages.</p>
			<p class="jet-theme-builder-form__header-sub-title" v-if="!isStructureTemplatesEmpty">Here you can select a custom <span class="capitalize-format">{{ $store.state.layoutStructureType}}</span> template and apply it to the needed page template. Such a custom template will override the default one from the current theme.</p>
		</div>
		<div class="jet-theme-builder-form__body">

			<div class="empty-template-library" v-if="isStructureTemplatesEmpty && !progressState">
				<cx-vui-button
					button-style="default"
					class="cx-vui-button--style-accent"
					size="mini"
					@on-click="createTemplateHandler"
				>
					<template v-slot:label>
						<span>Create template</span>
					</template>
				</cx-vui-button>
			</div>
			<div class="template-library" v-if="!isStructureTemplatesEmpty">
				<div class="template-library__list">
					<div
						:class="[ 'template-library__item', ! templateData.editLink ? 'not-editable' : '' ]"
						v-for="(templateData, index) in structureTemplates"
						:key="index"
						:templateData="templateData"
					>
						<div class="template-library__item-header">
							<VTooltip
								:triggers="['hover', 'focus']"
								v-if="!templateData.editLink"
							>
								<div class="svg-icon warning">
									<svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
										<path d="M4.47 21H19.53C21.07 21 22.03 19.33 21.26 18L13.73 4.99C12.96 3.66 11.04 3.66 10.27 4.99L2.74 18C1.97 19.33 2.93 21 4.47 21ZM12 14C11.45 14 11 13.55 11 13V11C11 10.45 11.45 10 12 10C12.55 10 13 10.45 13 11V13C13 13.55 12.55 14 12 14ZM13 18H11V16H13V18Z" fill="black"/>
									</svg>
								</div>
								<template #popper>
									<span>Template cannot be used, please install required content editor</span>
								</template>
							</VTooltip>
							<div class="template-library__item-name">{{ templateData.title }}</div>
						</div>
						<div class="template-library__item-details">
							<div class="template-library__item-details-label">Properties:</div>
							<div class="template-library__item-details-list">
								<div
									:class="[ 'template-library__item-detail' ]"
									v-for="(detail, index) in templateData.details"
									:key="index"
								>
									<VTooltip
										:triggers="['hover', 'focus']"
									>
										<div class="template-library__item-detail-icon" v-html="getTemplateDetail( detail ).icon"></div>
										<template #popper>
											<div class="template-library__item-detail-label" v-html="getTemplateDetail( detail ).label"></div>
										</template>
									</VTooltip>
								</div>
							</div>
						</div>
						<div class="template-library__item-meta">
							<div class="template-library__item-meta-item template-date">
								<svg class="svg-icon" width="15" height="15" viewBox="0 0 15 15" fill="none" xmlns="http://www.w3.org/2000/svg">
									<path d="M11.5 2.89773H11V2.38636C11 2.10511 10.775 1.875 10.5 1.875C10.225 1.875 10 2.10511 10 2.38636V2.89773H5V2.38636C5 2.10511 4.775 1.875 4.5 1.875C4.225 1.875 4 2.10511 4 2.38636V2.89773H3.5C2.95 2.89773 2.5 3.35795 2.5 3.92045V12.1023C2.5 12.6648 2.95 13.125 3.5 13.125H11.5C12.05 13.125 12.5 12.6648 12.5 12.1023V3.92045C12.5 3.35795 12.05 2.89773 11.5 2.89773ZM11 12.1023H4C3.725 12.1023 3.5 11.8722 3.5 11.5909V5.45455H11.5V11.5909C11.5 11.8722 11.275 12.1023 11 12.1023Z" fill="#CACBCD"/>
								</svg>
								<span>{{ templateData.date.format }}</span>
							</div>
							<div class="template-library__item-meta-item template-author">
								<svg class="svg-icon"  width="15" height="15" viewBox="0 0 15 15" fill="none" xmlns="http://www.w3.org/2000/svg">
									<path d="M7.5 7.5C8.88125 7.5 10 6.38125 10 5C10 3.61875 8.88125 2.5 7.5 2.5C6.11875 2.5 5 3.61875 5 5C5 6.38125 6.11875 7.5 7.5 7.5ZM7.5 8.75C5.83125 8.75 2.5 9.5875 2.5 11.25V11.875C2.5 12.2188 2.78125 12.5 3.125 12.5H11.875C12.2188 12.5 12.5 12.2188 12.5 11.875V11.25C12.5 9.5875 9.16875 8.75 7.5 8.75Z" fill="#CACBCD"/>
								</svg>
								<span>{{ templateData.author.name }}</span>
							</div>
						</div>
						<div class="template-library__item-controls">
							<cx-vui-button
								button-style="default"
								class="cx-vui-button--style-link-accent"
								size="mini"
								@on-click="editTemplateHandler( templateData.editLink )"
							>
								<template v-slot:label>
									<span class="svg-icon">
										<svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
											<path d="M2.5 14.3751V17.5001H5.625L14.8417 8.28346L11.7167 5.15846L2.5 14.3751ZM17.2583 5.8668C17.5833 5.5418 17.5833 5.0168 17.2583 4.6918L15.3083 2.7418C14.9833 2.4168 14.4583 2.4168 14.1333 2.7418L12.6083 4.2668L15.7333 7.3918L17.2583 5.8668Z" fill="#007CBA"/>
										</svg>
									</span>
									<span>Edit</span>
								</template>
							</cx-vui-button>
							<cx-vui-button
								button-style="default"
								class="cx-vui-button--style-link-accent"
								size="mini"
								@on-click="addTemplateHandler( templateData.id )"
							>
								<template v-slot:label>
									<span class="svg-icon">
										<svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
											<path d="M15.8332 10.8337H10.8332V15.8337H9.1665V10.8337H4.1665V9.16699H9.1665V4.16699H10.8332V9.16699H15.8332V10.8337Z" fill="#007CBA"/>
										</svg>
									</span>
									<span>Use</span>
								</template>
							</cx-vui-button>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</template>

<script>
import spinnerLoader from './spinnerLoader.vue';

export default {
	name: 'templateLibraryForm',
	components: {
		spinnerLoader,
	},
	data() {
		return {
			progressState: false,
			structureTemplates: [],
		}
	},
	mounted() {
		this.getStructureTemplatesRequest();
	},
	computed: {
		itemClasses() {
			return [
				//this.progressState ? 'progress-state' : ''
			];
		},
		isStructureTemplatesEmpty() {
			return 0 == this.structureTemplates.length ? true : false;
		},
	},
	methods: {
		getContentTypeIcon( contentType ) {
			let templateContentTypeIcons = window.JetThemeBuilderConfig.templateContentTypeIcons;

			return templateContentTypeIcons.hasOwnProperty( contentType ) ? templateContentTypeIcons[ contentType ] : false;
		},
		getTemplateDetail( detail ) {
			let templateDetailsData = window.JetThemeBuilderConfig.templateDetailsData;

			return templateDetailsData.hasOwnProperty( detail ) ? templateDetailsData[ detail ] : false;
		},
		addTemplateHandler( templateId = false ) {
			this.$store.commit( 'updateTemplateId', {
				id: templateId,
			} );

			this.$store.dispatch( 'updatePageTemplateStructureId', {
				id: templateId,
			} );

			this.$store.dispatch( 'closeTemplateLibraryPopup' );
		},
		editTemplateHandler( editLink = '#' ) {
			window.open( editLink, '_blank' ).focus();
		},
		createTemplateHandler() {
			this.$store.dispatch( 'closeTemplateLibraryPopup' );
			this.$store.dispatch( 'openCreateTemplatePopup' );
		},
		getStructureTemplatesRequest() {
			this.progressState = true;

			wp.apiFetch( {
				method: 'post',
				data: {
					type: this.$store.state.templateStructureType
				},
				path: window.JetThemeBuilderConfig.getStructureTemplatesPath
			} ).then( ( response ) => {
				this.progressState = false;

				if ( response.success ) {
					this.structureTemplates = response.data;
				}
			} );
		},
	}
}
</script>

<style lang="scss">

	.empty-template-library {
		display: flex;
		justify-content: center;
		align-items: center;
	}

	.template-library__list {
		display: grid;
		grid-template-columns: repeat(4, 1fr);
		grid-template-rows: auto;
		gap: 20px;

		@media (max-width: 1439px) {
			grid-template-columns: repeat(3, 1fr);
		}

		@media (max-width: 1023px) {
			grid-template-columns: repeat(2, 1fr);
		}
	}

	.template-library__item-header {
		display: flex;
		align-items: center;
		gap: 5px;
		padding: 12px 12px 0 12px;
		margin-bottom: 6px;
		flex: 1 1 auto;
		color: var(--primary-text-color);
		font-size: 15px;

		.svg-icon.warning {
			width: 20px;

			svg, path {
				fill: var(--warning-color);
			}
		}
	}

	.template-library__item-meta {
		display: flex;
		justify-content: flex-start;
		align-items: center;
		gap: 12px;
		padding: 8px 12px 8px 12px;
		border-top: 1px solid #F0F0F1;

		&-item {
			display: flex;
			justify-content: flex-start;
			align-items: center;
			gap: 4px;
			color: var(--secondary-text-color);
			font-size: 13px;

			.svg-icon {
				width: 20px;
				height: auto;

				svg, path {
					fill: var(--border-color);
				}
			}
		}
	}

	.template-library__item-details {
		display: flex;
		flex-direction: column;
		justify-content: flex-start;
		align-items: stretch;
		padding: 0 12px 12px 12px;

		&-label {
			margin-bottom: 2px;
		}
	}

	.template-library__item-details-list {
		display: flex;
		justify-content: flex-start;
		align-items: center;
		gap: 4px;
	}

	.template-library__item-detail {
		display: flex;
		justify-content: flex-start;
		gap: 4px;
	}

	.template-library__item-detail-icon {
		display: flex;
		justify-content: flex-start;
		align-items: flex-start;

		svg {
			width: 20px;
			height: auto;
		}
	}

	.template-library__item-content-icon {
		display: flex;
		justify-content: center;
		align-items: center;
		gap: 3px;
		width: 20px;

		svg, path {
			fill: var(--border-color);
		}
	}

	.template-library__item {
		display: flex;
		flex-direction: column;
		border-radius: 4px;
		background-color: white;
		border: 1px solid #F0F0F1;

		&-name {
			color: var( --primary-text-color );
		}

		&-controls {
			display: flex;
			justify-content: space-between;
			align-items: center;
			background-color: #F0F0F1;
			padding: 0 12px;
		}

		&-control {
			display: flex;
			justify-content: center;
			align-items: center;
			gap: 5px;
			cursor: pointer;
			color: var( --primary-text-color );

			.dashicons {
				color: var( --primary-text-color );
			}
		}

		&.not-editable {
			.cx-vui-button {
				pointer-events: none;
				opacity: 0.5;
			}
		}
	}
</style>

