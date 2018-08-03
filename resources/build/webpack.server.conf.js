const path = require('path')
const eslintFormatter = require('eslint-friendly-formatter')
const config = require('../config')
const nodeExternals = require('webpack-node-externals')
const VueSSRServerPlugin = require('vue-server-renderer/server-plugin')
const utils = require('./utils.js')

function resolve(dir) {
  return path.join(__dirname, '..', dir)
}

const createLintingRule = () => ({
  test: /\.(js|vue)$/,
  loader: 'eslint-loader',
  enforce: 'pre',
  include: [resolve('src')],
  options: {
    formatter: eslintFormatter,
    emitWarning: !config.dev.showEslintErrorsInOverlay,
  },
})

const esLintRules = config.dev.useEslint ? [createLintingRule()] : []

module.exports = {
  target: 'node',
  entry: path.join(__dirname, '../src/skeleton-entry.js'),
  output: {
    path: config.build.assetsRoot,
    filename: 'server.bundle.js',
    publicPath: process.env.NODE_ENV !== 'development'
      ? config.build.assetsPublicPath
      : config.dev.assetsPublicPath,
    libraryTarget: 'commonjs2',
  },
  module: {
    rules: [
      ...esLintRules,
      {
        test: /\.vue$/,
        loader: 'vue-loader',
      },
      {
        test: /\.css$/,
        use: utils.cssLoaders().css,
      },
      {
        test: /\.scss$/,
        use: utils.cssLoaders().scss,
      },
      {
        test: /\.(png|jpe?g|gif|svg)(\?.*)?$/,
        loader: 'url-loader',
        exclude: [resolve('src/icons')],
        options: {
          limit: 10000,
          name: utils.assetsPath('img/[name].[hash:7].[ext]'),
        },
      },
      {
        test: /\.(woff2?|eot|ttf|otf)(\?.*)?$/,
        loader: 'url-loader',
        options: {
          limit: 10000,
          name: utils.assetsPath('fonts/[name].[hash:7].[ext]'),
        },
      },
    ],
  },
  externals: nodeExternals({
    whitelist: /\.css$/,
  }),
  resolve: {
    alias: {
      'vue$': 'vue/dist/vue.esm.js',
    },
  },
  plugins: [
    new VueSSRServerPlugin({
      filename: 'skeleton.json',
    }),
  ],
}
