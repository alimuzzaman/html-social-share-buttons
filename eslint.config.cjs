const wordpressConfig = require('@wordpress/scripts/config/eslint.config.cjs');

module.exports = [
	...wordpressConfig,
	{
		files: ['src/js/**/*.js'],
		languageOptions: {
			globals: {
				jQuery: 'readonly',
				wp: 'readonly',
			},
		},
		settings: {
			react: {
				version: '18.3',
			},
		},
		rules: {
			'no-var': 'off',
			'object-shorthand': 'off',
			'prettier/prettier': 'off',
			'@wordpress/no-global-get-selection': 'off',
		},
	},
];
