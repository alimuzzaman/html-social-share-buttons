const { test, expect } = require( '@playwright/test' );
const { login } = require( './helpers/wordpress' );

test.describe( 'Settings accessibility', () => {
	test( 'persists the three viewer audience booleans', async ( { page } ) => {
		test.setTimeout( 60_000 );
		await login( page );
		await page.goto( '/wp-admin/options-general.php?page=zm_shbt_opt' );
		const iconset = page.locator( 'select#iconset' );
		if ( ( await iconset.inputValue() ) === 'default' ) {
			await iconset.selectOption( 'bootstrap-solid' );
			await saveSettings( page );
			await page.reload();
		}
		await expect( iconset ).toHaveValue( 'bootstrap-solid' );
		await expect( iconset.locator( 'option[value="default"]' ) ).toHaveCount( 0 );

		const currentUser = page.getByLabel(
			'Current user (content author)',
			{ exact: true }
		);
		const loggedInUser = page.getByLabel( 'Other logged-in users', {
			exact: true,
		} );
		const loggedOutUser = page.getByLabel( 'Logged-out users', {
			exact: true,
		} );
		await expect( currentUser ).toBeChecked();
		await expect( loggedInUser ).toBeChecked();
		await expect( loggedOutUser ).toBeChecked();

		await currentUser.uncheck();
		await loggedInUser.uncheck();
		await loggedOutUser.uncheck();
		try {
			await saveSettings( page );
			await page.reload();

			await expect(
				page.getByLabel( 'Current user (content author)', {
					exact: true,
				} )
			).not.toBeChecked();
			await expect(
				page.getByLabel( 'Other logged-in users', { exact: true } )
			).not.toBeChecked();
			await expect(
				page.getByLabel( 'Logged-out users', { exact: true } )
			).not.toBeChecked();
		} finally {
			await page.goto( '/wp-admin/options-general.php?page=zm_shbt_opt' );
			await page
				.getByLabel( 'Current user (content author)', { exact: true } )
				.check();
			await page
				.getByLabel( 'Other logged-in users', { exact: true } )
				.check();
			await page
				.getByLabel( 'Logged-out users', { exact: true } )
				.check();
			await saveSettings( page );
		}
	} );

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

async function saveSettings( page ) {
	const response = page.waitForResponse( ( candidate ) =>
		candidate.url().includes( '/wp-admin/admin-ajax.php' ) &&
		candidate.request().postData()?.includes( 'zm_sh_save_settings' )
	);
	await page.locator( '#submit' ).click();
	expect( ( await response ).ok() ).toBeTruthy();
	await expect(
		page.locator( '.components-snackbar__content' ).getByText(
			'Settings saved.',
			{ exact: true }
		)
	).toBeVisible();
}
