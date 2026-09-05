'use strict';
const assert = require('assert/strict');
const fs = require('fs');
const YAML = require('yaml');
const compatibilityText = fs.readFileSync('.github/workflows/compatibility.yml', 'utf8');
const deployText = fs.readFileSync('.github/workflows/deploy.yml', 'utf8');
const compatibility = YAML.parse(compatibilityText);
const deploy = YAML.parse(deployText);
const required = ['php', 'js', 'quality', 'wordpress', 'distribution', 'archive-runtime'];
assert.ok(Object.hasOwn(compatibility.on, 'workflow_call'));
assert.ok(Object.hasOwn(compatibility.on, 'pull_request'));
assert.equal(compatibility.on.push.tags, undefined);
assert.deepEqual(compatibility.jobs.required.needs, required);
assert.equal(deploy.jobs.validation.uses, './.github/workflows/compatibility.yml');
assert.equal(deploy.jobs.publish.needs, 'validation');
assert.equal(deploy.jobs.publish.if, "github.event_name == 'workflow_dispatch' && github.ref_type == 'tag' && inputs.confirmation == format('publish-{0}', github.ref_name)");
assert.match(deploy.jobs.publish.if, /github.ref_type == 'tag'/);
assert.match(deploy.jobs.publish.if, /inputs.confirmation == format\('publish-\{0\}', github.ref_name\)/);
assert.ok(deploy.on.workflow_dispatch.inputs.reviewed_sha256.required);
assert.ok(deploy.on.workflow_dispatch.inputs.confirmation.required);
assert.doesNotMatch(compatibilityText + deployText, /always\(\)|continue-on-error/);
assert.doesNotMatch(compatibilityText, /secrets\./);
assert.equal(compatibility.on.workflow_dispatch.inputs.failure_probe.default, false);
assert.equal(compatibility.on.workflow_call.inputs.failure_probe.default, false);
const failureProbe = compatibility.jobs.quality.steps.find((step) => step.if === 'inputs.failure_probe');
assert.match(failureProbe.run, /exit 1/);
for (const job of Object.values(compatibility.jobs)) {
	assert.equal(job.if, undefined, 'Required jobs must not be conditionally bypassed');
}
const publish = JSON.stringify(deploy.jobs.publish.steps);
assert.doesNotMatch(publish, /pnpm|composer install|run zip|build-archive/);
assert.match(publish, /release-candidate.py extract/);
assert.match(publish, /REVIEWED_SHA256/);
for (const name of ['wordpress', 'archive-runtime']) {
	assert.equal(compatibility.jobs[name].needs, 'distribution');
	assert.match(JSON.stringify(compatibility.jobs[name].steps), /candidate\/candidate.zip/);
}
// Evaluate the default success-only needs graph for each injected terminal result.
// This is a local graph contract, not a claimed hosted Actions execution.
for (const failed of required) {
	for (const state of ['failure', 'cancelled', 'skipped']) {
		const results = Object.fromEntries(required.map((job) => [job, job === failed ? state : 'success']));
		const aggregate = compatibility.jobs.required.needs.every((job) => results[job] === 'success');
		const validation = required.every((job) => results[job] === 'success') && aggregate;
		assert.equal(validation, false, `${failed} ${state} must block publication`);
	}
}
console.log('Release workflow graph and 18 failure/cancel/skip paths passed.');
