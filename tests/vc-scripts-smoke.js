'use strict';

const assert = require( 'node:assert/strict' );
const fs = require( 'node:fs' );
const path = require( 'node:path' );
const vm = require( 'node:vm' );

const bundlePath = path.join( __dirname, '..', 'build', 'vc-scripts.js' );
const bundle = fs.readFileSync( bundlePath, 'utf8' );
let changeHandler;
let responseHandler;
let request;

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
	querySelector() {
		return container;
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
				assert.equal( selector, '.iconset' );
				changeHandler = callback;
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
	document: documentStub,
	JSON,
	Object,
	String,
	window: {
		ajaxurl: '/wp-admin/admin-ajax.php',
		jQuery,
		zm_sh: { nonce: 'contract-nonce' },
	},
};
vm.runInNewContext( bundle, context, { filename: bundlePath } );

assert.equal( typeof changeHandler, 'function' );
changeHandler.call( selectedIconset );
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

console.log( 'WPBakery editor bundle smoke test passed.' );
