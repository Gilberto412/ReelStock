<div
	class="jet-reviews-settings-page jet-reviews-settings-page__advanced"
>
    <cx-vui-switcher
        name="forbidden-content-enable"
        label="<?php _e( 'Enable disallowed content checking', 'jet-reviews' ); ?>"
        description="<?php _e( 'Checking the content of reviews and comments for disallowed words.', 'jet-reviews' ); ?>"
        :wrapper-css="[ 'equalwidth' ]"
        :return-true="true"
        :return-false="false"
        v-model="pageOptions['forbidden-content']['enable']"
    >
    </cx-vui-switcher>

    <cx-vui-component-wrapper
        :wrapper-css="[ 'fullwidth-control' ]"
        :conditions="[
            {
                input: pageOptions['forbidden-content']['enable'],
                compare: 'equal',
                value: true,
            }
        ]"
    >
        <cx-vui-input
            name="forbidden-content-words"
            label="<?php _e( 'Disallowed words', 'jet-reviews' ); ?>"
            placeholder="<?php _e( 'Disallowed word 1, Disallowed word 2', 'jet-reviews' ); ?>"
            description="<?php _e( 'When a reviews contains any of these words in its content, it will be checked as unapproved automatically. Separate keywords with commas', 'jet-reviews' ); ?>"
            :wrapper-css="[ 'equalwidth' ]"
            size="fullwidth"
            v-model="pageOptions['forbidden-content']['words']"
        >
        </cx-vui-input>

    </cx-vui-component-wrapper>

    <cx-vui-switcher
        name="submit-review-notify"
        label="<?php _e( 'New review notify', 'jet-reviews' ); ?>"
        description="<?php _e( 'Email admin whenever anyone submit a review', 'jet-reviews' ); ?>"
        :wrapper-css="[ 'equalwidth' ]"
        :return-true="true"
        :return-false="false"
        v-model="pageOptions['submit-review-notify']['enable']"
    >
    </cx-vui-switcher>

    <cx-vui-component-wrapper
            :wrapper-css="[ 'fullwidth-control' ]"
            :conditions="[
            {
                input: pageOptions['submit-review-notify']['enable'],
                compare: 'equal',
                value: true,
            }
        ]"
    >
        <cx-vui-switcher
            name="approve-review-notify"
            label="<?php _e( 'Review approve needed notify', 'jet-reviews' ); ?>"
            description="<?php _e( 'Email admin only when approval is needed', 'jet-reviews' ); ?>"
            :wrapper-css="[ 'equalwidth' ]"
            :return-true="true"
            :return-false="false"
            v-model="pageOptions['submit-review-notify']['approval']"
        >
        </cx-vui-switcher>

        <cx-vui-switcher
            name="review-author-notify"
            label="<?php _e( 'Post author notify', 'jet-reviews' ); ?>"
            description="<?php _e( 'Email post author whenever anyone submit a review', 'jet-reviews' ); ?>"
            :wrapper-css="[ 'equalwidth' ]"
            :return-true="true"
            :return-false="false"
            v-model="pageOptions['submit-review-notify']['author_notify']"
        >
        </cx-vui-switcher>
    </cx-vui-component-wrapper>

    <cx-vui-switcher
        name="submit-comment-notify"
        label="<?php _e( 'New comment notify', 'jet-reviews' ); ?>"
        description="<?php _e( 'Email admin whenever anyone submit a comment', 'jet-reviews' ); ?>"
        :wrapper-css="[ 'equalwidth' ]"
        :return-true="true"
        :return-false="false"
        v-model="pageOptions['submit-comment-notify']['enable']"
    >
    </cx-vui-switcher>

    <cx-vui-component-wrapper
        :wrapper-css="[ 'fullwidth-control' ]"
        :conditions="[
        {
            input: pageOptions['submit-comment-notify']['enable'],
            compare: 'equal',
            value: true,
        }
    ]"
    >
        <cx-vui-switcher
            name="approve-comment-notify"
            label="<?php _e( 'Comment approve needed notify', 'jet-reviews' ); ?>"
            description="<?php _e( 'Email admin only when approval is needed', 'jet-reviews' ); ?>"
            :wrapper-css="[ 'equalwidth' ]"
            :return-true="true"
            :return-false="false"
            v-model="pageOptions['submit-comment-notify']['approval']"
        >
        </cx-vui-switcher>
    </cx-vui-component-wrapper>

</div>
