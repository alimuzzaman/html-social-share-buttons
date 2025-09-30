const { chromium } = require( 'playwright' );

// Configurable via environment variables
const FRONTEND_URL = process.env.WP_FRONTEND_URL || 'https://example.test/';
const SHARE_SELECTOR =
	process.env.SHARE_SELECTOR ||
	'.hss-share-buttons, .html-social-share-buttons, .hss-share';

async function testFrontendShareButtons() {
	console.log( 'Starting HTML Social Share Frontend Test...' );

	const browser = await chromium.launch( {
		headless: true,
		args: [ '--no-sandbox', '--disable-setuid-sandbox' ],
	} );
	const context = await browser.newContext();
	const page = await context.newPage();

	page.on( 'console', ( msg ) => console.log( 'PAGE LOG:', msg.text() ) );

	try {
		console.log( 'Navigating to frontend homepage:', FRONTEND_URL );
		await page.goto( FRONTEND_URL, { waitUntil: 'networkidle' } );

		await page.waitForTimeout( 1000 ); // give scripts a moment to initialize

		console.log(
			'Checking for share buttons using selector:',
			SHARE_SELECTOR
		);
		const buttons = await page.$$( SHARE_SELECTOR );

		if ( ! buttons || buttons.length === 0 ) {
			console.warn(
				'No share button elements found. Selector may be incorrect or plugin not active on the homepage.'
			);
		} else {
			console.log( `Found ${ buttons.length } share button element(s).` );

			// Take a screenshot to aid debugging/visual verification
			await page.screenshot( { path: 'frontend-share-buttons.png' } );
			console.log( 'Screenshot saved: frontend-share-buttons.png' );

			// Try clicking the first button's first network link, if present
			try {
				const firstButton = buttons[ 0 ];
				// Find an anchor inside the share button block
				const firstAnchor = await firstButton.$( 'a[href]' );
				if ( firstAnchor ) {
					console.log(
						'Found anchor inside share buttons, attempting click that opens share dialog'
					);
					// Open in new tab / intercept popup. We will simulate click with modifier to avoid popup blocking
					await Promise.all( [
						page.waitForTimeout( 500 ),
						firstAnchor.click( { button: 'left' } ),
					] );
					console.log( 'Clicked anchor inside share button block' );
				} else {
					console.log(
						'No anchor found inside first share button block'
					);
				}
			} catch ( e ) {
				console.log(
					'Error while interacting with share buttons:',
					e.message
				);
			}
		}

		console.log( 'Frontend test completed successfully' );
	} catch ( err ) {
		console.error( 'Frontend test failed:', err );
		await page.screenshot( { path: 'error-frontend.png' } );
		console.log( 'Error screenshot saved: error-frontend.png' );
	} finally {
		await browser.close();
	}
}

if ( require.main === module ) {
	testFrontendShareButtons().catch( ( err ) => {
		console.error( err );
		process.exit( 1 );
	} );
}

module.exports = testFrontendShareButtons;
