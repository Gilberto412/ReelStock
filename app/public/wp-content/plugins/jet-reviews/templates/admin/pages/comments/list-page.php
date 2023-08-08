<?php
/**
 * Review List template
 */
?><div id="jet-reviews-comments-list-page" class="jet-reviews-admin-page jet-reviews-admin-page--comments-list">
	<div class="jet-reviews-admin-page__header">
		<h1 class="wp-heading-inline"><?php _e( 'Comment List', 'jet-reviews' ); ?></h1>
	</div>
	<hr class="wp-header-end">
	<div class="jet-reviews-admin-page__filters">
        <div class="jet-reviews-admin-page__filter">
            <label for="cx_search-review-input"><?php _e( 'Bulk Actions', 'jet-reviews' ); ?></label>
            <div class="jet-reviews-admin-page__filter-form">
                <cx-vui-select
                    name="bulk-action"
                    :wrapper-css="[ 'equalwidth' ]"
                    size="fullwidth"
                    :prevent-wrap="true"
                    :options-list="[
                        {
                            value: '',
                            label: '<?php _e( 'Select action', 'jet-reviews' ); ?>'
                        },
                        {
                            value: 'unapprove',
                            label: '<?php _e( 'Unapprove', 'jet-reviews' ); ?>'
                        },
                        {
                            value: 'approve',
                            label: '<?php _e( 'Approve', 'jet-reviews' ); ?>'
                        },
                        {
                            value: 'delete',
                            label: '<?php _e( 'Delete', 'jet-reviews' ); ?>'
                        }
                    ]"
                    v-model="bulkAction">
                </cx-vui-select>
                <cx-vui-button
                        button-style="accent-border"
                        size="mini"
                        @click="bulkActionHandle"
                        :loading="bulkActionStatus"
                >
                    <span slot="label"><?php _e( 'Apply', 'jet-reviews' ); ?></span>
                </cx-vui-button>
            </div>
        </div>
		<div class="jet-reviews-admin-page__filter">
			<label for="cx_search-review-input"><?php _e( 'Search Comments', 'jet-reviews' ); ?></label>
			<div class="jet-reviews-admin-page__filter-form">
				<cx-vui-input
					name="search-comment-input"
					:wrapper-css="[ 'equalwidth' ]"
					size="fullwidth"
					:prevent-wrap="true"
					type="text"
					v-model="searchText"
				>
				</cx-vui-input>
				<cx-vui-button
					button-style="accent-border"
					size="mini"
					@click="searchCommentHandle"
					:loading="searchingState"
				>
					<span slot="label"><?php _e( 'Search', 'jet-reviews' ); ?></span>
				</cx-vui-button>
			</div>
		</div>
	</div>

	<div class="jet-reviews-admin-page__content">
		<cx-vui-list-table
			:is-empty="0 === itemsList.length"
			empty-message="<?php _e( 'No reviews found', 'jet-reviews' ); ?>"
			class="jet-reviews-admin-page__table jet-reviews-admin-page__table--comments"
			:class="{ 'loading-status': commentsGetting || actionExecution }"
		>
			<cx-vui-list-table-heading
				:slots="[ 'check', 'author', 'content', 'post', 'date', 'actions' ]"
				slot="heading"
			>
                <div slot="check">
                    <cx-vui-switcher
                        name="bulk-check"
                        :prevent-wrap="true"
                        :return-true="true"
                        :return-false="false"
                        v-model="bulkCheck"
                    >
                    </cx-vui-switcher>
                </div>
				<div slot="author"><?php _e( 'Author', 'jet-reviews' ); ?></div>
				<div slot="content"><?php _e( 'Comment', 'jet-reviews' ); ?></div>
				<div slot="post"><?php _e( 'Assigned to', 'jet-reviews' ); ?></div>
				<div slot="date"><?php _e( 'Date', 'jet-reviews' ); ?></div>
				<div slot="actions"><?php _e( 'Actions', 'jet-reviews' ); ?></div>
			</cx-vui-list-table-heading>
			<cx-vui-list-table-item
				:class="{ 'not-approved': ! item.approved }"
				:slots="[ 'check', 'author', 'content', 'post', 'date', 'actions' ]"
				slot="items"
				v-for="( item, index ) in itemsList"
				:key="index"
			>
                <div slot="check">
                    <cx-vui-switcher
                        name="`bulk-check-${index}`"
                        :prevent-wrap="true"
                        :return-true="true"
                        :return-false="false"
                        v-model="item.check"
                    >
                    </cx-vui-switcher>
                </div>
				<div slot="author">
					<div class="author-data">
						<a class="author-data__avatar" :href="item.author.url" v-html="item.author.avatar"></a>
						<div class="author-data__info">
							<b>{{ item.author.name }}</b>
							<i>{{ item.author.mail }}</i>
							<div class="author-data__roles" v-html="getRolesLabel( item.author.roles )"></div>
						</div>
					</div>
				</div>
				<div slot="content" v-html="item.content"></div>
				<div slot="post">
					<i>{{ item.post.type }}: </i><a class="link" target="_blank" :href="item.post.link">{{ item.post.title }}</a>
				</div>
				<div slot="date">{{ item.date.raw }}</div>
				<div slot="actions">
					<span
						class="approve-action"
						@click='approveHandler( [ item.id ] )'
					>
						<span v-if="item.approved" :style="{ color: '#d98500'}"><?php _e( 'Unapprove', 'jet-reviews' ); ?></span>
						<span v-if="!item.approved" :style="{ color: '#46B450'}"><?php _e( 'Approve', 'jet-reviews' ); ?></span>
					</span>
					<span>|</span>
					<span
						class="edit-action"
						@click="openEditPopup( index )"
					><?php
						_e( 'Edit', 'jet-reviews' );
					?></span>
					<span>|</span>
					<span
						class="delete-action"
						@click="deleteCommentHandle( [ item.id ] )"
					><?php
						_e( 'Delete', 'jet-reviews' );
					?></span>
				</div>
			</cx-vui-list-table-item>
		</cx-vui-list-table>
		<div
			class="jet-reviews-admin-page__pagination"
			v-if="0 !== itemsList.length"
		>
			<cx-vui-pagination
				:total="commentsCount"
				:page-size="pageSize"
				@on-change="changePage"
			></cx-vui-pagination>
		</div>
	</div>

	<transition name="popup">
		<cx-vui-popup
			class="jet-reviews-admin-page__popup"
			v-model="editPopupVisible"
			:header="false"
			:footer="false"
			body-width="600px"
		>
			<div class="cx-vui-subtitle" slot="title"><?php _e( 'Edit Comment', 'jet-reviews' ); ?></div>
			<div
				slot="content"
			>

				<cx-vui-textarea
					name="comment-content"
					label="<?php _e( 'Comment', 'jet-reviews' ); ?>"
					:wrapper-css="[ 'equalwidth' ]"
					size="fullwidth"
					v-model="editCommentData['content']"
					:rows="9"
				>
				</cx-vui-textarea>

				<div class="cx-vui-popup__controls">
					<cx-vui-button
						button-style="accent-border"
						size="mini"
						@click="saveCommentHandle"
						:loading="commentSavingState"
					>
						<span slot="label"><?php _e( 'Save', 'jet-menu' ); ?></span>
					</cx-vui-button>
				</div>

			</div>
		</cx-vui-popup>
	</transition>
</div>
