<div id="<?php echo 'jet-reviews-advanced-' . $uniqid ;?>" class="jet-reviews-advanced" data-uniqid="<?php echo $uniqid; ?>">
	<div
		:class="instanceClass"
	>
		<div
			class="jet-reviews-advanced__loader"
			v-if="!reviewsLoaded"
		>
			<svg
				xmlns:svg="http://www.w3.org/2000/svg"
				xmlns="http://www.w3.org/2000/svg"
				xmlns:xlink="http://www.w3.org/1999/xlink"
				version="1.0"
				width="24px"
				height="25px"
				viewBox="0 0 128 128"
				xml:space="preserve"
			>
				<g>
					<linearGradient id="linear-gradient">
						<stop offset="0%" stop-color="#3a3a3a" stop-opacity="0"/>
						<stop offset="100%" stop-color="#3a3a3a" stop-opacity="1"/>
					</linearGradient>
				<path d="M63.85 0A63.85 63.85 0 1 1 0 63.85 63.85 63.85 0 0 1 63.85 0zm.65 19.5a44 44 0 1 1-44 44 44 44 0 0 1 44-44z" fill="url(#linear-gradient)" fill-rule="evenodd"/>
				<animateTransform attributeName="transform" type="rotate" from="0 64 64" to="360 64 64" dur="1080ms" repeatCount="indefinite"></animateTransform>
				</g>
			</svg>
		</div>

		<div
			class="jet-reviews-advanced__container"
			v-if="reviewsLoaded"
		>
			<div
				class="jet-reviews-advanced__header"
			>
				<div class="jet-reviews-advanced__header-top">
					<div class="jet-reviews-advanced__header-info">
						<div
							class="jet-reviews-advanced__header-title"
							v-if="0 !== reviewsTotal && 1 === reviewsTotal"
						>
							<span>{{ reviewsTotal }}</span>{{ options.labels.singularReviewCountLabel }}
                        </div>
						<div
							class="jet-reviews-advanced__header-title"
							v-if="0 !== reviewsTotal && 1 < reviewsTotal"
						>
							<span>{{ reviewsTotal }}</span>{{ options.labels.pluralReviewCountLabel }}</div>
						<div
							class="jet-reviews-advanced__header-title"
							v-html="options.labels.noReviewsLabel"
							v-if="0 === reviewsTotal"
						></div>
						<div
							v-if="userData.canReview.allowed"
							class="jet-reviews-button jet-reviews-button--primary"
							tabindex="0"
							@click="formVisibleToggle"
							@keyup.enter="formVisibleToggle"
						>
							<span class="jet-reviews-button__icon" v-if="addReviewIcon" v-html="addReviewIcon"></span>
							<span class="jet-reviews-button__text">{{ options.labels.newReviewButton }}</span>
						</div>
						<span class="jet-reviews-message" v-if="!userData.canReview.allowed">{{ userData.canReview.message }}</span>
					</div>

					<div
						class="jet-reviews-advanced__summary-rating"
						v-if="!reviewsListEmpty"
					>
						<component
							:is="$root.options.ratingLayout"
							:before="false"
							:rating="reviewsAverageRating"
							:after="'points-field' === $root.options.ratingLayout ? averageValue : false"
						></component>
					</div>
				</div>

				<jet-advanced-reviews-form
					v-if="formVisible"
					:review-fields="preparedFields"
				></jet-advanced-reviews-form>
			</div>

			<div
				class="jet-reviews-advanced__reviews"
				v-if="!reviewsListEmpty"
			>
				<transition-group name="fade" tag="div">
					<jet-advanced-reviews-item
						v-for="item in reviewsList"
						:key="item.id"
						:item-data="item"
					>
					</jet-advanced-reviews-item>
				</transition-group>
			</div>

			<jet-reviews-widget-pagination
				v-if="paginationVisible"
				:class="{ 'jet-progress-state': getReviewsProcessing }"
				:total="reviewsTotal"
				:page-size="options.pageSize"
				:prev-icon="refsHtml.prevIcon"
				:next-icon="refsHtml.nextIcon"
				@on-change="changePageHandle"
			></jet-reviews-widget-pagination>
		</div>
	</div>
	<?php
	    echo $widget_refs;
?></div>
