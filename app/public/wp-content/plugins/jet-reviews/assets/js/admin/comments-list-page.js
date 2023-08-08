(function( $, pageConfig ) {

	'use strict';

	Vue.config.devtools = true;

	window.JetReviewsCommentsListPage = new Vue( {
		el: '#jet-reviews-comments-list-page',

		data: {
			commentsGetting: false,
			itemsList: pageConfig.commentsList,
			commentsCount: +pageConfig.commentsCount,
			page: +pageConfig.currentPage,
			pageSize: +pageConfig.pageSize,
			reviewId: +pageConfig.reviewId,
			searchText: '',
			editPopupVisible: false,
			editCommentId: false,
			commentSavingState: false,
			actionExecution: false,
			searchingState: false,
			bulkCheck: false,
			bulkAction: '',
			bulkActionStatus: false,
			urlAction: false,
			urlCommentId: false,
		},

		mounted: function() {
			this.$el.className = this.$el.className + ' is-mounted';

			const urlParams = new URLSearchParams( window.location.href );

			this.urlAction = urlParams.get( 'action' ) || false;
			this.urlCommentId = urlParams.get( 'comment' ) || false;

			this.getComments();
		},
		watch: {
			bulkCheck: function( state ) {
				this.itemsList = this.itemsList.map( ( itemData ) => {
					itemData.check = state;

					return itemData;
				} );

			}
		},

		computed: {
			getCommentsParams: function () {
				return {
					id: this.urlCommentId,
					page: this.page - 1,
					search: this.searchText,
					reviewId: this.reviewId,
				}
			},

			checkedItemsList: function () {
				const filteredList = this.itemsList.filter( ( itemData ) => {
					return itemData.check;
				} );

				return filteredList.map( ( itemData ) => {
					return itemData.id;
				} );
			},

			editCommentData: function() {

				if ( 'undefined' !== typeof this.itemsList[ this.editCommentId ] ) {
					return this.itemsList[ this.editCommentId ];
				}

				return {
					approved: 'true',
					author: {
						avatar: '',
						id: '1',
						mail: 'demo@demo.com',
						name: 'admin',
					},
					content: '',
					date: '2000-01-01 00:00:00',
					id: '0',
					post: {},
				};
			},

			deleteReviewData: function() {

				if ( 'undefined' !== typeof this.itemsList[ this.deleteCommentId ] ) {
					return this.itemsList[ this.deleteCommentId ];
				}

				return false;
			}
		},

		methods: {
			changePage: function( page ) {
				this.page = page;
				this.getComments();
			},

			searchCommentHandle: function() {
				this.searchingState = true;
				this.getComments();
			},

			getIndexByID: function ( id ) {
				return this.itemsList.findIndex( ( item ) => {
					return item.id === id;
				} );
			},

			getComments: function() {
				let self = this;

				if ( '' !== this.searchText ) {
					this.page = 1;
				}

				this.commentsGetting = true;

				wp.apiFetch( {
					method: 'post',
					path: pageConfig.getCommentsRoute,
					data: {
						pageArgs: self.getCommentsParams,
					},
				} ).then( function( response ) {
					self.commentsGetting = false;
					self.searchingState = false;

					if ( response.success && response.data ) {
						self.itemsList = response.data.page_list;
						self.commentsCount = +response.data.total_count;

						self.urlActionHandle();
					} else {
						self.$CXNotice.add( {
							message: response.message,
							type: 'error',
							duration: 5000,
						} );
					}
				} );
			},

			approveHandler: function( ids = [], status = 'single' ) {
				let self = this,
				    itemsList = ids.map( ( id ) => {
					    let index    = this.getIndexByID( id ),
					        approved = true;

					    switch ( status ) {
						    case 'single':
							    approved = -1 !== index ? !this.itemsList[ index ].approved : true;
							    break;
						    case 'unapprove':
							    approved = false;
							    break;
						    case 'approve':
							    approved = true;
							    break;
					    }

					    return {
						    id: id,
						    approved: approved,
					    };
				    } );

				if ( ! itemsList.length ) {
					return false;
				}

				this.actionExecution = true;

				wp.apiFetch( {
					method: 'post',
					path: pageConfig.toggleCommentApproveRoute,
					data: {
						itemsList: itemsList,
						pageArgs: self.getReviewsParams,
					},
				} ).then( function( response ) {

					self.actionExecution = false;

					if ( response.success ) {
						self.itemsList = response.data.page_list;
						self.commentsCount = +response.data.total_count;
					} else {
						self.$CXNotice.add( {
							message: response.message,
							type: 'error',
							duration: 3000,
						} );
					}
				} );
			},

			openEditPopup: function( index ) {
				this.editCommentId = index;
				this.editPopupVisible = true;
			},

			saveCommentHandle: function() {
				let self = this;

				this.commentSavingState = true;

				wp.apiFetch( {
					method: 'post',
					path: pageConfig.updateCommentRoute,
					data: {
						data: self.editCommentData
					},
				} ).then( function( response ) {

					self.commentSavingState = false;

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

			deleteCommentHandle: function( ids ) {
				let self = this,
				    itemsList = ids.map( ( id ) => {
					    return {
						    id: id,
					    };
				    } );

				this.actionExecution = true;

				if ( ! itemsList.length ) {
					return false;
				}

				wp.apiFetch( {
					method: 'post',
					path: pageConfig.deleteCommentRoute,
					data: {
						itemsList: itemsList,
						pageArgs: self.getReviewsParams,
					},
				} ).then( function( response ) {
					self.actionExecution = false;

					if ( response.success ) {
						self.itemsList = response.data.page_list;
						self.commentsCount = +response.data.total_count;
					} else {
						self.$CXNotice.add( {
							message: response.message,
							type: 'error',
							duration: 5000,
						} );
					}
				} );
			},

			bulkActionHandle: function () {

				switch ( this.bulkAction ) {
					case 'unapprove':
						this.approveHandler( this.checkedItemsList, 'unapprove' );
						break;
					case 'approve':
						this.approveHandler( this.checkedItemsList, 'approve' );
						break;
					case 'delete':
						this.deleteCommentHandle( this.checkedItemsList );
						break;
				}

				this.bulkCheck = false;
			},

			urlActionHandle: function () {

				if ( this.urlAction && this.urlCommentId ) {
					switch ( this.urlAction ) {
						case 'approve':
							this.approveHandler( [ this.urlCommentId ], 'approve' );
							break;
						case 'delete':
							this.deleteCommentHandle( [ this.urlCommentId ] );
							break;
					}

					this.urlAction = false;
					this.urlCommentId = false;
				}
			},

			getRolesLabel: function( $rolesList ) {
				let label = '';

				if ( 'function' !== typeof $rolesList[ Symbol.iterator ] ) {
					return label;
				}

				for ( let role of $rolesList ) {
					label += `<span class="${ role }-role">${ role }</span>`;
				}

				return label;
			}

		}
	} );

})( jQuery, window.JetReviewsCommentsListPageConfig );
