'use strict';
const assert = require('assert/strict');
const fs = require('fs');
const os = require('os');
const path = require('path');
const { spawnSync } = require('child_process');
const directory = fs.mkdtempSync(path.join(os.tmpdir(), 'hssb-plugin-check-'));
try {
	for (const [value, success] of [
		['[]', true],
		[JSON.stringify([{ type: 'WARNING', code: 'review-me' }]), true],
		[JSON.stringify([{ type: 'ERROR', code: 'fail' }]), false],
		['{}', false],
		['invalid-json', false],
		[JSON.stringify([{ type: 'unknown' }]), false],
	]) {
		const file = path.join(directory, 'result.json');
		fs.writeFileSync(file, value);
		const result = spawnSync(process.execPath, ['scripts/verify-plugin-check.js', file]);
		assert.equal(result.status === 0, success, value);
	}
} finally {
	fs.rmSync(directory, { recursive: true, force: true });
}
console.log('Plugin Check errors and malformed output fail closed.');
