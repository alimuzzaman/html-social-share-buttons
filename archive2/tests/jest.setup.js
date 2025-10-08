require('@testing-library/jest-dom');
const { TextEncoder, TextDecoder } = require('util');

// Polyfills for jsdom
global.TextEncoder = TextEncoder;
global.TextDecoder = TextDecoder;

// Mock WordPress globals
global.wp = {
  element: {
    createElement: jest.fn(),
    Fragment: 'Fragment',
    Component: class MockComponent {},
    useState: jest.fn(),
    useEffect: jest.fn(),
    useContext: jest.fn(),
  },
  i18n: {
    __: jest.fn((text) => text),
    _x: jest.fn((text, context) => text),
    _n: jest.fn((single, plural, number) => number === 1 ? single : plural),
    sprintf: jest.fn((format, ...args) => {
      let i = 0;
      return format.replace(/%s/g, () => args[i++] || '');
    }),
  },
  data: {
    useSelect: jest.fn(),
    useDispatch: jest.fn(),
    select: jest.fn(),
    dispatch: jest.fn(),
  },
  blocks: {
    registerBlockType: jest.fn(),
    createBlock: jest.fn(),
  },
  components: {
    Panel: 'div',
    PanelBody: 'div',
    PanelRow: 'div',
    TextControl: 'input',
    SelectControl: 'select',
    CheckboxControl: 'input',
    ColorPicker: 'input',
    Button: 'button',
  },
  blockEditor: {
    useBlockProps: jest.fn(() => ({})),
    InspectorControls: 'div',
    BlockControls: 'div',
  },
  apiFetch: jest.fn(() => Promise.resolve({})),
};

// Mock WordPress API settings
global.wpApiSettings = {
  root: 'http://localhost/wp-json/',
  nonce: 'test-nonce',
  versionString: 'wp/v2/',
};

// Mock plugin globals
global.htmlSocialShare = {
  apiUrl: 'http://localhost/wp-json/html-social-share/v1/',
  nonce: 'test-nonce',
  ajaxUrl: 'http://localhost/wp-admin/admin-ajax.php',
  adminUrl: 'http://localhost/wp-admin/',
  pluginUrl: 'http://localhost/wp-content/plugins/html-social-share/',
  networks: {
    facebook: { name: 'Facebook', icon: 'fab fa-facebook-f' },
    twitter: { name: 'Twitter', icon: 'fab fa-twitter' },
    linkedin: { name: 'LinkedIn', icon: 'fab fa-linkedin-in' },
  },
};

// Mock console methods in tests
global.console = {
  ...console,
  warn: jest.fn(),
  error: jest.fn(),
  log: jest.fn(),
};

// Mock fetch for API calls
global.fetch = jest.fn(() =>
  Promise.resolve({
    ok: true,
    status: 200,
    json: () => Promise.resolve({}),
    text: () => Promise.resolve(''),
  })
);

// Mock ResizeObserver
global.ResizeObserver = jest.fn().mockImplementation(() => ({
  observe: jest.fn(),
  unobserve: jest.fn(),
  disconnect: jest.fn(),
}));

// Mock IntersectionObserver
global.IntersectionObserver = jest.fn().mockImplementation(() => ({
  observe: jest.fn(),
  unobserve: jest.fn(),
  disconnect: jest.fn(),
}));

// Mock matchMedia
Object.defineProperty(window, 'matchMedia', {
  writable: true,
  value: jest.fn().mockImplementation(query => ({
    matches: false,
    media: query,
    onchange: null,
    addListener: jest.fn(), // deprecated
    removeListener: jest.fn(), // deprecated
    addEventListener: jest.fn(),
    removeEventListener: jest.fn(),
    dispatchEvent: jest.fn(),
  })),
});

// Mock scrollTo
Object.defineProperty(window, 'scrollTo', {
  writable: true,
  value: jest.fn(),
});

// Clean up after each test
afterEach(() => {
  jest.clearAllMocks();
});