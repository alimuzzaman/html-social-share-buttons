#!/usr/bin/env node

'use strict';

const fs = require('fs');
const {ENV_FILE, REPO_ROOT, ensureInstance, instanceName, runSandbox, runSandboxTests} = require('./lib/sandbox');

const command = process.argv[2];
const rest = process.argv.slice(3);

switch (command) {
	case 'start':
		start();
		break;
	case 'status':
		runSandbox(['--instance', instanceName(), 'status']);
		break;
	case 'destroy':
		runSandbox(['--instance', instanceName(), 'clean', '--yes']);
		try {
			fs.unlinkSync(ENV_FILE);
		} catch (error) {
			// The descriptor is already absent.
		}
		console.log('Sandbox instance cleaned and .wp-env-port removed.');
		break;
	case 'run':
		runSandbox(['--instance', instanceName(), 'wp', ...rest]);
		break;
	case 'test':
		runSandboxTests(['test', '--project-dir', REPO_ROOT, '--label', process.env.SANDBOX_LABEL || 'default', ...rest]);
		break;
	case 'e2e':
		runSandbox(['e2e', '--project-dir', REPO_ROOT, '--workers', '1', ...rest]);
		break;
	default:
		console.error('Usage: sandbox-env.js <start|status|destroy|run [wp-cli args]|test [phpunit args]|e2e [playwright args]>');
		process.exit(1);
}

function start() {
	console.log('Booting Sandbox instance...');
	const record = ensureInstance();
	if (!record.url || !record.login_url) {
		console.error('Sandbox did not report both a site URL and an auto-login URL.');
		process.exit(1);
	}

	fs.writeFileSync(ENV_FILE, `${JSON.stringify({
		baseUrl: record.url,
		loginUrl: record.login_url || '',
		runtime: 'sandbox',
		instance: record.instance,
	}, null, 2)}\n`);

	console.log('Sandbox ready');
	console.log(`  instance: ${record.instance}`);
	console.log(`  url:      ${record.url}`);
	console.log(`  login:    ${record.login_url}`);
	console.log(`  written:  ${ENV_FILE}`);
}
