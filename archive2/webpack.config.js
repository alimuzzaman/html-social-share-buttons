/**
 * WordPress scripts configuration for multiple entry points
 */
const defaultConfig = require( '@wordpress/scripts/config/webpack.config' );

module.exports = {
	...defaultConfig,
	entry: {
		// Main block entry point
		index: './src/index.js',
		// Admin UI entry point
		admin: './src/admin.js',
		// Frontend scripts entry point
		frontend: './src/frontend.js',
	},
};
