/**
 * Upgrade submenu behavior outside the plugin screen.
 *
 * @param {jQuery} $ jQuery object.
 */
( function ( $ ) {
	'use strict';

	$( function () {
		$( '.ssb-sidebar-upgrade-pro a' )
			.attr( 'target', '_blank' )
			.attr( 'rel', 'noopener noreferrer' );
	} );
}( jQuery ) );
