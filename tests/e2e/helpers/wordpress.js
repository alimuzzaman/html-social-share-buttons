const { expect } = require( '@playwright/test' );
const builderStorage = require( '../../fixtures/builder-storage-baseline.json' );

async function login( page ) {
	await page.goto( '/wp-login.php' );
	await page
		.locator( '#user_login' )
		.fill( process.env.WP_ADMIN_USER || 'admin' );
	await page
		.locator( '#user_pass' )
		.fill( process.env.WP_ADMIN_PASSWORD || 'admin' );
	await Promise.all( [
		page.waitForURL( /\/wp-admin\// ),
		page.locator( '#wp-submit' ).click(),
	] );
}

/**
 * Store content through WordPress's authenticated REST API. Using the admin
 * nonce means this exercises WordPress persistence, rather than rendering a
 * synthetic DOM fragment in the test.
 *
 * @param {import('@playwright/test').Page} page          Browser page.
 * @param {Object}                          root0         Fixture data.
 * @param {string}                          root0.content Stored post content.
 * @param {string}                          root0.title   Stored post title.
 */
async function createPublishedPage( page, { content, title } ) {
	await page.goto( '/wp-admin/post-new.php?post_type=page' );

	const result = await page.evaluate(
		async ( payload ) => {
			const settings = window.wpApiSettings;
			if ( ! settings || ! settings.root || ! settings.nonce ) {
				return {
					error: 'WordPress REST settings were not available in wp-admin.',
				};
			}

			const response = await fetch( `${ settings.root }wp/v2/pages`, {
				method: 'POST',
				headers: {
					'Content-Type': 'application/json',
					'X-WP-Nonce': settings.nonce,
				},
				body: JSON.stringify( {
					content: payload.content,
					status: 'publish',
					title: payload.title,
				} ),
			} );
			const body = await response.json();

			return response.ok
				? { id: body.id, link: body.link }
				: {
						error:
							body.message ||
							`WordPress returned ${ response.status }.`,
				  };
		},
		{ content, title }
	);

	if ( result.error ) {
		throw new Error(
			`Could not create the stored browser fixture: ${ result.error }`
		);
	}

	return result;
}

async function assertVisibleCanonicalShareButtons(
	page,
	{ placement, iconset, shape }
) {
	const wrapper = page.locator( `.zmshbt.${ placement }` ).first();
	await expect( wrapper ).toBeVisible();
	if ( iconset ) {
		await expect( wrapper ).toHaveClass(
			new RegExp( `\\b${ iconset }\\b` )
		);
	}
	if ( shape ) {
		await expect( wrapper ).toHaveClass( new RegExp( `\\b${ shape }\\b` ) );
	}

	const shareButton = wrapper.locator( 'a.facebook' );
	await expect( shareButton ).toBeVisible();
	const href = await shareButton.getAttribute( 'href' );
	expect( href ).toBeTruthy();

	const permalink = page.url();
	const encodedPermalink = encodeURIComponent( permalink );
	expect( href ).toContain( encodedPermalink );
	expect( href ).not.toContain( encodeURIComponent( encodedPermalink ) );
	expect( href ).not.toContain( '%%permalink%%' );
	expect( href ).not.toContain( '%25%25permalink%25%25' );
}

function storedElementorFixturePostId() {
	return (
		process.env.ELEMENTOR_STORED_FIXTURE_POST_ID ||
		process.env.ELEMENTOR_TEST_POST_ID
	);
}

module.exports = {
	assertVisibleCanonicalShareButtons,
	builderStorage,
	createPublishedPage,
	login,
	storedElementorFixturePostId,
};
