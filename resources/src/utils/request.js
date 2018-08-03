// 对axios的简单封装

import axios from 'axios'
import store from '@/store/index'
import md5 from 'js-md5'
import { Message } from 'element-ui'
import { BASE_API } from '@/config/env'

// 检测webp支持
function checkWebp() {
  return !![].map && document.createElement('canvas').toDataURL('image/webp').indexOf('data:image/webp') === 0
}

// header增加支持webp类型
const ACCEPT_HEADER = `application/json,text/javascript;q=0.9${checkWebp() ? ',image/webp,image/apng,*/*;q=0.8' : ''}`

// create an axios instance
const service = axios.create({
  baseURL: BASE_API,
  timeout: 30000,
})

/* eslint-disable */
// request interceptor
service.interceptors.request.use(
  config => {
    if (store.state.user.userInfo) {
      const userInfo = store.state.user.userInfo
      const user_no = userInfo.user_no
      const token = userInfo.token
      const nounce = Math.random().toString(36).substr(2)
      const sign = md5(`${token}${user_no}ofashion_wms${nounce}`)

      if (!config.params) {
        config.params = {}
      }

      config.params.nounce = nounce
      config.params.sign = sign
    }

    // Do something before request is sent
    config.headers.Accept = ACCEPT_HEADER

    return config
  },
  error => Promise.reject(error)
)

// respone interceptor
service.interceptors.response.use(
  response => {
    const config = response.config
    const data = response.data
    const code = data.code
    const msg = data.msg

    // 接口响应状态处理
    if (code !== 0) {
      switch (code) {
        case 'A401':
          const url = config.url.replace(config.baseURL, '')
          const method = url.substr(url.lastIndexOf('/')+1, url.length)

          if (method !== 'checkLogin') {
            window.location.replace(`${BASE_API}/login`)
          }

          break
        case 30002:
          Message({
            message: msg,
            type: 'error',
            duration: 5000,
          })

          break
        case 40001:
          Message({
            message: msg,
            type: 'error',
            duration: 5000,
          })

          setTimeout(() => {
            window.location.replace(`${BASE_API}/login`)
          }, 2000)

          break
        default:
          Message({
            message: msg,
            type: 'error',
            duration: 5000,
          })
          break
      }

      const error = new Error('服务器响应' + code)

      error.code = code

      return Promise.reject(error)
    }

    return data
  },
  error => {
    Message({
      message: error.message,
      type: 'error',
      duration: 2000,
    })

    return Promise.reject(error)
  }
)

export default service
