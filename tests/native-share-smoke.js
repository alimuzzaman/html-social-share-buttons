#!/usr/bin/env node
const assert = require('assert');
const fs = require('fs');
const vm = require('vm');

function source(file) {
	return fs.readFileSync('src/js/' + file + '.js', 'utf8')
		.replace(/^import[^\n]+\n/gm, '')
		.replace(/export function/g, 'function')
		.replace(/export\s+\{\};?\s*$/, '');
}
const runtime = ['share-clipboard', 'share-browser-url', 'native-share'].map(source).join('\n');

function element(tag, attributes = {}) {
	const values = { ...attributes };
	const node = {
		tagName: tag.toUpperCase(), parentNode: null, children: [], style: {}, textContent: '',
		get attributes() { return Object.entries(values).map(([name, value]) => ({ name, value })); },
		getAttribute(name) { return values[name] ?? null; },
		setAttribute(name, value) { values[name] = String(value); },
		removeAttribute(name) { delete values[name]; },
		appendChild(child) { child.parentNode = this; this.children.push(child); return child; },
		removeChild(child) { this.children.splice(this.children.indexOf(child), 1); },
		replaceChild(next, previous) { next.parentNode = this; this.children[this.children.indexOf(previous)] = next; previous.parentNode = null; },
		select() {},
		classList: { add() {}, remove() {} },
	};
	return node;
}

function setup(options = {}) {
	const writes = [], shares = [], prompts = [], statuses = [], listeners = {}, timers = [];
	const group = element('div', options.browserUrl ? { 'data-hssb-browser-url': '1' } : {});
	function addControl(hasCopy = false, url = 'https://example.test/loop/?a=1&b=2') {
		return group.appendChild(element(hasCopy ? 'span' : 'a', {
			...(hasCopy ? { hidden: '', style: 'display:none' } : { href: url }),
			'data-hssb-native-share': '1', 'data-hssb-native-url': url,
			'data-hssb-native-title': 'Loop title & café', 'data-hssb-native-has-copy': hasCopy ? '1' : '0',
			'data-hssb-share-label': 'Share…', 'data-hssb-copy-label': 'Copy link',
			'data-hssb-native-failed-label': 'Could not open sharing. Use Copy link.',
			'data-hssb-copied-label': 'Link copied', 'data-hssb-copy-failed-label': 'Could not copy the link',
			'aria-label': 'Copy link',
			...(options.browserUrl ? {
				'data-hssb-browser-descriptor': JSON.stringify({ template: '%%permalink%%', permalink_slot: '%%permalink%%', encoding: 'raw' }),
				'data-hssb-server-href': url,
			} : {}),
		}));
	}
	addControl(options.hasCopy, options.url);
	const document = {
		readyState: 'complete', documentElement: {}, body: element('body'), head: element('head'),
		querySelectorAll() { return group.children.filter((n) => n.getAttribute('data-hssb-native-share') === '1'); },
		createElement(tag) { const node = element(tag); statuses.push(node); return node; },
		addEventListener(name, callback) { listeners[name] = callback; },
		execCommand() { return options.legacyCopy === true; },
	};
	let observer;
	const navigator = {
		clipboard: { writeText: (url) => { writes.push(url); return options.copyError ? Promise.reject(new Error()) : Promise.resolve(); } },
		...(options.unsupported ? {} : { share: (payload) => {
			shares.push(payload);
			if (options.syncError) { throw options.syncError; }
			return options.share ? options.share(payload) : Promise.resolve();
		} }),
		...(options.canShare === undefined ? {} : { canShare: () => options.canShare }),
	};
	const window = {
		navigator, isSecureContext: options.secure !== false,
		location: { href: 'https://example.test/current/?x=1&x=2#part' },
		setTimeout: (callback) => timers.push(callback),
		prompt: (label, url) => prompts.push([label, url]),
		MutationObserver: class { constructor(callback) { observer = callback; } observe() {} },
	};
	vm.runInNewContext(runtime, { document, window, Promise, URL, JSON, encodeURIComponent });
	function click(index = 0, overrides = {}) {
		const event = { target: group.children[index], button: 0, defaultPrevented: false,
			preventDefault() { this.defaultPrevented = true; }, ...overrides };
		listeners.click(event);
		return event;
	}
	async function settle() { await new Promise((resolve) => setImmediate(resolve)); timers.splice(0).forEach((callback) => callback()); }
	return { group, window, shares, writes, prompts, statuses, click, settle, addControl, initialize: () => observer() };
}

(async () => {
	const success = setup();
	assert.equal(success.group.children[0].tagName, 'BUTTON');
	assert.equal(success.group.children[0].getAttribute('type'), 'button');
	assert.equal(success.group.children[0].getAttribute('aria-label'), 'Share…');
	success.click();
	assert.equal(success.shares.length, 1, 'share must run synchronously during activation');
	assert.equal(success.shares[0].url, 'https://example.test/loop/?a=1&b=2');
	assert.equal(success.shares[0].title, 'Loop title & café');
	await success.settle();
	assert.equal(success.writes.length, 0);
	assert.equal(success.group.children[0].getAttribute('aria-busy'), null);
	assert.ok(!success.statuses.some((n) => n.textContent.includes('success')));

	const unsupported = setup({ unsupported: true });
	assert.equal(unsupported.group.children[0].tagName, 'A');
	unsupported.click(); await unsupported.settle();
	assert.equal(unsupported.writes[0], 'https://example.test/loop/?a=1&b=2');
	const duplicate = setup({ unsupported: true, hasCopy: true });
	assert.equal(duplicate.group.children[0].tagName, 'SPAN');
	assert.equal(duplicate.group.children[0].getAttribute('hidden'), '');
	const both = setup({ hasCopy: true });
	assert.equal(both.group.children[0].tagName, 'BUTTON');
	assert.equal(both.group.children[0].getAttribute('hidden'), null);
	const insecure = setup({ secure: false });
	insecure.click(); await insecure.settle();
	assert.equal(insecure.shares.length, 0); assert.equal(insecure.prompts.length, 1);

	const cancel = setup({ share: () => Promise.reject({ name: 'AbortError' }) });
	cancel.click(); await cancel.settle();
	assert.equal(cancel.writes.length, 0);
	assert.equal(cancel.group.children[0].getAttribute('aria-label'), 'Share…');
	for (const options of [
		{ share: () => Promise.reject({ name: 'NotAllowedError' }) },
		{ syncError: new Error('blocked') }, { canShare: false },
	]) {
		const denied = setup(options);
		denied.click(); await denied.settle();
		assert.equal(denied.writes.length, 0, 'error must wait for a separate copy gesture');
		assert.equal(denied.group.children[0].getAttribute('aria-label'), 'Copy link');
		denied.click(); await denied.settle();
		assert.equal(denied.writes.length, 1);
	}
	const deniedBoth = setup({ hasCopy: true, syncError: new Error() });
	deniedBoth.click(); await deniedBoth.settle();
	assert.equal(deniedBoth.group.children[0].getAttribute('aria-label'), 'Share…');
	assert.equal(deniedBoth.group.children.length, 1);

	let finish;
	const pending = setup({ share: () => new Promise((resolve) => { finish = resolve; }) });
	pending.addControl(); pending.initialize();
	pending.click(); pending.click(1); pending.click();
	assert.equal(pending.shares.length, 1, 'guard pending operations across groups');
	finish(); await pending.settle(); pending.click(1);
	assert.equal(pending.shares.length, 2); finish(); await pending.settle();

	const browser = setup({ browserUrl: true });
	browser.window.location.href = 'https://example.test/updated/?q=%26&q=2#new';
	browser.click(); await browser.settle();
	assert.equal(browser.shares[0].url, browser.window.location.href);
	const custom = setup(); custom.window.location.href = 'https://unrelated.test/'; custom.click();
	assert.equal(custom.shares[0].url, 'https://example.test/loop/?a=1&b=2');
	for (const url of ['javascript:alert(1)', 'mailto:a@example.test', 'not a URL']) {
		const invalid = setup({ url }); invalid.click(); await invalid.settle();
		assert.equal(invalid.shares.length, 0); assert.equal(invalid.writes.length, 0);
	}
	const copyFailure = setup({ unsupported: true, copyError: true });
	copyFailure.click(); await copyFailure.settle(); assert.equal(copyFailure.prompts.length, 1);
	const modified = setup({ unsupported: true });
	assert.equal(modified.click(0, { metaKey: true }).defaultPrevented, false);
	console.log('Native share smoke passed: supported, fallback, cancellation, errors, pending, dynamic groups and URL provenance.');
})().catch((error) => { console.error(error); process.exit(1); });
