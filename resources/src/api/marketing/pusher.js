
// 推送相关接口
import request from '@/utils/request'

export function getList(params) {
  return request.get('/api/marketing/pusher', {
    params,
  })
}

export function sendMsg(data) {
  return request.post('/api/marketing/pusher', {
    ...data,
  })
}
