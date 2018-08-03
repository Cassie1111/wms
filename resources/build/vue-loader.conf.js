
const utils = require('./utils')
const config = require('../config')

// 本项目的线上、测试环境是基本一致的，打包后的静态资源路径不同而已
const isProduction = process.env.NODE_ENV !== 'development'
const sourceMapEnabled = isProduction
  ? config.build.productionSourceMap
  : config.dev.cssSourceMap

module.exports = {
  loaders: utils.cssLoaders({
    sourceMap: sourceMapEnabled,
    extract: isProduction,
  }),
  cssSourceMap: sourceMapEnabled,
  cacheBusting: config.dev.cacheBusting,
  transformToRequire: {
    video: ['src', 'poster'],
    source: 'src',
    img: 'src',
    image: 'xlink:href',
  },
}
