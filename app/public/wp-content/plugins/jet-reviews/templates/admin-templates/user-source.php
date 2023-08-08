<div
	class="jet-reviews-settings-page jet-reviews-settings-page__user-source"
>
    <cx-vui-switcher
            name="allowed-user-source"
            label="<?php _e( 'Use review for user source', 'jet-reviews' ); ?>"
            description="<?php _e( 'Allows you to use reviews for user source', 'jet-reviews' ); ?>"
            :wrapper-css="[ 'equalwidth' ]"
            :return-true="true"
            :return-false="false"
            v-model="pageOptions['user-wp-user-source-settings']['allowed']"
    >
    </cx-vui-switcher>

    <cx-vui-component-wrapper
        :wrapper-css="[ 'fullwidth-control' ]"
        :conditions="[
            {
                input: pageOptions['user-wp-user-source-settings']['allowed'],
                compare: 'equal',
                value: true,
            }
        ]"
    >
        <cx-vui-select
            name="user-source-review-type"
            label="<?php _e( 'Review type', 'jet-reviews' ); ?>"
            description="<?php _e( 'Choose review type for user source', 'jet-reviews' ); ?>"
            :wrapper-css="[ 'equalwidth' ]"
            size="fullwidth"
            :options-list="reviewTypeOptions"
            v-model="pageOptions['user-wp-user-source-settings']['review_type']"
        ></cx-vui-select>

        <cx-vui-f-select
            name="user-source-allowed-roles"
            label="<?php _e( 'Allowed roles', 'jet-reviews' ); ?>"
            :wrapper-css="[ 'equalwidth' ]"
            :placeholder="'Select...'"
            :multiple="true"
            :options-list="allRolesOptions"
            autocomplete="<?php echo jet_reviews_tools()->generate_rand_string(); ?>"
            v-model="pageOptions['user-wp-user-source-settings']['allowed_roles']"
        ></cx-vui-f-select>

        <cx-vui-f-select
                v-if="verificationVisible"
                name="user-source-review-verifications"
                label="<?php _e( 'Review author verification', 'jet-reviews' ); ?>"
                description="<?php _e( 'Choose review author verification types', 'jet-reviews' ); ?>"
                :wrapper-css="[ 'equalwidth' ]"
                :placeholder="'Select...'"
                :multiple="true"
                :options-list="verificationOptions"
                autocomplete="<?php echo jet_reviews_tools()->generate_rand_string(); ?>"
                v-model="pageOptions['user-wp-user-source-settings']['verifications']">
        </cx-vui-f-select>

        <cx-vui-f-select
                v-if="verificationVisible"
                name="user-source-review-comment-verifications"
                label="<?php _e( 'Comment author verification', 'jet-reviews' ); ?>"
                description="<?php _e( 'Choose сomment author verification types', 'jet-reviews' ); ?>"
                :wrapper-css="[ 'equalwidth' ]"
                :placeholder="'Select...'"
                :multiple="true"
                :options-list="verificationOptions"
                v-model="pageOptions['user-wp-user-source-settings']['comment_verifications']">
        </cx-vui-f-select>

        <cx-vui-switcher
                name="need-approve-review-user-source"
                label="<?php _e( 'New review approval', 'jet-reviews' ); ?>"
                description="<?php _e( 'Need admin approval for a new review', 'jet-reviews' ); ?>"
                :wrapper-css="[ 'equalwidth' ]"
                :return-true="true"
                :return-false="false"
                v-model="pageOptions['user-wp-user-source-settings']['need_approve']"
        >
        </cx-vui-switcher>

        <cx-vui-switcher
                name="review-comments-allowed-user-source"
                label="<?php _e( 'Allow comments', 'jet-reviews' ); ?>"
                description="<?php _e( 'Allow review comments for this type of post', 'jet-reviews' ); ?>"
                :wrapper-css="[ 'equalwidth' ]"
                :return-true="true"
                :return-false="false"
                v-model="pageOptions['user-wp-user-source-settings']['comments_allowed']"
        >
        </cx-vui-switcher>

        <cx-vui-switcher
                name="review-comments-need-approve-user-source"
                label="<?php _e( 'New review comments need approval', 'jet-reviews' ); ?>"
                description="<?php _e( 'Need admin approval for a new review comment', 'jet-reviews' ); ?>"
                :wrapper-css="[ 'equalwidth' ]"
                :return-true="true"
                :return-false="false"
                v-model="pageOptions['user-wp-user-source-settings']['comments_need_approve']"
        >
        </cx-vui-switcher>

        <cx-vui-switcher
                name="review-approval-allowed-user-source"
                label="<?php _e( 'Allow review rate actions', 'jet-reviews' ); ?>"
                description="<?php _e( 'Allow likes/dislikes for review items for this type of post', 'jet-reviews' ); ?>"
                :wrapper-css="[ 'equalwidth' ]"
                :return-true="true"
                :return-false="false"
                v-model="pageOptions['user-wp-user-source-settings']['approval_allowed']"
        >
        </cx-vui-switcher>

    </cx-vui-component-wrapper>
</div>
