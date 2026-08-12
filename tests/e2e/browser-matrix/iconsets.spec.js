const fs = require( 'fs' );
const path = require( 'path' );
const { test, expect } = require( '@playwright/test' );
const { createPublishedPage, login } = require( '../helpers/wordpress' );

const cells = [
	{ id: 'default-square', iconset: 'default', shape: 'square', links: 7 },
	{ id: 'flat-square', iconset: 'flat', shape: 'square', links: 7 },
	{ id: 'flat-circle', iconset: 'flat', shape: 'circle', links: 7 },
	{ id: 'long-shadows-square', iconset: 'long-shadows', shape: 'square', links: 7 },
	{ id: 'long-shadows-circle', iconset: 'long-shadows', shape: 'circle', links: 7 },
	{ id: 'prajin-square', iconset: 'prajin', shape: 'square', links: 6 },
	{ id: 'prajin-circle', iconset: 'prajin', shape: 'circle', links: 6 },
	{ id: 'bootstrap-solid-square', iconset: 'bootstrap-solid', shape: 'square', links: 7 },
	{ id: 'bootstrap-solid-circle', iconset: 'bootstrap-solid', shape: 'circle', links: 7 },
	{ id: 'tabler-outline-square', iconset: 'tabler-outline', shape: 'square', links: 7 },
	{ id: 'tabler-outline-circle', iconset: 'tabler-outline', shape: 'circle', links: 7 },
];

const networks = 'facebook,x,linkedin,pinterest,telegram,bluesky,mail';

function fixtureContent() {
	return cells
		.map(
			( cell ) =>
				`<section class="hssb-browser-matrix-cell" data-hssb-cell="${ cell.id }"><h2>${ cell.id }</h2>[zm_sh_btn title="${ cell.id }" iconset="${ cell.iconset }" iconset_type="${ cell.shape }" icons="${ networks }"]</section>`
		)
		.join( '\n' );
}

function screenshotPath( testInfo ) {
	const configuredRoot = process.env.HSSB_BROWSER_ARTIFACT_DIR;
	const filename = `${ testInfo.project.name }.png`;

	if ( ! configuredRoot ) {
		return testInfo.outputPath( filename );
	}

	const output = path.resolve( process.cwd(), configuredRoot, filename );
	fs.mkdirSync( path.dirname( output ), { recursive: true } );
	return output;
}

test.describe( 'Icon-set browser matrix', () => {
	test( 'renders every declared icon-set and shape cell', async ( {
		page,
	}, testInfo ) => {
		await login( page );
		const fixture = await createPublishedPage( page, {
			content: fixtureContent(),
			title: `HSSB browser matrix ${ testInfo.project.name }`,
		} );

		await page.goto( fixture.link );

		for ( const cell of cells ) {
			const section = page.locator(
				`[data-hssb-cell="${ cell.id }"]`
			);
			const wrapper = section.locator( '.zmshbt.in_shortcode' );
			await expect( section ).toBeVisible();
			await expect( wrapper ).toBeVisible();
			await expect( wrapper ).toHaveClass(
				new RegExp( `\\b${ cell.iconset }\\b` )
			);
			await expect( wrapper ).toHaveClass(
				new RegExp( `\\b${ cell.shape }\\b` )
			);
			await expect( wrapper.locator( 'a' ) ).toHaveCount( cell.links );
			await expect( wrapper.locator( 'a.facebook' ) ).toHaveCSS(
				'background-image',
				/url\(.+\)/
			);
		}

		if ( testInfo.project.name.endsWith( 'mobile-viewport' ) ) {
			const pageHeading = page.locator( 'main h1' ).first();
			const rails = page.locator( '.zmshbt.left, .zmshbt.right' );
			await expect( pageHeading ).toBeVisible();
			expect( await rails.count() ).toBeGreaterThan( 0 );

			const headingBox = await pageHeading.boundingBox();
			for ( const rail of await rails.all() ) {
				await expect( rail ).toHaveCSS( 'position', 'static' );
				const railBox = await rail.boundingBox();
				expect( headingBox ).not.toBeNull();
				expect( railBox ).not.toBeNull();
				if ( ! headingBox || ! railBox ) {
					continue;
				}
				expect(
					railBox.x < headingBox.x + headingBox.width &&
						railBox.x + railBox.width > headingBox.x &&
						railBox.y < headingBox.y + headingBox.height &&
						railBox.y + railBox.height > headingBox.y
				).toBeFalsy();
			}
		}

		const output = screenshotPath( testInfo );
		await page.screenshot( { path: output, fullPage: true } );
		await testInfo.attach( 'icon-set-matrix', {
			path: output,
			contentType: 'image/png',
		} );
	} );
} );
