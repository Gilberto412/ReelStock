<div
	class="jet-reviews-settings-page jet-reviews-settings-page__post-types"
>
	<cx-vui-tabs
		:in-panel="false"
		:value="activeTab"
		layout="horizontal"
		@input="tabSwitch"
	>

		<cx-vui-tabs-panel
			v-for="( postData, index ) in avaliablePostTypes"
			:name="`${postData.value}-post-type-settings`"
			:label="pageOptions[`${postData.value}-type-settings`]['name']"
			:key="`${postData.value}-post-type-settings`"
		>
			<cx-vui-switcher
				:name="`allowed-post-type-${postData.value}`"
				label="<?php _e( 'Use review for post type', 'jet-reviews' ); ?>"
				description="<?php _e( 'Allow this type of post to use JetReviews', 'jet-reviews' ); ?>"
				:wrapper-css="[ 'equalwidth' ]"
				:return-true="true"
				:return-false="false"
				v-model="pageOptions[`${postData.value}-type-settings`]['allowed']"
			>
			</cx-vui-switcher>

			<cx-vui-component-wrapper
				:wrapper-css="[ 'fullwidth-control' ]"
				:conditions="[
					{
						input: pageOptions[`${postData.value}-type-settings`]['allowed'],
						compare: 'equal',
						value: true,
					}
				]"
			>
				<cx-vui-select
					:name="`${postData.value}-review-type`"
					label="<?php _e( 'Review type', 'jet-reviews' ); ?>"
					description="<?php _e( 'Choose review type for post type', 'jet-reviews' ); ?>"
					:wrapper-css="[ 'equalwidth' ]"
					size="fullwidth"
					:options-list="reviewTypeOptions"
					v-model="pageOptions[`${postData.value}-type-settings`]['review_type']">
				</cx-vui-select>

				<cx-vui-f-select
					:name="`${postData.value}-allowed-roles`"
					label="<?php _e( 'Allowed roles', 'jet-reviews' ); ?>"
					:wrapper-css="[ 'equalwidth' ]"
					:placeholder="'Select...'"
					:multiple="true"
					:options-list="allRolesOptions"
                    autocomplete="<?php echo jet_reviews_tools()->generate_rand_string(); ?>"
					v-model="pageOptions[`${postData.value}-type-settings`]['allowed_roles']"
				></cx-vui-f-select>

				<cx-vui-f-select
					v-if="verificationVisible"
					:name="`${postData.value}-review-verifications`"
					label="<?php _e( 'Review author verification', 'jet-reviews' ); ?>"
					description="<?php _e( 'Choose review author verification types', 'jet-reviews' ); ?>"
					:wrapper-css="[ 'equalwidth' ]"
					:placeholder="'Select...'"
					:multiple="true"
					:options-list="verificationOptions"
                    autocomplete="<?php echo jet_reviews_tools()->generate_rand_string(); ?>"
					v-model="pageOptions[`${postData.value}-type-settings`]['verifications']">
				</cx-vui-f-select>

				<cx-vui-f-select
					v-if="verificationVisible"
					:name="`${postData.value}-review-comment-verifications`"
					label="<?php _e( 'Comment author verification', 'jet-reviews' ); ?>"
					description="<?php _e( 'Choose сomment author verification types', 'jet-reviews' ); ?>"
					:wrapper-css="[ 'equalwidth' ]"
					:placeholder="'Select...'"
					:multiple="true"
					:options-list="verificationOptions"
                    autocomplete="<?php echo jet_reviews_tools()->generate_rand_string(); ?>"
					v-model="pageOptions[`${postData.value}-type-settings`]['comment_verifications']">
				</cx-vui-f-select>

				<cx-vui-switcher
					:name="`need-approve-review-${postData.value}`"
					label="<?php _e( 'New review approval', 'jet-reviews' ); ?>"
					description="<?php _e( 'Need admin approval for a new review', 'jet-reviews' ); ?>"
					:wrapper-css="[ 'equalwidth' ]"
					:return-true="true"
					:return-false="false"
					v-model="pageOptions[`${postData.value}-type-settings`]['need_approve']"
				>
				</cx-vui-switcher>

				<cx-vui-switcher
					:name="`review-comments-allowed-${postData.value}`"
					label="<?php _e( 'Allow comments', 'jet-reviews' ); ?>"
					description="<?php _e( 'Allow review comments for this type of post', 'jet-reviews' ); ?>"
					:wrapper-css="[ 'equalwidth' ]"
					:return-true="true"
					:return-false="false"
					v-model="pageOptions[`${postData.value}-type-settings`]['comments_allowed']"
				>
				</cx-vui-switcher>

				<cx-vui-switcher
					:name="`review-comments-need-approve-${postData.value}`"
					label="<?php _e( 'New review comments need approval', 'jet-reviews' ); ?>"
					description="<?php _e( 'Need admin approval for a new review comment', 'jet-reviews' ); ?>"
					:wrapper-css="[ 'equalwidth' ]"
					:return-true="true"
					:return-false="false"
					v-model="pageOptions[`${postData.value}-type-settings`]['comments_need_approve']"
				>
				</cx-vui-switcher>

				<cx-vui-switcher
					:name="`review-approval-allowed-${postData.value}`"
					label="<?php _e( 'Allow review rate actions', 'jet-reviews' ); ?>"
					description="<?php _e( 'Allow likes/dislikes for review items for this type of post', 'jet-reviews' ); ?>"
					:wrapper-css="[ 'equalwidth' ]"
					:return-true="true"
					:return-false="false"
					v-model="pageOptions[`${postData.value}-type-settings`]['approval_allowed']"
				>
				</cx-vui-switcher>

			</cx-vui-component-wrapper>

			<cx-vui-switcher
				:name="`metadata-${postData.value}`"
				label="<?php _e( 'Post metadata', 'jet-reviews' ); ?>"
				description="<?php _e( 'Use post metadata for jet reviews data', 'jet-reviews' ); ?>"
				:wrapper-css="[ 'equalwidth' ]"
				:return-true="true"
				:return-false="false"
				v-model="pageOptions[`${postData.value}-type-settings`]['metadata']"
			>
			</cx-vui-switcher>

			<cx-vui-component-wrapper
				:wrapper-css="[ 'fullwidth-control' ]"
				:conditions="[
					{
						input: pageOptions[`${postData.value}-type-settings`]['metadata'],
						compare: 'equal',
						value: true,
					}
				]"
			>
				<cx-vui-input
					:name="`metadata-key-${postData.value}`"
					label="<?php _e( 'Average rating metadata key', 'jet-reviews' ); ?>"
					description="<?php _e( 'Define meta key, which will store the average post rating.', 'jet-reviews' ); ?>"
					:wrapper-css="[ 'equalwidth' ]"
					size="fullwidth"
					v-model="pageOptions[`${postData.value}-type-settings`]['metadata_rating_key']"
				>
				</cx-vui-input>

				<cx-vui-input
					:name="`metadata-ratio-bound-${postData.value}`"
					type="number"
					label="<?php _e( 'Post meta ratio bound', 'jet-reviews' ); ?>"
					description="<?php _e( 'Specify ratio conversion limit for post metadata.', 'jet-reviews' ); ?>"
					:wrapper-css="[ 'equalwidth' ]"
					size="fullwidth"
					:min="1"
					:max="100"
					v-model="pageOptions[`${postData.value}-type-settings`]['metadata_ratio_bound']"
				>
				</cx-vui-input>

				<div class="cx-vui-component cx-vui-component--equalwidth">
					<div class="cx-vui-component__meta">
						<label class="cx-vui-component__label"><?php _e( 'Sync average rating', 'jet-reviews' ); ?></label>
						<div class="cx-vui-component__desc"><?php _e( 'Add/Update jet-rating data to post metadata', 'jet-reviews' ); ?></div>
					</div>
					<div class="cx-vui-component__control">
						<cx-vui-button
							button-style="accent-border"
							size="mini"
							:loading="syncPostmetaStatus"
							@click="syncRatingData(postData.value)"
						>
							<span slot="label"><?php _e( 'Sync rating data', 'jet-reviews' ); ?></span>
						</cx-vui-button>
					</div>
				</div>
			</cx-vui-component-wrapper>

            <cx-vui-switcher
                    :name="`structuredata-${postData.value}`"
                    label="<?php _e( 'Structure Data', 'jet-reviews' ); ?>"
                    description="<?php _e( 'Rendering structure data in JSON-LD format', 'jet-reviews' ); ?>"
                    :wrapper-css="[ 'equalwidth' ]"
                    :return-true="true"
                    :return-false="false"
                    v-model="pageOptions[`${postData.value}-type-settings`]['structuredata']"
            >
            </cx-vui-switcher>

            <cx-vui-component-wrapper
                    :wrapper-css="[ 'fullwidth-control' ]"
                    :conditions="[
					{
						input: pageOptions[`${postData.value}-type-settings`]['structuredata'],
						compare: 'equal',
						value: true,
					}
				]"
            >
                <cx-vui-select
                    :name="`${postData.value}-structuredata-type`"
                    label="<?php _e( 'Type', 'jet-reviews' ); ?>"
                    description="<?php _e( 'Choose structure data type', 'jet-reviews' ); ?>"
                    :wrapper-css="[ 'equalwidth' ]"
                    size="fullwidth"
                    :options-list="structureDataTypesOptions"
                    v-model="pageOptions[`${postData.value}-type-settings`]['structuredata_type']">
                </cx-vui-select>

            </cx-vui-component-wrapper>

		</cx-vui-tabs-panel>

	</cx-vui-tabs>
</div>
