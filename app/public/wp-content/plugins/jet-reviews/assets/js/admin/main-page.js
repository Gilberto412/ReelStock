( function( $, JetReviewsMainPageConfig ) {

	'use strict';

	Vue.config.devtools = true;

	Vue.component( 'general-reviews-line-chart', {
		extends: VueChartJs.Line,
		mounted () {
			this.renderChart( {
				labels: JetReviewsMainPageConfig.monthList,
				datasets: JetReviewsMainPageConfig.generalDataSets,
			}, {
				responsive: true,
				maintainAspectRatio: false,
				scales: {
					yAxes: [ {
						ticks: {
							min: 0,
							stepSize: 10
						}
					} ]
				},
				legend: {
					display: true,
					position: 'bottom'
				}
			} );
		}
	} );

	Vue.component( 'post-type-reviews-line-chart', {
		extends: VueChartJs.Line,
		props: {
			dataSets: Array
		},
		mounted () {
			this.renderChart( {
				labels: JetReviewsMainPageConfig.monthList,
				datasets: this.dataSets,
			}, {
				responsive: true,
				maintainAspectRatio: false,
				scales: {
					yAxes: [ {
						ticks: {
							min: 0,
							stepSize: 10
						}
					} ]
				},
				animation: {
					duration: 0
				},
				legend: {
					display: false,
				}
			} );
		}
	} );

	Vue.component( 'doughnut-rating-chart', {
		extends: VueChartJs.Doughnut,
		props: {
			low: Number,
			medium: Number,
			high: Number,
		},
		mounted () {
			this.renderChart( {
				labels: [ 'Low', 'Medium', 'High' ],
				datasets: [ {
				backgroundColor: [ '#C92C2C', '#E3C004', '#46B450'],
				data: [ this.low, this.medium, this.high ]
				} ]
			}, {
				responsive: true,
				maintainAspectRatio: false,
				legend: {
					position: 'bottom'
					//display: false,
				}
			} );
		}
	} );

	window.JetReviewsMainPage = new Vue( {
		el: '#jet-reviews-main-page',

		data: {
			activeTab: window.localStorage.getItem( 'jet-reviews/admin/main-page/active-post-type-tab' ) || 'post',
		},

		mounted: function() {
			this.$el.className = this.$el.className + ' is-mounted';
		},

		computed: {
			reviewCountData: function() {
				return JetReviewsMainPageConfig.reviewCount;
			},

			reviewCount: function() {
				return JetReviewsMainPageConfig.reviewCount.all;
			},

			approvedReviewCount: function() {
				return JetReviewsMainPageConfig.approvedReviewCount;
			},

			notApprovedReviewCount: function() {
				return this.reviewCount - this.approvedReviewCount;
			},

			commentCount: function() {
				return JetReviewsMainPageConfig.commentsCount;
			},

			approvedCommentCount: function() {
				return JetReviewsMainPageConfig.approvedCommentsCount;
			},

			notApprovedCommentCount: function() {
				return this.commentCount - this.approvedCommentCount;
			},

			postTypesData: function() {
				return JetReviewsMainPageConfig.postTypes || [];
			}
		},

		methods: {
			tabSwitch: function( currentTab ) {
				window.localStorage.setItem( 'jet-reviews/admin/main-page/active-post-type-tab', currentTab );
			},
		}
	} );

} )( jQuery, window.JetReviewsMainPageConfig );
