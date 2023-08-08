<?php
/**
 * Main dashboard template
 */
?><div id="jet-reviews-main-page" class="jet-reviews-admin-page jet-reviews-admin-page--main-page">
	<div class="jet-reviews-admin-page__header">
		<h1 class="wp-heading-inline"><?php _e( 'JetReviews', 'jet-reviews' ); ?></h1>
	</div>
	<hr class="wp-header-end">
	<div class="jet-reviews-admin-page__content">
		<h2 class="wp-heading-inline"><?php _e( 'General Review Stats', 'jet-reviews' ); ?></h2>
		<div class="quick-info">
			<div class="quick-info__item cx-vui-panel">
				<h3><?php _e( 'Reviews', 'jet-reviews' ); ?></h3>
				<span>{{ reviewCount }}</span>
			</div>
			<div class="quick-info__item cx-vui-panel">
				<h3><?php _e( 'Approved Reviews', 'jet-reviews' ); ?></h3>
				<span>{{ approvedReviewCount }}</span>
			</div>
			<div class="quick-info__item cx-vui-panel">
				<h3><?php _e( 'Not Approved Reviews', 'jet-reviews' ); ?></h3>
				<span>{{ notApprovedReviewCount }}</span>
			</div>

			<div class="quick-info__item cx-vui-panel">
				<h3><?php _e( 'Comments', 'jet-reviews' ); ?></h3>
				<span>{{ commentCount }}</span>
			</div>
			<div class="quick-info__item cx-vui-panel">
				<h3><?php _e( 'Approved Comments', 'jet-reviews' ); ?></h3>
				<span>{{ approvedCommentCount }}</span>
			</div>
			<div class="quick-info__item cx-vui-panel">
				<h3><?php _e( 'Not Approved Comments', 'jet-reviews' ); ?></h3>
				<span>{{ notApprovedCommentCount }}</span>
			</div>
		</div>

		<div class="stats-charts cx-vui-panel">
			<div class="row">
				<div class="stats-chart col col-3-4">
					<h3><?php _e( 'Reviews', 'jet-reviews' ); ?></h3>
					<general-reviews-line-chart></general-reviews-line-chart>
				</div>
				<div class="stats-chart col col-1-4">
					<h3><?php _e( 'Rating', 'jet-reviews' ); ?></h3>
					<doughnut-rating-chart
						:low="+reviewCountData.low"
						:medium="+reviewCountData.medium"
						:high="+reviewCountData.high"
					></doughnut-rating-chart>
				</div>
			</div>
		</div>

		<h2 class="wp-heading-inline"><?php _e( 'Post Types Review Stats', 'jet-reviews' ); ?></h2>
		<div class="cx-vui-panel">
			<cx-vui-tabs
				class="post-types"
				:in-panel="false"
				:value="activeTab"
				layout="vertical"
				@input="tabSwitch"
			>
				<cx-vui-tabs-panel
					class="post-types__item"
					v-for="( postType, index ) in postTypesData"
					:name="postType.slug"
					:label="postType.label"
					:key="postType.slug"
				>
					<div class="row">
						<div class="col col-2-3">
							<h3><?php _e( 'Reviews', 'jet-reviews' ); ?></h3>
							<post-type-reviews-line-chart
								class="post-type-chart"
								:data-sets="postType.dataSet"
							></post-type-reviews-line-chart>
						</div>
						<div class="col col-1-3">
							<h3><?php _e( 'Rating', 'jet-reviews' ); ?></h3>
							<doughnut-rating-chart
								class="post-type-chart"
								:low="+postType.reviewCount.low"
								:medium="+postType.reviewCount.medium"
								:high="+postType.reviewCount.high"
							></doughnut-rating-chart>
						</div>
						<div class="col col-4-4">
							<h3><?php _e( 'Details', 'jet-reviews' ); ?></h3>
							<div class="post-types__details">
								<div class="post-types__detail">
									<label><?php _e( 'Reviews:', 'jet-reviews' ); ?></label><span>{{ postType.reviewCount.all }}</span>
								</div>
								<div class="post-types__detail">
									<label><?php _e( 'Approved reviews:', 'jet-reviews' ); ?></label><span>{{ postType.approvedReviews }}</span>
								</div>
								<div class="post-types__detail">
									<label><?php _e( 'Not approved reviews:', 'jet-reviews' ); ?></label><span>{{ postType.reviewCount.all - postType.approvedReviews }}</span>
								</div>
								<div class="post-types__detail">
									<label><?php _e( 'Review type:', 'jet-reviews' ); ?></label><span>{{ postType.reviewType }}</span>
								</div>
								<div class="post-types__detail">
									<label><?php _e( 'Allowed:', 'jet-reviews' ); ?></label><span :style="{ color: postType.allowed ? '#46B450' : '#C92C2C' }">{{ postType.allowed ? 'yes' : 'no' }}</span>
								</div>
								<div class="post-types__detail">
									<label><?php _e( 'Reviews need approval:', 'jet-reviews' ); ?></label><span :style="{ color: postType.needApprove ? '#46B450' : '#C92C2C' }">{{ postType.needApprove ? 'yes' : 'no' }}</span>
								</div>
								<div class="post-types__detail">
									<label><?php _e( 'Comments allowed:', 'jet-reviews' ); ?></label><span :style="{ color: postType.commentsAllowed ? '#46B450' : '#C92C2C' }">{{ postType.commentsAllowed ? 'yes' : 'no' }}</span>
								</div>
								<div class="post-types__detail">
									<label><?php _e( 'Comments need approval:', 'jet-reviews' ); ?></label><span :style="{ color: postType.commentsNeedApprove ? '#46B450' : '#C92C2C' }">{{ postType.commentsNeedApprove ? 'yes' : 'no' }}</span>
								</div>
								<div class="post-types__detail">
									<label><?php _e( 'Likes/dislikes allowed:', 'jet-reviews' ); ?></label><span :style="{ color: postType.approvalAllowed ? '#46B450' : '#C92C2C' }">{{ postType.approvalAllowed ? 'yes' : 'no' }}</span>
								</div>
							</div>
						</div>
					</div>
				</cx-vui-tabs-panel>
			</cx-vui-tabs>
		</div>

	</div>
</div>
