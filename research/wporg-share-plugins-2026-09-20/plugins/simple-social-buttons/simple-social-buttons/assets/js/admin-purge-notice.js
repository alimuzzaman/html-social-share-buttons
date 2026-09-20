/**
 * Auto-dismiss SSB purge admin notices after a delay.
 */
( function () {
	'use strict';

	var notices = document.querySelectorAll( '.ssb-purge-notice[data-ssb-purge-autodismiss="1"]' );
	if ( ! notices.length ) {
		return;
	}

	var delayMs = 6000;

	notices.forEach( function ( notice ) {
		setTimeout( function () {
			notice.style.transition = 'opacity 0.4s ease';
			notice.style.opacity = '0';
			setTimeout( function () {
				if ( notice.parentNode ) {
					notice.parentNode.removeChild( notice );
				}
			}, 400 );
		}, delayMs );
	} );
} )();
