// 开发环境配置
/*eslint import/no-extraneous-dependencies: "off"*/

const merge = require('webpack-merge')
const prodEnv = require('./prod.env')

module.exports = merge(prodEnv, {
  NODE_ENV: JSON.stringify(process.env.NODE_ENV),
})
