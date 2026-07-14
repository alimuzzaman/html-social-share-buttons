#!/usr/bin/env node

'use strict';

const fs = require('fs');
const path = require('path');
const { REPO_ROOT, instanceName, runSandbox } = require('./lib/sandbox');

const DISTIGNORE_FILE = path.join(REPO_ROOT, '.distignore');

function isGlob(pattern) {
	return /[*?]/.test(pattern);
}

function matchesGlob(file, pattern) {
	const expression = pattern
		.replace(/[|\\{}()[\]^$+?.]/g, '\\$&')
		.replace(/\*/g, '[^/]*')
		.replace(/\\\?/g, '[^/]');

	return new RegExp(`^${expression}$`).test(file);
}

function listFiles(directory, ignoredDirectories) {
	const files = [];

	for (const entry of fs.readdirSync(directory, { withFileTypes: true })) {
		const relativePath = path.relative(REPO_ROOT, path.join(directory, entry.name)).split(path.sep).join('/');
		if (entry.isDirectory()) {
			if (!ignoredDirectories.has(relativePath)) {
				files.push(...listFiles(path.join(directory, entry.name), ignoredDirectories));
			}
		} else if (entry.isFile()) {
			files.push(relativePath);
		}
	}

	return files;
}

function loadDistignore() {
	const rules = fs.readFileSync(DISTIGNORE_FILE, 'utf8')
		.split(/\r?\n/)
		.map((line) => line.trim())
		.filter((line) => line && !line.startsWith('#'));
	const directories = new Set(['.git', 'node_modules', 'vendor']);
	const fileRules = [];

	for (const rule of rules) {
		const ignored = !rule.startsWith('!');
		const pattern = (ignored ? rule : rule.slice(1)).replace(/^\.\//, '').replace(/\/$/, '');
		if (!pattern || pattern.startsWith('../')) {
			continue;
		}

		if (rule.endsWith('/')) {
			if (ignored && !isGlob(pattern)) {
				directories.add(pattern);
			}
			continue;
		}

		fileRules.push({ ignored, pattern });
	}

	const files = new Set();
	for (const { ignored, pattern } of fileRules) {
		const matchedFiles = isGlob(pattern)
			? listFiles(REPO_ROOT, directories).filter((file) => matchesGlob(file, pattern))
			: [pattern];

		for (const file of matchedFiles) {
			if (ignored) {
				files.add(file);
			} else {
				files.delete(file);
			}
		}
	}

	return { directories: [...directories], files: [...files] };
}

const { directories, files } = loadDistignore();

runSandbox([
	'--instance',
	instanceName(),
	'wp',
	'plugin',
	'check',
	'html-social-share-buttons',
	'--format=json',
	`--exclude-directories=${directories.join(',')}`,
	`--exclude-files=${files.join(',')}`,
]);
