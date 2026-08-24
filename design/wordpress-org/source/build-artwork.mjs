#!/usr/bin/env node

import { readFile, writeFile } from 'node:fs/promises';
import { fileURLToPath } from 'node:url';
import path from 'node:path';

const sourceDir = path.dirname(fileURLToPath(import.meta.url));
const assetDir = path.dirname(sourceDir);

const palette = {
  ink: '#0B1220',
  deepBlue: '#172554',
  productBlue: '#2563EB',
  cyan: '#38BDF8',
  teal: '#2DD4BF',
  paper: '#F8FAFC',
  slate: '#CBD5E1',
};

function indent(value, spaces = 4) {
  const padding = ' '.repeat(spaces);
  return value
    .trim()
    .split('\n')
    .map((line) => `${padding}${line}`)
    .join('\n');
}

function embedOutlinedSvg(svg, { idPrefix, x, y }) {
  const root = svg.match(
    /<svg[^>]*width="([^"]+)" height="([^"]+)" viewBox="([^"]+)"[^>]*>([\s\S]*)<\/svg>\s*$/,
  );

  if (!root) {
    throw new Error(`Could not parse outlined type fragment: ${idPrefix}`);
  }

  const [, width, height, viewBox, originalBody] = root;
  const body = originalBody.replaceAll('glyph-0-', `${idPrefix}-glyph-`);

  return `<svg x="${x}" y="${y}" width="${width}" height="${height}" viewBox="${viewBox}" overflow="visible" aria-hidden="true">
${indent(body, 2)}
</svg>`;
}

function markGeometry(prefix) {
  return `<g id="${prefix}-mark" fill="none" stroke-linecap="round" stroke-linejoin="round">
  <path d="M82 70 L48 128 L82 186" stroke="${palette.paper}" stroke-width="16"/>
  <path d="M174 70 L208 128 L174 186" stroke="${palette.paper}" stroke-width="16"/>
  <path d="M110 128 L153 96 M110 128 L153 160" stroke="${palette.cyan}" stroke-width="12"/>
  <circle cx="110" cy="128" r="15" fill="${palette.paper}" stroke="${palette.ink}" stroke-width="5"/>
  <circle cx="153" cy="96" r="15" fill="${palette.productBlue}" stroke="${palette.paper}" stroke-width="5"/>
  <circle cx="153" cy="160" r="15" fill="${palette.teal}" stroke="${palette.paper}" stroke-width="5"/>
</g>`;
}

function iconSvg() {
  return `<?xml version="1.0" encoding="UTF-8"?>
<svg xmlns="http://www.w3.org/2000/svg" width="256" height="256" viewBox="0 0 256 256" role="img" aria-labelledby="title desc">
  <title id="title">HTML Social Share Buttons icon</title>
  <desc id="desc">Angle brackets surrounding a connected three-node share path.</desc>
  <defs>
    <linearGradient id="icon-background" x1="22" y1="18" x2="232" y2="240" gradientUnits="userSpaceOnUse">
      <stop offset="0" stop-color="${palette.ink}"/>
      <stop offset="1" stop-color="${palette.deepBlue}"/>
    </linearGradient>
    <radialGradient id="icon-halo" cx="0" cy="0" r="1" gradientTransform="translate(206 40) rotate(135) scale(150)" gradientUnits="userSpaceOnUse">
      <stop stop-color="${palette.productBlue}" stop-opacity="0.30"/>
      <stop offset="1" stop-color="${palette.productBlue}" stop-opacity="0"/>
    </radialGradient>
    <clipPath id="icon-clip">
      <rect width="256" height="256" rx="44"/>
    </clipPath>
  </defs>
  <g clip-path="url(#icon-clip)">
    <rect width="256" height="256" fill="url(#icon-background)"/>
    <rect width="256" height="256" fill="url(#icon-halo)"/>
    <path d="M-8 211 C50 176 81 217 134 188 C179 164 205 172 270 126" fill="none" stroke="${palette.cyan}" stroke-opacity="0.10" stroke-width="2"/>
    <circle cx="29" cy="36" r="2" fill="${palette.paper}" fill-opacity="0.22"/>
    <circle cx="225" cy="216" r="2" fill="${palette.paper}" fill-opacity="0.18"/>
  </g>
  <rect x="1.5" y="1.5" width="253" height="253" rx="42.5" fill="none" stroke="${palette.paper}" stroke-opacity="0.10" stroke-width="3"/>
${indent(markGeometry('icon'), 2)}
</svg>
`;
}

function decorationTile({ x, y, size, rotation, accent, content }) {
  const center = size / 2;
  const inner = content === 'branch'
    ? `<path d="M${size * 0.30} ${size * 0.53} L${size * 0.66} ${size * 0.32} M${size * 0.30} ${size * 0.53} L${size * 0.66} ${size * 0.72}"/>
       <circle cx="${size * 0.30}" cy="${size * 0.53}" r="${size * 0.075}"/>
       <circle cx="${size * 0.66}" cy="${size * 0.32}" r="${size * 0.075}"/>
       <circle cx="${size * 0.66}" cy="${size * 0.72}" r="${size * 0.075}"/>`
    : `<path d="M${size * 0.27} ${size * 0.37} H${size * 0.73} M${size * 0.27} ${size * 0.53} H${size * 0.62} M${size * 0.27} ${size * 0.69} H${size * 0.53}"/>`;

  return `<g transform="translate(${x} ${y}) rotate(${rotation} ${center} ${center})">
  <rect width="${size}" height="${size}" rx="${size * 0.24}" fill="${palette.paper}" fill-opacity="0.045" stroke="${accent}" stroke-opacity="0.22" stroke-width="2"/>
  <g fill="none" stroke="${accent}" stroke-opacity="0.42" stroke-width="${Math.max(3, size * 0.045)}" stroke-linecap="round" stroke-linejoin="round">
    ${inner}
  </g>
</g>`;
}

async function bannerSvg() {
  const [titleSvg, taglineSvg] = await Promise.all([
    readFile(path.join(sourceDir, 'type/product-name.outlined.svg'), 'utf8'),
    readFile(path.join(sourceDir, 'type/tagline.outlined.svg'), 'utf8'),
  ]);

  const title = embedOutlinedSvg(titleSvg, {
    idPrefix: 'title',
    x: 243,
    y: 227.5,
  });
  const tagline = embedOutlinedSvg(taglineSvg, {
    idPrefix: 'tagline',
    x: 310.5,
    y: 343.25,
  });

  return `<?xml version="1.0" encoding="UTF-8"?>
<svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="1544" height="500" viewBox="0 0 1544 500" role="img" aria-labelledby="title desc">
  <title id="title">HTML Social Share Buttons banner</title>
  <desc id="desc">HTML + CSS sharing. No frontend JS by default.</desc>
  <defs>
    <linearGradient id="banner-background" x1="80" y1="20" x2="1470" y2="510" gradientUnits="userSpaceOnUse">
      <stop offset="0" stop-color="${palette.ink}"/>
      <stop offset="0.54" stop-color="#101C36"/>
      <stop offset="1" stop-color="${palette.deepBlue}"/>
    </linearGradient>
    <radialGradient id="left-halo" cx="0" cy="0" r="1" gradientTransform="translate(105 70) rotate(27) scale(470 330)" gradientUnits="userSpaceOnUse">
      <stop stop-color="${palette.productBlue}" stop-opacity="0.32"/>
      <stop offset="1" stop-color="${palette.productBlue}" stop-opacity="0"/>
    </radialGradient>
    <radialGradient id="right-halo" cx="0" cy="0" r="1" gradientTransform="translate(1450 410) rotate(-153) scale(430 310)" gradientUnits="userSpaceOnUse">
      <stop stop-color="${palette.cyan}" stop-opacity="0.16"/>
      <stop offset="1" stop-color="${palette.cyan}" stop-opacity="0"/>
    </radialGradient>
    <linearGradient id="mark-tile" x1="700" y1="54" x2="844" y2="198" gradientUnits="userSpaceOnUse">
      <stop stop-color="#15284C"/>
      <stop offset="1" stop-color="#0C172A"/>
    </linearGradient>
    <pattern id="dot-grid" width="28" height="28" patternUnits="userSpaceOnUse">
      <circle cx="2" cy="2" r="1.5" fill="${palette.paper}" fill-opacity="0.12"/>
    </pattern>
    <clipPath id="banner-clip">
      <rect width="1544" height="500"/>
    </clipPath>
  </defs>

  <g clip-path="url(#banner-clip)">
    <rect width="1544" height="500" fill="url(#banner-background)"/>
    <rect width="1544" height="500" fill="url(#left-halo)"/>
    <rect width="1544" height="500" fill="url(#right-halo)"/>
    <rect x="0" y="0" width="250" height="500" fill="url(#dot-grid)" opacity="0.40"/>
    <rect x="1294" y="0" width="250" height="500" fill="url(#dot-grid)" opacity="0.32"/>

    <g fill="none" stroke-linecap="round" stroke-linejoin="round">
      <path d="M-40 396 C74 330 91 208 204 155 C241 138 270 113 308 70" stroke="${palette.cyan}" stroke-opacity="0.16" stroke-width="3"/>
      <path d="M1584 100 C1472 151 1451 275 1340 329 C1307 345 1272 382 1238 433" stroke="${palette.teal}" stroke-opacity="0.15" stroke-width="3"/>
      <circle cx="205" cy="154" r="8" fill="${palette.cyan}" fill-opacity="0.20" stroke="none"/>
      <circle cx="1338" cy="330" r="8" fill="${palette.teal}" fill-opacity="0.20" stroke="none"/>
    </g>

${indent(decorationTile({ x: -42, y: 78, size: 132, rotation: -8, accent: palette.cyan, content: 'branch' }), 4)}
${indent(decorationTile({ x: 90, y: 40, size: 96, rotation: 9, accent: palette.paper, content: 'lines' }), 4)}
${indent(decorationTile({ x: 112, y: 330, size: 112, rotation: -6, accent: palette.teal, content: 'branch' }), 4)}
${indent(decorationTile({ x: 1454, y: 60, size: 132, rotation: 8, accent: palette.teal, content: 'branch' }), 4)}
${indent(decorationTile({ x: 1358, y: 270, size: 104, rotation: -8, accent: palette.paper, content: 'lines' }), 4)}
${indent(decorationTile({ x: 1442, y: 376, size: 96, rotation: 7, accent: palette.cyan, content: 'branch' }), 4)}

    <rect x="700" y="54" width="144" height="144" rx="36" fill="url(#mark-tile)" stroke="${palette.paper}" stroke-opacity="0.13" stroke-width="2"/>
    <g transform="translate(708 62) scale(0.5)">
${indent(markGeometry('banner'), 6)}
    </g>
  </g>

${indent(title, 2)}
${indent(tagline, 2)}
</svg>
`;
}

await Promise.all([
  writeFile(path.join(assetDir, 'icon.svg'), iconSvg()),
  writeFile(path.join(sourceDir, 'banner-master.svg'), await bannerSvg()),
]);

console.log('Generated icon.svg and source/banner-master.svg');
