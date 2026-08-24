#!/usr/bin/env node

import { readFile, writeFile } from 'node:fs/promises';

const pngSignature = Buffer.from([137, 80, 78, 71, 13, 10, 26, 10]);

function crc32(buffer) {
  let crc = 0xffffffff;

  for (const byte of buffer) {
    crc ^= byte;
    for (let bit = 0; bit < 8; bit += 1) {
      crc = (crc >>> 1) ^ ((crc & 1) ? 0xedb88320 : 0);
    }
  }

  return (crc ^ 0xffffffff) >>> 0;
}

function pngChunk(type, data) {
  const typeBuffer = Buffer.from(type, 'ascii');
  const chunk = Buffer.alloc(12 + data.length);
  chunk.writeUInt32BE(data.length, 0);
  typeBuffer.copy(chunk, 4);
  data.copy(chunk, 8);
  chunk.writeUInt32BE(crc32(Buffer.concat([typeBuffer, data])), 8 + data.length);
  return chunk;
}

async function tagSrgb(filename) {
  const input = await readFile(filename);

  if (!input.subarray(0, 8).equals(pngSignature)) {
    throw new Error(`${filename} is not a PNG file`);
  }

  const chunks = [];
  let offset = 8;
  let inserted = false;

  while (offset < input.length) {
    const length = input.readUInt32BE(offset);
    const end = offset + 12 + length;
    const type = input.toString('ascii', offset + 4, offset + 8);

    if (type !== 'sRGB' && type !== 'iCCP') {
      chunks.push(input.subarray(offset, end));
    }

    if (type === 'IHDR' && !inserted) {
      chunks.push(pngChunk('sRGB', Buffer.from([0])));
      inserted = true;
    }

    offset = end;
  }

  await writeFile(filename, Buffer.concat([pngSignature, ...chunks]));
}

if (process.argv.length < 3) {
  throw new Error('Usage: node tag-srgb.mjs <png> [png ...]');
}

await Promise.all(process.argv.slice(2).map(tagSrgb));
console.log(`Tagged ${process.argv.length - 2} PNG files as sRGB`);
