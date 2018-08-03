// 测试环境、线上环境切换配置

const location = window.location
const BASE_API = `${location.protocol}//${location.host}`
const IMG_URL = 'https://com-ofashion-supply-chain.oss-cn-beijing.aliyuncs.com'

export {
  BASE_API,
  IMG_URL,
}
