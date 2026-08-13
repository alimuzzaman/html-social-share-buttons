#!/usr/bin/env node

'use strict';

const fs = require('fs');
const path = require('path');

const BASELINE = {
	startedAt: '2026-08-12T09:04:40Z',
	candidateSha256: 'd6575a33ff120ec768b6f71a4ea29f51a083760d016cd5f9a599aa0982945b05',
	installedManifestSha256: '8ede5b6e6789c218a10c8efe5f395a4db0d6b928167a4050334da2f78432c42b',
	candidateGitRevision: '78c7f2344f01620441528b00707bb77152de476c',
	fixtureUrl: 'https://default-html-social-share-buttons.sandbox.asb.bd/hssb-staging-soak-fixture/',
	persistedHashes: {
		settings: '9c6286ffade97ba5926dafef4c328f697a4ff0e0df300e5d6c4fb41d31748aaf',
		disabled_share: '36761a168eb691d20edf88ace3d06fb63ec9112f257ac2f20fce1afdc331b40b',
		elementor: '2cb96b33cbd7c1b5080d4666f71740e5257c777e7322d0f455ad33d7359a6388',
		wpbakery: 'd92ebdfe82b849bdc64686a4ea341e14bfa2fb668d6228bb7f34ac67059c713c',
		content: 'a00f5e609c577e8cd87b34a79b04d71026a2e8efcd34dd0beda4140c2b910565',
		schema_version: 'absent',
	},
};

function argument(name) {
	const index = process.argv.indexOf(name);
	return index === -1 ? '' : String(process.argv[index + 1] || '');
}

function validateRecord(record, expectedDay, expectedDate, now = new Date(), finalRollback = false) {
	const errors = [];
	const requireValue = (condition, message) => {
		if (!condition) errors.push(message);
	};
	const observed = new Date(record.observed_at);
	const observedValid = !Number.isNaN(observed.getTime());
	const observedIso = observedValid ? observed.toISOString() : '';
	const started = new Date(BASELINE.startedAt);
	const windowStart = new Date(started.getTime() + (expectedDay - 1) * 86400000);
	const windowEnd = new Date(windowStart.getTime() + 86400000);
	const completionThreshold = new Date(started.getTime() + 14 * 86400000);
	const validateProbe = (probe, label, requireClean, requireRecordTimestamp = false) => {
		const probeObserved = new Date(probe && probe.observed_at);
		const probeObservedValid = !Number.isNaN(probeObserved.getTime());
		requireValue(probe && typeof probe.ok === 'boolean', `${label} probe ok must be boolean`);
		requireValue(probe && probeObservedValid && probe.observed_at === probeObserved.toISOString(), `${label} probe timestamp is invalid`);
		requireValue(!requireRecordTimestamp || probe && probe.observed_at === record.observed_at, `${label} probe timestamp must match observation`);
		requireValue(probeObservedValid && observedValid && probeObserved <= observed, `${label} probe cannot follow the record observation`);
		requireValue(probe && probe.url === BASELINE.fixtureUrl, `${label} probe URL drifted`);
		requireValue(probe && probe.status >= 200 && probe.status < 300, `${label} probe HTTP status must be 2xx`);
		for (const check of ['http_ok', 'marker_present', 'share_wrapper_present', 'share_link_present']) {
			requireValue(probe && probe.checks && probe.checks[check] === true, `${label} probe check ${check} must pass`);
		}
		for (const check of ['no_raw_placeholder', 'no_encoded_placeholder']) {
			requireValue(probe && probe.checks && typeof probe.checks[check] === 'boolean', `${label} probe check ${check} must be boolean`);
			if (requireClean) requireValue(probe && probe.checks && probe.checks[check] === true, `${label} probe check ${check} must pass`);
		}
		const checks = probe && probe.checks;
		const calculatedOk = checks && ['http_ok', 'marker_present', 'share_wrapper_present', 'share_link_present', 'no_raw_placeholder', 'no_encoded_placeholder'].every((check) => checks[check] === true);
		requireValue(probe && probe.ok === calculatedOk, `${label} probe ok is inconsistent with checks`);
		if (requireClean) requireValue(probe && probe.ok === true, `${label} probe must pass`);
		requireValue(probe && /^[a-f0-9]{64}$/.test(probe.body_sha256), `${label} probe body SHA-256 is invalid`);
	};
	const validatePersistedHashes = (hashes, label) => {
		for (const [key, value] of Object.entries(BASELINE.persistedHashes)) {
			requireValue(hashes && hashes[key] === value, `${label} persisted hash ${key} drifted`);
		}
	};

	requireValue(record.schema_version === 1, 'schema_version must be 1');
	requireValue(record.day === expectedDay, `day must be ${expectedDay}`);
	requireValue(record.date === expectedDate, `date must be ${expectedDate}`);
	requireValue(finalRollback ? record.kind === 'final_rollback' : record.kind === undefined, 'kind does not match record type');
	requireValue(observedValid, 'observed_at must be an ISO timestamp');
	requireValue(observedValid && record.observed_at === observedIso, 'observed_at must be canonical UTC ISO');
	requireValue(
		observedValid && (finalRollback ? observed >= completionThreshold : observed >= windowStart && observed < windowEnd),
		finalRollback ? 'final rollback cannot precede the completion threshold' : `observation must fall in Day ${expectedDay} elapsed window`
	);
	requireValue(observedValid && observed <= now, 'observation cannot be in the future');
	requireValue(observedValid && observedIso.slice(0, 10) === expectedDate, 'path date must match observed UTC date');
	requireValue(record.candidate_sha256 === BASELINE.candidateSha256, 'candidate SHA-256 drifted');
	requireValue(record.installed_manifest_sha256 === BASELINE.installedManifestSha256, 'installed manifest SHA-256 drifted');
	requireValue(record.candidate_git_revision === BASELINE.candidateGitRevision, 'candidate Git revision drifted');
	requireValue(record.runtime && record.runtime.wordpress === '7.0.3', 'WordPress runtime drifted');
	requireValue(record.runtime && record.runtime.php === '8.3.33', 'PHP runtime drifted');
	requireValue(record.runtime && record.runtime.active_plugin_version === '2.2.6', 'active plugin version drifted');

	const probe = record.fixture && record.fixture.probe;
	requireValue(record.fixture && record.fixture.url === BASELINE.fixtureUrl, 'fixture URL drifted');
	validateProbe(probe, 'fixture', true, true);
	validatePersistedHashes(record.persisted_hashes, 'record');
	requireValue(record.error_snapshot && record.error_snapshot.plugin_errors === 0, 'plugin error count must be zero');
	requireValue(record.error_snapshot && record.error_snapshot.http_5xx === 0, 'HTTP 5xx count must be zero');
	requireValue(Array.isArray(record.anomalies) && record.anomalies.length === 0, 'passing record cannot contain anomalies');
	requireValue(record.disposition === 'pass', 'disposition must be pass');
	requireValue(record.browser_reviewed === (!finalRollback && [1, 7, 13].includes(expectedDay)), `browser_reviewed is incorrect for Day ${expectedDay}`);
	if (expectedDay === 7 || finalRollback) {
		const label = finalRollback ? 'Final' : 'Day 7';
		requireValue(record.rollback && record.rollback.completed === true, `${label} rollback must be complete`);
		requireValue(record.rollback && record.rollback.published_sha256 === 'f056820bf7377ca4e228fe28792f23a3e6bf226db4d1a98c85bb26be9d23f941', `${label} published archive SHA-256 is invalid`);
		requireValue(record.rollback && record.rollback.published_version === '2.2.6', `${label} published version is invalid`);
		requireValue(record.rollback && record.rollback.published_activated === true, `${label} published archive must be activated`);
		validateProbe(record.rollback && record.rollback.published_probe, `${label} published`, false);
		validatePersistedHashes(record.rollback && record.rollback.published_persisted_hashes, `${label} published`);
		requireValue(record.rollback && record.rollback.restored_candidate_sha256 === BASELINE.candidateSha256, `${label} candidate archive was not restored`);
		requireValue(record.rollback && record.rollback.restored_manifest_sha256 === BASELINE.installedManifestSha256, `${label} candidate tree was not restored`);
		validateProbe(record.rollback && record.rollback.restored_probe, `${label} restored`, true, true);
		validatePersistedHashes(record.rollback && record.rollback.restored_persisted_hashes, `${label} restored`);
		const publishedObserved = new Date(record.rollback && record.rollback.published_probe && record.rollback.published_probe.observed_at);
		const restoredObserved = new Date(record.rollback && record.rollback.restored_probe && record.rollback.restored_probe.observed_at);
		requireValue(!Number.isNaN(publishedObserved.getTime()) && !Number.isNaN(restoredObserved.getTime()) && publishedObserved < restoredObserved, `${label} published probe must precede restored probe`);
	} else {
		requireValue(record.rollback === null, `rollback must be null on Day ${expectedDay}`);
	}

	return errors;
}

function validateEvidence(root, now = new Date(), requireThrough = 0, completion = false) {
	const files = [];
	const finalFiles = [];
	const humanFiles = [];
	const errors = [];
	const seenDates = new Set();
	for (const dateDirectory of fs.readdirSync(root, { withFileTypes: true })) {
		if (!dateDirectory.isDirectory()) continue;
		for (const entry of fs.readdirSync(path.join(root, dateDirectory.name), { withFileTypes: true })) {
			if (!entry.isFile()) continue;
			if (/^day-\d{2}\.json$/.test(entry.name)) files.push({ date: dateDirectory.name, name: entry.name });
			else if (entry.name === 'final-rollback.json') finalFiles.push({ date: dateDirectory.name, name: entry.name });
			else if (/^day-\d{2}\.md$/.test(entry.name) || entry.name === 'final-rollback.md') humanFiles.push({ date: dateDirectory.name, name: entry.name });
			else errors.push(`${entry.name}: unrecognized staging evidence file`);
		}
	}
	for (const human of humanFiles) {
		const jsonName = human.name.replace(/\.md$/, '.json');
		if (!fs.existsSync(path.join(root, human.date, jsonName))) errors.push(`${human.name}: matching JSON sidecar is missing`);
	}
	files.sort((left, right) => left.name.localeCompare(right.name));
	files.forEach((file, index) => {
		const day = index + 1;
		const expectedName = `day-${String(day).padStart(2, '0')}.json`;
		if (seenDates.has(file.date)) errors.push(`${file.name}: duplicate daily record date ${file.date}`);
		seenDates.add(file.date);
		if (file.name !== expectedName) errors.push(`${file.name}: expected ${expectedName}`);
		const fullPath = path.join(root, file.date, file.name);
		const humanPath = fullPath.replace(/\.json$/, '.md');
		if (!fs.existsSync(humanPath)) errors.push(`${file.name}: matching human-readable Markdown record is missing`);
		else if (!fs.readFileSync(humanPath, 'utf8').startsWith(`# Day ${String(day).padStart(2, '0')} - ${file.date}\n`)) errors.push(`${file.name}: Markdown heading does not match path`);
		try {
			const record = JSON.parse(fs.readFileSync(fullPath, 'utf8'));
			errors.push(...validateRecord(record, day, file.date, now).map((message) => `${file.name}: ${message}`));
		} catch (error) {
			errors.push(`${file.name}: invalid JSON: ${error.message}`);
		}
	});
	if (requireThrough && files.length < requireThrough) errors.push(`missing records through Day ${requireThrough}`);
	if (finalFiles.length > 1) errors.push('expected at most one final rollback record');
	if (finalFiles.length === 1) {
		const file = finalFiles[0];
		const fullPath = path.join(root, file.date, file.name);
		const humanPath = fullPath.replace(/\.json$/, '.md');
		if (!fs.existsSync(humanPath)) errors.push(`${file.name}: matching human-readable Markdown record is missing`);
		else if (!fs.readFileSync(humanPath, 'utf8').startsWith(`# Final rollback - ${file.date}\n`)) errors.push(`${file.name}: Markdown heading does not match path`);
		try {
			const record = JSON.parse(fs.readFileSync(fullPath, 'utf8'));
			errors.push(...validateRecord(record, 14, file.date, now, true).map((message) => `${file.name}: ${message}`));
		} catch (error) {
			errors.push(`${file.name}: invalid JSON: ${error.message}`);
		}
	}
	if (completion && files.length !== 14) errors.push('completion requires exactly 14 daily records');
	if (completion && finalFiles.length !== 1) errors.push('completion requires one final rollback record');
	return { files, finalFiles, errors };
}

function main() {
	const root = path.resolve(argument('--root') || path.join(__dirname, '..', 'docs/evidence/staging-soak'));
	const requireThrough = Number(argument('--require-through') || 0);
	const completion = process.argv.includes('--completion');
	const nowArgument = argument('--now');
	const now = nowArgument ? new Date(nowArgument) : new Date();
	if (!Number.isInteger(requireThrough) || requireThrough < 0 || Number.isNaN(now.getTime())) {
		throw new Error('Invalid --require-through or --now value.');
	}
	const result = validateEvidence(root, now, requireThrough, completion);
	if (result.errors.length) throw new Error(result.errors.join('\n'));
	process.stdout.write(`Validated ${result.files.length} staging soak evidence record(s).\n`);
}

if (require.main === module) {
	try {
		main();
	} catch (error) {
		process.stderr.write(`${error.message}\n`);
		process.exitCode = 1;
	}
}

module.exports = { BASELINE, validateEvidence, validateRecord };
