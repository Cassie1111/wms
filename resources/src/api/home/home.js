// 登录相关接口
import request from '@/utils/request'

export function index(page) {
  return request.get('/api/home', {
    params: {
      page,
    },
  })
}

export function getBgManageCount() {
  return request.get('/api/home/getBgManageCount')
}

export function getMainAccountInfo() {
  return request.get('/api/home/getMainAccountInfo')
}
