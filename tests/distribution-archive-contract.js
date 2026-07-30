#!/usr/bin/env node

'use strict';

const fs = require('fs');
const path = require('path');
const { spawnSync } = require('child_process');

const repositoryRoot = path.resolve(__dirname, '..');
const pluginSource = fs.readFileSync(path.join(repositoryRoot, 'html-social-share.php'), 'utf8');
const versionMatch = pluginSource.match(/^Version:\s*(\S+)\s*$/m);

if (!versionMatch) {
	throw new Error('Could not determine the plugin version.');
}

const archive = process.argv[2]
	? path.resolve(process.argv[2])
	: path.resolve(repositoryRoot, '..', `html-social-share-buttons.${versionMatch[1]}.zip`);
const listing = spawnSync('unzip', ['-Z1', archive], { encoding: 'utf8' });

if (listing.error) {
	throw listing.error;
}
if (listing.status !== 0) {
	throw new Error(listing.stderr || 'Could not inspect the distribution archive.');
}

const files = new Set(listing.stdout.trim().split(/\r?\n/).filter(Boolean));

function repositoryFiles(relativeDirectory, predicate = () => true) {
	const absoluteDirectory = path.join(repositoryRoot, relativeDirectory);
	const collected = [];

	for (const entry of fs.readdirSync(absoluteDirectory, { withFileTypes: true })) {
		const relativePath = path.join(relativeDirectory, entry.name);
		if (entry.isDirectory()) {
			collected.push(...repositoryFiles(relativePath, predicate));
		} else if (entry.isFile() && predicate(relativePath)) {
			collected.push(`html-social-share-buttons/${relativePath.split(path.sep).join('/')}`);
		}
	}

	return collected;
}

const required = [...new Set([
	'html-social-share-buttons/html-social-share.php',
	'html-social-share-buttons/vendor/autoload.php',
	'html-social-share-buttons/vendor/composer/autoload_psr4.php',
	'html-social-share-buttons/src/Bootstrap/Plugin.php',
	'html-social-share-buttons/src/Bootstrap/PluginFactory.php',
	...repositoryFiles('src', (file) => file.endsWith('.php')),
	...repositoryFiles('resources/iconsets', (file) => file.endsWith('.php')),
	...repositoryFiles('assets/iconsets'),
	...repositoryFiles('build'),
	...[
		'actions.php',
		'block-integration.php',
		'elementor-integration.php',
		'filters.php',
		'form.php',
		'function.php',
		'iconsets.php',
		'interfaces.php',
		'metabox.php',
		'schemas.php',
		'settings_page.php',
		'share-templates.php',
		'shortcode.php',
		'vc-integration.php',
		'widget.php',
	].map((file) => `html-social-share-buttons/${file}`),
])];
const forbidden = [
	/^html-social-share-buttons\/src\/js\//,
	/^html-social-share-buttons\/tests\//,
	/^html-social-share-buttons\/test-results\//,
	/^html-social-share-buttons\/playwright-report\//,
	/^html-social-share-buttons\/docs\//,
	/^html-social-share-buttons\/node_modules\//,
	/^html-social-share-buttons\/composer\.(?:json|lock)$/,
	/^html-social-share-buttons\/package\.json$/,
	/^html-social-share-buttons\/\.env/,
];
const failures = [];

for (const requiredFile of required) {
	if (!files.has(requiredFile)) {
		failures.push(`missing ${requiredFile}`);
	}
}

for (const file of files) {
	if (forbidden.some((pattern) => pattern.test(file))) {
		failures.push(`development-only file ${file}`);
	}
}

if (failures.length) {
	throw new Error(`Distribution archive contract failed:\n- ${failures.join('\n- ')}`);
}

process.stdout.write(`Distribution archive contract passed: ${files.size} files.\n`);
