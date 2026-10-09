#!/usr/bin/env node
const assert = require('assert');
const fs = require('fs');
const vm = require('vm');

const source = fs
	.readFileSync('src/js/copy-link.js', 'utf8')
	.replace(/export\s+\{\};?\s*$/, '');

function createElement(attributes, parentNode) {
	const values = Object.assign({}, attributes);
	const classes = new Set();
	return {
		parentNode: parentNode || null,
		style: {},
		textContent: '',
		classList: {
			add(...names) {
				names.forEach((name) => classes.add(name));
			},
			remove(...names) {
				names.forEach((name) => classes.delete(name));
			},
			contains(name) {
				return classes.has(name);
			},
		},
		getAttribute(name) {
			return Object.prototype.hasOwnProperty.call(values, name) ? values[name] : null;
		},
		setAttribute(name, value) {
			values[name] = String(value);
		},
		appendChild() {},
		removeChild() {},
		select() {},
	};
}

async function run({ clipboard, execCommand, event }) {
	const listeners = {};
	const timers = [];
	const prompts = [];
	const link = createElement({
		'data-hssb-copy-link': '1',
		href: 'https://site.example/post/?a=1&b=2',
		'aria-label': 'Copy link',
		'data-hssb-copied-label': 'Link copied',
		'data-hssb-copy-failed-label': 'Could not copy the link',
	});
	const icon = createElement({}, link);
	const created = [];
	const document = {
		body: createElement({}),
		head: createElement({}),
		createElement() {
			const element = createElement({});
			created.push(element);
			return element;
		},
		execCommand() {
			if (execCommand instanceof Error) {
				throw execCommand;
			}
			return execCommand;
		},
		addEventListener(name, callback) {
			listeners[name] = callback;
		},
	};
	const window = {
		isSecureContext: true,
		navigator: clipboard ? { clipboard } : {},
		setTimeout(callback) {
			timers.push(callback);
		},
		prompt(message, value) {
			prompts.push([message, value]);
		},
	};

	vm.runInNewContext(source, { window, document, Promise });

	let prevented = false;
	listeners.click(Object.assign({
		target: icon,
		button: 0,
		defaultPrevented: false,
		preventDefault() {
			prevented = true;
		},
	}, event || {}));
	await new Promise((resolve) => setImmediate(resolve));
	timers.splice(0).forEach((callback) => callback());
	const liveRegion = created.find((element) => element.getAttribute('role') === 'status');

	return { link, prevented, prompts, liveRegion };
}

(async () => {
	const written = [];
	const success = await run({
		clipboard: {
			writeText(value) {
				written.push(value);
				return Promise.resolve();
			},
		},
	});
	assert.strictEqual(success.prevented, true);
	assert.deepStrictEqual(written, ['https://site.example/post/?a=1&b=2']);
	assert.strictEqual(success.liveRegion.textContent, 'Link copied');
	assert.strictEqual(success.link.getAttribute('data-hssb-feedback'), 'Link copied');
	assert.strictEqual(success.prompts.length, 0);

	const fallback = await run({
		clipboard: { writeText: () => Promise.reject(new Error('denied')) },
		execCommand: true,
	});
	assert.strictEqual(fallback.liveRegion.textContent, 'Link copied');
	assert.strictEqual(fallback.prompts.length, 0);

	const failure = await run({ clipboard: null, execCommand: new Error('unsupported') });
	assert.deepStrictEqual(failure.prompts, [['Copy link', 'https://site.example/post/?a=1&b=2']]);
	assert.strictEqual(failure.liveRegion.textContent, 'Could not copy the link');

	const modified = await run({ clipboard: null, execCommand: true, event: { metaKey: true } });
	assert.strictEqual(modified.prevented, false);

	const middle = await run({ clipboard: null, execCommand: true, event: { button: 1 } });
	assert.strictEqual(middle.prevented, false);

	console.log('Copy link smoke passed.');
})().catch((error) => {
	console.error(error);
	process.exit(1);
});
