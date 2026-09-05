// Exercises the compiled app via the admin smoke harness; no live endpoint implied.
const assert = require('node:assert/strict');

module.exports = function checkTemplatePreview(App, $, collectNodes, collectText) {
	const requests = [];
	$.ajax = (options) => {
		const callbacks = {};
		const request = {
			options,
			done(fn) { callbacks.done = fn; return this; },
			fail(fn) { callbacks.fail = fn; return this; },
			always(fn) { callbacks.always = fn; return this; },
			abort() { this.aborted = true; },
			resolve(response) { callbacks.done(response); callbacks.always(); },
			reject() { callbacks.fail(); callbacks.always(); },
		};
		requests.push(request);
		return request;
	};
	const app = new App({});
	const respond = (network, url) => ({ success: true, data: {
		network, resolved_url: url, uses_default: false,
		diagnostics: [{ code: 'sample', severity: 'warning', message: '<img src=x onerror=alert(1)>' }],
	} });
	const initialOptions = JSON.stringify(app.state.options);
	app.previewShareTemplate('x');
	assert.equal(requests.length, 1);
	assert.equal(requests[0].options.data.action, 'hssb_preview_share_template');
	assert.equal(requests[0].options.data.network, 'x');
	assert.equal(requests[0].options.data.template, app.previewTemplateValue('x'));
	assert.equal(app.state.templatePreviews.x.status, 'loading');
	app.previewShareTemplate('x');
	assert.equal(requests.length, 1, 'Duplicate pending preview must not send another request');
	requests[0].resolve(respond('x', 'https://example.com/?text=<script>alert(1)</script>'));
	assert.equal(app.state.templatePreviews.x.status, 'result');
	assert.equal(JSON.stringify(app.state.options), initialOptions, 'Preview must not change settings');
	assert.equal(app.state.isDirty, false, 'Preview must not dirty the form');
	const tree = app.renderTemplatePreview('x');
	const nodes = collectNodes(tree);
	assert(nodes.some((node) => node.props.role === 'status' && node.props['aria-live'] === 'polite'));
	assert(!nodes.some((node) => node.type === 'a' || node.type === 'script' || node.type === 'img' || node.props.dangerouslySetInnerHTML));
	assert(collectText(tree).includes('<img src=x onerror=alert(1)>'), 'Diagnostic must remain inert text');
	app.previewShareTemplate('x');
	const stale = requests[1];
	app.update('share_templates.x', 'https://example.com/?text=%%title%%');
	assert(stale.aborted, 'An edit cancels its pending preview');
	assert.equal(app.state.templatePreviews.x.status, 'idle');
	app.previewShareTemplate('x');
	const current = requests[2];
	stale.resolve(respond('x', 'https://stale.example/'));
	assert.equal(app.state.templatePreviews.x.status, 'loading', 'Stale result cannot replace a later request');
	current.resolve(respond('x', 'https://current.example/'));
	assert.equal(app.state.templatePreviews.x.url, 'https://current.example/');
	app.previewShareTemplate('x');
	requests[3].resolve(respond('mail', 'mailto:'));
	assert.equal(app.state.templatePreviews.x.status, 'error', 'Mismatched response must fail safely');
	app.previewShareTemplate('x');
	requests[4].reject();
	assert.equal(app.state.templatePreviews.x.status, 'error');
	app.resetShareTemplate('x');
	assert.equal(app.previewTemplateValue('x'), '');
	assert.equal(app.state.templatePreviews.x.status, 'idle', 'Reset invalidates an old preview');
	app.previewShareTemplate('x');
	assert.equal(requests[5].options.data.template, '', 'Reset previews the canonical default');
	app.disposeTemplatePreviews();
	assert(requests[5].aborted);
	requests[5].resolve(respond('x', 'https://after-unmount.example/'));
	assert.notEqual(app.state.templatePreviews.x.url, 'https://after-unmount.example/');
	console.log('Template preview smoke passed: request, text safety, errors, reset and stale responses.');
};
