<?php
/**
 * Posts query component template
 */
?>
<div class="jet-engine-edit-page__fields">
	<div class="cx-vui-collapse__heading">
		<h3 class="cx-vui-subtitle"><?php _e( 'Jet Reviews Query', 'jet-reviews' ); ?></h3>
	</div>
	<div class="cx-vui-panel">
        <cx-vui-select
            label="<?php _e( 'Source', 'jet-reviews' ); ?>"
            description="<?php _e( 'Select review source', 'jet-reviews' ); ?>"
            :wrapper-css="[ 'equalwidth' ]"
            :options-list="sourceList"
            size="fullwidth"
            v-model="query.source"
        ></cx-vui-select>
        <cx-vui-input
            label="<?php _e( 'Source Type', 'jet-reviews' ); ?>"
            description="<?php _e( 'Select review source type', 'jet-reviews' ); ?>"
            :wrapper-css="[ 'equalwidth', 'has-macros' ]"
            size="fullwidth"
            name="query_source_type"
            v-model="query.source_type"
        ><jet-query-dynamic-args v-model="dynamicQuery.source_type"></jet-query-dynamic-args></cx-vui-input>
        <cx-vui-input
            label="<?php _e( 'Source ID', 'jet-reviews' ); ?>"
            description="<?php _e( 'Get reviews by source ID. Leave empty to detect automatically', 'jet-reviews' ); ?>"
            :wrapper-css="[ 'equalwidth', 'has-macros' ]"
            size="fullwidth"
            name="query_source_id"
            v-model="query.source_id"
        ><jet-query-dynamic-args v-model="dynamicQuery.source_id"></jet-query-dynamic-args></cx-vui-input>
        <cx-vui-input
            label="<?php _e( 'Number', 'jet-reviews' ); ?>"
            description="<?php _e( 'Number of items to show in the listing grid or per page if JetSmartFilters pagination is used. Query Count dynamic tag will show total number of items matched current Query', 'jet-reviews' ); ?>"
            :wrapper-css="[ 'equalwidth' ]"
            size="fullwidth"
            name="query_number"
            v-model="query.number"
        ></cx-vui-input>
        <cx-vui-input
            label="<?php _e( 'Offset', 'jet-reviews' ); ?>"
            description="<?php _e( 'Number of items to skip', 'jet-reviews' ); ?>"
            :wrapper-css="[ 'equalwidth' ]"
            size="fullwidth"
            name="query_offset"
            v-model="query.offset"
        ></cx-vui-input>
	</div>
</div>
