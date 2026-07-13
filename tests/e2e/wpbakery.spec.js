const { test, expect } = require( '@playwright/test' );

async function login( page ) {
	await page.goto( '/wp-login.php' );
	await page
		.locator( '#user_login' )
		.fill( process.env.WP_ADMIN_USER || 'admin' );
	await page
		.locator( '#user_pass' )
		.fill( process.env.WP_ADMIN_PASSWORD || 'password' );
	await page.locator( '#wp-submit' ).click();
	await expect( page ).not.toHaveURL( /wp-login\.php/ );
}

test.describe( 'WPBakery integration', () => {
	test( 'registers Html Social Share in the WPBakery element picker', async ( {
		page,
	} ) => {
		await login( page );
		await page.goto( '/wp-admin/post-new.php?post_type=page' );

		const wpbakery = page
			.locator(
				'.vc_navbar, #wpb_visual_composer, .vc_add-element-button'
			)
			.first();
		if ( ( await wpbakery.count() ) === 0 ) {
			test.skip(
				true,
				'WPBakery Page Builder is not installed in this Sandbox instance.'
			);
		}

		await page.locator( '.vc_add-element-button' ).first().click();
		await expect( page.locator( '.vc_ui-panel-window' ) ).toContainText(
			'Html Social Share'
		);
	} );

	test( 'renders the configured WPBakery share element on the frontend', async ( {
		page,
	} ) => {
		const postId = process.env.WPBAKERY_TEST_POST_ID;
		test.skip(
			! postId,
			'Set WPBAKERY_TEST_POST_ID to a published WPBakery page for frontend verification.'
		);

		await page.goto( `/?p=${ postId }` );
		await expect(
			page.locator( '.zm_sh_btn, .zm-social-share, [class*="zm_sh"]' )
		).toBeVisible();
	} );
} );
