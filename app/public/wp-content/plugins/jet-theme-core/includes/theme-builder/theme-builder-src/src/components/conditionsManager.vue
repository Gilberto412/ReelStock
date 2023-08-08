<template>
	<div
		:class="itemClasses"
	>
		<div class="jet-theme-builder-conditions-manager__container">
			<div class="jet-theme-builder-conditions-manager__header" v-if="!isPageTemplateCreateMode">
				<div class="jet-theme-builder-conditions-manager__header-title">Set the page template visibility conditions</div>
				<div class="jet-theme-builder-conditions-manager__header-message">
					<span>Here you can set one or multiple conditions, according to which the given template will be shown on specific pages or not.</span>
				</div>
			</div>
			<div class="jet-theme-builder-conditions-manager__header" v-if="isPageTemplateCreateMode">
				<div class="jet-theme-builder-conditions-manager__header-title">Create page template</div>
				<div class="jet-theme-builder-conditions-manager__header-message">
					<span>Here you can set one or multiple conditions, according to which the given template will be shown on specific pages or not.</span>
				</div>
			</div>
			<div class="jet-theme-builder-conditions-manager__loader" v-if="getConditionsStatus">
				<spinnerLoader/>
			</div>
			<div class="jet-theme-builder-conditions-manager__list" v-if="!getConditionsStatus">
				<div class="jet-theme-builder-conditions-manager__list-inner" v-if="!emptyConditions">
					<transition-group name="conditions-list-anim" tag="div">
						<conditionsItem
							v-for="сondition in templateConditions"
							:key="сondition.id"
							:id="сondition.id"
							:rawCondition="сondition"
							@remove-condition="removeCondition"
						></conditionsItem>
					</transition-group>
				</div>
				<div class="jet-theme-builder-conditions-manager__condition-controls">
					<cx-vui-button
						button-style="default"
						class="cx-vui-button--style-link-accent"
						size="mini"
						@on-click="addCondition"
					>
						<template v-slot:label>
							<span class="svg-icon">
								<svg width="21" height="20" viewBox="0 0 21 20" fill="none" xmlns="http://www.w3.org/2000/svg">
									<path d="M16.3332 10.8334H11.3332V15.8334H9.6665V10.8334H4.6665V9.16675H9.6665V4.16675H11.3332V9.16675H16.3332V10.8334Z" fill="#007CBA"/>
								</svg>
							</span>
							<span>Add Condition</span>
						</template>
					</cx-vui-button>
					<div class="jet-theme-builder-conditions-manager__condition-control">
						<div class="cx-vui-component__label">Relation Type</div>
						<cx-vui-select
							:prevent-wrap="true"
							:options-list="[
							{
								value: 'or',
								label: 'OR'
							},
							{
								value: 'and',
								label: 'AND'
							}
						]"
							:value="relationType"
							@on-input="relationType=$event"
						></cx-vui-select>
					</div>
				</div>
				<div class="jet-theme-builder-conditions-manager__controls">
					<cx-vui-button
						button-style="default"
						class="cx-vui-button--style-accent-border"
						size="mini"
						@on-click="cancelHandler"
					>
						<template v-slot:label>
							<span>Cancel</span>
						</template>
					</cx-vui-button>
					<cx-vui-button
						button-style="default"
						class="cx-vui-button--style-accent"
						v-if="!isPageTemplateCreateMode"
						:loading="$store.state.updatePageTemplatesProgressState"
						size="mini"
						@on-click="saveConditions"
					>
						<template v-slot:label>
							<span>Save</span>
						</template>
					</cx-vui-button>
					<cx-vui-button
						button-style="default"
						class="cx-vui-button--style-accent"
						v-if="isPageTemplateCreateMode"
						:loading="saveProgressState"
						size="mini"
						@on-click="createPageTemplate"
					>
						<template v-slot:label>
							<span>Create</span>
						</template>
					</cx-vui-button>
				</div>
			</div>
		</div>
	</div>
</template>

<script>
import conditionsItem from './conditionsItem.vue';
import spinnerLoader from './spinnerLoader.vue';

export default {
	name: 'conditionsManager',
	components: {
		conditionsItem,
		spinnerLoader
	},
	data() {
		return {
			rawConditionsData: window.JetThemeBuilderConfig.rawConditionsData,
			conditions: [],
			relationType: 'or',
			getConditionsStatus: false,
			saveProgressState: false,
		}
	},
	created() {
		this.getConditions();
	},
	computed: {
		itemClasses() {
			return [
				'jet-theme-builder-conditions-manager',
			];
		},
		emptyConditions() {
			return ( 0 === this.conditions.length ) ? true : false;
		},
		templateConditions() {
			return this.conditions;
		},
		isPageTemplateCreateMode() {
			return ! this.$store.state.pageTemplateId ? true : false;
		}
	},
	methods: {
		generateUniqId: function() {
			return '_' + Math.random().toString(36).substr(2, 9);
		},

		addCondition: function() {
			let newCond = {
				id: this.generateUniqId(),
				include: 'true',
				group: 'entire',
				subGroup: 'entire',
				subGroupValue: '',
				subGroupValueVerbose: '',
				priority: 100,
			};

			this.conditions.unshift( newCond );
		},

		removeCondition: function( conditionId = false ) {
			this.conditions = this.conditions.filter( function( condition ) {
				return condition['id'] !== conditionId;
			} );
		},

		getConditions: function () {

			if ( ! this.$store.state.pageTemplateId ) {
				return false;
			}

			this.getConditionsStatus = true;

			wp.apiFetch( {
				method: 'post',
				path: window.JetThemeBuilderConfig.getPageTemplateConditionsPath,
				data: {
					template_id: this.$store.state.pageTemplateId,
				},
			} ).then( ( response ) => {
				this.getConditionsStatus = false;

				if ( response.success ) {
					this.conditions = response.data.conditions;
					this.relationType = response.data.relationType;

					this.$store.commit( 'updateEditablePageTemplateConditions', {
						conditions: this.conditions,
					} );
				} else {
					console.log('getPageTemplateConditions')
				}
			} );
		},

		saveConditions: function() {

			this.$store.commit( 'updateEditablePageTemplateConditions', {
				conditions: this.templateConditions,
			} );

			this.$store.dispatch( 'updatePageTemplateConditions', {
				conditions: this.templateConditions,
				relationType: this.relationType,
			} );

			this.$store.dispatch( 'closeConditionsPopup' );
		},

		cancelHandler: function() {
			this.$store.dispatch( 'closeConditionsPopup' );
		},

		createPageTemplate() {
			this.saveProgressState = true;

			wp.apiFetch( {
				method: 'post',
				path: window.JetThemeBuilderConfig.createPageTemplatePath,
				data: {
					name: '',
					conditions: this.conditions,
					relationType: this.relationType,
				},
			} ).then( ( response ) => {
				this.saveProgressState = false;

				if ( response.success ) {
					this.$store.commit( 'updateRawPageTemplateList', {
						list: response.data.list,
					} );

					this.$store.commit( 'updatePageTemplateId', {
						id: response.data.newTemplateId,
					} );

					this.$store.dispatch( 'updatePageTemplateConditions', {
						conditions: this.templateConditions,
						relationType: this.relationType,
					} );

					this.$store.dispatch( 'closeConditionsPopup' );
				}
			} );
		}
	}
}
</script>

<style lang="scss">

.jet-theme-builder-conditions-manager {
	display: flex;
	flex-direction: column;
	align-items: stretch;

	&__container {
		display: flex;
		flex-direction: column;
		align-items: stretch;
	}

	&__controls {
		display: flex;
		justify-content: flex-end;
		align-items: center;
		gap: 16px;
		margin-top: 16px;
		border-top: 1px solid #E0E0E0;
		padding-top: 32px;
	}

	&__header {
		display: flex;
		flex-direction: column;
		align-items: center;
		text-align: center;
		margin-bottom: 32px;

		&-title {
			font-style: normal;
			font-size: 21px;
			color: var(--primary-text-color);
			margin-bottom: 10px;
		}

		&-message {
			display: flex;
			flex-direction: column;
			color: #7b7e81;
			max-width: 600px;
		}
	}

	&__list {
		display: flex;
		flex-direction: column;
		align-items: stretch;
	}

	&__list-inner {
		margin-bottom: 24px;

		& > div {
			display: flex;
			flex-direction: column;
			align-items: stretch;
			gap: 16px;
		}
	}

	&__loader {
		display: flex;
		justify-content: center;
	}

	&__condition-controls {
		display: flex;
		justify-content: space-between;
		align-items: center;
	}

	&__condition-control {
		display: flex;
		justify-content: flex-start;
		align-items: center;
		gap: 12px;
		min-width: 200px;

		.cx-vui-component-raw {
			flex: 1 1 auto;

			select {
				width: 100%;
			}
		}
	}
}

.conditions-list-enter-active,
.conditions-list-leave-active {
	transition: all .3s cubic-bezier(.35,.77,.38,.96);
}
.conditions-list-enter-from {
	opacity: 0;
}
.conditions-list-leave-to {
	opacity: 0;
	transition-duration: 0s;
}
.conditions-list-move {
	transition: all .3s cubic-bezier(.35,.77,.38,.96);
}

</style>
