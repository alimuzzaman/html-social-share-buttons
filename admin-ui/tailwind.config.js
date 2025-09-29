/** @type {import('tailwindcss').Config} */
export default {
  content: [
    "./index.html",
    "./src/**/*.{js,ts,jsx,tsx}",
  ],
  theme: {
    extend: {
      colors: {
        // WordPress admin colors
        'wp-blue': {
          DEFAULT: '#0073aa',
          'dark': '#005177',
          'light': '#00a0d2',
          '50': '#eff9ff',
          '100': '#daf2ff',
          '200': '#bee9ff',
          '300': '#91ddff',
          '400': '#5dc7fd',
          '500': '#00a0d2',
          '600': '#0073aa',
          '700': '#005177',
          '800': '#003951',
          '900': '#002635'
        },
        'wp-gray': {
          DEFAULT: '#23282d',
          '50': '#f6f7f7',
          '100': '#e2e4e7',
          '200': '#c3c4c7',
          '300': '#a7aaad',
          '400': '#8c8f94',
          '500': '#646970',
          '600': '#50575e',
          '700': '#3c434a',
          '800': '#32373c',
          '900': '#23282d'
        },
        'wp-success': '#46b450',
        'wp-warning': '#ffb900',
        'wp-error': '#dc3232',
      },
      fontFamily: {
        'wp': ['-apple-system', 'BlinkMacSystemFont', '"Segoe UI"', 'Roboto', 'Oxygen-Sans', 'Ubuntu', 'Cantarell', '"Helvetica Neue"', 'sans-serif'],
      },
      spacing: {
        'wp-admin': '32px', // WordPress admin bar height
      },
      borderRadius: {
        'wp': '3px', // WordPress default border radius
      },
      boxShadow: {
        'wp': '0 1px 1px rgba(0,0,0,.04)',
        'wp-focus': '0 0 0 1px #5b9dd9, 0 0 2px 1px rgba(30, 140, 190, 0.8)',
      }
    },
  },
  plugins: [],
}