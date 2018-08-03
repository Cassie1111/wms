// 获取注册签名
import request from '@/utils/request'

export function getSignature() {
  return request.get('/api/auth/getRegisterSign')
}
