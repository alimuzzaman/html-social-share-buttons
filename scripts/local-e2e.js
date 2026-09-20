#!/usr/bin/env node

'use strict';

const {execFileSync, spawnSync} = require('child_process');
const {
	REPO_ROOT,
	ensureInstance,
	instanceName,
	resolveSb,
} = require('./lib/sandbox');

const playwrightArgs = process.argv.slice(2);
const record = ensureInstance();
const port = Number(record.wordpress_port);

if (!Number.isInteger(port) || port < 1) {
	console.error('Sandbox did not report a usable local WordPress port for E2E tests.');
	process.exit(1);
}

const instance = record.instance || instanceName();
const baseUrl = `http://localhost:${port}`;

function runWp(args) {
	try {
		execFileSync(resolveSb(), ['--instance', instance, 'wp', ...args], {
			cwd: REPO_ROOT,
			stdio: 'inherit',
		});
	} catch (error) {
		process.exit(error.status || 1);
	}
}

// The managed proxy can redirect or reject requests while the local container
// is healthy. Keep browser checks on the container's HTTP endpoint and align
// WordPress's canonical URLs with that endpoint for the duration of the run.
runWp(['option', 'update', 'home', baseUrl]);
runWp(['option', 'update', 'siteurl', baseUrl]);
runWp(['user', 'update', process.env.WP_ADMIN_USER || 'admin', '--user_pass=' + (process.env.WP_ADMIN_PASSWORD || 'admin'), '--skip-email']);

const env = {
	...process.env,
	SANDBOX_E2E_BASE_URL: baseUrl,
	WP_ADMIN_USER: process.env.WP_ADMIN_USER || 'admin',
	WP_ADMIN_PASSWORD: process.env.WP_ADMIN_PASSWORD || 'admin',
};

console.log(`Running Playwright against local Sandbox endpoint ${baseUrl}`);

const result = spawnSync(
	'pnpm',
	['exec', 'playwright', 'test', '--config=tests/playwright.config.js', ...playwrightArgs],
	{
		cwd: REPO_ROOT,
		env,
		stdio: 'inherit',
	}
);

if (result.error) {
	console.error(`Could not start Playwright: ${result.error.message}`);
	process.exit(1);
}

process.exit(result.status === null ? 1 : result.status);
