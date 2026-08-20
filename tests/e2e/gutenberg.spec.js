const { test, expect } = require( '@playwright/test' );
const {
	assertVisibleCanonicalShareButtons,
	builderStorage,
	createPublishedPage,
	login,
} = require( './helpers/wordpress' );

test.describe( 'Gutenberg integration', () => {
	test( 'inserts, edits, persists, and renders both API v3 blocks in the iframe editor', async ( {
		page,
	} ) => {
		test.setTimeout( 60_000 );
		const consoleErrors = [];
		const pageErrors = [];
		page.on( 'console', ( message ) => {
			if ( message.type() === 'error' ) {
				consoleErrors.push( message.text() );
			}
		} );
		page.on( 'pageerror', ( error ) => pageErrors.push( error.message ) );

		await login( page );
		const fixture = await createPublishedPage( page, {
			content: '',
			title: 'HSSB WordPress 7.1 iframe fixture',
		} );
		await page.goto(
			`/wp-admin/post.php?post=${ fixture.id }&action=edit`
		);

		const canvasFrame = page.locator( 'iframe[name="editor-canvas"]' );
		await expect( canvasFrame ).toBeVisible();
		await page.waitForFunction( () => {
			return (
				window.wp?.blocks?.getBlockType(
					'html-social-share/social-share'
				)?.apiVersion === 3 &&
				window.wp?.blocks?.getBlockType(
					'html-social-share/social-links'
				)?.apiVersion === 3
			);
		} );

		const inserterNames = await page.evaluate( () =>
			window.wp.data
				.select( 'core/block-editor' )
				.getInserterItems()
				.map( ( item ) => item.name )
		);
		expect( inserterNames ).toContain(
			'html-social-share/social-share'
		);
		expect( inserterNames ).toContain(
			'html-social-share/social-links'
		);

		const clientIds = await page.evaluate( () => {
			const { createBlock } = window.wp.blocks;
			const share = createBlock( 'html-social-share/social-share', {
				title: 'Initial share title',
				iconset: 'default',
				iconset_type: 'square',
				icons: [ 'facebook', 'x' ],
				profile_links_mode: 'none',
			} );
			const links = createBlock( 'html-social-share/social-links', {
				title: 'Initial links title',
				iconset: 'default',
				iconset_type: 'square',
				profile_links_mode: 'custom',
				profile_links: {
					facebook: 'https://example.com/hssb-profile',
				},
			} );
			window.wp.data
				.dispatch( 'core/block-editor' )
				.resetBlocks( [ share, links ] );

			return { share: share.clientId, links: links.clientId };
		} );

		const canvas = page.frameLocator( 'iframe[name="editor-canvas"]' );
		const sharePreview = canvas.locator(
			'[data-type="html-social-share/social-share"].hssb-block-preview'
		);
		const linksPreview = canvas.locator(
			'[data-type="html-social-share/social-links"].hssb-social-links-block-preview'
		);
		await expect( sharePreview ).toBeVisible();
		await expect( linksPreview ).toBeVisible();
		await expect( sharePreview ).toHaveClass(
			/\bwp-block-html-social-share-social-share\b/
		);
		await expect( linksPreview ).toHaveClass(
			/\bwp-block-html-social-share-social-links\b/
		);

		await selectBlockAndOpenInspector( page, clientIds.share );
		const sidebar = page.locator(
			'.interface-interface-skeleton__sidebar'
		);
		await sidebar
			.getByLabel( 'Title', { exact: true } )
			.fill( 'Persisted share title' );
		await sidebar
			.getByLabel( 'Icon set', { exact: true } )
			.selectOption( 'flat' );
		await sidebar
			.getByLabel( 'Button shape', { exact: true } )
			.selectOption( 'circle' );

		await selectBlockAndOpenInspector( page, clientIds.links );
		await sidebar
			.getByLabel( 'Title', { exact: true } )
			.fill( 'Persisted links title' );
		await sidebar
			.getByLabel( 'Icon set', { exact: true } )
			.selectOption( 'flat' );
		await sidebar
			.getByLabel( 'Button shape', { exact: true } )
			.selectOption( 'circle' );

		await page.evaluate( async () => {
			await window.wp.data.dispatch( 'core/editor' ).savePost();
		} );
		await page.waitForFunction( () =>
			! window.wp.data.select( 'core/editor' ).isSavingPost()
		);
		await page.reload();
		await expect( page.locator( 'iframe[name="editor-canvas"]' ) ).toBeVisible();
		await page.waitForFunction( () =>
			window.wp.data.select( 'core/block-editor' ).getBlocks().length === 2
		);

		const storedBlocks = await page.evaluate( () =>
			window.wp.data
				.select( 'core/block-editor' )
				.getBlocks()
				.map( ( block ) => ( {
					name: block.name,
					attributes: block.attributes,
				} ) )
		);
		expect( storedBlocks ).toEqual( [
			expect.objectContaining( {
				name: 'html-social-share/social-share',
				attributes: expect.objectContaining( {
					title: 'Persisted share title',
					iconset: 'flat',
					iconset_type: 'circle',
					icons: [ 'facebook', 'x' ],
				} ),
			} ),
			expect.objectContaining( {
				name: 'html-social-share/social-links',
				attributes: expect.objectContaining( {
					title: 'Persisted links title',
					iconset: 'flat',
					iconset_type: 'circle',
					profile_links: {
						facebook: 'https://example.com/hssb-profile',
					},
				} ),
			} ),
		] );
		await expect(
			page.getByRole( 'button', { name: /Attempt Block Recovery/i } )
		).toHaveCount( 0 );

		await page.goto( fixture.link );
		await expect(
			page.locator(
				'meta[name="generator"][content="WordPress 7.1"]'
			)
		).toHaveCount( 1 );
		const shareWrapper = page.locator( '.zmshbt.in_block' ).first();
		await expect( shareWrapper ).toBeVisible();
		await expect( shareWrapper ).toHaveClass( /\bflat\b/ );
		await expect( shareWrapper ).toHaveClass( /\bcircle\b/ );
		const facebookShare = shareWrapper.locator(
			'a.facebook:not(.zmshbt-profile-link)'
		);
		await expect( facebookShare ).toBeVisible();
		await expect( shareWrapper.locator( 'a.twitter' ) ).toBeVisible();
		const shareHref = await facebookShare.getAttribute( 'href' );
		expect( shareHref ).toContain( encodeURIComponent( fixture.link ) );
		expect( shareHref ).not.toContain( '%%permalink%%' );
		expect( shareHref ).not.toContain( '%25%25permalink%25%25' );
		await expect(
			page.locator(
				'.zmshbt.in_block a.zmshbt-profile-link[href="https://example.com/hssb-profile"]'
			)
		).toBeVisible();
		await expect(
			page.locator( 'link[href*="/iconset/flat/style.css"]' )
		).toHaveCount( 1 );
		const iconBackground = await facebookShare.evaluate( ( element ) =>
				window.getComputedStyle( element ).backgroundImage
			);
		expect( iconBackground ).not.toBe( 'none' );

		expect( consoleErrors ).toEqual( [] );
		expect( pageErrors ).toEqual( [] );
	} );

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

async function selectBlockAndOpenInspector( page, clientId ) {
	await page.evaluate( ( id ) => {
		window.wp.data.dispatch( 'core/block-editor' ).selectBlock( id );
		const editor = window.wp.data.dispatch( 'core/edit-post' );
		if ( editor && typeof editor.openGeneralSidebar === 'function' ) {
			editor.openGeneralSidebar( 'edit-post/block' );
		}
	}, clientId );
}
