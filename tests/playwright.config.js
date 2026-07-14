const { defineConfig } = require( '@playwright/test' );

module.exports = defineConfig( {
	testDir: './e2e',
	timeout: 30_000,
	workers: 1,
	use: {
		baseURL:
			process.env.SANDBOX_E2E_BASE_URL ||
			process.env.WP_BASE_URL ||
			'http://localhost:8221',
		channel: process.env.PLAYWRIGHT_CHANNEL || undefined,
		headless: true,
		ignoreHTTPSErrors: true,
	},
} );
