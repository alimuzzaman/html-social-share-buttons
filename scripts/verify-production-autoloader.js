#!/usr/bin/env node

'use strict';

const fs = require('fs');
const path = require('path');

const root = path.resolve(__dirname, '..');
const composerDirectory = path.join(root, 'vendor', 'composer');
const requiredFiles = [
	path.join(root, 'vendor', 'autoload.php'),
	path.join(composerDirectory, 'autoload_real.php'),
	path.join(composerDirectory, 'autoload_static.php'),
	path.join(composerDirectory, 'installed.json'),
];

for (const file of requiredFiles) {
	if (!fs.existsSync(file)) {
		throw new Error(
			`Production Composer autoloader is incomplete (${path.relative(root, file)} is missing). Run Composer with --no-dev --classmap-authoritative before packaging.`
		);
	}
}

const autoloadRuntime = fs.readFileSync(
	path.join(composerDirectory, 'autoload_real.php'),
	'utf8'
);
if (!autoloadRuntime.includes('setClassMapAuthoritative(true)')) {
	throw new Error(
		'Composer autoloading is not classmap-authoritative. Rebuild it with --classmap-authoritative.'
	);
}

const installed = JSON.parse(
	fs.readFileSync(path.join(composerDirectory, 'installed.json'), 'utf8')
);
if (installed.dev !== false || (installed['dev-package-names'] || []).length) {
	throw new Error(
		'Composer development dependencies are installed. Run Composer with --no-dev before packaging.'
	);
}

const autoloadStatic = fs.readFileSync(
	path.join(composerDirectory, 'autoload_static.php'),
	'utf8'
);
for (const className of [
	'Alimuzzaman\\\\HtmlSocialShareButtons\\\\Bootstrap\\\\Plugin',
	'Alimuzzaman\\\\HtmlSocialShareButtons\\\\Presentation\\\\Rendering\\\\RenderFacade',
]) {
	if (!autoloadStatic.includes(className)) {
		throw new Error(
			`Optimized Composer class map does not contain ${className.replaceAll('\\\\', '\\')}.`
		);
	}
}

process.stdout.write('Production Composer autoloader contract passed.\n');
