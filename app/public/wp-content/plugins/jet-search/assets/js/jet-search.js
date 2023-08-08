(function( $ ) {

	'use strict';

	var JetSearch = {

		initElementor: function() {

			var widgets = {
				'jet-ajax-search.default': JetSearch.widgetAjaxSearch,
				'jet-search-suggestions.default': JetSearch.widgetSearchSuggestions
			};

			$.each( widgets, function( widget, callback ) {
				window.elementorFrontend.hooks.addAction( 'frontend/element_ready/' + widget, callback );
			} );

			/*
			// Example of usage AJAX success trigger
			$( document ).on( 'jet-ajax-search/show-results', function( event, searchResults ) {
				searchResults.find( '.jet-ajax-search__results-item' ).css( 'border', '2px solid red' );
			} );
			*/

		},

		initBlocks: function( $scope ) {

			$scope = $scope || $( 'body' );

			window.JetPlugins.init( $scope, [
				{
					block: 'jet-search/ajax-search',
					callback: JetSearch.widgetAjaxSearch
				},
				{
					block: 'jet-search/search-suggestions',
					callback: JetSearch.widgetSearchSuggestions
				}
			] );
		},

		widgetAjaxSearch: function( $scope ) {

			var settings = {
				searchClass:        '.jet-ajax-search',
				searchFormClass:    '.jet-ajax-search__form',
				fieldsHolderClass:  '.jet-ajax-search__fields-holder',
				inputClass:         '.jet-ajax-search__field',
				settingsInput:      'input[name="jet_ajax_search_settings"]',
				submitClass:        '.jet-ajax-search__submit',
				chosenClass:        '.jet-ajax-search__categories select[name="jet_ajax_search_categories"]',
				resultsAreaClass:   '.jet-ajax-search__results-area',
				resultsHeaderClass: '.jet-ajax-search__results-header',
				resultsFooterClass: '.jet-ajax-search__results-footer',
				listHolderClass:    '.jet-ajax-search__results-holder',
				listClass:          '.jet-ajax-search__results-list',
				listInnerClass:     '.jet-ajax-search__results-list-inner',
				listSlideClass:     '.jet-ajax-search__results-slide',
				itemClass:          '.jet-ajax-search__results-item',
				countClass:         '.jet-ajax-search__results-count',
				messageHolderClass: '.jet-ajax-search__message',
				fullResultsClass:   '.jet-ajax-search__full-results',
				navigationClass:    '.jet-ajax-search__navigation-holder',
				navButtonClass:     '.jet-ajax-search__navigate-button',
				bulletClass:        '.jet-ajax-search__bullet-button',
				numberClass:        '.jet-ajax-search__number-button',
				prevClass:          '.jet-ajax-search__prev-button',
				nextClass:          '.jet-ajax-search__next-button',
				activeNavClass:     '.jet-ajax-search__active-button',
				disableNavClass:    '.jet-ajax-search__navigate-button-disable',
				spinnerClass:       '.jet-ajax-search__spinner-holder',
				handlerId:          'jetSearchSettings',
				isRtl:              ( window.elementorFrontend && window.elementorFrontend.config.is_rtl ) ? window.elementorFrontend.config.is_rtl : $( 'body' ).hasClass( 'rtl' )
			};

			if ( $scope.hasClass('jet-ajax-search-block') ) {
				var resultAreaWidthBy             = $scope.find( settings.searchClass ).data('settings')['results_area_width_by'],
					resultAreaCustomWidth         = $scope.find( settings.searchClass ).data('settings')['results_area_custom_width'],
					resultAreaCustomWidthPosition = $scope.find( settings.searchClass ).data('settings')['results_area_custom_position'],
					resultAreaContainer           = $( '.jet-ajax-search__results-area', $scope );

				if ( "custom" === resultAreaWidthBy ) {
					if ( "" !== resultAreaCustomWidth ) {
						resultAreaContainer.width( resultAreaCustomWidth );
					}

					switch( resultAreaCustomWidthPosition ) {
						case 'left':
							resultAreaContainer.css( "left", 0 );
							resultAreaContainer.css( "right", "auto" );
							break;
						case 'center':
							resultAreaContainer.css( "left", "50%" );
							resultAreaContainer.css( "right", "auto" );
							resultAreaContainer.css( "-webkit-transform", "translateX(-55%)" );
							resultAreaContainer.css( "transform", "translateX(-50%)" );
							break;
						case 'right':
							resultAreaContainer.css( "left", "auto" );
							resultAreaContainer.css( "right", 0 );
							break;
					}
				}
			}

			$scope.find( settings.searchClass ).jetAjaxSearch( settings );

			var $chosenSelect = $scope.find( settings.chosenClass );

			if ( $chosenSelect[0] ) {
				$chosenSelect.chosen( {
					disable_search: true,
					placeholder_text: '',
					placeholder_text_single: ''
				} );
			}
		},

		widgetSearchSuggestions: function( $scope ) {

			let	$target  = $scope.find( '.jet-search-suggestions' ),
				settings = {
					searchClass:        '.jet-search-suggestions',
					searchFormClass:    '.jet-search-suggestions__form',
					fieldsHolderClass:  '.jet-search-suggestions__fields-holder',
					inputClass:         '.jet-search-suggestions__field',
					spinnerClass:       '.jet-search-suggestions__spinner-holder',
					messageHolderClass: '.jet-search-suggestions__message',
					settingsInput:      'input[name="jet_search_suggestions_settings"]',
					submitClass:        '.jet-search-suggestions__submit',
					chosenClass:        '.jet-search-suggestions__categories select[name="jet_search_suggestions_categories"]',
					inlineClass:        '.jet-search-suggestions__inline-area',
					inlineItemClass:    '.jet-search-suggestions__inline-area-item',
					focusClass:         '.jet-search-suggestions__focus-area',
					focusHolderClass:   '.jet-search-suggestions__focus-results-holder',
					focusItemClass:     '.jet-search-suggestions__focus-area-item',
					handlerId:          'jetSearchSettings',
					isRtl:              ( window.elementorFrontend && window.elementorFrontend.config.is_rtl ) ? window.elementorFrontend.config.is_rtl : $( 'body' ).hasClass( 'rtl' )
				},
				$chosenSelect = $scope.find( settings.chosenClass );

			$target.jetAjaxSearchSuggestions( settings );

			if ( $chosenSelect[0] ) {
				$chosenSelect.chosen( {
					disable_search: true,
					placeholder_text: '',
					placeholder_text_single: ''
				} );
			}
		},

		setFormSuggestion: function( name, form, userId, url = '' ) {
			const ajaxSettings = window['jetSearchSettings']['searchSuggestions'] || {},
				sendData       = {
				name: name,
				user_id: userId
			};

			const ajaxData = {
				action: ajaxSettings.set_action,
				data: sendData || {},
			};

			$.ajax( {
				type: 'POST',
				url: ajaxSettings.add_suggestions_rest_api_url,
				data: ajaxData,
				dataType: 'json',
				complete: function() {
					if ( '' != url ) {
						url = JetSearch.getCustomResultUrl( form, url );

						window.location.href = url;
					} else {
						form.submit();
					}
				}
			} );
		},
		getCustomResultUrl: function( form, url ) {
			if ( '' != url ) {
				let formData = {},
					customUrl = '';

				formData = form.serializeArray().reduce( function( obj, item ) {
					obj[item.name] = item.value;

					return obj;
				}, {} );

				customUrl = url + '?' + $.param(formData);

				return customUrl;
			}
		},
		suggestionsPreloader: function( is_active, state, preloader ) {
			if ( ! is_active ) {
				return;
			}

			if ( '' != preloader ) {
				if ( 'show' === state ) {
					preloader.addClass( 'show' );
				} else if ( 'hide' === state ) {
					preloader.removeClass( 'show' );
				}
			}
		}
	};

	$.fn.getSuggestionsList = function( options, settings, showSpinner, hightlightText = false ) {
		let self               = this[0],
			outputHtml         = '',
			listPosition       = options.list_position,
			inlineItemTemplate = wp.template( 'jet-search-inline-suggestion-item' ),
			focusItemTemplate  = wp.template( 'jet-search-focus-suggestion-item' ),
			spinner            = $( settings.spinnerClass, self ),
			manualList         = [];

		const ajaxSettings = window['jetSearchSettings']['searchSuggestions'] || {};

		function highlightMatches( item ) {
			let searched = options.value.trim();

			if ( searched !== "" ) {
				let reg     = new RegExp("[\>][^\<\>.]*"+searched+"[^\<\>.]*[\<]","gi"),
					reg2    = new RegExp( searched, "gi" ),
					regHtml = new RegExp("<\/?[a-z](.*?)[\s\S]*>", "gi");

				if ( reg.test( item ) ) {
					item = item.replace( reg, function( item ) {
						let subRegex = new RegExp( searched, "gi" );
						return item.replace( subRegex,`<mark>${searched}</mark>` );
					} );
				}

				if ( regHtml.test( item ) ) {
					return item;
				} else {
					item = item.replace( reg2, str => `<mark>${str}</mark>` );
				}
			}

			return item;
		}

		if ( 'manual' === options.list_type ) {

			if ( options['manual_list'].length ) {
				let list = options['manual_list'].split( "," );

				list.map( function( suggestion, i ) {
					manualList[i] = { name: suggestion };
				} );

				if ( 'inline' === listPosition ) {
					manualList.map( function( suggestion ) {
						outputHtml += inlineItemTemplate( suggestion );
					} );

					$( self ).html( outputHtml );
				} else if ( 'focus' === listPosition ) {
					manualList.map( function( suggestion ) {
						outputHtml += focusItemTemplate( suggestion );
					} );

					$( settings.focusHolderClass ,self ).html( outputHtml );

					JetSearch.suggestionsPreloader( showSpinner, 'hide', spinner );
				}
			}

			return;
		}

		let sendData = {
				list_type: options.list_type || '',
				value: options.value || '',
				limit: options.limit
			},
			ajaxData = {
				action: ajaxSettings.get_action,
				data: sendData || {}
			};

		jQuery.ajax( {
			type: 'GET',
			url: ajaxSettings.get_suggestions_rest_api_url,
			data: ajaxData,
			dataType: 'json',
			cache: false,
			processData: true,
			error: function( jqXHR, textStatus, errorThrown ) {
				errorCallback( jqXHR );
			},
			success: function( response, textStatus, jqXHR ) {
				successCallback( response );
			}
		} );

		const successCallback = function( response ) {

			if ( response ) {
				JetSearch.suggestionsPreloader( showSpinner, 'hide', spinner );

				if ( 'inline' === listPosition ) {
					response.map( function( suggestion ) {
						outputHtml += inlineItemTemplate( suggestion );
					} );

					$( self ).html( outputHtml );
				} else if ( 'focus' === listPosition ) {

					response.map( function( suggestion ) {
						if ( options.value && ( "yes" === hightlightText || true === hightlightText ) ) {
							suggestion['name'] = highlightMatches( suggestion['name'] );
						}

						outputHtml += focusItemTemplate( suggestion );
					} );

					$( settings.focusHolderClass, self ).html( outputHtml );
				}
			}
		}

		const errorCallback = function( jqXHR ) {
			if ( 'abort' !== jqXHR.statusText ) {
				JetSearch.suggestionsPreloader( showSpinner, 'hide', spinner );
			}
		};
	};

	/**
	 * jetAjaxSearchSuggestions jQuery Plugin
	 *
	 * @param args
	 */
	$.fn.jetAjaxSearchSuggestions = function( args ) {
		let self                   = this[0],
			settings               = args,
			options                = $( self ).data( 'settings' ) || {},
			timer                  = null,
			showformList           = options['show_search_suggestions_list_inline'] || false,
			showfocusList          = options['show_search_suggestions_list_on_focus'] || false,
			formListType           = options['search_suggestions_list_inline'] || false,
			focusListType          = options['search_suggestions_list_on_focus'] || false,
			searchSuggestionsLimit = options['search_suggestions_quantity_limit'] || 10,
			inlineLimit            = options['search_suggestions_list_inline_quantity'] || 5,
			focusLimit             = options['search_suggestions_list_on_focus_quantity'] || 5,
			showSpinner            = options['show_search_suggestions_list_on_focus_preloader'] || '',
			hightlightText         = options['highlight_searched_text'] || '',
			spinner                = $( settings.spinnerClass, settings.focusClass ),
			formFocusClass         = settings.searchFormClass.replace( '.', '' ) + '--focus',
			form                   = $( settings.searchFormClass, self ),
			focusTarget            = $( settings.focusHolderClass, self ),
			disableInputs          = false,
			customResultUrl        = options['search_results_url'] || '',
			userId                 = null;

		if ( window.elementorFrontend ) {
			var editMode = Boolean( window.elementorFrontend.isEditMode() )
		} else {
			var editMode = false;
		}

		if ( !self.isInit ) {
			self.isInit = true;

			/**
			 * Ajax settings from localized global variable
			 */
			self.ajaxSettings = window[ settings.handlerId ]['searchSuggestions'] || {};

			customResultUrl = $.trim( customResultUrl );

			function getUserId() {
				return new Promise(function( resolve, reject ) {
					$.ajax( {
						url: self.ajaxSettings.ajaxurl,
						type: 'POST',
						cache: false,
						dataType: 'json',
						data: {
							action: 'suggestions_get_user_id'
						},
						success: function( response ) {
							let user_id = response;
							resolve( user_id );
						},
						error: function( jqXHR, textStatus, errorThrown ) {
							console.log( jqXHR );
						},
					} );
				} );
			}

			getUserId().then( function( user_id ) {
				userId = user_id;
			} );

			self.selectSuggestion = function( event ) {
				if ( false === disableInputs && !editMode ) {
					disableInputs = true;

					let value = event.target.innerText;

					$( settings.inputClass, self )[0].value = value;

					if ( null != userId ) {
						JetSearch.setFormSuggestion( value, form, userId, customResultUrl );
					} else if ( '' != customResultUrl ) {
						window.location.href = customResultUrl;
					} else {
						form.submit();
					}
				}
			}

			if ( formListType || focusListType ) {

				if ( '' != formListType && ( "yes" === showformList || true === showformList ) ) {
					let listOptions = {
						list_position: 'inline',
						list_type: formListType,
						limit: inlineLimit
					};

					if ( 'manual' === formListType ) {
						listOptions.manual_list = options['search_suggestions_list_inline_manual'];
					}

					$( settings.inlineClass, self ).getSuggestionsList( listOptions, settings, showSpinner );
				}

				if ( '' != focusListType && ( "yes" === showfocusList || true === showfocusList ) ) {
					let listOptions = {
						list_position: 'focus',
						list_type: focusListType,
						limit: focusLimit
					};

					if ( 'manual' === focusListType ) {
						listOptions.manual_list = options['search_suggestions_list_on_focus_manual'];
					}

					$( settings.focusClass, self ).getSuggestionsList( listOptions, settings, showSpinner );
				}
			}

			self.inputChangeHandler = function( event ) {

				let value       = $( event.target ).val(),
					listOptions = {
						list_position: 'focus',
						value: value,
						limit: searchSuggestionsLimit
					};

				if ( '' != value ) {
					focusTarget.empty();
					self.showList();
					JetSearch.suggestionsPreloader( showSpinner, 'show', spinner );

					clearTimeout( timer );
					timer = setTimeout( function() {
						$( settings.focusClass, self ).getSuggestionsList( listOptions, settings, showSpinner, hightlightText );
					}, 450 );
				} else {
					clearTimeout( timer );
					focusTarget.empty();
					JetSearch.suggestionsPreloader( showSpinner, 'shide', spinner );

					if ( false != focusListType && ( "yes" === showfocusList || true === showfocusList ) ) {
						JetSearch.suggestionsPreloader( showSpinner, 'show', spinner );

						listOptions.limit = focusLimit;

						if ( 'manual' === focusListType ) {
							listOptions.list_type = focusListType;
							listOptions.manual_list = options['search_suggestions_list_on_focus_manual'];
						}

						$( settings.focusClass, self ).getSuggestionsList( listOptions, settings, showSpinner );
					}
				}
			};

			self.hideList = function(event) {
				$( settings.focusClass, self ).removeClass( 'show' );
			};

			self.showList = function() {
				$( settings.focusClass, self ).addClass( 'show' );
			};

			self.focusHandler = function( event ) {
				$( settings.searchFormClass, self ).addClass( formFocusClass );
				self.showList();
			};

			self.chosenFocusHandler = function() {
				self.hideList();
			};

			self.formClick = function( event ) {
				event.stopPropagation();
			};

			self.changeHandler = function( event ) {
				let target              = $( event.target ),
					settingsInput       = $( settings.settingsInput, self ),
					querySettings       = JSON.parse( settingsInput.val() ),
					globalQuerySettings = $( self ).data( 'settings' );

				querySettings.category__in       = target.val();
				globalQuerySettings.category__in = target.val();

				settingsInput.val( JSON.stringify( querySettings ) );
				$( self ).data( 'settings', globalQuerySettings );

				self.inputChangeHandler( { target: $( settings.inputClass, self ) } )
			};

			self.formSubmit = function( event ) {

				if ( false === disableInputs ) {
					let value = event.target.value;

					if ( 13 === event.keyCode && value.length != 0 ) {
						disableInputs = true;
						event.preventDefault();

						if ( null != userId ) {
							JetSearch.setFormSuggestion( value, form, userId, customResultUrl );
						} else if ( '' != customResultUrl ) {
							window.location.href = customResultUrl;
						} else {
							form.submit();
						}
					}
				}
			}

			self.blurHandler = function( event ) {
				$( settings.searchFormClass, self ).removeClass( formFocusClass );
			};

			self.clickFullResults = function( event ) {
				if ( false === disableInputs ) {
					disableInputs = true;

					var searchInput = $( settings.inputClass, self ),
						value       = searchInput.val();

					event.preventDefault();

					if ( null != userId ) {
						JetSearch.setFormSuggestion( value, form, userId, customResultUrl );
					} else if ( '' != customResultUrl ) {
						window.location.href = customResultUrl;
					} else {
						form.submit();
					}
				}
			};

			$( settings.inputClass, self )
				.on( 'input' + settings.searchClass, self.inputChangeHandler )
				.on( 'focus' + settings.searchClass, self.focusHandler )
				.on( 'blur' + settings.searchClass, self.blurHandler )
				.on( 'keypress' + settings.searchClass, self.formSubmit );

			$( settings.submitClass, self ).on( 'click' + settings.searchClass, self.clickFullResults );

			$( self )
				.on( 'click' + settings.searchClass, settings.focusItemClass, self.selectSuggestion )
				.on( 'click' + settings.searchClass, settings.inlineItemClass, self.selectSuggestion )
				.on( 'click' + settings.searchClass, self.formClick )
				.on( 'change', settings.chosenClass, self.changeHandler )
				.on( 'touchend' + settings.searchClass, self.formClick )
				.on( 'chosen:showing_dropdown', settings.chosenClass, self.chosenFocusHandler );

			$( 'body' )
				.on( 'click' + settings.searchClass, self.hideList )
				.on( 'touchend' + settings.searchClass, self.hideList );

			// If after reloading the page the value of the select is not '0'.
			if ( '0' !== $( settings.chosenClass, self ).val() ) {
				$( settings.chosenClass, self ).trigger( 'change' );
			}
		}
	};

	/**
	 * JetAjaxSearch jQuery Plugin
	 *
	 * @param args
	 */
	$.fn.jetAjaxSearch = function( args ) {

		var self              = this[0],
			settings          = args,
			timer             = null,
			itemTemplate      = null,
			resultsArea       = $( settings.resultsAreaClass, self ),
			resultsHolder     = $( settings.listHolderClass, resultsArea ),
			resultsHeader     = $( settings.resultsHeaderClass, resultsArea ),
			resultsFooter     = $( settings.resultsFooterClass, resultsArea ),
			countHolder       = $( settings.countClass, resultsArea ),
			fullResults       = $( settings.fullResultsClass, resultsArea ),
			resultsList       = $( settings.listClass, resultsArea ),
			resultsListInner  = $( settings.listInnerClass, resultsArea ),
			resultsHeaderNav  = $( settings.navigationClass, resultsHeader ),
			resultsFooterNav  = $( settings.navigationClass, resultsFooter ),
			messageHolder     = $( settings.messageHolderClass, resultsArea ),
			spinner           = $( settings.spinnerClass, resultsArea ),
			data              = $( self ).data( 'settings' ) || [],
			formFocusClass    = settings.searchFormClass.replace( '.', '' ) + '--focus',
			currentPosition   = 1,
			allowEmptyString  = false;

		if ( 'yes' === data.search_by_empty_value || true === data.search_by_empty_value ) {
			allowEmptyString = true;
		}

		if ( !self.isInit ) {
			self.isInit = true;

			/**
			 * Ajax request instance
			 */
			self.ajaxRequest = null;

			/**
			 * Ajax settings from localized global variable
			 */
			self.ajaxSettings = window[ settings.handlerId ] || {};

			self.inputChangeHandler = function( event ) {
				var value = $( event.target ).val(),
					symbolNumberForStart = 'number' === $.type( data.symbols_for_start_searching ) ? data.symbols_for_start_searching : 2;

				if ( 'number' === $.type( symbolNumberForStart ) && symbolNumberForStart > value.length ) {
					self.hideList();
					return false;
				}

				resultsHolder.removeClass( 'show' );
				self.outputMessage( '', '' );
				resultsListInner.css( 'transform', 'translateX(0)' );
				resultsList.css( 'height', 'auto' );

				if ( value ) {
					self.showList();
					spinner.addClass( 'show' );

					clearTimeout( timer );
					timer = setTimeout( function() {
						data.value = value;
						data.deviceMode = window.elementorFrontend && window.elementorFrontend.getCurrentDeviceMode() ? window.elementorFrontend.getCurrentDeviceMode() : false;
						self.ajaxSendData( data );
					}, 450 );
				} else {
					self.hideList();
				}
			};

			self.successCallback = function( response ) {
				if ( ! response.success ) {
					spinner.removeClass( 'show' );
					self.outputMessage( data.server_error, 'error show' );
					return;
				}

				var responseData = response.data,
					error        = responseData.error,
					message      = responseData.message,
					posts        = responseData.posts,
					post         = null,
					outputHtml   = '',
					listItemHtml = '',
					listHtml     = '<div class="' + settings.listSlideClass.replace( '.', '' ) + '">%s</div>';

				resultsHolder.removeClass( 'show' );
				spinner.removeClass( 'show' );
				currentPosition = 1;

				resultsListInner.html( '' );

				const allowedHighlightFields = [ 'title', 'after_content', 'after_title', 'before_content', 'before_title', 'content', 'price' ];

				function highlightMatches( item ) {
					let searched = responseData.search_value.trim();

					if ( searched !== "" ) {
						let reg  = new RegExp("[\>][^\<\>.]*"+searched+"[^\<\>.]*[\<]","gi"),
							reg2 = new RegExp( searched, "gi" ),
							regHtml = new RegExp("<\/?[a-z](.*?)[\s\S]*>", "gi");

						if ( reg.test( item ) ) {
							item = item.replace( reg, function( item ) {
								let subRegex = new RegExp( searched, "gi" );
								return item.replace( subRegex,`<mark>${searched}</mark>` );
							} );
						}

						if ( regHtml.test( item ) ) {
							return item;
						} else {
							item = item.replace( reg2, str => `<mark>${str}</mark>` );
						}
					}

					return item;
				}

				function highlightFields( fields, allowHighlightFields ) {

					$.each( fields, function( key, value ) {
						if ( -1 != $.inArray( key, allowHighlightFields ) && ( null != value && '' != value ) ) {
							fields[key] = highlightMatches( value );
						}
					} );

					return fields;
				}

				if ( 0 !== responseData.post_count && !error ) {

					messageHolder.removeClass( 'show' );
					itemTemplate = wp.template( 'jet-ajax-search-results-item' );

					for ( post in posts ) {
						if ( responseData.search_highlight && true === responseData.search_highlight ) {
							highlightFields( posts[post], allowedHighlightFields );
						}

						listItemHtml += itemTemplate( posts[post] );

						if ( (parseInt( post ) + 1) % responseData.limit_query == 0 || parseInt( post ) === posts.length - 1 ) {
							outputHtml += listHtml.replace( '%s', listItemHtml );
							listItemHtml = '';
						}
					}

					$( 'span', countHolder ).html( responseData.post_count );
					resultsListInner
						.html( outputHtml )
						.data( 'columns', responseData.columns );

					resultsHeaderNav.html( responseData.results_navigation.in_header );
					resultsFooterNav.html( responseData.results_navigation.in_footer );

					if ( !countHolder[0] && !responseData.results_navigation.in_header ) {
						resultsHeader.addClass( 'is-empty' );
					} else {
						resultsHeader.removeClass( 'is-empty' );
					}

					if ( !fullResults[0] && !responseData.results_navigation.in_footer ) {
						resultsFooter.addClass( 'is-empty' );
					} else {
						resultsFooter.removeClass( 'is-empty' );
					}

					resultsHolder.addClass( 'show' );
					resultsListInner.imagesLoaded( function() {
						resultsList.css( 'height', $( settings.listSlideClass, resultsListInner ).eq(0).outerHeight() );
					} );

					$( document ).trigger( 'jet-ajax-search/show-results', [ resultsHolder ] );

				} else {
					self.outputMessage( message, 'show' );
					//self.hideList();
				}
			};

			self.errorCallback = function( jqXHR ) {
				if ( 'abort' !== jqXHR.statusText ) {
					spinner.removeClass( 'show' );
					self.outputMessage( data.server_error, 'error show' );
				}
			};

			self.ajaxSendData = function( sendData ) {
				var ajaxData = {
					action: self.ajaxSettings.action,
					//nonce: self.ajaxSettings.nonce,
					data: sendData || {}
				};

				self.ajaxRequest = jQuery.ajax( {
					type: 'GET',
					url: self.ajaxSettings.rest_api_url,
					data: ajaxData,
					dataType: 'json',
					cache: false,
					processData: true,
					beforeSend: function( jqXHR, ajaxSettings ) {
						if ( null !== self.ajaxRequest ) {
							self.ajaxRequest.abort();
						}
					},
					error: function( jqXHR, textStatus, errorThrown ) {
						self.errorCallback( jqXHR );
					},
					success: function( response, textStatus, jqXHR ) {
						self.successCallback( response );
					}
				} );
			};

			self.hideList = function() {
				resultsArea.removeClass( 'show' );
			};

			self.showList = function() {
				resultsArea.addClass( 'show' );
			};

			self.focusHandler = function( event ) {
				var value = event.target.value,
					symbolNumberForStart = 'number' === $.type( data.symbols_for_start_searching ) ? data.symbols_for_start_searching : 2;

				$( settings.searchFormClass, self ).addClass( formFocusClass );

				if ( 'number' === $.type( symbolNumberForStart ) && symbolNumberForStart > value.length ) {
					return;
				}

				self.showList();
			};

			self.blurHandler = function( event ) {
				$( settings.searchFormClass, self ).removeClass( formFocusClass );
			};

			self.outputMessage = function( message, messageClass ) {
				message = message.replace( /\\/g, '' ); // remove slashes
				message = $( "<div/>" ).html( message ).text();
				message = message.replace( /\\*"/g, '' );
				messageHolder.removeClass( 'error show' ).addClass( messageClass ).html( message );
			};

			self.formClick = function( event ) {
				event.stopPropagation();
			};

			self.clickFullResults = function( event ) {
				var searchInput = $( settings.inputClass, self );

				event.preventDefault();

				if ( searchInput.val().length != 0 || true === allowEmptyString ) {
					$( settings.searchFormClass, self ).submit();
				}
			};

			self.changeSlide = function( number ) {
				var currentSlide = $( settings.listSlideClass, resultsListInner ).eq( number ),
					direction = settings.isRtl ? 1 : -1,
					position = number * 100 * direction;

				currentSlide.scrollTop( 0 );
				resultsListInner.css( 'transform', 'translateX(' + position + '%)' );
				resultsList.css( 'height', currentSlide.outerHeight() );
			};

			self.clickBulletHandler = function( event ) {
				var target = $( event.target );

				currentPosition = target.data( 'number' );
				self.syncNavigation();

				self.changeSlide( currentPosition - 1 );
			};

			self.clickNavigationButtonHandler = function( event ) {
				var target    = $( event.target ),
					direction = target.data( 'direction' );

				currentPosition = currentPosition + direction;
				self.syncNavigation();

				self.changeSlide( currentPosition - 1 );
			};

			self.syncNavigation = function() {
				var lastPosition = resultsListInner.data( 'columns' ),
					disableClass = settings.disableNavClass.replace( '.', '' ),
					activeClass  = settings.activeNavClass.replace( '.', '' );

				$( settings.activeNavClass, self ).removeClass( activeClass );
				$( settings.disableNavClass, self ).removeClass( disableClass );

				$( settings.navButtonClass + '[data-number="' + currentPosition +'"]', self ).addClass( activeClass );

				if ( 1 === currentPosition ) {
					$( settings.prevClass, self ).addClass( disableClass );
				}

				if ( lastPosition === currentPosition ) {
					$( settings.nextClass, self ).addClass( disableClass );
				}
			};

			self.formSubmit = function( event ) {
				var value = event.target.value;

				if ( ( 1 > value.length && false === allowEmptyString ) && ( 13 === event.keyCode || 'click' === event.type ) ) {
					return false;
				} else {
					if ( 13 === event.keyCode && self.ajaxSettings.sumbitOnEnter ) {
						event.preventDefault();
						$( settings.searchFormClass, self ).submit();
					}
				}
			};

			self.changeHandler = function( event ) {
				var target              = $( event.target ),
					settingsInput       = $( settings.settingsInput, self ),
					querySettings       = JSON.parse( settingsInput.val() ),
					globalQuerySettings = $( self ).data( 'settings' );

				querySettings.category__in = target.val();
				globalQuerySettings.category__in = target.val();

				settingsInput.val( JSON.stringify( querySettings ) );
				$( self ).data( 'settings', globalQuerySettings );

				self.inputChangeHandler( { target: $( settings.inputClass, self ) } )
			};

			self.chosenFocusHandler = function() {
				self.hideList();
			};

			self.setResultsAreaWidth = function() {

				if ( 'fields_holder' !== data.results_area_width_by ) {
					return;
				}

				resultsArea.css( 'width', $( settings.fieldsHolderClass, self ).outerWidth() );
			};

			$( settings.inputClass, self )
				.on( 'input' + settings.searchClass, self.inputChangeHandler )
				.on( 'focus' + settings.searchClass, self.focusHandler )
				.on( 'blur' + settings.searchClass, self.blurHandler )
				.on( 'keypress' + settings.searchClass, self.formSubmit );

			$( settings.submitClass, self ).on( 'click' + settings.searchClass, self.clickFullResults );

			$( self )
				.on( 'click' + settings.searchClass, self.formClick )
				.on( 'touchend' + settings.searchClass, self.formClick )
				.on( 'click' + settings.searchClass, settings.fullResultsClass, self.clickFullResults )
				.on( 'click' + settings.searchClass, settings.countClass, self.clickFullResults )
				.on( 'click' + settings.searchClass, settings.bulletClass, self.clickBulletHandler )
				.on( 'click' + settings.searchClass, settings.numberClass, self.clickBulletHandler )
				.on( 'click' + settings.searchClass, settings.prevClass + ':not( ' + settings.disableNavClass + ' )', self.clickNavigationButtonHandler )
				.on( 'click' + settings.searchClass, settings.nextClass + ':not( ' + settings.disableNavClass + ' )', self.clickNavigationButtonHandler )
				.on( 'change', settings.chosenClass, self.changeHandler )
				.on( 'chosen:showing_dropdown', settings.chosenClass, self.chosenFocusHandler );

			if ( ! self.ajaxSettings.sumbitOnEnter ){
				$( window ).keydown( function( event ) {
					if ( 13 === event.keyCode && event.target.className.includes( 'jet-ajax-search' ) ) {
						event.preventDefault();
						return false;
					}
				});
			}

			// If after reloading the page the value of the select is not '0'.
			if ( '0' !== $( settings.chosenClass, self ).val() ) {
				$( settings.chosenClass, self ).trigger( 'change' );
			}

			$( 'body' )
				.on( 'click' + settings.searchClass, self.hideList )
				.on( 'touchend' + settings.searchClass, self.hideList );

			self.setResultsAreaWidth();
			$( window ).on( 'resize' + settings.searchClass, self.setResultsAreaWidth );

		} else {
			return 'is init: true';
		}
	};

	// initialize after all is defined
	$( window ).on( 'elementor/frontend/init', JetSearch.initElementor );
	JetSearch.initBlocks();

}( jQuery ));
