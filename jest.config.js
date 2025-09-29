module.exports = {
  // Test environment
  testEnvironment: 'jsdom',

  // Setup files
  setupFilesAfterEnv: ['<rootDir>/tests/jest.setup.js'],

  // Module paths and aliases
  moduleNameMapping: {
    '^@/(.*)$': '<rootDir>/admin-ui/src/$1',
    '^@components/(.*)$': '<rootDir>/admin-ui/src/components/$1',
    '^@utils/(.*)$': '<rootDir>/admin-ui/src/utils/$1',
    '^@types/(.*)$': '<rootDir>/admin-ui/src/types/$1',
    '^@contexts/(.*)$': '<rootDir>/admin-ui/src/contexts/$1',
    '\\.(css|less|scss|sass)$': 'identity-obj-proxy',
  },

  // Test file patterns
  testMatch: [
    '<rootDir>/tests/js/**/*.test.{js,jsx,ts,tsx}',
    '<rootDir>/admin-ui/src/**/__tests__/**/*.{js,jsx,ts,tsx}',
    '<rootDir>/admin-ui/src/**/*.{test,spec}.{js,jsx,ts,tsx}',
  ],

  // Coverage configuration
  collectCoverageFrom: [
    'admin-ui/src/**/*.{js,jsx,ts,tsx}',
    'blocks/**/*.js',
    '!admin-ui/src/**/*.d.ts',
    '!admin-ui/src/index.tsx',
    '!**/node_modules/**',
    '!**/vendor/**',
  ],

  // Coverage thresholds
  coverageThreshold: {
    global: {
      branches: 80,
      functions: 80,
      lines: 80,
      statements: 80,
    },
  },

  // Transform configuration
  transform: {
    '^.+\\.(js|jsx|ts|tsx)$': ['babel-jest', {
      presets: [
        ['@babel/preset-env', { targets: { node: 'current' } }],
        ['@babel/preset-react', { runtime: 'automatic' }],
        '@babel/preset-typescript',
      ],
    }],
  },

  // Module file extensions
  moduleFileExtensions: ['js', 'jsx', 'ts', 'tsx', 'json'],

  // Test environment options
  testEnvironmentOptions: {
    html: '<html lang="en"><body><div id="root"></div></body></html>',
    url: 'http://localhost:3000',
    userAgent: 'node.js',
  },

  // Clear mocks between tests
  clearMocks: true,

  // Verbose output
  verbose: true,

  // Watch plugins
  watchPlugins: [
    'jest-watch-typeahead/filename',
    'jest-watch-typeahead/testname',
  ],

  // Globals for WordPress
  globals: {
    wp: {
      element: {},
      i18n: {
        __: jest.fn((text) => text),
        _x: jest.fn((text) => text),
        _n: jest.fn((single) => single),
      },
      data: {},
      blocks: {},
      components: {},
      blockEditor: {},
      apiFetch: jest.fn(),
    },
    wpApiSettings: {
      root: 'http://localhost/wp-json/',
      nonce: 'test-nonce',
    },
    htmlSocialShare: {
      apiUrl: 'http://localhost/wp-json/html-social-share/v1/',
      nonce: 'test-nonce',
      ajaxUrl: 'http://localhost/wp-admin/admin-ajax.php',
    },
  },
};