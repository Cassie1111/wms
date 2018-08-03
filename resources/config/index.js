// Template version: 1.3.1
// see http://vuejs-templates.github.io/webpack for documentation.

// 获取根目录.env配置项--JS配置
require('dotenv').config()

const path = require('path')

module.exports = {
  dev: {

    // Paths
    assetsSubDirectory: 'static',
    assetsPublicPath: '/',
    proxyTable: {
      '/api': {
        target: process.env.PROXY_API,
        secure: false,
        changeOrigin: true,
      },
    },

    /* Various Dev Server settings */

    // can be overwritten by process.env.HOST
    host: process.env.DEV_HOST,

    // can be overwritten by process.env.PORT, if port is in use, a free one will be determined
    port: process.env.DEV_PORT,
    autoOpenBrowser: true,
    errorOverlay: true,
    notifyOnErrors: true,

    // https://webpack.js.org/configuration/contact_namecontact_namedev-server/#devserver-watchoptions-
    poll: false,

    // Use Eslint Loader?
    // If true, your code will be linted during bundling and linting errors and warnings will be shown in the console.
    useEslint: true,

    // If true, eslint errors and warnings will also be shown in the error overlay in the browser.
    showEslintErrorsInOverlay: false,

    /**
     * Source Maps
     */

    // https://webpack.js.org/configuration/devtool/#development
    devtool: 'cheap-module-eval-source-map',

    // If you have problems debugging vue-files in devtools,
    // set this to false - it *may* help
    // https://vue-loader.vuejs.org/en/options.html#cachebusting
    cacheBusting: true,

    // CSS Sourcemaps off by default because relative paths are "buggy"
    // with this option, according to the CSS-Loader README
    // (https://github.com/webpack/css-loader#sourcemaps)
    // In our experience, they generally work as expected,
    // just be aware of this issue when enabling this option.
    cssSourceMap: false,
  },

  build: {
    // Template for index.html
    index: path.resolve(__dirname, '../dist/index.blade.php'),

    // Paths
    assetsRoot: path.resolve(__dirname, '../../public/dist'),
    assetsSubDirectory: 'static',

    // you can set by youself according to actual condition
    assetsPublicPath: process.env[`${process.env.NODE_ENV.toUpperCase()}_ASSETS_PUBLIC_PATH`],

    /**
     * Source Maps
     */

    productionSourceMap: true,

    // https://webpack.js.org/configuration/devtool/#production
    devtool: '#source-map',

    // Gzip off by default as many popular static hosts such as
    // Surge or Netlify already gzip all static assets for you.
    // Before setting to `true`, make sure to:
    // npm install --save-dev compression-webpack-plugin
    productionGzip: false,
    productionGzipExtensions: ['js', 'css'],

    // Run the build command with an extra argument to
    // View the bundle analyzer report after build finishes:
    // `npm run build --report`
    // Set to `true` or `false` to always turn it on or off
    bundleAnalyzerReport: JSON.parse(process.env.NPM_CONFIG_REPORT),
  },
}
