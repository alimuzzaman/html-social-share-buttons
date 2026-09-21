#!/usr/bin/env node
const assert = require('assert');
const fs = require('fs');
const vm = require('vm');

const source = fs
	.readFileSync('src/js/browser-url.js', 'utf8')
	.replace(/export\s+\{\};?\s*$/, '');

function createElement(attributes, parentNode) {
	const values = Object.assign({}, attributes);
	return {
		parentNode: parentNode || null,
		getAttribute(name) {
			return Object.prototype.hasOwnProperty.call(values, name) ? values[name] : null;
		},
		setAttribute(name, value) {
			values[name] = String(value);
		},
		value(name) {
			return values[name];
		},
	};
}

function run(attributes, locationHref) {
	const listeners = {};
	const documentListeners = {};
	const root = createElement({ 'data-hssb-browser-url': '1' });
	const link = createElement(attributes, root);
	const document = {
		readyState: 'complete',
		documentElement: {},
		addEventListener(name, callback) {
			documentListeners[name] = callback;
		},
		querySelectorAll() {
			return [link];
		},
	};
	const window = {
		location: { href: locationHref },
		MutationObserver: class {
			observe() {}
		},
		addEventListener(name, callback) {
			listeners[name] = callback;
		},
	};

	vm.runInNewContext(source, { window, document, encodeURIComponent, JSON });

	return { link, listeners, documentListeners };
}

const repeated = run({
	'data-hssb-browser-descriptor': JSON.stringify({
		permalink_slot: '%%permalink%%',
		template: 'https://example.test/?first=%%permalink%%&second=%%permalink%%',
	}),
	'data-hssb-server-href': 'https://fallback.example/',
	}, 'https://site.example/article/?q=a%2Bb#part');

assert.strictEqual(
	repeated.link.value('href'),
	'https://example.test/?first=https%3A%2F%2Fsite.example%2Farticle%2F%3Fq%3Da%252Bb%23part&second=https%3A%2F%2Fsite.example%2Farticle%2F%3Fq%3Da%252Bb%23part'
);

const invalid = run({
	'data-hssb-browser-descriptor': JSON.stringify({
		permalink_slot: '%%permalink%%',
		template: 'https://example.test/?title=%%title%%',
	}),
	'data-hssb-server-href': 'https://fallback.example/',
}, 'https://site.example/article/');
assert.strictEqual(invalid.link.value('href'), 'https://fallback.example/');

const malformed = run({
	'data-hssb-browser-descriptor': '{broken',
	'data-hssb-server-href': 'https://fallback.example/malformed',
}, 'https://site.example/article/');
assert.strictEqual(malformed.link.value('href'), 'https://fallback.example/malformed');

const history = run({
	'data-hssb-browser-descriptor': JSON.stringify({
		permalink_slot: '%%permalink%%',
		template: 'https://example.test/?url=%%permalink%%',
	}),
	'data-hssb-server-href': 'https://fallback.example/history',
}, 'https://site.example/one');
history.listeners.popstate();
assert.strictEqual(history.link.value('href'), 'https://example.test/?url=https%3A%2F%2Fsite.example%2Fone');

console.log('Browser URL smoke passed.');
