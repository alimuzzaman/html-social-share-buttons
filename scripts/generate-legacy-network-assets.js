#!/usr/bin/env node

'use strict';

/**
 * Build the WhatsApp, Reddit, and Copy link tiles for the four historical
 * icon packs (Default, Flat, Long Shadows, Prajin).
 *
 * Each tile reproduces the wrapper of the pack's existing hand-adapted
 * Telegram/Bluesky SVGs and places a glyph read from the pinned MIT-licensed
 * Bootstrap Icons v1.13.1 sources vendored under scripts/iconsets/upstream.
 * Source checksums are verified before use; no network fetch is performed.
 *
 * Run `node scripts/generate-legacy-network-assets.js` to write the files, or
 * pass `--check` to verify that every committed file matches its generated
 * bytes.
 */

const crypto = require('crypto');
const fs = require('fs');
const path = require('path');

const repositoryRoot = path.resolve(__dirname, '..');
const sourceDirectory = path.join(__dirname, 'iconsets', 'upstream', 'bootstrap-icons-v1.13.1');
const checkOnly = process.argv.includes('--check');

const networks = {
	whatsapp: {
		label: 'WhatsApp',
		color: '#25D366',
		shadow: '#1a9c4b',
		source: ['whatsapp.svg', 'dc1de80c69f87f91ff4570e40baf8ff824a6fb81e617a9e054c26907cd608647'],
	},
	reddit: {
		label: 'Reddit',
		color: '#FF4500',
		shadow: '#c23600',
		source: ['reddit.svg', 'fa867c4e8587e8e274a42871f168625250aa7abb1e6efbb9cec5178b7bae3a3e'],
	},
	copy: {
		label: 'Copy link',
		color: '#4B5563',
		shadow: '#2f3744',
		source: ['link-45deg.svg', '3a58b899539e6e96a8a279c4dcc603a562627e733484aa72f721cda06b60c1f8'],
	},
};

// Bootstrap glyphs use a 16x16 grid.
const TILE_GLYPH = 'translate(24 24) scale(5)';
const LONG_SHADOW_GLYPH = 'translate(27 27) scale(4.625)';
const PRAJIN_SQUARE_GLYPH = 'translate(33 21.5) scale(2.5)';
const LONG_SHADOW_LAYERS = 64;
const PRAJIN_SHADOW_LAYERS = 14;

function sourceGlyph(network) {
	const [file, checksum] = network.source;
	const source = fs.readFileSync(path.join(sourceDirectory, file), 'utf8');
	const digests = [
		crypto.createHash('sha256').update(source).digest('hex'),
		crypto.createHash('sha256').update(source.replace(/\n$/, '')).digest('hex'),
	];
	if (!digests.includes(checksum)) {
		throw new Error(`Upstream icon checksum mismatch: bootstrap-icons-v1.13.1/${file}`);
	}
	const match = source.match(/<svg\b[^>]*>([\s\S]*?)<\/svg>/i);
	if (!match) {
		throw new Error(`Upstream icon is malformed: bootstrap-icons-v1.13.1/${file}`);
	}
	return match[1].replace(/<!--[\s\S]*?-->/g, '').replace(/>\s+</g, '><').replace(/\s+/g, ' ').trim();
}

function document(network, viewBox, body) {
	return `<svg xmlns="http://www.w3.org/2000/svg" viewBox="${viewBox}" role="img">` +
		`<title>${network.label}</title>` +
		`<!-- Bootstrap Icons v1.13.1 ${network.source[0]}; https://github.com/twbs/icons; MIT licensed. -->` +
		`${body}</svg>\n`;
}

function inlineGlyph(glyph, transform) {
	return `<g fill="#fff" transform="${transform}">${glyph}</g>`;
}

function defaultSquare(network, glyph) {
	return document(
		network,
		'0 0 128 128',
		`<rect x="5" y="5" width="118" height="118" fill="${network.color}"/>` +
		'<rect x="10" y="10" width="108" height="108" fill="none" stroke="#fff" stroke-width="2" stroke-dasharray="6 4"/>' +
		inlineGlyph(glyph, TILE_GLYPH)
	);
}

function flatSquare(network, glyph) {
	return document(network, '0 0 128 128', `<rect width="128" height="128" fill="${network.color}"/>${inlineGlyph(glyph, TILE_GLYPH)}`);
}

function plainCircle(network, glyph) {
	return document(network, '0 0 128 128', `<circle cx="64" cy="64" r="64" fill="${network.color}"/>${inlineGlyph(glyph, TILE_GLYPH)}`);
}

function longShadow(network, glyph, shape) {
	const outline = shape === 'circle' ? '<circle cx="64" cy="64" r="64"/>' : '<rect width="128" height="128" rx="18"/>';
	const background = shape === 'circle'
		? `<circle cx="64" cy="64" r="64" fill="${network.color}"/>`
		: `<rect width="128" height="128" rx="18" fill="${network.color}"/>`;
	let layers = '';
	for (let step = 1; step <= LONG_SHADOW_LAYERS; step += 1) {
		layers += `<g transform="translate(-${step * 2} ${step * 2})"><use href="#glyph" transform="${LONG_SHADOW_GLYPH}"/></g>`;
	}
	return document(
		network,
		'0 0 128 128',
		`<defs><clipPath id="clip">${outline}</clipPath><g id="glyph">${glyph}</g></defs>${background}` +
		`<g clip-path="url(#clip)" fill="${network.shadow}" opacity=".72">${layers}</g>` +
		`<use href="#glyph" fill="#fff" transform="${LONG_SHADOW_GLYPH}"/>`
	);
}

function prajinSquare(network, glyph) {
	let layers = '';
	for (let step = 1; step <= PRAJIN_SHADOW_LAYERS; step += 1) {
		layers += `<g transform="translate(${step} ${step})"><use href="#glyph" transform="${PRAJIN_SQUARE_GLYPH}"/></g>`;
	}
	return document(
		network,
		'0 0 106 83',
		'<defs><clipPath id="clip"><rect width="106" height="83" rx="4"/></clipPath>' +
		'<filter id="soft" x="-20%" y="-20%" width="160%" height="160%"><feGaussianBlur stdDeviation=".8"/></filter>' +
		`<g id="glyph">${glyph}</g></defs>` +
		`<rect width="106" height="83" rx="4" fill="${network.color}"/>` +
		`<g clip-path="url(#clip)" fill="#1f2937" opacity=".22" filter="url(#soft)">${layers}</g>` +
		`<use href="#glyph" fill="#fff" transform="${PRAJIN_SQUARE_GLYPH}"/>`
	);
}

const builders = [
	['iconset/default/square', defaultSquare],
	['iconset/flat/square', flatSquare],
	['iconset/flat/circle', plainCircle],
	['iconset/long_shadow/square', (network, glyph) => longShadow(network, glyph, 'square')],
	['iconset/long_shadow/circle', (network, glyph) => longShadow(network, glyph, 'circle')],
	['iconset/prajin/square', prajinSquare],
	['iconset/prajin/circle', plainCircle],
];

const expected = new Map();
for (const [networkId, network] of Object.entries(networks)) {
	const glyph = sourceGlyph(network);
	for (const [directory, build] of builders) {
		expected.set(`${directory}/${networkId}.svg`, build(network, glyph));
	}
}

let failures = 0;
for (const [relativePath, contents] of expected) {
	const target = path.join(repositoryRoot, relativePath);
	if (checkOnly) {
		if (!fs.existsSync(target) || fs.readFileSync(target, 'utf8') !== contents) {
			process.stderr.write(`Generated legacy network asset is missing or stale: ${relativePath}\n`);
			failures += 1;
		}
		continue;
	}
	fs.mkdirSync(path.dirname(target), { recursive: true });
	fs.writeFileSync(target, contents);
}

if (failures) {
	process.exit(1);
}
process.stdout.write(checkOnly
	? 'Historical pack WhatsApp, Reddit, and Copy link tiles are current.\n'
	: 'Generated historical pack WhatsApp, Reddit, and Copy link tiles.\n');
