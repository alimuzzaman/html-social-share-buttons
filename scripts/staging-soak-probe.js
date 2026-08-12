#!/usr/bin/env node

'use strict';

const crypto = require('crypto');

function argument(name) {
	const index = process.argv.indexOf(name);
	return index === -1 ? '' : String(process.argv[index + 1] || '');
}

async function probe(url, marker, timeoutMilliseconds = 20000) {
	const target = new URL(url);
	if (target.protocol !== 'https:' && target.hostname !== 'localhost') {
		throw new Error('Staging probes require HTTPS unless the host is localhost.');
	}

	const response = await fetch(target, {
		headers: { 'user-agent': 'hssb-staging-soak/1.0' },
		signal: AbortSignal.timeout(timeoutMilliseconds),
	});
	const body = await response.text();
	if (body.length > 2 * 1024 * 1024) {
		throw new Error('Staging response exceeded the 2 MiB probe limit.');
	}

	const checks = {
		http_ok: response.ok,
		marker_present: body.includes(marker),
		share_wrapper_present: /class=["'][^"']*\bzmshbt\b/.test(body),
		share_link_present: /<a\b[^>]*\bhref=/.test(body),
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

module.exports = { probe };
