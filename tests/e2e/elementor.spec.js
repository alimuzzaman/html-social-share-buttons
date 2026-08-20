const { test, expect } = require( '@playwright/test' );
const {
	assertVisibleCanonicalShareButtons,
	builderStorage,
	createStoredElementorFixture,
	createPublishedPage,
	login,
	saveElementorFixture,
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
		const previewFacebook = previewButtons.locator(
			'a.facebook:not(.zmshbt-profile-link)'
		);
		await expect( previewFacebook ).toBeVisible();
		await expect( previewFacebook ).toHaveCSS(
			'background-image',
			/url\(.+Facebook\.png.+\)/
		);
		await preview
			.locator(
				`[data-widget_type^="${ builderStorage.elementor.document_element.widgetType }"]`
			)
			.click();
		const iconsetControl = page.locator(
			'.elementor-control-iconset select'
		);
		await expect( iconsetControl ).toHaveValue( 'flat' );
		const legacyDefault = iconsetControl.locator(
			'option[value="default"]'
		);
		await expect( legacyDefault ).toHaveAttribute( 'hidden', '' );
		await expect( legacyDefault ).toBeDisabled();
	} );

	test( 'preserves an existing legacy Default selection in the Elementor editor', async ( {
		page,
	} ) => {
		await login( page );
		const fixture = await createPublishedPage( page, {
			content: '',
			title: 'HSSB legacy Elementor icon fixture',
		} );
		const element = JSON.parse(
			JSON.stringify( builderStorage.elementor.document_element )
		);
		element.id = 'd3f4a5b';
		element.settings.iconset = 'default';
		await saveElementorFixture( page, fixture, element );
		await page.goto(
			`/wp-admin/post.php?post=${ fixture.id }&action=elementor`
		);
		const search = page
			.locator(
				'.elementor-panel-search-input, input[placeholder*="Search" i]'
			)
			.first();
		await page.locator( '#elementor-loading' ).waitFor( {
			state: 'hidden',
			timeout: 15_000,
		} ).catch( () => {} );
		if ( ! ( await search.isVisible() ) ) {
			await page.getByRole( 'button', { name: 'Add Element' } ).click();
		}
		await expect( search ).toBeVisible();
		await search.fill( 'Html Social Share' );

		const preview = page.frameLocator( '#elementor-preview-iframe' );
		const widget = preview.locator(
			`[data-widget_type^="${ builderStorage.elementor.document_element.widgetType }"]`
		);
		await expect( widget ).toBeVisible();
		await widget.click();
		const iconsetControl = page.locator(
			'.elementor-control-iconset select'
		);
		await expect( iconsetControl ).toHaveValue( 'default' );
		const legacyDefault = iconsetControl.locator(
			'option[value="default"]'
		);
		await expect( legacyDefault ).not.toHaveAttribute( 'hidden', '' );
		await expect( legacyDefault ).toBeEnabled();
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
