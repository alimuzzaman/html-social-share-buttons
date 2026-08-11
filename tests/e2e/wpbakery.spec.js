const { test, expect } = require( '@playwright/test' );
const {
	assertVisibleCanonicalShareButtons,
	builderStorage,
	createPublishedPage,
	login,
} = require( './helpers/wordpress' );

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

	test( 'renders the real stored WPBakery shortcode fixture with a canonical share URL', async ( {
		page,
	} ) => {
		await login( page );
		const fixture = await createPublishedPage( page, {
			content: builderStorage.wpbakery.shortcode,
			title: 'HSSB stored WPBakery fixture',
		} );

		await page.goto( fixture.link );
		await assertVisibleCanonicalShareButtons( page, {
			placement: 'in_shortcode',
			iconset: 'flat',
			shape: 'circle',
		} );
		await expect(
			page.getByRole( 'heading', { name: 'Stored title', exact: true } )
		).toBeVisible();
		await expect(
			page.locator( '.zmshbt.in_shortcode a.twitter' )
		).toBeVisible();
	} );
} );
