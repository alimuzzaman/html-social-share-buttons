#!/usr/bin/env node
const assert = require('assert');
const fs = require('fs');
const vm = require('vm');

const shared = fs.readFileSync('src/js/share-clipboard.js', 'utf8').replace(/export function/g, 'function');
const source = shared + '\n' + fs
	.readFileSync('src/js/copy-link.js', 'utf8')
	.replace(/^import[^\n]+\n/gm, '').replace(/export\s+\{\};?\s*$/, '');

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

	// Shared style and live-region reuse + timer cancellation on rapid clicks.
	await (async function verifySharedDomReuseAndTimerCancellation() {
		const listeners = {};
		const headAppended = [];
		const bodyAppended = [];
		const timers = new Map();
		const clearedIds = [];
		let nextTimerId = 1;
		const existingStyle = createElement({ id: 'hssb-copy-status-style' });
		const existingRegion = createElement({ id: 'hssb-copy-live-region', role: 'status' });
		const link = createElement({
			'data-hssb-copy-link': '1',
			href: 'https://site.example/rapid/',
			'aria-label': 'Copy link',
			'data-hssb-copied-label': 'Link copied',
			'data-hssb-copy-failed-label': 'Could not copy the link',
		});
		const head = Object.assign(createElement({}), {
			appendChild(node) { headAppended.push(node); },
		});
		const body = Object.assign(createElement({}), {
			appendChild(node) { bodyAppended.push(node); },
		});
		const document = {
			body,
			head,
			getElementById(id) {
				if (id === 'hssb-copy-status-style') { return existingStyle; }
				if (id === 'hssb-copy-live-region') { return existingRegion; }
				return null;
			},
			createElement() { return createElement({}); },
			execCommand() { return true; },
			addEventListener(name, callback) { listeners[name] = callback; },
		};
		const window = {
			isSecureContext: true,
			navigator: { clipboard: { writeText: () => Promise.resolve() } },
			setTimeout(callback) {
				const id = nextTimerId++;
				timers.set(id, callback);
				return id;
			},
			clearTimeout(id) {
				clearedIds.push(id);
				timers.delete(id);
			},
		};
		vm.runInNewContext(source, { window, document, Promise });

		listeners.click({ target: link, button: 0, defaultPrevented: false, preventDefault() {} });
		await new Promise((resolve) => setImmediate(resolve));
		listeners.click({ target: link, button: 0, defaultPrevented: false, preventDefault() {} });
		await new Promise((resolve) => setImmediate(resolve));

		assert.strictEqual(headAppended.length, 0, 'existing style ID must be reused');
		assert.strictEqual(bodyAppended.length, 0, 'existing live-region ID must be reused');
		assert.ok(clearedIds.length >= 2, 'rapid clicks must clear previous announce and feedback timers');

		for (const callback of [...timers.values()]) {
			callback();
		}
		assert.strictEqual(existingRegion.textContent, 'Link copied');
		assert.strictEqual(link.classList.contains('is-copied'), false);
	}());

	console.log('Copy link smoke passed.');
})().catch((error) => {
	console.error(error);
	process.exit(1);
});
