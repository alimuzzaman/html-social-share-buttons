/**
 * Main.
 *
 * @package SimpleShareButtonsAdder
 */

/* exported Main */
var Main = ( function( $, FB ) {
	'use strict';

	// These open a chat prefilled with a prompt in a new tab, not a share popup.
	var aiSites = [ 'chatgpt', 'claude', 'grok', 'perplexity' ];

	// These take no prompt in the URL, so it is copied to the clipboard instead.
	var aiCopySites = [ 'copilot', 'gemini' ];

	// Shown when the prompt travels over the clipboard, and how long to hold the tab.
	var AI_COPY_MESSAGE = 'Prompt copied — paste it into the chat';
	var AI_COPY_DELAY = 600;

	// How long the copy notice sits above the button, its fade, and its screen gutter.
	var COPY_NOTICE_HOLD = 1800;
	var COPY_NOTICE_FADE = 400;
	var COPY_NOTICE_MARGIN = 8;

	return {
		/**
		 * Holds data.
		 */
		data: {},

		/**
		 * Boot plugin.
		 *
		 * @param data
		 */
		boot: function( data ) {
			this.data = data;

			$( document ).ready(
				function() {
					this.init();
				}.bind( this )
			);
		},

		/**
		 * Initialize plugin.
		 */
		init: function() {
			this.listen();
			this.removeP();
		},

		/**
		 * Listener event.
		 */
		listen: function() {
			var self = this;

			// Upon clicking a share button.
			$( 'body' ).on(
				'click',
				'.ssbp-wrap a',
				function( event ) {

					// Don't go the the href yet.
					event.preventDefault();
					self.engageShareButton( this );
				}
			);
		},

		/**
		 * Open an AI assistant in a new tab rather than a popup.
		 *
		 * @param url
		 */
		openAiTab: function( url ) {
			var tab = window.open( url, '_blank' );

			if ( tab ) {
				tab.opener = null;
			}
		},

		/**
		 * Copy text to the clipboard.
		 *
		 * Fire and forget, so the caller stays inside the click gesture.
		 *
		 * @param text
		 */
		copyToClipboard: function( text ) {
			if ( ! text ) {
				return;
			}

			if ( navigator.clipboard && navigator.clipboard.writeText ) {
				navigator.clipboard.writeText( text );
				return;
			}

			// Fallback for browsers without the async clipboard API.
			var field   = document.createElement( 'textarea' );
			field.value          = text;
			field.setAttribute( 'readonly', '' );
			field.style.position = 'fixed';
			field.style.left     = '-9999px';
			document.body.appendChild( field );
			field.select();

			try {
				document.execCommand( 'copy' );
			} catch ( error ) {
			}

			field.parentNode.removeChild( field );
		},

		/**
		 * Show a transient message above a share button.
		 *
		 * The notice is anchored to the top edge of the button so it never covers
		 * the icon, and is nudged back inside the viewport when it would spill out.
		 *
		 * @param event
		 * @param message
		 */
		showCopyNotice: function( event, message ) {
			var button = $( event ).get( 0 );

			if ( ! button ) {
				return;
			}

			// The notice is positioned against the button, so the button has to be
			// a containing block. Themes leave these links statically positioned.
			if ( 'static' === window.getComputedStyle( button ).position ) {
				button.style.position = 'relative';
			}

			var notice       = document.createElement( 'span' );
			notice.innerText = message;
			notice.className = 'copy-notify ssba-copy-notify';
			button.appendChild( notice );

			// Pull the notice back on screen if centring pushed it past an edge.
			var bounds = notice.getBoundingClientRect();
			var overflowLeft  = COPY_NOTICE_MARGIN - bounds.left;
			var overflowRight = bounds.right - ( document.documentElement.clientWidth - COPY_NOTICE_MARGIN );

			if ( 0 < overflowLeft ) {
				notice.style.marginLeft = Math.round( overflowLeft ) + 'px';
			} else if ( 0 < overflowRight ) {
				notice.style.marginLeft = '-' + Math.round( overflowRight ) + 'px';
			}

			window.setTimeout(
				function() {
					notice.className += ' ssba-copy-notify--hiding';

					window.setTimeout(
						function() {
							if ( notice.parentNode ) {
								notice.parentNode.removeChild( notice );
							}
						},
						COPY_NOTICE_FADE
					);
				},
				COPY_NOTICE_HOLD
			);
		},

		/**
		 * Share button popup
		 *
		 * @param event
		 */
		engageShareButton: function( event ) {

			// If it's facebook mobile.
			if ( 'mobile' === $( event ).data( 'facebook' ) ) {
				FB.ui(
					{
						method: 'share',
						mobile_iframe: true,
						href: $( event ).data( 'href' )
					},
					function( response ) {}
				);
			} else if ( -1 !== aiSites.indexOf( $( event ).data( 'site' ) ) ) {

				// AI assistants open a chat in a new tab rather than a popup.
				this.openAiTab( $( event ).attr( 'href' ) );
			} else if ( -1 !== aiCopySites.indexOf( $( event ).data( 'site' ) ) ) {

				// These take no prompt in the URL, so hand it over the clipboard.
				this.copyToClipboard( $( event ).data( 'prompt' ) || '' );
				this.showCopyNotice( event, AI_COPY_MESSAGE );

				// Let the notice land before the new tab takes focus. The delay stays
				// well inside the user-activation window, so the tab is not blocked.
				setTimeout( this.openAiTab.bind( this, $( event ).attr( 'href' ) ), AI_COPY_DELAY );
			} else {
				// These share options don't need to have a popup.
				if ( 'copy' === $( event ).data( 'site' ) || 'email' === $( event ).data( 'site' ) || 'print' === $( event ).data( 'site' ) || 'pinterest' === $( event ).data( 'site' ) ) {
					if ( 'copy' === $( event ).data( 'site' ) ) {
						this.copyToClipboard( $( event ).attr( 'href' ) );
						this.showCopyNotice( event, 'URL Copied!' );
					} else {
						// Just redirect.
						window.location.href = $( event ).attr( 'href' );
					}
				} else {

					// Prepare popup window.
					var width  = 575,
						height = 520,
						left   = ( $( window ).width() - width ) / 2,
						top    = ( $( window ).height() - height ) / 2,
						opts   = 'status=1' +
								',width=' + width +
								',height=' + height +
								',top=' + top +
								',left=' + left;

					// Open the share url in a smaller window.
					window.open( $( event ).attr( 'href' ), 'share', opts );
				}
			}
		},

		/**
		 * Remove generated p tag from facebook save button.
		 */
		removeP: function() {
		}
	};
} )( window.jQuery, window.FB );
