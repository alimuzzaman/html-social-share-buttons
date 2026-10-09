#!/usr/bin/env node

'use strict';

const crypto = require('crypto');
const fs = require('fs');
const path = require('path');

const repositoryRoot = path.resolve(__dirname, '..');
const assetRoot = path.join(repositoryRoot, 'assets', 'iconsets');
const sourceRoot = path.join(__dirname, 'iconsets', 'upstream');
const checkOnly = process.argv.includes('--check');
const networkColors = {
	facebook: '#1877f2',
	x: '#111111',
	linkedin: '#0a66c2',
	pinterest: '#bd081c',
	telegram: '#229ed9',
	bluesky: '#1185fe',
	whatsapp: '#25d366',
	reddit: '#ff4500',
	copy: '#4b5563',
	mail: '#5f6368',
};

const iconSets = {
	'bootstrap-solid': {
		sourceDirectory: 'bootstrap-icons-v1.13.1',
		sourceName: 'Bootstrap Icons v1.13.1',
		sourceUrl: 'https://github.com/twbs/icons/tree/v1.13.1/icons',
		transform: 'translate(32 32) scale(4)',
		mode: 'solid',
		files: {
			facebook: ['facebook.svg', 'fccefddbce24b7acef96fcdb75aa9bc37dc1d6e725efa8b32eca6b619ff153ff'],
			x: ['twitter-x.svg', '173e37e584ccb49cb87242a2e5444201da2d779cee1b1464732893302975950d'],
			linkedin: ['linkedin.svg', 'ddcbb2735eea12f090ea0ee371d1b9a3462531dc11efd0e944adc2d38c71e2df'],
			pinterest: ['pinterest.svg', '083f12722ed3e07f560e156fd6b836985ee6ded90446998d7754ac2e091eed3c'],
			telegram: ['telegram.svg', 'c56e6919d63e25681ead8615fb50e80d5de5be0466cac9d2790611030af97cce'],
			bluesky: ['bluesky.svg', '3bccd3889db609418f95baab25107a0beb8b7642d49d90c0fb1f872b2a313d37'],
			whatsapp: ['whatsapp.svg', 'dc1de80c69f87f91ff4570e40baf8ff824a6fb81e617a9e054c26907cd608647'],
			reddit: ['reddit.svg', 'fa867c4e8587e8e274a42871f168625250aa7abb1e6efbb9cec5178b7bae3a3e'],
			copy: ['link-45deg.svg', '3a58b899539e6e96a8a279c4dcc603a562627e733484aa72f721cda06b60c1f8'],
			mail: ['envelope.svg', '46354ede34e6acffd9828377884007ae3090db3b892e4b4a8fb8eec3e1017d63'],
		},
	},
	'tabler-outline': {
		sourceDirectory: 'tabler-icons-v3.46.0',
		sourceName: 'Tabler Icons v3.46.0',
		sourceUrl: 'https://github.com/tabler/tabler-icons/tree/v3.46.0/icons/outline',
		transform: 'translate(28 28) scale(3)',
		mode: 'outline',
		files: {
			facebook: ['brand-facebook.svg', '53dacd220215b24e91f7c34ec5c13ee6053678cfa36c49483af0d396ac2d3da0'],
			x: ['brand-x.svg', '780d5b422ad633c95020593b4e3daeb4a8f71a2f3640d5aac5dd4410d5a9b4bb'],
			linkedin: ['brand-linkedin.svg', '88c0934596b27c394e5f221f6dc30903e97d30a3ea0b2063c766485143a8ee75'],
			pinterest: ['brand-pinterest.svg', '810bca7fa4780d5fb5305b683b1999f23c7fcb1b30d6f916797b5c4d8098bfb8'],
			telegram: ['brand-telegram.svg', '4a1023bb65efb2a268a3e4740225c369fbdaae87fba3d48c87ea2dd526c4cfbe'],
			bluesky: ['brand-bluesky.svg', '9387998373350399524767e7369b1805bf21573cede4ebdfb4fb15d91b4dc9f6'],
			whatsapp: ['brand-whatsapp.svg', '9bbaad3b257a124accd91dda575ea1a3e32318faa925a280879cb1a2dcd417dc'],
			reddit: ['brand-reddit.svg', 'c4e35cb92bc535e45e19b35ecaaef4a01eb4d6da4db59dc8877e8aca8f85d4b5'],
			copy: ['link.svg', '0c1448dddf85857b80da2c51ddb5a27399013d3830eb756654cce2eef93fff33'],
			mail: ['mail.svg', '5f27eaf548faab23f994093fa56cc9011b8457a1f2b26dc50e7ed5afe95727df'],
		},
	},
};

function sourceGlyph(set, networkId) {
	const metadata = set.files[networkId];
	const source = fs.readFileSync(path.join(sourceRoot, set.sourceDirectory, metadata[0]), 'utf8');
	const digests = [
		crypto.createHash('sha256').update(source).digest('hex'),
		crypto.createHash('sha256').update(source.replace(/\n$/, '')).digest('hex'),
	];
	if (!digests.includes(metadata[1])) {
		throw new Error(`Upstream icon checksum mismatch: ${set.sourceDirectory}/${metadata[0]}`);
	}
	const match = source.match(/<svg\b[^>]*>([\s\S]*?)<\/svg>/i);
	if (!match) {
		throw new Error(`Upstream icon is malformed: ${set.sourceDirectory}/${metadata[0]}`);
	}
	return match[1].replace(/<!--[\s\S]*?-->/g, '').replace(/\s+/g, ' ').trim();
}

function tile(set, networkId, shape) {
	const color = networkColors[networkId];
	const background = set.mode === 'solid'
		? shape === 'circle'
			? `<circle cx="64" cy="64" r="60" fill="${color}"/>`
			: `<rect x="4" y="4" width="120" height="120" rx="24" fill="${color}"/>`
		: shape === 'circle'
			? `<circle cx="64" cy="64" r="58" fill="#fff" stroke="${color}" stroke-width="6"/>`
			: `<rect x="7" y="7" width="114" height="114" rx="22" fill="#fff" stroke="${color}" stroke-width="6"/>`;
	const glyphColor = set.mode === 'solid' ? '#fff' : color;
	const glyphAttributes = set.mode === 'solid'
		? 'fill="#fff"'
		: `fill="none" stroke="${color}" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"`;
	// Some upstream glyphs paint details with currentColor, which resolves to
	// black inside an <img>/background SVG; bind it to the tile's glyph color.
	const glyph = sourceGlyph(set, networkId).replace(/currentColor/g, glyphColor);
	return `${background}<g transform="${set.transform}" ${glyphAttributes}>${glyph}</g>`;
}

function svg(set, networkId, shape) {
	return `<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 128 128">\n<!-- ${set.sourceName}; ${set.sourceUrl}; MIT licensed. -->\n${tile(set, networkId, shape)}\n</svg>\n`;
}

function preview(set) {
	const tiles = Object.keys(networkColors).map((networkId, index) =>
		`<g transform="translate(${index * 72 + 4} 4) scale(.5)">${tile(set, networkId, 'square')}</g>`
	).join('\n');
	const width = Object.keys(networkColors).length * 72;
	return `<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 ${width} 72">\n<!-- ${set.sourceName}; ${set.sourceUrl}; MIT licensed. -->\n${tiles}\n</svg>\n`;
}

function stylesheet(id) {
	return `.zmshbt.${id}.left,.zmshbt.${id}.right{position:fixed;top:30%;z-index:9999}\n` +
		`.zmshbt.${id}.left{left:0}.zmshbt.${id}.right{right:0}\n` +
		`.zmshbt.${id}.in_widget a,.zmshbt.${id}.in_shortcode a,.zmshbt.${id}.in_block a,.zmshbt.${id}.in_elementor a,.zmshbt.${id}.in_php_function a{display:inline-block}\n` +
		`.zmshbt.${id} a{display:block;width:36px;height:36px;margin:7px;background-position:center;background-repeat:no-repeat;background-size:cover;transition:transform .16s ease,filter .16s ease}\n` +
		`.zmshbt.${id} a:hover{filter:brightness(1.06);transform:translateY(-2px) scale(1.06)}\n` +
		`.zmshbt.${id} a:focus-visible{outline:3px solid #2271b1;outline-offset:3px}\n` +
		`.zmshbt.${id} .zmshbt-profile-separator{display:inline-block;width:1px;height:28px;margin:11px 14px;vertical-align:top;background:#c3c4c7}\n` +
		`.zmshbt.${id}.left .zmshbt-profile-separator,.zmshbt.${id}.right .zmshbt-profile-separator{display:block;width:28px;height:1px;margin:14px 11px}\n` +
		`@media (max-width:600px){.zmshbt.left,.zmshbt.right{position:static!important;display:flex;flex-wrap:wrap;justify-content:center}.zmshbt.left a,.zmshbt.right a{margin:5px!important}}\n` +
		`@media (max-width:600px){.zmshbt.${id}.left .zmshbt-profile-separator,.zmshbt.${id}.right .zmshbt-profile-separator{display:inline-block;width:1px;height:28px;margin:9px 12px}}\n` +
		`@media (prefers-reduced-motion:reduce){.zmshbt.${id} a{transition:none}.zmshbt.${id} a:hover{transform:none}}\n`;
}

const expected = new Map();
for (const [id, set] of Object.entries(iconSets)) {
	expected.set(path.join(id, 'preview.svg'), preview(set));
	expected.set(path.join(id, 'style.css'), stylesheet(id));
	for (const shape of ['square', 'circle']) {
		for (const networkId of Object.keys(networkColors)) {
			expected.set(path.join(id, shape, `${networkId}.svg`), svg(set, networkId, shape));
		}
	}
}

let failures = 0;
for (const [relativePath, contents] of expected) {
	const target = path.join(assetRoot, relativePath);
	if (checkOnly) {
		if (!fs.existsSync(target) || fs.readFileSync(target, 'utf8') !== contents) {
			process.stderr.write(`Generated icon asset is missing or stale: ${relativePath}\n`);
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
process.stdout.write(checkOnly ? 'Generated icon assets are current.\n' : 'Generated complete SVG icon sets.\n');
