'use strict';

const assert = require( 'node:assert/strict' );
const fs = require( 'node:fs' );
const path = require( 'node:path' );
const vm = require( 'node:vm' );

const bundlePath = path.join( __dirname, '..', 'build', 'vc-scripts.js' );
const bundle = fs.readFileSync( bundlePath, 'utf8' );
const changeHandlers = {};
let responseHandler;
let request;

function option( value ) {
	return { value, hidden: false, disabled: false };
}

const wpBakeryIconset = {
	value: 'bootstrap-solid',
	options: [ option( 'bootstrap-solid' ), option( 'default' ) ],
	appendChild( child ) {
		this.options.push( child );
	},
};
const elementorIconset = {
	value: 'default',
	options: [ option( 'bootstrap-solid' ), option( 'default' ) ],
	appendChild( child ) {
		this.options.push( child );
	},
};

const container = {
	children: [],
	set textContent( value ) {
		this.children = [];
		this._textContent = value;
	},
	get textContent() {
		return this._textContent;
	},
	appendChild( child ) {
		this.children.push( child );
	},
};
const documentStub = {
	body: {},
	querySelector() {
		return container;
	},
	querySelectorAll( selector ) {
		return selector.includes( 'wpb_vc_param_value' ) ? [ wpBakeryIconset ] : [];
	},
	createElement( tagName ) {
		return {
			tagName,
			children: [],
			appendChild( child ) {
				this.children.push( child );
			},
		};
	},
	createTextNode( value ) {
		return { nodeType: 3, textContent: value };
	},
};
const selectedIconset = {};
function jQuery( subject ) {
	if ( subject === documentStub ) {
		return {
			ready( callback ) {
				callback();
			},
			on( event, selector, callback ) {
				assert.equal( event, 'change' );
				changeHandlers[ selector ] = callback;
			},
		};
	}
	if ( subject === selectedIconset ) {
		return {
			val() {
				return 'flat';
			},
		};
	}
	throw new Error( 'Unexpected jQuery subject.' );
}
jQuery.post = function ( url, data, callback ) {
	request = { url, data };
	responseHandler = callback;
};

const context = {
	Array,
	document: documentStub,
	JSON,
	Object,
	String,
	window: {
		ajaxurl: '/wp-admin/admin-ajax.php',
		addEventListener() {},
		requestAnimationFrame( callback ) {
			callback();
		},
		elementor: {
			hooks: {
				addAction( name, callback ) {
					context.elementorHook = { name, callback };
				},
			},
		},
		jQuery,
		zm_sh: {
			nonce: 'contract-nonce',
			elementorWidget: 'zm_social_share',
			legacyIconsets: { default: 'Default (legacy)' },
		},
	},
};
vm.runInNewContext( bundle, context, { filename: bundlePath } );

const wpBakeryDetailsHandler = changeHandlers[ '.iconset' ];
assert.equal( typeof wpBakeryDetailsHandler, 'function' );
wpBakeryDetailsHandler.call( selectedIconset );
assert.equal( request.url, '/wp-admin/admin-ajax.php' );
assert.equal( request.data.action, 'get_iconset_details' );
assert.equal( request.data.iconset, 'flat' );
assert.equal( request.data.nonce, 'contract-nonce' );

responseHandler( {
	facebook: 'Facebook',
	x: '<img src=x onerror=alert(1)>',
} );
assert.equal( container.children.length, 2 );
assert.equal( container.children[ 0 ].children[ 0 ].id, 'icons-facebook' );
assert.equal( container.children[ 0 ].children[ 1 ].textContent, 'Facebook' );
assert.equal(
	container.children[ 1 ].children[ 1 ].textContent,
	'<img src=x onerror=alert(1)>'
);

responseHandler( '{"telegram":"Telegram"}' );
assert.equal( container.children.length, 1 );
assert.equal( container.children[ 0 ].children[ 0 ].value, 'telegram' );
assert.equal( container.children[ 0 ].children[ 1 ].textContent, 'Telegram' );

const legacyVisibilityHandler =
	changeHandlers[ 'select.wpb_vc_param_value.iconset, select.iconset' ];
assert.equal( typeof legacyVisibilityHandler, 'function' );
assert.equal( wpBakeryIconset.options[ 1 ].hidden, true );
assert.equal( wpBakeryIconset.options[ 1 ].disabled, true );
wpBakeryIconset.value = 'default';
legacyVisibilityHandler.call( wpBakeryIconset );
assert.equal( wpBakeryIconset.options[ 1 ].hidden, false );
assert.equal( wpBakeryIconset.options[ 1 ].disabled, false );

assert.equal(
	context.elementorHook.name,
	'panel/open_editor/widget/zm_social_share'
);
context.elementorHook.callback(
	{
		$el: [ {
			querySelectorAll() {
				return [ elementorIconset ];
			},
		} ],
	},
	{
		getSetting() {
			return 'default';
		},
	}
);
assert.equal( elementorIconset.options[ 1 ].hidden, false );
assert.equal( elementorIconset.options[ 1 ].disabled, false );

console.log( 'Builder editor bundle smoke test passed.' );
