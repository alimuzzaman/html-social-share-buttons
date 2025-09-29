module.exports = {
  semi: true,
  trailingComma: 'es5',
  singleQuote: true,
  printWidth: 100,
  tabWidth: 2,
  useTabs: false,
  bracketSpacing: true,
  arrowParens: 'avoid',
  endOfLine: 'lf',
  overrides: [
    {
      files: '*.{js,jsx,ts,tsx}',
      options: {
        singleQuote: true,
      },
    },
    {
      files: '*.{css,scss,sass}',
      options: {
        singleQuote: false,
      },
    },
  ],
};