const { defineConfig } = require( '@playwright/test' );

module.exports = defineConfig( {
	testDir: './e2e',
	testMatch: [ 'settings.spec.js', '*preview*.spec.js', 'browser-matrix/iconsets.spec.js', 'gutenberg.spec.js' ],
	timeout: 120_000,
	workers: 1,
	fullyParallel: false,
	use: {
		baseURL: process.env.WP_BASE_URL,
		headless: true,
		trace: 'retain-on-failure',
	},
	projects: [ 'chromium', 'firefox', 'webkit' ].flatMap( ( browserName ) => [
		{
			name: `${ browserName }-desktop`,
			use: { browserName, viewport: { width: 1440, height: 1024 } },
		},
		{
			name: `${ browserName }-mobile-viewport`,
			use: { browserName, viewport: { width: 390, height: 844 } },
		},
	] ),
} );
