const { defineConfig } = require( '@playwright/test' );

const baseURL =
	process.env.SANDBOX_E2E_BASE_URL ||
	process.env.WP_BASE_URL ||
	'http://localhost:8221';

const desktop = { width: 1440, height: 1024 };
const mobile = { width: 390, height: 844 };

/**
 * A deliberately separate configuration for release-evidence capture. The
 * ordinary Sandbox E2E suite remains Chromium-first; this matrix uses real
 * installed engines and records a viewport-based mobile pass, not an emulated
 * device certification.
 */
module.exports = defineConfig( {
	testDir: './e2e/browser-matrix',
	timeout: 90_000,
	workers: 1,
	fullyParallel: false,
	use: {
		baseURL,
		headless: true,
		ignoreHTTPSErrors: true,
	},
	projects: [
		{
			name: 'chrome-desktop',
			use: { browserName: 'chromium', channel: 'chrome', viewport: desktop },
		},
		{
			name: 'chrome-mobile-viewport',
			use: { browserName: 'chromium', channel: 'chrome', viewport: mobile },
		},
		{
			name: 'firefox-desktop',
			use: { browserName: 'firefox', viewport: desktop },
		},
		{
			name: 'firefox-mobile-viewport',
			use: { browserName: 'firefox', viewport: mobile },
		},
		{
			name: 'edge-desktop',
			use: { browserName: 'chromium', channel: 'msedge', viewport: desktop },
		},
		{
			name: 'edge-mobile-viewport',
			use: { browserName: 'chromium', channel: 'msedge', viewport: mobile },
		},
		{
			name: 'webkit-desktop',
			use: { browserName: 'webkit', viewport: desktop },
		},
		{
			name: 'webkit-mobile-viewport',
			use: { browserName: 'webkit', viewport: mobile },
		},
	],
} );
