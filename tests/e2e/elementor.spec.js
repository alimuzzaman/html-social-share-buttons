const { test, expect } = require( '@playwright/test' );

async function login( page ) {
	await page.goto( '/wp-login.php' );
	await page
		.locator( '#user_login' )
		.fill( process.env.WP_ADMIN_USER || 'admin' );
	await page
		.locator( '#user_pass' )
		.fill( process.env.WP_ADMIN_PASSWORD || 'admin' );
	await page.locator( '#wp-submit' ).click();
	await expect( page ).not.toHaveURL( /wp-login\.php/ );
}

test.describe( 'Elementor integration', () => {
	test( 'lists Html Social Share in the Elementor widget panel', async ( {
		page,
	} ) => {
		const postId = process.env.ELEMENTOR_TEST_POST_ID;
		test.skip(
			! postId,
			'Set ELEMENTOR_TEST_POST_ID to an Elementor page for editor verification.'
		);

		await login( page );
		await page.goto(
			`/wp-admin/post.php?post=${ postId }&action=elementor`
		);

		const panel = page
			.locator( '#elementor-panel, .elementor-panel' )
			.first();
		if ( ( await panel.count() ) === 0 ) {
			test.skip(
				true,
				'Elementor is not installed or the supplied page is not Elementor-editable.'
			);
		}

		const search = page
			.locator(
				'.elementor-panel-search-input, input[placeholder*="Search" i]'
			)
			.first();
		await search.fill( 'Html Social Share' );
		await expect( panel ).toContainText( 'Html Social Share' );
	} );

	test( 'renders the Elementor share widget on the frontend', async ( {
		page,
	} ) => {
		const postId = process.env.ELEMENTOR_TEST_POST_ID;
		test.skip(
			! postId,
			'Set ELEMENTOR_TEST_POST_ID to a published Elementor page for frontend verification.'
		);

		await page.goto( `/?p=${ postId }` );
		await expect(
			page.locator(
				'.in_elementor, .zm_sh_btn, .zm-social-share, [class*="zm_sh"]'
			)
		).toBeVisible();
	} );
} );
