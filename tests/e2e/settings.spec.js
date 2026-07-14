const { test, expect } = require( '@playwright/test' );

async function login( page ) {
	await page.goto( '/wp-login.php' );
	await page.locator( '#user_login' ).fill( process.env.WP_ADMIN_USER || 'admin' );
	await page.locator( '#user_pass' ).fill( process.env.WP_ADMIN_PASSWORD || 'admin' );
	await page.locator( '#wp-submit' ).click();
	await expect( page ).not.toHaveURL( /wp-login\.php/ );
}

test.describe( 'Settings accessibility', () => {
	test( 'opens and closes the generated-code dialog with keyboard focus', async ( { page } ) => {
		await login( page );
		await page.goto( '/wp-admin/options-general.php?page=zm_shbt_opt' );

		const trigger = page.locator( 'button.get_phpcode' );
		await expect( trigger ).toBeVisible();
		await trigger.focus();
		await page.keyboard.press( 'Enter' );

		const dialog = page.locator( '[role="dialog"]' );
		await expect( dialog ).toBeVisible();
		await expect( dialog ).toHaveAttribute( 'aria-modal', 'true' );
		await expect( dialog ).toHaveAttribute( 'aria-labelledby', 'zm-sh-code-modal-title' );
		await expect( page.locator( '#zm-sh-code-modal-title' ) ).toBeVisible();
		await expect.poll( () => page.evaluate( () => document.activeElement && document.activeElement.textContent ) ).toBe( 'Close' );

		await page.locator( 'textarea#copy_shortcode' ).focus();
		await page.keyboard.press( 'Tab' );
		await expect.poll( () => page.evaluate( () => document.activeElement && document.activeElement.textContent ) ).toBe( 'Close' );
		await page.keyboard.press( 'Shift+Tab' );
		await expect( page.locator( 'textarea#copy_shortcode' ) ).toBeFocused();
		await page.keyboard.press( 'Shift+Tab' );
		await expect( page.locator( '#shortcode-iconset-type' ) ).toBeFocused();
		await page.keyboard.press( 'Escape' );
		await expect( dialog ).toBeHidden();
		await expect.poll( () => page.evaluate( () => document.activeElement && document.activeElement.className ) ).toContain( 'get_phpcode' );

	} );
} );
