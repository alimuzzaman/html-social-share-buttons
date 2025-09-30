import { test, expect } from '@playwright/test';

test( 'share buttons render on post', async ( { page } ) => {
	// Navigate to the test post created by the blueprint
	await page.goto( '/?p=1' ); // Assuming the post ID is 1

	// Check if share buttons are present
	const shareButtons = page.locator( '.hss-share-buttons' );
	await expect( shareButtons ).toBeVisible();

	// Check for specific networks, e.g., Facebook
	const facebookButton = shareButtons.locator( '[data-network="facebook"]' );
	await expect( facebookButton ).toBeVisible();
} );
