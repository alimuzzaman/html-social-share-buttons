/**
 * Helpers for driving the WPDeveloper Sandbox from pnpm test scripts.
 */

'use strict';

const { execFileSync, execSync } = require('child_process');
const fs = require('fs');
const path = require('path');

const REPO_ROOT = path.resolve(__dirname, '..', '..');
const ENV_FILE = path.join(REPO_ROOT, '.wp-env-port');

function fail(message) {
	console.error(`\nError: ${message}\n`);
	process.exit(1);
}

function resolveSb() {
	if (process.env.SANDBOX_SB) {
		if (!fs.existsSync(process.env.SANDBOX_SB)) {
			fail(`SANDBOX_SB points at a missing file: ${process.env.SANDBOX_SB}`);
		}
		return process.env.SANDBOX_SB;
	}

	try {
		execSync('command -v sb', {stdio: 'ignore', shell: '/bin/bash'});
		return 'sb';
	} catch (error) {
		fail('Could not find the Sandbox `sb` CLI. Run `./sb global` in the Sandbox repository or set SANDBOX_SB.');
	}
}

function ensureInstance() {
	const output = execFileSync(resolveSb(), ['ensure', '--project-dir', REPO_ROOT, '--json'], {
		encoding: 'utf8',
		stdio: ['ignore', 'pipe', 'inherit'],
	});
	const line = output.trim().split('\n').filter(Boolean).pop();

	try {
		return JSON.parse(line);
	} catch (error) {
		fail(`Could not parse the Sandbox instance record:\n${output}`);
	}
}

function instanceName() {
	if (process.env.SANDBOX_INSTANCE) {
		return process.env.SANDBOX_INSTANCE;
	}

	try {
		const metadata = JSON.parse(fs.readFileSync(ENV_FILE, 'utf8'));
		if (metadata.instance) {
			return metadata.instance;
		}
	} catch (error) {
		// Start has not written the descriptor yet.
	}

	return path.basename(REPO_ROOT);
}

function runSandbox(args) {
	try {
		execFileSync(resolveSb(), args, {stdio: 'inherit'});
	} catch (error) {
		process.exit(error.status || 1);
	}
}

module.exports = {ENV_FILE, REPO_ROOT, ensureInstance, instanceName, runSandbox};
