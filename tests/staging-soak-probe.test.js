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
	let responseBody = '<h1>HSSB staging soak fixture</h1><div class="zmshbt"><a href="https://example.com/share">Share</a></div>';
	const server = http.createServer((request, response) => {
		response.writeHead(200, { 'content-type': 'text/html; charset=utf-8' });
		response.end(responseBody);
	});

	await listen(server);
	const address = server.address();
	try {
		const passed = await probe(`http://localhost:${address.port}/fixture`, 'HSSB staging soak fixture');
		assert.strictEqual(passed.ok, true);
		assert.strictEqual(passed.checks.no_raw_placeholder, true);

		responseBody = responseBody.replace('https://example.com/share', '%%permalink%%');
		const failed = await probe(`http://localhost:${address.port}/fixture`, 'HSSB staging soak fixture');
		assert.strictEqual(failed.ok, false);
		assert.strictEqual(failed.checks.no_raw_placeholder, false);
	} finally {
		await close(server);
	}

	process.stdout.write('Staging soak probe contract passed.\n');
}

main().catch((error) => {
	process.stderr.write(`${error.stack || error.message}\n`);
	process.exitCode = 1;
});
