#!/usr/bin/env node

'use strict';

const fs = require('fs');
const path = require('path');
const AdmZip = require('adm-zip');

const repositoryRoot = path.resolve(__dirname, '..');
const pluginSource = fs.readFileSync(path.join(repositoryRoot, 'html-social-share.php'), 'utf8');
const versionMatch = pluginSource.match(/^Version:\s*(\S+)\s*$/m);

if (!versionMatch) {
	throw new Error('Could not determine the plugin version.');
}

const archive = process.argv[2]
	? path.resolve(process.argv[2])
	: path.resolve(repositoryRoot, '..', `html-social-share-buttons.${versionMatch[1]}.zip`);
if (!fs.existsSync(archive)) {
	throw new Error(`Distribution archive does not exist: ${archive}`);
}

const files = new Set(
	new AdmZip(archive).getEntries().map((entry) => entry.entryName).filter(Boolean)
);

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
	'html-social-share-buttons/readme.txt',
	'html-social-share-buttons/block.json',
	'html-social-share-buttons/vendor/autoload.php',
	'html-social-share-buttons/vendor/composer/autoload_psr4.php',
	'html-social-share-buttons/vendor/composer/autoload_static.php',
	'html-social-share-buttons/src/Bootstrap/Plugin.php',
	'html-social-share-buttons/src/Bootstrap/PluginFactory.php',
	'html-social-share-buttons/blocks/social-links/block.json',
	...repositoryFiles('src', (file) => file.endsWith('.php')),
	...repositoryFiles('resources/iconsets', (file) => file.endsWith('.php')),
	// The established PNG packs are the published source for the four
	// historical icon sets. They must remain in the archive even though the
	// canonical SVG-only sets live under assets/iconsets.
	...repositoryFiles('iconset'),
	...repositoryFiles('assets/iconsets'),
	...repositoryFiles('build'),
])];
const forbidden = [
	/^html-social-share-buttons\/Readme\.txt$/,
	/^html-social-share-buttons\/src\/js\//,
	/^html-social-share-buttons\/tests\//,
	/^html-social-share-buttons\/test-results\//,
	/^html-social-share-buttons\/playwright-report\//,
	/^html-social-share-buttons\/docs\//,
	/^html-social-share-buttons\/node_modules\//,
	/^html-social-share-buttons\/composer\.(?:json|lock)$/,
	/^html-social-share-buttons\/package\.json$/,
	/^html-social-share-buttons\/vendor\/(?!autoload\.php$|composer\/)/,
	/^html-social-share-buttons\/\.env/,
	// Do not restore duplicated historical packs below the canonical asset
	// directory. The manifests deliberately resolve them from iconset/ so
	// their historical URLs, filenames, and styling remain intact.
	/^html-social-share-buttons\/assets\/iconsets\/(?:default|flat|long-shadows|prajin)\//,
	/^html-social-share-buttons\/(?:actions|block-integration|elementor-integration|filters|form|function|iconsets|interfaces|metabox|schemas|settings_page|share-templates|shortcode|vc-integration|widget)\.php$/,
	/^html-social-share-buttons\/src\/Compatibility\/Legacy\/(?:Admin|Bootstrap|Global|Hook|IconSet|Integration|Network|Rendering|Runtime)\//,
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
