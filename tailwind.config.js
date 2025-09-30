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
        'wp-blue': '#007cba',
        'wp-blue-700': '#005a87',
        'wp-gray': '#23282d',
        'wp-success': '#46b450',
        'wp-warning': '#ffb900',
        'wp-error': '#dc3232',
      },
      fontFamily: {
        'wp': ['-apple-system', 'BlinkMacSystemFont', '"Segoe UI"', 'Roboto', 'Oxygen-Sans', 'Ubuntu', 'Cantarell', '"Helvetica Neue"', 'sans-serif'],
      },
    },
  },
  plugins: [],
}