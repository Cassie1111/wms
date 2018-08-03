// 注册相关接口
import request from '@/utils/request'

export function getAccountList() {
  return request.get('/api/account')
}

export function deleteAccount(userNo) {
  return request.delete(`/api/account/${userNo}`)
}
