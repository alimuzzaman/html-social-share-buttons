'use strict';
const fs = require('fs');
const MAX_WARNINGS = 58;
const findings = JSON.parse(fs.readFileSync(process.argv[2], 'utf8'));
if (!Array.isArray(findings)) {
	throw new Error('Plugin Check did not return a findings array');
}
const errors = findings.filter((finding) => String(finding.type).toUpperCase() === 'ERROR');
if (errors.length) {
	throw new Error(`Plugin Check reported ${errors.length} errors: ${JSON.stringify(errors)}`);
}
if (findings.some((finding) => !['ERROR', 'WARNING'].includes(String(finding.type).toUpperCase()))) {
	throw new Error('Unrecognized Plugin Check finding type');
}
if (findings.length > MAX_WARNINGS) {
	throw new Error(`Plugin Check reported ${findings.length} warnings, exceeding limit ${MAX_WARNINGS}.`);
}
console.log(`Plugin Check: zero errors; ${findings.length} warnings (limit ${MAX_WARNINGS}).`);
