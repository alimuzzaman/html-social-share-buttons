/* Preserve stored legacy values without offering them on newly inserted elements. */
( function ( $, window, document ) {
	'use strict';

	const config = window.zm_sh || {};
	const legacyIconsets = config.legacyIconsets || {};

	function syncSelect( select, selected ) {
		if ( ! select || ! select.options ) {
			return;
		}

		Object.keys( legacyIconsets ).forEach( function ( id ) {
			let option = Array.prototype.find.call(
				select.options,
				function ( candidate ) {
					return candidate.value === id;
				}
			);
			if ( ! option ) {
				option = document.createElement( 'option' );
				option.value = id;
				option.textContent = legacyIconsets[ id ];
				select.appendChild( option );
			}

			const isSelected = selected === id;
			option.hidden = ! isSelected;
			option.disabled = ! isSelected;
		} );
	}

	function syncWpBakery( root ) {
		const scope = root && root.querySelectorAll ? root : document;
		scope
			.querySelectorAll( 'select.wpb_vc_param_value.iconset, select.iconset' )
			.forEach( function ( select ) {
				syncSelect( select, select.value );
			} );
	}

	function modelValue( model ) {
		if ( model && typeof model.getSetting === 'function' ) {
			return model.getSetting( 'iconset' );
		}
		if ( ! model || typeof model.get !== 'function' ) {
			return '';
		}

		const settings = model.get( 'settings' );
		if ( settings && typeof settings.get === 'function' ) {
			return settings.get( 'iconset' );
		}

		return settings && settings.iconset ? settings.iconset : '';
	}

	function registerElementor() {
		if (
			! config.elementorWidget ||
			! window.elementor ||
			! window.elementor.hooks ||
			typeof window.elementor.hooks.addAction !== 'function' ||
			window.hssbLegacyIconsetHookRegistered
		) {
			return;
		}

		window.hssbLegacyIconsetHookRegistered = true;
		window.elementor.hooks.addAction(
			'panel/open_editor/widget/' + config.elementorWidget,
			function ( panel, model ) {
				window.requestAnimationFrame( function () {
					const root = panel && panel.$el && panel.$el[ 0 ]
						? panel.$el[ 0 ]
						: document;
					root
						.querySelectorAll( '.elementor-control-iconset select' )
						.forEach( function ( select ) {
							syncSelect( select, modelValue( model ) );
						} );
				} );
			}
		);
	}

	$( document ).ready( function () {
		syncWpBakery( document );
		registerElementor();
		window.addEventListener( 'elementor/init', registerElementor );

		if ( window.MutationObserver && document.body ) {
			new window.MutationObserver( function ( mutations ) {
				mutations.forEach( function ( mutation ) {
					mutation.addedNodes.forEach( function ( node ) {
						syncWpBakery( node );
					} );
				} );
			} ).observe( document.body, { childList: true, subtree: true } );
		}
	} );

	$( document ).on(
		'change',
		'select.wpb_vc_param_value.iconset, select.iconset',
		function () {
			syncSelect( this, this.value );
		}
	);
} )( window.jQuery, window, document );
