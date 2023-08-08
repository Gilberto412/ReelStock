(function( $ ) {

	'use strict';

	Vue.component( 'jet-reviews-query', {
		template: '#jet-reviews-query',
		mixins: [
			window.JetQueryWatcherMixin,
			//window.JetQueryRepeaterMixin,
		],
		props: [ 'value', 'dynamic-value' ],
		data: function() {
			return {
				operators: window.JetEngineQueryConfig.operators_list,
				dataTypes: window.JetEngineQueryConfig.data_types,
				sourceList: window.jet_query_component_jet_reviews.sourceOptions,
				query: {},
				dynamicQuery: {},
			};
		},
		computed: {

		},
		created: function() {

			this.query        = { ...this.value };
			this.dynamicQuery = { ...this.dynamicValue };

			if ( ! this.query.order ) {
				this.$set( this.query, 'order', [] );
			}

			//this.presetArgs();

		},
		methods: {
			/*hasFields: function() {
				return 0 < this.currentFields.length;
			},
			presetArgs: function() {
				if ( ! this.query.args ) {
					this.$set( this.query, 'args', [] );
				}

				if ( ! this.dynamicQuery.args ) {
					this.$set( this.dynamicQuery, 'args', {} );
				} else if ( 'object' !== typeof this.dynamicQuery.args || undefined !== this.dynamicQuery.args.length ) {
					this.$set( this.dynamicQuery, 'args', {} );
				}
			},
			newDynamicArgs: function( newClause, metaQuery, prevID ) {

				let newItem = {};

				if ( prevID && this.dynamicQuery.args[ prevID ] ) {
					newItem = { ...this.dynamicQuery.args[ prevID ] };
				}

				this.$set( this.dynamicQuery.args, newClause._id, newItem );

			},
			deleteDynamicArgs: function( id ) {
				this.$delete( this.dynamicQuery.args, id );
			},*/
		}
	} );

})( jQuery );
