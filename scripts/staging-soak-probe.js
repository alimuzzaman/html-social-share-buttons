#!/usr/bin/env node

'use strict';

const crypto = require('crypto');
const { parse } = require('parse5');

function argument(name) {
	const index = process.argv.indexOf(name);
	return index === -1 ? '' : String(process.argv[index + 1] || '');
}

function renderedChecks(markup, marker) {
	const inert = new Set(['script', 'style', 'template', 'textarea', 'title', 'noscript', 'xmp', 'iframe', 'noembed', 'noframes', 'plaintext']);
	let markerPresent = false;
	let wrapperPresent = false;
	let linkPresent = false;
	function visit(node, insideWrapper = false, insideInert = false) {
		const tag = String(node.tagName || '').toLowerCase();
		const nodeInert = insideInert || inert.has(tag);
		const attributes = new Map((node.attrs || []).map((attribute) => [attribute.name, attribute.value]));
		const classes = String(attributes.get('class') || '').split(/\s+/);
		const nodeWrapper = !nodeInert && tag === 'div' && classes.includes('zmshbt');
		const withinWrapper = insideWrapper || nodeWrapper;
		if (nodeWrapper) wrapperPresent = true;
		if (!nodeInert && withinWrapper && tag === 'a' && String(attributes.get('href') || '').trim() !== '') linkPresent = true;
		if (!nodeInert && node.nodeName === '#text' && String(node.value || '').includes(marker)) markerPresent = true;
		for (const child of node.childNodes || []) visit(child, withinWrapper, nodeInert);
		if (node.content) visit(node.content, withinWrapper, nodeInert);
	}
	visit(parse(markup));
	return { markerPresent, wrapperPresent, linkPresent };
}

async function probe(url, marker, timeoutMilliseconds = 20000) {
	const target = new URL(url);
	if (target.protocol !== 'https:' && target.hostname !== 'localhost') {
		throw new Error('Staging probes require HTTPS unless the host is localhost.');
	}

	const response = await fetch(target, {
		headers: { 'user-agent': 'hssb-staging-soak/1.0' },
		redirect: 'error',
		signal: AbortSignal.timeout(timeoutMilliseconds),
	});
	const limit = 2 * 1024 * 1024;
	const declaredLength = Number(response.headers.get('content-length') || 0);
	if (declaredLength > limit) {
		await response.body.cancel();
		throw new Error('Staging response exceeded the 2 MiB probe limit.');
	}
	const chunks = [];
	let received = 0;
	for await (const chunk of response.body) {
		received += chunk.byteLength;
		if (received > limit) {
			throw new Error('Staging response exceeded the 2 MiB probe limit.');
		}
		chunks.push(Buffer.from(chunk));
	}
	const body = Buffer.concat(chunks).toString('utf8');
	const rendered = renderedChecks(body, marker);

	const checks = {
		http_ok: response.ok,
		marker_present: rendered.markerPresent,
		share_wrapper_present: rendered.wrapperPresent,
		share_link_present: rendered.linkPresent,
		no_raw_placeholder: !body.includes('%%permalink%%'),
		no_encoded_placeholder: !body.includes('%25%25permalink%25%25'),
	};
	const ok = Object.values(checks).every(Boolean);

	return {
		ok,
		observed_at: new Date().toISOString(),
		url: target.toString(),
		status: response.status,
		checks,
		body_sha256: crypto.createHash('sha256').update(body).digest('hex'),
	};
}

async function main() {
	const url = argument('--url') || process.env.HSSB_STAGING_URL || '';
	const marker = argument('--marker') || process.env.HSSB_STAGING_MARKER || 'HSSB staging soak fixture';
	if (!url) {
		throw new Error('Pass --url or set HSSB_STAGING_URL.');
	}

	const result = await probe(url, marker);
	process.stdout.write(`${JSON.stringify(result)}\n`);
	if (!result.ok) {
		process.exitCode = 1;
	}
}

if (require.main === module) {
	main().catch((error) => {
		process.stderr.write(`${error.message}\n`);
		process.exitCode = 1;
	});
}

module.exports = { probe, renderedChecks };
