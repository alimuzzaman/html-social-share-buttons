import { defineConfig } from 'vite'
import react from '@vitejs/plugin-react'
import path from 'path'

// https://vitejs.dev/config/
export default defineConfig({
  plugins: [react()],
  build: {
    outDir: '../assets/js/admin-ui',
    emptyOutDir: true,
    lib: {
      entry: path.resolve(__dirname, 'src/main.tsx'),
      name: 'HtmlSocialShareAdmin',
      fileName: (format) => `admin-interface.${format}.js`
    },
    rollupOptions: {
      external: [
        'react',
        'react-dom',
        '@wordpress/api-fetch',
        '@wordpress/components',
        '@wordpress/element',
        '@wordpress/i18n',
        '@wordpress/icons'
      ],
      output: {
        globals: {
          'react': 'React',
          'react-dom': 'ReactDOM',
          '@wordpress/api-fetch': 'wp.apiFetch',
          '@wordpress/components': 'wp.components',
          '@wordpress/element': 'wp.element',
          '@wordpress/i18n': 'wp.i18n',
          '@wordpress/icons': 'wp.icons'
        }
      }
    }
  },
  resolve: {
    alias: {
      '@': path.resolve(__dirname, './src'),
    },
  },
})