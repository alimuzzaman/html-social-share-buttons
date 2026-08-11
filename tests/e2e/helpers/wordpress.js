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

			/*
			 * Sandbox can expose wpApiSettings.root through a different host alias
			 * than the page Playwright is currently controlling. Keep the request
			 * same-origin while retaining WordPress's authenticated REST nonce.
			 */
			const response = await fetch( `${ window.location.origin }/wp-json/wp/v2/pages`, {
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

/**
 * Persist the fixture through Elementor's real editor save action. Elementor
 * owns the _elementor_data and _elementor_edit_mode records; this test helper
 * deliberately does not write those post-meta values itself.
 *
 * @param {import('@playwright/test').Page} page       Browser page.
 * @param {Object}                          fixture    Fresh WordPress page.
 * @param {Object}                          element    Stored Elementor element.
 * @return {Promise<Object>} The published fixture page.
 */
async function saveElementorFixture( page, fixture, element ) {
	await page.goto(
		`/wp-admin/post.php?post=${ fixture.id }&action=elementor`
	);

	const panel = page.locator( '#elementor-panel, .elementor-panel' ).first();
	await expect( panel ).toBeVisible();

	const result = await page.evaluate(
		async ( payload ) => {
			if (
				! window.elementorCommon ||
				! window.elementorCommon.ajax ||
				'function' !==
					typeof window.elementorCommon.ajax.addRequest
			) {
				return {
					error: 'Elementor editor AJAX is not available on this page.',
				};
			}

			try {
				const response = await window.elementorCommon.ajax.addRequest(
					'save_builder',
					{
						data: {
							elements: [ payload.element ],
							settings: {
								post_status: 'publish',
								title: payload.title,
							},
							status: 'publish',
						},
					}
				);

				return { response };
			} catch ( error ) {
				const details =
					error && error.responseJSON
						? error.responseJSON
						: error && error.data
							? error.data
							: error;
				return {
					error:
						details && details.message
							? details.message
							: JSON.stringify( details ),
				};
			}
		},
		{ element, title: fixture.title }
	);

	if ( result.error ) {
		throw new Error(
			`Could not save the Elementor browser fixture: ${ result.error }`
		);
	}

	return fixture;
}

/**
 * Create and persist a public Elementor document with the actual Elementor
 * editor. An explicit fixture post ID remains supported for external
 * environments where a persistent fixture is preferred.
 *
 * @param {import('@playwright/test').Page} page Browser page.
 * @return {Promise<Object>} Fixture post ID and public link.
 */
async function createStoredElementorFixture( page ) {
	const postId = storedElementorFixturePostId();
	if ( postId ) {
		return {
			id: postId,
			link: `/?p=${ postId }`,
		};
	}

	const fixture = await createPublishedPage( page, {
		content: '',
		title: 'HSSB stored Elementor fixture',
	} );

	return saveElementorFixture(
		page,
		fixture,
		builderStorage.elementor.document_element
	);
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
	createStoredElementorFixture,
	login,
	saveElementorFixture,
	storedElementorFixturePostId,
};
