const { test, expect } = require( '@playwright/test' );
const {
	assertVisibleCanonicalShareButtons,
	builderStorage,
	login,
	storedElementorFixturePostId,
} = require( './helpers/wordpress' );

test.describe( 'Elementor integration', () => {
	test( 'lists Html Social Share in the Elementor widget panel', async ( {
		page,
	} ) => {
		const postId = storedElementorFixturePostId();
		test.skip(
			! postId,
			'Set ELEMENTOR_STORED_FIXTURE_POST_ID to a published page containing the stored zm_social_share widget fixture.'
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

	test( 'renders the real stored Elementor widget fixture with a canonical share URL', async ( {
		page,
	} ) => {
		const postId = storedElementorFixturePostId();
		test.skip(
			! postId,
			'Set ELEMENTOR_STORED_FIXTURE_POST_ID to a published page containing title="Stored title", iconset="flat", iconset_type="circle", and icons=["facebook","x"].'
		);

		await page.goto( `/?p=${ postId }` );
		const widget = page.locator(
			`[data-widget_type^="${ builderStorage.elementor.document_element.widgetType }"]`
		);
		await expect( widget ).toBeVisible();
		await expect( widget.locator( '.zmshbt.in_elementor' ) ).toBeVisible();
		await assertVisibleCanonicalShareButtons( page, {
			placement: 'in_elementor',
			iconset: builderStorage.elementor.settings.iconset,
			shape: builderStorage.elementor.settings.iconset_type,
		} );
		await expect(
			page.locator( '.zmshbt.in_elementor a.twitter' )
		).toBeVisible();
		await expect(
			page.locator( '.zmshbt.in_elementor' )
		).not.toContainText( builderStorage.elementor.settings.title );
	} );
} );
