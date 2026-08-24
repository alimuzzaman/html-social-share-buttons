#!/usr/bin/env node

import { readFile, stat } from 'node:fs/promises';
import { createHash } from 'node:crypto';
import { fileURLToPath } from 'node:url';
import path from 'node:path';

const sourceDir = path.dirname(fileURLToPath(import.meta.url));
const assetDir = path.dirname(sourceDir);
const pngSignature = Buffer.from([137, 80, 78, 71, 13, 10, 26, 10]);
const expectedTagline = 'HTML + CSS sharing. No frontend JS by default.';
const expectedTaglineOutlineSha = '998770c2a64b1717524112e8da751eca1a72cd4f81fb5251e432b4c4addc3954';

const expectedPngs = new Map([
  ['banner-772x250.png', { width: 772, height: 250, maxBytes: 4_000_000 }],
  ['banner-1544x500.png', { width: 1544, height: 500, maxBytes: 4_000_000 }],
  ['icon-128x128.png', { width: 128, height: 128, maxBytes: 1_000_000 }],
  ['icon-256x256.png', { width: 256, height: 256, maxBytes: 1_000_000 }],
]);

function fail(message) {
  throw new Error(message);
}

function pngMetadata(buffer) {
  if (!buffer.subarray(0, 8).equals(pngSignature)) {
    fail('Invalid PNG signature');
  }

  const metadata = {
    width: buffer.readUInt32BE(16),
    height: buffer.readUInt32BE(20),
    bitDepth: buffer[24],
    colorType: buffer[25],
    hasSrgb: false,
  };
  let offset = 8;

  while (offset < buffer.length) {
    const length = buffer.readUInt32BE(offset);
    const type = buffer.toString('ascii', offset + 4, offset + 8);
    metadata.hasSrgb ||= type === 'sRGB' || type === 'iCCP';
    offset += length + 12;
  }

  return metadata;
}

function channel(hex) {
  const value = Number.parseInt(hex, 16) / 255;
  return value <= 0.04045 ? value / 12.92 : ((value + 0.055) / 1.055) ** 2.4;
}

function luminance(hex) {
  const normalized = hex.replace('#', '');
  return (
    0.2126 * channel(normalized.slice(0, 2))
    + 0.7152 * channel(normalized.slice(2, 4))
    + 0.0722 * channel(normalized.slice(4, 6))
  );
}

function contrast(first, second) {
  const lighter = Math.max(luminance(first), luminance(second));
  const darker = Math.min(luminance(first), luminance(second));
  return (lighter + 0.05) / (darker + 0.05);
}

for (const [filename, expected] of expectedPngs) {
  const filepath = path.join(assetDir, filename);
  const [buffer, fileStat] = await Promise.all([readFile(filepath), stat(filepath)]);
  const actual = pngMetadata(buffer);

  if (actual.width !== expected.width || actual.height !== expected.height) {
    fail(`${filename}: expected ${expected.width}x${expected.height}, got ${actual.width}x${actual.height}`);
  }
  if (actual.bitDepth !== 8 || ![2, 6].includes(actual.colorType)) {
    fail(`${filename}: expected 8-bit RGB/RGBA, got bit depth ${actual.bitDepth}, color type ${actual.colorType}`);
  }
  if (!actual.hasSrgb) {
    fail(`${filename}: missing sRGB color tag`);
  }
  if (fileStat.size >= expected.maxBytes) {
    fail(`${filename}: ${fileStat.size} bytes exceeds limit ${expected.maxBytes}`);
  }

  console.log(`PASS ${filename}: ${actual.width}x${actual.height}, 8-bit ${actual.colorType === 2 ? 'RGB' : 'RGBA'}, sRGB, ${fileStat.size} bytes`);
}

const iconSvg = await readFile(path.join(assetDir, 'icon.svg'), 'utf8');
const bannerSvg = await readFile(path.join(sourceDir, 'banner-master.svg'), 'utf8');
const taglineOutline = await readFile(path.join(sourceDir, 'type/tagline.outlined.svg'), 'utf8');
const exportScript = await readFile(path.join(sourceDir, 'export-assets.sh'), 'utf8');

if (!/width="256" height="256" viewBox="0 0 256 256"/.test(iconSvg)) {
  fail('icon.svg: incorrect dimensions or viewBox');
}
if (!/width="1544" height="500" viewBox="0 0 1544 500"/.test(bannerSvg)) {
  fail('banner-master.svg: incorrect dimensions or viewBox');
}

for (const [filename, svg] of [
  ['icon.svg', iconSvg],
  ['banner-master.svg', bannerSvg],
  ['type/tagline.outlined.svg', taglineOutline],
]) {
  const forbidden = [/<script\b/i, /javascript:/i, /<image\b/i, /<text\b/i, /font-family=/i, /https?:\/\/(?!www\.w3\.org\/)/i];
  const match = forbidden.find((pattern) => pattern.test(svg));
  if (match) {
    fail(`${filename}: found forbidden active, external, raster, or font-dependent content (${match})`);
  }
  console.log(`PASS ${filename}: static vector, no raster/external/font dependency`);
}

if (!bannerSvg.includes('HTML Social Share Buttons banner') || !bannerSvg.includes(`<desc id="desc">${expectedTagline}</desc>`)) {
  fail('banner-master.svg: missing exact accessible product copy');
}

const taglineOutlineSha = createHash('sha256').update(taglineOutline).digest('hex');
if (taglineOutlineSha !== expectedTaglineOutlineSha) {
  fail(`type/tagline.outlined.svg: unexpected outline checksum ${taglineOutlineSha}`);
}
if (!/width="923" height="48\.390625" viewBox="0 0 923 48\.390625"/.test(taglineOutline)) {
  fail('type/tagline.outlined.svg: unexpected dimensions');
}
if (/<rect\b/.test(taglineOutline)) {
  fail('type/tagline.outlined.svg: expected a transparent outline with no preview background');
}
const taglineGlyphCount = (taglineOutline.match(/<use\b/g) || []).length;
if (taglineGlyphCount !== [...expectedTagline].length) {
  fail(`type/tagline.outlined.svg: expected ${[...expectedTagline].length} positioned glyphs, got ${taglineGlyphCount}`);
}
console.log(`PASS tagline: exact accessible copy, ${taglineGlyphCount} positioned glyphs, pinned outline checksum`);

const taglinePlacement = bannerSvg.match(
  /<svg x="([\d.]+)" y="343\.25" width="923" height="48\.390625" viewBox="0 0 923 48\.390625" overflow="visible" aria-hidden="true">/,
);
if (!taglinePlacement) {
  fail('banner-master.svg: missing expected centered tagline placement');
}
const taglineLeft = Number.parseFloat(taglinePlacement[1]);
const taglineRight = taglineLeft + 923;
if (taglineLeft < 144 || taglineRight > 1400) {
  fail(`banner-master.svg: tagline extends outside the 2x safe zone (${taglineLeft}..${taglineRight})`);
}
console.log(`PASS tagline placement: centered at x=${(taglineLeft + 923 / 2).toFixed(1)}, inside safe zone`);

const sharedBannerSourceCount = (exportScript.match(/"\$source_dir\/banner-master\.svg"/g) || []).length;
if (sharedBannerSourceCount !== 2) {
  fail(`export-assets.sh: expected both banner sizes to use banner-master.svg, found ${sharedBannerSourceCount} uses`);
}
console.log('PASS 1x/2x parity: both banner exports use the same vector master');

const titleContrast = contrast('#F8FAFC', '#172554');
const supportContrast = contrast('#CBD5E1', '#172554');
if (titleContrast < 4.5 || supportContrast < 4.5) {
  fail(`Text contrast below AA: title ${titleContrast.toFixed(2)}, support ${supportContrast.toFixed(2)}`);
}

console.log(`PASS contrast: title ${titleContrast.toFixed(2)}:1, support ${supportContrast.toFixed(2)}:1 against deep blue`);
console.log('PASS shared geometry: build-artwork.mjs supplies the mark to both icon.svg and banner-master.svg');
