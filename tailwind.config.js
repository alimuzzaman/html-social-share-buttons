/** @type {import('tailwindcss').Config} */
module.exports = {
	content: [
		'./src/**/*.{js,jsx,ts,tsx}',
		'./templates/**/*.php',
		'./blocks/**/*.php',
		'./admin/**/*.php',
		'./*.php',
	],
	theme: {
		extend: {
			colors: {
				primary: '#007cba',
				'primary-700': '#005a87',
				muted: '#23282d',
				success: '#46b450',
				warning: '#ffb900',
				danger: '#dc3232',
			},
			fontFamily: {
				system: [
					'-apple-system',
					'BlinkMacSystemFont',
					'"Segoe UI"',
					'Roboto',
					'Oxygen-Sans',
					'Ubuntu',
					'Cantarell',
					'"Helvetica Neue"',
					'sans-serif',
				],
			},
		},
	},
	plugins: [],
};
