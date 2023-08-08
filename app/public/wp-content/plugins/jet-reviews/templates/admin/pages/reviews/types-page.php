<?php
/**
 * Review Types List template
 */
?><div id="jet-reviews-types-page" class="jet-reviews-admin-page jet-reviews-admin-page--review-types">
	<div class="jet-reviews-admin-page__header">
		<h1 class="wp-heading-inline"><?php _e( 'Review Types', 'jet-reviews' ); ?></h1>
		<span
			class="page-title-action"
			@click="showAddTypePopup"
		><?php
			_e( 'Add New', 'jet-reviews' );
		?></span>
	</div>
	<hr class="wp-header-end">
	<div class="jet-reviews-admin-page__content">
		<cx-vui-list-table
			:is-empty="0 === itemsList.length"
			empty-message="<?php _e( 'No Types found', 'jet-reviews' ); ?>"
			class="jet-reviews-admin-page__table jet-reviews-admin-page__table--review-types"
			:class="{ 'loading-status': deletingStatus }"
		>
			<cx-vui-list-table-heading
				:slots="[ 'name', 'slug', 'fields', 'actions' ]"
				slot="heading"
			>
				<div slot="name"><?php _e( 'Name', 'jet-reviews' ); ?></div>
				<div slot="slug"><?php _e( 'Slug', 'jet-reviews' ); ?></div>
				<div slot="fields"><?php _e( 'Fields', 'jet-reviews' ); ?></div>
				<div slot="actions"><?php _e( 'Actions', 'jet-reviews' ); ?></div>
			</cx-vui-list-table-heading>
			<cx-vui-list-table-item
				:slots="[ 'name', 'slug', 'fields', 'actions' ]"
				slot="items"
				v-for="item in itemsList"
				:key="item.id"
			>
				<div slot="name">{{ item.name }}</div>
				<div slot="slug">{{ item.slug }}</div>
				<div slot="fields">{{ generateFieldsList( item.fields )}}</div>
				<div slot="actions" v-if="'default' !== item.slug">
					<span
						class="edit-action"
						@click='openEditTypePopup( item.id )'
					><?php
						_e( 'Edit Fields', 'jet-reviews' );
					?></span>
					<span>|</span>
					<span
						class="delete-action"
						@click='openDeleteTypePopup( item.id )'
					><?php
						_e( 'Delete', 'jet-reviews' );
					?></span>
				</div>
			</cx-vui-list-table-item>
		</cx-vui-list-table>
	</div>

	<transition name="popup">
		<cx-vui-popup
			class="jet-reviews-admin-page__popup"
			v-model="addTypePopupVisible"
			body-width="1000px"
			:ok-label="'<?php _e( 'Add Type', 'jet-reviews' ) ?>'"
			:cancel-label="'<?php _e( 'Cancel', 'jet-reviews' ) ?>'"
			@on-ok="addTypeHandle"
		>
			<div
				class="cx-vui-subtitle"
				slot="title"
			><?php
				_e( 'Add New Review Type', 'jet-reviews' );
			?></div>
			<div
				:class="{ 'loading-status': creatingStatus }"
				slot=content
			>

				<cx-vui-input
					name="type-name"
					label="<?php _e( 'Name', 'jet-reviews' ); ?>"
					description="<?php _e( 'Enter review type name', 'jet-reviews' ); ?>"
					:wrapper-css="[ 'equalwidth' ]"
					size="fullwidth"
					type="text"
					v-model="tempTypeData['name']"
				>
				</cx-vui-input>

				<div class="cx-vui-component cx-vui-component--equalwidth">
					<div class="cx-vui-component__meta">
						<div class="cx-vui-component__label"><?php _e( 'Type Fields', 'jet-reviews' ); ?></div>
						<div class="cx-vui-component__desc"><?php _e( 'Review type fields', 'jet-reviews' ); ?></div>
					</div>
					<div class="cx-vui-component__control">
						<cx-vui-repeater
							class="cx-vui-repeater__half-col"
							button-label="Add New Field"
							button-style="accent"
							button-size="mini"
							v-model="tempTypeData['fields']"
							@add-new-item="addNewField"
						>

							<cx-vui-repeater-item
								v-for="( field, index ) in tempTypeData['fields']"
								:title="tempTypeData['fields'][ index ].label"
								:index="index"
								@clone-item="cloneField( $event )"
								@delete-item="deleteField( $event )"
								:key="index">

								<cx-vui-input
									label="<?php _e( 'Label', 'jet-reviews' ); ?>"
									:wrapper-css="[ 'equalwidth' ]"
									type="text"
									:size="'fullwidth'"
									v-model="tempTypeData['fields'][ index ].label"
								></cx-vui-input>

								<cx-vui-input
									label="<?php _e( 'Step', 'jet-reviews' ); ?>"
									:wrapper-css="[ 'equalwidth' ]"
									type="number"
									:size="'fullwidth'"
									:min="0"
									v-model="tempTypeData['fields'][ index ].step"
								></cx-vui-input>

								<cx-vui-input
									label="<?php _e( 'Max Value', 'jet-reviews' ); ?>"
									:wrapper-css="[ 'equalwidth' ]"
									type="number"
									:size="'fullwidth'"
									:min="0"
									:step="1"
									v-model="tempTypeData['fields'][ index ].max"
								></cx-vui-input>

							</cx-vui-repeater-item>

						</cx-vui-repeater>
					</div>
				</div>
			</div>
		</cx-vui-popup>
	</transition>

	<transition name="popup">
		<cx-vui-popup
			class="jet-reviews-admin-page__popup"
			v-model="editPopupVisible"
			body-width="1000px"
			:ok-label="'<?php _e( 'Save', 'jet-reviews' ) ?>'"
			:cancel-label="'<?php _e( 'Cancel', 'jet-reviews' ) ?>'"
			@on-ok="saveTypeHandle"
		>
			<div
				class="cx-vui-subtitle"
				slot="title"
			><?php
				_e( 'Edit Review Fields', 'jet-reviews' );
			?></div>
			<div
				:class="{ 'loading-status': updatingStatus }"
				slot=content
			>
				<!-- <cx-vui-input
					label="<?php _e( 'Name', 'jet-reviews' ); ?>"
					:wrapper-css="[ 'equalwidth' ]"
					type="text"
					:size="'fullwidth'"
					v-model="tempTypeData.label"
				></cx-vui-input> -->

				<cx-vui-repeater
					class="cx-vui-repeater__half-col"
					button-label="Add New Field"
					button-style="accent"
					button-size="mini"
					v-model="tempTypeData['fields']"
					@add-new-item="addNewField"
				>

					<cx-vui-repeater-item
						v-for="( field, index ) in tempTypeData['fields']"
						:title="tempTypeData['fields'][ index ].label"
						:index="index"
						@clone-item="cloneField( $event )"
						@delete-item="deleteField( $event )"
						:key="index">

						<cx-vui-input
							label="<?php _e( 'Label', 'jet-reviews' ); ?>"
							:wrapper-css="[ 'equalwidth' ]"
							type="text"
							:size="'fullwidth'"
							v-model="tempTypeData['fields'][ index ].label"
						></cx-vui-input>

						<cx-vui-input
							label="<?php _e( 'Step', 'jet-reviews' ); ?>"
							:wrapper-css="[ 'equalwidth' ]"
							type="number"
							:size="'fullwidth'"
							:min="0"
							v-model="tempTypeData['fields'][ index ].step"
						></cx-vui-input>

						<cx-vui-input
							label="<?php _e( 'Max Value', 'jet-reviews' ); ?>"
							:wrapper-css="[ 'equalwidth' ]"
							type="number"
							:size="'fullwidth'"
							:min="0"
							:step="1"
							v-model="tempTypeData['fields'][ index ].max"
						></cx-vui-input>

					</cx-vui-repeater-item>

				</cx-vui-repeater>
			</div>
		</cx-vui-popup>
	</transition>

	<transition name="popup">
		<cx-vui-popup
			class="jet-reviews-admin-page__popup"
			v-model="deletePopupVisible"
			body-width="350px"
			:ok-label="'<?php _e( 'Delete', 'jet-reviews' ) ?>'"
			:cancel-label="'<?php _e( 'Cancel', 'jet-reviews' ) ?>'"
			@on-ok="deleteTypeHandle"
		>
			<div class="cx-vui-subtitle" slot="title"><?php _e( 'Please confirm type deletion', 'jet-reviews' ); ?></div>
		</cx-vui-popup>
	</transition>
</div>
