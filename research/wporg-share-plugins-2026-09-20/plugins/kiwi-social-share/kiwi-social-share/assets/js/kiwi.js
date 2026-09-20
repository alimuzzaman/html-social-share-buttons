"use strict";

var kiwi = {
	loaded: 0,

	/**
	 * Interface related methods
	 */
	interface: {
		/**
		 * Listen on option changes and provide a notification
		 * @param $
		 */
		saveChangesNotification: function ($) {
			var context = $('#sl-kiwi'),
					notification = context.find('.kiwi-notification-popup'),
					submitter = notification.find('input'),
					form = context.find('form').find('#submit');

			context.find('input').on('change', function () {
				notification.slideDown(300);
			});

			submitter.on('click', function () {
				form.click();
			})
		},

		/**
		 * Handle the preloader
		 * @param $
		 */
		preloader: function ($) {
			var context = $('#sl-kiwi'),
					overlay = context.find('.overlay'),
					floating_bar = $('ul[data-id="networks_ordering_floating_bar"]');

			setTimeout(function () {
				overlay.find('.overlay-content').fadeOut(400, function () {
					overlay.removeClass('active').css({ 'z-index': 0, 'bottom': 'initial', 'right': 'initial' });
					floating_bar.css({ right: '-46px', 'opacity': 1, 'z-index': 1 });
				});
			}, 1500);
		},

		/**
		 * Init the wp color picker and handle changes
		 *
		 * @param $
		 */
		colorFields: function ($) {
			var colorpickers = $('.kiwi-styles .epsilon-ui-color input'),
					customColorPickers = $('.kiwi-styles .epsilon-custom-colors .epsilon-ui-color input'),
					networks = $('.shift a');

			colorpickers.wpColorPicker();
			$('input[name="kiwi_general_settings[styles_colors]"]:radio').change(function () {
				var scheme = $(this).val();

				switch ( scheme ) {
					case 'custom':
						$.each(customColorPickers, function () {
							$(this).val($(this).data('color-custom')).trigger('change');
						});
						break;
					case 'monochrome':
						$.each(customColorPickers, function () {
							$(this).val($(this).data('color-monochrome')).trigger('change');
						});
						break;
					default:
						$.each(customColorPickers, function () {
							$(this).val($(this).data('color-original')).trigger('change');
						});
						break;
				}
			});

			$.each(colorpickers, function () {
				var parent = $(this).parents('.epsilon-ui-color'),
						em = parent.find('em'),
						self = $(this),
						network = parent.parent().parent().data('network'),
						sortable = $('.shift').find('.kiwi-nw-' + network),
						currentColor = self.wpColorPicker('color'),
						property = parent.attr('data-prop');

				$(this).wpColorPicker({
					'change': function (event, ui) {
						var val = self.wpColorPicker('color');
						em.html(val);
					}
				})
			});


			var fbem = $('.floating-bar-background input');
			if ( fbem.length ) {
				fbem.wpColorPicker({
					defaultColor: '#272F32',
					change      : function (event, ui) {
						var val = fbem.wpColorPicker('color');

						$('.floating-bar-background').find('em').html(val);
					}
				});
			}


			kiwi.loaded += 1;
		},

		/**
		 * Disable the keyboard arrows on this elements while the toggle is off
		 * @param $
		 */
		toggleHelper: function ($) {
			$(".epsilon-ui-option input").keypress(function (event) {
				var overlay_active = $(this).closest('.epsilon-ui-option').find('.epsilon-ui-overlay.active').length;

				if ( overlay_active && event.keyCode > 36 && event.keyCode < 41 ) {
					event.preventDefault();
				}
			});

			kiwi.loaded += 1;
		},

		/**
		 * Tab functionality
		 *
		 * @param $
		 */
		tabs: function ($) {
			$('.epsilon-ui-tabs a').click(function () {

				if ( $(this).closest('li').hasClass('selected') ) {
					return 0;
				}

				var tab = $(this).attr('data-tab');

				$(this).closest('ul').find('.selected').removeClass('selected');
				$(this).closest('li').addClass('selected');

				$('.sl-kiwi-content .epsilon-tab-active').hide().removeClass('epsilon-tab-active');
				$('.sl-kiwi-content .' + tab).show(700).addClass('epsilon-tab-active');

				document.cookie = 'tab=' + tab;
				$(document).trigger('KiwiTabChanged');
				return false;
			});
			kiwi.loaded += 1;
		}
	},

	/**
	 * UI / UX interactions
	 */
	interactions: {

		/**
		 * Hide/Show checked networks in the list
		 *
		 * @param $
		 */
		networks: function ($) {

			$('.sl-kiwi-tab-networks .sl-kiwi-networks input[data-source="article-bar"]').on('click change', function () {

				var social_item = $(this).attr('data-list-item');

				if ( $(this).is(':checked') ) {
					$('.sl-kiwi-dragdrop ul[data-id="networks_ordering"] li .' + social_item).closest('li').removeClass('sl-kiwi-item-remove').addClass('sl-kiwi-item-add');
				} else {
					$('.sl-kiwi-dragdrop ul[data-id="networks_ordering"] li .' + social_item).closest('li').removeClass('sl-kiwi-item-add').addClass('sl-kiwi-item-remove');
				}

			});

			$('.sl-kiwi-tab-networks .sl-kiwi-networks input[data-source="floating-bar"]').on('click change', function () {

				var social_item = $(this).attr('data-list-item');

				if ( $(this).is(':checked') ) {
					$('.sl-kiwi-dragdrop ul[data-id="networks_ordering_floating_bar"] li .' + social_item).closest('li').removeClass('sl-kiwi-item-remove').addClass('sl-kiwi-item-add');
				} else {
					$('.sl-kiwi-dragdrop ul[data-id="networks_ordering_floating_bar"] li .' + social_item).closest('li').removeClass('sl-kiwi-item-add').addClass('sl-kiwi-item-remove');
				}

			});

			kiwi.loaded += 1;
		},

		/**
		 * Change the bar style on radio clicks
		 *
		 * @param $
		 */
		articleStyling: function ($) {
			$('input[name="kiwi_general_settings[article_bar_style]"]:radio').change(function () {
				$('.sl-kiwi-dragdrop').find("[data-id='networks_ordering']").attr('data-style', this.value);
			});
		},

		/**
		 * Toggle functionality ( creates an overlay on the closest option group )
		 *
		 * @param $
		 */
		toggles: function ($) {
			$('.epsilon-ui-toggle input').click(function () {

				if ( $(this).prop('checked') ) {
					$(this).val('on');
				} else {
					$(this).val('');
				}

				var group = $(this).closest('.sl-kiwi-opt-group');

				if ( $(this).is(':checked') ) {
					$('.epsilon-ui-overlay', group).removeClass('active');
				} else {
					$('.epsilon-ui-overlay', group).addClass('active');
				}
			});
			kiwi.loaded += 1;
		},

		/**
		 * Toggle an overlay on radio elements ( used in styles tab )
		 *
		 * @param $
		 */
		radioToggles: function ($) {
			$('.sl-kiwi-radio-post-types .epsilon-ui-radio input').click(function () {
				if ( $(this).val() === 'all' ) {
					$(this).parent().parent().find('.epsilon-ui-checklist').slideUp();
				} else if ( $(this).val() === 'custom' ) {
					$(this).parent().parent().find('.epsilon-ui-checklist').slideDown();
				}
			});

			$('.epsilon-ui-radio-toggle input').click(function () {
				if ( $(this).val() === 'original' ) {
					$(this).parents('.sl-kiwi-tab-networks').find('.epsilon-ui-overlay').addClass('active');
				} else {
					$(this).parents('.sl-kiwi-tab-networks').find('.epsilon-ui-overlay').removeClass('active');
				}
			});
			kiwi.loaded += 1;
		},

		/**
		 * Change the shapes of the network list elements
		 *
		 * @param $
		 */
		shapes: function ($) {

			$('input[name="kiwi_general_settings[button_shape]"]:radio').change(function () {
				$('.sl-kiwi-dragdrop').find("[data-id='networks_ordering']").attr('class', this.value);
			});

			$('input[name="kiwi_general_settings[button_shape_floating]"]:radio').change(function () {
				$('.sl-kiwi-dragdrop').find("[data-id='networks_ordering_floating_bar']").attr('class', this.value);
			});
			kiwi.loaded += 1;
		},

		/**
		 * Toggle selection of all networks in article and floating bar
		 *
		 * @param $
		 */
		allCheckbox: function ($) {
			var state,
					floatingBar = $('input[name="kiwi_general_settings[networks_floating_bar][]"]'),
					floatingBarChecked = $('input[name="kiwi_general_settings[networks_floating_bar][]"]:checked'),
					articleBar = $('input[name="kiwi_general_settings[networks_article_bar][]"]'),
					articleBarChecked = $('input[name="kiwi_general_settings[networks_article_bar][]"]:checked');


			if ( floatingBar.length === floatingBarChecked.length ) {
				$('#floating-bar-all').prop('checked', true);
			}

			if ( articleBar.length === articleBarChecked.length ) {
				$('#social-bar-all').prop('checked', true);
			}


			$('#social-bar-all, #floating-bar-all').on('click', function (event) {
				switch ( $(this).attr('id') ) {
					case 'floating-bar-all':
						state = $(this).prop('checked');
						$('input[name="kiwi_general_settings[networks_floating_bar][]"]').prop('checked', state).change();
						break;
					default:
						state = $(this).prop('checked');
						$('input[name="kiwi_general_settings[networks_article_bar][]"]').prop('checked', state).change();
						break;
				}
			});
		},

		/**
		 * Sorting functionality on the network list
		 *
		 * @param $
		 */
		sortable: function ($) {
			var kiwi_sortable = $(".sl-kiwi-dragdrop ul[data-id='networks_ordering']");
			kiwi_sortable.sortable({
				placeholder: "sl-kiwi-dragdrop-placeholder",
				stop       : function (event, ui) {
					var elements = [],
							input = $(this).attr('data-id');

					$('.sl-kiwi-dragdrop ul[data-id="networks_ordering"] li').each(function (index, elem) {
						var kiwi_pos = $(elem).attr('data-item');
						elements.push(kiwi_pos);
					});

					var serialize_elem = elements.join();
					$('#kiwi_' + input).val(serialize_elem).trigger('change');
				},
				create     : function (event, ui) {
					kiwi.loaded += 1
				}
			}).disableSelection();
		},

		/**
		 * Handle the modal content
		 *
		 * @param $
		 */
		upsellModalPage: function ($) {
			var context = $('.kiwi-modal');

			$('.ui-locked, .epsilon-ui-locked, .epsilon-locked').on('click', function (e) {
				context.fadeIn(200, function () {
					context.addClass('in');
				});
			});

			$('input[name="kiwi_product_upsell[type]"]').on('change', function () {
				if ( $(this).val() === 'business' ) {
					$('.kiwi-modal').find('.company-group').fadeIn(300);
				} else {
					context.find('.company-group').fadeOut(300);
					context.find('.company-group input').val('');
				}
			});

			$('.kiwi-modal .close-modal').on('click', function (e) {
				e.preventDefault();

				context.removeClass('in');
				setTimeout(function () {
					context.fadeOut(200);
					context.find('#page-one').show();
					context.find('#page-two').hide();
					context.find('.modal-footer').show();
					context.find('.modal-title').text(kiwi_locale.kiwi_step_one_title);
					context.find('.modal-subtitle').text(kiwi_locale.kiwi_step_one_subtitle);
				}, 200);
			});

			$('.button-modal').on('click', function (e) {
				e.preventDefault();
				var action = $(this).attr('data-action');
				if ( action !== 'modal-buy-now' ) {
					return;
				}
				var current_page = $('#page-one'),
						next_page = $('#page-two'),
						footer = $('.kiwi-modal .modal-footer'),
						title_container = $('.kiwi-modal .modal-header-content'),
						title = title_container.find('.modal-title'),
						subtitle = title_container.find('.modal-subtitle');

				title.fadeOut(300, function () {
					title.text(kiwi_locale.kiwi_step_two_title);
					title.fadeIn(300);
				});

				subtitle.fadeOut(300, function () {
					subtitle.text(kiwi_locale.kiwi_step_two_subtitle);
					subtitle.fadeIn(300);
				});

				footer.fadeOut(300);
				current_page.fadeOut(300, function () {
					next_page.fadeIn(300);
				});
			});
		},

		/**
		 * Handle the form on the modal upsell
		 * @param $
		 */
		handleModalPageForm: function ($) {
			$('.button-modal').on('click', function (e) {
				e.preventDefault();
				var action = $(this).attr('data-action');

				if ( action !== 'continue-to-checkout' ) {
					return;
				}

				var fields = {
					"customer": {
						"first_name"  : $('input[name="kiwi_product_upsell[first_name]"]').val(),
						"last_name"   : $('input[name="kiwi_product_upsell[last_name]"]').val(),
						"email"       : $('input[name="kiwi_product_upsell[email]"]').val(),
						"vat"         : $('input[name="kiwi_product_upsell[vat]"]').val(),
						"company_name": $('input[name="kiwi_product_upsell[company_name]"]').val()
					},
					"product" : {
						"id"      : $('input[name="kiwi_product_upsell[product]"]').val(),
						"options" : {},
						"quantity": "1"
					}
				};

				var obj = JSON.stringify(fields);
				var hash = encodeURIComponent(btoa(obj));

				var newwindow = window.open('https://www.machothemes.com/checkout/?mthash=' + hash);

				if ( window.focus ) {
					newwindow.focus()
				}
			});
		}
	},

	frontend: {
		/**
		 * Handles the popup window on click events
		 * @param $
		 */
		modalPopup: function ($) {
			$('a[data-class="popup"]').on('click', function (e) {
				e.preventDefault();
				var kiwiTracking;

				var newwindow = window.open($(this).attr('href'), '', 'height=500,width=500');
				if ( window.focus ) {
					newwindow.focus()
				}

				var network = $(this).attr('data-network'),
						parent = $(this).parent(),
						context;

				if ( parent.is('li') ) {
					context = parent.parent().attr('data-tracking-container');
					kiwiTracking = kiwi.frontend.checkTracking(this);
				} else if ( parent.is('blockquote') ) {
					context = 'click-to-tweet';
					kiwiTracking = 'true' === $(this).attr('data-tracking');
				}

				if ( typeof ga === "function" && true === kiwiTracking ) {
					ga('send', 'event', 'kiwi_social_media', 'kiwi_' + context + '_' + network + '_share');
				}

				return false;
			});
		},

		/**
		 * Highlighted content
		 */
		highlighted: {
			start  : 0,
			end    : 0,
			url    : null,
			title  : null,
			content: null
		},

		/**
		 * Highlight share functionality
		 * @param $
		 */
		highlightShare: function ($) {
			var areas = $('.kiwi-highlighter-excerpt-area, .kiwi-highlighter-content-area');

			$('.kiwi-highlight-sharer a').on('click', function (e) {
				e.preventDefault();
				var proxy = kiwi.frontend.highlighted,
						href = null;

				kiwi.frontend.highlighted = {};

				href = this.href.replace('%url%', encodeURIComponent(proxy.url));
				href = href.replace('%text%', encodeURIComponent(proxy.content));
				window.open(href, "tweethighlight", "width=575,height=430,toolbar=false,menubar=false,location=false,status=false");

				var kiwiTracking = 'true' === $(this).attr('data-tracking');

				if ( typeof ga === "function" && true === kiwiTracking ) {
					var network = 'twitter',
							context = 'highlight-to-tweet';

					ga('send', 'event', 'kiwi_social_media', 'kiwi_' + context + '_' + network + '_share');
				}

				$('.kiwi-highlight-sharer').css({
					display: 'none'
				});

				return false;
			});

			$('body').on('mousedown vmouseup', function (e) {
				var parent = $(e.target).parent(),
						sharer = $('.kiwi-highlight-sharer');

				if ( parent.hasClass('kiwi-highlight-sharer') || parent.is('a') ) {
					return;
				}

				if ( sharer.is(':not(:hidden)') ) {
					sharer.css({
						display: 'none'
					});
				}
			});

			areas.on('mousedown vmouseup', function (e) {
				kiwi.frontend.highlighted.start = e.pageX;
				var sharer = $('.kiwi-highlight-sharer');
				if ( sharer.is(':not(:hidden)') ) {
					sharer.css({
						display: 'none'
					});
				}

			});

			areas.on('mouseup vmouseup', function (e) {
				var selection = kiwi.frontend.getSelected(),
						sharer = $('.kiwi-highlight-sharer');

				kiwi.frontend.highlighted.end = e.pageX;
				if ( typeof selection === 'undefined' ) {
					sharer.css({ display: 'none' });
					return;
				}

				var object = {
					start  : e.pageX,
					end    : e.pageX,
					url    : $(this).attr('data-url'),
					title  : $(this).attr('data-title'),
					content: selection
				};

				var offset = parseInt(Math.abs((kiwi.frontend.highlighted.start - kiwi.frontend.highlighted.end) / 2));

				sharer.css({
					position: 'absolute',
					left    : e.pageX - offset,
					top     : e.pageY - 50,
					display : 'block'
				});

				kiwi.frontend.highlighted = object;
			});

			document.addEventListener("selectionchange", function () {
				kiwi.frontend.getSelected();
			}, false);

		},

		/**
		 * Get selection text
		 *
		 * @returns {string}
		 */
		getSelected: function () {
			var selection = window.getSelection();
			var text = selection.toString();
			if ( '' === text ) {
				return;
			}

			return text;
		},

		/**
		 * Check tracking helper
		 *
		 * @param element
		 * @returns {boolean}
		 */
		checkTracking: function (element) {
			var parent = jQuery(element).parents('ul');
			return 'true' === parent.attr('data-tracking');
		},

		/**
		 * Animate the floating bar
		 * @param $
		 */
		handleFloatingBarDisplay: function ($) {
			var element = $('.kiwi-floating-bar'),
					position;
			if ( element.hasClass('bottom') ) {
				position = 'bottom';
			} else if ( element.hasClass('right') ) {
				position = 'right';
			} else {
				position = 'left';
			}

			setTimeout(function () {
				switch ( position ) {
					case 'bottom':
						element.css({ 'bottom': 0 });
						break;
					case 'right':
						element.css({ 'right': 0 });
						break;
					default:
						element.css({ 'left': 0 });
						break;
				}
			}, 300);
		}
	},

	/**
	 * Initiate the helper functions
	 *
	 * @param $
	 */
	init: function ($) {
		/**
		 * Interface methods
		 */
		kiwi.interface.tabs($);
		kiwi.interface.toggleHelper($);
		kiwi.interface.colorFields($);
		kiwi.interface.saveChangesNotification($);
		/**
		 * Interaction handlers
		 */
		kiwi.interactions.toggles($);
		kiwi.interactions.radioToggles($);
		kiwi.interactions.networks($);
		kiwi.interactions.shapes($);
		kiwi.frontend.modalPopup($);
		kiwi.interactions.sortable($);
		kiwi.interactions.allCheckbox($);
		kiwi.interactions.articleStyling($);

		/**
		 * Preloader handler
		 *
		 * @type {number}
		 */
		var loader = setInterval(function () {
			if ( kiwi.loaded >= 8 ) {
				$(document).trigger('KiwiObjectsLoaded');
				clearInterval(loader);
			}
		}, 100);
	}
};
