const { test, expect } = require('@playwright/test');
const { login } = require('./helpers/wordpress');

test('previews unsaved templates without saving or navigating', async ({ page }) => {
	await login(page);
	await page.goto('/wp-admin/options-general.php?page=zm_shbt_opt');
	const card = page.locator('.zm_network_item').filter({
		has: page.locator('input[name="zm_shbt_fld[icons][facebook]"]'),
	});
	await card.locator('input[type="checkbox"]').check();
	const editor = card.locator('.zm_template_parameter_editor').first();
	await editor.fill('%%permalink%%&label=%%description%%');
	// Completing a token opens suggestions, which can cover the button on mobile.
	await editor.press('Escape');
	await expect(page.locator('.zm_template_autocomplete')).toHaveCount(0);
	const formValues = () => page.locator('#zm-social-share-settings').evaluate((form) => Array.from(new FormData(form).entries()));
	const before = await formValues();
	const previews = [];
	const saves = [];
	page.on('request', (request) => {
		const body = request.postData() || '';
		if (body.includes('action=hssb_preview_share_template')) previews.push(request);
		if (body.includes('action=zm_sh_save_settings')) saves.push(request);
	});
	const initialUrl = page.url();
	await card.getByRole('button', { name: 'Preview sample', exact: true }).click();
	const result = card.getByRole('status');
	await expect(result.locator('code')).toContainText('example.com');
	await expect(result.locator('code')).toContainText('Example%20site%20description');
	await expect(result.locator('a')).toHaveCount(0);
	expect(previews).toHaveLength(1);
	expect(saves).toHaveLength(0);
	expect(await formValues()).toEqual(before);
	expect(page.url()).toBe(initialUrl);
	await card.getByText('Sample content', { exact: true }).click();
	await expect(card.locator('details')).toContainText('%%description%%');
	await card.getByRole('button', { name: 'Restore defaults', exact: true }).click();
	await expect(result.locator('code')).toHaveCount(0);
	await card.getByRole('button', { name: 'Preview sample', exact: true }).click();
	await expect(result).toContainText('Resolved URL using the default template:');
	await expect(result.locator('code')).not.toContainText('%%');
	await expect(page.locator('input[name="zm_shbt_fld[g_analytics]"], input[name="zm_shbt_fld[use_port]"]')).toHaveCount(0);
});
