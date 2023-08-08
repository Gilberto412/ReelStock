<div
	class="jet-reviews-settings-page jet-reviews-settings-page__woocommerce"
>
    <div class="cx-vui-component cx-vui-component--equalwidth">
        <div class="cx-vui-component__meta">
            <label class="cx-vui-component__label"><?php _e( 'Import products reviews', 'jet-reviews' ); ?></label>
            <div class="cx-vui-component__desc"><?php _e( 'Import product native reviews and comments to create JetReviews items', 'jet-reviews' ); ?></div>
        </div>
        <div class="cx-vui-component__control">
            <cx-vui-f-select
                class="to-pull-product-list"
                name="to-pull-product-list"
                :wrapper-css="[ 'equalwidth' ]"
                :prevent-wrap="true"
                :placeholder="'Select...'"
                :multiple="true"
                :options-list="productList"
                autocomplete="<?php echo jet_reviews_tools()->generate_rand_string(); ?>"
                v-model="toPullProductList">
            </cx-vui-f-select>

            <cx-vui-button
                button-style="accent-border"
                size="mini"
                :loading="convertWooReviewsStatus"
                @click="convertWooReviews()"
            >
                <span slot="label"><?php _e( 'Import', 'jet-reviews' ); ?></span>
            </cx-vui-button>
        </div>
    </div>
</div>
