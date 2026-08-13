#!/usr/bin/env node

'use strict';

const assert = require('assert');
const http = require('http');
const { probe } = require('../scripts/staging-soak-probe');

async function listen(server) {
	return new Promise((resolve) => server.listen(0, '127.0.0.1', resolve));
}

async function close(server) {
	return new Promise((resolve, reject) => server.close((error) => error ? reject(error) : resolve()));
}

async function main() {
	const validBody = '<h1>HSSB staging soak fixture</h1><div class="zmshbt"><a href="https://example.com/share">Share</a></div>';
	let responseBody = validBody;
	let status = 200;
	const server = http.createServer((request, response) => {
		if (request.url === '/redirect') {
			response.writeHead(302, { location: '/fixture' });
			response.end();
			return;
		}
		if (request.url === '/chunked-large') {
			response.writeHead(200, { 'content-type': 'text/html; charset=utf-8' });
			response.write('x'.repeat(1024 * 1024 + 1));
			response.end('x'.repeat(1024 * 1024));
			return;
		}
		response.writeHead(status, { 'content-type': 'text/html; charset=utf-8' });
		response.end(responseBody);
	});

	await listen(server);
	const address = server.address();
	try {
		const passed = await probe(`http://localhost:${address.port}/fixture`, 'HSSB staging soak fixture');
		assert.strictEqual(passed.ok, true);
		assert.strictEqual(passed.checks.no_raw_placeholder, true);

		responseBody = '<h1>HSSB staging soak fixture</h1><div class="zmshbt"><div class="inner"><a href="https://example.com/share">Share</a></div></div>';
		const nested = await probe(`http://localhost:${address.port}/fixture`, 'HSSB staging soak fixture');
		assert.strictEqual(nested.ok, true);

		responseBody = '<h1>HSSB staging soak fixture</h1><div class=zmshbt><a href=https://example.com/share>Share</a></div>';
		const unquoted = await probe(`http://localhost:${address.port}/fixture`, 'HSSB staging soak fixture');
		assert.strictEqual(unquoted.ok, true);

		responseBody = '<h1>HSSB staging soak fixture</h1><div class="not-zmshbt" class="zmshbt"><a href="https://example.com/share">Share</a></div>';
		const duplicateClass = await probe(`http://localhost:${address.port}/fixture`, 'HSSB staging soak fixture');
		assert.strictEqual(duplicateClass.ok, false);

		responseBody = '<h1>HSSB staging soak fixture</h1><div class=zmshbt><a href>Share</a></div>';
		const emptyHref = await probe(`http://localhost:${address.port}/fixture`, 'HSSB staging soak fixture');
		assert.strictEqual(emptyHref.ok, false);
		assert.strictEqual(emptyHref.checks.share_link_present, false);

		responseBody = validBody.replace('https://example.com/share', '%%permalink%%');
		const failed = await probe(`http://localhost:${address.port}/fixture`, 'HSSB staging soak fixture');
		assert.strictEqual(failed.ok, false);
		assert.strictEqual(failed.checks.no_raw_placeholder, false);

		responseBody = validBody.replace('https://example.com/share', '%25%25permalink%25%25');
		const encoded = await probe(`http://localhost:${address.port}/fixture`, 'HSSB staging soak fixture');
		assert.strictEqual(encoded.ok, false);
		assert.strictEqual(encoded.checks.no_encoded_placeholder, false);

		responseBody = '<div class="zmshbt"></div><a href="https://example.com/unrelated">Navigation</a>';
		const unrelated = await probe(`http://localhost:${address.port}/fixture`, 'HSSB staging soak fixture');
		assert.strictEqual(unrelated.ok, false);
		assert.strictEqual(unrelated.checks.share_link_present, false);

		responseBody = '<!-- <div class="zmshbt"><a href="https://example.com/share">Share</a></div> -->';
		const commented = await probe(`http://localhost:${address.port}/fixture`, 'HSSB staging soak fixture');
		assert.strictEqual(commented.ok, false);
		assert.strictEqual(commented.checks.share_link_present, false);

		responseBody = '<script>const fixture = `<div class="zmshbt"><a href="https://example.com/share">Share</a></div>`;</script>';
		const scripted = await probe(`http://localhost:${address.port}/fixture`, 'HSSB staging soak fixture');
		assert.strictEqual(scripted.ok, false);
		assert.strictEqual(scripted.checks.share_link_present, false);

		for (const tag of ['textarea', 'title', 'noscript', 'xmp', 'iframe', 'noembed', 'noframes']) {
			responseBody = `<${tag}>HSSB staging soak fixture<div class="zmshbt"><a href="https://example.com/share">Share</a></div></${tag}>`;
			const inert = await probe(`http://localhost:${address.port}/fixture`, 'HSSB staging soak fixture');
			assert.strictEqual(inert.ok, false);
			assert.strictEqual(inert.checks.marker_present, false);
			assert.strictEqual(inert.checks.share_link_present, false);
		}

		for (const tag of ['script', 'style', 'template', 'textarea', 'xmp', 'iframe']) {
			responseBody = `<${tag}>HSSB staging soak fixture<div class="zmshbt"><a href="https://example.com/share">Share</a></div>`;
			const unclosed = await probe(`http://localhost:${address.port}/fixture`, 'HSSB staging soak fixture');
			assert.strictEqual(unclosed.ok, false);
		}

		responseBody = '<plaintext>HSSB staging soak fixture<div class="zmshbt"><a href="https://example.com/share">Share</a></div>';
		const plaintext = await probe(`http://localhost:${address.port}/fixture`, 'HSSB staging soak fixture');
		assert.strictEqual(plaintext.ok, false);
		assert.strictEqual(plaintext.checks.marker_present, false);
		assert.strictEqual(plaintext.checks.share_link_present, false);

		responseBody = validBody.replace('HSSB staging soak fixture', 'Different page');
		const missingMarker = await probe(`http://localhost:${address.port}/fixture`, 'HSSB staging soak fixture');
		assert.strictEqual(missingMarker.ok, false);
		assert.strictEqual(missingMarker.checks.marker_present, false);

		responseBody = '<!-- HSSB staging soak fixture --><div class="zmshbt"><a href="https://example.com/share">Share</a></div>';
		const commentedMarker = await probe(`http://localhost:${address.port}/fixture`, 'HSSB staging soak fixture');
		assert.strictEqual(commentedMarker.ok, false);
		assert.strictEqual(commentedMarker.checks.marker_present, false);

		responseBody = validBody;
		status = 500;
		const serverError = await probe(`http://localhost:${address.port}/fixture`, 'HSSB staging soak fixture');
		assert.strictEqual(serverError.ok, false);
		assert.strictEqual(serverError.checks.http_ok, false);

		status = 200;
		await assert.rejects(() => probe(`http://localhost:${address.port}/redirect`, 'HSSB staging soak fixture'));

		responseBody = 'x'.repeat(2 * 1024 * 1024 + 1);
		await assert.rejects(
			() => probe(`http://localhost:${address.port}/fixture`, 'HSSB staging soak fixture'),
			/2 MiB probe limit/
		);
		await assert.rejects(
			() => probe(`http://localhost:${address.port}/chunked-large`, 'HSSB staging soak fixture'),
			/2 MiB probe limit/
		);
	} finally {
		await close(server);
	}

	process.stdout.write('Staging soak probe contract passed.\n');
}

main().catch((error) => {
	process.stderr.write(`${error.stack || error.message}\n`);
	process.exitCode = 1;
});
