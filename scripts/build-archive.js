#!/usr/bin/env node

'use strict';

const fs = require('fs');
const path = require('path');
const AdmZip = require('adm-zip');
const createIgnore = require('ignore');

const repositoryRoot = path.resolve(__dirname, '..');
const pluginFile = path.join(repositoryRoot, 'html-social-share.php');
const pluginSource = fs.readFileSync(pluginFile, 'utf8');
const versionMatch = pluginSource.match(/^Version:\s*(\S+)\s*$/m);

if (!versionMatch) {
	throw new Error('Could not determine the plugin version.');
}

const pluginSlug = 'html-social-share-buttons';
const requestedTarget = process.env.HSSB_ARCHIVE_PATH;
const target = requestedTarget
	? path.resolve(requestedTarget)
	: path.resolve(repositoryRoot, '..', `${pluginSlug}.${versionMatch[1]}.zip`);
const rootExclusions = new Set(['.git', 'node_modules']);
const sourceDateEpoch = Number(process.env.SOURCE_DATE_EPOCH || 946684800);

if (!Number.isSafeInteger(sourceDateEpoch) || sourceDateEpoch < 315532800) {
	throw new Error('SOURCE_DATE_EPOCH must be an integer Unix timestamp no earlier than 1980-01-01.');
}

const archiveTimestamp = new Date(sourceDateEpoch * 1000);
const distributionIgnore = createIgnore().add(
	fs.readFileSync(path.join(repositoryRoot, '.distignore'), 'utf8')
);

function collectFiles(source, relativeDirectory = '') {
	const files = [];

	for (const entry of fs.readdirSync(source, { withFileTypes: true }).sort((left, right) => left.name.localeCompare(right.name))) {
		const relativePath = path.join(relativeDirectory, entry.name).split(path.sep).join('/');
		if (!relativeDirectory && rootExclusions.has(entry.name)) {
			continue;
		}
		if (distributionIgnore.ignores(relativePath)) {
			continue;
		}

		const sourcePath = path.join(source, entry.name);
		if (entry.isSymbolicLink()) {
			throw new Error(`Distribution source contains an included symlink: ${relativePath}`);
		}
		if (entry.isDirectory()) {
			files.push(...collectFiles(sourcePath, relativePath));
		} else if (entry.isFile()) {
			files.push({ absolutePath: sourcePath, relativePath });
		}
	}

	return files;
}

const archive = new AdmZip();
for (const file of collectFiles(repositoryRoot)) {
	const entryName = `${pluginSlug}/${file.relativePath}`;
	const entry = archive.addFile(entryName, fs.readFileSync(file.absolutePath), '', 0o644);
	entry.header.time = archiveTimestamp;
}

if (fs.existsSync(target)) {
	throw new Error(`Refusing to overwrite an existing distribution archive: ${target}`);
}
archive.writeZip(target);
if (!fs.existsSync(target)) {
	throw new Error('Distribution archive was not created.');
}

process.stdout.write(`Created ${target}\n`);
