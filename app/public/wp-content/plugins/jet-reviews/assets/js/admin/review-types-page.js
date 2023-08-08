(function( $, pageConfig ) {

	'use strict';

	Vue.config.devtools = true;

	window.JetReviewsTypesPage = new Vue( {
		el: '#jet-reviews-types-page',

		data: {
			creatingStatus: false,
			updatingStatus: false,
			deletingStatus: false,
			addTypePopupVisible: false,
			editPopupVisible: false,
			deletePopupVisible: false,
			editTypeId: false,
			deleteTypeId: false,
			itemsList: pageConfig.typesList || {},
			tempTypeData: {
				name: '',
				fields: []
			}
		},

		mounted: function() {
			this.$el.className = this.$el.className + ' is-mounted';
		},

		computed: {
			preparedTempTypeData: function() {

				return {
					name: this.tempTypeData.name,
					slug: this.preSetSlug( this.tempTypeData.name ),
					fields: this.tempTypeData.fields
				}
			},
		},

		methods: {
			showAddTypePopup: function() {
				this.tempTypeData = {
					name: '',
					slug: '',
					fields: []
				};

				this.addTypePopupVisible = true;
			},

			addTypeHandle: function() {
				let self = this;

				this.addTypePopupVisible = true;

				if ( '' === this.preparedTempTypeData.name ) {
					self.$CXNotice.add( {
						message: pageConfig.messages['emptyName'],
						type: 'error',
						duration: 5000,
					} );

					return false;
				}

				if ( 0 === this.preparedTempTypeData.fields.length ) {
					self.$CXNotice.add( {
						message: pageConfig.messages['emptyFields'],
						type: 'error',
						duration: 5000,
					} );

					return false;
				}

				this.creatingStatus = true;

				wp.apiFetch( {
					method: 'post',
					path: pageConfig.addReviewType,
					data: {
						name: self.preparedTempTypeData.name,
						slug: self.preparedTempTypeData.slug,
						fields: self.preparedTempTypeData.fields,
					},
				} ).then( function( response ) {

					self.creatingStatus = false;

					if ( response.success ) {

						self.addTypePopupVisible = false;

						let newKey = response.data.insert_id;

						let preparedData = {
							id: newKey,
							name: self.preparedTempTypeData.name,
							slug: self.preparedTempTypeData.slug,
							fields: self.preparedTempTypeData.fields,
						}

						Vue.set( self.itemsList, newKey, preparedData );

						self.$CXNotice.add( {
							message: response.message,
							type: 'success',
							duration: 5000,
						} );
					} else {
						self.$CXNotice.add( {
							message: response.message,
							type: 'error',
							duration: 5000,
						} );
					}
				} );
			},

			saveTypeHandle: function() {
				let self = this;

				this.editPopupVisible = true;

				this.updatingStatus = true;

				wp.apiFetch( {
					method: 'post',
					path: pageConfig.updateReviewType,
					data: {
						name: self.preparedTempTypeData.name,
						slug: self.preparedTempTypeData.slug,
						fields: self.preparedTempTypeData.fields,
					},
				} ).then( function( response ) {

					self.updatingStatus = false;

					if ( response.success ) {

						self.editPopupVisible = false;

						self.$CXNotice.add( {
							message: response.message,
							type: 'success',
							duration: 5000,
						} );
					} else {
						self.$CXNotice.add( {
							message: response.message,
							type: 'error',
							duration: 5000,
						} );
					}
				} );
			},

			addNewField: function( event ) {
				var field = {
					label: '',
					step: 1,
					max: 100,
				};

				this.preparedTempTypeData.fields.push( field );
			},

			cloneField: function( index ) {
				var field    = this.tempTypeData.fields[ index ],
					newField = {
						label: field.label + '_copy',
						step: field.step,
						max: field.max,
					};

				this.tempTypeData.fields.splice( index + 1, 0, newField );
			},

			deleteField: function( index ) {

				if ( 1 === this.tempTypeData.fields.length ) {
					this.$CXNotice.add( {
						message: pageConfig.messages['emptyFields'],
						type: 'error',
						duration: 5000,
					} );

					return;
				}

				this.tempTypeData.fields.splice( index, 1 );
			},

			openEditTypePopup: function( typeId ) {
				this.editPopupVisible = true;
				this.editTypeId = typeId;

				this.tempTypeData = this.itemsList[typeId];
			},

			openDeleteTypePopup: function( typeId ) {
				this.deletePopupVisible = true;
				this.deleteTypeId = typeId;
			},

			deleteTypeHandle: function() {
				let self = this;

				this.deletingStatus = true;

				wp.apiFetch( {
					method: 'post',
					path: pageConfig.deleteReviewType,
					data: {
						id: self.deleteTypeId
					},
				} ).then( function( response ) {

					if ( response.success ) {
						self.deletingStatus = false;

						Vue.delete( self.itemsList, self.deleteTypeId );

						self.$CXNotice.add( {
							message: response.message,
							type: 'success',
							duration: 5000,
						} );
					} else {
						self.$CXNotice.add( {
							message: response.message,
							type: 'error',
							duration: 5000,
						} );
					}
				} );
			},

			generateFieldsList: function( fields ) {
				let labelsArray = fields.map( function( field ) {

					return field.label;
				} );

				return labelsArray.join( ', ' );
			},

			preSetSlug: function( string ) {

				if ( 0 === string.length ) {
					return '';
				}

				var regex = /\s+/g,
					slug  = string.toLowerCase().replace( regex, '-' );

				// Replace accents
				slug = slug.normalize( 'NFD' ).replace( /[\u0300-\u036f]/g, "" );

				if ( 20 < slug.length ) {
					slug = slug.substr( 0, 20 );

					if ( '-' === slug.slice( -1 ) ) {
						slug = slug.slice( 0, -1 );
					}
				}

				return slug;

			},
		}
	} );

})( jQuery, window.JetReviewsTypesPageConfig );
