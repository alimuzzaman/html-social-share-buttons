#!/usr/bin/env node

'use strict';

const fs = require('fs');
const os = require('os');
const path = require('path');
const { spawnSync } = require('child_process');

const repositoryRoot = path.resolve(__dirname, '..');
const pluginFile = path.join(repositoryRoot, 'html-social-share.php');
const pluginSource = fs.readFileSync(pluginFile, 'utf8');
const versionMatch = pluginSource.match(/^Version:\s*(\S+)\s*$/m);

if (!versionMatch) {
	throw new Error('Could not determine the plugin version.');
}

const pluginSlug = 'html-social-share-buttons';
const target = path.resolve(repositoryRoot, '..', `${pluginSlug}.${versionMatch[1]}.zip`);
const stagingRoot = fs.mkdtempSync(path.join(os.tmpdir(), 'hssb-archive-'));
const stagedPlugin = path.join(stagingRoot, pluginSlug);
const rootExclusions = new Set(['.git', 'node_modules']);

function copyTree(source, destination, relativeDirectory = '') {
	fs.mkdirSync(destination, { recursive: true });

	for (const entry of fs.readdirSync(source, { withFileTypes: true }).sort((left, right) => left.name.localeCompare(right.name))) {
		const relativePath = path.join(relativeDirectory, entry.name);
		if (!relativeDirectory && rootExclusions.has(entry.name)) {
			continue;
		}

		const sourcePath = path.join(source, entry.name);
		const destinationPath = path.join(destination, entry.name);
		if (entry.isSymbolicLink()) {
			throw new Error(`Distribution source contains an included symlink: ${relativePath}`);
		}
		if (entry.isDirectory()) {
			copyTree(sourcePath, destinationPath, relativePath);
		} else if (entry.isFile()) {
			fs.copyFileSync(sourcePath, destinationPath);
		}
	}
}

try {
	copyTree(repositoryRoot, stagedPlugin);

	const result = spawnSync(
		'wp',
		[
			'dist-archive',
			stagedPlugin,
			target,
			'--force',
			`--plugin-dirname=${pluginSlug}`,
		],
		{
			cwd: stagingRoot,
			encoding: 'utf8',
			stdio: 'inherit',
		}
	);

	if (result.error) {
		throw result.error;
	}
	if (result.status !== 0 || !fs.existsSync(target)) {
		throw new Error(`Distribution archive failed with exit code ${result.status}.`);
	}

	process.stdout.write(`Created ${target}\n`);
} finally {
	fs.rmSync(stagingRoot, { recursive: true, force: true });
}
