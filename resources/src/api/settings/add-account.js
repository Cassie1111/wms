// 登录相关接口
import request from '@/utils/request'

// 获取职务列表
export function getDutyList() {
  return request.get('/api/account/getDutyList')
}

// 获取部门列表
export function getDepartmentList() {
  return request.get('/api/account/getDepartmentList')
}

// 获取国家区号
export function getCountryCode() {
  return request.get('/api/account/getCountryCode')
}

// 获取账号信息
export function getAccountInfo(userNo) {
  return request.get(`/api/account/${userNo}`)
}

// 新建账号
export function addAccount(data) {
  return request.post('/api/account', data)
}

// 编辑账号
export function editAccount(userNo, data) {
  return request.put(`/api/account/${userNo}`, data)
}
