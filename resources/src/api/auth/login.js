// 登录相关接口
import request from '@/utils/request'

export function login(accountName, password) {
  const data = {
    username: accountName,
    password,
  }

  return request.post('/api/auth/doLogin', data)
}

export function logout() {
  return request.post('/api/auth/logout')
}
