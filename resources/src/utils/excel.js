/*eslint import/prefer-default-export: "off"*/

import store from '@/store/index'
import md5 from 'js-md5'

// 导出Excel表格
// @param url  导出Excel表格相应的后端方法路由，URL中需要包含请求相关的参数
export function exportExcel(url) {
  let exportUrl = url

  if (store.state.user.userInfo) {
    const userInfo = store.state.user.userInfo
    const userNo = userInfo.user_no
    const token = userInfo.token
    const nounce = Math.random().toString(36).substr(2)
    const sign = md5(`${token}${userNo}ofashion_wms${nounce}`)

    exportUrl = `${url + (url.indexOf('?') === -1 ? '?' : '&')}nounce=${nounce}&sign=${sign}`
    window.open(exportUrl)
  }
}

// 导入Excel表格
export function importExcel(url) {
  let importUrl = url

  if (store.state.user.userInfo) {
    const userInfo = store.state.user.userInfo
    const userNo = userInfo.user_no
    const token = userInfo.token
    const nounce = Math.random().toString(36).substr(2)
    const sign = md5(`${token}${userNo}ofashion_wms${nounce}`)

    importUrl = `${url + (url.indexOf('?') === -1 ? '?' : '&')}nounce=${nounce}&sign=${sign}`

    return importUrl
  }
}
