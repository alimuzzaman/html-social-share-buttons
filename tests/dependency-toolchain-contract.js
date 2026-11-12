'use strict';
const assert = require('node:assert/strict');
const fs = require('node:fs');
const path = require('node:path');
const vm = require('node:vm');
const manifest = JSON.parse(fs.readFileSync('package.json', 'utf8'));
const workspace = require('yaml').parse(fs.readFileSync('pnpm-workspace.yaml', 'utf8'));
for (const [selector, version] of Object.entries(workspace.overrides)) {
	const [parent, dependency] = selector.split('>');
	assert.equal(manifest.overrides[parent][dependency], version, 'npm/pnpm override drift');
}
assert.equal(Object.keys(manifest.overrides).length, Object.keys(workspace.overrides).length);
const npmLock = JSON.parse(fs.readFileSync('package-lock.json', 'utf8'));
const pnpmLock = require('yaml').parse(fs.readFileSync('pnpm-lock.yaml', 'utf8'));
for (const [name, version] of Object.entries(manifest.devDependencies)) {
	assert.equal(npmLock.packages['node_modules/' + name].version, version);
	assert.equal(pnpmLock.importers['.'].devDependencies[name].specifier, version);
}
assert.equal(npmLock.packages['node_modules/serialize-javascript'].version, '7.1.1');
const root = path.dirname(require.resolve('@wordpress/scripts/package.json'));
const copyRoot = path.dirname(require.resolve('copy-webpack-plugin/package.json', { paths: [root] }));
const serializerFile = require.resolve('serialize-javascript', { paths: [copyRoot] });
const serialize = require(serializerFile);
assert.equal(require(path.join(path.dirname(serializerFile), 'package.json')).version, '7.1.1');
// Exercise the actual serializer resolved by copy-webpack-plugin, including
// attacker-controlled RegExp/Date methods from GHSA-5c6j-r48x-rmvq.
for (const fake of [Object.create(RegExp.prototype), Object.create(Date.prototype)]) {
	fake.toJSON = () => '@placeholder';
	if (fake instanceof RegExp) {
		Object.defineProperty(fake, 'source', { get: () => 'x' });
		Object.defineProperty(fake, 'flags', { get: () => '"+(globalThis.injected=true)+"' });
	} else {
		fake.toISOString = () => '"+(globalThis.injected=true)+"';
	}
	const context = {};
	try {
		vm.runInNewContext('(' + serialize({ value: fake }) + ')', context, { timeout: 1000 });
	} catch (error) {
		// Invalid fake flags/dates may be rejected. Executing the payload may not.
		assert.ok(error instanceof Error || typeof error.message === 'string');
	}
	assert.equal(context.injected, undefined);
}
const serialized = serialize({ name: 'block', pattern: /\.json$/i, content: 'café </script>' });
assert.equal(vm.runInNewContext('(' + serialized + ').name'), 'block');
assert.ok(vm.runInNewContext('(' + serialized + ').pattern.test("block.JSON")'));
assert.ok(!serialized.includes('</script>'));
// webpack-dev-server 6 replaced the affected SockJS/UUID dependency path.
const serverRoot = path.dirname(require.resolve('webpack-dev-server/package.json', { paths: [root] }));
const serverManifest = require(path.join(serverRoot, 'package.json'));
assert.ok(Number(serverManifest.version.split('.')[0]) >= 6);
assert.equal(serverManifest.dependencies.sockjs, undefined);
console.log('Scoped serializer remediation and SockJS dependency removal passed.');
