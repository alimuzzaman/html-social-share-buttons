const { test, expect } = require( '@playwright/test' );
const {
	assertVisibleCanonicalShareButtons,
	builderStorage,
	createStoredElementorFixture,
	login,
} = require( './helpers/wordpress' );

test.describe( 'Elementor integration', () => {
	test.setTimeout( 60_000 );
	test( 'lists Html Social Share in the Elementor widget panel', async ( {
		page,
	} ) => {
		const diagnostics = [];
		page.on( 'pageerror', ( error ) => diagnostics.push( error.message ) );
		page.on( 'response', ( response ) => {
			if ( response.status() >= 400 ) {
				diagnostics.push( `${ response.status() } ${ response.url() }` );
			}
		} );
		await login( page );
		const fixture = await createStoredElementorFixture( page );
		await page.goto(
			`/wp-admin/post.php?post=${ fixture.id }&action=elementor`
		);

		const panel = page
			.locator( '#elementor-panel, .elementor-panel' )
			.first();
		const search = page
			.locator(
				'.elementor-panel-search-input, input[placeholder*="Search" i]'
			)
			.first();
		await page.locator( '#elementor-loading' ).waitFor( {
			state: 'hidden',
			timeout: 15_000,
		} ).catch( () => {} );
		const fatalDialog = page.locator( '#elementor-fatal-error-dialog' );
		if ( await fatalDialog.isVisible() ) {
			const frameUrls = page.frames().map( ( frame ) => frame.url() );
			throw new Error(
				`Elementor preview failed: ${ await fatalDialog.innerText() } frames=${ frameUrls.join( ', ' ) } ${ diagnostics.join( ' | ' ) }`
			);
		}
		if ( ! ( await search.isVisible() ) ) {
			await page.getByRole( 'button', { name: 'Add Element' } ).click();
		}
		await expect( search ).toBeVisible();
		await search.fill( 'Html Social Share' );
		await expect( panel ).toContainText( 'Html Social Share' );

		const preview = page.frameLocator( '#elementor-preview-iframe' );
		await expect(
			preview.locator(
				`[data-widget_type^="${ builderStorage.elementor.document_element.widgetType }"]`
			)
		).toBeVisible();
		const previewButtons = preview.locator( '.zmshbt.in_elementor' );
		await expect( previewButtons ).toBeVisible();
		const previewFacebook = previewButtons.locator( 'a.facebook' );
		await expect( previewFacebook ).toBeVisible();
		await expect( previewFacebook ).toHaveCSS(
			'background-image',
			/url\(.+Facebook\.png.+\)/
		);
	} );

	test( 'renders the real stored Elementor widget fixture with a canonical share URL', async ( {
		page,
	} ) => {
		await login( page );
		const fixture = await createStoredElementorFixture( page );
		await page.goto( fixture.link );
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
