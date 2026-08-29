#!/usr/bin/env node

'use strict';

/**
 * Rebuild the historical X assets without changing their public names.
 *
 * The four original PNG packs pre-date the X rebrand and still contain a
 * Twitter bird.  Their filenames and directories are part of the public
 * compatibility surface, so this script keeps those paths and only replaces
 * the artwork.  The glyph is read from the pinned MIT-licensed Bootstrap
 * Icons source already vendored by this repository; no network fetch is
 * performed.
 *
 * Requirements: Node.js, rsvg-convert, and PHP with GD (for preview patches).
 * Run `node scripts/generate-legacy-x-assets.js --check` to verify all
 * generated PNGs, or omit --check to rebuild them.
 */

const crypto = require('crypto');
const fs = require('fs');
const os = require('os');
const path = require('path');
const childProcess = require('child_process');

const repositoryRoot = path.resolve(__dirname, '..');
const sourcePath = path.join(
	repositoryRoot,
	'scripts',
	'iconsets',
	'upstream',
	'bootstrap-icons-v1.13.1',
	'twitter-x.svg'
);
const expectedSourceHashes = new Set([
	'17d383a17ee6990fb53ac005346c09bed57a3c2c3c537a2c0dd6db367ae69d83',
	'173e37e584ccb49cb87242a2e5444201da2d779cee1b1464732893302975950d',
]);
const checkOnly = process.argv.includes('--check');

function sourceGlyph() {
	const source = fs.readFileSync(sourcePath, 'utf8');
	const hashes = [
		crypto.createHash('sha256').update(source).digest('hex'),
		crypto.createHash('sha256').update(source.replace(/\n$/, '')).digest('hex'),
	];
	if (!hashes.some((hash) => expectedSourceHashes.has(hash))) {
		throw new Error('Pinned Bootstrap Icons X source checksum mismatch.');
	}
	const match = source.match(/<svg\b[^>]*>([\s\S]*?)<\/svg>/i);
	if (!match) {
		throw new Error('Pinned Bootstrap Icons X source is malformed.');
	}
	return match[1].replace(/<!--[\s\S]*?-->/g, '').replace(/\s+/g, ' ').trim();
}

function svgDocument(width, height, body) {
	return [
		`<svg xmlns="http://www.w3.org/2000/svg" width="${width}" height="${height}" viewBox="0 0 ${width} ${height}">`,
		'<!-- Bootstrap Icons v1.13.1 twitter-x.svg; https://github.com/twbs/icons; MIT licensed. -->',
		body,
		'</svg>',
		'',
	].join('\n');
}

function glyph(glyphMarkup, width, height, scale, color, x, y, extra = '') {
	const glyphSize = 16 * scale;
	const left = typeof x === 'number' ? x : (width - glyphSize) / 2;
	const top = typeof y === 'number' ? y : (height - glyphSize) / 2;
	return `<g transform="translate(${left} ${top}) scale(${scale})" fill="${color}"${extra}>${glyphMarkup}</g>`;
}

function roundedTile(width, height, color, radius, glyphMarkup, options = {}) {
	const border = options.border || '';
	const shadow = options.shadow || '';
	const scale = options.scale || Math.min(width, height) / 24;
	const x = typeof options.glyphX === 'number' ? options.glyphX : undefined;
	const y = typeof options.glyphY === 'number' ? options.glyphY : undefined;
	return svgDocument(
		width,
		height,
		`<rect x="0" y="0" width="${width}" height="${height}" rx="${radius}" fill="${color}"/>${shadow}${border}${glyph(glyphMarkup, width, height, scale, '#fff', x, y)}`
	);
}

function circleTile(size, color, glyphMarkup, options = {}) {
	const scale = options.scale || size / 24;
	const cx = size / 2;
	const shadow = options.shadow || '';
	return svgDocument(
		size,
		size,
		`<defs><clipPath id="tile"><circle cx="${cx}" cy="${cx}" r="${cx}"/></clipPath></defs><circle cx="${cx}" cy="${cx}" r="${cx}" fill="${color}"/>${shadow}${glyph(glyphMarkup, size, size, scale, '#fff')}`
	);
}

function defaultTile(glyphMarkup) {
	return svgDocument(
		128,
		128,
		'<rect width="128" height="128" fill="#28abdb"/>' +
		'<rect x="5" y="5" width="118" height="118" fill="none" stroke="#fff" stroke-width="2" stroke-dasharray="6 4"/>' +
		glyph(glyphMarkup, 128, 128, 4, '#fff')
	);
}

function flatSquare(glyphMarkup) {
	return roundedTile(129, 129, '#29c5f6', 0, glyphMarkup, { scale: 4.15 });
}

function flatCircle(glyphMarkup) {
	return circleTile(129, '#00acf0', glyphMarkup, { scale: 4.15 });
}

function longShadowBackground(width, height, shape) {
	const color = '#00acf0';
	const shadow = '#0067b7';
	const clip = shape === 'circle'
		? `<clipPath id="shadow-clip"><circle cx="${width / 2}" cy="${height / 2}" r="${Math.min(width, height) / 2}"/></clipPath>`
		: `<clipPath id="shadow-clip"><rect width="${width}" height="${height}" rx="16"/></clipPath>`;
	const base = shape === 'circle'
		? `<circle cx="${width / 2}" cy="${height / 2}" r="${Math.min(width, height) / 2}" fill="${color}"/>`
		: `<rect width="${width}" height="${height}" rx="16" fill="${color}"/>`;
	const diagonal = `<path d="M0 ${height * 0.62}L${width * 0.62} 0H${width}V${height * 0.38}L${width * 0.38} ${height}H0Z" fill="${shadow}" clip-path="url(#shadow-clip)"/>`;
	return `<defs>${clip}</defs>${base}${diagonal}`;
}

function longShadowTile(width, height, shape, glyphMarkup) {
	const scale = Math.min(width, height) / 25;
	const background = longShadowBackground(width, height, shape);
	const source = svgDocument(width, height, background + glyph(glyphMarkup, width, height, scale, '#fff'));
	return source;
}

function prajinSquare(glyphMarkup) {
	const width = 106;
	const height = 83;
	const shadow = glyph(glyphMarkup, width, height, 3.3, '#38577f', 48, 31, ' opacity=".28"');
	return svgDocument(
		width,
		height,
		`<rect x="0" y="0" width="${width}" height="${height}" rx="2" fill="#1bb2e9"/>${shadow}${glyph(glyphMarkup, width, height, 3.3, '#fff', 45, 28)}`
	);
}

function prajinCircle(glyphMarkup) {
	const size = 128;
	const shadow = glyph(glyphMarkup, size, size, 4, '#315984', 40, 40, ' opacity=".3"');
	return svgDocument(
		size,
		size,
		`<circle cx="64" cy="64" r="64" fill="#1cb7eb"/>${shadow}${glyph(glyphMarkup, size, size, 4, '#fff', 36, 36)}`
	);
}

function prajinSquareMail() {
	const width = 106;
	const height = 83;
	const envelope = [
		'<rect x="20" y="20" width="66" height="43" rx="1" fill="#fff"/>',
		'<path d="M20 21l33 27 33-27" fill="none" stroke="#e6d3b5" stroke-width="2"/>',
		'<path d="M20 62l24-21m38 21L58 41" fill="none" stroke="#e6d3b5" stroke-width="2"/>',
	].join('');
	return svgDocument(
		width,
		height,
		`<rect x="0" y="0" width="${width}" height="${height}" rx="2" fill="#3498db"/><g opacity=".2" transform="translate(3 4)">${envelope}</g>${envelope}`
	);
}

function previewPatch(width, height, style, glyphMarkup) {
	if (style === 'default') {
		return svgDocument(
			width,
			height,
			'<rect width="116" height="104" fill="#28abdb"/>' +
			'<rect x="5" y="5" width="106" height="94" fill="none" stroke="#fff" stroke-width="2" stroke-dasharray="6 4"/>' +
			glyph(glyphMarkup, 116, 104, 3.25, '#fff')
		);
	}
	if (style === 'flat') {
		return svgDocument(width, height, `<rect width="${width}" height="${height}" fill="#0481d9"/>${glyph(glyphMarkup, width, height, 3.3, '#fff')}`);
	}
	if (style === 'long-shadow') {
		return svgDocument(width, height, longShadowBackground(width, height, 'square') + glyph(glyphMarkup, width, height, 3.3, '#fff'));
	}
	return svgDocument(width, height, `<rect width="${width}" height="${height}" fill="#5474ad"/>${glyph(glyphMarkup, width, height, 3.3, '#38577f', 53, 35, ' opacity=".25"')}${glyph(glyphMarkup, width, height, 3.3, '#fff')}`);
}

function run(command, args) {
	const result = childProcess.spawnSync(command, args, { encoding: 'utf8' });
	if (result.error) {
		throw result.error;
	}
	if (result.status !== 0) {
		throw new Error(`${command} failed: ${(result.stderr || result.stdout || '').trim()}`);
	}
}

function renderSvg(svgPath, pngPath) {
	run('rsvg-convert', [svgPath, '-o', pngPath]);
}

function compositePreview(previewPath, patchPath, outputPath) {
	const php = [
		'$base = imagecreatefrompng($argv[1]);',
		'$patch = imagecreatefrompng($argv[2]);',
		'$x = (int) $argv[4];',
		'imagecopy($base, $patch, $x, 0, 0, 0, imagesx($patch), imagesy($patch));',
		'imagepng($base, $argv[3], 9);',
	].join('');
	run('php', ['-r', php, previewPath, patchPath, outputPath, '128']);
}

function writeOrCheck(target, data) {
	if (checkOnly) {
		if (!fs.existsSync(target) || !fs.readFileSync(target).equals(data)) {
			throw new Error(`Generated legacy X asset is missing or stale: ${path.relative(repositoryRoot, target)}`);
		}
		return;
	}
	fs.mkdirSync(path.dirname(target), { recursive: true });
	fs.writeFileSync(target, data);
}

function main() {
	const glyphMarkup = sourceGlyph();
	const temporaryDirectory = fs.mkdtempSync(path.join(os.tmpdir(), 'hssb-legacy-x-'));
	const generated = [
		['iconset/default/square/twitter.png', defaultTile(glyphMarkup)],
		['iconset/flat/square/Twitter.png', flatSquare(glyphMarkup)],
		['iconset/flat/circle/Twitter.png', flatCircle(glyphMarkup)],
		['iconset/long_shadow/square/twitter.png', longShadowTile(93, 93, 'square', glyphMarkup)],
		['iconset/long_shadow/circle/twitter.png', longShadowTile(101, 101, 'circle', glyphMarkup)],
		['iconset/long_shadow/square/twitter_2.png', longShadowTile(92, 92, 'square', glyphMarkup)],
		['iconset/long_shadow/circle/twitter_2.png', longShadowTile(101, 101, 'circle', glyphMarkup)],
		['iconset/prajin/square/twitter.png', prajinSquare(glyphMarkup)],
		['iconset/prajin/circle/twitter.png', prajinCircle(glyphMarkup)],
		['iconset/prajin/square/mail.png', prajinSquareMail()],
	];

	for (const [relativePath, svg] of generated) {
		const svgPath = path.join(temporaryDirectory, `${relativePath.replace(/[\\/]/g, '_')}.svg`);
		const pngPath = path.join(temporaryDirectory, `${relativePath.replace(/[\\/]/g, '_')}.png`);
		fs.writeFileSync(svgPath, svg);
		renderSvg(svgPath, pngPath);
		writeOrCheck(path.join(repositoryRoot, relativePath), fs.readFileSync(pngPath));
	}

	const previews = [
		['iconset/default/preview.png', 'default'],
		['iconset/flat/preview.png', 'flat'],
		['iconset/long_shadow/preview.png', 'long-shadow'],
		['iconset/prajin/preview.png', 'prajin'],
	];
	for (const [relativePath, style] of previews) {
		const patchSvgPath = path.join(temporaryDirectory, `${style}-preview.svg`);
		const patchPngPath = path.join(temporaryDirectory, `${style}-preview-patch.png`);
		const outputPngPath = path.join(temporaryDirectory, `${style}-preview-output.png`);
		fs.writeFileSync(patchSvgPath, previewPatch(116, 104, style, glyphMarkup));
		renderSvg(patchSvgPath, patchPngPath);
		compositePreview(path.join(repositoryRoot, relativePath), patchPngPath, outputPngPath);
		writeOrCheck(path.join(repositoryRoot, relativePath), fs.readFileSync(outputPngPath));
	}

	if (!checkOnly) {
		process.stdout.write('Generated historical X PNG assets and preview patches.\n');
	} else {
		process.stdout.write('Historical X PNG assets and preview patches are current.\n');
	}
}

main();
