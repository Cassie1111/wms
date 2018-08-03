// 本地自动化测试环境配置（并不是服务器测试环境）
/*eslint import/no-extraneous-dependencies: "off"*/

if (!process.env.NODE_ENV) {
  process.env.NODE_ENV = 'testing'
}

const merge = require('webpack-merge')
const devEnv = require('./dev.env')

module.exports = merge(devEnv, {
  NODE_ENV: JSON.stringify(process.env.NODE_ENV),
})
