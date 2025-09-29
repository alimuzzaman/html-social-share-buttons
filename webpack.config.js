const path = require('path');
const MiniCssExtractPlugin = require('mini-css-extract-plugin');
const CssMinimizerPlugin = require('css-minimizer-webpack-plugin');
const TerserPlugin = require('terser-webpack-plugin');
const { CleanWebpackPlugin } = require('clean-webpack-plugin');
const BundleAnalyzerPlugin = require('webpack-bundle-analyzer').BundleAnalyzerPlugin;

const isProduction = process.env.NODE_ENV === 'production';
const isDevelopment = !isProduction;

module.exports = {
  mode: isProduction ? 'production' : 'development',

  entry: {
    // Admin UI entry point
    'admin-ui': './admin-ui/src/index.tsx',

    // Gutenberg block entry point
    'blocks/html-social-share/index': './blocks/html-social-share/index.js',

    // Frontend scripts
    'frontend': './assets/js/frontend.js',
  },

  output: {
    path: path.resolve(__dirname, 'dist'),
    filename: '[name].js',
    chunkFilename: '[name].[contenthash].js',
    clean: true,
  },

  resolve: {
    extensions: ['.tsx', '.ts', '.js', '.jsx'],
    alias: {
      '@': path.resolve(__dirname, 'admin-ui/src'),
      '@components': path.resolve(__dirname, 'admin-ui/src/components'),
      '@utils': path.resolve(__dirname, 'admin-ui/src/utils'),
      '@types': path.resolve(__dirname, 'admin-ui/src/types'),
      '@contexts': path.resolve(__dirname, 'admin-ui/src/contexts'),
    },
  },

  module: {
    rules: [
      // TypeScript and JSX
      {
        test: /\.(ts|tsx|js|jsx)$/,
        exclude: /node_modules/,
        use: {
          loader: 'babel-loader',
          options: {
            presets: [
              ['@babel/preset-env', {
                targets: {
                  browsers: ['last 2 versions', 'ie >= 11']
                },
                modules: false,
              }],
              '@babel/preset-react',
              '@babel/preset-typescript',
            ],
            plugins: [
              '@babel/plugin-proposal-class-properties',
              '@babel/plugin-transform-runtime',
              ...(isDevelopment ? ['react-refresh/babel'] : []),
            ],
          },
        },
      },

      // CSS and SCSS
      {
        test: /\.(css|scss|sass)$/,
        use: [
          isProduction ? MiniCssExtractPlugin.loader : 'style-loader',
          {
            loader: 'css-loader',
            options: {
              sourceMap: isDevelopment,
              importLoaders: 2,
            },
          },
          {
            loader: 'postcss-loader',
            options: {
              sourceMap: isDevelopment,
              postcssOptions: {
                plugins: [
                  ['autoprefixer'],
                  ...(isProduction ? [['cssnano', { preset: 'default' }]] : []),
                ],
              },
            },
          },
          {
            loader: 'sass-loader',
            options: {
              sourceMap: isDevelopment,
            },
          },
        ],
      },

      // Images and fonts
      {
        test: /\.(png|jpe?g|gif|svg|woff|woff2|eot|ttf|otf)$/,
        type: 'asset/resource',
        generator: {
          filename: 'assets/[hash][ext][query]',
        },
      },

      // WordPress specific externals
      {
        test: /\.js$/,
        include: path.resolve(__dirname, 'blocks'),
        use: {
          loader: 'babel-loader',
          options: {
            presets: [
              ['@babel/preset-env', {
                targets: {
                  browsers: ['last 2 versions']
                }
              }],
              ['@babel/preset-react', {
                pragma: 'wp.element.createElement',
                pragmaFrag: 'wp.element.Fragment',
              }]
            ],
          },
        },
      },
    ],
  },

  plugins: [
    new CleanWebpackPlugin(),

    new MiniCssExtractPlugin({
      filename: '[name].css',
      chunkFilename: '[name].[contenthash].css',
    }),

    // Bundle analyzer (only in development)
    ...(process.env.ANALYZE ? [new BundleAnalyzerPlugin()] : []),
  ],

  optimization: {
    minimize: isProduction,
    minimizer: [
      new TerserPlugin({
        terserOptions: {
          compress: {
            drop_console: isProduction,
          },
          format: {
            comments: false,
          },
        },
        extractComments: false,
      }),
      new CssMinimizerPlugin(),
    ],

    splitChunks: {
      chunks: 'all',
      cacheGroups: {
        // Vendor libraries
        vendor: {
          test: /[\\/]node_modules[\\/]/,
          name: 'vendors',
          priority: 10,
          chunks: 'all',
        },

        // React-specific vendor chunk
        react: {
          test: /[\\/]node_modules[\\/](react|react-dom)[\\/]/,
          name: 'react',
          priority: 20,
          chunks: 'all',
        },

        // WordPress-specific chunk
        wordpress: {
          test: /[\\/]@wordpress[\\/]/,
          name: 'wordpress',
          priority: 15,
          chunks: 'all',
        },

        // Common components
        common: {
          name: 'common',
          minChunks: 2,
          priority: 5,
          chunks: 'all',
          enforce: true,
        },
      },
    },
  },

  // WordPress externals
  externals: {
    // WordPress globals
    '@wordpress/blocks': ['wp', 'blocks'],
    '@wordpress/i18n': ['wp', 'i18n'],
    '@wordpress/element': ['wp', 'element'],
    '@wordpress/components': ['wp', 'components'],
    '@wordpress/data': ['wp', 'data'],
    '@wordpress/block-editor': ['wp', 'blockEditor'],
    '@wordpress/api-fetch': ['wp', 'apiFetch'],
    '@wordpress/html-entities': ['wp', 'htmlEntities'],
    '@wordpress/compose': ['wp', 'compose'],
    '@wordpress/hooks': ['wp', 'hooks'],
    '@wordpress/url': ['wp', 'url'],
    '@wordpress/date': ['wp', 'date'],

    // WordPress jQuery
    'jquery': 'jQuery',

    // React (if using WordPress version)
    ...(process.env.USE_WP_REACT ? {
      'react': ['wp', 'element'],
      'react-dom': ['wp', 'element'],
    } : {}),
  },

  devtool: isDevelopment ? 'eval-source-map' : false,

  stats: {
    colors: true,
    modules: false,
    chunks: false,
    chunkModules: false,
  },

  performance: {
    hints: isProduction ? 'warning' : false,
    maxEntrypointSize: 250000,
    maxAssetSize: 250000,
  },

  devServer: {
    static: {
      directory: path.join(__dirname, 'dist'),
    },
    compress: true,
    port: 3000,
    hot: true,
    open: false,
    historyApiFallback: true,
    headers: {
      'Access-Control-Allow-Origin': '*',
    },
  },
};