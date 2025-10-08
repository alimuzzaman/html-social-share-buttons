( function () {
	'use strict';

	function toggleQr( button ) {
		const container = button.closest( '.hssb-wechat' );
		if ( ! container ) {
			return;
		}
		const qr = container.querySelector( '.hssb-wechat-qr' );
		if ( ! qr ) {
			return;
		}
		const isVisible = qr.classList.contains( 'hssb-wechat-qr--visible' );
		if ( isVisible ) {
			qr.classList.remove( 'hssb-wechat-qr--visible' );
			qr.style.display = 'none';
			button.setAttribute( 'aria-expanded', 'false' );
		} else {
			qr.classList.add( 'hssb-wechat-qr--visible' );
			qr.style.display = '';
			button.setAttribute( 'aria-expanded', 'true' );
		}
	}

	function onDocumentClick( e ) {
		const btn = e.target.closest( '.hssb-wechat-btn' );
		if ( btn ) {
			e.preventDefault();
			toggleQr( btn );
			return;
		}

		// Click outside any QR container closes all visible QR areas
		const openQrs = document.querySelectorAll(
			'.hssb-wechat-qr.hssb-wechat-qr--visible'
		);
		if ( openQrs.length ) {
			openQrs.forEach( function ( q ) {
				q.classList.remove( 'hssb-wechat-qr--visible' );
				q.style.display = 'none';
				const container = q.closest( '.hssb-wechat' );
				if ( container ) {
					const b = container.querySelector( '.hssb-wechat-btn' );
					if ( b ) {
						b.setAttribute( 'aria-expanded', 'false' );
					}
				}
			} );
		}
	}

	function onKeydown( e ) {
		if ( e.key === 'Escape' || e.key === 'Esc' ) {
			const openQrs = document.querySelectorAll(
				'.hssb-wechat-qr.hssb-wechat-qr--visible'
			);
			openQrs.forEach( function ( q ) {
				q.classList.remove( 'hssb-wechat-qr--visible' );
				q.style.display = 'none';
				const container = q.closest( '.hssb-wechat' );
				if ( container ) {
					const b = container.querySelector( '.hssb-wechat-btn' );
					if ( b ) {
						b.setAttribute( 'aria-expanded', 'false' );
					}
				}
			} );
		}
	}

	document.addEventListener( 'click', onDocumentClick );
	document.addEventListener( 'keydown', onKeydown );
} )();
