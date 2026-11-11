const { test, expect } = require('@playwright/test');
const { createPublishedPage, login } = require('./helpers/wordpress');
const fs = require('fs');
const path = require('path');

test('native share preserves custom targets, keyboard semantics and cancellation', async ({ page }) => {
	await page.addInitScript(() => {
		window.hssbNativeCalls = [];
		Object.defineProperty(navigator, 'share', { configurable: true, value: (payload) => {
			window.hssbNativeCalls.push(payload);
			return Promise.reject(new DOMException('Cancelled', 'AbortError'));
		} });
	});
	await login(page);
	const fixture = await createPublishedPage(page, {
		title: 'Native share context title',
		content: '[zm_sh_btn iconset="bootstrap-solid" icons="native,copy" url="https://example.test/custom/?a=1&b=2#part"]',
	});
	await page.goto(fixture.link);
	const group = page.locator('.zmshbt.in_shortcode');
	const native = group.getByRole('button', { name: 'Share…', exact: true });
	await expect(native).toBeVisible();
	await expect(native).toHaveCSS('background-image', /url\(.+\)/);
	await expect(native).toHaveCSS('width', '36px');
	await expect(group.locator('a.copy')).toHaveCount(1);
	await native.focus();
	await page.keyboard.press('Space');
	await expect.poll(() => page.evaluate(() => window.hssbNativeCalls.length)).toBe(1);
	expect(await page.evaluate(() => window.hssbNativeCalls[0])).toEqual({
		title: 'Native share context title', url: 'https://example.test/custom/?a=1&b=2#part',
	});
	await expect(native).toBeFocused();
	await expect(native).toHaveAttribute('aria-label', 'Share…');
	await expect(native).not.toHaveAttribute('aria-busy', 'true');
});

test('native buttons retain every bundled icon shape and appearance', async ({ page }) => {
	await page.addInitScript(() => {
		Object.defineProperty(navigator, 'share', { configurable: true, value: () => Promise.resolve() });
	});
	const cells = [];
	for (const pack of ['default', 'flat', 'long-shadows', 'prajin', 'bootstrap-solid', 'tabler-outline']) {
		for (const shape of pack === 'default' ? ['square'] : ['square', 'circle']) {
			for (const appearance of ['legacy', 'minimal', 'framed', 'soft-shadow']) {
				cells.push({ pack, shape, appearance, id: `native-${pack}-${shape}-${appearance}` });
			}
		}
	}
	await login(page);
	const fixture = await createPublishedPage(page, {
		title: 'Native icon and appearance matrix',
		content: cells.map((cell) => `<section id="${cell.id}">[zm_sh_btn iconset="${cell.pack}" iconset_type="${cell.shape}" icons="native"]</section>`).join('\n'),
	});
	await page.goto(fixture.link);
	// Exercise shipped presentation CSS without changing the site's global setting.
	const pluginRoot = process.env.WP_ROOT
		? path.join(process.env.WP_ROOT, 'wp-content/plugins/html-social-share-buttons')
		: path.resolve(__dirname, '../..');
	await page.addStyleTag({ content: fs.readFileSync(path.join(pluginRoot, 'assets/frontend/button-appearance.css'), 'utf8') });
	for (const cell of cells) {
		const wrapper = page.locator(`#${cell.id} .zmshbt`);
		await wrapper.evaluate((node, appearance) => {
			node.classList.remove('hssb-appearance--minimal', 'hssb-appearance--framed', 'hssb-appearance--soft-shadow');
			if (appearance !== 'legacy') node.classList.add(`hssb-appearance--${appearance}`);
		}, cell.appearance);
		const button = wrapper.getByRole('button', { name: 'Share…', exact: true });
		await expect(button).toBeVisible();
		await expect(button).toHaveCSS('background-image', /native\.svg/);
		if (cell.appearance !== 'legacy') await expect(button).toHaveCSS('width', '44px');
	}
});

test('unsupported native sharing keeps a single fallback and no-JS remains useful', async ({ page, browser }) => {
	await page.addInitScript(() => {
		Object.defineProperty(navigator, 'share', { configurable: true, value: undefined });
	});
	await login(page);
	const fixture = await createPublishedPage(page, {
		title: 'Native fallback fixture',
		content: '<section id="native-only">[zm_sh_btn icons="native"]</section><section id="native-copy">[zm_sh_btn icons="native,copy"]</section>',
	});
	await page.goto(fixture.link);
	await expect(page.locator('#native-only a.native')).toBeVisible();
	await expect(page.locator('#native-copy [data-hssb-native-share]')).toBeHidden();
	await expect(page.locator('#native-copy a.copy')).toBeVisible();
	const noJs = await browser.newContext({ javaScriptEnabled: false });
	try {
		const plain = await noJs.newPage();
		await plain.goto(fixture.link);
		await expect(plain.locator('#native-only a.native')).toHaveAttribute('href', fixture.link);
		await expect(plain.locator('#native-copy a.copy')).toBeVisible();
		await expect(plain.locator('#native-copy [data-hssb-native-share]')).toBeHidden();
	} finally {
		await noJs.close();
	}
});
