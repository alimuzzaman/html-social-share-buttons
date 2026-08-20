#!/usr/bin/env node

'use strict';

const assert = require('assert');
const fs = require('fs');
const os = require('os');
const path = require('path');
const { loadBaseline, validateEvidence, validateRecord } = require('../scripts/validate-staging-soak-evidence');

const BASELINE = {
	startedAt: '2026-08-12T09:04:40.000Z',
	candidateSha256: 'd6575a33ff120ec768b6f71a4ea29f51a083760d016cd5f9a599aa0982945b05',
	installedManifestSha256: '8ede5b6e6789c218a10c8efe5f395a4db0d6b928167a4050334da2f78432c42b',
	candidateGitRevision: '78c7f2344f01620441528b00707bb77152de476c',
	fixtureUrl: 'https://default-html-social-share-buttons.sandbox.asb.bd/hssb-staging-soak-fixture/',
	runtime: { wordpress: '7.0.3', php: '8.3.33', active_plugin_version: '2.2.6' },
	persistedHashes: {
		settings: '9c6286ffade97ba5926dafef4c328f697a4ff0e0df300e5d6c4fb41d31748aaf',
		disabled_share: '36761a168eb691d20edf88ace3d06fb63ec9112f257ac2f20fce1afdc331b40b',
		elementor: '2cb96b33cbd7c1b5080d4666f71740e5257c777e7322d0f455ad33d7359a6388',
		wpbakery: 'd92ebdfe82b849bdc64686a4ea341e14bfa2fb668d6228bb7f34ac67059c713c',
		content: 'a00f5e609c577e8cd87b34a79b04d71026a2e8efcd34dd0beda4140c2b910565',
		schema_version: 'absent',
	},
};

function record(day, observedAt) {
	const date = observedAt.slice(0, 10);
	const probe = {
		ok: true,
		observed_at: observedAt,
		url: BASELINE.fixtureUrl,
		status: 200,
		checks: {
			http_ok: true,
			marker_present: true,
			share_wrapper_present: true,
			share_link_present: true,
			no_raw_placeholder: true,
			no_encoded_placeholder: true,
		},
		body_sha256: 'a'.repeat(64),
	};
	return {
		schema_version: 1,
		day,
		date,
		observed_at: observedAt,
		candidate_sha256: BASELINE.candidateSha256,
		installed_manifest_sha256: BASELINE.installedManifestSha256,
		candidate_git_revision: BASELINE.candidateGitRevision,
		runtime: { ...BASELINE.runtime },
		fixture: { url: BASELINE.fixtureUrl, probe },
		persisted_hashes: { ...BASELINE.persistedHashes },
		error_snapshot: { plugin_errors: 0, http_5xx: 0 },
		browser_reviewed: [1, 7, 13].includes(day),
		rollback: day === 7 ? rollbackEvidence(observedAt) : null,
		anomalies: [],
		disposition: 'pass',
	};
}

function rollbackEvidence() {
	const restoredAt = arguments[0] || '2026-08-18T09:15:00.000Z';
	const publishedAt = new Date(new Date(restoredAt).getTime() - 60000).toISOString();
	const probe = (observedAt, clean) => ({
		ok: clean,
		observed_at: observedAt,
		url: BASELINE.fixtureUrl,
		status: 200,
		checks: {
			http_ok: true,
			marker_present: true,
			share_wrapper_present: true,
			share_link_present: true,
			no_raw_placeholder: clean,
			no_encoded_placeholder: clean,
		},
		body_sha256: (clean ? 'd' : 'e').repeat(64),
	});
	return {
		completed: true,
		published_sha256: 'f056820bf7377ca4e228fe28792f23a3e6bf226db4d1a98c85bb26be9d23f941',
		published_version: '2.2.6',
		published_activated: true,
		published_probe: probe(publishedAt, false),
		published_persisted_hashes: { ...BASELINE.persistedHashes },
		restored_candidate_sha256: BASELINE.candidateSha256,
		restored_manifest_sha256: BASELINE.installedManifestSha256,
		restored_probe: probe(restoredAt, true),
		restored_persisted_hashes: { ...BASELINE.persistedHashes },
	};
}

function expectFailure(candidate, day, date, pattern) {
	assert.match(validateRecord(candidate, day, date, new Date('2026-08-30T00:00:00.000Z'), false, BASELINE).join('\n'), pattern);
}

const dayOne = record(1, '2026-08-12T09:04:40.000Z');
assert.deepStrictEqual(validateRecord(dayOne, 1, '2026-08-12', new Date('2026-08-13T00:00:00.000Z'), false, BASELINE), []);

const dayTwo = record(2, '2026-08-13T09:15:00.000Z');
assert.deepStrictEqual(validateRecord(dayTwo, 2, '2026-08-13', new Date('2026-08-14T00:00:00.000Z'), false, BASELINE), []);

expectFailure(record(2, '2026-08-13T08:00:00.000Z'), 2, '2026-08-13', /elapsed window/);

const drifted = record(2, '2026-08-13T09:15:00.000Z');
drifted.installed_manifest_sha256 = 'b'.repeat(64);
expectFailure(drifted, 2, '2026-08-13', /installed manifest/);

const missingTimestamp = record(2, '2026-08-13T09:15:00.000Z');
delete missingTimestamp.observed_at;
expectFailure(missingTimestamp, 2, '2026-08-13', /observed_at must be an ISO timestamp/);

const unrelatedLink = record(2, '2026-08-13T09:15:00.000Z');
unrelatedLink.fixture.probe.checks.share_link_present = false;
expectFailure(unrelatedLink, 2, '2026-08-13', /share_link_present/);

const missingRollback = record(7, '2026-08-18T09:15:00.000Z');
missingRollback.rollback = null;
expectFailure(missingRollback, 7, '2026-08-18', /rollback must be complete/);

const dayFourteen = record(14, '2026-08-25T09:15:00.000Z');
assert.deepStrictEqual(validateRecord(dayFourteen, 14, '2026-08-25', new Date('2026-08-26T00:00:00.000Z'), false, BASELINE), []);

const finalRollback = record(14, '2026-08-26T09:05:00.000Z');
finalRollback.kind = 'final_rollback';
finalRollback.browser_reviewed = false;
finalRollback.rollback = rollbackEvidence(finalRollback.observed_at);
assert.deepStrictEqual(validateRecord(finalRollback, 14, '2026-08-26', new Date('2026-08-27T00:00:00.000Z'), true, BASELINE), []);

const earlyFinalRollback = record(14, '2026-08-26T09:00:00.000Z');
earlyFinalRollback.kind = 'final_rollback';
earlyFinalRollback.browser_reviewed = false;
earlyFinalRollback.rollback = rollbackEvidence(earlyFinalRollback.observed_at);
assert.match(validateRecord(earlyFinalRollback, 14, '2026-08-26', new Date('2026-08-27T00:00:00.000Z'), true, BASELINE).join('\n'), /completion threshold/);

const missingPublishedProbe = record(7, '2026-08-18T09:15:00.000Z');
delete missingPublishedProbe.rollback.published_probe;
expectFailure(missingPublishedProbe, 7, '2026-08-18', /published probe/);

const restoredHashDrift = record(7, '2026-08-18T09:15:00.000Z');
restoredHashDrift.rollback.restored_persisted_hashes.settings = 'f'.repeat(64);
expectFailure(restoredHashDrift, 7, '2026-08-18', /restored persisted hash settings drifted/);

const unorderedRollback = record(7, '2026-08-18T09:15:00.000Z');
unorderedRollback.rollback.published_probe.observed_at = unorderedRollback.rollback.restored_probe.observed_at;
expectFailure(unorderedRollback, 7, '2026-08-18', /published probe must precede restored probe/);

const inconsistentPublishedProbe = record(7, '2026-08-18T09:15:00.000Z');
inconsistentPublishedProbe.rollback.published_probe.ok = true;
expectFailure(inconsistentPublishedProbe, 7, '2026-08-18', /probe ok is inconsistent/);

const changedBody = record(2, '2026-08-13T09:15:00.000Z');
changedBody.fixture.probe.body_sha256 = 'c'.repeat(64);
assert.deepStrictEqual(validateRecord(changedBody, 2, '2026-08-13', new Date('2026-08-14T00:00:00.000Z'), false, BASELINE), []);

const temporaryRoot = fs.mkdtempSync(path.join(os.tmpdir(), 'hssb-soak-validator-'));
try {
	function writeRecord(candidate) {
		const directory = path.join(temporaryRoot, candidate.date);
		fs.mkdirSync(directory, { recursive: true });
		const day = String(candidate.day).padStart(2, '0');
		fs.writeFileSync(
			path.join(directory, `day-${day}.md`),
			`# Day ${day} - ${candidate.date}\n`
		);
		fs.writeFileSync(path.join(directory, `day-${day}.json`), `${JSON.stringify(candidate)}\n`);
	}
	writeRecord(dayOne);
	assert.deepStrictEqual(validateEvidence(temporaryRoot, new Date('2026-08-13T00:00:00.000Z'), 1, false, BASELINE).errors, []);
	assert.match(validateEvidence(temporaryRoot, new Date('2026-08-13T00:00:00.000Z'), 2, false, BASELINE).errors.join('\n'), /missing records through Day 2/);
	fs.writeFileSync(path.join(temporaryRoot, '2026-08-12', 'unexpected.txt'), 'unexpected');
	assert.match(validateEvidence(temporaryRoot, new Date('2026-08-13T00:00:00.000Z'), 0, false, BASELINE).errors.join('\n'), /unrecognized/);
	fs.mkdirSync(path.join(temporaryRoot, '2026-08-13'));
	fs.writeFileSync(path.join(temporaryRoot, '2026-08-13', 'day-02.md'), '# Day 02 - 2026-08-13\n');
	assert.match(validateEvidence(temporaryRoot, new Date('2026-08-14T00:00:00.000Z'), 0, false, BASELINE).errors.join('\n'), /matching JSON sidecar is missing/);
} finally {
	fs.rmSync(temporaryRoot, { recursive: true, force: true });
}

const completionRoot = fs.mkdtempSync(path.join(os.tmpdir(), 'hssb-soak-completion-'));
try {
	function writeMachineFile(candidate, filename, heading) {
		const directory = path.join(completionRoot, candidate.date);
		fs.mkdirSync(directory, { recursive: true });
		fs.writeFileSync(path.join(directory, filename), `${JSON.stringify(candidate)}\n`);
		fs.writeFileSync(path.join(directory, filename.replace(/\.json$/, '.md')), `${heading}\n`);
	}
	for (let day = 1; day <= 14; day += 1) {
		const observed = new Date(new Date(BASELINE.startedAt).getTime() + (day - 1) * 86400000 + (day === 1 ? 0 : 600000));
		const candidate = record(day, observed.toISOString());
		const paddedDay = String(day).padStart(2, '0');
		writeMachineFile(candidate, `day-${paddedDay}.json`, `# Day ${paddedDay} - ${candidate.date}`);
	}
	assert.match(
		validateEvidence(completionRoot, new Date('2026-08-27T00:00:00.000Z'), 14, true, BASELINE).errors.join('\n'),
		/completion requires one final rollback record/
	);
	writeMachineFile(finalRollback, 'final-rollback.json', `# Final rollback - ${finalRollback.date}`);
	assert.deepStrictEqual(validateEvidence(completionRoot, new Date('2026-08-27T00:00:00.000Z'), 14, true, BASELINE).errors, []);
} finally {
	fs.rmSync(completionRoot, { recursive: true, force: true });
}

const baselineRoot = fs.mkdtempSync(path.join(os.tmpdir(), 'hssb-soak-baseline-'));
try {
	const baselinePath = path.join(baselineRoot, 'baseline.json');
	assert.throws(() => loadBaseline(baselinePath), /not initialized/);
	fs.writeFileSync(
		baselinePath,
		`${JSON.stringify({
			schema_version: 1,
			status: 'active',
			started_at: BASELINE.startedAt,
			candidate_sha256: BASELINE.candidateSha256,
			installed_manifest_sha256: BASELINE.installedManifestSha256,
			candidate_git_revision: BASELINE.candidateGitRevision,
			fixture_url: BASELINE.fixtureUrl,
			runtime: BASELINE.runtime,
			persisted_hashes: BASELINE.persistedHashes,
		})}\n`
	);
	assert.deepStrictEqual(loadBaseline(baselinePath), BASELINE);
} finally {
	fs.rmSync(baselineRoot, { recursive: true, force: true });
}

process.stdout.write('Staging soak evidence validator contract passed.\n');
