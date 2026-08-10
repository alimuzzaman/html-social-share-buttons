const { test, expect } = require( '@playwright/test' );
const {
	assertVisibleCanonicalShareButtons,
	builderStorage,
	createPublishedPage,
	login,
} = require( './helpers/wordpress' );

test.describe( 'Gutenberg integration', () => {
	test( 'renders the real stored block fixture with a canonical share URL', async ( {
		page,
	} ) => {
		await login( page );
		const fixture = await createPublishedPage( page, {
			content: builderStorage.block.serialized,
			title: 'HSSB stored Gutenberg fixture',
		} );

		await page.goto( fixture.link );
		await assertVisibleCanonicalShareButtons( page, {
			placement: 'in_block',
		} );
		await expect(
			page.locator( '.zmshbt.in_block a.twitter' )
		).toBeVisible();
		await expect( page.locator( '.zmshbt.in_block' ) ).not.toContainText(
			'Stored title'
		);
	} );
} );
